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
// Controle variable data présente 
if (isset($data['client_id']) && filter_var($data['client_id'], FILTER_VALIDATE_INT) !== false) {
    $client_id = (int)$data['client_id'];
} else {
    echo $responseHandler->sendResponse(false, 'Client_id absent ou non conforme IN client_user_statut');
    exit();
}
if (isset($data['user_id']) && filter_var($data['user_id'], FILTER_VALIDATE_INT) !== false) {
    $userToChange_id = (int)$data['user_id'];
} else {
    echo $responseHandler->sendResponse(false, 'User_id absent ou non conforme IN client_user_statut');
    exit();
}

if (isset($data['statut']) && is_string($data['statut']) && strlen($data['statut']) <= 10) {
    $statut = $data['statut'];
} else {
    echo $responseHandler->sendResponse(false, 'Statut non conforme ou trop long IN client_user_statut');
    exit();
}

////////////////////////////////
//
// Autorisation 
// Seule les superAdmin OU les propriétaire pour ce client peuvent effectuer cet action
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

    try {
        // Définir la requête SQL
        $sql = "
        SELECT id
        FROM client_admin ca
        WHERE client_id= :client_id AND user_id =:user_id
    ";
    
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(':client_id', $client_id);
        $stmt->bindParam(':user_id', $_SESSION['user']['id']);
        $stmt->execute();
        $resultat= $stmt->fetchAll(PDO::FETCH_ASSOC);
        if (empty($resultat)) {
            // Aucun utilisateur trouvé avec ce login
            echo $responseHandler->sendResponse(false, 'l\'utilisateur n\'est pas administrateur de ce client');
            exit();
        }
    } catch (PDOException $e) {
    echo $responseHandler->sendResponse(false, "Erreur lors de la récupération des informations des utilisateurs du client : " . $e->getMessage());
    exit();
    }


    
}



////////////////////////////////
// Tout est ok on peut effectuer la demande
////////////////////////////////

if($statut == 'add') {
    require_once dirname(__DIR__, 3) . '/classes/DBHandler.php';
    $dbHandler = new DBHandler($dbh);
    $table = 'user_client';
    $data = [
        "user_id" => $userToChange_id,
        "client_id" => $client_id
    ];
    $dbHandler->insert($table, $data);
    echo $responseHandler->sendResponse(true, 'user added successfully');
}
if ($statut == 'remove') {
    require_once dirname(__DIR__, 3) . '/classes/DBHandler.php';
    $dbHandler = new DBHandler($dbh);
    $table = 'user_client';
    $key = ['user_id' => $userToChange_id, 'client_id' => $client_id];
    $dbHandler->delete($table,$key);
    echo $responseHandler->sendResponse(true, 'user deleted successfully');
}


?>