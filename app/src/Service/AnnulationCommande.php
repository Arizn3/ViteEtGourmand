<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Commande;

class AnnulationCommande
{

    public function __construct(
        private EntityManagerInterface $em,
    ) {}

    public function AnnulationCommande(Commande $commande): void
    {
        if ($commande->getStatut() === 'Votre commande va être prise en compte') {
            $menu = $commande->getMenu();
            $menu->setQttRestante(
                $menu->getQttRestante() + $commande->getNbPersonne()
            );
            $commande->setStatut('Annuler');
            $this->em->flush();
        } else {
            throw new \Exception('Cette commande ne peut plus être annulée.');
        }
    }
}
