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
    'date' => ['type' => 'string', 'max_length' => 200],
];
// Gérer la requête avec authentification, assainissement, et validation
$form_field =[];
foreach($data['form_field'] as $field){
    $form_field[$field['name']] = $field['value'];
    
}

$form_field = $requestHandler->handleRequest($form_field, $rules);



$dbHandler = new DbHandler($dbh);
$table = 'thor_evenement';
$data = $form_field;
//Verication que le nom n'existe pas déjà pour cette même date pour ce client
try{
    $query = "SELECT * FROM thor_evenement WHERE 
                            nom = :nom AND
                            date = :date AND
                            client_id = :client_id";
    $stmt = $dbh->prepare($query);
    $stmt->bindParam(':client_id', $_SESSION['client_actif']);
    $stmt->bindParam(':nom', $data['nom']);
    $stmt->bindParam(':date', $data['date']);
    $stmt->execute();
}catch (PDOException $e) {
    echo $responseHandler->sendResponse(false, "Erreur  : " . $e->getMessage());
    exit();
    }
// Check if any rows are returned, which would indicate permission
if($stmt->rowCount() > 0) {
    exit($responseHandler->sendResponse(true, "Cet événement existe déjà !"));
}




try{
    $query = "INSERT INTO thor_evenement (nom, date, client_id) VALUES (:nom, :date, :client_id)";
    $stmt = $dbh->prepare($query);
    $stmt->bindParam(':client_id', $_SESSION['client_actif']);
    $stmt->bindParam(':nom', $data['nom']);
    $stmt->bindParam(':date', $data['date']);
    $stmt->execute();
    
} catch (PDOException $e) {
    echo $responseHandler->sendResponse(true, "Erreur  : " . $e->getMessage());
    exit();
    }

echo $responseHandler->sendResponse(true, $data['date']);

?>