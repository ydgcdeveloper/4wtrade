<?php

namespace App\Help;

use Exception;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;


class MailerHelp
{

    public function sendConfEmail($mailer, $address, $username , $emailhash)
    {
        
        $email = (new TemplatedEmail())
            ->from('ygomezcoba@gmail.com')
            ->to(new Address($address))
            ->subject('Welcome to 4WTrade!')
        //    ->text('Sending emails is fun again!')

            // path of the Twig template to render
            ->htmlTemplate('registration/confirmation_email.html.twig')

            // pass variables (name => value) to the template
            ->context([
                'username' => $username,
                'emailhash' => $emailhash,
            ]);

        try {
            $mailer->send($email);
        } catch (TransportExceptionInterface $e) {
            $mailer->send($email);
         //   throw new Exception($e->getMessage(), 1);
            
        }
    }
}
