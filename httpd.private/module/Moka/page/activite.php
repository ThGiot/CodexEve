<?php

require_once PRIVATE_PATH . '/classes/Table.php'; 
require_once PRIVATE_PATH . '/classes/PageLayout.php'; 
require_once PRIVATE_PATH . '/classes/Form.php'; 


require_once dirname(__DIR__, 1) . '/fonctions/analytiqueGetOptions.php';





$table = new Table(title: "Liste des activites",
                  columns: ["Analytique", 
                            "CODE",
                            "Type de rémunération"
                          ], 
                  id:"activiteListe",);

try {
    $sql = "SELECT act.id AS act_id, ana.id AS ana_id, act.*, ana.*  
            FROM moka_activite act
            JOIN moka_analytique ana ON act.analytique_id = ana.id
            WHERE act.client_id =:client_id";
    $stmt = $dbh->prepare($sql);
    $stmt -> bindParam(':client_id', $_SESSION['client_actif']);
    $stmt->execute();
    $activites = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($activites as $activite) {

        $table->addRow(
            [   "Analytique" => $activite['analytique'], 
                "CODE" => $activite['code'],
                "Type de rémunération" => $activite['remuneration_type']
             ],
            [
                ["name" => "Manage", "link" => "getContent(202,{activite_id : '".$activite['act_id']."'})", "class" => ""],
                ["name" => "Remove", "link" => 'node(\'moka_activite_dell\',{activiteId : \''.$activite['act_id'].'\'})', "class" => "danger"],
            ]
        );
    }
} catch (PDOException $e) {
    echo "Erreur lors de la récupération des activites : " . $e->getMessage();
    exit;
}
//Récupération des analytiques du clients 
$analytique_options = analytiqueGetOptions($dbh, $_SESSION['client_actif']);

$onsubmit = 'event.preventDefault(); node(\'moka_addactivite\', {formId : \'addactivite\'})';

$form = new Form(   id: 'addactivite', 
                    name: 'addactivite',
                    method: 'POST', 
                    action: $onsubmit, 
                    title: 'Ajouter un activite'
                );


$form->addField(    type: 'text', 
                    id: 'codeActivite',
                    name: 'codeActivite', 
                    label: 'Code (dans Ebrigade exemple : SMUR ou APS)',
                    placeholder: 'Code de l\'activite '
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
   
);
$form->addField(
    type: 'select', 
    id: 'selectAnalytique',
    name: 'selectAnalytique', 
    label: 'Analytique',
    options: $analytique_options
);
$form->setSubmitButton(id: 'buttonSubmit',name: 'submit',value: 'send',text: 'Enregistrer');
$layout = new PageLayout();
$layout->addElement($table->render()); 
$layout->addElement($form->render(),4); 
echo $layout->render();


  ?>