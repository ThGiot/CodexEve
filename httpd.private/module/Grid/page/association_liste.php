<?php
require_once PRIVATE_PATH . '/classes/Table.php'; 
require_once PRIVATE_PATH . '/classes/PageLayout.php'; 
require_once PRIVATE_PATH . '/classes/createModalContent.php'; 
require_once PRIVATE_PATH . '/classes/Modal.php'; 
require_once PRIVATE_PATH . '/classes/Form.php'; 
require_once dirname(__DIR__, 3) . '/classes/RequestHandler.php';

use Grid\PosteService;
use Grid\HoraireService;



$posteService = new PosteService($dbh);

// Création du modal de suppression
$modal = new Modal(
    id: "associationDell", 
    title: "Suppression d'une association", 
    body: '<b>Vous êtes sur le point de supprimer une association.</b></br></br>Toutes les informations liées à cette association seront supprimées !',
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
    title: "Liste des associations",
    columns: ["Nom","Contact","Email","Téléphone"],
    id: "associationListe"
);

try {
    // Récupération des associations via PosteService
    $clientId = $_SESSION['client_actif'];
    $associations = $posteService->getAssociations($clientId);
    foreach ($associations as $association) {
        $table->addRow(
            [ 
             
              "Nom" => $association['nom'],
              "Contact" => $association['contact_nom'],
              "Email" => $association['contact_email'],
              "Téléphone" => $association['contact_telephone']
            ],
            [
                ["name" => "Edit", "link" => "getContent(501, {association_id : '".$association['id']."'})", "class" => ""],
                ["name" => "Devis", "link" => "getContent(502, {association_id : '".$association['id']."'})", "class" => ""],
                ["name" => "Remove", "link" => "node('grid_association_dell', {associationId : '".$association['id']."'})", "class" => "danger"],
            ],
            [
                "Nom" => [
                        'id' => 'association_' . $association['id'],
                        'onclick' => "node('grid_association_edit', {associationId : '".$association['id']."',associationNom : '".$association['nom']."'});"
                    ]
            ]
        );
    }
} catch (PDOException $e) {
    echo "Erreur lors de la récupération des associations : " . $e->getMessage();
    exit;
}
$onsubmit = 'event.preventDefault(); node(\'grid_add_assos\', {formId : \'addZone\'})';
// Affichage du tableau
$layout = new PageLayout();
$form = new Form(   id: 'addZone', 
                    name: 'addZone',
                    method: 'POST', 
                    action: $onsubmit, 
                    title: 'Ajouter une association'
                );


$form->addField(    type: 'text', 
                    id: 'nomZone',
                    name: 'nomZone', 
                    label: 'Nom',
                    placeholder: 'Nom de la association'
                );
$form->setSubmitButton(id: 'buttonSubmit',name: 'submit',value: 'send',text: 'Enregistrer');
$layout = new PageLayout();
$layout->addElement($table->render()); 
$layout->addElement($form->render(),4); 
echo $layout->render();
?>
