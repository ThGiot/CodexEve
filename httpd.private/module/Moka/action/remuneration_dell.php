<?php

require_once dirname(__DIR__, 3) . '/config.php';
require_once dirname(__DIR__, 3) . '/sql.php';
require_once dirname(__DIR__, 3) . '/classes/ResponseHandler.php';
require_once dirname(__DIR__, 3) . '/classes/RequestHandler.php';
require_once dirname(__DIR__, 3) . '/classes/DBHandler.php';



$responseHandler = new ResponseHandler();
$requestHandler = new RequestHandler();

$rules = [
    'remuneration_id' => ['type' => 'INT']
];
// Gérer la requête avec authentification, assainissement, et validation
$data = $requestHandler->handleRequest($data, $rules);

//Vérification que cet aactivite appartient au client
$query = "SELECT * FROM moka_activite_remuneration mr JOIN moka_activite ma ON ma.id = mr.activite_id WHERE client_id =:client_id AND mr.id =:remuneration_id";
$stmt = $dbh->prepare($query);
$stmt->bindParam(':client_id',  $_SESSION['client_actif']);
$stmt->bindParam(':remuneration_id', $data['remuneration_id']);
$stmt->execute();
// Check if any rows are returned, which would indicate permission
if($stmt->rowCount() == 0) {
    exit($responseHandler->sendResponse(true, "Vous ne possedez pas cette activitée."));
}

//Vérification que cet aactivite appartient au client
$query = "DELETE FROM moka_activite_remuneration WHERE id =:remuneration_id";
$stmt = $dbh->prepare($query);
$stmt->bindParam(':remuneration_id', $data['remuneration_id']);
$stmt->execute();
// Check if any rows are returned, which would indicate permission
exit($responseHandler->sendResponse(true, "Grade supprimée."));

?>