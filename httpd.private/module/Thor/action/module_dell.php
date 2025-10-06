<?php

require_once dirname(__DIR__, 3) . '/classes/RequestHandler.php';
require_once dirname(__DIR__, 3) . '/classes/ResponseHandler.php';
require_once dirname(__DIR__, 3) . '/classes/DBHandler.php';
require_once dirname(__DIR__, 3) . '/sql.php';

$requestHandler = new RequestHandler();
$responseHandler = new ResponseHandler();

// Définir les règles de validation
$rules = [
    'module_id' => ['type' => 'int'],
    'evenement_id' => ['type' => 'int']
];
// Gérer la requête avec authentification, assainissement, et validation
$data= $requestHandler->handleRequest($data, $rules);

try{
    $query = "DELETE FROM thor_module_inscription WHERE module_id = :module_id ";
    $stmt = $dbh->prepare($query);
    $stmt->bindParam(':module_id', $data['module_id'], PDO::PARAM_INT);
    $stmt->execute();
    $query = "DELETE FROM thor_module WHERE id = :module_id ";
    $stmt = $dbh->prepare($query);
    $stmt->bindParam(':module_id', $data['module_id'], PDO::PARAM_INT);
    $stmt->execute();
    
} catch (PDOException $e) {
    echo $responseHandler->sendResponse(true, "Erreur  : " . $e->getMessage());
    exit();
    }

echo $responseHandler->sendResponse(true, 'Module supprimé');

?>