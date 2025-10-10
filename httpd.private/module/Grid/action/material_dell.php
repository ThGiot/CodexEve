<?php
require_once dirname(__DIR__, 3) . '/classes/RequestHandler.php';
require_once dirname(__DIR__, 3) . '/classes/ResponseHandler.php';
use Grid\MaterialService;

$requestHandler = new RequestHandler();
$responseHandler = new ResponseHandler();
// Définir les règles de validation

$rules = [
    'material_id' => ['type' => 'INT'],
];
// Gérer la requête avec authentification, assainissement, et validation
$data = $requestHandler->handleRequest($data, $rules);
$response = MaterialService::deleteMaterial($_SESSION['client_actif'],$data['material_id']);
exit($responseHandler->sendResponse($response['success'], $response['message']));

