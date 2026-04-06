<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\MenuRepository;
use App\Entity\Commande;
use App\Repository\UtilisateurRepository;

// Contrôleur pour les commandes
final class CommandeController extends AbstractController
{
    #[Route('/commande/{id}', name: 'app_commande')]
    public function index(
        Request $request,
        int $id,
        MenuRepository $menuRepository,
        EntityManagerInterface $em,
    ): Response {

        // Contrôle d'accès
        if (
            !$this->isGranted('ROLE_USER') &&
            !$this->isGranted('ROLE_EMPLOYE') &&
            !$this->isGranted('ROLE_ADMIN')
        ) {
            // Accès refusé si aucun des rôles n'est utilisé par un utilisateur
            throw $this->createAccessDeniedException();
        }

        // Récupération du menu ciblé
        $menu = $menuRepository->find($id);
        // Exception en cas de problème
        if (!$menu) {
            throw $this->createNotFoundException('Menu introuvable, veuillez ressayer');
        }

        // Vérification de la connexion de l'utilisateur 
        $utilisateur = $this->getUser();

        $prixTotal = null;
        $erreur = null;

        if ($request->isMethod('POST')) {
            // Variable qui récupère des données lors de la commande grâce à la classe Request 
            $nbPersonnes = (int) $request->request->get('nb_personnes');
            $datePrestation = $request->request->get('date_prestation');
            $heureLivraison = $request->request->get('heure_livraison');
            $villeLivraison = $request->request->get('ville_livraison');
            $adresseLivraison = $request->request->get('adresse_livraison');

            // Vérification pour champs vides en cas de ByPass
            if (!$datePrestation || !$heureLivraison || !$villeLivraison || !$adresseLivraison) {
                $erreur = 'Erreur1';
                // Vérification du minimum de personnes pour le menu en cas de ByPass
            } elseif ($nbPersonnes < $menu->getPersonneMini()) {
                $erreur = 'Erreur2';
            }

            // Calcule du prix d'un menu si aucune erreur détectée
            if (!$erreur) {
                // Calcule du prix total par personnes sans réduction
                $prixTotal = $menu->getPrixPersonne() * $nbPersonnes;
                // Réduction de 10% sur le prix pour les commandes ayant 5 personnes de
                // plus que le le nombre de personnes minimum indiqué dans le menu
                if ($nbPersonnes >= ($menu->getPersonneMini() + 5)) {
                    $prixTotal *= 0.9;
                }
                // Calcule du prix total en cas de livraison hors la ville de Bordeaux
                $prixLivraison = 0;
                if (strtolower($villeLivraison) !== 'bordeaux') {
                    // Simulation temporaire d'une distance hors la ville de Bordeaux
                    $distance = 10;
                    $prixLivraison = 5 + ($distance * 0.59);
                    // Prix final avec livraison
                    $prixTotal += $prixLivraison;
                }
                if ($erreur) {
                    return $this->render('commande/index.html.twig', [
                        'menu' => $menu,
                        'utilisateur' => $utilisateur,
                        'prixTotal' => $prixTotal,
                        'erreur' => $erreur,
                    ]);
                }

                // Création de la commande
                $commande = new Commande();

                // Utilisation des Setters (Modifie) de l'Entité Commande
                $commande->setMenu($menu);
                $commande->setNbPersonne($nbPersonnes);
                $commande->setPrixMenu($prixTotal);
                $commande->setDateCmd(new \DateTime());
                $commande->setDatePrestation(new \DateTime($datePrestation));
                $commande->setHeureLivraison(new \DateTime($heureLivraison));
                $commande->setVilleLivraison($villeLivraison);
                $commande->setAdresseLivraison($adresseLivraison);

                // Données factices pour test (temporaire)
                $commande->setUtilisateur($utilisateur);
                $commande->setPrixLivraison(0);
                $commande->setStatut('TEST');
                $commande->setPretMateriel(false);
                $commande->setRestitutionMateriel(false);

                // Persistance de la commande en BDD
                $em->persist($commande);
                $em->flush();

                // Redirection une fois la commande enregistrée
                return $this->redirectToRoute('app_menu');
            }
        }

        return $this->render('commande/index.html.twig', [
            'menu' => $menu,
            'utilisateur' => $utilisateur,
            'prixTotal' => $prixTotal,
            'erreur' => $erreur,
        ]);
    }
}
