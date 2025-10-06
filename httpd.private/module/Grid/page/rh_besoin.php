<?php
require_once PRIVATE_PATH . '/classes/Table.php'; 
require_once PRIVATE_PATH . '/classes/PageLayout.php'; 
require_once PRIVATE_PATH . '/classes/createModalContent.php'; 
require_once PRIVATE_PATH . '/classes/Modal.php'; 
require_once dirname(__DIR__, 1) . '/classes/PosteService.php';
require_once dirname(__DIR__, 1) . '/classes/FilterService.php';

$posteService = new PosteService($dbh);
$clientId = (int) $_SESSION['client_actif'];

// ───── Récupération des données horaires volontaires et dispos ─────
$horaireVolontaire = $posteService->getTableauHorairesMultiplies($clientId);
$dispos = $posteService->getDisposParAssociationEtHoraire($clientId);

// ───── Détection des horaires uniques (hors Total) ─────
$horaireNoms = [];
foreach ($horaireVolontaire as $horaires) {
    foreach ($horaires as $nom => $val) {
        if ($nom !== "Total") {
            $horaireNoms[$nom] = true;
        }
    }
}
ksort($horaireNoms);
$columns = array_merge(["Association"], array_keys($horaireNoms), ["Total", "Action"]);

// ───── Création de la table ─────
$table = new Table(
    title: "Résumé des horaires volontaires (périodes × postes)",
    columns: $columns,
    id: "horaireVolontaireTable"
);

// ───── Récupération des ID association (nom => id) ─────
$assos = $posteService->getAssociations($clientId);
$mapNomToId = [];
foreach ($assos as $a) {
    $mapNomToId[$a['nom']] = $a['id'];
}

// ───── Remplissage des lignes ─────
foreach ($horaireVolontaire as $association => $horaires) {
    $row = ["Association" => $association];

    foreach (array_keys($horaireNoms) as $nom) {
        $besoin = $horaires[$nom] ?? 0;
        $dispo = $dispos[$association][$nom] ?? 0;
    
        if ($besoin === 0) {
            $row[$nom] = ''; // Pas de rendu si besoin est 0
            continue;
        }
    
        $rapport = "$dispo / $besoin";
        $bgClass = ($dispo >= $besoin) ? 'text-bg-success' : 'text-bg-danger';
    
        $row[$nom] = '<div class="rounded px-2 py-1 text-center small ' . $bgClass . '">' . $rapport . '</div>';
    }
    

    $row["Total"] = '<div class="text-center">' . ($horaires["Total"] ?? 0) . '</div>';

    $id_assos = $mapNomToId[$association] ?? 0;
    $row["Action"] = '<button class="btn btn-sm btn-primary" onclick="getContent(501, {association_id : ' . $id_assos . '})">View</button>';

    $table->addRow($row);
}

// ───── Affichage ─────
$layout = new PageLayout();
$layout->addElement($table->render());
echo $layout->render();
?>
