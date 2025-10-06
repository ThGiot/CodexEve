<?php
function factureInsert($dbh,$designation, $data,$analytique,$montant, $client_id,$p_id) {
    // On recherche le prochain numéro de facture pour le client
    $sql = "SELECT MAX(numero) AS numero_max FROM moka_facture WHERE client_id = :client_id";
    $stmt = $dbh->prepare($sql);
    $stmt->bindParam(':client_id', $client_id, PDO::PARAM_INT);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result && $result['numero_max'] !== null) {
        $numero = $result['numero_max'] + 1;
    } else {
        $numero = 1;
    }
    if(empty($data['id'])) $numero = 0;
    // Récupération du résultat
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    try {

        $sql = "SELECT * FROM moka_prestataire WHERE id = :id AND client_id= :client_id";
        $stmt = $dbh->prepare($sql);
        $stmt ->bindParam(':id', $data['id']);
        $stmt ->bindParam(':client_id', $_SESSION['client_actif']);
        $stmt->execute();
        $prestataire = $stmt->fetch(PDO::FETCH_ASSOC);
        if(!empty($prestataire['societe'])){
            $data['nom'] =$prestataire['societe'];
            $data['prenom']='';
        }



        $sql = "INSERT INTO moka_facture (designation, montant, numero,analytique, nom, prenom, niss,telephone, bce, compte, adresse,prestataire_id, client_id, p_id) 
                values(:designation,:montant,:numero, :analytique,:nom,:prenom,:niss,:telephone,:bce,:compte, :adresse,:prestataire_id,:client_id,:p_id)";
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(':designation',$designation);
        $stmt->bindParam(':montant', $montant);
        $stmt->bindParam(':numero', $numero);
        $stmt->bindParam(':analytique', $analytique);
        $stmt->bindParam(':nom', $data['nom']);
        $stmt->bindParam(':prenom', $data['prenom']);
        $stmt->bindParam(':niss', $data['niss']);
        $stmt->bindParam(':telephone', $data['telephone']);
        $stmt->bindParam(':bce', $data['bce']);
        $stmt->bindParam(':compte', $data['compte']);
        $stmt->bindParam(':adresse', $data['adresse']);
        $stmt->bindParam(':prestataire_id', $data['id']);
        $stmt->bindParam(':client_id', $client_id);
        $stmt->bindParam(':p_id', $p_id);
        $stmt->execute();
        return $last_id = $dbh->lastInsertId();
    } catch (PDOException $e) {
      return $e->getMessage();
        exit();
    }
}
?>