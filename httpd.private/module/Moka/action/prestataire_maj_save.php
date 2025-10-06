<?php

require_once dirname(__DIR__, 3) . '/classes/RequestHandler.php';
require_once dirname(__DIR__, 3) . '/classes/ResponseHandler.php';
require_once dirname(__DIR__, 3) . '/classes/DBHandler.php';
require_once dirname(__DIR__, 3) . '/sql.php';

$requestHandler = new RequestHandler();
$responseHandler = new ResponseHandler();
// Définir les règles de validation
$rules = [
    'nom' => ['type' => 'string', 'max_length' => 200],
    'prenom' => ['type' => 'string', 'max_length' => 200],
    'telephone' => ['type' => 'string', 'max_length' => 200],
    'email' => ['type' => 'string', 'max_length' => 200],
    'adresse' => ['type' => 'string', 'max_length' => 200],
    'niss' => ['type' => 'string', 'max_length' => 200],
    'bce' => ['type' => 'string', 'max_length' => 200],
    'compte' => ['type' => 'string', 'max_length' => 200],
    'societe' => ['type' => 'string', 'max_length' => 200],
    'inami' => ['type' => 'string', 'max_length' => 200]
];
// Gérer la requête avec authentification, assainissement, et validation
$form_field =[];


foreach($data['form_field'] as $field){
    $form_field[$field['name']] = $field['value'];
    
}



$rules =['prestataire_id' => ['type' => 'int']];
$data = $requestHandler->handleRequest($data, $rules);
$dbHandler = new DbHandler($dbh);
$table = 'moka_prestataire';
$key = ['id' => $data['prestataire_id']];
$exclude = ['p_id'];
$data = $form_field;
$dbHandler -> update($table, $key, $data,$exclude);
echo $responseHandler->sendResponse(true, 'Informations mises à jour');

?>