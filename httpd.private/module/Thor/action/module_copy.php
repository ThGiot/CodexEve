<?php
require_once dirname(__DIR__, 3) . '/classes/RequestHandler.php';
require_once dirname(__DIR__, 3) . '/classes/ResponseHandler.php';
require_once dirname(__DIR__, 3) . '/classes/DBHandler.php';
require_once dirname(__DIR__, 3) . '/sql.php';

$requestHandler = new RequestHandler();
$responseHandler = new ResponseHandler();

// Définir les règles de validation
$rules = [
    'module_id' => ['type' => 'int'],
    'evenement_id' => ['type' => 'int']
];
// Gérer la requête avec authentification, assainissement, et validation
$data= $requestHandler->handleRequest($data, $rules);

try {
    $sql = "SELECT * FROM thor_module WHERE id = :module_id";
    $stmt = $dbh->prepare($sql);
    $stmt ->bindParam(':module_id', $data['module_id']);
    $stmt->execute();
    $module = $stmt->fetch(PDO::FETCH_ASSOC);
    
} catch (PDOException $e) {
    echo "Erreur lors de la récupération du prestataire : " . $e->getMessage();
    exit;
}

try{
    $query = "INSERT INTO thor_module (evenement_id,nom,groupe,nb_volontaire,date_debut,date_fin,heure_debut,heure_fin ) VALUES (:evenement_id,:nom,:groupe,:nb_volontaire,:date_debut,:date_fin,:heure_debut,:heure_fin )";
    $stmt = $dbh->prepare($query);
    $stmt->bindParam(':evenement_id', $data['evenement_id']);
    $stmt->bindParam(':nom', $module['nom']);
    $stmt->bindParam(':date_debut', $module['date_debut']);
    $stmt->bindParam(':heure_debut', $module['heure_debut']);
    $stmt->bindParam(':date_fin', $module['date_fin']);
    $stmt->bindParam(':heure_fin', $module['heure_fin']);
    $stmt->bindParam(':nb_volontaire', $module['nb_volontaire']);
    $stmt->bindParam(':groupe', $module['groupe']);
    $stmt->execute();
    
} catch (PDOException $e) {
    echo $responseHandler->sendResponse(true, "Erreur  : " . $e->getMessage());
    exit();
    }

    exit($responseHandler->sendResponse(true, 'Module dupliqué'));
?>