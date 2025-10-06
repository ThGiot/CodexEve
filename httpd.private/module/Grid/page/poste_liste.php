<?php
require_once PRIVATE_PATH . '/classes/Table.php'; 
require_once PRIVATE_PATH . '/classes/PageLayout.php'; 
require_once PRIVATE_PATH . '/classes/createModalContent.php'; 
require_once PRIVATE_PATH . '/classes/Modal.php'; 
require_once dirname(__DIR__, 1) . '/classes/PosteService.php';

$posteService = new PosteService($dbh);

// Création du modal de suppression
$modal = new Modal(
    id: "posteDell", 
    title: "Suppression d'un poste", 
    body: '<b>Vous êtes sur le point de supprimer un poste.</b></br></br>Toutes les informations liées à ce poste seront supprimées !',
    headerClass: "danger",
    okayButtonClass: "primary",
    okayButtonText : "Supprimer",
    cancelButtonClass: "outline-secondary",
    showOkayButton: true,
    showButton : false
);
echo $modal->render();

// Création de la table
$table = new Table(
    title: "Liste des postes",
    columns: ["Numero", "Poste", "Association", "Zone", "Type", "Horaire"], 
    id: "posteListe"
);

try {
    // Récupération des postes via PosteService
    $clientId = $_SESSION['client_actif'];
    $postes = $posteService->getPostes($clientId);

    foreach ($postes as $poste) {
        $table->addRow(
            [ 
              "Numero" => strtoupper(substr($poste['poste_type'], 0, 1)) . $poste['numero'], 
              "Poste" => $poste['nom'],
              "Association" => $poste['association'],
              "Zone" => $poste['zone_nom'],
              "Type" => $poste['poste_type'],
              "Horaire" => $poste['horaire']
            ],
            [
                ["name" => "View", "link" => "getContent(201, {poste_id : '".$poste['poste_id']."'})", "class" => ""],
                ["name" => "Remove", "link" => "node('grid_poste_dell', {posteId : '".$poste['poste_id']."'})", "class" => "danger"],
            ]
        );
    }
} catch (PDOException $e) {
    echo "Erreur lors de la récupération des postes : " . $e->getMessage();
    exit;
}

// Affichage du tableau
$layout = new PageLayout();
$layout->addElement($table->render()); 
echo $layout->render();
?>
