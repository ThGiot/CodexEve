<?php

require_once dirname(__DIR__, 3) . '/classes/RequestHandler.php';
require_once dirname(__DIR__, 3) . '/classes/ResponseHandler.php';
require_once dirname(__DIR__, 3) . '/classes/DBHandler.php';
require_once dirname(__DIR__, 3) . '/sql.php';

$requestHandler = new RequestHandler();
$responseHandler = new ResponseHandler();
// Définir les règles de validation

$rules = [
    'nomZone' => ['type' => 'string', 'max_length' => 200]
];
// Gérer la requête avec authentification, assainissement, et validation
$form_field =[];
foreach($data['formData'] as $field){
    $form_field[$field['name']] = $field['value'];
   
}
$form_field = $requestHandler->handleRequest($form_field, $rules);
$form_field['client_id'] =  $_SESSION['client_actif'];


$data = $form_field;
//Récupération du prochain numéro



$query = "INSERT INTO grid_association SET    nom =:nom,
                                        client_id =:client_id";
$stmt = $dbh->prepare($query);
$stmt->bindParam(':nom', $data['nomZone']);
$stmt->bindParam(':client_id', $_SESSION['client_actif']);
$stmt->execute();
exit($responseHandler->sendResponse(true,'La zone à été créée'));

?>