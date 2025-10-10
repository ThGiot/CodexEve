<?php
require_once dirname(__DIR__, 3) . '/classes/RequestHandler.php';
require_once dirname(__DIR__, 3) . '/classes/ResponseHandler.php';
use Grid\MaterialService;

$requestHandler = new RequestHandler();
$responseHandler = new ResponseHandler();
// Définir les règles de validation

$rules = [
    'nomMaterial' => ['type' => 'string', 'max_length' => 200]
];
// Gérer la requête avec authentification, assainissement, et validation
$form_field =[];
foreach($data['form_field'] as $field){
    $form_field[$field['name']] = $field['value'];
   
}
$form_field = $requestHandler->handleRequest($form_field, $rules);
$response = MaterialService::addMaterial($_SESSION['client_actif'],$form_field['nomMaterial']);
exit($responseHandler->sendResponse($response['success'], $response['message']));

