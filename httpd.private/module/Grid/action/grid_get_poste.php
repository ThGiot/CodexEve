<?php
require_once dirname(__DIR__, 3) . '/config.php';
require_once dirname(__DIR__, 3) . '/sql.php';
require_once dirname(__DIR__, 3) . '/classes/ResponseHandler.php';
require_once dirname(__DIR__, 3) . '/classes/RequestHandler.php';
require_once dirname(__DIR__, 3) . '/classes/DBHandler.php';


$responseHandler = new ResponseHandler();
$requestHandler = new RequestHandler();

$rules = ['poste_id' => ['type' => 'INT']];
// Gérer la requête avec authentification, assainissement, et validation
$data = $requestHandler->handleRequest($data, $rules);

$sql = "SELECT gp.id AS poste_id,
gp.nom, 
gp.numero, 
gh.nom AS horaire, 
gh.id AS horaire_id,
ga.nom AS association, 
gpt.nom AS poste_type,
gz.nom AS zone_nom,
ga.id AS association_id, 
gpt.id AS poste_type_id, 
gz.id AS zone_id
FROM grid_poste gp
LEFT JOIN grid_horaire gh ON gp.horaire_id = gh.id 
LEFT JOIN grid_association ga ON ga.id = gp.association_id
LEFT JOIN grid_poste_type gpt ON gpt.id = gp.poste_type_id
LEFT JOIN grid_zone gz ON gz.id = gp.zone_id
LEFT JOIN grid_horaire_periode ghp ON ghp.horaire_id = gh.id
WHERE gp.client_id = :client_id AND gp.id = :poste_id
ORDER BY gpt.nom,gp.numero";

$stmt = $dbh->prepare($sql);
$stmt->bindParam(':client_id', $_SESSION['client_actif']);
$stmt->bindParam(':poste_id', $data['poste_id']);
$stmt->execute();
$poste = $stmt->fetch(PDO::FETCH_ASSOC);

$responseHandler->addData('data', $poste);
echo $responseHandler->sendResponse(true, 'success');

?>