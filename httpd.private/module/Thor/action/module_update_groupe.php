<?php

require_once dirname(__DIR__, 3) . '/classes/RequestHandler.php';
require_once dirname(__DIR__, 3) . '/classes/ResponseHandler.php';
require_once dirname(__DIR__, 3) . '/classes/DBHandler.php';
require_once dirname(__DIR__, 3) . '/sql.php';

$requestHandler = new RequestHandler();
$responseHandler = new ResponseHandler();

// Définir les règles de validation
$rules = [
    'groupe' => ['type' => 'string', 'max_length' => 200],
    'evenement_id' => ['type' => 'int'],
];
// Gérer la requête avec authentification, assainissement, et validation
$data = $requestHandler->handleRequest($data, $rules);






try{
    $query = "UPDATE thor_module SET groupe = :groupe WHERE evenement_id = :evenement_id AND groupe = :old_groupe";
    $stmt = $dbh->prepare($query);
    $stmt->bindParam(':evenement_id', $data['evenement_id']);
    $stmt->bindParam(':groupe', $data['groupe']);
    $stmt->bindParam(':old_groupe', $data['old_groupe']);
    $stmt->execute();
    
} catch (PDOException $e) {
    echo $responseHandler->sendResponse(true, "Erreur  : " . $e->getMessage());
    exit();
    }

echo $responseHandler->sendResponse(true, "Groupe mis à jour !");

?>