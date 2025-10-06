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

$form_field = $requestHandler->handleRequest($form_field, $rules);
$form_field['client_id'] =  $_SESSION['client_actif'];


$dbHandler = new DbHandler($dbh);
$table = 'moka_prestataire';
$exclude = ['p_id'];
$data = $form_field;
//Verication que l'adresse email n'est pas déjà utilisé

$query = "SELECT * FROM moka_prestataire WHERE 
                        email = :email AND
                        client_id = :client_id";
$stmt = $dbh->prepare($query);
$stmt->bindParam(':client_id', $_SESSION['client_actif']);
$stmt->bindParam(':email', $data['email']);
$stmt->execute();

// Check if any rows are returned, which would indicate permission
if($stmt->rowCount() > 0) {
    exit($responseHandler->sendResponse(true, "Ce prestataire existe déjà !"));
}




try{
    $dbHandler -> insert($table, $data,$exclude);
} catch (PDOException $e) {
    echo $responseHandler->sendResponse(false, "Erreur  : " . $e->getMessage());
    exit();
    }

echo $responseHandler->sendResponse(true, 'Informations mises à jour');

?>