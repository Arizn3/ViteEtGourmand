<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use App\Repository\AvisRepository;
use App\Entity\Utilisateur;
use App\Entity\Avis;


class UtilisateurAvis
{

    public function __construct(
        private EntityManagerInterface $em,
        private AvisRepository $avisRepo,
    ) {}

    public function recupererAvis(Utilisateur $utilisateur): Avis
    {
        $avis = $this->avisRepo->findOneBy([
            'utilisateur' => $utilisateur
        ]);

        if (!$avis) {
            $avis = new Avis();
            $avis->setUtilisateur($utilisateur);
        }

        return $avis;

    }

    public function ajouterModifierAvis(Avis $avis): void 
    {

        $avis->setStatut('EN_ATTENTE');
        $this->em->persist($avis);
        $this->em->flush();
    }

    public function supprimerAvis(Avis $avis): void
    {
        $this->em->remove($avis);
        $this->em->flush();
    }
}
