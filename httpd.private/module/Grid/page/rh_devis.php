<?php
require_once PRIVATE_PATH . '/vendor/autoload.php';
require dirname(__DIR__, 3) . '/vendor/autoload.php';
require_once PRIVATE_PATH . '/classes/Table.php'; 
require_once PRIVATE_PATH . '/classes/PageLayout.php'; 
require_once PRIVATE_PATH . '/classes/createModalContent.php'; 
require_once PRIVATE_PATH . '/classes/Modal.php'; 
require_once dirname(__DIR__, 3) . '/classes/RequestHandler.php';

use Grid\PosteService;
use Grid\FilterService;
use Grid\WordDevisExporter;

$requestHandler = new RequestHandler();
$posteService = new PosteService($dbh);
$clientId = (int) $_SESSION['client_actif'];
// ───── Validation des données ─────
$rules = ['association_id' => ['type' => 'int']];
$data = $requestHandler->handleRequest($data, $rules);

$association = $posteService->getAssociation($clientId,$data['association_id']);
// ───── Récupération des ID association (nom => id) ─────
$devis = $posteService->getHorairesParJour($clientId, $data['association_id']);
$chemin = WordDevisExporter::export($devis, $association['nom'], 'devis'.$association['nom'].'.docx');

echo '<a href="devis'.$association['nom'].'.docx" download>Télécharger le devis</a>';

?>
