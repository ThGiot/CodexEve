<?php

// Inclure la configuration de la base de données
require_once __DIR__ . '/../../httpd.private/config.php';
require_once __DIR__ . '/../../httpd.private/sql.php';

try {
    // Sécurité : Valider et échapper la clé fournie en GET
    if (!isset($_GET['key']) || empty($_GET['key'])) {
        http_response_code(400);
        echo json_encode(["error" => "Clé non fournie"]);
        exit;
    }

    $key = $_GET['key'];

    // Vérifier si la clé est valide (clé API définie dans config.php)
    if ($key !== PWBI_KEY) {
        http_response_code(403);
        echo json_encode(["error" => "Clé invalide ou non autorisée"]);
        exit;
    }

    // Fonction générique pour récupérer les données d'une table
    function fetch_table_data($dbh, $table_name, $columns = '*') {
        $data = [];
        $query = "SELECT $columns FROM $table_name";
        $stmt = $dbh->prepare($query);
        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $data;
    }

    // Vérifier quelle table est demandée via le paramètre GET
    if (!isset($_GET['table']) || empty($_GET['table'])) {
        http_response_code(400);
        echo json_encode(["error" => "Table non spécifiée"]);
        exit;
    }

    $table = $_GET['table'];
    $data = [];

    // Récupérer les données de la table demandée
    switch ($table) {
        case 'zones':
            $data = fetch_table_data($dbh, 'grid_zone');
            break;
        case 'poste_types':
            $data = fetch_table_data($dbh, 'grid_poste_type');
            break;
        case 'horaires':
            $data = fetch_table_data($dbh, 'grid_horaire');
            break;
        case 'horaires_periodes':
            $data = fetch_table_data($dbh, 'grid_horaire_periode', 'id, horaire_id, nom,date_debut, date_fin, DAYNAME(date_debut) AS jour_debut, TIME(date_debut) AS heure_debut, DAYNAME(date_fin) AS jour_fin, TIME(date_fin) AS heure_fin');
            break;
        case 'associations':
            $data = fetch_table_data($dbh, 'grid_assosiation');
            break;
        case 'postes':
            $postes_query = "
                SELECT grid_poste.id, grid_poste.nom, grid_poste.numero, 
                       grid_poste.horaire_id, grid_horaire.nom AS horaire_nom, 
                       grid_poste.assosiation_id, grid_assosiation.nom AS assosiation_nom, 
                       grid_poste.zone_id, grid_zone.nom AS zone_nom, 
                       grid_poste.poste_type_id, grid_poste_type.nom AS poste_type_nom
                FROM grid_poste
                LEFT JOIN grid_horaire ON grid_poste.horaire_id = grid_horaire.id
                LEFT JOIN grid_assosiation ON grid_poste.assosiation_id = grid_assosiation.id
                LEFT JOIN grid_zone ON grid_poste.zone_id = grid_zone.id
                LEFT JOIN grid_poste_type ON grid_poste.poste_type_id = grid_poste_type.id
            ";
            $stmt = $dbh->prepare($postes_query);
            $stmt->execute();
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
            break;
        default:
            http_response_code(400);
            echo json_encode(["error" => "Table non reconnue"]);
            exit;
    }

    // Envoyer les données sous forme de JSON
    header('Content-Type: application/json');
    echo json_encode($data);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["error" => "Erreur de dbhexion : " . $e->getMessage()]);
} finally {
    // Fermer la dbhexion
    $dbh = null;
}
?>
