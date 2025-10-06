<?php

require_once dirname(__DIR__, 3) . '/classes/RequestHandler.php';
require_once dirname(__DIR__, 3) . '/classes/ResponseHandler.php';
require_once dirname(__DIR__, 3) . '/classes/DBHandler.php';
require_once dirname(__DIR__, 3) . '/sql.php';

$requestHandler = new RequestHandler();
$responseHandler = new ResponseHandler();
// Définir les règles de validation

$rules = [
    'poste_nom' => ['type' => 'string', 'max_length' => 200],
    'poste_zone' => ['type' => 'string', 'max_length' => 200],
    'poste_association' => ['type' => 'string', 'max_length' => 200],
    'poste_type' => ['type' => 'string', 'max_length' => 200],
    'horaire_id' => ['type' => 'string', 'max_length' => 200]
];
// Gérer la requête avec authentification, assainissement, et validation
$form_field =[];
foreach($data['formData'] as $field){
    $form_field[$field['name']] = $field['value'];
   
}
$form_field = $requestHandler->handleRequest($form_field, $rules);
$form_field['client_id'] =  $_SESSION['client_actif'];


$data = $form_field;
//Récupération du prochain numéro

    // Recherche du premier numéro libre pour le même `poste_type_id` et `client_id`
    $query = "
    SELECT MIN(missing.numero) AS next_numero
    FROM (
        SELECT t1.numero + 1 AS numero
        FROM grid_poste t1
        LEFT JOIN grid_poste t2 
        ON t1.numero + 1 = t2.numero 
        AND t1.poste_type_id = t2.poste_type_id
        AND t1.client_id = t2.client_id
        WHERE t1.poste_type_id = :poste_type_1
        AND t1.client_id = :client_id_1
        AND t2.numero IS NULL
        UNION 
        SELECT 1 AS numero
    ) AS missing
    WHERE NOT EXISTS (
        SELECT 1 FROM grid_poste gp 
        WHERE gp.numero = missing.numero
        AND gp.poste_type_id = :poste_type_2
        AND gp.client_id = :client_id_2
    )
    LIMIT 1;
    ";

    $stmt = $dbh->prepare($query);
    $stmt->bindParam(':client_id_1', $_SESSION['client_actif'], PDO::PARAM_INT);
    $stmt->bindParam(':client_id_2', $_SESSION['client_actif'], PDO::PARAM_INT);
    $stmt->bindParam(':poste_type_1', $data['poste_type'], PDO::PARAM_INT);
    $stmt->bindParam(':poste_type_2', $data['poste_type'], PDO::PARAM_INT);
    $stmt->execute();

    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result && isset($result['next_numero'])) {
        $next_numero = $result['next_numero']; // Assigne la bonne valeur
    } else {
        exit('Erreur : Impossible de déterminer le prochain numéro.');
    }




$query = "INSERT INTO grid_poste SET    nom =:nom, 
                                        numero =:numero,
                                        horaire_id =:horaire_id,
                                        association_id =:association_id,
                                        zone_id = :zone_id,
                                        poste_type_id =:poste_type_id,
                                        client_id =:client_id";
$stmt = $dbh->prepare($query);
$stmt->bindParam(':nom', $data['poste_nom']);
$stmt->bindParam(':numero', $next_numero);
$stmt->bindParam(':horaire_id', $data['horaire_id']);
$stmt->bindParam(':zone_id', $data['poste_zone']);
$stmt->bindParam(':association_id', $data['poste_association']);
$stmt->bindParam(':poste_type_id', $data['poste_type']);
$stmt->bindParam(':client_id', $_SESSION['client_actif']);
$stmt->execute();
exit($responseHandler->sendResponse(true,'Le poste à été crée avec le numéro :'.$next_numero));

?>