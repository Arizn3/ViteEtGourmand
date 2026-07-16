<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use App\Service\RegistrationService;
use Symfony\Bundle\SecurityBundle\Security;
use App\Security\LoginFormAuthenticator;
use App\Form\RegistrationFormType;
use App\Entity\Utilisateur;

// Contrôleur pour l'inscription d'un utilisateur
class RegistrationController extends AbstractController
{

    #[Route('/register', name: 'app_register')]
    public function register(
        RegistrationService $registrationService,
        Security $security,
        Request $request,
    ): Response {

        $user = new Utilisateur();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
                /** @var string $plainPassword */
                $plainPassword = $form->get('plainPassword')->getData();
                $registrationService->createUser($user, $plainPassword);
                return $security->login($user, LoginFormAuthenticator::class, 'main');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form,
        ]);
    }
}