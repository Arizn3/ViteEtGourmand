<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use App\Repository\RegimeRepository;
use App\Entity\Regime;
use RuntimeException;

class GestionRegimeService
{
    public function __construct(
        private RegimeRepository $regimeRepo,
        private EntityManagerInterface $em,
    ) {}

    public function afficherRegime()
    {
        return $this->regimeRepo->findBy([
            'deletedAt' => null
        ]);
    }

    public function ajouterRegime(Regime $regime)
    {
        $regime->setCreatedAt(new \DateTime());
        $this->em->persist($regime);
        $this->em->flush();
    }

    public function desactiverRegime(Regime $regime)
    {
        // Condition pour la vérification d'une relation entre un régime et un menu
        $menusActifs = $regime->getMenus()->filter(function ($menu) {
            return $menu->getDeletedAt() === null;
        });
        if (!$menusActifs->isEmpty()) {
            throw new \RuntimeException(
                $regime->getDescription() . ' est impossible à supprimer, ce régime est utilisé dans un menu.'
            );
        };
        $regime->setDeletedAt(new \DateTime());
        $this->em->flush();
    }
}