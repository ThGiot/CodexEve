<?php
require_once dirname(__DIR__, 3) . '/config.php';
function importEbrigadeCurl($payload){
    $ch = curl_init(MEDTEAM_EB_API_URL);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $result = curl_exec($ch);
    print curl_error($ch);
    $info = curl_getinfo($ch);
    curl_close($ch);
    
    //Récupération des données
    return json_decode($result, true);
}
?>