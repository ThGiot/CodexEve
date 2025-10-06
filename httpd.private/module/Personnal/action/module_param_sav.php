<?php
require_once dirname(__DIR__, 3) . '/classes/ResponseHandler.php';
require_once dirname(__DIR__, 3) . '/classes/RequestHandler.php';

$responseHandler = new ResponseHandler();
$requestHandler = new RequestHandler();

// Définir les règles de validation
$rules = [
    'module_id' => ['type' => 'int'],
    'client_id' => ['type' => 'int'],
    'param_id' => ['type' => 'int'],
    'param_value' => ['type' => 'string', 'max_length' => 500]
];

// Gérer la requête avec authentification, assainissement, et validation
$cleanData = $requestHandler->handleRequest($data, $rules); 

//---------------------------------------
//Authorisation
//---------------------------------------
//Si pas SUPER ADMIN on stop
$query = "SELECT * FROM module_permission_role WHERE 
                        module_id = 0 AND
                        role_id = 1 AND
                        user_id = :user_id AND
                        client_id = 0";
$stmt = $dbh->prepare($query);
$stmt->bindParam(':user_id', $_SESSION['user']['id']);
$stmt->execute();

// Check if any rows are returned, which would indicate permission
if($stmt->rowCount() == 0) {
    exit($responseHandler->sendResponse(false, "Autorisation insufisante"));
}
$query = "UPDATE module_client_param_values SET param_value = :param_value WHERE 
                        module_id = :module_id AND
                        role_id = :client_id AND
                        id = :param_id ";
$stmt = $dbh->prepare($query);
$stmt->bindParam(':module_id', $data['module_id']);
$stmt->bindParam(':client_id', $data['client_id']);
$stmt->bindParam(':param_id', $data['param_id']);
$stmt->bindParam(':param_value', $data['param_value']);
$stmt->execute();
exit($responseHandler->sendResponse(true, "Mise à jour effectué"));

?>