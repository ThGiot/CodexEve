<?php

require_once dirname(__DIR__, 3) . '/vendor/autoload.php';
require_once PRIVATE_PATH . '/classes/Table.php'; 
require_once PRIVATE_PATH . '/classes/PageLayout.php'; 
require_once PRIVATE_PATH . '/classes/createModalContent.php'; 
require_once PRIVATE_PATH . '/classes/Modal.php'; 
require_once PRIVATE_PATH . '/classes/Form.php'; 

use Grid\MaterialService;

$allMaterials = MaterialService::getAllMaterials($_SESSION['client_actif']);

// Création du modal de suppression
$modal = new Modal(
    id: "deviceDell", 
    title: "Suppression d'un item", 
    body: '<b>Vous êtes sur le point de supprimer un item.</b></br></br>Toutes les informations liées à cet item seront supprimées !',
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
    title: "Liste des items",
    columns: ["Nom"], 
    id: "itemListe"
);

try {
    // Récupération des zones via PosteService

    foreach ($allMaterials as $material) {
        $table->addRow(
            [ 
             
              "Nom" => $material['nom']
            ],
            [
                ["name" => "Edit/View", "link" => "node('grid_material_edit', {materialId : '".$material['id']."',materialNom : '".$material['nom']."'})", "class" => ""],
                ["name" => "Remove", "link" => "smartAction({
                                                                action: 'material_dell',
                                                                modalId: 'deviceDell',
                                                                confirmButtonId: 'deviceDellOkayButton',
                                                                extra: { material_id: ".$material['id']." },
                                                                onSuccess: () => refreshContent()
                                                            })", "class" => "danger"],
            ],
        );
    }
} catch (PDOException $e) {
    echo "Erreur lors de la récupération des materials : " . $e->getMessage();
    exit;
}
$onsubmit = "event.preventDefault();smartAction({
    action: 'material_add',
    formId: 'addmaterial',
    onSuccess: () => refreshContent()
  })";
// Affichage du tableau
$layout = new PageLayout();
$form = new Form(   id: 'addmaterial', 
                    name: 'addmaterial',
                    method: 'POST', 
                    action: $onsubmit, 
                    title: 'Ajouter une material'
                );


$form->addField(    type: 'text', 
                    id: 'nomMaterial',
                    name: 'nomMaterial', 
                    label: 'Nom',
                    placeholder: 'Nom'
                );
$form->setSubmitButton(id: 'buttonSubmit',name: 'submit',value: 'send',text: 'Enregistrer');
$layout = new PageLayout();
$layout->addElement($table->render()); 
$layout->addElement($form->render(),4); 
echo $layout->render();
?>
