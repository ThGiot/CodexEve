<?php
require_once dirname(__DIR__, 3) . '/config.php';
require_once dirname(__DIR__, 3) . '/sql.php';


//header('Content-Type: application/json');

try {
    // Requête SQL complète avec les périodes
    $sql = "SELECT gp.id AS poste_id,
                   gp.nom, 
                   gp.numero, 
                   gh.nom AS horaire, 
                   gh.personne_nb AS nombre,
                   ga.nom AS association, 
                   gpt.nom AS poste_type,
                   gz.nom AS zone_nom,
                   ghp.date_debut, 
                   ghp.date_fin
            FROM grid_poste gp
            LEFT JOIN grid_horaire gh ON gp.horaire_id = gh.id 
            LEFT JOIN grid_association ga ON ga.id = gp.association_id
            LEFT JOIN grid_poste_type gpt ON gpt.id = gp.poste_type_id
            LEFT JOIN grid_zone gz ON gz.id = gp.zone_id
            LEFT JOIN grid_horaire_periode ghp ON ghp.horaire_id = gh.id
            WHERE gp.client_id = :client_id
           ORDER BY gpt.nom,gp.numero";

    $stmt = $dbh->prepare($sql);
    $stmt->bindParam(':client_id', $_SESSION['client_actif']);
    $stmt->execute();
    $postes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $data = [];
    $daysMap = ["Thu" => "Jeu", "Fri" => "Ven", "Sat" => "Sam", "Sun" => "Dim", "Mon" => "Lun"];

    foreach ($postes as $poste) {
        $posteId = $poste['poste_id'];
        $poste_type_ab = substr($poste['poste_type'], 0, 1);

        // Initialisation de la structure du poste s'il n'existe pas encore
        if (!isset($data[$posteId])) {
            $data[$posteId] = [
                "numero"      => " $poste_type_ab" . $poste['numero'],
                "poste_id"    => $poste['poste_id'],
                "poste"       => $poste['nom'],
                "zone"        => $poste['zone_nom'] ?? '',
                "association" => $poste['association'] ?? '',
                "type"        => $poste['poste_type'] ?? '',
                "periode"     => $poste['horaire'] ?? '',
                "heures"      => []
            ];
        }

        // Inclusion des périodes horaires
        if (!empty($poste['date_debut']) && !empty($poste['date_fin'])) {
            $start = new DateTime($poste['date_debut']);
            $end = new DateTime($poste['date_fin']);

            // Exclure l'heure de fin
            while ($start < $end) {
                $day = $daysMap[$start->format('D')];  // Conversion du jour en français
                $hour = $start->format('H') . "h";     // Format de l'heure
                $key = "$day-$hour";

                // Incrémenter les présences sans écrasement
                if (!isset($data[$posteId]['heures'][$key])) {
                    $data[$posteId]['heures'][$key] = 1;
                } else {
                    $data[$posteId]['heures'][$key] += 1;
                }

                $start->modify('+1 hour');
            }
        }
    }

    // Retourner les données formatées en JSON
    echo json_encode([
        "success" => true,
        "data"    => array_values($data)
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

} catch (PDOException $e) {
    echo json_encode([
        "success" => false,
        "message" => "Erreur lors de la récupération des postes : " . $e->getMessage()
    ]);
    exit;
}
?>
