<?php

require_once dirname(__DIR__, 3) . '/config.php';
require_once dirname(__DIR__, 3) . '/sql.php';
require_once dirname(__DIR__, 3) . '/classes/ResponseHandler.php';
require_once dirname(__DIR__, 3) . '/classes/RequestHandler.php';
require_once dirname(__DIR__, 3) . '/classes/DBHandler.php';



$responseHandler = new ResponseHandler();
$requestHandler = new RequestHandler();

$rules = [
    'designation' => ['type' => 'string', 'max_length' => 500],
    'analytique_id' => ['type' => 'string', 'max_length' => 40],
    'facture_id' => ['type' => 'INT']
];
// Gérer la requête avec authentification, assainissement, et validation
$data = $requestHandler->handleRequest($data, $rules);

//On récupère l'id de l'analytique

$sql = "SELECT * FROM moka_analytique WHERE client_id = :client_id AND id=:analytique";
            $stmt = $dbh->prepare($sql);
            $stmt->bindParam(':client_id', $_SESSION['client_actif']);
            $stmt->bindParam(':analytique', $data['analytique_id']);
            $stmt->execute();
            $analytique = $stmt->fetch(PDO::FETCH_ASSOC);
$analytique=$analytique['analytique'];
try{
    $query = "UPDATE moka_facture SET designation = :designation, analytique = :analytique WHERE id = :id AND client_id = :client_id";
    $stmt = $dbh->prepare($query);
    $stmt->bindParam(':designation', $data['designation']);
    $stmt->bindParam(':analytique', $analytique);
    $stmt->bindParam(':id', $data['facture_id']);
    $stmt->bindParam(':client_id', $_SESSION['client_actif']);
    $stmt->execute();
    exit($responseHandler->sendResponse(true,'Facture mise à jour'));
}catch (PDOException $e) {
    echo $responseHandler->sendResponse(false, "Erreur  : " . $e->getMessage());
    exit();
    }
?>