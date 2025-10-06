<?php

require_once dirname(__DIR__, 3) . '/config.php';
require_once dirname(__DIR__, 3) . '/sql.php';
require_once dirname(__DIR__, 3) . '/classes/ResponseHandler.php';
require_once dirname(__DIR__, 3) . '/classes/RequestHandler.php';
require_once dirname(__DIR__, 3) . '/classes/DBHandler.php';


$return = '';
$responseHandler = new ResponseHandler();
$requestHandler = new RequestHandler();
$rules = [
    'zone_id' => ['type' => 'INT'],
    'zone_nom' => ['type' => 'string', 'max_length' => 80],

];
// Gérer la requête avec authentification, assainissement, et validation
$data = $requestHandler->handleRequest($data, $rules);


$query = "UPDATE grid_zone SET nom=:nom
                            WHERE id = :id AND client_id =:client_id";
$stmt = $dbh->prepare($query);

$stmt->bindParam(':nom',  $data['zone_nom']);
$stmt->bindParam(':id',  $data['zone_id']);
$stmt->bindParam(':client_id',  $_SESSION['client_actif']);
$stmt->execute();
exit($responseHandler->sendResponse(true,'Zone mise à jour'));
?>