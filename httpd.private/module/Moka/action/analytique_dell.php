<?php
require_once dirname(__DIR__, 3) . '/config.php';
require_once dirname(__DIR__, 3) . '/sql.php';
require_once dirname(__DIR__, 3) . '/classes/ResponseHandler.php';
require_once dirname(__DIR__, 3) . '/classes/RequestHandler.php';
$responseHandler = new ResponseHandler();
$requestHandler = new RequestHandler();

$rules = [
    'analytique_id' => ['type' => 'INT']
];
// Gérer la requête avec authentification, assainissement, et validation
$data = $requestHandler->handleRequest($data, $rules);

$query = "DELETE FROM moka_activite_remuneration WHERE activite_id IN (SELECT id FROM moka_activite WHERE analytique_id = :id AND client_id = :client_id)";
$stmt = $dbh->prepare($query);
$stmt->bindParam(':id', $data['analytique_id']);
$stmt->bindParam(':client_id', $_SESSION['client_actif']);
$stmt->execute();

$query = "DELETE FROM moka_activite WHERE analytique_id = :id AND client_id = :client_id";
$stmt = $dbh->prepare($query);
$stmt->bindParam(':id', $data['analytique_id']);
$stmt->bindParam(':client_id',  $_SESSION['client_actif']);
$stmt->execute();

$query = "DELETE FROM moka_facture_correction WHERE analytique_id = :id AND client_id = :client_id";
$stmt = $dbh->prepare($query);
$stmt->bindParam(':id', $data['analytique_id']);
$stmt->bindParam(':client_id',  $_SESSION['client_actif']);
$stmt->execute();

$query = "DELETE FROM moka_analytique WHERE id = :id AND client_id = :client_id";
$stmt = $dbh->prepare($query);
$stmt->bindParam(':id', $data['analytique_id']);
$stmt->bindParam(':client_id',  $_SESSION['client_actif']);
$stmt->execute();

exit($responseHandler->sendResponse(true,'Analytique supprimé.'));

?>