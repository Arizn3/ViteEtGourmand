<?php

namespace App\Controller;

use App\Entity\Theme;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\MenuRepository;
use App\Repository\RegimeRepository;
use App\Repository\ThemeRepository;
use Symfony\Component\HttpFoundation\Request;

// Contrôleur lié à l'Entité Menu
final class MenuController extends AbstractController
{
    // Affichage des menus
    #[Route('/menu', name: 'app_menu')]
    public function index(MenuRepository $menuRepository, ThemeRepository $theme, RegimeRepository $regimeRepo): Response
    {
        // findby() récupère uniquement les menus qui n'ont pas de date dans DELETED_AT
        $menus = $menuRepository->findBy(['deletedAt' => null]);

        return $this->render('menu/index.html.twig', [
            'menus' => $menus,
            'themes' => $theme->findAll(),
            'regimes' => $regimeRepo->findAll(),
        ]);
    }
    
        // Filtre des menus
        #[Route('/menu/filtre', name: 'app_menu_filtre')]
        public function filtre(
            Request $request,
            MenuRepository $menuRepo,
            ThemeRepository $themeRepo,
            RegimeRepository $regimeRepo
            ): Response
        {
            $prixMax = $request->query->get('prixMax');
            $theme = $request->query->get('theme');
            $regime = $request->query->get('regime');
            $nbPersonne = $request->query->get('nbPersonne');
    
            $menus = $menuRepo->findByFilters($prixMax, $theme, $regime, $nbPersonne);
    
            return $this->render('menu/index.html.twig', [
                'menus' => $menus,
                'themes' => $themeRepo->findAll(),
                'regimes' => $regimeRepo->findAll(),
            ]);
        }

    // Affichage des détails d'un menu
    #[Route('/menu/{id}', name: 'app_menu_detail')]
    public function show(int $id, MenuRepository $menuRepository): Response
    {
        // find() est une méthode de MenuRepository qui permet de récupérer une seule donnée de l'Entité Menu
        $menu = $menuRepository->find($id);

        // Exception en cas de problème
        if (!$menu) {
            throw $this->createNotFoundException('Menu non trouvé ! Veuillez ressayer ou sélectionner un autre menu');
        }

        return $this->render('menu/detail.html.twig', [
            'menu' => $menu,
        ]);
    }
}
