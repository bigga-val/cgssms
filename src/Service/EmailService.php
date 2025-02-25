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
            $mail->addBCC('gabrielkatonge@gmail.com');
            $mail->addBCC('katonge@insoftware.tech');




            $mail->Subject = ucfirst($subject);
            $mail->Body = $body;
            $mail->AddAddress($to);
            return $mail->Send();
        }catch (\Exception $e){
            return false;
        }
    }

    public function confirmerCommandeBody($username, $nbresms){
        $body = "<div style='padding: 10px; text-align: justify;'>";
        $body .="<h4>Cher ". $username;
        $body .=",</h4><p>Merci d'avoir choisi Rapide SMS !</p>";
        $body .="<p>Votre commande des ". $nbresms ." SMS a bien été enregistrée.</p>";
        $body .="<p>Notre équipe se charge de préparer votre quota des SMS.";
        $body .="Vous recevrez un email de confirmation avec toutes les informations nécessaires ";
        $body .="dans les prochaines heures. N'hésitez pas à nous contacter si vous avez la moindre question.</p>";
        $body .="<p>Cordialement !</p>";
        $body .="<p>L'équipe commerciale</p>";
        $body .= "</div>";
        return $body;
    }

    public function confirmerCompteBody($username, $email, $userID){
        $body = "<h4>Cher(e) ". $username;
        $body .=",</h4><p> Nous sommes ravis de vous accueillir dans la communauté Rapide SMS, la meilleure plateforme des SMS en RDC</p>";
        $body .="<p>Pour activer votre compte et accéder à toutes nos fonctionnalités, veuillez cliquer sur le lien suivant :</p>";
        $body .="<a href='https://rapide-sms.com/confirmer?id=" . $userID ."'>Votre lien unique</a>";
        $body .= "<p>Une fois votre compte confirmé, vous pourrez :</p><ul>";
        $body .= "<li>Beneficier des 5 SMS gratuits</li>";
        $body .= "<li>Envoyer des SMS vers differents reseaux de la RDC sans inquietude(Vodacom, Airtel, Orange et Africell)</li>";
        $body .= "<li>Faire des envois rapides des SMS avec un seul numero</li>";
        $body .= "<li>Envoyer des SMS en masse à vos groupes des contacts</li>";
        $body .= "<li>Uploader votre fichier des numeros et les enregistrer pour des envois futurs</li>";
        $body .= "</ul><p>N'hésitez pas à nous contacter si vous avez la moindre question.</p>";
        $body .= "<p>Cordialement !</p>";
        $body .= "<p>Rapide SMS</p>";
        $body .= "<p>Telephone: +243 851 331 051</p>";
        return $body;
    }

    public function pwdResetBody($username, $pwdID){
        $body = "<h4>Cher(e) ". $username;
        $body .=",</h4><p>Une demande de réinitialisation de mot de passe a été effectuée pour votre compte Rapide SMS</p>";
        $body .="<p>Pour créer un nouveau mot de passe, veuillez cliquer sur le lien suivant :</p>";
        $body .="<a href='http://localhost:8009/resetter?id=" . $pwdID ."'>Votre lien unique</a>";
        $body .= "<p>Une fois sur la page, vous pourrez saisir et confirmer votre nouveau mot de passe.</p>";
        $body .= "<p>Important : </p><ul>";
        $body .= "<li>Ce lien est valide pendant 1 heure(60 minutes). Passé ce délai, vous devrez refaire une demande de réinitialisation.</li>";
        $body .= "<li>Ce lien devient valide une fois que le mot de passe est réinitialisé avec succès.</li>";
        $body .= "<li>Si vous n'êtes pas à l'origine de cette demande, nous vous recommandons de contacter immédiatement notre support client à Info@rapide-sms.com</li>";
        $body .= "</ul><p>Merci,</p>";
        $body .= "<p>Rapide SMS</p>";
        $body .= "<p>Telephone: +243 851 331 051</p>";
        return $body;
    }
}