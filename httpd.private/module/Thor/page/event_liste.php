<?php

require_once PRIVATE_PATH . '/classes/Table.php'; 
require_once PRIVATE_PATH . '/classes/PageLayout.php'; 

$table = new Table(title: "Liste des evenements",
                  columns: ["Nom", 
                            "Date"
                          ], 
                  id:"evenementListe");

try {
    $sql = "SELECT * FROM thor_evenement WHERE client_id =:client_id";
    $stmt = $dbh->prepare($sql);
    $stmt -> bindParam(':client_id', $_SESSION['client_actif']);
    $stmt->execute();
    $evenements = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($evenements as $evenement) {

        $table->addRow(
            [ "Nom" => $evenement['nom'], 
              "Date" => $evenement['date']
             ],
            [
                ["name" => "View", "link" => "getContent(12,{evenement_id : '".$evenement['id']."'})", "class" => ""],
                ["name" => "Remove", "link" => "#!", "class" => "danger"],
            ]
        );
    }
} catch (PDOException $e) {
    echo "Erreur lors de la récupération des evenements : " . $e->getMessage();
    exit;
}



$layout = new PageLayout();
$layout->addElement($table->render()); 
echo $layout->render();
  ?>