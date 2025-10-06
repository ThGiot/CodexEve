<?php

require_once dirname(__DIR__, 3) . '/config.php';
require_once dirname(__DIR__, 3) . '/sql.php';
require_once dirname(__DIR__, 3) . '/classes/ResponseHandler.php';
require_once dirname(__DIR__, 3) . '/classes/RequestHandler.php';
require_once dirname(__DIR__, 3) . '/classes/DBHandler.php';
require_once dirname(__DIR__, 1) . '/fonctions/majFactureMontant.php';



$responseHandler = new ResponseHandler();
$requestHandler = new RequestHandler();

$rules = [
    'designation' => ['type' => 'string', 'max_length' => 40],
    'montant' => ['type' => 'INT'],
    'facture_id' => ['type' => 'INT']
];
// Gérer la requête avec authentification, assainissement, et validation
$data = $requestHandler->handleRequest($data, $rules);

//On récupère l'id de l'analytique


try{
    $query = "INSERT INTO  moka_facture_detail (facture_id, designation,montant) VALUES(:facture_id, :designation,:montant)";
    $stmt = $dbh->prepare($query);
   
    $stmt->bindParam(':facture_id', $data['facture_id']);
    $stmt->bindParam(':designation', $data['designation']);
    $stmt->bindParam(':montant', $data['montant']);
    $stmt->execute();

    majFactureMontant($dbh, $data['facture_id']);

    exit($responseHandler->sendResponse(true,'Détail ajouté.'));
}catch (PDOException $e) {
    echo $responseHandler->sendResponse(false, "Erreur  : " . $e->getMessage());
    exit();
    }
?>