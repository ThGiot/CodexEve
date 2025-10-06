<?php

$clef ='mklnzeijnhje7Y78392h984894za6515698zarzzefsfesfeszzéae';
$url = "https://medteam.be/migre.php";
require_once __DIR__ . '/../httpd.private/config.php';
require_once PRIVATE_PATH . '/sql.php';

function fetchAndInsert($url, $clef, $table, $columnMapping, $dbh) {
    // Récupérer les données
    $donnees = ['clef' => $clef, 'table' => $table];
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url . '?' . http_build_query($donnees));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    if (curl_errno($ch)) {
        throw new Exception('Erreur cURL : ' . curl_error($ch));
    }
    curl_close($ch);
    $resultats = json_decode($response, true);

    // Mapper les données
    $mappedData = [];
    foreach ($resultats as $row) {
        $mappedRow = [];
        foreach ($columnMapping as $jsonKey => $dbColumn) {
            $mappedRow[$dbColumn] = $row[$jsonKey] ?? null;
        }
        $mappedData[] = $mappedRow;
    }

    // Préparation de la requête d'insertion
    $columns = implode(", ", array_keys($mappedData[0]));
    $placeholders = implode(", ", array_fill(0, count($mappedData[0]), "?"));
    $query = "INSERT INTO $table ($columns) VALUES ($placeholders)";
    $stmt = $dbh->prepare($query);

    // Exécution des insertions
    $dbh->beginTransaction();
    foreach ($mappedData as $row) {
        $stmt->execute(array_values($row));
    }
    $dbh->commit();
}

try {
    $columnMappings = [
        'moka_facture' => [
            'mail' => 'email',
            'NISS' => 'niss',
            // ... autres mappages pour moka_facture
        ],
        // ... mappages pour d'autres tables
    ];

    foreach ($columnMappings as $table => $mapping) {
        fetchAndInsert($url, $clef, $table, $mapping, $dbh);
    }
} catch (Exception $e) {
    $dbh->rollBack();
    echo "Erreur lors de l'insertion: " . $e->getMessage();
}
?>
