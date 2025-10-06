<?php

require_once dirname(__DIR__, 3) . '/config.php';
require_once dirname(__DIR__, 3) . '/sql.php';
require_once dirname(__DIR__, 3) . '/classes/ResponseHandler.php';
require_once dirname(__DIR__, 3) . '/classes/RequestHandler.php';



$responseHandler = new ResponseHandler();
$requestHandler = new RequestHandler();

$rules = [
    
    'prestation_id' => ['type' => 'INT'],
    'statut' => ['type' => 'INT']

];
// Gérer la requête avec authentification, assainissement, et validation
$data = $requestHandler->handleRequest($data, $rules);

try{
    $query = "UPDATE moka_prestation_ebrigade SET statut = :statut WHERE id = :prestation_id AND client_id = :client_id";
    $stmt = $dbh->prepare($query);
    $stmt->bindParam(':statut', $data['statut']);
    $stmt->bindParam(':prestation_id', $data['prestation_id']);
    $stmt->bindParam(':client_id', $_SESSION['client_actif']);
    $stmt->execute();
    exit($responseHandler->sendResponse(true,'Statut mise à jour'));
}catch (PDOException $e) {
    echo $responseHandler->sendResponse(false, "Erreur  : " . $e->getMessage());
    exit();
    }
?>