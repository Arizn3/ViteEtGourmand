<?php

namespace App\Controller;

use SymfonyCasts\Bundle\ResetPassword\Exception\ResetPasswordExceptionInterface;
use SymfonyCasts\Bundle\ResetPassword\Controller\ResetPasswordControllerTrait;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use SymfonyCasts\Bundle\ResetPassword\ResetPasswordHelperInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use App\Form\ResetPasswordRequestFormType;
use Doctrine\ORM\EntityManagerInterface;
use App\Form\ChangePasswordFormType;
use App\Entity\Utilisateur;

// Contrôleur pour la réinitialisation d'un mot de passe
#[Route('/reset-password')]
class ResetPasswordController extends AbstractController
{
    
    use ResetPasswordControllerTrait;

    public function __construct(
        private ResetPasswordHelperInterface $resetPasswordHelper,
        private EntityManagerInterface $entityManager,
    ) {}


    // Formulaire de demande de réinitialisation du mot de passe
    #[Route('/réinitialiser-son-mot-de-passe', name: 'app_forgot_password_request')]
    public function request(Request $request, MailerInterface $mailer): Response
    {
        $form = $this->createForm(ResetPasswordRequestFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var string $email */
            $email = $form->get('email')->getData();

            return $this->processSendingPasswordResetEmail(
                $email,
                $mailer
            );
        }

        return $this->render('reset_password/request.html.twig', [
            'requestForm' => $form,
        ]);
    }

    // Page de confirmation après qu'un utilisateur a demandé une réinitialisation de mot de passe
    #[Route('/check-email', name: 'app_check_email')]
    public function checkEmail(): Response
    {
        // Génére un jeton factice si l'utilisateur n'existe pas ou si quelqu'un accède directement à cette page
        // Cela empêche de révéler si un utilisateur a été trouvé avec l'adresse e-mail fournie
        if (null === ($resetToken = $this->getTokenObjectFromSession())) {
            $resetToken = $this->resetPasswordHelper->generateFakeResetToken();
        }

        return $this->render('reset_password/check_email.html.twig', [
            'resetToken' => $resetToken,
        ]);
    }

    // Valide et traite l'URL de réinitialisation sur laquelle l'utilisateur a cliqué dans son e-mail
    #[Route('/reset/{token}', name: 'app_reset_password')]
    public function reset(Request $request, UserPasswordHasherInterface $passwordHasher, ?string $token = null): Response
    {
        if ($token) {
            // Stocke le jeton en session et le supprime de l'URL, afin d'éviter que l'URL ne soit
            // chargée dans un navigateur et ne divulgue potentiellement le jeton à du JavaScript tiers
            $this->storeTokenInSession($token);

            return $this->redirectToRoute('app_reset_password');
        }

        $token = $this->getTokenFromSession();

        if (null === $token) {
            throw $this->createNotFoundException('Aucun Token de mot de passe n\'a été trouvé dans l\'URL ni dans la session..');
        }

        try {
            /** @var Utilisateur $utilisateur */
            $utilisateur = $this->resetPasswordHelper->validateTokenAndFetchUser($token);
        } catch (ResetPasswordExceptionInterface $e) {
            $this->addFlash('reset_password_error', sprintf(
                '%s - %s',
                ResetPasswordExceptionInterface::MESSAGE_PROBLEM_VALIDATE,
                $e->getReason()
            ));

            return $this->redirectToRoute('app_forgot_password_request');
        }

        // Si le token est valide, autorise l'utilisateur a modifier son mot de passe
        $form = $this->createForm(ChangePasswordFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $this->resetPasswordHelper->removeResetRequest($token);

            /** @var string $plainPassword */
            $plainPassword = $form->get('plainPassword')->getData();

            // Hashage du mot de passe
            $utilisateur->setPassword($passwordHasher->hashPassword($utilisateur, $plainPassword));
            $this->entityManager->flush();

            // La session est nettoyée après le changement de mot de passe
            $this->cleanSessionAfterReset();

            return $this->redirectToRoute('app_home');
        }

        return $this->render('reset_password/reset.html.twig', [
            'resetForm' => $form,
        ]);
    }

    // Méthode d'envoie d'un email automatique pour la réinitialisation d'un mot de passe
    private function processSendingPasswordResetEmail(string $emailFormData, MailerInterface $mailer): RedirectResponse
    {
        $utilisateur = $this->entityManager->getRepository(Utilisateur::class)->findOneBy([
            'email' => $emailFormData,
        ]);

        // Si aucun utilisateur a été trouvé
        if (!$utilisateur) {
            return $this->redirectToRoute('app_check_email');
        }

        try {
            $resetToken = $this->resetPasswordHelper->generateResetToken($utilisateur);
        } catch (ResetPasswordExceptionInterface $e) {

            // Si vous souhaitez informer l'utilisateur de la raison pour laquelle un e-mail de réinitialisation n'a pas été envoyé,
            // décommentez les lignes ci-dessous et remplacez la redirection par 'app_forgot_password_request'.
            // Attention : ceci peut révéler si l'utilisateur est enregistré ou non.

            // $this->addFlash('reset_password_error', sprintf(
            //     '%s - %s',
            //     ResetPasswordExceptionInterface::MESSAGE_PROBLEM_HANDLE,
            //     $e->getReason()
            // ));

            return $this->redirectToRoute('app_check_email');
        }

        // Email défini depuis une template Twig
        $email = (new TemplatedEmail())
            ->from(
                $this->getParameter('mailer_from_name')
                    . ' <' . $this->getParameter('mailer_from_address') . '>'
            )
            ->to((string) $utilisateur->getEmail())
            ->subject('Réinitialiser votre mot de passe')
            ->htmlTemplate('reset_password/email.html.twig')
            ->context([
                'resetToken' => $resetToken,
            ]);

        $mailer->send($email);

        $this->setTokenObjectInSession($resetToken);

        return $this->redirectToRoute('app_check_email');
    }

}