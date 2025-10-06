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
$champsRequis = ['email'];
$resultat = assainirEtVerifierChamps($_POST, $champsRequis);
$data = $resultat['data'];

if (!$resultat['success']) {
    $message = 'Merci de renseigner votre adresse email';
    require 'lost_password.php';
    exit();
}

try {
    $query = "SELECT * FROM user WHERE email = :email";
    $stmt = $dbh->prepare($query);
    $stmt->bindParam(':email', $data['email']);
    $stmt->execute();
} catch (PDOException $e) {
    $message = "Erreur lors de la vérification de l'email";
    require 'lost_password.php';
    exit();
}

// Vérifier si l'utilisateur existe
if ($stmt->rowCount() == 0) {
    $message = "Aucun utilisateur ne possède cette adresse email.";
    require 'lost_password.php';
    exit();
}

// Récupérer l'utilisateur
$user = $stmt->fetch(PDO::FETCH_ASSOC);
$user_id = $user['id'];

// Générer une clé aléatoire de 12 caractères
$key = bin2hex(random_bytes(6));

try {
    // Vérifier si une clé existe déjà pour cet utilisateur
    $query = "SELECT * FROM user_recover WHERE user_id = :user_id";
    $stmt = $dbh->prepare($query);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        // Mise à jour de la clé de récupération existante
        $query = "UPDATE user_recover SET `key` = :key, created_at = CURRENT_TIMESTAMP WHERE user_id = :user_id";
    } else {
        // Insérer une nouvelle clé de récupération
        $query = "INSERT INTO user_recover (user_id, `key`) VALUES (:user_id, :key)";
    }

    $stmt = $dbh->prepare($query);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->bindParam(':key', $key);
    $stmt->execute();
} catch (PDOException $e) {
    $message = "Erreur lors de la génération du lien de récupération.";
    require 'lost_password.php';
    exit();
}

// Envoyer l'email de récupération
$emailSender = new EmailSender();
$to = $data['email']; 
$subject = 'Récupération de votre Compte Eve';
$htmlMessage = $emailSender->generatePasswordRecoveryEmail($user_id, $key);
$textMessage = "Bonjour,\n\nVeuillez cliquer sur le lien suivant pour réinitialiser votre mot de passe :\n" . 
                "https://hygea-consult.be/recover_action.php?user_id=" . $user_id . "&key=" . $key;
$attachments = [];
$cc = [];
$bcc = [];

// Envoi de l'email
$result = $emailSender->sendEmail($to, $subject, $htmlMessage, $textMessage, $attachments, $cc, $bcc);

$message = 'Un lien de récupération vous a été envoyé. Consultez vos emails pour réinitialiser votre mot de passe.';
require 'login.php';

?>
