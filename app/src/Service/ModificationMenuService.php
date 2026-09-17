<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Menu;

class ModificationMenuService
{
    public function __construct(
        private EntityManagerInterface $em,
    ) {}

    public function modifierMenu(Menu $menu): void
    {
        $this->em->flush();
    }

    public function desactiverMenu(Menu $menu): void
    {
        if ($menu->getDeletedAt() !== null) {
            throw new \InvalidArgumentException('Le menu est déjà désactivé.');
        };
        $menu->setDeletedAt(new \DateTime());
        $this->em->flush();
    }
}
