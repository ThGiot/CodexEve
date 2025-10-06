<?php

require_once dirname(__DIR__, 3) . '/config.php';
require_once dirname(__DIR__, 3) . '/sql.php';
require_once dirname(__DIR__, 3) . '/classes/ResponseHandler.php';
require_once dirname(__DIR__, 3) . '/classes/RequestHandler.php';
require_once dirname(__DIR__, 3) . '/classes/DBHandler.php';



$responseHandler = new ResponseHandler();
$requestHandler = new RequestHandler();

// Définir les règles de validation
$rules = [
    'grade' => ['type' => 'string', 'max_length' => 40],
    'tarif_perm' => ['type' => 'string', 'max_length' => 40],
    'tarif_garde' => ['type' => 'string', 'max_length' => 40],
    'activite_id' => ['type' => 'int']
];
// Gérer la requête avec authentification, assainissement, et validation
$data = $requestHandler->handleRequest($data, $rules);

//Vérification que cet analytlique de client n'existe pas encore
try {
    $query = "  SELECT mar.id FROM moka_activite_remuneration mar JOIN moka_activite ma ON ma.id = mar.activite_id 
                WHERE mar.activite_id =:activite_id AND client_id =:client_id AND grade =:grade";
    $stmt = $dbh->prepare($query);
    $stmt->bindParam(':activite_id', $data['activite_id']);
    $stmt->bindParam(':grade', $data['grade']);
    $stmt->bindParam(':client_id',  $_SESSION['client_actif']);
    $stmt->execute();
} catch (PDOException $e) {
    echo "Erreur lors de la récupération des remunerations : " . $e->getMessage();
    exit;
}
// Check if any rows are returned, which would indicate permission
if($stmt->rowCount() >= 1) {
    exit($responseHandler->sendResponse(true, "Ce grade est déjà configuré pour cette activité !."));
}
try{
    $query = "INSERT INTO moka_activite_remuneration SET activite_id =:activite_id, grade = :grade, montant_perm =:montant_perm, montant_garde = :montant_garde";
    $stmt = $dbh->prepare($query);
    $stmt->bindParam(':activite_id', $data['activite_id']);
    $stmt->bindParam(':grade', $data['grade']);
    $stmt->bindParam(':montant_garde', $data['tarif_garde']);
    $stmt->bindParam(':montant_perm', $data['tarif_perm']);
    $stmt->execute();
    exit($responseHandler->sendResponse(true,'Grade Configuré'));
} catch (PDOException $e) {
    echo "Erreur lors de la récupération des remunerations : " . $e->getMessage();
    exit;
}
?>