<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\MenuRepository;

// Contrôleur lié à l'Entité Menu
final class MenuController extends AbstractController
{
    #[Route('/menu', name: 'app_menu')]
    public function index(MenuRepository $menuRepository): Response
    {
        // findAll() est une méthode de MenuRepository qui permet de récupérer toutes les données de l'Entité Menu
        $menus = $menuRepository->findAll();

        return $this->render('menu/index.html.twig', [
            'menus' => $menus,
        ]);
    }

    // Route pour l'affichage des détails d'un menu
    #[Route('/menu/{id}', name: 'app_menu_detail')]
    public function show(int $id, MenuRepository $menuRepository): Response
    {
        // find() est une méthode de MenuRepository qui permet de récupérer une seule donnée de l'Entité Menu
        $menu = $menuRepository->find($id);

        // Exception en cas de problème
        if(!$menu) {
            throw $this->createNotFoundException('Menu non trouvé ! Veuillez ressayer ou sélectionner un autre menu');
        }

        return $this->render('menu/detail.html.twig', [
            'menu' => $menu,
        ]);
    }
}
