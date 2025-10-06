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
    'poste_nom' => ['type' => 'string', 'max_length' => 200],
    'poste_zone' => ['type' => 'string', 'max_length' => 200],
    'poste_association' => ['type' => 'string', 'max_length' => 200],
    'poste_type' => ['type' => 'string', 'max_length' => 200],
    'horaire_id' => ['type' => 'string', 'max_length' => 200],
    'poste_num' => ['type' => 'string', 'max_length' => 200],

];
// Gérer la requête avec authentification, assainissement, et validation
$form_field =[];
foreach($data['formData'] as $field){
    $form_field[$field['name']] = $field['value'];
   
}
$form_field = $requestHandler->handleRequest($form_field, $rules);


$rules = ['poste_id' => ['type' => 'INT']];
// Gérer la requête avec authentification, assainissement, et validation
$data = $requestHandler->handleRequest($data, $rules);


$query = "UPDATE grid_poste SET nom=:nom, 
                                numero = :numero,
                                association_id=:association_id, 
                                zone_id =:zone_id, 
                                poste_type_id =:poste_type_id, 
                                horaire_id =:horaire_id 
                            WHERE id = :poste_id AND client_id =:client_id";
$stmt = $dbh->prepare($query);


$stmt->bindParam(':nom',  $form_field['poste_nom']);
$stmt->bindParam(':numero',  $form_field['poste_num']);
$stmt->bindParam(':association_id',  $form_field['poste_association']);
$stmt->bindParam(':zone_id',  $form_field['poste_zone']);
$stmt->bindParam(':poste_type_id',  $form_field['poste_type']);
$stmt->bindParam(':horaire_id',  $form_field['horaire_id']);
$stmt->bindParam(':poste_id',  $data['poste_id']);
$stmt->bindParam(':client_id',  $_SESSION['client_actif']);
try {
    $stmt->execute();
} catch (PDOException $e) {
    echo "Erreur SQL : " . $e->getMessage();
}

exit($responseHandler->sendResponse(true,'Poste mise à jour'));
?>