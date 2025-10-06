<?php
$module_id = 2; // ID MODULE CONNECT

// Inclure les fichiers de configuration et de bibliothèques nécessaires
require_once dirname(__DIR__, 3) . '/config.php';
require_once dirname(__DIR__, 3) . '/sql.php';
require_once dirname(__DIR__, 3) . '/classes/DBHandler.php';
require_once dirname(__DIR__, 3) . '/classes/PhoneNumberHandler.php';
require_once dirname(__DIR__, 3) . '/classes/ResponseHandler.php';
require_once dirname(__DIR__, 3) . '/classes/RequestHandler.php';
require dirname(__DIR__, 3) . '/vendor/autoload.php';
use Twilio\Rest\Client;

// Initialiser les gestionnaires de réponse et de requête
$requestHandler = new RequestHandler();
$responseHandler = new ResponseHandler();

// Définir les règles de validation
$rules = [
    'message_id' => ['type' => 'int']
];

// Gérer la requête avec authentification, assainissement et validation
if (!isset($auth_API) || $auth_API != true) {
    $data = $requestHandler->handleRequest($data, $rules);
    $requestHandler->verifyModulePermission($module_id, $dbh);
}

// Mettre à jour le statut du message à "pending"
$dbHandler = new DBHandler($dbh);
$table = 'connect_messages';
$key = ['id' => $data['message_id']];
$dbData = ['status' => 'pending'];
$dbHandler->update($table, $key, $dbData);

try {
    // Lire les informations du message et du destinataire depuis la base de données
    $sql = "SELECT cr.phone_number AS phone_number, cr.id AS id, cm.message AS message
            FROM connect_recipients cr
            JOIN connect_messages cm ON cm.id = cr.message_id
            WHERE cr.message_id = :message_id AND (cr.status = 'pending' OR cr.status = 'scheduled')
            LIMIT 100"; // Limite pour éviter les grands ensembles de données

    $stmt = $dbh->prepare($sql);
    $stmt->bindParam(':message_id', $data['message_id']);
    $stmt->execute();
    $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($messages)) {
        $dbData = ['status' => 'sent'];
        $dbHandler->update($table, $key, $dbData);
        $responseHandler->addData('isComplete', 'true');
        echo $responseHandler->sendResponse(true, 'success');

        if (isset($auth_API)) apiRequest($dbh, $data['job_id'], $data['message_id']);
        exit();
    }

    $sid = TWILIO_SID;
    $token = TWILIO_TOKEN;
    $twilio = new Client($sid, $token);

    foreach ($messages as $incomingMessage) {
        try {
            $twilio->messages->create(
                $incomingMessage['phone_number'],
                [
                    'from' => '+32460260009',
                    'body' => $incomingMessage['message']
                ]
            );
            $dbData = ['status' => 'sent', 'sent_at' => date('Y-m-d H:i:s')];
        } catch (Exception $e) {
            $dbData = ['status' => 'failed'];
        }
        $dbHandler->update('connect_recipients', ['id' => $incomingMessage['id']], $dbData);
    }

} catch (PDOException $e) {
    echo "Erreur critique lors de l'envoi de sms : connect_send_sms : " . $e->getMessage();
    exit;
}

// Calculer les pourcentages en PHP pour éviter les requêtes complexes
try {
    // Total des statuts
    $totalQuery = "SELECT status, COUNT(*) AS count FROM connect_recipients
                   WHERE message_id = :message_id
                   GROUP BY status";
    $stmt = $dbh->prepare($totalQuery);
    $stmt->bindParam(':message_id', $data['message_id']);
    $stmt->execute();
    $statusCounts = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $totalQuery = "SELECT COUNT(*) AS total FROM connect_recipients WHERE message_id = :message_id";
    $stmt = $dbh->prepare($totalQuery);
    $stmt->bindParam(':message_id', $data['message_id']);
    $stmt->execute();
    $total = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

    $percentages = [];
    foreach ($statusCounts as $row) {
        $percentages[$row['status']] = round(($row['count'] / $total) * 100, 0);
    }

    $progress = ($percentages['sent'] ?? 0) + ($percentages['failed'] ?? 0);
    $responseHandler->addData('isComplete', 'false');
    $responseHandler->addData('progress', $progress);
    $responseHandler->addData('message_id', $data['message_id']);
    echo $responseHandler->sendResponse(true, 'success');

} catch (PDOException $e) {
    echo "Erreur critique lors du calcul des pourcentages : " . $e->getMessage();
    exit;
}
?>
