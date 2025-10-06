<?php
session_start();
// Dependencies
require_once __DIR__ . '/../httpd.private/config.php';
require_once PRIVATE_PATH.'/sql.php';
require_once PRIVATE_PATH.'/classes/ClearPost.php';
require_once PRIVATE_PATH.'/classes/CronJob.php';  
require_once PRIVATE_PATH.'/classes/DBHandler.php';  


//1 On sanitaze les données reçus
$data = ClearPost::clearPost($_GET);
//if(!isset($data['message_id'])) exit('Error');


//On vérifie l'authenticité de la demande via la clef
if(!isset($data['key']) OR $data['key'] != CRON_API_KEY) exit('Error 2');




//On vérifie sur le status est toujours scheduled pour éviter un double envois
$query = "SELECT * 
FROM connect_recipients cr
JOIN connect_messages cm ON cr.message_id = cm.id 
WHERE (cm.status = 'pending' OR cm.status = 'scheduled') AND cr.status != 'sent'

AND send_date <= 
  DATE_ADD(
    NOW(), 
    INTERVAL
      CASE 
        WHEN CURDATE() BETWEEN 
          CONCAT(YEAR(CURDATE()), '-03-', 25 + (7 - WEEKDAY(CONCAT(YEAR(CURDATE()), '-03-31')))) 
          AND 
          CONCAT(YEAR(CURDATE()), '-10-', 25 + (7 - WEEKDAY(CONCAT(YEAR(CURDATE()), '-10-31')))) 
        THEN 2 
        ELSE 1 
      END 
    HOUR
  )
  ORDER BY send_date ASC
LIMIT 1";
$stmt = $dbh->prepare($query);
$stmt->execute();
$resultat = $stmt->fetchAll(PDO::FETCH_ASSOC);
if (empty($resultat)) exit('NO SMS TO SEND');
$data['message_id'] = $resultat[0]['id'];
//Tout est OK on peut avancer et envoyer le sms 
//Création d'un utilisateur temporaire

session_start([
    'cookie_secure' => true,
    'cookie_httponly' => true,
]);

$_SESSION['user'] =[
    "id" => 0
];
$auth_API = true;
$API_REQUEST = 'cron_sms';
$data['action'] = 'connect_sms_send';
$isComplete = false;


//Avant la boucle on défini ce qu'on attends après
function apiRequest($dbh, $jobId, $messageId){
    
    $cronJob = new CronJob(CRON_JOB_TOKEN);
    $cronJob -> deleteJob($jobId);

    $dbHandler = new DBHandler($dbh);
    $table = 'connect_cronjob';
    $primaryKey = ["connect_messages_id" => $messageId];

    $dbHandler->delete($table, $primaryKey);
    
}


while (!$isComplete) {
   
    require PRIVATE_PATH.'/action/connect_send_sms.php';
    // Vérifier si isComplete est égal à true dans la réponse JSON
    $responseArray = json_decode($response, true);
    $isComplete = isset($responseArray['isComplete']) && $responseArray['isComplete'] === true;
 
}
//on met a jour les status des messages

 $updateQuery = "
        UPDATE connect_messages cm
        SET cm.status = 'sent'
        WHERE cm.id IN (
            SELECT DISTINCT cr.message_id
            FROM connect_recipients cr
            WHERE cr.message_id = cm.id
            GROUP BY cr.message_id
            HAVING COUNT(*) = SUM(cr.status IN ('sent', 'failed'))
        )
    ";

    // Préparation et exécution de la requête
    $stmt = $dbh->prepare($updateQuery);
    $stmt->execute();



?>