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
    exit('aucune session user obtenue AUTH FAILED in GRID action  handler.php'); 
}

$allowedActions = [
    'schedule_data' => 'schedule_data.php',
    'grid_get_poste' => 'grid_get_poste.php',
    'poste_maj' => 'poste_maj.php',
    'poste_add' => 'poste_add.php',
    'poste_dell' => 'poste_dell.php',
    'horaire_dell' => 'horaire_dell.php',
    'horaire_periode_dell' => 'horaire_periode_dell.php',
    'poste_update' => 'poste_update.php',
    'periode_add' => 'periode_add.php',
    'periode_get_data' => 'periode_get_data.php',
    'periode_update' => 'periode_update.php',
    'zone_dell' => 'zone_dell.php',
    'zone_edit' => 'zone_edit.php',
    'zone_add' => 'zone_add.php',
    'assos_add' => 'assos_add.php',
    'horaire_add' => 'horaire_add.php',
    'assos_dell' => 'assos_dell.php',
    'association_maj_save' => 'association_maj_save.php',
];

$action = $data['action'];
if (!isset($allowedActions[$action])) {
    exit('Action non valide in ADMIN '.$action);
}

require __DIR__.'/'.$allowedActions[$action];

?>