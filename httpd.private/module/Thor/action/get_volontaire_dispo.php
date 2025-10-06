<?php

require_once dirname(__DIR__, 3) . '/classes/RequestHandler.php';
require_once dirname(__DIR__, 3) . '/classes/ResponseHandler.php';
require_once dirname(__DIR__, 3) . '/sql.php';

$requestHandler = new RequestHandler();
$responseHandler = new ResponseHandler();

// Définir les règles de validation
$rules = [
    'evenement_id' => ['type' => 'int'],
    'module_id' => ['type' => 'int'],
];
// Gérer la requête avec authentification, assainissement, et validation
$data = $requestHandler->handleRequest($data, $rules);






try{
    $query = "SELECT * FROM thor_module WHERE id = :id";
    $stmt = $dbh->prepare($query);
    $stmt->bindParam(':id', $data['module_id']);
    $stmt->execute();
    $module = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo $responseHandler->sendResponse(true, "Erreur  : " . $e->getMessage());
    exit();
    }




$dateDebutSpecifie = $module['date_debut'].' '.$module['heure_debut']; 
$dateFinSpecifie = $module['date_fin'].' '.$module['heure_fin'];

$sql = "SELECT thor_disponibilites.volontaire_id, thor_volontaire.nom, thor_volontaire.centre_secours,thor_volontaire.prenom, thor_disponibilites.debut, thor_disponibilites.fin, thor_disponibilites.remarque 
        FROM thor_disponibilites
        JOIN thor_volontaire ON thor_volontaire.id = thor_disponibilites.volontaire_id
        WHERE (debut >= :dateDebutSpecifie1 AND fin <= :dateFinSpecifie1)
           OR (debut <= :dateDebutSpecifie2 AND fin >= :dateFinSpecifie2)
        ORDER BY  thor_volontaire.centre_secours, thor_volontaire.nom";

// Préparation de la requête avec PDO
$stmt = $dbh->prepare($sql);

// Liaison des paramètres
$stmt->bindParam(':dateDebutSpecifie1', $dateDebutSpecifie, PDO::PARAM_STR);
$stmt->bindParam(':dateFinSpecifie1', $dateFinSpecifie, PDO::PARAM_STR);
$stmt->bindParam(':dateDebutSpecifie2', $dateDebutSpecifie, PDO::PARAM_STR);
$stmt->bindParam(':dateFinSpecifie2', $dateFinSpecifie, PDO::PARAM_STR);

// Exécution de la requête
$stmt->execute();


// Récupération des résultats
$resultats = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Affichage des résultats
// Tableau pour stocker les résultats
$volontaires = [];

foreach ($resultats as $row) {
    $sql = "SELECT  * FROM thor_evenement_qualification
            WHERE evenement_id = :evenement_id AND volontaire_id = :volontaire_id";
    // Préparation de la requête avec PDO
    $stmt = $dbh->prepare($sql);
    $stmt->bindParam(':evenement_id', $data['evenement_id']);
    $stmt->bindParam(':volontaire_id', $row['volontaire_id']);
    $stmt->execute();
    $qualification = $stmt->fetch(PDO::FETCH_ASSOC);
    if(!empty($qualification['qualification'])) $qualification['qualification'] = '['.$qualification['qualification'].']';


    $volontaires[] = [
        'id' => $row['volontaire_id'],
        'nom' => $row['nom'],
        'prenom' => $row['prenom'],
        'debut' => $row['debut'],
        'fin' => $row['fin'],
        'centreSecours' => $row['centre_secours'],
        'qualification' => $qualification['qualification'],
    ];
}

// Conversion du tableau en JSON
$json = json_encode($volontaires );
header('Content-Type: application/json');
// Affichage du JSON
echo $json;
?>