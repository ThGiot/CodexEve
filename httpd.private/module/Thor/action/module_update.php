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
    'module_id' => ['type' => 'int'],
    'evenement_id' => ['type' => 'int'],
];
// Gérer la requête avec authentification, assainissement, et validation
$data = $requestHandler->handleRequest($data, $rules);
foreach ($data['form_field'] as $field) {
    $data[$field['name']] = $field['value'];
}
unset($data['form_field']);


//Verication que le nom n'existe pas déjà pour cette même date pour ce client
try{
    $query = "SELECT * FROM thor_module WHERE 
                            nom = :nom AND
                            date_debut = :date_debut AND
                            evenement_id = :evenement_id";
    $stmt = $dbh->prepare($query);
    $stmt->bindParam(':evenement_id', $data['evenement_id']);
    $stmt->bindParam(':nom', $data['nom']);
    $stmt->bindParam(':date_debut', $data['date_debut']);
    $stmt->execute();
}catch (PDOException $e) {
    echo $responseHandler->sendResponse(false, "Erreur  : " . $e->getMessage());
    exit();
    }
// Check if any rows are returned, which would indicate permission
if($stmt->rowCount() > 1) {
    exit($responseHandler->sendResponse(true, "Ce module existe déjà !"));
}




try{
    $query = "UPDATE thor_module SET nom = :nom, groupe = :groupe, nb_volontaire = :nb_volontaire, date_debut = :date_debut, date_fin = :date_fin, heure_debut = :heure_debut, heure_fin = :heure_fin WHERE id = :id";
    $stmt = $dbh->prepare($query);
    $stmt->bindParam(':id', $data['module_id']);
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

echo $responseHandler->sendResponse(true, "Module mis à jour !");

?>