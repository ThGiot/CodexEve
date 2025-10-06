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

    'connect_send_byexcell' => 'connect_send_byexcell.php',
    'connect_sms_send' => 'connect_send_sms.php',
    'connect_sms_maj' => 'connect_sms_maj.php',
    'connect_sms_delete' => 'connect_sms_delete.php',


];

$action = $data['action'];
if (!isset($allowedActions[$action])) {
    exit('Action non valide in CONNECT');
}

require $allowedActions[$action];

?>