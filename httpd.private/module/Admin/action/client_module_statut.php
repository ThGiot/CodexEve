<?php

// ----------------------------------------------------------------
// Action : Ajout ou suppression d'un user à un client
// ----------------------------------------------------------------
// Class reponsHandler
require_once dirname(__DIR__, 3) . '/classes/ResponseHandler.php';
$responseHandler = new ResponseHandler();
////////////////////////////////
// Mesure de sécurité
////////////////////////////////

// Controle session user
if(!isset($_SESSION['user'])){
    echo $responseHandler->sendResponse(false, 'aucune session user obtenue AUTH FAILED in action handler.php');
    exit();
}
// Controle variable data présentes et utilisées
if (isset($data['client_id']) && filter_var($data['client_id'], FILTER_VALIDATE_INT) !== false) {
    $client_id = (int)$data['client_id'];
} else {
    echo $responseHandler->sendResponse(false, 'Client_id absent ou non conforme IN client_module_statut');
    exit();
}
if (isset($data['module_id']) && filter_var($data['module_id'], FILTER_VALIDATE_INT) !== false) {
    $module_id = (int)$data['module_id'];
} else {
    echo $responseHandler->sendResponse(false, 'User_id absent ou non conforme IN client_module_statut');
    exit();
}

if (isset($data['statut']) && is_string($data['statut']) && strlen($data['statut']) <= 10) {
    $statut = $data['statut'];
} else {
    echo $responseHandler->sendResponse(false, 'Statut non conforme ou trop long IN client_module_statut');
    exit();
}

////////////////////////////////
//
// Autorisation 
// Seule les superAdmin peuvent ajouter ou enlever des modules à des clients
//
////////////////////////////////

$query = "  SELECT role_id FROM module_permission_role
WHERE user_id = :user_id AND
module_id = :module_id AND
client_id = :client_id";
$stmt = $dbh->prepare($query);
// Exécuter la requête en liant les paramètres
$stmt->bindParam(':user_id', $_SESSION['user']['id']);
$stmt->bindParam(':module_id', $_SESSION['module_actif']);
$stmt->bindParam(':client_id', $_SESSION['client_actif']);
$stmt->execute();
$role= $stmt->fetch(PDO::FETCH_ASSOC);


if(empty($role) OR $role['role_id'] != 1){
    echo $responseHandler->sendResponse(false, 'Seul un gestionnaire Eve peux gerer les accès aux modules');
    exit();
}

////////////////////////////////
// Tout est ok on peut effectuer la demande
////////////////////////////////

if($statut == 'add') {
    require_once dirname(__DIR__, 3) . '/classes/DBHandler.php';
    $dbHandler = new DBHandler($dbh);
    $table = 'module_permission_client';
    $data = [
        "module_id" => $module_id,
        "client_id" => $client_id
    ];
    $dbHandler->insert($table, $data);
    echo $responseHandler->sendResponse(true, '');
}
if ($statut == 'remove') {
    require_once dirname(__DIR__, 3) . '/classes/DBHandler.php';
    $dbHandler = new DBHandler($dbh);
    $table = 'module_permission_client';
    $key = ['module_id' => $module_id, 'client_id' => $client_id];
    $dbHandler->delete($table,$key);
    echo $responseHandler->sendResponse(true, '');
}
?>