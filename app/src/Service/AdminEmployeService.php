<?php

namespace App\Service;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Mailer\MailerInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\RoleRepository;
use Symfony\Component\Mime\Email;
use App\Entity\Utilisateur;

class AdminEmployeService
{

    public function __construct(
        private UserPasswordHasherInterface $passwordHasher,
        private ParameterBagInterface $parameterBag,
        private RoleRepository $roleRepository,
        private EntityManagerInterface $em,
        private MailerInterface $mailer
    ) {}

    public function creerCompteEmploye(
        string $email,
        string $prenom,
        string $nom,
    ): string {

        // Verification doublon d'email
        $existeEmail = $this->em
            ->getRepository(Utilisateur::class)
            ->findOneBy(['email' => $email]);

        if ($existeEmail) {
            throw new \RuntimeException('Email déjà utilisé');
        }

        $user = new Utilisateur();
        $plainPassword = bin2hex(random_bytes(4));

        $hashedPassword = $this->passwordHasher->hashPassword(
            $user,
            $plainPassword
        );

        $user->setPassword($hashedPassword);
        $user->setPrenom($prenom);
        $user->setEmail($email);
        $user->setNom($nom);

        $user->setCreatedAt(new \DateTime());
        $user->setTelephone('0000000000');
        $user->setAdresse('UserEmp');

        $role = $this->roleRepository
            ->findOneBy(['description' => 'ROLE_EMPLOYE']);

        $user->setRole($role);

        $this->em->persist($user);
        $this->em->flush();

        // Email
        $mail = (new Email())
            ->from(
                $this->parameterBag->get('mailer_from_name')
                    . ' <' . $this->parameterBag->get('mailer_from_address') . '>'
            )
            ->to($email)
            ->subject('Création de votre compte employé')
            ->text(
                "Bonjour,

Un compte employé a été créé pour vous.

Merci de contacter l'administrateur pour récupérer votre mot de passe.

L'équipe Vite & Gourmand"
            );

        $this->mailer->send($mail);

        return $plainPassword;
    }
}
