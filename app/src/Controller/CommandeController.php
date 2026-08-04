<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use App\Service\NouvelleCommandeService;
use App\Service\DistanceService;
use App\Form\CommandeType;
use App\Entity\Commande;
use App\Entity\Menu;

// Contrôleur pour les commandes
final class CommandeController extends AbstractController
{

    // Enregistrement d'une nouvelle commande (Service/NouvelleCommande)
    #[Route('/commande/{id}', name: 'app_commande')]
    public function index(
        NouvelleCommandeService $nouvelleCommandeService,
        Request $request,
        Menu $menu,
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

            // Condition pour le nombre minimum de boîte à repas
            if ($commande->getNbPersonne() < $menu->getPersonneMini()) {
                $this->addFlash('error', '⚠️ ' . $menu->getPersonneMini() . ' boîte à repas minimum');
                return $this->redirectToRoute('app_commande', [
                    'id' => $menu->getId()
                ]);
            }

            // Condition pour le nombre de personne minimum
            if ($commande->getNbPersonne() > $menu->getQttRestante()) {
                $this->addFlash('error', '⚠️ ' . $menu->getQttRestante() . ' boîte à repas maximum disponible');
                return $this->redirectToRoute('app_commande', [
                    'id' => $menu->getId()
                ]);
            }

            // Condition pour une date de livraison valide

            $minDate = (clone $commande->getCreatedAt())->modify('7 days');
            if ($commande->getDatePrestation() < $minDate) {
                $this->addFlash('error', '⚠️ La date de livraison doit être au minimum dans 7 jours');
                return $this->redirectToRoute('app_commande', [
                    'id' => $menu->getId()
                ]);
            }

            // Condition pour une heure de livraison valide
            $heure = $commande->getHeureLivraison()->format('H:i');
            if ($heure < '11:00' || $heure > '19:00') {
                $this->addFlash('error', '⚠️ Les livraisons sont disponibles entre 11h et 19h.');
                return $this->redirectToRoute('app_commande', [
                    'id' => $menu->getId()
                ]);
            }

            try {
                $nouvelleCommandeService->nouvelleCommande(
                    $commande,
                    $menu
                );
            } catch (\Exception) {
                $this->addFlash(
                    'error',
                    '⚠️ Attention : cette ville n’existe pas ou n’est pas desservie en Gironde.'
                );

                return $this->redirectToRoute('app_commande', [
                    'id' => $menu->getId()
                ]);
            }

            return $this->redirectToRoute('app_menu');
        }

        return $this->render('commande/nouvelle-commande.html.twig', [
            'form' => $form->createView(),
            'menu' => $menu,
            'utilisateur' => $utilisateur
        ]);
    }

    // Calcule de la distance (Service/DistanceService)
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
