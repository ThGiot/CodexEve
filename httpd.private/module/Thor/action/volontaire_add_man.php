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
    'nom' => ['type' => 'string', 'max_length' => 200],
    'prenom' => ['type' => 'string', 'max_length' => 200],
    'centre_secours' => ['type' => 'string', 'max_length' => 200]
];
// Gérer la requête avec authentification, assainissement, et validation
$data= $requestHandler->handleRequest($data, $rules);

try {
    $query = "SELECT * FROM thor_volontaire WHERE nom = :nom AND prenom = :prenom";
    $stmt = $dbh->prepare($query);
    $stmt->bindParam(':nom', $data['nom']);
    $stmt->bindParam(':prenom', $data['prenom']);
    $stmt->execute();
} catch (PDOException $e) {
    echo $responseHandler->sendResponse(false, "Erreur  : " . $e->getMessage());
    exit();
}
$null = 0;

    if ($stmt->rowCount() == 0) {
    $query = "INSERT INTO thor_volontaire (nom, prenom, centre_secours, chauffeur) VALUES (:nom, :prenom, :centre_secours, :chauffeur)";
    $stmt = $dbh->prepare($query);
    $stmt->bindParam(':nom', $data['nom']);
    $stmt->bindParam(':prenom', $data['prenom']);
    $stmt->bindParam(':centre_secours', $data['centre_secours']);
    $stmt->bindParam(':chauffeur', $null);
    $stmt->execute();
    $volontaire_id = $dbh->lastInsertId();
    
}else{
    $volontaire = $stmt->fetch(PDO::FETCH_ASSOC);
    $volontaire_id = $volontaire['id'];
}




try{
    $query = "INSERT INTO thor_module_inscription (module_id, volontaire_id) VALUES (:module_id, :volontaire_id)";
    $stmt = $dbh->prepare($query);
    $stmt->bindParam(':volontaire_id', $volontaire_id);
    $stmt->bindParam(':module_id', $data['module_id']);
    $stmt->execute();
    
} catch (PDOException $e) {
    echo $responseHandler->sendResponse(true, "Erreur  : " . $e->getMessage());
    exit();
    }

echo $responseHandler->sendResponse(true, 'Volontaire inscrit');

?>