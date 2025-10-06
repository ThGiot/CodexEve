<?php

require_once dirname(__DIR__, 3) . '/config.php';
require_once dirname(__DIR__, 3) . '/sql.php';
require_once dirname(__DIR__, 3) . '/classes/ResponseHandler.php';
require_once dirname(__DIR__, 3) . '/classes/RequestHandler.php';
require_once dirname(__DIR__, 3) . '/classes/DBHandler.php';



$responseHandler = new ResponseHandler();
$requestHandler = new RequestHandler();

$rules = [
    'to_maj' => ['type' => 'string', 'max_length' => 40],
    'value' => ['type' => 'string', 'max_length' => 40],
    'remuneration_id' => ['type' => 'INT'],
    'activite_id' => ['type' => 'INT']
];
// Gérer la requête avec authentification, assainissement, et validation
$data = $requestHandler->handleRequest($data, $rules);

//Vérification que cet aactivite appartient au client
$query = "SELECT * FROM moka_activite_remuneration mr JOIN moka_activite ma ON ma.id = mr.activite_id WHERE client_id =:client_id AND mr.id =:remuneration_id";
$stmt = $dbh->prepare($query);
$stmt->bindParam(':client_id',  $_SESSION['client_actif']);
$stmt->bindParam(':remuneration_id', $data['remuneration_id']);
$stmt->execute();
// Check if any rows are returned, which would indicate permission
if($stmt->rowCount() == 0) {
    exit($responseHandler->sendResponse(true, "Vous ne possedez pas cette activitée."));
}


//Si on modifie le Grade on vérifie qu'il n'existe pas encore
if($data['to_maj'] == 'grade'){
    $query = "SELECT * FROM moka_activite_remuneration WHERE grade =:grade AND activite_id =:activite_id";
    $stmt = $dbh->prepare($query);
    $stmt->bindParam(':activite_id',  $data['activite_id']);
    $stmt->bindParam(':grade',  $data['value']);
    $stmt->execute();
    // Check if any rows are returned, which would indicate permission
    if($stmt->rowCount() > 1) {
        exit($responseHandler->sendResponse(true, "Ce Grade existe déjà pour cette activitée"));
    }
}elseif(!is_numeric($data['value'])){
    exit($responseHandler->sendResponse(true, "Le montant doit etre un nombre "));
   
}
$allowedColumns = ['grade', 'montant_perm', 'montant_garde']; // Add all allowed columns here

if (!in_array($data['to_maj'], $allowedColumns)) {
    exit($responseHandler->sendResponse(true, "MAJ INTERDITE !"));
}

$query = "UPDATE moka_activite_remuneration SET " . $data['to_maj'] . " = :value_param WHERE id = :id";
$stmt = $dbh->prepare($query);
$stmt->bindParam(':value_param', $data['value']);
$stmt->bindParam(':id', $data['remuneration_id']);
$stmt->execute();
exit($responseHandler->sendResponse(true,'Activitée mise à jour'));
?>