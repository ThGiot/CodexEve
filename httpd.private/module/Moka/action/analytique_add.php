<?php

require_once dirname(__DIR__, 3) . '/config.php';
require_once dirname(__DIR__, 3) . '/sql.php';
require_once dirname(__DIR__, 3) . '/classes/ResponseHandler.php';
require_once dirname(__DIR__, 3) . '/classes/RequestHandler.php';
require_once dirname(__DIR__, 3) . '/classes/DBHandler.php';



$responseHandler = new ResponseHandler();
$requestHandler = new RequestHandler();

// Définir les règles de validation
$rules = [
    'analytique_nom' => ['type' => 'string', 'max_length' => 40],
    'analytique' => ['type' => 'string', 'max_length' => 40]
];
// Gérer la requête avec authentification, assainissement, et validation
$data = $requestHandler->handleRequest($data, $rules);

//Vérification que cet analytlique de client n'existe pas encore

$query = "SELECT * FROM moka_analytique WHERE (nom =:nom OR analytique =:analytique) AND client_id =:client_id";
$stmt = $dbh->prepare($query);
$stmt->bindParam(':nom', $data['analytique_nom']);
$stmt->bindParam(':analytique', $data['analytique']);
$stmt->bindParam(':client_id',  $_SESSION['client_actif']);
$stmt->execute();

// Check if any rows are returned, which would indicate permission
if($stmt->rowCount() >= 1) {
    exit($responseHandler->sendResponse(true, "Un analytique possede déjà ce nom ou ce code."));
}
$query = "INSERT INTO moka_analytique SET nom =:nom, analytique =:analytique, client_id =:client_id";
$stmt = $dbh->prepare($query);
$stmt->bindParam(':nom', $data['analytique_nom']);
$stmt->bindParam(':analytique', $data['analytique']);
$stmt->bindParam(':client_id',  $_SESSION['client_actif']);
$stmt->execute();
exit($responseHandler->sendResponse(true,'Un nouveau analytique à été ajouté'));
?>