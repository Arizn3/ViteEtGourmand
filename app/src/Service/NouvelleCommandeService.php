<?php

namespace App\Service;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Mailer\MailerInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Mime\Email;
use App\Entity\Commande;
use App\Entity\Menu;

class NouvelleCommandeService
{
    public function __construct(
        private EntityManagerInterface $em,
        private MailerInterface $mailer,
        private ParameterBagInterface $parameterBag,
        private DistanceService $distanceService
    ) {
    }

    public function nouvelleCommande(Commande $commande, Menu $menu): void
    {
        $prixTotal = $menu->getPrixPersonne() * $commande->getNbPersonne();

        if ($commande->getNbPersonne() >= ($menu->getPersonneMini() + 5)) {
            $prixTotal *= 0.9;
        }

        $prixLivraison = 0;

        if (strtolower($commande->getVilleLivraison()) !== 'bordeaux') {
            $distance = $this->distanceService->getDistance(
                $commande->getVilleLivraison()
            );

            $prixLivraison = 5 + ($distance * 0.59);
            $prixTotal += $prixLivraison;
        }

        $commande->setPrixMenu($prixTotal);
        $commande->setPrixLivraison($prixLivraison);
        $commande->setDateCmd(new \DateTime());
        $commande->setCreatedAt(new \DateTime());
        $commande->setStatut('Votre commande va être prise en compte');
        $commande->setPretMateriel(true);
        $commande->setRestitutionMateriel(false);

        $menu->setQttRestante(
            $menu->getQttRestante() - $commande->getNbPersonne()
        );

        $this->em->persist($commande);
        $this->em->flush();

        $email = (new Email())
            ->from(
                $this->parameterBag->get('mailer_from_name')
                . ' <'
                . $this->parameterBag->get('mailer_from_address')
                . '>'
            )
            ->to($commande->getUtilisateur()->getEmail())
            ->subject('Réception de votre commande')
            ->text(
                "Bonjour,\n\n" .
                "Votre commande va être prise en compte par notre service.\n" .
                "Accédez à votre espace personnel pour suivre l'avancement de votre commande.\n\n" .
                "Merci d'avoir choisi Vite & Gourmand !\n\n" .
                "Nous restons à votre disposition, cordialement.\n" .
                "L'équipe Vite & Gourmand"
            );

        $this->mailer->send($email);
    }
}