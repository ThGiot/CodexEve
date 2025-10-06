<?php
require_once dirname(__DIR__, 3) . '/config.php';
require_once dirname(__DIR__, 3) . '/sql.php';
require_once dirname(__DIR__, 2) . '/role.php';
require_once dirname(__DIR__, 3) . '/classes/ResponseHandler.php';
require_once dirname(__DIR__, 3) . '/classes/RequestHandler.php';
require_once dirname(__DIR__, 3) . '/classes/DBHandler.php';

// Gérer la requête avec authentification, assainissement, et validation
$responseHandler = new ResponseHandler();
$requestHandler = new RequestHandler();
if($role >= 3){
    exit('Niveau d\'accès insufisant'.$role);
}
$rules = [
    'fichier' => ['type' => 'string', 'max_length' => 40]
];
$data = $requestHandler->handleRequest($data, $rules);
$parts = explode('_', $data['fichier']);
if($parts[0] != $_SESSION['client_actif']){
    exit('Niveau d\'accès insufisant : Mauvais client !');
}
$requestHandler ->verifyModulePermission(3,$dbh);
//----------------------------------------------------------------




$filePath = dirname(__DIR__, 1) . '/facture/'.$data['fichier'];

if (file_exists($filePath)) {
    // Ajouter un log pour indiquer que le fichier existe
  

    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename="' . basename($filePath) . '"');
    readfile($filePath);
    exit;
} else {
    // Ajouter un log pour indiquer que le fichier n'existe pas
  

    http_response_code(404);
    echo "Fichier non trouvé";
}

?>
