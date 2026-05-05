<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use App\Repository\RoleRepository;
use App\Form\RegistrationFormType;
use App\Security\LoginFormAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

// Controller pour l'inscription
class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(
        Request $request,
        UserPasswordHasherInterface $userPasswordHasher,
        RoleRepository $roleRepository,
        Security $security,
        EntityManagerInterface $entityManager,
        MailerInterface $mailer
    ): Response {
        $user = new Utilisateur();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var string $plainPassword */
            $plainPassword = $form->get('plainPassword')->getData();

            // Chiffrement du mot de passe
            $user->setPassword($userPasswordHasher->hashPassword($user, $plainPassword));

            // Ajout du rôle ROLE_USER automatique à la création d'un compte
            $roleUser = $roleRepository->findOneBy(['description' => 'ROLE_USER']);
            $user->setRole($roleUser);

            $user->setCreatedAt(new \DateTime());

            $email = (new Email())
                ->from('Vite & Gourmand <33vitegourmand@gmail.com>')
                ->to($user->getEmail())
                ->subject('Bienvenue chez nous !')
                ->text('Bonjour,

Un grand merci de nous avoir rejoints. Nous sommes ravis de vous accueillir parmi nos nouveaux clients,
et nous réjouissons de vous accompagner dans vos événements gourmands, qu\'ils soient familiaux ou d\'entreprise.

Vous pouvez dès à présent découvrir nos menus, et passer commande via l\'application Vite&Gourmand.

Nous restons à votre disposition, cordialement.
L\'équipe Vite & Gourmand
                ');

            $mailer->send($email);

            $entityManager->persist($user);
            $entityManager->flush();

            return $security->login($user, LoginFormAuthenticator::class, 'main');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form,
        ]);
    }
}
