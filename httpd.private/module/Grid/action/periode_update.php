<?php
require_once dirname(__DIR__, 3) . '/classes/RequestHandler.php';
require_once dirname(__DIR__, 3) . '/classes/ResponseHandler.php';
require_once dirname(__DIR__, 3) . '/classes/DBHandler.php';
require_once dirname(__DIR__, 3) . '/sql.php';

use Grid\HoraireService;

$responseHandler = new ResponseHandler();
$requestHandler = new RequestHandler();
$rules = [
    'horaire_id' => ['type' => 'int'],
    'periodes' => [
        'type' => 'array',
        'sub_rules' => [
            'nom' => ['type' => 'string'],
            'date_debut' => ['type' => 'string'],
            'date_fin' => ['type' => 'string']
        ]
    ]
];

// Gérer la requête avec authentification, assainissement, et validation
$data = $requestHandler->handleRequest($data, $rules);


$horaireService = new HoraireService($dbh);
$result = $horaireService->mettreAJourHoraire($data['horaire_id'], $data['periodes'],$_SESSION['client_actif']);

if ($result) {
    exit($responseHandler->sendResponse(true,'Horaire mise à jour'));
} else {
    exit($responseHandler->sendResponse(true,'Erreur lors de la mise à jour.'));
}
?>