<?php

namespace App\Controller;

use App\Service\ContactService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;

// Contrôleur pour l'onglet Contact
final class ContactController extends AbstractController
{
    #[Route('/contact', name: 'app_contact')]
    public function index(Request $request, ContactService $contactService): Response
    {
        if ($request->isMethod('post')) {

            $email = $request->request->get('email');
            $message = $request->request->get('message');

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                throw new \Exception('Email invalide');
            }

            try {
                $contactService->emailContact($email, $message);
                $this->addFlash('success', 'Message envoyé !');
            } catch (\Throwable $e) {
                $this->addFlash('error', 'Une erreur est survenue');
            }
        }

        return $this->render('contact/index.html.twig');
    }
}
