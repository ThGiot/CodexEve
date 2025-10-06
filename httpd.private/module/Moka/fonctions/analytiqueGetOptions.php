<?php
    function analytiqueGetOptions($dbh, $client_id, $selectId ='') {
        $analytique_options = [];
        try {
            $sql = "SELECT * FROM moka_analytique WHERE client_id = :client_id";
            $stmt = $dbh->prepare($sql);
            $stmt->bindParam(':client_id', $client_id);
            $stmt->execute();
            $analytiques = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($analytiques as $analytique) {
                $analytique_options[] = [
                    'value' => $analytique['id'], 
                    'text' => $analytique['analytique'].' - '.$analytique['nom']
                ];
            }

            return $analytique_options;
        } catch (PDOException $e) {
            echo "Erreur lors de la récupération des analytiques : " . $e->getMessage();
            exit;
        }
    }
?>