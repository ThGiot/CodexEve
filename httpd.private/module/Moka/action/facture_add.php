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
    'designation_facture' => ['type' => 'string', 'max_length' => 250],
    'designation_detail' => ['type' => 'string', 'max_length' => 250],
    'montant' => ['type' => 'INT'],
    'prestataire_id' => ['type' => 'INT'],
    'analytique_id' => ['type' => 'INT']
];
// Gérer la requête avec authentification, assainissement, et validation
$data = $requestHandler->handleRequest($data, $rules);

$sql = "SELECT * FROM moka_prestataire WHERE id = :id AND client_id= :client_id";
$stmt = $dbh->prepare($sql);
$stmt ->bindParam(':id', $data['prestataire_id']);
$stmt ->bindParam(':client_id', $_SESSION['client_actif']);
$stmt->execute();
$prestataire = $stmt->fetch(PDO::FETCH_ASSOC);

$sql = "SELECT * FROM moka_analytique WHERE id = :id AND client_id= :client_id";
$stmt = $dbh->prepare($sql);
$stmt ->bindParam(':id', $data['analytique_id']);
$stmt ->bindParam(':client_id', $_SESSION['client_actif']);
$stmt->execute();
$analytique = $stmt->fetch(PDO::FETCH_ASSOC);


$facture_id =   factureInsert(  dbh: $dbh,
                designation: $data['designation_facture'], 
                data: $prestataire,
                analytique: $analytique['analytique'],
                montant: $data['montant'], 
                client_id: $_SESSION['client_actif'], 
                p_id: null);





try{
    $query = "INSERT INTO  moka_facture_detail (facture_id, designation,montant) VALUES(:facture_id, :designation,:montant)";
    $stmt = $dbh->prepare($query);
   
    $stmt->bindParam(':facture_id', $facture_id);
    $stmt->bindParam(':designation', $data['designation_detail']);
    $stmt->bindParam(':montant', $data['montant']);
    $stmt->execute();
    exit($responseHandler->sendResponse(true,'Facture Ajoutée.'));
}catch (PDOException $e) {
    echo $responseHandler->sendResponse(false, "Erreur  : " . $e->getMessage());
    exit();
    }
?>