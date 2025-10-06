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

    'prestataire_maj_save' => 'prestataire_maj_save.php',
    'prestataire_add' => 'prestataire_add.php',
    'analytique_add' => 'analytique_add.php',
    'activite_add' => 'activite_add.php',
    'remuneration_add' => 'remuneration_add.php',
    'activite_maj' => 'activite_maj.php',
    'remuneration_maj' => 'remuneration_maj.php',
    'remuneration_dell' => 'remuneration_dell.php',
    'activite_dell' => 'activite_dell.php',
    'import_ebrigade' => 'import_ebrigade.php',
    'facture_maj' => 'facture_maj.php',
    'facture_detail_dell' => 'facture_detail_dell.php',
    'facture_detail_add' => 'facture_detail_add.php',
    'facture_detail_maj'=> 'facture_detail_maj.php',
    'facture_attente_attribuer' => 'facture_attente_attribuer.php',
    'analytique_maj' => 'analytique_maj.php',
    'analytique_dell' => 'analytique_dell.php',
    'facture_correction_add'=> 'facture_correction_add.php',
    'facture_correction_dell' => 'facture_correction_dell.php',
    'facture_add' => 'facture_add.php',
    'prestation_add' => 'prestation_add.php',
    'prestation_change_statut' => 'prestation_change_statut.php',
    'prestation_dell' => 'prestation_dell.php',
    'facture_create_pdf' => 'facture_create_pdf.php',
    'get_pdf_generate' => 'get_pdf_generate.php',
    'facture_get_single'=>'facture_get.php',
    'facture_send' => 'facture_send.php',
    'moka_recover' => 'moka_recover.php',
    'hs_maj'=>'hs_maj.php',


];

$action = $data['action'];
if (!isset($allowedActions[$action])) {
    exit('Action non valide in MOKA');
}

require $allowedActions[$action];

?>