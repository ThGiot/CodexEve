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
    'adresse' => ['type' => 'string', 'max_length' => 9000],
    'contact_nom' => ['type' => 'string', 'max_length' => 200],
    'contact_email' => ['type' => 'string', 'max_length' => 200],
    'contact_telephone' => ['type' => 'string', 'max_length' => 200]
];
// Gérer la requête avec authentification, assainissement, et validation
$form_field =[];


foreach($data['form_field'] as $field){
    $form_field[$field['name']] = $field['value'];
    
}



$rules =['association_id' => ['type' => 'int']];
$data = $requestHandler->handleRequest($data, $rules);
$dbHandler = new DbHandler($dbh);
$table = 'grid_association';
$key = ['id' => $data['association_id']];
$data = $form_field;
$dbHandler -> update($table, $key, $data);
echo $responseHandler->sendResponse(true, 'Informations mises à jour');

?>