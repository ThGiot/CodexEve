<?php

require_once PRIVATE_PATH . '/classes/Table.php'; 
require_once PRIVATE_PATH . '/classes/PageLayout.php'; 
require_once PRIVATE_PATH . '/classes/Form.php'; 
require_once PRIVATE_PATH . '/classes/Modal.php'; 

$form = new Form("monFormulaire", "monFormulaire", "post", "traitement.php", "Mon Formulaire");
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


// Générer un champ select
$select= $form->renderSingleField('select', 'prestataireSelect', 'nameSelect', '', '', $optionsSelect);
$modal = new Modal(
    id: "factureModal", 
    title: "Attribuer une facture", 
    body: 'Prestataire :</br>'.$select,
    headerClass: "",
    okayButtonClass: "primary",
    okayButtonText : "Je confirme",
    cancelButtonClass: "outline-secondary",
    showOkayButton: true,
    showButton : false
);
$modal -> setOkayButtonOnClick("alert('ok');");
echo $modal->render();




$table = new Table(title: "Liste des factures en attentes",
                  columns: [
                            "Date", 
                            "Analytique",
                            "Téléphone",
                            "Nom",
                            "Prénom",
                            "Désignation",
                            "Montant"
                          ], 
                  id:"analytiqueListe",);

try {
    $sql = "SELECT YEAR(date) AS annee,moka_facture.* FROM moka_facture WHERE client_id =:client_id AND prestataire_id IS NULL ORDER BY id DESC";
    $stmt = $dbh->prepare($sql);
    $stmt -> bindParam(':client_id', $_SESSION['client_actif']);
    $stmt->execute();
    $factures = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($factures as $facture) {
      
        $numero = $facture['annee'].'-'.$facture['numero'].'-MT';

        $table->addRow(
            [   
                "Date" => $facture['date'],
                "Analytique" => $facture['analytique'],
                "Téléphone" => $facture['telephone'],
                "Nom" => $facture['nom'],
                "Prénom" => $facture['prenom'],
                "Désignation" => $facture['designation'],
                "Montant" => $facture['montant'].'€',
             ],
            [
                ["name" => "Attribuer", "link" => 'node(\'moka_facture_att_attribuer\',{factureId : \''.$facture['id'].'\'})', "class" => "success"]
            ]
        );
    }
} catch (PDOException $e) {
    echo "Erreur lors de la récupération des analytiques : " . $e->getMessage();
    exit;
}



$layout = new PageLayout();
$layout->addElement($table->render()); 
echo $layout->render();


  ?>