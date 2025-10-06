<?php
require_once dirname(__DIR__, 1) . '/config.php';
require_once dirname(__DIR__, 1) . '/sql.php';
require_once dirname(__DIR__, 1) . '/classes/DBHandler.php';
require_once dirname(__DIR__, 1) . '/classes/PhoneNumberHandler.php';
require_once dirname(__DIR__, 1) . '/classes/ResponseHandler.php';
require_once dirname(__DIR__, 1) . '/classes/RequestHandler.php';
require_once dirname(__DIR__, 1) . '/classes/excell/php-excel-reader/excel_reader2.php';
require_once dirname(__DIR__, 1) . '/classes/excell/SpreadsheetReader.php';
require_once dirname(__DIR__, 1) . '/classes/CronJob.php';  

try{
    $cronJob = new CronJob(CRON_JOB_TOKEN);
    $data['date']='30/08/23 10:00';
    $dateFormat = 'd/m/y H:i';
    $lastMessageId = '444';
$inputDate = DateTime::createFromFormat($dateFormat, $data['date']);
$schedule = $cronJob->createAnnualSchedule($inputDate);
$jobOptions = [
    'enabled' => true,
    'title' => 'Job sms :'. $lastMessageId,
    'schedule' => $schedule,
    'url' => CRON_JOB_HANDLER_ULR.'?message_id='.$lastMessageId.'&job_id='
];
   $job = $cronJob->createJob($jobOptions);
   $jobDataToUpdate = [
         'url' => CRON_JOB_HANDLER_ULR.'?message_id='.$lastMessageId.'&job_id='.$job['jobId']
     ];
     $cronJob->updateJob($job['jobId'], $jobDataToUpdate);
}catch (Exception $e) {
  
    exit($responseHandler->sendResponse(true, 'FAILED connect_send_byexcell :'. $e));
}  

?>