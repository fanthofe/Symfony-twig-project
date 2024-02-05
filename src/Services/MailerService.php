<?php

namespace App\Services;

use Symfony\Component\Mime\Address;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
Use Symfony\Component\Mime\Email;

class MailerService
{
    public function __construct (private MailerInterface $mailer) {

    }

    public function sendEmail(
        $to = 'siteadmin@hotmail.fr',
        $subject = 'This is the Mail subject !',
        $name = '',
        $message = '',
    ): void{
        $email = (new TemplatedEmail())
        ->from(new Address('noreply@69dev.io', '69pixl'))
        ->to($to)
        ->subject($subject)
        ->htmlTemplate('contact/email.html.twig')
        ->context([
            'name' => $name,
            'message' => $message
        ]);
        $this->mailer->send($email);
    }
}
