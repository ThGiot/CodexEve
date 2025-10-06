<?php
require_once dirname(__DIR__, 3) . '/config.php';
require_once dirname(__DIR__, 3) . '/sql.php';
require_once dirname(__DIR__, 3) . '/classes/ResponseHandler.php';
require_once dirname(__DIR__, 3) . '/classes/RequestHandler.php';
$responseHandler = new ResponseHandler();
$requestHandler = new RequestHandler();

$rules = [
    'prestation_id' => ['type' => 'INT']
];
// Gérer la requête avec authentification, assainissement, et validation
$data = $requestHandler->handleRequest($data, $rules);

$query = "DELETE FROM moka_prestation_ebrigade WHERE id = :id AND client_id = :client_id";
$stmt = $dbh->prepare($query);
$stmt->bindParam(':id', $data['prestation_id']);
$stmt->bindParam(':client_id',  $_SESSION['client_actif']);
$stmt->execute();

exit($responseHandler->sendResponse(true,'Prestation supprimée.'));

?>