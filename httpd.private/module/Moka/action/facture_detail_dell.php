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
    'facture_detail_id' => ['type' => 'INT']
];
// Gérer la requête avec authentification, assainissement, et validation
$data = $requestHandler->handleRequest($data, $rules);


$stmt = $dbh->prepare("SELECT facture_id,montant FROM moka_facture_detail WHERE id = :id");
$stmt->execute(['id' => $data['facture_detail_id']]);
$result = $stmt->fetch();
$factureId = $result['facture_id'];


// Compter les entrées pour ce facture_id
$stmt = $dbh->prepare("SELECT COUNT(*) AS count FROM moka_facture_detail WHERE facture_id = :facture_id");
$stmt->execute(['facture_id' => $factureId]);
$resultat = $stmt->fetch();
$count = $resultat['count'];


//si count > 1 on est ok de supprimer l'entrée.
if($count > 1){
    $stmt = $dbh->prepare("
            DELETE
            FROM moka_facture_detail 
            WHERE id = :id
        
    ");
    $stmt->execute(['id' => $data['facture_detail_id']]);
    majFactureMontant($dbh, $factureId);
    $responseHandler->addData('deleted', 'true');


    exit($responseHandler->sendResponse(true,'Détail supprimé.'));
}else{
    exit($responseHandler->sendResponse(true,'Une facture ne peux pas être vide.'));
}
?>