<?php

namespace App\Service;

use App\Entity\Commande;

class CommandeModificationService
{

    public function verifierDateModification(Commande $commande): void
    {
        $minDate = (clone $commande->getCreatedAt())->modify('7 days');
        $today = new \DateTime();
        if ($commande->getDatePrestation() < $today) {
            throw new \Exception('⚠️ La date de livraison n\'est pas valide');
        } elseif ($commande->getDatePrestation() < $minDate) {
            throw new \Exception('⚠️ La date de livraison doit être au minimum 7 jours après la date de commande');
        };

        $heure = $commande->getHeureLivraison()->format('H:i');
        if ($heure < '11:00' || $heure > '19:00') {
            throw new \Exception('⚠️ L\'heure de livraison doit être comprise entre 11h00 et 19h00.');
        };
    }
}
