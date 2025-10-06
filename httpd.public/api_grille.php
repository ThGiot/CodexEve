<?php
// grille.php - Endpoint d'API dédié à la grille de planification

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once __DIR__ . '/../httpd.private/config.php';
require_once PRIVATE_PATH . '/sql.php';
require_once PRIVATE_PATH . '/classes/Logger.php';

try {
    // Authentification
    $providedKey = $_GET['api_key'] ?? '';
    if ($providedKey !== API_KEY) {
        http_response_code(401);
        throw new Exception("Unauthorized: Invalid API key.");
    }

    // Client requis
    
    $clientId = 6;

    // Connexion et logger

    $logger = new Logger($dbh);

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
            ORDER BY gpt.nom, gp.numero";

    $stmt = $dbh->prepare($sql);
    $stmt->bindParam(':client_id', $clientId, PDO::PARAM_INT);
    $stmt->execute();
    $postes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $data = [];
    $daysMap = ["Thu" => "Jeu", "Fri" => "Ven", "Sat" => "Sam", "Sun" => "Dim", "Mon" => "Lun"];

    foreach ($postes as $poste) {
        $posteId = $poste['poste_id'];
        $poste_type_ab = substr($poste['poste_type'], 0, 1);

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

        if (!empty($poste['date_debut']) && !empty($poste['date_fin'])) {
            $start = new DateTime($poste['date_debut']);
            $end = new DateTime($poste['date_fin']);

            while ($start < $end) {
                $day = $daysMap[$start->format('D')] ?? $start->format('D');
                $hour = $start->format('H') . "h";
                $key = "$day-$hour";

                $data[$posteId]['heures'][$key] = ($data[$posteId]['heures'][$key] ?? 0) + 1;
                $start->modify('+1 hour');
            }
        }
    }

    $logger->log(
        'action',
        'Accès grille planification',
        basename(__FILE__),
        Logger::getUserIP(),
        null,
        $clientId,
        json_encode(["resultCount" => count($data)])
    );

    echo json_encode(["success" => true, "data" => array_values($data)], JSON_UNESCAPED_UNICODE);

} catch (Exception $e) {
    if (isset($logger)) {
        $logger->log('error', $e->getMessage(), basename(__FILE__), Logger::getUserIP(), null, null, json_encode($_GET));
    }
    http_response_code(400);
    echo json_encode(["success" => false, "message" => $e->getMessage()]);
    exit;
}
?>
