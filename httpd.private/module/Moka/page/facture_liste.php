<?php

require_once PRIVATE_PATH . '/classes/Table.php'; 
require_once PRIVATE_PATH . '/classes/PageLayout.php'; 
require_once PRIVATE_PATH . '/classes/Form.php'; 

$table = new Table(title: "Liste des factures",
                  columns: ["Numéro", 
                            "Date", 
                            "Analytique",
                            "Nom",
                            "Prénom",
                            "Désignation",
                            "Montant"
                          ], 
                  id:"analytiqueListe",);

try {
    $sql = "SELECT *, YEAR(date) AS annee FROM moka_facture WHERE client_id =:client_id AND numero != 0 AND prestataire_id IS NOT NULL ORDER BY id DESC";
    $stmt = $dbh->prepare($sql);
    $stmt -> bindParam(':client_id', $_SESSION['client_actif']);
    $stmt->execute();
    $factures = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($factures as $facture) {
      
        $numero = $facture['annee'].'-'.$facture['numero'].'-MT';

        $table->addRow(
            [   "Numéro" => $numero, 
                "Date" => $facture['date'],
                "Analytique" => $facture['analytique'],
                "Nom" => $facture['nom'],
                "Prénom" => $facture['prenom'],
                "Désignation" => $facture['designation'],
                "Montant" => $facture['montant'].'€',
             ],
            [
                ["name" => "Manage", "link" => "getContent(401,{facture_id : '".$facture['id']."'})", "class" => ""],
                ["name" => "Télécharger", "link" => "node('moka_facture_get_single', {factureId : '".$facture['id']."'})", "class" => ""],
                ["name" => "Remove", "link" => "#!", "class" => "danger"],
            ],
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