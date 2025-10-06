<?php
require_once PRIVATE_PATH . '/classes/Table.php'; 
require_once PRIVATE_PATH . '/classes/PageLayout.php'; 
require_once PRIVATE_PATH . '/classes/Toolbar.php'; 
require_once PRIVATE_PATH . '/classes/ClearInput.php'; 
require_once PRIVATE_PATH . '/classes/Form.php'; 
require_once PRIVATE_PATH . '/classes/RequestHandler.php';
require_once PRIVATE_PATH . '/classes/Modal.php';
require_once dirname(__DIR__, 1) . '/fonctions/analytiqueGetOptions.php';

$requestHandler = new RequestHandler();

// Définir les règles de validation
$rules = [
    'facture_id' => ['type' => 'int']
];
// Gérer la requête avec authentification, assainissement, et validation
$data = $requestHandler->handleRequest($data, $rules);

//Récupération des analytiques du clients 
$analytique_options = analytiqueGetOptions($dbh, $_SESSION['client_actif']);



$client_id = $_SESSION['client_actif'];
$facture_id = $data['facture_id'];

if (!isset($client_id) || !isset($facture_id)) {
    die('Client ID or Facture ID is not set');
}

// Requête SQL modifiée pour comparaison sans espace et valeur par défaut pour analytique_id
$sql = "
SELECT mf.*, 
       COALESCE(ma.id, 'unknown') AS analytique_id 
FROM moka_facture mf 
LEFT JOIN moka_analytique ma 
ON REPLACE(ma.analytique, ' ', '') = REPLACE(mf.analytique, ' ', '') 
WHERE mf.client_id = :client_id 
AND mf.id = :id
";
$stmt = $dbh->prepare($sql);
$stmt->bindParam(':client_id', $client_id);
$stmt->bindParam(':id', $facture_id);

$stmt->execute();
$facture = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$facture) {
    die('Facture not found');
}

// Assurez-vous que 'protected' est bien une clé du tableau $facture
if (isset($facture['protected'])) {
    $protected = $facture['protected'];
} else {
    die('Protected field not found in the facture');
}
$table = new Table(title: "Détails de la facture",
                  columns: ["Désignation", 
                            "Montant"
                          ], 
                  id:"RemunerationListe",);
$form = new Form();

try {
    $sql = "SELECT fact_det.* FROM moka_facture_detail fact_det JOIN moka_facture fact ON fact.id = fact_det.facture_id WHERE fact_det.facture_id = :facture_id AND fact.client_id = :client_id";
    $stmt = $dbh->prepare($sql);
    $stmt -> bindParam(':facture_id', $data['facture_id']);
    $stmt -> bindParam(':client_id', $_SESSION['client_actif']);
    $stmt->execute();
    $details = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($details as $detail) {

        if($protected == 0){
            $input_designation =  $form->renderSingleField(
                type: 'text',
                id: 'designation_' . $detail['id'],
                name: 'designation_' . $detail['id'],
                value: $detail['designation'],
                options: ['onchange' => 'node(\'moka_facture_detail_maj\',    { factureDetailId : \''.$detail['id'].'\',
                                                                                toMaj : \'designation\', 
                                                                                inputId :\'designation_' . $detail['id'].'\' 
                                                                            })']
            );

            $input_montant =  $form->renderSingleField(
                type: 'text',
                id: 'montant_' . $detail['id'],
                name: 'montant_' . $detail['id'],
                value: $detail['montant'],
                options: ['onchange' => 'node(\'moka_facture_detail_maj\',    {   factureDetailId : \''.$detail['id'].'\',
                                                                                toMaj : \'montant\', 
                                                                                inputId :\'montant_' . $detail['id'].'\' 
                                                                            })']
            );
            $action = ['name' => 'Remove', 'link' => 'node(\'moka_facture_detail_dell\',{factureDetailId : \''.$detail['id'].'\'})', 'class' => 'danger'];
        }else{
            $input_designation = $detail['designation'];
            $input_montant = $detail['montant'];
            $action =[];
        }

        
        $table->addRow(
            [   "Désignation" => $input_designation, 
                "Montant" => $input_montant
             ],
            [
                $action
            ]
        );
    }
} catch (PDOException $e) {
    echo "Erreur lors de la récupération des details : " . $e->getMessage();
    exit;
}

$onsubmit = 'event.preventDefault(); node(\'moka_facture_maj\', {factureId : \''.$facture['id'].'\'})';

$form = new Form(   id: 'facturee', 
                    name: 'facture',
                    method: 'POST', 
                    action: $onsubmit, 
                    title: 'Facture n° : '.$facture['numero']
                );


$form->addField(    type: 'text', 
                    id: 'factureNom',
                    name: 'factureNom', 
                    label: 'Nom',
                    value: $facture['nom'],
                    group: 'identification',
                    options: ['disabled' => 'disabled']
                );
$form->addField(    type: 'text', 
                    id: 'facturePrenom',
                    name: 'facturePrenom', 
                    label: 'Prénom',
                    value: $facture['prenom'],
                    group: 'identification',
                    options: ['disabled' => 'disabled']
                ); 
if($facture['protected'] == 0){             
$form->addField(    type: 'text', 
                    id: 'factureDesignation',
                    name: 'factureDesignation', 
                    label: 'Désignation',
                    value: $facture['designation'],
                    group: 'facture'
                );
}else{
   $form->addField(    type: 'text', 
                    id: 'factureDesignation',
                    name: 'factureDesignation', 
                    label: 'Désignation',
                    value: $facture['designation'],
                    group: 'facture',
                    options: ['disabled' => 'disabled']
                ); 
}
$form->addField(    type: 'text', 
                    id: 'factureMontant',
                    name: 'factureMontant', 
                    label: 'Montant',
                    value: $facture['montant'].' €',
                    options: ['disabled' => 'disabled'],
                    group: 'facture'
                );

if($protected == 0){
    $form->addField(
        type: 'select', 
        id: 'selectAnalytique',
        name: 'selectAnalytique', 
        label: 'Analytique',
        options: $analytique_options,
        selectedValue: $facture['analytique_id'],
        group: 'facture'
    );
}else{
    $form->addField(    type: 'text', 
                    id: 'factureAnalytique',
                    name: 'factureAnalytique', 
                    label: 'Analytique',
                    value: $facture['analytique'],
                    options: ['disabled' => 'disabled'],
                    group: 'facture'
                );
}
$form->setSubmitButton(id: 'buttonSubmit',name: 'submit',value: 'send',text: 'Enregistrer');


$onsubmit = 'event.preventDefault(); node(\'moka_facture_detail_add\', {factureId : \''.$facture['id'].'\'})';
$form_new_detail = new Form(   
                    id: 'new_detail', 
                    name: 'new_detail',
                    method: 'POST', 
                    action: $onsubmit, 
                    title: 'Ajouter une rémunération'
                );


$form_new_detail ->addField(    type: 'text', 
                    id: 'detailDesignationAdd',
                    name: 'detailDesignationAdd', 
                    label: 'Désignation',
                    placeholder: 'Désignation'
                );

$form_new_detail ->addField(    type: 'text', 
                    id: 'montantDesignationAdd',
                    name: 'montantDesignationAdd', 
                    label: 'Montant',
                    placeholder: '50'
                );

$form_new_detail ->setSubmitButton(id: 'buttonSubmit',name: 'submit',value: 'send',text: 'Enregistrer');


$layout = new PageLayout();
$layout->addElement($form->render()); 
$layout->addElement($table->render()); 
if($protected == 0)$layout->addElement($form_new_detail->render(),6); 

echo $layout->render();

