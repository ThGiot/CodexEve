<?php
require_once dirname(__DIR__, 3) . '/classes/DBHandler.php';

$stmt = $dbh->query("SELECT code AS id, label FROM clinical_hospital ORDER BY id ASC");
$hospitals = $stmt->fetchAll();


$stmt = $dbh->query("
    SELECT id, slug, title, type, system, category, summary, body, version, created_at AS createdAt, updated_at AS updatedAt
    FROM clinical_procedure
    ORDER BY id ASC
");
$procedures = $stmt->fetchAll();

foreach ($procedures as &$proc) {
    // 🔹 Tags
    $sql = "
        SELECT t.name
        FROM clinical_procedure_tag t
        JOIN clinical_procedure_tag_link l ON l.tag_id = t.id
        WHERE l.procedure_id = ?
        ORDER BY t.name ASC
    ";
    $stmt = $dbh->prepare($sql);
    $stmt->execute([$proc['id']]);
    $tags = $stmt->fetchAll(PDO::FETCH_COLUMN);
    $proc['tags'] = $tags;

    // 🔹 Variantes
    $sql = "
        SELECT v.id AS variant_id, h.code AS hospital_code, v.note
        FROM clinical_procedure_variant v
        JOIN clinical_hospital h ON v.hospital_id = h.id
        WHERE v.procedure_id = ?
        ORDER BY h.code ASC
    ";
    $stmt = $dbh->prepare($sql);
    $stmt->execute([$proc['id']]);
    $variantsRaw = $stmt->fetchAll();

    $variants = [];
    foreach ($variantsRaw as $v) {
        // Récupérer les blocs HTML associés à la variante
        $stmtBlocks = $dbh->prepare("
            SELECT html FROM clinical_procedure_variant_block WHERE variant_id = ?
        ");
        $stmtBlocks->execute([$v['variant_id']]);
        $blocks = $stmtBlocks->fetchAll(PDO::FETCH_ASSOC);

        $variants[$v['hospital_code']] = [
            'note' => $v['note'],
            'blocks' => $blocks
        ];
    }
    $proc['variants'] = $variants;
}

// 🔹 Retour du tableau global
return [
    'meta' => [
        'version' => '0.1.0',
        'about' => "Pilote PWA pour consultation rapide des procédures et fiches SMUR. Contenu issu de la base de données."
    ],
    'hospitals' => $hospitals,
    'procedures' => $procedures
];

