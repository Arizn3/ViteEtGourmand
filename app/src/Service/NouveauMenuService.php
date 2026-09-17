<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Menu;

class NouveauMenuService
{
    public function __construct(
        private EntityManagerInterface $em
    ) {}

    public function nouveauMenu(Menu $menu): void
    {
        $menu->setCreatedAt(new \DateTime());
        $this->em->persist($menu);
        $this->em->flush();
    }
}
