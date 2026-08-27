<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use App\Repository\ThemeRepository;
use App\Entity\Theme;

class GestionThemeService
{
    public function __construct(
        private EntityManagerInterface $em,
        private ThemeRepository $themeRepo,
    ) {}

    public function afficherThemes()
    {
        return $this->themeRepo->findBy([
            'deletedAt' => null
        ]);
    }

    public function ajoutTheme(Theme $theme)
    {
        $theme->setCreatedAt(new \DateTime());
        $this->em->persist($theme);
        $this->em->flush();
    }

    public function desactiverTheme(Theme $theme)
    {
        // Condition pour la vérification d'une relation entre un theme et un menu
        $menusActifs = $theme->getMenus()->filter(function ($menu) {
            return $menu->getDeletedAt() === null;
        });
        if (!$menusActifs->isEmpty()) {
            throw new \RuntimeException(
                $theme->getDescription() . ' est impossible à supprimer, ce thème est utilisé dans un menu.'
            );
        };
        $theme->setDeletedAt(new \DateTime());
        $this->em->flush();
    }
}
