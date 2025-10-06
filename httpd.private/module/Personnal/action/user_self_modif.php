<?php
require_once dirname(__DIR__, 3) . '/classes/ResponseHandler.php';
require_once dirname(__DIR__, 3) . '/classes/RequestHandler.php';
require_once dirname(__DIR__, 3) . '/classes/DBHandler.php';
$responseHandler = new ResponseHandler();
$requestHandler = new RequestHandler();

// Définir les règles de validation
$rules = [
    'telephone' => ['type' => 'string', 'max_length'=> 30],
    'email' => ['type' => 'string', 'max_length'=> 30]
];

// Gérer la requête avec authentification, assainissement, et validation
$data= $requestHandler->handleRequest($data, $rules); 
try {
    $dbHandler = new DBHandler($dbh);
    $table = 'user';
    $key = ['id' => $_SESSION['user']['id']];
    $data = ['email' => $data['email'],'telephone' => $data['telephone']];
    $dbHandler->update($table, $key, $data);
    echo $responseHandler->sendResponse(true, 'Information enregistree');
    $_SESSION['user']['email'] = $data['email'];
    $_SESSION['user']['telephone'] = $data['telephone'];

}
catch(Exception $e){
    echo $responseHandler->sendResponse(false, 'erreur : '.$e);
}


?>