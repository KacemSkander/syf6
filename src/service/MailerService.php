<?php

namespace App\service;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class MailerService
{   private $email;
    public function __construct(private MailerInterface $mailer, $email){
        $this->email=$email;
    }
    public function sendEmail(
        $to='ahmedjaidi4@gmail.com',
        $content='<p>See Twig integration for better HTML integration!</p>',
        $subject='Time for Symfony Mailer!'

        
    ): void
    {   
        
       
        $email = (new Email())
            ->from($this->email)
            ->to($to)
            //->cc('cc@example.com')
            //->bcc('bcc@example.com')
            //->replyTo('fabien@example.com')
            //->priority(Email::PRIORITY_HIGH)
            ->subject($subject)
            ->text('Sending emails is fun again!')
            ->html($content);

         $this->mailer->send($email);

        // ...
    }
}