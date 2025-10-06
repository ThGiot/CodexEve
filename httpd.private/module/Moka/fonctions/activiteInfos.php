<?php
function activiteInfo($dbh,$client_id){
    $sql = "SELECT ma.code, ma.analytique_id, ma.remuneration_type, ma.id AS activite_id,mana.analytique, mana.nom
        FROM   moka_activite ma        
        JOIN moka_analytique mana ON mana.id = ma.analytique_id 
        WHERE ma.client_id = :client_id";
    $stmt = $dbh->prepare($sql);
    $stmt -> bindParam(':client_id',$client_id);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $mokaActiviteInfo = [];

    // Récupérer les informations avec une boucle foreach
    foreach ($result as $row) {
        $mokaActiviteInfo[$row['code']] = [
            'AnalytiqueId' => $row['analytique_id'],
            'RemunerationType' => $row['remuneration_type'],
            'activite_id' => $row['activite_id'],
            'analytique_nom' => $row['nom'],
            'analytique' => $row['analytique'],
        ];
    }

    foreach ($mokaActiviteInfo as $code => &$info) {
        $sqlRemuneration = "SELECT grade, montant_perm, montant_garde 
                            FROM moka_activite_remuneration 
                            WHERE activite_id = :activite_id";

        $stmtRemuneration = $dbh->prepare($sqlRemuneration);
        $stmtRemuneration->bindParam(':activite_id', $info['activite_id']);
        $stmtRemuneration->execute();
        $remunerationResult = $stmtRemuneration->fetchAll(PDO::FETCH_ASSOC);

        // Ajouter les informations de remuneration au tableau existant
        foreach ($remunerationResult as $remunerationRow) {
            $info['Remuneration'][$remunerationRow['grade']] = [
                'MontantPerm' => $remunerationRow['montant_perm'],
                'MontantGarde' => $remunerationRow['montant_garde']
            ];
        }
    }
    return $mokaActiviteInfo;
}
?>