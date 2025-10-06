<?php
require_once PRIVATE_PATH . '/classes/PageLayout.php'; 
require_once PRIVATE_PATH . '/classes/RequestHandler.php';
require_once dirname(__DIR__, 1) . '/classes/PosteService.php';
require_once dirname(__DIR__, 1) . '/classes/PosteForm.php';


$requestHandler = new RequestHandler();
$posteService = new PosteService($dbh);

// Définir les règles de validation
$rules = ['poste_id' => ['type' => 'int']];
$data = $requestHandler->handleRequest($data, $rules);

// Récupération des informations du poste
$clientId = $_SESSION['client_actif'];
$posteId = $data['poste_id'] ?? 0;
$poste = $posteService->getPoste($clientId, $posteId);

if (!$poste) {
    echo "Aucun poste trouvé.";
    exit;
}

$options = $posteService->getAllOptions($_SESSION['client_actif']);

// Définition de l'action du formulaire pour la modification
$action = "node('grid_poste_update', {formId : 'posteForm', posteId : '".$data['poste_id']."'}); return false;";

// Création du formulaire avec `PosteForm`
$posteForm = new PosteForm(
    poste: $poste,
    id : 'posteForm',
    optionsAssociations: $options['associations'],
    optionsTypes: $options['types'],
    optionsZones: $options['zones'],
    optionsHoraires: $options['horaires'],
    action: $action
);

// Affichage du formulaire avec `PageLayout`
$layout = new PageLayout();
$layout->addElement($posteForm->render());
echo $layout->render();
?>
