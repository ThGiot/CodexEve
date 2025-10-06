<?php
require_once dirname(__DIR__, 3) . '/config.php';
require_once dirname(__DIR__, 3) . '/sql.php';
require_once dirname(__DIR__, 3) . '/classes/ResponseHandler.php';
require_once dirname(__DIR__, 3) . '/classes/RequestHandler.php';
require_once dirname(__DIR__, 3) . '/classes/DBHandler.php';



$responseHandler = new ResponseHandler();
$requestHandler = new RequestHandler();

$rules = [
    'facture_id' => ['type' => 'INT'],
    'prestataire_id' => ['type' => 'INT']
];
// Gérer la requête avec authentification, assainissement, et validation
$data = $requestHandler->handleRequest($data, $rules);

//Récupération du p_id du prestataire et maj

$sql = "SELECT * FROM moka_facture WHERE id = :id AND client_id = :client_id";
$stmt = $dbh->prepare($sql);
$stmt->  bindParam(':id', $data['facture_id']);
$stmt->  bindParam(':client_id', $_SESSION['client_actif']);
$stmt->execute();
$p_id = $stmt->fetch();
$p_id = $p_id['p_id'];


$query = "UPDATE moka_prestataire SET p_id = :p_id WHERE id = :prestataire_id";
$stmt = $dbh->prepare($query);
$stmt->  bindParam(':p_id', $p_id);
$stmt->  bindParam(':prestataire_id', $data['prestataire_id']);
$stmt->execute();

//Récupération des infos du prestataire
$sql = "SELECT * FROM moka_prestataire WHERE id = :id AND client_id = :client_id";
$stmt = $dbh->prepare($sql);
$stmt->  bindParam(':id', $data['prestataire_id']);
$stmt->  bindParam(':client_id', $_SESSION['client_actif']);
$stmt->execute();
$prestataire = $stmt->fetch();

//Récupération du numero de facture
$sql = "SELECT MAX(numero)  FROM moka_facture WHERE client_id = :client_id";
$stmt = $dbh->prepare($sql);
$stmt->  bindParam(':client_id', $_SESSION['client_actif']);
$stmt->execute();
$numero = $stmt->fetchColumn();
$numero++;

$stmt = $dbh->prepare("
    UPDATE moka_facture
    SET numero = :numero,
        date = NOW(),
        niss = :niss,
        bce = :bce,
        compte = :compte,
        adresse = :adresse,
        prestataire_id = :prestataire_id
    WHERE id = :id
");

$result = $stmt->execute([
    'numero' => $numero,
    'niss' => $prestataire['niss'],
    'bce' => $prestataire['bce'],
    'compte' => $prestataire['compte'],
    'adresse' => $prestataire['adresse'],
    'prestataire_id' => $prestataire['id'],
    'id' => $data['facture_id']
]);

if ($result) {
    exit($responseHandler->sendResponse(true, 'Facture Attribuée'));
} else {
    // Récupère l'erreur de la base de données
    $errorInfo = $stmt->errorInfo();
    exit($responseHandler->sendResponse(false, 'Erreur lors de la mise à jour: ' . $errorInfo[2]));
}

?>