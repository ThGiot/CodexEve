<?php
function majFactureMontant($dbh,$facture_id){
    $sql = "SELECT SUM(montant) as montant FROM moka_facture_detail WHERE facture_id = :facture_id";
    $stmt = $dbh->prepare($sql);
    $stmt->  bindParam(':facture_id', $facture_id);
    $stmt->execute();
    $montant = $stmt->fetchColumn();
   

    $stmt = $dbh->prepare("
            UPDATE moka_facture
            SET montant = :montant 
            WHERE id = :id
        
    ");
    $stmt->execute([
        'montant' => $montant,
        'id' => $facture_id
        ]);
}

?>