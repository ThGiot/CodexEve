<?php
require_once PRIVATE_PATH . '/classes/Table.php'; 
require_once PRIVATE_PATH . '/classes/PageLayout.php'; 
require_once PRIVATE_PATH . '/classes/createModalContent.php'; 
require_once PRIVATE_PATH . '/classes/Modal.php'; 
require_once PRIVATE_PATH . '/classes/Form.php'; 
require_once dirname(__DIR__, 1) . '/classes/HoraireService.php';
require_once dirname(__DIR__, 1) . '/classes/HoraireTable.php';

// Création du modal de suppression
$modal = new Modal(
    id: "horaireDell", 
    title: "Suppression d'un poste", 
    body: '<b>Vous êtes sur le point de supprimer un horaire.</b></br></br>Toutes les informations liées à cet horaire seront supprimées !</br>Des postes pourront se retrouver sans horaires et ne seront plus affichés dans la grilles.',
    headerClass: "danger",
    okayButtonClass: "primary",
    okayButtonText : "Supprimer",
    cancelButtonClass: "outline-secondary",
    showOkayButton: true,
    showButton : false
);
echo $modal->render();

$horaireService = new HoraireService($dbh);

// Récupérer les horaires avec leurs périodes
$horaires = $horaireService->getHoraires($_SESSION['client_actif']);

// Générer le tableau des horaires
$horaireTable = new HoraireTable($horaires);

// Affichage dans la page
$onsubmit = 'event.preventDefault(); node(\'grid_AddHoraire\', {formId : \'addHoraire\'})';
// Affichage du tableau
$layout = new PageLayout();
$form = new Form(   id: 'addHoraire', 
                    name: 'addHoraire',
                    method: 'POST', 
                    action: $onsubmit, 
                    title: 'Ajouter un Horaire'
                );


$form->addField(    type: 'text', 
                    id: 'nomHoraire',
                    name: 'nomHoraire', 
                    label: 'Nom',
                    placeholder: 'Nom de la zone'
                );
$form->setSubmitButton(id: 'buttonSubmit',name: 'submit',value: 'send',text: 'Enregistrer');
$layout = new PageLayout();
$layout->addElement($horaireTable->render()); 
$layout->addElement($form->render(),4); 
echo $layout->render();
?>
