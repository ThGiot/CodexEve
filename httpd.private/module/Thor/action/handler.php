<?php
// ----------------------------------------------------------------
// Gestion des actions via node.php 
// ----------------------------------------------------------------


if (!class_exists('ClearPost')) {
    exit('Erreur : La classe ClearPost n\'est pas définie.');
}

$_POST = ClearPost::clearPost($_POST); // Nettoyer l'ensemble des variables $_POST
$data = ClearPost::clearPost($data); // Nettoyer l'ensemble des variables $_POST
if(!isset($_SESSION['user'])){   
    exit('aucune session user obtenue AUTH FAILED in action handler.php'); 
}

$allowedActions = [
    'evenement_add' => 'evenement_add.php',
    'module_add' => 'module_add.php',
    'module_update' => 'module_update.php',
    'module_update_groupe' => 'module_update_groupe.php',
    'event_update' => 'event_update.php',
    'event_import_dispo'=>'event_import_dispo.php',
    'get_volontaire_dispo' => 'get_volontaire_dispo.php',
    'volontaire_dispo_add' => 'volontaire_dispo_add.php',
    'volontaire_dispo_dell' => 'volontaire_dispo_dell.php',
    'module_dell' => 'module_dell.php',
    'volontaire_add_man' => 'volontaire_add_man.php',
    'module_copy' => 'module_copy.php',

];

$action = $data['action'];
if (!isset($allowedActions[$action])) {
    exit('Action non valide in ADMIN '.$action);
}

require __DIR__.'/'.$allowedActions[$action];

?>