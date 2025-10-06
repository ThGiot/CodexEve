<?php

require_once dirname(__DIR__, 3) . '/config.php';
require_once dirname(__DIR__, 3) . '/sql.php';
require_once dirname(__DIR__, 3) . '/classes/ResponseHandler.php';
require_once dirname(__DIR__, 3) . '/classes/RequestHandler.php';
require_once dirname(__DIR__, 3) . '/classes/DBHandler.php';

$responseHandler = new ResponseHandler();
$requestHandler = new RequestHandler();

$rules = [
    'code' => ['type' => 'string', 'max_length'=> 250]
];

// Gérer la requête avec authentification, assainissement, et validation
$data = $requestHandler->handleRequest($data, $rules);

// Initialiser une session cURL
$ch = curl_init();

// Définir l'URL de la requête
curl_setopt($ch, CURLOPT_URL, "https://medteam.be/moka_eve_recover.php");
curl_setopt($ch, CURLOPT_POST, 1);


if($data['code'] != MEDTEAM_INVITATION_KEY){
    exit($responseHandler->sendResponse(false, 'Le code saisi est incorrect'));
}

// Rechercher l'id du prestataire
$query = "SELECT * FROM moka_prestataire WHERE email = :email AND client_id = 3 AND user_id IS NULL";
$stmt = $dbh->prepare($query);
$stmt->bindParam(':email', $decodedResponse['mail']);
$stmt->execute();
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);



$query = "  SELECT role_id FROM module_permission_role
WHERE user_id = :user_id AND
module_id = 3 AND
client_id = 3";
$stmt = $dbh->prepare($query);
// Exécuter la requête en liant les paramètres
$stmt->bindParam(':user_id', $_SESSION['user']['id']);
$stmt->execute();
$role_to_change= $stmt->fetch(PDO::FETCH_ASSOC);
if(empty($role_to_change)){
    $dbHandler = new DBHandler($dbh);
    $table = 'module_permission_role';
    $data = ['user_id' => $_SESSION['user']['id'], 'client_id' => 3, 'module_id' => 3, 'role_id'=>3];
    $dbHandler->insert($table, $data);

    $dbHandler = new DBHandler($dbh);
    $table = 'user_client';
    $data = ['user_id' => $_SESSION['user']['id'], 'client_id' => 3];
    $dbHandler->insert($table, $data);
}else{
    exit($responseHandler->sendResponse(false, 'Le compte à déjà été intégré au client. Merci de vous reconnecter'));
}



exit($responseHandler->sendResponse(true, 'Récupération de compte réussie. Merci de vous reconnecter.</br><p id="decompte"></p>'));

?>
