<?php

require_once dirname(__DIR__, 3) . '/config.php';
require_once dirname(__DIR__, 3) . '/sql.php';
require_once dirname(__DIR__, 3) . '/classes/ResponseHandler.php';
require_once dirname(__DIR__, 3) . '/classes/RequestHandler.php';
require_once dirname(__DIR__, 3) . '/classes/DBHandler.php';



$responseHandler = new ResponseHandler();
$requestHandler = new RequestHandler();

$rules = [
    'zone_id' => ['type' => 'INT']
];
// Gérer la requête avec authentification, assainissement, et validation
$data = $requestHandler->handleRequest($data, $rules);

//Vérification que cet aactivite appartient au client
$query = "SELECT * FROM  grid_association WHERE client_id =:client_id AND id =:zone_id";
$stmt = $dbh->prepare($query);
$stmt->bindParam(':client_id',  $_SESSION['client_actif']);
$stmt->bindParam(':zone_id', $data['zone_id']);
$stmt->execute();
// Check if any rows are returned, which would indicate permission
if($stmt->rowCount() == 0) {
    exit($responseHandler->sendResponse(false, "Vous ne possedez pas cette zone."));
}

//Vérification que cet aactivite appartient au client
$query = "DELETE FROM grid_association WHERE id =:zone_id";
$stmt = $dbh->prepare($query);
$stmt->bindParam(':zone_id', $data['zone_id']);
$stmt->execute();


exit($responseHandler->sendResponse(true, "Zone supprimé."));

?>