<?php
require_once dirname(__DIR__, 3) . '/classes/ResponseHandler.php';
require_once dirname(__DIR__, 3) . '/classes/RequestHandler.php';
require_once dirname(__DIR__, 3) . '/classes/DBHandler.php';
$responseHandler = new ResponseHandler();
$requestHandler = new RequestHandler();

// Définir les règles de validation
$rules = [
    'pass_old' => ['type' => 'string', 'max_length'=> 30],
    'pass_new' => ['type' => 'string', 'max_length'=> 30]
];

$data= $requestHandler->handleRequest($data, $rules); 

//1. On vérifie que l'ancien mot de passe correspond au mot de passe actuel dans la bdd

$query = "SELECT * FROM user WHERE id = :id";
$stmt = $dbh->prepare($query);

// Exécuter la requête en liant les paramètres
$stmt->bindParam(':id', $_SESSION['user']['id']);
$stmt->execute();
$resultat= $stmt->fetchAll(PDO::FETCH_ASSOC);

$resultat = $resultat[0];
$hashedPassword = $resultat['password'];
// Vérifier si le couple identifiant/mot de passe existe
if (password_verify($data['pass_old'], $hashedPassword)) {
    // Authentification réussie
    $mot_de_passe_hache = password_hash($data['pass_new'], PASSWORD_DEFAULT);
    try {
        $dbHandler = new DBHandler($dbh);
        $table = 'user';
        $key = ['id' => $_SESSION['user']['id']];
        $data = ['password' => $mot_de_passe_hache];
        $dbHandler->update($table, $key, $data);
        $responseHandler ->addData('statut', '200');
        echo $responseHandler->sendResponse(true, 'Mot de passe mis à jour');
        exit();
    
    }
    catch(Exception $e){
        echo $responseHandler->sendResponse(false, 'erreur : '.$e);
        exit();
    }
} else {
    // Authentification échouée
    $responseHandler ->addData('statut', '101');
    echo $responseHandler->sendResponse(true, 'Ancien mot de passe incorect');
    exit();
}





?>