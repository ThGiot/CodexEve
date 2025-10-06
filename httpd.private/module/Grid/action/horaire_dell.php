<?php

require_once dirname(__DIR__, 3) . '/config.php';
require_once dirname(__DIR__, 3) . '/sql.php';
require_once dirname(__DIR__, 3) . '/classes/ResponseHandler.php';
require_once dirname(__DIR__, 3) . '/classes/RequestHandler.php';
require_once dirname(__DIR__, 1) . '/classes/HoraireService.php';

$responseHandler = new ResponseHandler();
$requestHandler = new RequestHandler();
$horaireService = new HoraireService($dbh);

$rules = [
    'horaire_id' => ['type' => 'INT']
];

// 🔹 Gestion des erreurs d'entrée utilisateur
$data = $requestHandler->handleRequest($data, $rules);

// 🔹 Vérification que l'ID est bien un entier valide
$horaireId = filter_var($data['horaire_id'], FILTER_VALIDATE_INT);
if ($horaireId === false || $horaireId <= 0) {
    exit($responseHandler->sendResponse(false, "ID de période invalide."));
}

// 🔹 Suppression avec gestion d'erreur
if ($horaireService->supprimerHoraire($horaireId)) {
    exit($responseHandler->sendResponse(true, "Horaire supprimée."));
} else {
    exit($responseHandler->sendResponse(false, "Erreur lors de la suppression de la période."));
}

?>