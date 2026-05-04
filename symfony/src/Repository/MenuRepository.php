<?php

namespace App\Repository;

use App\Entity\Menu;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Menu>
 */
class MenuRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Menu::class);
    }

    public function findByFilters($prixMax, $theme, $regime, $nbPersonne)
    {
        $queryB = $this->createQueryBuilder('menu');

        if ($prixMax) {
            $queryB->andWhere('menu.prixPersonne <= :prixMax')
                    ->setParameter('prixMax', $prixMax);
        }
        if ($theme) {
            $queryB->andWhere('menu.theme = :theme')
                    ->setParameter('theme', $theme);
        }
        if ($regime) {
            $queryB->andWhere('menu.regime = :regime')
                    ->setParameter('regime', $regime);
        }
        if ($nbPersonne) {
            $queryB->andWhere('menu.personneMini <= :nb')
                    ->setParameter('nb', $nbPersonne);
        }

        return $queryB->getQuery()->getResult();
    }
}
