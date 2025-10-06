<?php

require_once dirname(__DIR__, 3) . '/config.php';
require_once dirname(__DIR__, 3) . '/sql.php';
require_once dirname(__DIR__, 3) . '/classes/ResponseHandler.php';
require_once dirname(__DIR__, 3) . '/classes/RequestHandler.php';
require_once dirname(__DIR__, 3) . '/classes/DBHandler.php';



$responseHandler = new ResponseHandler();
$requestHandler = new RequestHandler();

$rules = [
    'code' => ['type' => 'string', 'max_length' => 40],
    'analytique_id' => ['type' => 'string', 'max_length' => 40],
    'remuneration_type' => ['type' => 'string', 'max_length' => 40]
];
// Gérer la requête avec authentification, assainissement, et validation
$data = $requestHandler->handleRequest($data, $rules);

//Vérification que cet analytlique de client n'existe pas encore

$query = "SELECT * FROM moka_activite WHERE (code =:code) AND client_id =:client_id";
$stmt = $dbh->prepare($query);
$stmt->bindParam(':code', $data['code']);
$stmt->bindParam(':client_id',  $_SESSION['client_actif']);
$stmt->execute();

// Check if any rows are returned, which would indicate permission
if($stmt->rowCount() >= 1) {
    exit($responseHandler->sendResponse(true, "Une activité possede déjà ce code."));
}
$query = "INSERT INTO moka_activite SET code =:code, analytique_id =:analytique_id, client_id =:client_id, remuneration_type =:remuneration_type";
$stmt = $dbh->prepare($query);
$stmt->bindParam(':code', $data['code']);
$stmt->bindParam(':analytique_id', $data['analytique_id']);
$stmt->bindParam(':remuneration_type', $data['remuneration_type']);
$stmt->bindParam(':client_id',  $_SESSION['client_actif']);
$stmt->execute();
exit($responseHandler->sendResponse(true,'Une nouvelle activitée à été crée'));
?>