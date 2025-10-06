<?php
require_once dirname(__DIR__, 3) . '/config.php';
require_once dirname(__DIR__, 3) . '/sql.php';
require_once dirname(__DIR__, 3) . '/classes/ResponseHandler.php';
require_once dirname(__DIR__, 3) . '/classes/RequestHandler.php';
require_once dirname(__DIR__, 3) . '/classes/DBHandler.php';



$responseHandler = new ResponseHandler();
$requestHandler = new RequestHandler();
$requestHandler-> superAdminAuth($dbh);

// Définir les règles de validation
$rules = [
    'client_nom' => ['type' => 'string', 'max_length' => 40]
];
// Gérer la requête avec authentification, assainissement, et validation
$data = $requestHandler->handleRequest($data, $rules);

//Vérification que ce nom de client n'existe pas encore

$query = "SELECT * FROM client WHERE nom =:nom";
$stmt = $dbh->prepare($query);
$stmt->bindParam(':nom', $data['client_nom']);
$stmt->execute();

// Check if any rows are returned, which would indicate permission
if($stmt->rowCount() >= 1) {
    exit($responseHandler->sendResponse(true, "Un client possede déjà ce nom."));
}
$query = "INSERT INTO client SET nom =:nom, logo ='-', note = '-'";
$stmt = $dbh->prepare($query);
$stmt->bindParam(':nom', $data['client_nom']);
$stmt->execute();
exit($responseHandler->sendResponse(true,'Un nouveau client à été ajouté'));
?>