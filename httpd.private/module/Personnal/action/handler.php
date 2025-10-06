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
    'user_self_modif' => 'user_self_modif.php',
    'user_self_modif_pass' => 'user_self_modif_pass.php',
    'image_change' => 'image_change.php',
    'user_self_modif_avatar' => 'user_self_modif_avatar.php',
    'module_param_save' => 'module_param_save.php'

];

$action = $data['action'];
if (!isset($allowedActions[$action])) {
    exit('Action non valide in PERSONNAL');
}

require $allowedActions[$action];

?>