<?php
require_once dirname(__DIR__, 3) . '/config.php';
require_once dirname(__DIR__, 3) . '/sql.php';
require_once dirname(__DIR__, 3) . '/classes/ResponseHandler.php';
require_once dirname(__DIR__, 3) . '/classes/RequestHandler.php';
require_once dirname(__DIR__, 3) . '/classes/DBHandler.php';
require_once dirname(__DIR__, 3) . '/classes/CronJob.php';  

$responseHandler = new ResponseHandler();
$requestHandler = new RequestHandler();

$module_id = 2; // ID MODULE CONNECT
$requestHandler-> verifyModulePermission($module_id,$dbh);

// Définir les règles de validation
$rules = [
    'item' => ['type' => 'string', 'max_length'=> 10],
    'value' => ['type' => 'string', 'max_length'=> 10000]
];
// Gérer la requête avec authentification, assainissement, et validation
$data = $requestHandler->handleRequest($data, $rules); 


switch($data['item']){
    case 'message':
        $dbHandler = new DBHandler($dbh);
        $table = 'connect_messages';
        $key = ['id' => $data['sms_id']];
        $dbData = ['message' => $data['value']];
        $dbHandler->update($table, $key, $dbData);

    break;    
    case 'date':
        $date = DateTime::createFromFormat('d/m/y H:i', $data['value']);
        $dateFormatted = $date->format('Y-m-d H:i:s'); // Convertir l'objet DateTime en chaîne
        $sql = "UPDATE connect_messages SET send_date = :send_date WHERE id = :id";
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(':id', $data['sms_id']);
        $stmt->bindParam(':send_date', $dateFormatted); // Utiliser la chaîne formatée
        $stmt->execute();
        $sms = $stmt->fetch(PDO::FETCH_ASSOC);
        //on recrée un CronJob pour la nouvelle date

        $sql='DELETE FROM connect_cronjob  WHERE connect_messages_id = :id';
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(':id', $data['sms_id']);
        $stmt->execute();
        try{
            $cronJob = new CronJob(CRON_JOB_TOKEN);
            $dateFormat = 'd/m/y H:i';
            $inputDate = DateTime::createFromFormat($dateFormat, $data['value']);
            $schedule = $cronJob->createAnnualSchedule($inputDate);
            $jobOptions = [
                'enabled' => true,
                'title' => 'Job sms :'. $data['sms_id'],
                'schedule' => $schedule,
                'url' => CRON_JOB_HANDLER_ULR.'?message_id='.$data['sms_id'].'&job_id='
            ];
            $job = $cronJob->createJob($jobOptions);
            $jobDataToUpdate = [
                    'url' => CRON_JOB_HANDLER_ULR.'?message_id='.$data['sms_id'].'&job_id='.$job['jobId'].'&key='.CRON_API_KEY
                ];
                $responseHandler->addData('JOB', $job['jobId']);
            $cronJob->updateJob($job['jobId'], $jobDataToUpdate);
            $query = "INSERT INTO connect_cronjob (connect_messages_id, job_id) VALUES (:message_id, :job_id)";
            $stmt =$dbh->prepare($query);
            $stmt->bindParam(':message_id',$data['sms_id']);
            $stmt->bindParam(':job_id', $job['jobId']);
            $stmt->execute();
    
    
        }catch (Exception $e) {
          
            exit($responseHandler->sendResponse(true, 'FAILED connect_send_byexcell :'. $e));
        }  
        
        

    break;
}
echo $responseHandler->sendResponse(true, $data['value']);



?>