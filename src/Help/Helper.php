<?php

namespace App\Help;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;


class Helper
{
    //generar hash de un size determinado
    public static function generateHash($len)
    {
        $KEY_CHARS = 'acefghjkpqrstwxyz23456789';
        $k = str_repeat('.', $len);
        while ($len--) {
            $k[$len] = substr($KEY_CHARS, mt_rand(0, strlen($KEY_CHARS) - 1), 1);
        }
        return $k;
    }


    //enviar un correo hacia la dirección dada
    public static function sendEmail($email , $hash){

        // Instantiation and passing `true` enables exceptions
        $mail = new PHPMailer(true);

        try {
            //Server settings
            $mail->SMTPDebug = SMTP::DEBUG_SERVER;                      // Enable verbose debug output
            $mail->isSMTP();                                            // Send using SMTP
            $mail->Host       = 'smtp.gmail.com';                    // Set the SMTP server to send through
            $mail->SMTPAuth   = true;                                   // Enable SMTP authentication
            $mail->Username   = 'ygomezcoba';                     // SMTP username
            $mail->Password   = 'Yandavid*1996';                               // SMTP password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;         // Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` encouraged
            $mail->Port       = 587;                                    // TCP port to connect to, use 465 for `PHPMailer::ENCRYPTION_SMTPS` above

            //Recipients
            $mail->setFrom('ygomezcoba@gmail.com', 'Tukzoncoin');
            $mail->addAddress($email, 'Tukzoncoin Vision');     // Add a recipient
        //    $mail->addReplyTo('', 'Information');
            /*
            $mail->addAddress('ellen@example.com');               // Name is optional
            $mail->addCC('cc@example.com');
            $mail->addBCC('bcc@example.com');

            // Attachments
            $mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
            $mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name
            */

            // Content
            $mail->isHTML(true);                                  // Set email format to HTML
            $mail->Subject = 'Please confirm your e-mail';
        //    $mail->AddEmbeddedImage('logo.jpg', 'imagen');
        //    $mail->msgHTML(file_get_contents('contents.html'), dirname(__FILE__));
            $mail->Body    = 'Click the following link to verify your account https://localhost/TKZ/check_email?id='  . $hash;
          //  $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

            $mail->send();
           // echo 'Message has been sent';
        } catch (Exception $e) {
           // echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
    }


    //enviar un correo hacia la dirección dada
    public static function sendPassToEmail($email , $pass){

        // Instantiation and passing `true` enables exceptions
        $mail = new PHPMailer(true);

        try {
            //Server settings
            $mail->SMTPDebug = SMTP::DEBUG_SERVER;                      // Enable verbose debug output
            $mail->isSMTP();                                            // Send using SMTP
            $mail->Host       = 'smtp.gmail.com';                    // Set the SMTP server to send through
            $mail->SMTPAuth   = true;                                   // Enable SMTP authentication
            $mail->Username   = 'ygomezcoba';                     // SMTP username
            $mail->Password   = 'Yandavid*1996';                               // SMTP password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;         // Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` encouraged
            $mail->Port       = 587;                                    // TCP port to connect to, use 465 for `PHPMailer::ENCRYPTION_SMTPS` above
        //    $mail->SMTPDebug = false;

            //Recipients
            $mail->setFrom('ygomezcoba@gmail.com', 'TukzonCoin');
            $mail->addAddress($email, 'TukzonCoin Vision');     // Add a recipient
        //    $mail->addReplyTo('', 'Information');
            /*
            $mail->addAddress('ellen@example.com');               // Name is optional
            $mail->addCC('cc@example.com');
            $mail->addBCC('bcc@example.com');

            // Attachments
            $mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
            $mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name
            */

            // Content
            $mail->isHTML(true);                                  // Set email format to HTML
            $mail->Subject = 'Recover password';
        //    $mail->AddEmbeddedImage('logo.jpg', 'imagen');
        //    $mail->msgHTML(file_get_contents('contents.html'), dirname(__FILE__));
            $mail->Body    = 'This is your new password: '.$pass;
          //  $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

            $mail->send();
           // echo 'Message has been sent';
        } catch (Exception $e) {
           // echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
    }
}