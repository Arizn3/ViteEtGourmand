<?php

namespace App\Service;

use App\Repository\CommandeRepository;

class AffichageCommandeService
{
    public function __construct(
        private CommandeRepository $commandeRepo,
    ) {}

    public function getFiltreCommandesActives(
        ?string $idFilter,
        ?string $emailFilter,
        ?string $statutFilter
    ): array {
        $query = $this->commandeRepo->createQueryBuilder('c')
            ->join('c.utilisateur', 'u')
            ->where('c.statut NOT IN (:statuts)')
            ->setParameter('statuts', ['Terminer', 'Annuler'])
            ->orderBy('c.id', 'ASC');

        if ($idFilter) {
            $query->andWhere('c.id = :id')
                ->setParameter('id', $idFilter);
        };
        if ($emailFilter) {
            $query->andWhere('u.email LIKE :email')
                ->setParameter('email', '%' . $emailFilter . '%');
        };
        if ($statutFilter) {
            $query->andWhere('c.statut = :statut')
                ->setParameter('statut', $statutFilter);
        };
        return $query->getQuery()->getResult();
    }

    public function getFiltreCommandesTerminer(
        ?string $idTerminer,
        ?string $emailTerminer,
        ?string $statutTerminer,
    ): array {
        $query = $this->commandeRepo->createQueryBuilder('c')
            ->join('c.utilisateur', 'u')
            ->where('c.statut IN (:statuts)')
            ->setParameter('statuts', ['Terminer', 'Annuler'])
            ->orderBy('c.id', 'DESC');

        if ($idTerminer) {
            $query->andWhere('c.id = :id')
                ->setParameter('id', $idTerminer);
        };
        if ($emailTerminer) {
            $query->andWhere('u.email LIKE :email')
                ->setParameter('email', '%' . $emailTerminer . '%');
        };
        if ($statutTerminer) {
            $query->andWhere('c.statut LIKE :statut')
                ->setParameter('statut', '%' . $statutTerminer . '%');
        };
        return $query->getQuery()->getResult();
    }
}
