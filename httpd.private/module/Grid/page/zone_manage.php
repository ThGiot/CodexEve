<?php
require_once PRIVATE_PATH . '/vendor/autoload.php';
require_once PRIVATE_PATH . '/classes/Table.php'; 
require_once PRIVATE_PATH . '/classes/PageLayout.php'; 
require_once PRIVATE_PATH . '/classes/createModalContent.php'; 
require_once PRIVATE_PATH . '/classes/Modal.php';
use Grid\PosteService;

$posteService = new PosteService($dbh);

// Création du modal de suppression
$modal = new Modal(
    id: "posteDell", 
    title: "Suppression d'une zone", 
    body: '<b>Vous êtes sur le point de supprimer une zone.</b></br></br>Toutes les informations liées à cette zone seront supprimées !',
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
    columns: ["Numero", "Zone"], 
    id: "zoneListe"
);

try {
    // Récupération des postes via PosteService
    $clientId = $_SESSION['client_actif'];
    $zones = $posteService->getZones($clientId);

    foreach ($zones as $zone) {
        $table->addRow(
            [ 
              "Numero" => $zone['id'], 
              "Zone" => $zone['nom']
            ],
            [
                ["name" => "View", "link" => "", "class" => ""],
                ["name" => "Remove", "link" => "node('grid_poste_dell', {posteId : ''})", "class" => "danger"],
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
