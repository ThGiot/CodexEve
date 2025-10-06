<?php

require_once PRIVATE_PATH . '/classes/PageLayout.php'; 
require_once PRIVATE_PATH . '/classes/Form.php'; 
require_once PRIVATE_PATH . '/classes/BootstrapCard.php'; 
require_once PRIVATE_PATH . '/classes/Modal.php'; 
require_once dirname(__DIR__, 1) . '/fonctions/getLastPDFsForClient.php';

//Nombre de facture en attente 

$sql = "SELECT COUNT(*) AS total 
        FROM moka_facture 
        WHERE client_id = :client_id 
        AND (prestataire_id IS NULL)";

$stmt = $dbh->prepare($sql);
$stmt->bindParam(':client_id', $_SESSION['client_actif']);
$stmt->execute();
$result = $stmt->fetch(PDO::FETCH_ASSOC);

$total_factures_attente = $result['total'];
$class_facture = ($total_factures_attente > 0) ? 'alert alert-danger' : '';



$onsubmit = 'event.preventDefault(); node(\'moka_facture_generate\', {})';

$form = new Form(   id: 'factureGenerate', 
                    name: 'factureGenerate',
                    method: 'POST', 
                    action: $onsubmit, 
                    title: 'Generer les derniers documents'
                );


$form->addField(    type: 'date', 
                    id: 'dateDebut',
                    name: 'dateDebut', 
                    label: 'Date de début',
                    placeholder: ''
                );
$form->addField(    type: 'date', 
                    id: 'dateFin',
                    name: 'dateFin', 
                    label: 'Date de fin',
                    placeholder: ''
                );

$form->setSubmitButton(id: 'buttonSubmit',name: 'submit',value: 'send',text: 'Générer');

$onsubmit2 = 'event.preventDefault(); node(\'moka_facture_send\', {})';

$form2 = new Form(   id: 'factureSend', 
                    name: 'factureSend',
                    method: 'POST', 
                    action: $onsubmit2, 
                    title: 'Envoyer les facture à la comptabilité'
                );



$form2 -> addHtml('<div style="cursor: pointer;" onclick="getContent(\'44\')" class="'.$class_facture.'">'.$total_factures_attente.' Facture(s) en attente </br></div>');
$form2->setSubmitButton(id: 'buttonSubmitSend',name: 'submit',value: 'send',text: 'Envoyer');

//Modal pour l'envois des factures

$modal = new Modal(
    id: "modalSend", 
    title: "Envois des factures", 
    body: '<b>Vous êtes sur le point d\'envoyer les factures à la comptabilité.</b>',
    headerClass: "warning",
    okayButtonClass: "primary",
    okayButtonText : "Envoyer",
    cancelButtonClass: "outline-secondary",
    showOkayButton: true,
    showButton : false
);
echo $modal->render();


//Recherche des derniers documents 

$directoryPath = dirname(__DIR__, 1) . '/facture/';
$clientActif = $_SESSION['client_actif']; 
$latestPDFs = getLastPDFsForClient($clientActif, $directoryPath);

$content = '<div class="row">'; // Commencer une nouvelle rangée

foreach ($latestPDFs as $pdfFilePath) {
    $filename = basename($pdfFilePath);
    $parts = explode('_', $filename);

    
        $analytique = $parts[1]; // L'analytique est la seconde partie
        if (count($parts) >= 3) {
        // Créer une nouvelle carte pour chaque fichier PDF
        $card = new BootstrapCard();
        $card->setTitle('<a style="text-decoration: none; color: inherit;" href="#" onclick="node(\'moka_facture_get\',{fichier : \''. $filename.'\'})"><center>'.$analytique.'</center>'); // Définir le titre de la carte comme le nom du fichier
        $card->setBody('<center><span class="text-900 fs-3 far fa-file-pdf"></span></center>'.$parts[2].'-'.$parts[3].'-'.$parts[4].':'.$parts[5].'</a>'); // Définir le corps de la carte avec l'icône PDF

        // Ajouter la carte au contenu
        $content .= '<div class="col-md-4 mb-3">'; // Colonne pour chaque PDF
        $content .= $card->render();
        $content .= '</div>'; // Fin de col-md-4
    }
}

$content .= '</div>'; // Fin de row

$layout = new PageLayout();
$card = new BootstrapCard();
$card->setHeader("<center><b>Derniers Documents</b></center>");
$card->setBody($content);
$c = $form->render().' '.$form2->render();
$layout->addElement($c,4,'groupe1'); 
$layout->addElement($card->render(),4,'groupe1'); 
echo $layout->render();
?>
