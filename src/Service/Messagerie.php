<?php
namespace App\Service;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Messagerie{
    
    public function sendEmail(string $login, string $mdp, 
    string $recepMail, string $subject, string $body){
        //Load Composer's autoloader
        require '../vendor/autoload.php';

        //Create an instance; passing `true` enables exceptions
        $mail = new PHPMailer(true);

        try {
            //Server settings
            $mail->SMTPDebug  = 0;                                      //Enable verbose debug output
            $mail->isSMTP();                                            //Send using SMTP
            $mail->Host       = 'smtp-mail.outlook.com';                //Set the SMTP server to send through
            $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
            $mail->Username   = $login;                                 //SMTP username
            $mail->Password   = $mdp;                                   //SMTP password
            $mail->SMTPSecure = 'tls';            //Enable implicit TLS encryption
            $mail->Port       = 587;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

            //Recipients
            $mail->setFrom($login, 'Admin');                            //Adresse mail de l'expéditeur 'Admin'= nom visible /!\ ne cache pas l'adresse mail
            $mail->addAddress($recepMail, 'Destinataire');              //Add a recipient
            /* $mail->addAddress('ellen@example.com');                  //Name is optional
            $mail->addReplyTo('info@example.com', 'Information');
            $mail->addCC('cc@example.com');                             //dans le cas d'une newsletter ajouter tous les destinataires ici
            $mail->addBCC('bcc@example.com'); */

            //Attachments (piece jointe)
            /* $mail->addAttachment('/var/tmp/file.tar.gz');         //Add attachments
            $mail->addAttachment('/tmp/image.jpg', 'new.jpg');    //Optional name */

            //Content
            $mail->isHTML(true);                                  //Set email format to HTML
            $mail->Subject = $subject;
            $mail->Body    = $body;
            //$mail->AltBody = 'This is the body in plain text for non-HTML mail clients';  //client de messagerie qui ne prend pas en compte le html

            $mail->send();
            return 'Le mail a été envoyé';
        } catch (Exception $e) {
            return "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
    }

}
?>