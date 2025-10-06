<?php
// ----------------------------------------------------------------
// Gestion des actions via node.php 
// ----------------------------------------------------------------

/*
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
    //Module Connect 
    'connect_send_byexcell' => 'connect_send_byexcell.php',
    'connect_send_byexcell_status' => 'connect_send_byexcell_status.php',
    'connect_sms_send' => 'connect_send_sms.php',
    'connect_sms_maj' => 'connect_sms_maj.php',
    'connect_sms_delete' => 'connect_sms_delete.php',
    // user Self Modif
    'user_self_modif' => 'user_self_modif.php',
    'user_self_modif_pass' => 'user_self_modif_pass.php',
    'image_change' => 'image_change.php',
    'user_self_modif_avatar' => 'user_self_modif_avatar.php',
    'module_param_save' => 'module_param_save.php'

];

$action = $data['action'];
if (!isset($allowedActions[$action])) {
    exit('Action non valide in based');
}

require $allowedActions[$action];
*/
?>