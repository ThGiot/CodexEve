<?php
    function findPrestataire($dbh, $client_id,$P_ID) {
        $analytique_options = [];
        try {
            $sql = "SELECT * FROM moka_prestataire WHERE client_id = :client_id  AND P_ID = :P_ID";
            $stmt = $dbh->prepare($sql);
            $stmt->bindParam(':client_id', $client_id);
            $stmt->bindParam(':P_ID', $P_ID);
            $stmt->execute();
            $prestataire = $stmt->fetch(PDO::FETCH_ASSOC);

            if(!empty($prestataire)){
                return $prestataire;
            }else{
                return false;
            }

            return $analytique_options;
        } catch (PDOException $e) {
            echo "Erreur : " . $e->getMessage();
            exit;
        }
    }
?>