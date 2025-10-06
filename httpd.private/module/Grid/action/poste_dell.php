<?php

require_once dirname(__DIR__, 3) . '/config.php';
require_once dirname(__DIR__, 3) . '/sql.php';
require_once dirname(__DIR__, 3) . '/classes/ResponseHandler.php';
require_once dirname(__DIR__, 3) . '/classes/RequestHandler.php';
require_once dirname(__DIR__, 3) . '/classes/DBHandler.php';



$responseHandler = new ResponseHandler();
$requestHandler = new RequestHandler();

$rules = [
    'poste_id' => ['type' => 'INT']
];
// Gérer la requête avec authentification, assainissement, et validation
$data = $requestHandler->handleRequest($data, $rules);

//Vérification que cet aactivite appartient au client
$query = "SELECT * FROM  grid_poste WHERE client_id =:client_id AND id =:poste_id";
$stmt = $dbh->prepare($query);
$stmt->bindParam(':client_id',  $_SESSION['client_actif']);
$stmt->bindParam(':poste_id', $data['poste_id']);
$stmt->execute();
// Check if any rows are returned, which would indicate permission
if($stmt->rowCount() == 0) {
    exit($responseHandler->sendResponse(false, "Vous ne possedez pas ce poste."));
}

//Vérification que cet aactivite appartient au client
$query = "DELETE FROM grid_poste WHERE id =:poste_id";
$stmt = $dbh->prepare($query);
$stmt->bindParam(':poste_id', $data['poste_id']);
$stmt->execute();


exit($responseHandler->sendResponse(true, "Poste supprimé."));

?>