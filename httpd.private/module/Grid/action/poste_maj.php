<?php

require_once dirname(__DIR__, 3) . '/config.php';
require_once dirname(__DIR__, 3) . '/sql.php';
require_once dirname(__DIR__, 3) . '/classes/ResponseHandler.php';
require_once dirname(__DIR__, 3) . '/classes/RequestHandler.php';
require_once dirname(__DIR__, 3) . '/classes/DBHandler.php';


$return = '';
$responseHandler = new ResponseHandler();
$requestHandler = new RequestHandler();
$rules = [
    'poste_id' => ['type' => 'INT'],
    'poste_numero' => ['type' => 'INT'],
    'poste_nom' => ['type' => 'string', 'max_length' => 40],
    'poste_association_id' => ['type' => 'INT'],
    'poste_zone_id' => ['type' => 'INT'],
    'poste_type_id' => ['type' => 'INT'],
    'horaire_id' => ['type' => 'INT'],
];
// Gérer la requête avec authentification, assainissement, et validation
$data = $requestHandler->handleRequest($data, $rules);



$query = "UPDATE grid_poste SET nom = :nom, 
                                numero = :poste_numero, 
                                association_id = :association_id, 
                                zone_id = :zone_id, 
                                poste_type_id = :poste_type_id, 
                                horaire_id = :horaire_id 
                            WHERE id = :poste_id AND client_id = :client_id";

$stmt = $dbh->prepare($query);

$stmt->bindParam(':nom',  $data['poste_nom']); // Toujours une chaîne, pas besoin de gérer NULL

// Gérer les valeurs NULL pour les autres champs sauf nom et client_id
$poste_numero = !empty($data['poste_numero']) ? $data['poste_numero'] : null;
$association_id = !empty($data['poste_association_id']) ? $data['poste_association_id'] : null;
$zone_id = !empty($data['poste_zone_id']) ? $data['poste_zone_id'] : null;
$poste_type_id = !empty($data['poste_type_id']) ? $data['poste_type_id'] : null;
$horaire_id = !empty($data['horaire_id']) ? $data['horaire_id'] : null;
$poste_id = !empty($data['poste_id']) ? $data['poste_id'] : null;

$stmt->bindParam(':poste_numero', $poste_numero, is_null($poste_numero) ? PDO::PARAM_NULL : PDO::PARAM_INT);
$stmt->bindParam(':association_id', $association_id, is_null($association_id) ? PDO::PARAM_NULL : PDO::PARAM_INT);
$stmt->bindParam(':zone_id', $zone_id, is_null($zone_id) ? PDO::PARAM_NULL : PDO::PARAM_INT);
$stmt->bindParam(':poste_type_id', $poste_type_id, is_null($poste_type_id) ? PDO::PARAM_NULL : PDO::PARAM_INT);
$stmt->bindParam(':horaire_id', $horaire_id, is_null($horaire_id) ? PDO::PARAM_NULL : PDO::PARAM_INT);
$stmt->bindParam(':poste_id', $poste_id, is_null($poste_id) ? PDO::PARAM_NULL : PDO::PARAM_INT);

$stmt->bindParam(':client_id', $_SESSION['client_actif']); // Toujours défini

$stmt->execute();
exit($responseHandler->sendResponse(true, 'Poste mis à jour'.$return));

?>