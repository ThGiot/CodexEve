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
    'client_user_statut' => 'client_user_statut.php',
    'client_module_statut' => 'client_module_statut.php',
    'module_manage_get_select' => 'module_manage_get_select.php',
    'module_update_user_role' => 'module_update_user_role.php',
    'image_change' =>'image_change.php',
    'client_add' =>'client_add.php'

];

$action = $data['action'];
if (!isset($allowedActions[$action])) {
    exit('Action non valide in ADMIN '.$action);
}

require __DIR__.'/'.$allowedActions[$action];

?>