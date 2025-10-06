<?php
require_once dirname(__DIR__, 3) . '/classes/ResponseHandler.php';
require_once dirname(__DIR__, 3) . '/classes/RequestHandler.php';

$responseHandler = new ResponseHandler();
$requestHandler = new RequestHandler();

// Définir les règles de validation
$rules = [
    'module_id' => ['type' => 'int'],
    'user_id' => ['type' => 'int'],
    'client_id' => ['type' => 'int'],
    'role_id' => ['type' => 'int'],
    'cancel' => ['type' => 'string', 'max_length' => 5]
];

// Gérer la requête avec authentification, assainissement, et validation
$cleanData = $requestHandler->handleRequest($data, $rules); 

if($data['cancel'] == 'true') {
    echo $responseHandler->sendResponse(true, 'executed successfully');
    exit();
}

//---------------------------------------
//Authorisation
//---------------------------------------

//Seul les admin/SUPER_ADMIN du module OU les admin du client peuvent faire cela 
//Un admin ne peut pas affecter un SUPER_ADMIN ou un admin client
//Un utilisateur ne peut pas mettre qqu à un role supérieur au sien

// Récupération du role de l'utilisateur
$query = "  SELECT role_id FROM module_permission_role
WHERE user_id = :user_id AND
module_id = :module_id AND
client_id = :client_id";
$stmt = $dbh->prepare($query);
// Exécuter la requête en liant les paramètres
$stmt->bindParam(':user_id', $_SESSION['user']['id']);
$stmt->bindParam(':module_id', $data['module_id']);
$stmt->bindParam(':client_id', $data['client_id']);
$stmt->execute();
$role= $stmt->fetch(PDO::FETCH_ASSOC);

//Récupération du role actuel de l'utilisateur à modifier
$query = "  SELECT role_id FROM module_permission_role
WHERE user_id = :user_id AND
module_id = :module_id AND
client_id = :client_id";
$stmt = $dbh->prepare($query);
// Exécuter la requête en liant les paramètres
$stmt->bindParam(':user_id', $data['user_id']);
$stmt->bindParam(':module_id', $data['module_id']);
$stmt->bindParam(':client_id', $data['client_id']);
$stmt->execute();
$role_to_change= $stmt->fetch(PDO::FETCH_ASSOC);
if(empty($role_to_change)){
    $role_to_change = 999;
}else{
    $role_to_change=$role_to_change['role_id'];
}
if((empty($role) OR $role['role_id'] > 2) AND $_SESSION['user']['id'] != 2){
    echo $responseHandler->sendResponse(false, 'Vous n\'avez pas un accès suffisant pour effectuer cette action');
    exit();
}
if($role['role_id'] > $role_to_change){
    echo $responseHandler->sendResponse(false, 'Vous n\'avez pas un accès suffisant pour effectuer cette action');
    exit();
}

require_once dirname(__DIR__, 3) . '/classes/DBHandler.php';
if($data['role_id'] == 0){
    if($role_to_change == 999){
        //Rien à faire, on a pas de role on a rien a supprimer
        echo $responseHandler->sendResponse(true, 'Role mis à jour 0');
        exit();
    }
    $dbHandler = new DBHandler($dbh);
    $table = 'module_permission_role';
    $key = ['user_id' => $data['user_id'], 'client_id' => $data['client_id'], 'module_id' => $data['module_id']];
    $dbHandler->delete($table,$key);
    echo $responseHandler->sendResponse(true, 'Role mis à jour 1');
    exit();
}elseif($role_to_change == 999){
    $dbHandler = new DBHandler($dbh);
    $table = 'module_permission_role';
    $data = ['user_id' => $data['user_id'], 'client_id' => $data['client_id'], 'module_id' => $data['module_id'], 'role_id'=>$data['role_id']];
    $dbHandler->insert($table, $data);
    echo $responseHandler->sendResponse(true, 'Role mis à jour 2');
    exit();
}else{
    $dbHandler = new DBHandler($dbh);
    $table = 'module_permission_role';
    $key = ['user_id' => $data['user_id'], 'client_id' => $data['client_id'], 'module_id' => $data['module_id']];
    $data = ['role_id' => $data['role_id']];
    $dbHandler->update($table, $key, $data);
    echo $responseHandler->sendResponse(true, 'Role mis à jour 3');
    exit();
}


?>