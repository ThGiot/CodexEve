<?php
require_once dirname(__DIR__, 3) . '/classes/RequestHandler.php';
require_once dirname(__DIR__, 3) . '/classes/ResponseHandler.php';
require_once dirname(__DIR__, 3) . '/classes/DBHandler.php';
require_once dirname(__DIR__, 3) . '/sql.php';
require_once dirname(__DIR__, 1) . '/classes/HoraireService.php';

$requestHandler = new RequestHandler();
$responseHandler = new ResponseHandler();

$rules = [
    'horaire_id' => ['type' => 'INT'],
    'nom' => ['type' => 'string', 'max_length' => 40],
    'plage_debut' => ['type' => 'string', 'max_length' => 40],
    'plage_fin' => ['type' => 'string', 'max_length' => 40]
];
// Gérer la requête avec authentification, assainissement, et validation
$data = $requestHandler->handleRequest($data, $rules);
$horaireService = new HoraireService($dbh);
$horaireService -> ajouterPeriode(
                                    horaireId : $data['horaire_id'], 
                                    nom : $data['nom'], 
                                    dateDebut : $data['plage_debut'], 
                                    dateFin : $data['plage_fin'],
                                    clientId : $_SESSION['client_actif']
                                    );
exit($responseHandler->sendResponse(true, "Période ajoutée."));

?>