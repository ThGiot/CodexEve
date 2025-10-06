<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../vendor/autoload.php';

class EmailSender {
    private $mailer;

    public function __construct() {
        $transport = (new Swift_SmtpTransport(SMTP_HOST, SMTP_PORT, SMTP_SECURE))
            ->setUsername(SMTP_USERNAME)
            ->setPassword(SMTP_PASSWORD);

        $this->mailer = new Swift_Mailer($transport);
    }

    public function generateActivationEmail($activationKey) {
        // Créez l'URL du lien d'activation
        $activationUrl = "https://hygea-consult.be?activation_user=true&key=" . $activationKey;
    
        // Construisez le message HTML
        return '
        <!DOCTYPE html>
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; }
                .header { background-color: #f2f2f2; padding: 10px; text-align: center; }
                .content { margin: 20px; }
                .footer { color: grey; font-size: 12px; text-align: center; margin-top: 20px; }
                .button {
                    background-color: #4CAF50; /* Green */
                    border: none;
                    color: white;
                    padding: 15px 32px;
                    text-align: center;
                    text-decoration: none;
                    display: inline-block;
                    font-size: 16px;
                    margin: 4px 2px;
                    cursor: pointer;
                }
            </style>
        </head>
        <body>
            <div class="header">
                <h3>Activation de votre compte</h3>
            </div>
            <div class="content">
                <p>Bonjour,</p>
                <p>Veuillez cliquer sur le lien ci-dessous pour activer votre compte :</p>
                <a href="' . $activationUrl . '" class="button">Activer Mon Compte</a>
                <p>Si le lien ci-dessus ne fonctionne pas, copiez et collez l\'URL suivante dans votre navigateur :</p>
                <p><a href="' . $activationUrl . '">' . $activationUrl . '</a></p>
            </div>
            <div class="footer">
                <p>Cordialement,</p>
                <p>L\'équipe Hygea-Consult</p>
            </div>
        </body>
        </html>';
    }

    
  
    public function generateHtmlFacture($date, $analytique, $filename,$client_nom) {
        return '
        <!DOCTYPE html>
        <html>
        <head>
            <style>
                body {
                    font-family: Arial, sans-serif;
                    background-color: #f8f8f8;
                    margin: 0;
                    padding: 0;
                    width: 100% !important;
                }
                .container {
                    width: 100%;
                    background-color: #f8f8f8;
                    padding: 0;
                    margin: 0;
                }
                .inner-container {
                    width: 600px;
                    margin: 0 auto;
                    background-color: #ffffff;
                    border: 1px solid #dddddd;
                    border-radius: 8px;
                    overflow: hidden;
                }
                .header {
                    background-color: #4CAF50;
                    padding: 20px;
                    text-align: center;
                    color: white;
                    font-size: 24px;
                }
                .content {
                    padding: 20px;
                    font-size: 14px;
                    line-height: 1.6;
                    color: #333333;
                }
                .content p {
                    margin: 20px 0;
                }
                .content ul {
                    list-style-type: none;
                    padding: 0;
                }
                .content ul li {
                    background-color: #e7f3e7;
                    margin: 10px 0;
                    padding: 10px;
                    border-radius: 5px;
                }
                .footer {
                    background-color: #f2f2f2;
                    padding: 10px;
                    text-align: center;
                    color: grey;
                    font-size: 12px;
                }
                .footer p {
                    margin: 5px 0;
                }
                .button {
                    display: inline-block;
                    padding: 10px 20px;
                    margin: 20px 0;
                    font-size: 16px;
                    color: #ffffff;
                    background-color: #4CAF50;
                    border: none;
                    border-radius: 5px;
                    text-decoration: none;
                    text-align: center;
                }
            </style>
        </head>
        <body>
            <table class="container" role="presentation" border="0" cellpadding="0" cellspacing="0">
                <tr>
                    <td align="center">
                        <table class="inner-container" role="presentation" border="0" cellpadding="0" cellspacing="0">
                            <tr>
                                <td class="header">
                                    Envoi de factures
                                </td>
                            </tr>
                            <tr>
                                <td class="content">
                                    <p>Voici les informations concernant les factures en pièces jointes :</p>
                                    <ul>
                                        <li><b>Date :</b> ' . $date . '</li>
                                        <li><b>Analytique :</b> ' . $analytique . '</li>
                                        <li><b>Fichier :</b> ' . $filename . '</li>
                                    </ul>
                                    <p style="font-size: 14px;">Facture envoyée par : ' . $_SESSION['user']['nom'] . ' ' . $_SESSION['user']['prenom'] . '</p>
                                    <p style="font-size: 14px;">'.$client_nom.'</p>
                                    <a href="https://hygea-consult.be" class="button">Accéder à l\'application</a>
                                </td>
                            </tr>
                            <tr>
                                <td class="footer">
                                    <p>Généré par l\'application Eve <i>Hygea-Consult</i></p>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </body>
        </html>';
    }

    public function generatePasswordRecoveryEmail($userId, $recoveryKey) {
        // Créez l'URL du lien de récupération
        $recoveryUrl = "https://hygea-consult.be/recover_action.php?user_id=" . $userId . "&key=" . $recoveryKey;
    
        // Construisez le message HTML
        return '
        <!DOCTYPE html>
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; }
                .header { background-color: #f2f2f2; padding: 10px; text-align: center; }
                .content { margin: 20px; }
                .footer { color: grey; font-size: 12px; text-align: center; margin-top: 20px; }
                .button {
                    background-color: #4CAF50; /* Green */
                    border: none;
                    color: white;
                    padding: 15px 32px;
                    text-align: center;
                    text-decoration: none;
                    display: inline-block;
                    font-size: 16px;
                    margin: 4px 2px;
                    cursor: pointer;
                }
            </style>
        </head>
        <body>
            <div class="header">
                <h3>Récupération de votre mot de passe</h3>
            </div>
            <div class="content">
                <p>Bonjour,</p>
                <p>Veuillez cliquer sur le lien ci-dessous pour réinitialiser votre mot de passe :</p>
                <a href="' . $recoveryUrl . '" class="button">Réinitialiser Mon Mot de Passe</a>
                <p>Si le lien ci-dessus ne fonctionne pas, copiez et collez l\'URL suivante dans votre navigateur :</p>
                <p><a href="' . $recoveryUrl . '">' . $recoveryUrl . '</a></p>
            </div>
            <div class="footer">
                <p>Cordialement,</p>
                <p>L\'équipe Hygea-Consult</p>
            </div>
        </body>
        </html>';
    }
    
    
    public function sendEmail($to, $subject, $htmlContent, $textContent, $attachments = [], $cc = [], $bcc = [], $replyTo = null) {
        // Si $to est une chaîne contenant des adresses e-mail séparées par des points-virgules, on les transforme en tableau
        if (is_string($to) && strpos($to, ';') !== false) {
            $to = array_map('trim', explode(';', $to));
        }
    
        // Filtrer les adresses e-mail vides
        if (is_array($to)) {
            $to = array_filter($to, function($email) {
                return filter_var($email, FILTER_VALIDATE_EMAIL);
            });
        }
    
        $message = (new Swift_Message($subject))
            ->setFrom([FROM_EMAIL => FROM_NAME])
            ->setTo($to)
            ->setBody($htmlContent, 'text/html')
            ->addPart($textContent, 'text/plain'); // Ajout de la version texte
    
        if ($replyTo) {
            $message->setReplyTo($replyTo);
        }
    
        foreach ($attachments as $attachment) {
            $message->attach(Swift_Attachment::fromPath($attachment));
        }
    
        foreach ($cc as $ccEmail) {
            $message->addCc($ccEmail);
        }
    
        foreach ($bcc as $bccEmail) {
            $message->addBcc($bccEmail);
        }
    
        try {
            $this->mailer->send($message);
            return true;
        } catch (Exception $e) {
            // Gérer l'exception ou logger l'erreur
            return false;
        }
    }


}



?>