<?php

require_once PRIVATE_PATH . '/classes/Table.php'; 
require_once PRIVATE_PATH . '/classes/PageLayout.php'; 
require_once PRIVATE_PATH . '/classes/Form.php'; 
require_once PRIVATE_PATH . '/classes/Modal.php'; 
require_once dirname(__DIR__, 1) . '/fonctions/analytiqueGetOptions.php';


//Récupération des analytiques du clients 
$analytique_options = analytiqueGetOptions($dbh, $_SESSION['client_actif']);

$onsubmit = 'event.preventDefault(); node(\'moka_addfacture\', {})';

$form = new Form(   id: 'addfactureCorrection', 
                    name: 'addfactureCorrection',
                    method: 'POST', 
                    action: $onsubmit, 
                    title: 'Créer une facture'
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
    type: 'text', 
    id: 'designationFact',
    name: 'designation', 
    label: 'Désignation de la facture',
    placeholder:'Désignation'
);
$form->addField(
    type: 'text', 
    id: 'designationDetail',
    name: 'designation', 
    label: 'Désignation de la prestation',
    placeholder:'Désignation'
);
$form->addField(
    type: 'number', 
    id: 'montant',
    name: 'montant', 
    label: 'Montant',
    placeholder:'100'
);
$form->setSubmitButton(id: 'buttonSubmit',name: 'submit',value: 'send',text: 'Enregistrer');
$layout = new PageLayout();
$layout->addElement($form->render()); 
echo $layout->render();


  ?>