<?php

require_once PRIVATE_PATH . '/classes/Table.php'; 
require_once PRIVATE_PATH . '/classes/PageLayout.php'; 
require_once PRIVATE_PATH . '/classes/Form.php'; 
require_once PRIVATE_PATH . '/classes/createModalContent.php'; 
require_once PRIVATE_PATH . '/classes/Modal.php'; 

$modal = new Modal(
    id: "AnaDell", 
    title: "Suppresion analytique", 
    body: '<b>Vous êtes sur le point de supprimer un analytique.</b></br></br>Toutes les activités et les corrections en attentes qui s\'y réfèrent seront également supprimées !',
    headerClass: "danger",
    okayButtonClass: "primary",
    okayButtonText : "Supprimer",
    cancelButtonClass: "outline-secondary",
    showOkayButton: true,
    showButton : false
);
echo $modal->render();

// Création du Modal maj Analytique
createModalContent(
    formId: "analytiqueMaj",
    inputs: [
        ['name' => 'nom', 'type' => 'text', 'prefix' => 'anaMaj'],
        ['name' => 'analytique', 'type' => 'text', 'prefix' => 'anaMaj'],
        ['name' => 'code', 'type' => 'text', 'prefix' => 'anaMaj'],
        ['name' => 'entitée', 'type' => 'text', 'prefix' => 'anaMaj'],
        ['name' => 'distribution', 'type' => 'textarea', 'prefix' => 'anaMaj']
    ],
    modalId: "modaleAnaMaj",
    modalTitle: "Mise à jour Analytique",
);



$table = new Table(title: "Liste des analytiques",
                  columns: ["Analytique", 
                            "Nom",
                            "Code Centralisateur",
                            "Entité",
                          ], 
                  id:"analytiqueListe",);

try {
    $sql = "SELECT * FROM moka_analytique WHERE client_id =:client_id";
    $stmt = $dbh->prepare($sql);
    $stmt -> bindParam(':client_id', $_SESSION['client_actif']);
    $stmt->execute();
    $analytiques = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($analytiques as $analytique) {

        $table->addRow(
            [   "Analytique" => $analytique['analytique'], 
                "Nom" => $analytique['nom'],
                "Code Centralisateur" => $analytique['code_centralisateur'],
                "Entité" => $analytique['entite']
             ],
            [
                ["name" => "Manage", "link" => "node('moka_analytique_maj',{analytiqueId : '".$analytique['id']."',analytique : '".$analytique['analytique']."',nom : '".$analytique['nom']."', entite : '".$analytique['entite']."', code:'".$analytique['code_centralisateur']."', distribution:'".$analytique['distribution']."' })", "class" => ""],
                ["name" => "Remove", "link" => "node('moka_analytique_dell', {analytiqueId : '".$analytique['id']."'})", "class" => "danger"],
            ]
        );
    }
} catch (PDOException $e) {
    echo "Erreur lors de la récupération des analytiques : " . $e->getMessage();
    exit;
}


$onsubmit = 'event.preventDefault(); node(\'moka_addAnalytique\', {formId : \'addAnalytique\'})';

$form = new Form(   id: 'addAnalytique', 
                    name: 'addAnalytique',
                    method: 'POST', 
                    action: $onsubmit, 
                    title: 'Ajouter un analytique'
                );


$form->addField(    type: 'text', 
                    id: 'nomAnalytique',
                    name: 'nomAnalytique', 
                    label: 'Nom',
                    placeholder: 'Nom de l\'analytique'
                );
$form->addField(    type: 'text', 
                    id: 'analytique',
                    name: 'analytique', 
                    label: 'Analytique',
                    placeholder: '723040'
                );
$form->setSubmitButton(id: 'buttonSubmit',name: 'submit',value: 'send',text: 'Enregistrer');
$layout = new PageLayout();
$layout->addElement($table->render()); 
$layout->addElement($form->render(),4); 
echo $layout->render();


  ?>