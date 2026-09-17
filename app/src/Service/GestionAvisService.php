<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Avis;

class GestionAvisService
{
    public function __construct(
        private EntityManagerInterface $em,
    ) {}

    public function gestionAvis(
        Avis $avis,
        String $action,
    ) {
        if ($action === 'VALIDE') {
            $avis->setStatut('VALIDE');
        } elseif ($action === 'REFUSER') {
            $avis->setStatut('REFUSER');
        } else {
            throw new \InvalidArgumentException('Action inconnue.');
        };
        $this->em->flush();
    }
}