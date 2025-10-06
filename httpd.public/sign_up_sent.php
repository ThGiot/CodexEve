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
$champsRequis = ['email', 'nom', 'prenom', 'password', 'confirm_password'];
$resultat = assainirEtVerifierChamps($_POST, $champsRequis);
$data=$resultat['data'];

if (!$resultat['success']) {
    $message = 'Tous les champs ne sont pas remplis';
    require 'sign_up.php';
    exit(); 
}

if($_POST['checkCondition'] != 'on'){
    $message = 'Merci d\'accepter les conditions générales';
    require 'sign_up.php';
    exit(); 

}
try{
    $query = "SELECT * FROM user WHERE 
                            email = :email OR login = :login";
    $stmt = $dbh->prepare($query);
    $stmt->bindParam(':email', $data['email']);
    $stmt->bindParam(':login', $data['email']);
    $stmt->execute();
}catch (PDOException $e) {
    $message = "Erreur  lors de la vérification de l'email";
    require 'sign_up.php';
    exit();
    }

// Check if any rows are returned, which would indicate permission

if($stmt->rowCount() > 0) {
    $message = "Un utilisateur possède déjà cette adresse email.";
    require 'sign_up.php';
    exit();
}
if($data['confirm_password'] != $data['password']) {
    $message = "les mot de passe ne correspondent pas.";
    require 'sign_up.php';
    exit();
}


$hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);
$query = "INSERT INTO user (nom, prenom, email, login,password) VALUES (:nom, :prenom, :email, :login,:password)";
$stmt = $dbh->prepare($query);
$stmt->bindParam(':nom', $data['nom']);
$stmt->bindParam(':prenom', $data['prenom']);
$stmt->bindParam(':email', $data['email']);
$stmt->bindParam(':login', $data['email']);
$stmt->bindParam(':password', $hashedPassword);
$stmt->execute();



// Récupération de l'ID de l'utilisateur nouvellement créé
$userId = $dbh->lastInsertId();
// Génération d'une clé d'activation aléatoire
$activationKey = bin2hex(random_bytes(15));
// Insertion de la clé d'activation dans la table user_activation_key



$query = "INSERT INTO user_activation_key (user_id, activation_key) VALUES (:user_id, :activation_key)";
$stmt = $dbh->prepare($query);
$stmt->bindParam(':user_id', $userId);
$stmt->bindParam(':activation_key', $activationKey);
$stmt->execute();

$emailSender = new EmailSender();
$to = $data['email']; // Remplacer par l'adresse e-mail du destinataire
$subject = 'Activation Compte Eve';
$htmlMessage = $emailSender-> generateActivationEmail(activationKey : $activationKey);
$textMessage = "";
$attachments = [];
$cc = []; // Remplacer par les adresses e-mail réelles
$bcc = []; // Remplacer par les adresses e-mail réelles

// Envoi de l'email
$result = $emailSender->sendEmail($to, $subject, $htmlMessage, $textMessage, $attachments, $cc, $bcc);
$message ='Votre Compte à été Créé. Consultez vos emails pour activer votre compte';

require 'login.php';
print_r($_POST);
?>