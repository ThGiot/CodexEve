<?php
require_once __DIR__ . '/../httpd.private/config.php';
require_once __DIR__ . '/../httpd.private/sql.php';
try {
    // Démarrer une transaction
    $dbh->beginTransaction();

    // 1. Trouver les doublons
    $stmt = $dbh->prepare("SELECT id, COUNT(*) as Cnt FROM moka_facture GROUP BY id HAVING COUNT(*) > 1");
    $stmt->execute();
    $duplicates = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($duplicates as $duplicate) {
        // 2. Pour chaque ID en double, trouver le plus grand ID existant
        $stmt = $dbh->prepare("SELECT MAX(id) as max_id FROM moka_facture");
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $maxId = $result['max_id'];

        // 3. Mettre à jour les enregistrements doublons en assignant un nouvel ID (maxId + 1)
        $newId = $maxId + 1;
        $stmt = $dbh->prepare("UPDATE moka_facture SET id = :newId WHERE id = :oldId LIMIT 1"); // Limite à 1 pour éviter de changer tous les doublons en une fois
        $stmt->bindParam(':newId', $newId, PDO::PARAM_INT);
        $stmt->bindParam(':oldId', $duplicate['id'], PDO::PARAM_INT);
        $stmt->execute();

        // Incrémenter maxId pour le prochain doublon
        $maxId++;
    }

    // Valider la transaction
    $dbh->commit();
} catch (PDOException $e) {
    // En cas d'erreur, annuler la transaction
    $dbh->rollBack();
    echo "Erreur : " . $e->getMessage();
}
?>