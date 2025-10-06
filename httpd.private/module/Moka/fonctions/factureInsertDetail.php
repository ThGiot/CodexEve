<?php
function factureInsertDetail($dbh,$facture_id,$designation,$montant){

    try {
        $sql = "INSERT INTO moka_facture_detail (facture_id, designation, montant) 
                values(:facture_id, :designation,:montant)";
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(':facture_id',$facture_id);
        $stmt->bindParam(':designation',$designation);
        $stmt->bindParam(':montant', $montant);
        $stmt->execute();
        return true;
    } catch (PDOException $e) {
      return $e->getMessage();
        exit;
    }    

}
?>