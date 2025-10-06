<?php

require_once dirname(__DIR__, 3) . '/classes/RequestHandler.php';
require_once dirname(__DIR__, 3) . '/classes/ResponseHandler.php';
require_once dirname(__DIR__, 3) . '/classes/DBHandler.php';
require_once dirname(__DIR__, 3) . '/sql.php';

$requestHandler = new RequestHandler();
$responseHandler = new ResponseHandler();

// Authentifier et récupérer l'association_id
$rules = ['association_id' => ['type' => 'int']];
$data = $requestHandler->handleRequest($data, $rules);
$associationId = (int) $data['association_id'];

$form_field = [];
foreach ($data['form_field'] as $field) {
    $form_field[$field['name']] = $field['value'];
}

$db = $dbh;

// Parcourir chaque champ horaire_X
foreach ($form_field as $key => $value) {
    if (strpos($key, 'horaire_') === 0) {
        $horaireId = (int) str_replace('horaire_', '', $key);
        $nb = (int) $value;

        // INSERT ... ON DUPLICATE KEY UPDATE
        $sql = "
            INSERT INTO grid_association_dispo (association_id, horaire_id, nb)
            VALUES (:association_id, :horaire_id, :nb)
            ON DUPLICATE KEY UPDATE nb = :nb_update
        ";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':association_id', $associationId, PDO::PARAM_INT);
        $stmt->bindParam(':horaire_id', $horaireId, PDO::PARAM_INT);
        $stmt->bindParam(':nb', $nb, PDO::PARAM_INT);
        $stmt->bindParam(':nb_update', $nb, PDO::PARAM_INT);
        $stmt->execute();
    }
}

echo $responseHandler->sendResponse(true, 'Disponibilités enregistrées avec succès.');

?>
