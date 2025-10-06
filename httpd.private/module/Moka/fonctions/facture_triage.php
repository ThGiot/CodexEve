<?php
function facture_triage($dbh, $date_debut, $date_fin){
    try {
        // Préparation de la requête pour récupérer num_start et num_fin
        $stmt = $dbh->prepare("SELECT MIN(numero) AS num_start, MAX(numero) AS num_fin FROM `moka_facture` WHERE  date >= ? AND date <= ?");
        $stmt->execute([$date_debut, $date_fin]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
    
        $num_fin = $row['num_fin'];    
        $num_start = $row['num_start'];
        $num = $num_start;
        $i = 0;
    
        // Requête pour récupérer les factures dans l'intervalle num_start et num_fin
        $requette = "SELECT numero, id, date, analytique FROM moka_facture WHERE numero >= ? AND numero <= ? ORDER BY date, analytique, nom, prenom";
        $stmt = $dbh->prepare($requette);
        $stmt->execute([$num_start, $num_fin]);
    
        // Mise à jour des factures
        while ($donnees = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $updateStmt = $dbh->prepare('UPDATE moka_facture SET numero = ? WHERE id = ? AND protected = 0');
            $updateStmt->execute([$num, $donnees['id']]);
    
            $num++;
            $i++;
        }
    
    } catch (PDOException $e) {
        die("Erreur PDO : " . $e->getMessage());
    }
    
}

?>