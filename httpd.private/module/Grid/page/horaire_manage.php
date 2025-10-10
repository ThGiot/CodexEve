<?php
require_once PRIVATE_PATH . '/classes/Table.php';
require_once PRIVATE_PATH . '/classes/Form.php';
require_once PRIVATE_PATH . '/classes/PageLayout.php';
require_once PRIVATE_PATH . '/classes/Modal.php';
require_once PRIVATE_PATH . '/classes/RequestHandler.php';


use Grid\HoraireService;

$requestHandler = new RequestHandler();

// Définir les règles de validation
$rules = ['horaire_id' => ['type' => 'int']];
$data = $requestHandler->handleRequest($data, $rules);

$horaireService = new HoraireService($dbh);

$horaireId = isset($data['horaire_id']) ? (int) $data['horaire_id'] : 0;
$clientId = $_SESSION['client_actif'];

// Vérifie si l'ID de l'horaire est valide
if (!$horaireId) {
    exit('Aucun horaire spécifié.');
}

// Récupère les périodes associées à l’horaire
$periodes = $horaireService->getHoraireAvecPeriodes($horaireId);

// **1️⃣ Création du tableau des périodes**
$table = new Table(
    title: "Plages de l'horaires sélectionné",
    columns: ["Groupe Horaire", "Début", "Fin"],
    id: "tablePeriodes"
);

foreach ($periodes as $periode) {
    $dateDebut = strftime('%A %H:%M', strtotime($periode['date_debut'])); // Exemple : "Lundi 08:00"
    $dateFin = strftime('%A %H:%M', strtotime($periode['date_fin']));     // Exemple : "Mardi 10:00"

    $table->addRow(
        [
            "Groupe Horaire" => $periode['periode_nom'],
            "Début" => ucfirst($dateDebut),
            "Fin" => ucfirst($dateFin)
        ],
        [
            ["name" => "Modifier", "link" => "node('grid_horaire_update_periode', {periodeId: '" . $periode['periode_id'] . "'})", "class" => ""],
            ["name" => "Supprimer", "link" => "node('grid_horaire_delete_periode', {periodeId: '" . $periode['periode_id'] . "'})", "class" => "danger"]
        ]
    );
}

// Création d'une instance de Form pour générer les champs input
$form = new Form(id: "formAddPeriode", name: "formAddPeriode", method: "POST", action: "");

// Générer un champ texte pour le "Groupe Horaire"
$inputNom = $form->renderSingleField(
    type: 'text',
    id: 'add_periode_nom',
    name: 'periode_nom',
    placeholder: 'Nom de la période'
);

// Générer un champ DateTime Picker pour "Début"
$inputDebut = $form->renderSingleField(
    type: 'datetime-local',
    id: 'add_periode_debut',
    name: 'periode_debut',
    placeholder: 'Debut'
);

// Générer un champ DateTime Picker pour "Fin"
$inputFin = $form->renderSingleField(
    type: 'datetime-local',
    id: 'add_periode_fin',
    name: 'periode_fin',
    placeholder: 'Fin'
);

// Ajouter la ligne au tableau
$table->addRow(
    [
        "Groupe Horaire" => $inputNom,
        "Début" => $inputDebut,
        "Fin" => $inputFin
    ],
    [
        ["name" => "Enregistrer", "link" => "node('grid_horaire_save', {horaireId: '" . $horaireId . "'})", "class" => ""]
       
    ]
);




// **3️⃣ Affichage du formulaire et du tableau**
$layout = new PageLayout();
$layout->addElement($table->render(true));

echo $layout->render();

$modal = new Modal(
    id: "horairePeriodeDell", 
    title: "Suppression d'une plage horaire", 
    body: '<b>Vous êtes sur le point de supprimer une plage horaire.</b></br></br>Toutes les informations liées à cette plages seront supprimées !',
    headerClass: "danger",
    okayButtonClass: "primary",
    okayButtonText : "Supprimer",
    cancelButtonClass: "outline-secondary",
    showOkayButton: true,
    showButton : false
);
echo $modal->render();
?>

