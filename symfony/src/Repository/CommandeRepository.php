<?php

namespace App\Repository;

use App\Entity\Commande;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Commande>
 */
class CommandeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Commande::class);
    }

    public function commandeEnCours($user)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.utilisateur = :user')
            ->andWhere('c.statut NOT IN (:statuts)')
            ->setParameter('user', $user)
            ->setParameter('statuts', ['Terminer', 'Annuler'])
            ->orderBy('c.dateCmd', 'DESC')
            ->getQuery()
            ->getResult();
    }

}
