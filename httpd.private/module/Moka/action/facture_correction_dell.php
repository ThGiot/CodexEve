<?php

require_once dirname(__DIR__, 3) . '/config.php';
require_once dirname(__DIR__, 3) . '/sql.php';
require_once dirname(__DIR__, 3) . '/classes/ResponseHandler.php';
require_once dirname(__DIR__, 3) . '/classes/RequestHandler.php';
require_once dirname(__DIR__, 3) . '/classes/DBHandler.php';



$responseHandler = new ResponseHandler();
$requestHandler = new RequestHandler();

$rules = [
    'correction_id' => ['type' => 'INT']
];
// Gérer la requête avec authentification, assainissement, et validation
$data = $requestHandler->handleRequest($data, $rules);

//Vérification que cet aactivite appartient au client
$query = "DELETE FROM moka_facture_correction WHERE id =:correction_id AND client_id = :client_id";
$stmt = $dbh->prepare($query);
$stmt->bindParam(':correction_id', $data['correction_id']);
$stmt->bindParam(':client_id',  $_SESSION['client_actif']);
$stmt->execute();
// Check if any rows are returned, which would indicate permission
exit($responseHandler->sendResponse(true, "Correction supprimée."));

?>