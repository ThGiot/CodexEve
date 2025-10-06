<?php

require_once dirname(__DIR__, 3) . '/config.php';
require_once dirname(__DIR__, 3) . '/sql.php';
require_once dirname(__DIR__, 3) . '/classes/ResponseHandler.php';
require_once dirname(__DIR__, 3) . '/classes/RequestHandler.php';



$responseHandler = new ResponseHandler();
$requestHandler = new RequestHandler();

// Définir les règles de validation
$rules = [
    'analytique_id' => ['type' => 'INT'],
    'prestataire_id' => ['type' => 'INT'],
    'montant' => ['type' => 'string', 'max_length' => 400],
    'designation' => ['type' => 'string', 'max_length' => 400]
];
// Gérer la requête avec authentification, assainissement, et validation
$data = $requestHandler->handleRequest($data, $rules);

$query = "INSERT INTO moka_facture_correction SET   prestataire_id =:prestataire_id, 
                                                    analytique_id =:analytique_id, 
                                                    client_id =:client_id,
                                                    designation =:designation,
                                                    montant =:montant
                                                    ";
$stmt = $dbh->prepare($query);
$stmt->bindParam(':montant', $data['montant']);
$stmt->bindParam(':analytique_id', $data['analytique_id']);
$stmt->bindParam(':designation', $data['designation']);
$stmt->bindParam(':prestataire_id', $data['prestataire_id']);
$stmt->bindParam(':client_id',  $_SESSION['client_actif']);
$stmt->execute();
exit($responseHandler->sendResponse(true,'Correction ajoutée !'));
?>