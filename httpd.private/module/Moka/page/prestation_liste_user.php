<?php

require_once PRIVATE_PATH . '/classes/Table.php'; 
require_once PRIVATE_PATH . '/classes/PageLayout.php'; 
require_once PRIVATE_PATH . '/classes/Modal.php'; 


$table = new Table(title: "Mes heures supplémentaires",
                  columns: ["Nom", 
                            "Prénom",
                           
                            "Prestation",
                            "Nombre HS",
                            "Commentaire",
                            "Statut"
                          ], 
                  id:"prestationListe");

try {
    $sql = "SELECT mpe.id AS prestation_id, mpe.*, mp.*    FROM moka_prestation_ebrigade mpe
                        JOIN moka_prestataire mp ON mpe.P_ID = mp.p_id
                        WHERE mpe.client_id =:client_id
                            AND mp.user_id = :user_id
                        ORDER BY mpe.date DESC";
    $stmt = $dbh->prepare($sql);
    $stmt -> bindParam(':client_id', $_SESSION['client_actif']);
    $stmt -> bindParam(':user_id', $_SESSION['user']['id']);
    $stmt->execute();
    $prestations = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($prestations as $prestation) {

        $status = match ($prestation['statut']) {
            0 =>  'En attente',
            1 => 'Accepté',
            2 => 'Refusé',
            3 => 'Accepté',
            4 => 'Accepté',
            default => 'Erreur, statut inconnu !',
        };

        $table->addRow(
            [ "Nom" => $prestation['nom'], 
              "Prénom" => $prestation['prenom'],
              
              "Prestation" => $prestation['E_LIBELLE'].' le '.$prestation['date'],
              "Nombre HS" => $prestation['heure'],
              "Commentaire" => $prestation['commentaire'],
              "Statut" => $status
             ],
            [
            ],
            
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