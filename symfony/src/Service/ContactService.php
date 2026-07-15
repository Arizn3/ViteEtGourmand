<?php

namespace App\Service;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class ContactService
{

    public function __construct(
        private ParameterBagInterface $parameterBag,
        private MailerInterface $mailer,

    ) {}

    public function emailContact(
        string $email,
        string $message,
    ): void {


        $emailMessage = (new Email())
            ->from($email)
            ->to($this->parameterBag->get('mailer_from_address'))
            ->replyTo($email)
            ->subject('Message contacte')
            ->text($message);

        $this->mailer->send($emailMessage);

        $emailConfirmation = (new Email())
            ->from(
                $this->parameterBag->get('mailer_from_name')
                    . ' <' . $this->parameterBag->get('mailer_from_address') . '>'
            )
            ->to($email)
            ->subject('Vous nous avez contactés')
            ->text('Bonjour,

Votre message a bien été envoyé à notre service, une réponse vous sera faite dans les meilleurs délais.
                    
Nous restons à votre écoute, cordialement.
L\'équipe Vite & Gourmand
                    ');

        $this->mailer->send($emailConfirmation);
    }
}
