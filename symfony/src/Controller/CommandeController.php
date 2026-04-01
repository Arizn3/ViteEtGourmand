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
        UtilisateurRepository $utilisateurRp
    ): Response {

        // Récupération du menu ciblé
        $menu = $menuRepository->find($id);
        // Exception en cas de problème
        if (!$menu) {
            throw $this->createNotFoundException('Menu introuvable, veuillez ressayer');
        }

        // Récupération de l'utiliateur temporaire pour test
        $utilisateur = $utilisateurRp->findOneBy(['email' => 'TEST_EMAIL']);
        // Exception en cas de perte de connexion
        if (!$utilisateur) {
            throw new \Exception('Utilisateur introuvable');
        }

        $prixTotal = null;
        $erreur = null;

        if ($request->isMethod('POST')) {
            // Variable qui récupère des données lors de la commande grâce à la classe Request 
            $nbPersonnes = (int) $request->request->get('nb_personnes');
            $datePrestation = $request->request->get('date_prestation');
            $heureLivraison = $request->request->get('heure_livraison');
            $adresseLivraison = $request->request->get('adresse_livraison');

            // Vérification pour champs vides en cas de ByPass
            if (!$datePrestation || !$heureLivraison || !$adresseLivraison) {
                $erreur = 'Erreur1';
                // Vérification du minimum de personnes pour le menu en cas de ByPass
            } elseif ($nbPersonnes < $menu->getPersonneMini()) {
                $erreur = 'Erreur2';
            }

            if (!$erreur) {
                // Calcule du prix total par personnes sans réduction
                $prixTotal = $menu->getPrixPersonne() * $nbPersonnes;
                // Réduction de 10% sur le prix pour les commandes ayant 5 personnes de
                // plus que le le nombre de personnes minimum indiqué dans le menu
                if ($nbPersonnes >= ($menu->getPersonneMini() + 5)) {
                    $prixTotal *= 0.9;
                } elseif ($erreur) {
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
                $commande->setUtilisateur($utilisateur);
                $commande->setNbPersonne($nbPersonnes);
                $commande->setPrixMenu($prixTotal);
                $commande->setDateCmd(new \DateTime());
                $commande->setDatePrestation(new \DateTime($datePrestation));
                $commande->setHeureLivraison(new \DateTime($heureLivraison));
                $commande->setAdresseLivraison($adresseLivraison);


                // Données factices pour test (temporaire)
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
