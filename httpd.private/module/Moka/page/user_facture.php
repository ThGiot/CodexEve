<?php

require_once PRIVATE_PATH . '/classes/Table.php'; 
require_once PRIVATE_PATH . '/classes/PageLayout.php'; 
require_once PRIVATE_PATH . '/classes/Form.php'; 

$table = new Table(title: "Mes factures",
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
    $sql = "SELECT mf.*, YEAR(mf.date) AS annee FROM moka_facture mf JOIN moka_prestataire mp ON mp.id = mf.prestataire_id WHERE mf.client_id =:client_id AND mf.numero != 0 AND mp.user_id = :user_id AND protected = 1 ORDER BY id DESC";
    $stmt = $dbh->prepare($sql);
    $stmt -> bindParam(':client_id', $_SESSION['client_actif']);
    $stmt -> bindParam(':user_id', $_SESSION['user']['id']);
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
                ["name" => "Télécharger", "link" => "node('moka_facture_get_single', {factureId : '".$facture['id']."'})", "class" => ""]
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