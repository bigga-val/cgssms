<?php

namespace App\Service;
use PHPMailer\PHPMailer\PHPMailer;

class EmailService
{

    public function __construct()
    {
    }

    public function sendEmail($to, $subject, $body)
    {
        try {

            $mail = new PHPMailer(true);
            $mail->CharSet = 'UTF-8';
            $mail->isSMTP();
            $mail->Host = 'mail.rapide-sms.com';
            $mail->Port = 587;
            $mail->SMTPAuth = true;
            $mail->Username = 'info@rapide-sms.com';
            $mail->Password = 'Rapide@sms';
            $mail->SMTPSecure = 'tls';
            $mail->isHTML(true);
            $mail->SetFrom('info@rapide-sms.com', 'Rapide SMS');
            $mail->addCC('gabrielkatonge@gmail.com');
            $mail->addBCC('katonge@insoftware.tech');




            $mail->Subject = ucfirst($subject);
            $mail->Body = $body;
            $mail->AddAddress($to);
            return $mail->Send();
        }catch (\Exception $e){
            return false;
        }
    }

    public function confirmerCommandeBody($username){
        $body = "<div style='border: 1px solid black; padding: 10px; text-align: justify;'>";
        $body .="<h4>Cher ". $username;
        $body .=",</h4><p>Merci d'avoir choisi Rapide SMS !</p>";
        $body .="<p>Votre commande des SMS a bien été enregistrée.</p>";
        $body .="<p>Notre équipe se charge de préparer votre quota des SMS. ";
        $body .="Vous recevrez un email de confirmation avec toutes les informations nécessaires ";
        $body .="dans les prochaines heures. N'hésitez pas à nous contacter si vous avez la moindre question.</p>";
        $body .= "</div>";
        return $body;
    }

    public function confirmerCompteBody($username, $email){
        $body = "<h4>Cher ". $username;
        $body .=",</h4><p> Vous recevez cet email car vous avez créé un compte sur la meilleure plateforme des SMS en RDC,</p>";
        $body .="<p>Veuillez confirmer votre compte pour pouvoir bénéficier des 5 SMS gratuits offerts en suivant le lien ci-dessous</p>";
        $body .="<a href='https://rapide-sms.com/user/confirmerCompte?email=" . $email . "&username='".$username.">Veuillez Cliquer ICI</a>";
        $body .= "<p>En cas de souci avec votre compte, n'hesitez pas de nous contacter à ce numero : </p>";
        $body .= "<p>+243 851 331 051</p>";
        return $body;
    }
}