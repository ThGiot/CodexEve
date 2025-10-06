<?php
require_once dirname(__DIR__, 3) . '/config.php';
require_once dirname(__DIR__, 3) . '/sql.php';
require_once dirname(__DIR__, 3) . '/classes/ResponseHandler.php';
require_once dirname(__DIR__, 3) . '/classes/RequestHandler.php';
require_once dirname(__DIR__, 3) . '/classes/DBHandler.php';
require_once dirname(__DIR__, 1) . '/classes/HoraireService.php';



$responseHandler = new ResponseHandler();
$requestHandler = new RequestHandler();
$horaireService = new HoraireService($dbh);
$rules = [
    'horaire_id' => ['type' => 'INT']
];
// Gérer la requête avec authentification, assainissement, et validation
$data = $requestHandler->handleRequest($data, $rules);

$periodes = $horaireService->getHoraireAvecPeriodes((int) $data['horaire_id']);
if (!$periodes) {
    echo json_encode(["success" => false, "message" => "Aucune période trouvée pour cet horaire"]);
} else {
    echo json_encode(["success" => true, "data" => $periodes], JSON_PRETTY_PRINT);
}
exit;

?>