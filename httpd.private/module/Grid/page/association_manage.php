<?php
require_once PRIVATE_PATH . '/classes/PageLayout.php'; 
require_once PRIVATE_PATH . '/classes/Form.php'; 
require_once PRIVATE_PATH . '/classes/Table.php'; 
require_once dirname(__DIR__, 3) . '/classes/RequestHandler.php';
use Grid\PosteService;

$requestHandler = new RequestHandler();
$posteService = new PosteService($dbh);

// ───── Validation des données ─────
$rules = ['association_id' => ['type' => 'int']];
$data = $requestHandler->handleRequest($data, $rules);
$clientId = $_SESSION['client_actif'];
$association = $posteService->getAssociation($clientId, $data['association_id']);

// ───── Formulaire d'association ─────
$onsubmit = 'event.preventDefault(); node(\'grid_association_maj\', {formId : \'formAssociation\', association_id :\'' . $association['id'] . '\'})';

$form = new Form(
    id: 'formAssociation',
    name: 'formAssociation',
    method: 'POST',
    action: $onsubmit,
    title: 'Fiche Association'
);

$form->addField(type: 'text', id: 'nom', name: 'nom', label: 'Nom', value: $association['nom'], group: 'nom');
$form->addField(type: 'text', id: 'adresse', name: 'adresse', label: 'Adresse', value: $association['adresse'], group: 'adresse');
$form->addField(type: 'text', id: 'contact_nom', name: 'contact_nom', label: 'Personne de contacte', value: $association['contact_nom'], group: 'contact');
$form->addField(type: 'text', id: 'contact_email', name: 'contact_email', label: 'email', value: $association['contact_email'], group: 'contact');
$form->addField(type: 'text', id: 'contact_telephone', name: 'contact_telephone', label: 'Téléphone', value: $association['contact_telephone'], group: 'contact');
$form->setSubmitButton(id: 'buttonSubmit', name: 'submit', value: 'send', text: 'Enregistrer');

// ───── Tableau croisé dynamique Zone × Horaire ─────
$tableData = $posteService->getTableauCroiseVolontaires($clientId, $association['id']);

$horaireNoms = [];
foreach ($tableData as $ligne) {
    foreach ($ligne as $h => $_) {
        if ($h !== "Total") {
            $horaireNoms[$h] = true;
        }
    }
}
ksort($horaireNoms);
$columns = array_merge(["Zone"], array_keys($horaireNoms), ["Total"]);

$table = new Table(
    title: "Répartition des volontaires (périodes × postes) dans la grille",
    columns: $columns,
    id: "zoneHoraireTable"
);

foreach ($tableData as $zone => $horaires) {
    $row = ["Zone" => $zone];
    foreach ($horaireNoms as $h => $_) {
        $row[$h] = $horaires[$h] ?? 0;
    }
    $row["Total"] = $horaires["Total"] ?? 0;
    $table->addRow($row);
}
$horairesDispo = $posteService->getHorairesDisponibilite($clientId, $association['id']);

$dispoForm = new Form(
    id: 'formDispo',
    name: 'formDispo',
    method: 'POST',
    action: "event.preventDefault(); node('grid_association_dispo_save', {formId: 'formDispo', association_id: '{$association['id']}'})",
    title: "Volontaires disponibles par horaire"
);

foreach ($horairesDispo as $horaire) {
    $dispoForm->addField(
        type: 'number',
        id: 'horaire_' . $horaire['horaire_id'],
        name: 'horaire_' . $horaire['horaire_id'],
        label: $horaire['horaire_nom'],
        value: $horaire['nb'] ?? 0,
        group: 'disponibilite'
    );
}

$dispoForm->setSubmitButton(id: 'btnSaveDispo', name: 'submit', value: 'save', text: 'Enregistrer les disponibilités');

// ───── Affichage final ─────
$layout = new PageLayout();

$layout->addElement($form->render());
$layout->addElement($table->render());
$layout->addElement($dispoForm->render());
echo $layout->render();
?>
