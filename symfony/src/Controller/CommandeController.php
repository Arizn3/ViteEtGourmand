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
    public function index(Request $request, int $id, MenuRepository $menuRepository, EntityManagerInterface $em, UtilisateurRepository $utilisateurRp): Response
    {
        // Récupération du menu ciblé
        $menu = $menuRepository->find($id);
        // Exception en cas de problème
        if (!$menu) {
            throw $this->createNotFoundException('Menu introuvable, veuillez ressayer');
        }

        // Récupération de l'utiliateur temporaire pour test
        $utilisateur = $utilisateurRp->findOneBy(['email'=> 'TEST_EMAIL']);

        $prixTotal = null;
        $erreur = null;

        if ($request->isMethod('POST')) {

            // Variable qui récupère le nombre de personnes donné lors de la commande
            // grâce notamment à la classe Request 
            $nbPersonnes = (int) $request->request->get('nb_personnes');

            // Vérification minimum
            if ($nbPersonnes < $menu->getPersonneMini()) {
                $erreur = 'Le nombre minimum de personnes pour ce menu est ' . $menu->getPersonneMini();
            } else {
                // Calcule du prix total par personnes sans réduction
                $prixTotal = $menu->getPrixPersonne() * $nbPersonnes;
                // Réduction de 10% sur le prix pour les commandes ayant 5 personnes de
                // plus que le le nombre de personnes minimum indiqué dans le menu
                if ($nbPersonnes >= ($menu->getPersonneMini() + 5)) {
                    $prixTotal *= 0.9;
                }

                // Création de la commande
                $commande = new Commande();

                // Utilisation des Setters (Modifie) de l'Entité Commande
                $commande->setMenu($menu);
                $commande->setNbPersonne($nbPersonnes);
                $commande->setPrixMenu($prixTotal);
                $commande->setDateCmd(new \DateTime());
                // Données factices pour test (temporaire)
                $commande->setUtilisateur($utilisateur);
                $commande->setDatePrestation(new \DateTime());
                $commande->setHeureLivraison(new \DateTime());
                $commande->setPrixLivraison(0);
                $commande->setStatut('TEST');
                $commande->setPretMateriel(false);
                $commande->setRestitutionMateriel(false);

                // Persistance de la commande en BDD
                $em->persist($commande);
                $em->flush();

                // Message de validation
                $this->addFlash('success', 'Commande enregistrer');
            }
        }

        return $this->render('commande/index.html.twig', [
            'menu' => $menu,
            'prixTotal' => $prixTotal,
            'erreur' => $erreur,
        ]);
    }
}
