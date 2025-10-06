<?php
require_once dirname(__DIR__, 3) . '/config.php';
require_once dirname(__DIR__, 3) . '/sql.php';
require_once dirname(__DIR__, 3) . '/classes/ResponseHandler.php';
require_once dirname(__DIR__, 3) . '/classes/RequestHandler.php';
require_once dirname(__DIR__, 3) . '/classes/DBHandler.php';



$responseHandler = new ResponseHandler();
$requestHandler = new RequestHandler();

$rules = [
    'prestation_id' => ['type' => 'int'],
    'nb_heure' => ['type' => 'int'],

    
];
// Gérer la requête avec authentification, assainissement, et validation
$data = $requestHandler->handleRequest($data, $rules);

//Vérification que cet analytlique de client n'existe pas encore

$query = "UPDATE moka_prestation_ebrigade SET heure =:heure WHERE id = :id";
$stmt = $dbh->prepare($query);
$stmt->bindParam(':heure', $data['nb_heure']);
$stmt->bindParam(':id', $data['prestation_id']);

$stmt->execute();
exit($responseHandler->sendResponse(true,'Prestation mise à jour'));

?>