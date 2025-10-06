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
    'groupe' => ['type' => 'string', 'max_length' => 200],
    'date_debut' => ['type' => 'string', 'max_length' => 200],
    'date_fin' => ['type' => 'string', 'max_length' => 200],
    'heure_debut' => ['type' => 'string', 'max_length' => 200],
    'heure_fin' => ['type' => 'string', 'max_length' => 200],
    'volontaire' => ['type' => 'int'],
];
// Gérer la requête avec authentification, assainissement, et validation
$form_field =[];
foreach($data['form_field'] as $field){
    $form_field[$field['name']] = $field['value'];
    
}

$form_field = $requestHandler->handleRequest($form_field, $rules);
$rules = [
    'evenement_id' => ['type' => 'int'],
];
// Gérer la requête avec authentification, assainissement, et validation
$data = $requestHandler->handleRequest($data, $rules);
foreach ($data['form_field'] as $field) {
    $data[$field['name']] = $field['value'];
}
unset($data['form_field']);


try{
    $query = "INSERT INTO thor_module (evenement_id,nom,groupe,nb_volontaire,date_debut,date_fin,heure_debut,heure_fin ) VALUES (:evenement_id,:nom,:groupe,:nb_volontaire,:date_debut,:date_fin,:heure_debut,:heure_fin )";
    $stmt = $dbh->prepare($query);
    $stmt->bindParam(':evenement_id', $data['evenement_id']);
    $stmt->bindParam(':nom', $data['nom']);
    $stmt->bindParam(':date_debut', $data['date_debut']);
    $stmt->bindParam(':heure_debut', $data['heure_debut']);
    $stmt->bindParam(':date_fin', $data['date_fin']);
    $stmt->bindParam(':heure_fin', $data['heure_fin']);
    $stmt->bindParam(':nb_volontaire', $data['volontaire']);
    $stmt->bindParam(':groupe', $data['groupe']);
    $stmt->execute();
    
} catch (PDOException $e) {
    echo $responseHandler->sendResponse(true, "Erreur  : " . $e->getMessage());
    exit();
    }

echo $responseHandler->sendResponse(true, "Module Ajouté !");

?>