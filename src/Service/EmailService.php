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
            $mail = new PHPMailer();
            $mail->isSMTP();
            $mail->SMTPAuth = true;
            $mail->SMTPSecure = 'ssl';
            $mail->Host = 'smtp.hostinger.com';
            $mail->Port = '465';
            $mail->isHTML(true);
            $mail->Username = 'admin@insoftware.tech';
            $mail->Password = 'Insoft@123';
            $mail->SetFrom('admin@insoftware.tech');
            //$mail->addAttachment($myFile);

            $mail->Subject = ucfirst($subject);
            $mail->Body = $body;
            $mail->AddAddress($to);
            return $mail->Send();
        }catch (\Exception $e){
            return false;
        }
    }

    public function confirmerCommandeBody($username){
        $body = "<h4>Cher ". $username;
        $body .=",</h4><p> Votre commande des SMS a été bien prise en charge,</p>";
        $body .="<p>Notre service clientèle vous revient dans un moment.</p>";
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