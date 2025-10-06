<?php

require_once dirname(__DIR__, 3) . '/config.php';
require_once dirname(__DIR__, 3) . '/sql.php';
require_once dirname(__DIR__, 3) . '/classes/ResponseHandler.php';
require_once dirname(__DIR__, 3) . '/classes/RequestHandler.php';
require_once dirname(__DIR__, 3) . '/classes/DBHandler.php';
require_once dirname(__DIR__, 1) . '/fonctions/factureInsert.php';



$responseHandler = new ResponseHandler();
$requestHandler = new RequestHandler();

$rules = [
    'comment' => ['type' => 'string', 'max_length' => 250],
    'prestation' => ['type' => 'string', 'max_length' => 250],
    'nb_hr' => ['type' => 'INT'],
];

// Gérer la requête avec authentification, assainissement, et validation
$data = $requestHandler->handleRequest($data, $rules);
$prestation = explode("/",$data['prestation']);

$query = "SELECT * FROM moka_prestation_ebrigade WHERE P_ID = :P_ID AND E_CODE = :E_CODE  AND client_id = :client_id";
$stmt = $dbh->prepare($query);
$stmt->bindParam(':P_ID', $prestation[2]);
$stmt->bindParam(':E_CODE', $prestation[0]);
$stmt->bindParam(':client_id',  $_SESSION['client_actif']);
$stmt->execute();



// Check if any rows are returned, which would indicate permission
if($stmt->rowCount() >= 1) {
    exit($responseHandler->sendResponse(true, "Une demande pour cette garde est déjà encodées."));
}

try{
    $query = "INSERT INTO moka_prestation_ebrigade SET  E_CODE =:E_CODE, 
                                                        client_id = :client_id, 
                                                        P_ID = :P_ID, 
                                                        heure = :nb_heure, 
                                                        commentaire =:commentaire, 
                                                        E_LIBELLE = :E_LIBELLE , 
                                                        date = :date";
    $stmt = $dbh->prepare($query);
    $stmt->bindParam(':E_CODE', $prestation[0]);
    $stmt->bindParam(':P_ID', $prestation[2]);
    $stmt->bindParam(':E_LIBELLE', $prestation[3]);
    $stmt->bindParam(':date', $prestation[4]);
    $stmt->bindParam(':nb_heure', $data['nb_hr']);
    $stmt->bindParam(':commentaire', $data['comment']);
    $stmt->bindParam(':client_id',  $_SESSION['client_actif']);
    $stmt->execute();
    exit($responseHandler->sendResponse(true,'Prestation enregistrée'));
} catch (PDOException $e) {
    echo "Erreur lors de la récupération des remunerations : " . $e->getMessage();
    exit;
}