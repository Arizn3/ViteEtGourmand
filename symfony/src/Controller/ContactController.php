<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

final class ContactController extends AbstractController
{
    #[Route('/contact', name: 'app_contact')]
    public function index(Request $request, MailerInterface $mailer): Response
    {
        if ($request->isMethod('post')) {

            $email = $request->request->get('email');
            $message = $request->request->get('message');

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                throw new \Exception('Email invalide');
            }

            $emailMessage = (new Email())
                ->from($email)
                ->to('33vitegourmand@gmail.com')
                ->replyTo($email)
                ->subject('Message Contact')
                ->text($message);

            $mailer->send($emailMessage);

            $emailConfirmation = (new Email())
                ->from('Vite & Gourmand <33vitegourmand@gmail.com>')
                ->to($email)
                ->subject('Votre message')
                ->text('Bonjour,

Votre message a bien été envoyé à notre service, une réponse vous sera faite dans les meilleurs délais.
                    
Nous restons à votre écoute, cordialement.
L\'équipe Vite & Gourmand
                    ');

            $mailer->send($emailConfirmation);

            $this->addFlash('success', 'Message envoyé !');
        }

        return $this->render('contact/index.html.twig');
    }
}
