<?php

require_once dirname(__DIR__, 3) . '/config.php';
require_once dirname(__DIR__, 3) . '/sql.php';
require_once dirname(__DIR__, 3) . '/classes/ResponseHandler.php';
require_once dirname(__DIR__, 3) . '/classes/RequestHandler.php';
require_once dirname(__DIR__, 3) . '/classes/DBHandler.php';

$responseHandler = new ResponseHandler();
$requestHandler = new RequestHandler();
$dbh = new DBHandler();

// Définir les règles pour la validation de la date
$rules = [
    'date' => ['type' => 'string', 'max_length' => 10, 'pattern' => '/^\d{4}-\d{2}-\d{2}$/'],
];

// Gérer la requête avec authentification, assainissement, et validation
$data = $requestHandler->handleRequest($_GET, $rules);
$date = $data['date'];

try {
    // Désactiver la vérification des clés étrangères
    $dbh->beginTransaction();
    $dbh->exec("SET FOREIGN_KEY_CHECKS = 0");

    // Insérer les enregistrements de moka_facture_correction_archive vers moka_facture_correction
    $insertQuery = "
        INSERT INTO moka_facture_correction (prestataire_id, designation, montant, analytique_id, client_id)
        SELECT 
            mf.prestataire_id, 
            mfc.designation, 
            mfc.montant, 
            ma.id AS analytique_id, 
            mf.client_id
        FROM moka_facture_correction_archive mfc
        JOIN moka_facture mf ON mf.id = mfc.facture_id
        JOIN moka_analytique ma ON ma.analytique = mf.analytique
        WHERE DATE(mf.date) = :date
    ";
    $stmt = $dbh->prepare($insertQuery);
    $stmt->bindParam(':date', $date);
    $stmt->execute();

    // Supprimer les enregistrements enfants dans moka_facture_detail
    $deleteDetailQuery = "
        DELETE FROM moka_facture_detail 
        WHERE facture_id IN (
            SELECT id FROM moka_facture WHERE DATE(date) = :date
        )
    ";
    $stmt = $dbh->prepare($deleteDetailQuery);
    $stmt->bindParam(':date', $date);
    $stmt->execute();

    // Supprimer les enregistrements enfants dans moka_facture_correction_archive
    $deleteArchiveQuery = "
        DELETE FROM moka_facture_correction_archive 
        WHERE facture_id IN (
            SELECT id FROM moka_facture WHERE DATE(date) = :date
        )
    ";
    $stmt = $dbh->prepare($deleteArchiveQuery);
    $stmt->bindParam(':date', $date);
    $stmt->execute();

    // Supprimer les enregistrements dans moka_facture
    $deleteFactureQuery = "
        DELETE FROM moka_facture 
        WHERE DATE(date) = :date
    ";
    $stmt = $dbh->prepare($deleteFactureQuery);
    $stmt->bindParam(':date', $date);
    $stmt->execute();

    // Réactiver la vérification des clés étrangères
    $dbh->exec("SET FOREIGN_KEY_CHECKS = 1");
    
    // Commit the transaction
    $dbh->commit();
    exit($responseHandler->sendResponse(true, "Opération réussie pour la date : $date"));

} catch (Exception $e) {
    // Rollback en cas d'erreur
    $dbh->rollBack();
    exit($responseHandler->sendResponse(false, "Erreur lors de l'opération : " . $e->getMessage()));
}
?>
