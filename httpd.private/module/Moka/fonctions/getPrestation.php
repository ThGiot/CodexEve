<?php
    function getPrestation($dbh, $client_id, $E_CODE, $P_ID) {
        $analytique_options = [];
        try {
            $sql = "SELECT * FROM moka_prestation_ebrigade WHERE client_id = :client_id AND E_CODE = :E_CODE AND P_ID = :P_ID";
            $stmt = $dbh->prepare($sql);
            $stmt->bindParam(':client_id', $client_id);
            $stmt->bindParam(':E_CODE', $E_CODE);
            $stmt->bindParam(':P_ID', $P_ID);
            $stmt->execute();
            $prestation = $stmt->fetch(PDO::FETCH_ASSOC);

            if(!empty($prestation)){
                if($prestation['statut'] == 1){
                    $query = "UPDATE moka_prestation_ebrigade SET statut = 3 WHERE id = :prestation_id AND client_id = :client_id";
                    $stmt = $dbh->prepare($query);
                    $stmt->bindParam(':prestation_id', $prestation['id']);
                    $stmt->bindParam(':client_id', $_SESSION['client_actif']);
                    $stmt->execute();
                    return $prestation['heure'];
                }elseif($prestation['statut'] == 4){
                    $query = "UPDATE moka_prestation_ebrigade SET statut = 3 WHERE id = :prestation_id AND client_id = :client_id";
                    $stmt = $dbh->prepare($query);
                    $stmt->bindParam(':prestation_id', $prestation['id']);
                    $stmt->bindParam(':client_id', $_SESSION['client_actif']);
                    $stmt->execute();
                    return 0;
                }

            }else{
                return 0;
            }

           
        } catch (PDOException $e) {
            echo "Erreur lors de la récupération des prestations : " . $e->getMessage();
            exit;
        }
    }
?>