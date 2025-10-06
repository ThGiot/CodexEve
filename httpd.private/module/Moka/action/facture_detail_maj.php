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
    'to_maj' => ['type' => 'string', 'max_length' => 40],
    'value' => ['type' => 'string', 'max_length' => 400],
    'facture_detail_id' => ['type' => 'INT']
];
// Gérer la requête avec authentification, assainissement, et validation
$data = $requestHandler->handleRequest($data, $rules);
if(empty($data['value'])){
    exit($responseHandler->sendResponse(false, " : Le champ est vide"));
}
$allowedColumns = ['montant', 'designation']; 
if (!in_array($data['to_maj'], $allowedColumns)) {
    exit($responseHandler->sendResponse(true, "MAJ INTERDITE !"));
}

$query = "UPDATE moka_facture_detail SET " . $data['to_maj'] . " = :value_param WHERE id = :id";
$stmt = $dbh->prepare($query);
$stmt->bindParam(':value_param', $data['value']);
$stmt->bindParam(':id', $data['facture_detail_id']);
$stmt->execute();

if($data['to_maj'] =='montant'){
    $stmt = $dbh->prepare("SELECT facture_id,montant FROM moka_facture_detail WHERE id = :id");
    $stmt->execute(['id' => $data['facture_detail_id']]);
    $result = $stmt->fetch();
    $factureId = $result['facture_id'];
    majFactureMontant($dbh, $factureId);
}
exit($responseHandler->sendResponse(true,'Facture mise à jour'));
?>