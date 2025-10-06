<?php

require_once dirname(__DIR__, 3) . '/classes/RequestHandler.php';
require_once dirname(__DIR__, 3) . '/classes/ResponseHandler.php';
require_once dirname(__DIR__, 3) . '/sql.php';

$requestHandler = new RequestHandler();
$responseHandler = new ResponseHandler();

// Définir les règles de validation
$rules = [
    'infos' => ['type' => 'string', 'max_length' => 200],
    'nom' => ['type' => 'string', 'max_length' => 200],
    'date' => ['type' => 'string', 'max_length' => 200],
    'evenement_id' => ['type' => 'int'],
];
// Gérer la requête avec authentification, assainissement, et validation
$data = $requestHandler->handleRequest($data, $rules);






try{
    $query = "UPDATE thor_evenement SET nom =:nom, date=:date,infos=:infos WHERE id = :evenement_id ";
    $stmt = $dbh->prepare($query);
    $stmt->bindParam(':evenement_id', $data['evenement_id']);
    $stmt->bindParam(':nom', $data['nom']);
    $stmt->bindParam(':date', $data['date']);
    $stmt->bindParam(':infos', $data['infos']);
    $stmt->execute();
    
} catch (PDOException $e) {
    echo $responseHandler->sendResponse(true, "Erreur  : " . $e->getMessage());
    exit();
    }

echo $responseHandler->sendResponse(true, "Evénement mis à jour !");

?>