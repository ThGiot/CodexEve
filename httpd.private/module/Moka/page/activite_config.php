<?php
require_once PRIVATE_PATH . '/classes/Table.php'; 
require_once PRIVATE_PATH . '/classes/PageLayout.php'; 
require_once PRIVATE_PATH . '/classes/Toolbar.php'; 
require_once PRIVATE_PATH . '/classes/ClearInput.php'; 
require_once PRIVATE_PATH . '/classes/Form.php'; 
require_once PRIVATE_PATH . '/classes/RequestHandler.php';
require_once dirname(__DIR__, 1) . '/fonctions/analytiqueGetOptions.php';

$requestHandler = new RequestHandler();

// Définir les règles de validation
$rules = [
    'activite_id' => ['type' => 'int']
];
// Gérer la requête avec authentification, assainissement, et validation
$data = $requestHandler->handleRequest($data, $rules);

//Récupération des analytiques du clients 
$analytique_options = analytiqueGetOptions($dbh, $_SESSION['client_actif']);


try {
    // Définir la requête SQL
    $sql = "SELECT * FROM moka_activite WHERE client_id = :client_id AND id= :activite_id";
    $stmt = $dbh->prepare($sql);
    $stmt -> bindParam(':client_id', $_SESSION['client_actif']);
    $stmt->  bindParam(':activite_id', $data['activite_id']);
    $stmt->execute();
    $activite = $stmt->fetch(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    echo "Erreur lors de la récupération des informations activitée : " . $e->getMessage();
    exit;
}
$table = new Table(title: "Gestion des rémunérations",
                  columns: ["Grade (ebrigade)", 
                            "Tarif/h Perm",
                            "Tarif/h Garde"
                          ], 
                  id:"RemunerationListe",);
$form = new Form();

try {
    $sql = "SELECT mar.* FROM moka_activite_remuneration mar JOIN moka_activite ma ON mar.activite_id = ma.id WHERE activite_id = :activite_id AND client_id = :client_id";
    $stmt = $dbh->prepare($sql);
    $stmt -> bindParam(':activite_id', $data['activite_id']);
    $stmt -> bindParam(':client_id', $_SESSION['client_actif']);
    $stmt->execute();
    $remunerations = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($remunerations as $remuneration) {

        $input_grade =  $form->renderSingleField(
            type: 'text',
            id: 'grade_' . $remuneration['id'],
            name: 'grade_' . $remuneration['id'],
            value: $remuneration['grade'],
            options: ['onchange' => 'node(\'moka_remuneration_maj\',    {   remunerationId : \''.$remuneration['id'].'\',
                                                                            activiteId : \''.$activite['id'].'\',
                                                                            toMaj : \'grade\', 
                                                                            inputId :\'grade_' . $remuneration['id'].'\' 
                                                                        })']
        );

        $input_perm =  $form->renderSingleField(
            type: 'text',
            id: 'montant_perm_' . $remuneration['id'],
            name: 'montant_perm_' . $remuneration['id'],
            value: $remuneration['montant_perm'],
            options: ['onchange' => 'node(\'moka_remuneration_maj\',    {   remunerationId : \''.$remuneration['id'].'\',
                                                                            activiteId : \''.$activite['id'].'\',
                                                                            toMaj : \'montant_perm\', 
                                                                            inputId :\'montant_perm_' . $remuneration['id'].'\' 
                                                                        })']
        );

        $input_garde =  $form->renderSingleField(
            type: 'text',
            id: 'montant_garde_' . $remuneration['id'],
            name: 'montant_garde_' . $remuneration['id'],
            value: $remuneration['montant_garde'],
            options: ['onchange' => 'node(\'moka_remuneration_maj\',    {   remunerationId : \''.$remuneration['id'].'\',
                                                                            activiteId : \''.$activite['id'].'\',
                                                                            toMaj : \'montant_garde\', 
                                                                            inputId :\'montant_garde_' . $remuneration['id'].'\' 
                                                                        })']
        );

        $table->addRow(
            [   "Grade (ebrigade)" => $input_grade, 
                "Tarif/h Perm" => $input_perm,
                "Tarif/h Garde" => $input_garde
             ],
            [
                ['name' => 'Remove', 'link' => 'node(\'moka_remuneration_dell\',{remunerationId : \''.$remuneration['id'].'\',activiteId : \''.$activite['id'].'\'})', 'class' => 'danger']
            ]
        );
    }
} catch (PDOException $e) {
    echo "Erreur lors de la récupération des remunerations : " . $e->getMessage();
    exit;
}

$onsubmit = 'event.preventDefault(); node(\'moka_activite_maj\', {activiteId : \''.$activite['id'].'\'})';

$form = new Form(   id: 'activite', 
                    name: 'activite',
                    method: 'POST', 
                    action: $onsubmit, 
                    title: 'Activité : '.$activite['code']
                );


$form->addField(    type: 'text', 
                    id: 'codeActivite',
                    name: 'codeActivite', 
                    label: 'Code (dans Ebrigade exemple : SMUR ou APS)',
                    value: $activite['code']
                );


$selectOptions = [
    ['value' => 'PERM', 'text' => 'Permanence/APS/...'],
    ['value' => 'GARD', 'text' => 'Garde']
];
$form->addField(
    type: 'select', 
    id: 'selectOption',
    name: 'selectOption', 
    label: 'Type de rémunération',
    options: $selectOptions,
    selectedValue: $activite['remuneration_type']
);
$form->addField(
    type: 'select', 
    id: 'selectAnalytique',
    name: 'selectAnalytique', 
    label: 'Analytique',
    options: $analytique_options,
    selectedValue: $activite['analytique_id']
);
$form->setSubmitButton(id: 'buttonSubmit',name: 'submit',value: 'send',text: 'Enregistrer');


$onsubmit = 'event.preventDefault(); node(\'moka_remuneration_add\', {activiteId : \''.$activite['id'].'\'})';
$form_new_remuneration = new Form(   
                    id: 'new_remuneration', 
                    name: 'new_remuneration',
                    method: 'POST', 
                    action: $onsubmit, 
                    title: 'Ajouter une rémunération'
                );


$form_new_remuneration ->addField(    type: 'text', 
                    id: 'grade',
                    name: 'grade', 
                    label: 'Grade (ebrigade exemple : INFI)',
                    placeholder: 'INFI'
                );

$form_new_remuneration ->addField(    type: 'text', 
                    id: 'tarif_perm',
                    name: 'tarif_perm', 
                    label: 'Tarif/h Perm',
                    placeholder: '50'
                );
$form_new_remuneration ->addField(    type: 'text', 
                    id: 'tarif_garde',
                    name: 'tarif_garde', 
                    label: 'Tarif/h Garde',
                    placeholder: '10'
                );
$form_new_remuneration ->setSubmitButton(id: 'buttonSubmit',name: 'submit',value: 'send',text: 'Enregistrer');


$layout = new PageLayout();
$layout->addElement($form->render()); 
$layout->addElement($table->render()); 
$layout->addElement($form_new_remuneration->render(),6); 
echo $layout->render();
?>

