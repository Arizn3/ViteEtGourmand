<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Commande;
use App\Entity\Menu;
use App\Form\CommandeType;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use App\Service\DistanceService;
use Symfony\Component\HttpFoundation\JsonResponse;

// Contrôleur pour les commandes
final class CommandeController extends AbstractController
{
    #[Route('/commande/{id}', name: 'app_commande')]
    public function index(
        Request $request,
        Menu $menu,
        EntityManagerInterface $em,
        MailerInterface $mailer,
        DistanceService $distanceService
    ): Response {

        if (
            !$this->isGranted('ROLE_USER') &&
            !$this->isGranted('ROLE_EMPLOYE')
        ) {
            // Symfony renvoie vers la page définie dans Security.yml
            throw $this->createAccessDeniedException();
        }

        if (!$menu) {
            throw $this->createNotFoundException('Menu introuvable, veuillez ressayer');
        }

        $utilisateur = $this->getUser();

        // Création de la commande
        $commande = new Commande();
        $commande->setMenu($menu);
        $commande->setUtilisateur($utilisateur);

        $form = $this->createForm(CommandeType::class, $commande, [
            'modification' => false
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            if ($commande->getNbPersonne() < $menu->getPersonneMini()) {
                $this->addFlash('error', '⚠️ ' . $menu->getPersonneMini() . ' boîte à repas minimum');
                return $this->redirectToRoute('app_commande', [
                    'id' => $menu->getId()
                ]);
            }

            if ($commande->getNbPersonne() > $menu->getQttRestante()) {
                $this->addFlash('error', '⚠️ ' . $menu->getQttRestante() . ' boîte à repas maximum disponible');
                return $this->redirectToRoute('app_commande', [
                    'id' => $menu->getId()
                ]);
            }

            $today = new \DateTime();
            $minDate = (clone $today)->modify('+ 7 days');

            if ($commande->getDatePrestation() < $minDate) {
                $this->addFlash('error', '⚠️ La date de livraison doit être au minimum dans 7 jours');
                return $this->redirectToRoute('app_commande', [
                    'id' => $menu->getId()
                ]);
            }

            $prixTotal = $menu->getPrixPersonne() * $commande->getNbPersonne();

            if ($commande->getNbPersonne() >= ($menu->getPersonneMini() + 5)) {
                $prixTotal *= 0.9;
            }

            $heure = $commande->getHeureLivraison()->format('H:i');
            if ($heure < '11:00' || $heure > '19:00') {
                $this->addFlash('error', '⚠️ Les livraisons sont disponibles entre 11h et 19h.');
                return $this->redirectToRoute('app_commande', [
                    'id' => $menu->getId()
                ]);
            }

            $prixLivraison = 0;

            if (strtolower($commande->getVilleLivraison()) !== 'bordeaux') {

                try {
                    $distance = $distanceService->getDistance(
                        $commande->getVilleLivraison()
                    );
                    $prixLivraison = 5 + ($distance * 0.59);
                    $prixTotal += $prixLivraison;
                } catch (\Exception $e) {
                    $this->addFlash('error', '⚠️ Attention : cette ville n’existe pas ou n’est pas desservie en Gironde.');
                    return $this->redirectToRoute('app_commande', [
                        'id' => $menu->getId()
                    ]);
                }
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

            $em->persist($commande);
            $em->flush();

            // Email de confirmation à l'utilisateur
            $email = (new Email())
                ->from(
                    $this->getParameter('mailer_from_name')
                        . ' <' . $this->getParameter('mailer_from_address') . '>'
                )
                ->to($commande->getUtilisateur()->getEmail())
                ->subject('Réception de votre commande')
                ->text('Bonjour,

Votre commande va être prise en compte par notre service.
Accédez à votre espace personnel pour suivre l\'avancement de votre commande.

Merci d\'avoir choisi Vite & Gourmand !

Nous restons à votre disposition, cordialement.
L\'équipe Vite & Gourmand
                    ');
            $mailer->send($email);

            return $this->redirectToRoute('app_menu');
        }

        return $this->render('commande/nouvelle-commande.html.twig', [
            'form' => $form->createView(),
            'menu' => $menu,
            'utilisateur' => $utilisateur
        ]);
    }

    #[Route('/calcul-livraison', name: 'app_calcul_livraison')]
    public function calculeLivraison(Request $request, DistanceService $distanceService): JsonResponse
    {
        $ville = urldecode($request->query->get('ville'));

        try {
            if (strtolower($ville) === 'bordeaux') {
                return $this->json([
                    'prixLivraison' => 0
                ]);
            }

            $distance = $distanceService->getDistance($ville);
            $prixLivraison = 5 + ($distance * 0.59);

            return $this->json([
                'distance' => $distance,
                'prixLivraison' => round($prixLivraison, 2)
            ]);
        } catch (\Throwable $th) {

            return new JsonResponse([
                'error' => 'Ville invalide'
            ]);
        }
    }
}
