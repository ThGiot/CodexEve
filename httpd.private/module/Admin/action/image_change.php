<?php

require_once dirname(__DIR__, 3) . '/classes/ResponseHandler.php';
require_once dirname(__DIR__, 3) . '/classes/RequestHandler.php';
require_once dirname(__DIR__, 3) . '/classes/FileUploader.php';
require_once dirname(__DIR__, 3) . '/classes/DBHandler.php';
require_once dirname(__DIR__, 3) . '/config.php';

$responseHandler = new ResponseHandler();
$requestHandler = new RequestHandler();

$rules = [
    'categorie' => ['type' => 'string', 'max_length'=> 10],
    'object_id' => ['type' => 'int']
];

// Gérer la requête avec authentification, assainissement, et validation
$data= $requestHandler->handleRequest($data, $rules); 

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


// Le répertoire où les fichiers téléchargés seront enregistrés
$uploadDir = PUBLIC_PATH.'/assets/img/uploads/';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['avatar'])) {
    try {
        $uploader = new FileUploader($_FILES['avatar'], $uploadDir);
        $newFileName = $uploader->upload();
    
    } catch (Exception $e) {
        exit($responseHandler->sendResponse(false, $e->getMessage()));
    }
} else {
    exit($responseHandler->sendResponse(false, "Aucun fichier n'a été uploadé."));
}
$dbHandler = new DBHandler($dbh);


if (in_array($data['categorie'], ['client', 'module'])) {
    $table = $data['categorie'];
}else{
    exit($responseHandler->sendResponse(false, "categorie interdite"));
}


$key = ['id' => $data['object_id']];
$data = ['logo' =>'/assets/img/uploads/'.$newFileName];
$dbHandler->update($table, $key, $data);

echo $responseHandler->sendResponse(true, 'Information enregistree');

?>
