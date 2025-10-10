<?php
declare(strict_types=1);

// Chargement des styles et des dépendances
require_once PRIVATE_PATH . '/vendor/autoload.php';
require_once dirname(__DIR__, 1) . '/styles/la_grille.html';
require_once dirname(__DIR__, 3) . '/classes/Modal.php';
require_once dirname(__DIR__, 3) . '/classes/Form.php';
use Grid\PosteService;
use Grid\FilterService;
use Grid\SchedulePage;


$clientId = (int) $_SESSION['client_actif'];

// On suppose que $dbh est une instance de PDO correctement configurée
$filterService = new FilterService($dbh);
$schedulePage  = new SchedulePage($filterService, $clientId);
// Récupérer les options pour les trois listes
// Récupérer toutes les associations
$posteService = new PosteService($dbh);
$optionsAssociations = $posteService->getAssociationsOptions($clientId);
$optionsTypes = $posteService->getPosteTypesOptions($clientId);
$optionsZones = $posteService->getZonesOptions($clientId);
$optionsHoraires = $posteService->getHorairesOptions($clientId);

// Affichage de la page
$schedulePage->render();

$form = new Form(   id: 'addPrestation', 
                    name: 'addPrestation',
                    method: 'POST', 
                    action: '', 
                    title: 'Enregistrer des heures suplémentaires'
                );

$form->addField(
    type: 'number', 
    id: 'posteNum',
    name: 'poste_num', 
    label: 'Poste N°',
    placeholder:'1',
);
$form->addField(
    type: 'hidden', 
    id: 'posteId',
    name: 'posteId', 
    label: '',
    placeholder:'posteId',
);

$form->addField(
    type: 'text', 
    id: 'posteNom',
    name: 'poste_nom', 
    label: 'Poste',
    placeholder:'Poste'
);

$form->addField(
    type: 'select', 
    id: 'posteZone',
    name: 'poste_zone', 
    label: 'Zone',
    options: $optionsZones // Utilisation des options correctement formatées
);

$form->addField(
    type: 'select', 
    id: 'posteAssociation',
    name: 'poste_association', 
    label: 'Association',
    options: $optionsAssociations // Utilisation des options correctement formatées
);

$form->addField(
    type: 'select', 
    id: 'posteType',
    name: 'poste_type', 
    label: 'Type',
    options: $optionsTypes // Utilisation des options correctement formatées
);

$form->addField(
    type: 'select', 
    id: 'posteHoraire',
    name: 'horaire_id', 
    label: 'Horaire',
    options: $optionsHoraires // Utilisation des options correctement formatées
);



$body = $form->renderMinimal();
//Création du Modal

$modal = new Modal(
    id: "modalDetailPoste", 
    title: "Détail du poste", 
    body: $body,
    headerClass: "",
    okayButtonClass: "success",
    okayButtonText : "Enregistrer",
    cancelButtonClass: "outline-secondary",
    showOkayButton: true,
    showButton : false
);
$onsubmit = 'event.preventDefault(); node(\'grid_majposte\', {})';
$modal -> setOkayButtonOnClick($onsubmit);
echo $modal->render();

