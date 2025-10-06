<?php
require_once __DIR__ . '/../httpd.private/config.php';
require_once PRIVATE_PATH . '/sql.php';
require_once PRIVATE_PATH . '/classes/EmailSender.php';
require_once PRIVATE_PATH . '/classes/Modal.php';

function assainirEtVerifierChamps($data, $champsRequis) {
    $donneesAssainies = [];
    $champsManquants = [];

    foreach ($champsRequis as $champ) {
        if (empty(trim($data[$champ]))) {
            $champsManquants[] = $champ;
        } else {
            $donneesAssainies[$champ] = htmlspecialchars(stripslashes(trim($data[$champ])));
        }
    }

    return [
        'success' => empty($champsManquants),
        'data' => $donneesAssainies,
        'missing' => $champsManquants
    ];
}

// Utilisation de la fonction
$champsRequis = ['login'];
$resultat = assainirEtVerifierChamps($_GET, $champsRequis);
$data=$resultat['data'];


$sql = "SELECT * FROM user WHERE login = :login";
$stmt = $dbh->prepare($sql);
$stmt ->bindParam(':login', $data['login']);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

$sql = "SELECT * FROM user_activation_key WHERE user_id = :user_id";
$stmt = $dbh->prepare($sql);
$stmt ->bindParam(':user_id', $user['id']);
$stmt->execute();
$key = $stmt->fetch(PDO::FETCH_ASSOC);
$activationKey = $key['activation_key'];

$emailSender = new EmailSender();
$to = $user['email']; // Remplacer par l'adresse e-mail du destinataire
$subject = 'Activation Compte Eve';
$htmlMessage = $emailSender-> generateActivationEmail(activationKey : $activationKey);
$textMessage = "";
$attachments = [];
$cc = []; // Remplacer par les adresses e-mail réelles
$bcc = []; // Remplacer par les adresses e-mail réelles

// Envoi de l'email
$result = $emailSender->sendEmail($to, $subject, $htmlMessage, $textMessage, $attachments, $cc, $bcc);
$message ='Un nouveau lien à été envoyé à '.$user['email'].'. Consultez vos emails pour activer votre compte</br> Pensez à regarder vos Spam.';

require 'login.php';
print_r($_POST);
?>