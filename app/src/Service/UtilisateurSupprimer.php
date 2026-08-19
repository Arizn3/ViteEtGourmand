<?php

namespace App\Service;

use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Utilisateur;

class UtilisateurSupprimer {

    public function __construct(
        private EntityManagerInterface $em,
        private TokenStorageInterface $TokenStorage,
    )
    {}

    public function supprimerCompte(Utilisateur $utilisateur): void
    {
        $this->TokenStorage->setToken(null);
        $utilisateur->setEmail('deleted_' . $utilisateur->getEmail());
        $utilisateur->setDeletedAt(new \DateTime());
        $this->em->flush();
    }

}