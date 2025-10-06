<?php
require_once dirname(__DIR__, 3) . '/config.php';
require_once dirname(__DIR__, 3) . '/sql.php';
require_once dirname(__DIR__, 3) . '/classes/ResponseHandler.php';
require_once dirname(__DIR__, 3) . '/classes/RequestHandler.php';
require_once dirname(__DIR__, 3) . '/classes/DBHandler.php';

$responseHandler = new ResponseHandler();
$requestHandler = new RequestHandler();

$module_id = 2; // ID MODULE CONNECT
$requestHandler-> verifyModulePermission($module_id,$dbh);

// Définir les règles de validation
$rules = [
    'sms_id' => ['type' => 'int']
];
// Gérer la requête avec authentification, assainissement, et validation
$data = $requestHandler->handleRequest($data, $rules); 

try{
    $sql='UPDATE connect_messages set status = "canceled"  WHERE id = :id';
    $stmt = $dbh->prepare($sql);
    $stmt->bindParam(':id', $data['sms_id']);
    $stmt->execute();

    $sql='UPDATE connect_recipients set status = "canceled"  WHERE message_id = :id';
    $stmt = $dbh->prepare($sql);
    $stmt->bindParam(':id', $data['sms_id']);
    $stmt->execute();

    $sql='INSERT INTO connect_message_updates (message_id, updated_by,action) VALUES (:id, :user_id, "anulation de l\'envois")';
    $stmt = $dbh->prepare($sql);
    $stmt->bindParam(':id', $data['sms_id']);
    $stmt->bindParam(':user_id', $_SESSION['user']['id']);
    $stmt->execute();

    $sql='DELETE FROM connect_cronjob s  WHERE connect_messages_id = :id';
    $stmt = $dbh->prepare($sql);
    $stmt->bindParam(':id', $data['sms_id']);
    $stmt->execute();
}catch (PDOException $e) {
   
    echo $responseHandler->sendResponse(false, "Erreur  : " . $e->getMessage());
    exit();
}
echo $responseHandler->sendResponse(true, "mis à jour enregistré");
exit();
?>