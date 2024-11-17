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
}