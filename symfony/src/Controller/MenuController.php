<?php

// Contrôleur lié à la table (Entité) Menu
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\MenuRepository;

final class MenuController extends AbstractController
{
    #[Route('/menu', name: 'app_menu')]
    public function index(MenuRepository $menuRepository): Response
    {
        // findAll() est une méthode utiliser par le fichier MenuRepository qui permet de récupérer les données de l'Entité Menu
        $menus = $menuRepository->findAll();

        return $this->render('menu/index.html.twig', [
            'controller_name' => 'MenuController',
            'menus' => $menus,
        ]);
    }
}
