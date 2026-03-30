<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\MenuRepository;
use Symfony\Component\HttpFoundation\Request;

// Contrôleur pour les commandes
final class CommandeController extends AbstractController
{
    #[Route('/commande/{id}', name: 'app_commande')]
    public function index(Request $request, int $id, MenuRepository $menuRepository): Response
    {
        $menu = $menuRepository->find($id);
        // Exception en cas de problème
        if (!$menu) {
            throw $this->createNotFoundException('Menu introuvable, veuillez ressayer');
        }

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
            }
        }

        return $this->render('commande/index.html.twig', [
            'menu' => $menu,
            'prixTotal' => $prixTotal,
            'erreur' => $erreur,
        ]);
    }
}
