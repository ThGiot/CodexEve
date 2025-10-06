<?php

require_once PRIVATE_PATH . '/classes/Table.php'; 
require_once PRIVATE_PATH . '/classes/PageLayout.php'; 
require_once PRIVATE_PATH . '/classes/Form.php'; 
require_once PRIVATE_PATH . '/classes/Modal.php'; 
require_once dirname(__DIR__, 1) . '/fonctions/analytiqueGetOptions.php';

$modal = new Modal(
    id: "CorDell", 
    title: "Suppresion correction", 
    body: '<b>Vous êtes sur le point de supprimer une correction !</b>',
    headerClass: "danger",
    okayButtonClass: "primary",
    okayButtonText : "Supprimer",
    cancelButtonClass: "outline-secondary",
    showOkayButton: true,
    showButton : false
);
echo $modal->render();


$table = new Table(title: "Corrections en attentes",
                  columns: ["Nom", 
                            "Prénom",
                            "Désignation",
                            "Montant",
                            "Analytique"
                          ], 
                  id:"factureListe",);

try {
    $sql = "SELECT a.nom AS anlytique_nom, mfc.id AS correction_id ,mfc.*,p.*,a.* FROM moka_facture_correction mfc 
            JOIN moka_prestataire p ON p.id = mfc.prestataire_id 
            JOIN moka_analytique a ON a.id = mfc.analytique_id
            WHERE mfc.client_id = :client_id";
    $stmt = $dbh->prepare($sql);
    $stmt -> bindParam(':client_id', $_SESSION['client_actif']);
    $stmt->execute();
    $factures = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($factures as $facture) {

        $table->addRow(
            [   "Nom" => $facture['nom'], 
                "Prénom" => $facture['prenom'],
                "Désignation" => $facture['designation'],
                "Montant" => $facture['montant'],
                "Analytique" => $facture['analytique'].' - '.$facture['anlytique_nom']
             ],
            [
                
                ["name" => "Remove", "link" => 'node(\'moka_facture_correction_dell\',{correctionId : \''.$facture['correction_id'].'\'})', "class" => "danger"],
            ]
        );
    }
} catch (PDOException $e) {
    echo "Erreur lors de la récupération des factures : " . $e->getMessage();
    exit;
}
//Récupération des analytiques du clients 
$analytique_options = analytiqueGetOptions($dbh, $_SESSION['client_actif']);

$onsubmit = 'event.preventDefault(); node(\'moka_addfacture_correction\', {})';

$form = new Form(   id: 'addfactureCorrection', 
                    name: 'addfactureCorrection',
                    method: 'POST', 
                    action: $onsubmit, 
                    title: 'Ajouter une correction'
                );


$sql = "SELECT * FROM moka_prestataire WHERE client_id = :client_id ORDER BY nom, prenom";
$stmt = $dbh->prepare($sql);
$stmt->bindParam(':client_id', $_SESSION['client_actif']);
$stmt->execute();
$prestataires = $stmt->fetchAll(PDO::FETCH_ASSOC);
$optionsSelect=[];
foreach ($prestataires as $prestataire) {
    $optionsSelect[] = [
        'value' => $prestataire['id'], 
        'text' => $prestataire['nom'].' - '.$prestataire['prenom']
    ];
}
$form->addField(
    type: 'select', 
    id: 'selectPrestataire',
    name: 'selectOption', 
    label: 'Prestataire',
    options: $optionsSelect,
   
);
$form->addField(
    type: 'select', 
    id: 'selectAnalytique',
    name: 'selectAnalytique', 
    label: 'Analytique',
    options: $analytique_options
);
$form->addField(
    type: 'number', 
    id: 'montant',
    name: 'montant', 
    label: 'Montant',
    placeholder:'- 100'
);
$form->addField(
    type: 'text', 
    id: 'designation',
    name: 'designation', 
    label: 'Désignation',
    placeholder:'Désignation'
);
$form->setSubmitButton(id: 'buttonSubmit',name: 'submit',value: 'send',text: 'Enregistrer');
$layout = new PageLayout();
$layout->addElement($table->render()); 
$layout->addElement($form->render(),8); 
echo $layout->render();


  ?>