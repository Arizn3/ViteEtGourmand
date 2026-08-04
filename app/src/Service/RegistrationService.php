<?php

namespace App\Service;

use App\Entity\Utilisateur;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Mailer\MailerInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\RoleRepository;
use Symfony\Component\Mime\Email;


use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class RegistrationService
{

    public function __construct(
        private UserPasswordHasherInterface $userPasswordHasher,
        private EntityManagerInterface $entityManager,
        private ParameterBagInterface $parameterBag,
        private RoleRepository $roleRepository,
        private MailerInterface $mailer,
    ) {}

    public function createUser(Utilisateur $user, string $plainPassword)
    {

        // Chiffrement du mot de passe
        $user->setPassword($this->userPasswordHasher->hashPassword($user, $plainPassword));

        // Ajout du rôle ROLE_USER automatique à la création d'un compte
        $roleUser = $this->roleRepository->findOneBy(['description' => 'ROLE_USER']);
        $user->setRole($roleUser);

        $user->setCreatedAt(new \DateTime());

        $email = (new Email())
            ->from(
                $this->parameterBag->get('mailer_from_name')
                    . ' <' . $this->parameterBag->get('mailer_from_address') . '>'
            )
            ->to($user->getEmail())
            ->subject('Bienvenue chez nous !')
            ->text('Bonjour,

Un grand merci de nous avoir rejoints. Nous sommes ravis de vous accueillir parmi nos nouveaux clients,
et nous réjouissons de vous accompagner dans vos événements gourmands, qu\'ils soient familiaux ou d\'entreprise.

Vous pouvez dès à présent découvrir nos menus, et passer commande via l\'application Vite&Gourmand.

Nous restons à votre disposition, cordialement.
L\'équipe Vite & Gourmand
                ');

        $this->mailer->send($email);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

    }
}
