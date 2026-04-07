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

// Controller pour l'inscription
class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(
        Request $request,
        UserPasswordHasherInterface $userPasswordHasher,
        RoleRepository $roleRepository,
        Security $security,
        EntityManagerInterface $entityManager
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

            $entityManager->persist($user);
            $entityManager->flush();

            // Connecte l'utilisateur automatiquement une fois l'inscription faite,
            // à commenter si une validation par Email est utiliser
            return $security->login($user, LoginFormAuthenticator::class, 'main');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form,
        ]);
    }
}
