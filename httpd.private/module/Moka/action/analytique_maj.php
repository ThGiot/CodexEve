<?php

require_once dirname(__DIR__, 3) . '/config.php';
require_once dirname(__DIR__, 3) . '/sql.php';
require_once dirname(__DIR__, 3) . '/classes/ResponseHandler.php';
require_once dirname(__DIR__, 3) . '/classes/RequestHandler.php';
require_once dirname(__DIR__, 3) . '/classes/DBHandler.php';



$responseHandler = new ResponseHandler();
$requestHandler = new RequestHandler();

$rules = [
    'nom' => ['type' => 'string', 'max_length' => 40],
    'analytique' => ['type' => 'string', 'max_length' => 40],
    'analytique_id' => ['type' => 'INT'],
    'code_centralisateur' => ['type' => 'string', 'max_length'=>40],
    'entite' => ['type' => 'string', 'max_length'=>40],
    'distribution' => ['type' => 'string', 'max_length'=>400],
    
];
// Gérer la requête avec authentification, assainissement, et validation
$data = $requestHandler->handleRequest($data, $rules);

//Vérification que cet analytlique de client n'existe pas encore

$query = "SELECT * FROM moka_analytique WHERE (analytique =:analytique) AND client_id =:client_id";
$stmt = $dbh->prepare($query);
$stmt->bindParam(':analytique', $data['analytique']);
$stmt->bindParam(':client_id',  $_SESSION['client_actif']);
$stmt->execute();

// Check if any rows are returned, which would indicate permission
if($stmt->rowCount() > 1) {
    exit($responseHandler->sendResponse(true, "Cet analytique existe déjà !"));
}
$query = "UPDATE moka_analytique SET nom =:nom, analytique =:analytique, code_centralisateur =:code_centralisateur, entite =:entite, distribution =:distribution WHERE id = :analytique_id AND client_id =:client_id";
$stmt = $dbh->prepare($query);
$stmt->bindParam(':analytique', $data['analytique']);
$stmt->bindParam(':nom', $data['nom']);
$stmt->bindParam(':code_centralisateur', $data['code_centralisateur']);
$stmt->bindParam(':entite', $data['entite']);
$stmt->bindParam(':analytique_id', $data['analytique_id']);
$stmt->bindParam(':distribution', $data['distribution']);
$stmt->bindParam(':client_id',  $_SESSION['client_actif']);
$stmt->execute();
exit($responseHandler->sendResponse(true,'Analytique mise à jour'));
?>