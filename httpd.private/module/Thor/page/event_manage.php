<?php
require_once PRIVATE_PATH . '/classes/PageLayout.php'; 
require_once PRIVATE_PATH . '/classes/Form.php'; 
require_once dirname(__DIR__, 3) . '/classes/RequestHandler.php';
require_once dirname(__DIR__, 3) . '/classes/EventPage.php';
require_once PRIVATE_PATH . '/classes/Modal.php'; 
include_once 'event_manage_style.php';

$requestHandler = new RequestHandler();

// Définir les règles de validation
$rules = [
    'evenement_id' => ['type' => 'int']
];
// Gérer la requête avec authentification, assainissement, et validation
$data = $requestHandler->handleRequest($data, $rules);


try {
    $sql = "SELECT * FROM thor_evenement WHERE id = :id AND client_id= :client_id";
    $stmt = $dbh->prepare($sql);
    $stmt ->bindParam(':id', $data['evenement_id']);
    $stmt ->bindParam(':client_id', $_SESSION['client_actif']);
    $stmt->execute();
    $evenement = $stmt->fetch(PDO::FETCH_ASSOC);
    
} catch (PDOException $e) {
    echo "Erreur lors de la récupération du prestataire : " . $e->getMessage();
    exit;
}

function createModalContent($formId, $inputs, $modalId, $modalTitle, $onClickAction = '', $size='') {
    $body = '<form id="'.$formId.'">';
    $form = new Form("monFormulaire", "monFormulaire", "post", "traitement.php", "Mon Formulaire");

    foreach ($inputs as $input) {
        $input_name = $input['name'];
        $body .= ucfirst($input_name) . ':';
        $body .= $form->renderSingleField(
            type: $input['type'],
            id: $input['prefix'] . $input_name,
            name: $input_name,
            placeholder: $input_name
        );
        $body .= '</br>';
    }

    $body .= '</br></form>';

    $modal = new Modal(
        id: $modalId, 
        title: $modalTitle, 
        body: $body,
        headerClass: "",
        okayButtonClass: "primary",
        okayButtonText : "Enregistrer",
        cancelButtonClass: "outline-secondary",
        showOkayButton: true,
        showButton : false,
        size : $size
    );

    if ($onClickAction != '') {
        $modal->setOkayButtonOnClick($onClickAction);
    }

    echo $modal->render();
}
// Création du Modal "new_module"
createModalContent(
    formId: "monFormulaire",
    inputs: [
        ['name' => 'nom', 'type' => 'text', 'prefix' => ''],
        ['name' => 'groupe', 'type' => 'text', 'prefix' => ''],
        ['name' => 'volontaire', 'type' => 'number', 'prefix' => ''],
        ['name' => 'date_debut', 'type' => 'date', 'prefix' => ''],
        ['name' => 'date_fin', 'type' => 'date', 'prefix' => ''],
        ['name' => 'heure_debut', 'type' => 'time', 'prefix' => ''],
        ['name' => 'heure_fin', 'type' => 'time', 'prefix' => '']
    ],
    modalId: "moduleAdd",
    modalTitle: "Ajouter un module",
   
);

createModalContent(
    formId: "volontaireAddMan",
    inputs: [
        ['name' => 'nom', 'type' => 'text', 'prefix' => 'volontaireAddMan'],
        ['name' => 'prenom', 'type' => 'text', 'prefix' => 'volontaireAddMan'],
        ['name' => 'CS', 'type' => 'text', 'prefix' => 'volontaireAddMan']
    ],
    modalId: "volontaireAddMan",
    modalTitle: "Ajout Manuel"
);
// Création du modals pour ajouter un volontaire
$select = '<select id="volontaireSelect" class="form-select" id="organizerSingle" data-choices="data-choices" data-options=\'{"removeItemButton":true,"placeholder":true}\'></select>';
$modal = new Modal(
    id: "volontaireAdd", 
    title: "Ajouter un volontaire", 
    body: 'Volontaire :</br>'.$select,
    headerClass: "",
    okayButtonClass: "primary",
    okayButtonText : "Ajouter",
    cancelButtonClass: "outline-secondary",
    showOkayButton: true,
    showButton : false,
    size : 'lg'
);
echo $modal->render();

//Modal confirmation suppresion module

$modal = new Modal(
    id: "moduleDell", 
    title: "Suppresion module", 
    body: 'Vous êtes sur le point de supprimer un module',
    headerClass: "danger",
    okayButtonClass: "primary",
    okayButtonText : "Supprimer",
    cancelButtonClass: "outline-secondary",
    showOkayButton: true,
    showButton : false
);
echo $modal->render();

// Création du Modal "update_module"
createModalContent(
    formId: "updateModuleForm",
    inputs: [
        ['name' => 'nom', 'type' => 'text', 'prefix' => 'updateModule'],
        ['name' => 'groupe', 'type' => 'text', 'prefix' => 'updateModule'],
        ['name' => 'volontaire', 'type' => 'number', 'prefix' => 'updateModule'],
        ['name' => 'date_debut', 'type' => 'date', 'prefix' => 'updateModule'],
        ['name' => 'date_fin', 'type' => 'date', 'prefix' => 'updateModule'],
        ['name' => 'heure_debut', 'type' => 'time', 'prefix' => 'updateModule'],
        ['name' => 'heure_fin', 'type' => 'time', 'prefix' => 'updateModule']
    ],
    modalId: "updateModule",
    modalTitle: "Modifier le module"
);

// Création du Modal "update Groupe"
createModalContent(
    formId: "formGroup",
    inputs: [
        ['name' => 'nom', 'type' => 'text', 'prefix' => 'updateGroupe']
    ],
    modalId: "updateGroup",
    modalTitle: "Modifier le groupe"
);

createModalContent(
    formId: "formDispo",
    inputs: [
        ['name' => 'fichier', 'type' => 'file', 'prefix' => 'dispo']
    ],
    modalId: "dispoImport",
    modalTitle: "Importer les disponibilitées",
    onClickAction: 'node(\'thor_event_import_dispo\', {formId : \'formDispo\',evenementId : \''.$data['evenement_id'].'\'})'
);

// Création du Modal "update Event"
createModalContent(
    formId: "formEvent",
    inputs: [
        ['name' => 'nom', 'type' => 'text', 'prefix' => 'updateEvent'],
        ['name' => 'date', 'type' => 'date', 'prefix' => 'updateEvent'],
        ['name' => 'infos', 'type' => 'text', 'prefix' => 'updateEvent']
    ],
    modalId: "updateEvent",
    modalTitle: "Infos Événement"
);




//Générer la date de l'event
setlocale(LC_TIME, 'fr_FR.UTF-8', 'fr_FR', 'fr', 'fra', 'french');

// Créer une instance de DateTime à partir de la chaîne de date
$dateString = $evenement['date'];
$date = new DateTime($dateString);

// Formater la date
$formattedDate = strftime("%A %e %B %Y", $date->getTimestamp());


$eventPage = new EventPage();
$eventPage->setTitle($evenement['nom'].'<button class="btn" onclick="node(\'thor_event_update\', {evenementId : \''.$data['evenement_id'].'\', nom : \''.$evenement['nom'].'\',date : \''.$evenement['date'].'\',infos : \''.$evenement['infos'].'\'})"><span  data-feather="edit-3" style="color: white;height: 15px; width: 15px;"></span></button>');
$eventPage->setSubtitle($formattedDate);
$eventPage->setEventDetails([
    '<p id="eventTitle">'.$evenement['infos'].'</p>',
    '
    
    <button  onclick="node(\'thor_module_add\', {formId : \'monFormulaire\',evenementId : \''.$data['evenement_id'].'\'})" class="btn btn-link me-1 mb-1"><span  data-feather="plus-circle"></span> Ajouter un module</button>
    <button onclick="var myModal = new bootstrap.Modal(document.getElementById(\'dispoImport\'));myModal.show();"class="btn btn-link me-1 mb-1"><span  data-feather="plus-circle"></span> Importer les disponibilitées</button>
    ',
    // ... More details
]);


$sql = "SELECT * FROM thor_module WHERE evenement_id = :evenement_id ORDER BY groupe, date_debut, heure_debut";
$stmt = $dbh->prepare($sql);
$stmt->bindParam(':evenement_id', $data['evenement_id']);
$stmt->execute();
$modules = $stmt->fetchAll(PDO::FETCH_ASSOC);

$groupedModules = [];
foreach ($modules as $module) {
    $groupTitle = $module['groupe'].'<button class="btn" onclick="node(\'thor_groupe_update\', {groupe : \''.$module['groupe'].'\', evenementId : \''.$data['evenement_id'].'\'})"><span  data-feather="edit-3" style="height: 15px; width: 15px;"></span></button>';
    $title = $module['nom'];
    $time = '<i>'.$module['date_debut'].' </i></br> '.$module['heure_debut'] . ' - ' . $module['heure_fin'].'</br> ';
    //Récupération du personnel par module
    $sql = "SELECT  thor_volontaire.id,thor_volontaire.nom, thor_volontaire.centre_secours,thor_volontaire.prenom 
            FROM thor_module_inscription
            JOIN thor_volontaire ON thor_volontaire.id = thor_module_inscription.volontaire_id
            WHERE thor_module_inscription.module_id = :module_id
            ORDER BY  thor_volontaire.centre_secours, thor_volontaire.nom";
    // Préparation de la requête avec PDO
    $stmt = $dbh->prepare($sql);
    // Liaison des paramètres
    $stmt->bindParam(':module_id', $module['id']);
    $stmt->execute();
    // Récupération des résultats
    $resultats = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $personnel = [];
    $count_volontaire = 0;
    foreach ($resultats as $row) {
        //On recherche s'il y a une qualification 
        $sql = "SELECT  * FROM thor_evenement_qualification
                WHERE evenement_id = :evenement_id AND volontaire_id = :volontaire_id";
                // Préparation de la requête avec PDO
                $stmt = $dbh->prepare($sql);
                $stmt->bindParam(':evenement_id', $data['evenement_id']);
                $stmt->bindParam(':volontaire_id', $row['id']);
                $stmt->execute();
                $qualification = $stmt->fetch(PDO::FETCH_ASSOC);
                if (is_array($qualification) && isset($qualification['qualification']) && !empty($qualification['qualification'])) {
                    $qualification['qualification'] = '[' . $qualification['qualification'] . ']';
                } else {
                    $qualification['qualification'] = '';  // Or some default value as needed
                }
        $personnel[] = $row['nom'] . ' ' . $row['prenom'].'('.$row['centre_secours'].')'.$qualification['qualification'].'   
        <button class="btn" onclick="node(\'thor_volontaire_dell\', {moduleId : \''.$module['id'].'\', volontaireId : \''.$row['id'].'\'})"><span  data-feather="delete" style="height: 15px; width: 15px;"></span></button>';
        $count_volontaire++;
    }
    $count = $count_volontaire.'/'.$module['nb_volontaire'];

    $groupedModules[$groupTitle][] = [
        'title' => $title.' <button class="btn" onclick="node(\'thor_module_update\', { formId : \'updateModuleForm\',
                                                                                        evenementId : \''.$data['evenement_id'].'\', 
                                                                                        moduleId : \''.$module['id'].'\', 
                                                                                        nom:\''.$module['nom'].'\',
                                                                                        groupe:\''.$module['groupe'].'\',
                                                                                        nb_volontaire:\''.$module['nb_volontaire'].'\',
                                                                                        date_debut :\''.$module['date_debut'].'\',
                                                                                        date_fin :\''.$module['date_fin'].'\',
                                                                                        heure_debut :\''.$module['heure_debut'].'\',
                                                                                        heure_fin :\''.$module['heure_fin'].'\'})

                                                                                        "><span  data-feather="edit-3" style="height: 15px; width: 15px;"></span></button>
                                                                                        <button class="btn" onclick="node(\'thor_volontaire_add\', {moduleId : \''.$module['id'].'\', evenementId : \''.$data['evenement_id'].'\'})"><span  data-feather="user-plus" style="height: 15px; width: 15px;"></span></button>
                                                                                        <button class="btn" onclick="node(\'thor_volontaire_add_man\', {moduleId : \''.$module['id'].'\', evenementId : \''.$data['evenement_id'].'\'})"><span  data-feather="user-x" style="height: 15px; width: 15px;"></span></button>
                                                                                        <button class="btn" onclick="node(\'thor_module_copy\', {moduleId : \''.$module['id'].'\', evenementId : \''.$data['evenement_id'].'\'})"><span  data-feather="copy" style="height: 15px; width: 15px;"></span></button>
                                                                                        <button class="btn" onclick="node(\'thor_module_dell\', {moduleId : \''.$module['id'].'\', evenementId : \''.$data['evenement_id'].'\'})"><span  data-feather="trash-2" style="height: 15px; width: 15px;"></span></button>
        ',
        'time' => $time,
        'personnel' => $personnel,
        'count' => $count
    ];
}

// Add groups and blocks
foreach ($groupedModules as $groupTitle => $blocks) {
    $eventPage->addGroup($groupTitle, $blocks);
}

echo $eventPage->render();

?>

