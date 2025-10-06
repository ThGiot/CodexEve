<?php

require_once PRIVATE_PATH . '/classes/Table.php'; 
require_once PRIVATE_PATH . '/classes/PageLayout.php'; 

$table = new Table(title: "Liste des prestataires",
                  columns: ["Nom", 
                            "Prénom",
                            "Email",
                            "Téléphone"
                          ], 
                  id:"clientListe");

try {
    $sql = "SELECT * FROM moka_prestataire WHERE client_id =:client_id";
    $stmt = $dbh->prepare($sql);
    $stmt -> bindParam(':client_id', $_SESSION['client_actif']);
    $stmt->execute();
    $prestataires = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($prestataires as $prestataire) {

        $table->addRow(
            [ "Nom" => $prestataire['nom'], 
              "Prénom" => $prestataire['prenom'],
              "Email" => $prestataire['email'],
              "Téléphone" => $prestataire['telephone']
             ],
            [
                ["name" => "View", "link" => "getContent(101,{prestataire_id : '".$prestataire['id']."'})", "class" => ""],
                ["name" => "Remove", "link" => "#!", "class" => "danger"],
            ]
        );
    }
} catch (PDOException $e) {
    echo "Erreur lors de la récupération des clients : " . $e->getMessage();
    exit;
}



$layout = new PageLayout();
$layout->addElement($table->render()); 
echo $layout->render();
  ?>