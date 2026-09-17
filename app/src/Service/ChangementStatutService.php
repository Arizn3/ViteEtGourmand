<?php

namespace App\Service;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Mailer\MailerInterface;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\CommandeHistorique;
use Symfony\Component\Mime\Email;
use App\Entity\Commande;
use App\Document\Stat;

class ChangementStatutService
{

    public function __construct(
        private UrlGeneratorInterface $UrlGenerator,
        private ParameterBagInterface $parameterBag,
        private EntityManagerInterface $em,
        private MailerInterface $mailer,
        private DocumentManager $dm,
    ) {}

    public function changementStatut(
        Commande $commande,
        ?string $message,
        string $statut,
    ) {
        // Sécurité des statuts possible
        $statutPossible = [
            'Votre commande a été prise en compte',
            'Votre commande est en préparation',
            'En cours de livraison',
            'Commande livrée',
            'En attente du retour de matériel',
            'Terminer',
            'Annuler'
        ];
        if (!in_array($statut, $statutPossible)) {
            throw new \Exception('Statut Invalide');
        };

        // STATUT : 'En attente du retour de matériel'
        if ($statut === 'En attente du retour de matériel') {
            $email = (new Email())
                ->from(
                    $this->parameterBag->get('mailer_from_name')
                        . ' <'
                        . $this->parameterBag->get('mailer_from_address')
                        . '>'
                )
                ->to($commande->getUtilisateur()->getEmail())
                ->subject('Retour matériel')
                ->text('Bonjour,

Nous vous remercions de nous avoir fait confiance, et espérons que votre événement s\'est bien déroulé.
Nous vous rendons attentif au fait que le matériel loué doit être restitué sous 10 jours ouvrés.

Les conditions générales prévoient 600 euros de frais que nous serions bien désolés de devoir vous facturer.
Pour rendre le matériel dans de bonnes conditions, veuillez prendre contact avec nous dans les plus brefs délais,
en passant par l\'onglet contact du site.

Nous restons à votre disposition, cordialement.
L\'équipe Vite & Gourmand
                    ');
            $this->mailer->send($email);
        }

        // STATUT : 'Annuler'
        if ($statut === 'Annuler') {
            $commande->setDeletedAt(new \DateTime());
            $commande->setRestitutionMateriel(true);
            $commande->getMenu()->getQttRestante() + $commande->getNbPersonne();
            $messageEmail = $message;
            if (empty($messageEmail)) {
                throw new BadRequestHttpException('Motif d\'annulation obligatoire');
            } else {
                $email = (new Email())
                    ->from(
                        $this->parameterBag->get('mailer_from_name')
                            . ' <'
                            . $this->parameterBag->get('mailer_from_address')
                            . '>'
                    )
                    ->to($commande->getUtilisateur()->getEmail())
                    ->subject('Annulation de votre commmande')
                    ->text('Bonjour,
                    
Votre commande a été annulée pour les raisons suivantes :
                    
' . $messageEmail . '
                    
Nous restons à votre disposition, cordialement.
L\'équipe Vite & Gourmand
                    ');
                $menu = $commande->getMenu();
                $menu->setQttRestante(
                    $menu->getQttRestante() + $commande->getNbPersonne()
                );
                $this->mailer->send($email);
            };
        };

        // STATUT : 'Terminer'
        if ($statut === 'Terminer') {

            $commande->setDeletedAt(new \DateTime());
            $commande->setRestitutionMateriel(true);

            $url = $this->UrlGenerator->generate('app_login', [], UrlGeneratorInterface::ABSOLUTE_URL);
            $email = (new Email())
                ->from(
                    $this->parameterBag->get('mailer_from_name')
                        . ' <'
                        . $this->parameterBag->get('mailer_from_address')
                        . '>'
                )
                ->to($commande->getUtilisateur()->getEmail())
                ->subject('Commmande terminée')
                ->html("
                    <p>Bonjour,</p>
                    
                    <p>Votre commande est à présent terminée, nous espérons que vous vous êtes régalé.</p>

                    <p>Vous pouvez dès à présent vous rendre dans votre <a href='$url'>espace personnel</a> pour nous laisser un avis.</p>
                    
                    <p>Nous restons à votre disposition, cordialement.</p>
                    <p>L\'équipe Vite & Gourmand</p>
                    ");
            $this->mailer->send($email);
            $this->enregistrementStatistique($commande);
        }

        $commande->setStatut($statut);
        $historique = new CommandeHistorique();
        $historique->setStatut($statut);
        $historique->setDate(new \DateTime());
        $commande->addHistorique($historique);
        $this->em->persist($historique);
        $this->em->flush();
    }

    // Enregistrement des données d'une commande terminée dans MongoDB
    public function enregistrementStatistique(Commande $commande)
    {
        // Information sur la commande terminée
        $menuNom = $commande->getMenu()->getTitre();
        $prix = $commande->getPrixMenu();
        $periode = (new \DateTime())->format('Y-m');

        // Chercher si le docmuent menu existe déjà ainsi que sa période
        $stat = $this->dm->getRepository(Stat::class)->findOneBy([
            'menu' => $menuNom,
            'periode' => $periode
        ]);

        // Sinon création d'un nouveau document dans MongoDB
        if (!$stat) {
            $stat = new Stat();
            $stat->setMenu($menuNom);
            $stat->setPeriode($periode);
            $stat->setTotalCommandes(0);
            $stat->setChiffreAffaire(0);
        };
        // Incrémentation dans MongoDB
        $stat->setTotalCommandes($stat->getTotalCommandes() + 1);
        $stat->setChiffreAffaire($stat->getChiffreAffaire() + $prix);
        $this->dm->persist($stat);
        $this->dm->flush();
    }
}
