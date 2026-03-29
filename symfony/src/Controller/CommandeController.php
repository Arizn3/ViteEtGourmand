<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\MenuRepository;

// Contrôleur pour les commandes
final class CommandeController extends AbstractController
{
    #[Route('/commande/{id}', name: 'app_commande')]
    public function index(int $id, MenuRepository $menuRepository): Response
    {
        $menu = $menuRepository->find($id);
        // Exception en cas de problème
        if (!$menu) {
            throw $this->createNotFoundException('Menu introuvable, veuillez ressayer');
        }

        return $this->render('commande/index.html.twig', [
            'menu' => $menu,
        ]);
    }
}
