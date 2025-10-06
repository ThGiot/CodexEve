<?php
require_once PRIVATE_PATH . '/classes/Table.php'; 
require_once PRIVATE_PATH . '/classes/PageLayout.php'; 
require_once PRIVATE_PATH . '/classes/createModalContent.php'; 
require_once PRIVATE_PATH . '/classes/Modal.php'; 
require_once PRIVATE_PATH . '/classes/Form.php'; 
require_once dirname(__DIR__, 1) . '/classes/PosteService.php';

$posteService = new PosteService($dbh);

// Création du modal de suppression
$modal = new Modal(
    id: "zoneDell", 
    title: "Suppression d'une zone", 
    body: '<b>Vous êtes sur le point de supprimer un zone.</b></br></br>Toutes les informations liées à cette zone seront supprimées !',
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
    title: "Liste des zones",
    columns: ["Nom"], 
    id: "zoneListe"
);

try {
    // Récupération des zones via PosteService
    $clientId = $_SESSION['client_actif'];
    $zones = $posteService->getZones($clientId);
    foreach ($zones as $zone) {
        $table->addRow(
            [ 
             
              "Nom" => $zone['nom']
            ],
            [
                ["name" => "Edit", "link" => "node('grid_zone_edit', {zoneId : '".$zone['id']."',zoneNom : '".$zone['nom']."'})", "class" => ""],
                ["name" => "Remove", "link" => "node('grid_zone_dell', {zoneId : '".$zone['id']."'})", "class" => "danger"],
            ],
            [
                "Nom" => [
                        'id' => 'zone_' . $zone['id'],
                        'onclick' => "node('grid_zone_edit', {zoneId : '".$zone['id']."',zoneNom : '".$zone['nom']."'});"
                    ]
            ]
        );
    }
} catch (PDOException $e) {
    echo "Erreur lors de la récupération des zones : " . $e->getMessage();
    exit;
}
$onsubmit = 'event.preventDefault(); node(\'grid_AddZone\', {formId : \'addZone\'})';
// Affichage du tableau
$layout = new PageLayout();
$form = new Form(   id: 'addZone', 
                    name: 'addZone',
                    method: 'POST', 
                    action: $onsubmit, 
                    title: 'Ajouter une zone'
                );


$form->addField(    type: 'text', 
                    id: 'nomZone',
                    name: 'nomZone', 
                    label: 'Nom',
                    placeholder: 'Nom de la zone'
                );
$form->setSubmitButton(id: 'buttonSubmit',name: 'submit',value: 'send',text: 'Enregistrer');
$layout = new PageLayout();
$layout->addElement($table->render()); 
$layout->addElement($form->render(),4); 
echo $layout->render();
?>
