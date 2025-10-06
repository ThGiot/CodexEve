<?php
require_once dirname(__DIR__, 3) . '/classes/ResponseHandler.php';
require_once dirname(__DIR__, 3) . '/classes/RequestHandler.php';

$responseHandler = new ResponseHandler();
$requestHandler = new RequestHandler();

// Définir les règles de validation
$rules = [
    'module_id' => ['type' => 'int'],
    'user_id' => ['type' => 'int']
];

// Gérer la requête avec authentification, assainissement, et validation
$cleanData = $requestHandler->handleRequest($data, $rules); 

try {
    // Définir la requête SQL
    $sql = "SELECT * FROM role";
    $stmt = $dbh->prepare($sql);
    $stmt->execute();
    $roles = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch (PDOException $e) {
    echo $responseHandler->sendResponse(false, "Erreur lors de la récupération des informations des utilisateurs du client : " . $e->getMessage());
    exit();
}

try {
    // Définir la requête SQL
    $sql = "SELECT role_id FROM module_permission_role WHERE 
    module_id = :module_id AND user_id = :user_id AND client_id = :client_id";
    $stmt = $dbh->prepare($sql);
    $stmt->bindParam(':client_id', $data['client_id']);
    $stmt->bindParam(':module_id', $data['module_id']);
    $stmt->bindParam(':user_id', $data['user_id']);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $role_id = $result['role_id'] ?? 0;
    
} catch (PDOException $e) {
    echo $responseHandler->sendResponse(false, "Erreur lors de la récupération du role_id : " . $e->getMessage());
    exit();
}

$options = '';
foreach ($roles as $role) {
    $selected = $role_id == $role['id'] ? ' selected="selected"' : '';
    $options .= '<option value="' . $role['id'] . '"' . $selected . '>' . $role['nom'] . '</option>';
}


$form = '
<div class="d-flex align-items-center">
<select id="selectRole_'.$data['user_id'].'" class="form-select form-select-sm" aria-label=".form-select-sm example">
<option value="0">Aucun role</option>' . $options . '</select>
<button class="btn btn-sm btn-phoenix-success me-1 fs--2" onclick="node(\'moduleChangeRole\',{clientId : \''.$data['client_id'].'\', moduleId : \''.$data['module_id'].'\', userId : \''.$data['user_id'].'\'})"><span class="fas fa-check"></span></button>
<button class="btn btn-sm btn-phoenix-danger me-1 fs--2"  onclick="node(\'moduleChangeRole\',{clientId : \''.$data['client_id'].'\', moduleId : \''.$data['module_id'].'\', userId : \''.$data['user_id'].'\',cancel : true})"><span class="fa fa-times"></span></button>
</div>';

echo $responseHandler->sendResponse(true, $form);
?>
