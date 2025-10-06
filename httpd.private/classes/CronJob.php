<?php

/**
 * Utilisation de la Classe CronJob
 *
 * La classe CronJob fournie ci-dessous est conçue pour interagir avec l'API Cron-Job.
 * Cette API permet de gérer et de contrôler des tâches planifiées (cron jobs) à distance.
 * La classe offre des méthodes pour effectuer des opérations telles que récupérer la liste des jobs,
 * obtenir des détails spécifiques sur un job, créer un nouveau job, mettre à jour un job existant
 * et supprimer un job.
 *

 *
 * $cronJob = new CronJob(CRON_JOB_TOKEN);
 * $cronJob->getJobs();
 *
 * // Obtenir les détails d'un job
 * require_once 'CronJob.php';
 *
 * $cronJob = new CronJob(CRON_JOB_TOKEN);
 * $jobDetails = $cronJob->getJobDetail(4539151);
 * print_r($jobDetails);
 *
 * // Créer un nouveau job
 * require_once 'CronJob.php';
 *
 * $cronJob = new CronJob(CRON_JOB_TOKEN);
 * $schedule = $cronJob->createAnnualSchedule('2023-08-26 12:22:00');
 * $jobOptions = [
 *     'enabled' => true,
 *     'title' => 'Job annuel',
 *     'url' => 'https://mon-site.com/job',
 *     'schedule' => $schedule,
 *     // autres détails du job ici...
 * ];
 * $cronJob->createJob($jobOptions);
 *
 * // Mettre à jour un job existant
 * require_once 'CronJob.php';
 *
 * $cronJob = new CronJob(CRON_JOB_TOKEN);
 * $jobDataToUpdate = [
 *     'enabled' => true
 * ];
 * $cronJob->updateJob(4539151, $jobDataToUpdate);
 *
 * // Supprimer un job
 * require_once 'CronJob.php';
 *
 * $cronJob = new CronJob(CRON_JOB_TOKEN);
 * $cronJob->deleteJob(4539151);
 *
 */


class CronJob {
    private $token;
    private $endpoint = 'https://api.cron-job.org/jobs';

    public function __construct($token) {
        $this->token = $token;
    }

    public function getJobs() {
        $headers = [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->token
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->endpoint);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $result = curl_exec($ch);
        if ($result === false) {
            echo 'Erreur cURL : ' . curl_error($ch);
        } else {
            $resultArray = json_decode($result, true);
            print_r($resultArray);
        }

        curl_close($ch);
    }
    public function getJobDetail($jobId) {
        $url = $this->endpoint . '/' . $jobId; // Ajoutez '/jobs/' ici
    
        $headers = [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->token
        ];
    
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
        $result = curl_exec($ch);
        if ($result === false) {
            echo 'Erreur cURL : ' . curl_error($ch);
            return null;
        }
    
        $info = curl_getinfo($ch); // Récupère les informations sur la requête
        echo 'HTTP Code: ' . $info['http_code'] . "\n"; // Affiche le code de statut HTTP
    
        curl_close($ch);
        return json_decode($result, true);
    }
    
    
    public function createJob($jobDetails) {
        $headers = [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->token
        ];
        
        $payload = json_encode(['job' => $jobDetails]);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->endpoint);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT'); // Utilisation de PUT pour créer un nouveau job
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $result = curl_exec($ch);
        if ($result === false) {
            curl_close($ch);
            return 'Erreur cURL : ' . curl_error($ch);
        } else {
            curl_close($ch);
          return $resultArray = json_decode($result, true);
         
        }

        
    }
    public function createAnnualSchedule($dateTime, $timeZone = 'Europe/Paris'){

        $date = $dateTime;
        $expiresAtDate =  clone $date;
        $expiresAtDate->add(new DateInterval('PT1H')); // Ajoute une heure
        $expiresAt = $expiresAtDate->format('YmdHis'); 

        return [
            'timezone' => $timeZone,
            'expiresAt' => $expiresAt,
            'hours' => [(int)$date->format('H')],
            'mdays' => [(int)$date->format('d')],
            'minutes' => [(int)$date->format('i')],
            'months' => [(int)$date->format('m')],
            'wdays' => [-1] // Tous les jours de la semaine
        ];
    }

    public function updateJob($jobId, $jobData){

        $url = $this->endpoint . '/' . $jobId; // Ajoutez '/jobs/' ici
        $headers = [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->token // Modification ici
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PATCH');
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $jobData = ['job'=>$jobData];
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($jobData));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $result = curl_exec($ch);
        if ($result === false) {
            echo 'Erreur cURL : ' . curl_error($ch);
            return null;
        }
        $info = curl_getinfo($ch);
        return 'HTTP Code: ' . $info['http_code'] . "\n";
        
        curl_close($ch);
        return json_decode($result, true);
        }

    public function deleteJob($jobId){
        $url = $this->endpoint . '/' . $jobId; // Ajoutez '/jobs/' ici
        $headers = [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->token // Modification ici
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $result = curl_exec($ch);
        if ($result === false) {
            echo 'Erreur cURL : ' . curl_error($ch);
            return null;
        }

        curl_close($ch);
        return json_decode($result, true);
    }


    
}
?>