<?php
// Chargement des styles et des dépendances
require_once dirname(__DIR__, 1) . '/styles/la_grille.html';
require_once dirname(__DIR__, 3) . '/classes/Modal.php';
require_once dirname(__DIR__, 1) . '/classes/FilterService.php';
require_once dirname(__DIR__, 3) . '/classes/PageLayout.php';
require_once PRIVATE_PATH . '/vendor/autoload.php';
use Grid\PosteService;
use Grid\PosteForm;
// Instanciation de PosteService
$posteService = new PosteService($dbh);

// Récupération des options via PosteService
$optionsAssociations = $posteService->getAssociationsOptions($_SESSION['client_actif']);
$optionsTypes = $posteService->getPosteTypesOptions($_SESSION['client_actif']);
$optionsZones = $posteService->getZonesOptions($_SESSION['client_actif']);
$optionsHoraires = $posteService->getHorairesOptions($_SESSION['client_actif']);

// Définition de l'action du formulaire pour l'ajout
$action = "node('grid_poste_add', {formId : 'posteForm'}); return false;";

// Création du formulaire via `PosteForm`
$posteForm = new PosteForm(
    action: $action,
    id : 'posteForm',
    optionsAssociations: $optionsAssociations,
    optionsTypes: $optionsTypes,
    optionsZones: $optionsZones,
    optionsHoraires: $optionsHoraires
);

// Affichage du formulaire avec `PageLayout`
$layout = new PageLayout();
$layout->addElement($posteForm->render());
echo $layout->render();
?>
