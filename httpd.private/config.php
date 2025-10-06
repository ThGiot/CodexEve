<?php


define('LOCAL', false);
setlocale(LC_TIME, 'fr_FR.UTF-8', 'fr_FR', 'fra', 'french');

// Config SMTP
define('SMTP_HOST', 'send.one.com');
define('SMTP_PORT', 465);
define('SMTP_USERNAME', 'donotreply@hygea-consult.be');
define('SMTP_PASSWORD', 'DnrHgc2023!');
define('FROM_EMAIL', 'donotreply@hygea-consult.be');
define('FROM_NAME', 'Do Not Reply');
define('SMTP_SECURE', 'ssl');
// Chemin d'acces au dossier privé
define('PRIVATE_PATH', '../httpd.private');
if (LOCAL ==true){ 
    define('PUBLIC_PATH', '../httpd.public');
}else{
    define('PUBLIC_PATH', '../httpd.www');
}
define('SUPER_ADMIN_USER_ID',2);

define('API_KEY', 'aaf13e43-1cb7-44cb-a4f6-cd9e5725f28c');

// Heure et date 

date_default_timezone_set('Europe/Brussels');

//Ebrigade MEDTEAM API
define('MEDTEAM_EB_API_URL', 'https://medteam.ebrigade.be/api/export/participation.php');
define('MEDTEAM_EB_API_KEY','c30141dc4c723bae3fc106d710b7c7c2');  

//MEDICAL TEAM CLIENT KEY
define('MEDTEAM_INVITATION_KEY','MEDTEAM-171224');  

//Cron Job API
define ('CRON_JOB_TOKEN','6DCon9Ict5S4LrIIURNpEz2jswLDwaLM6GHIihGX2zs='); 
define ('CRON_JOB_HANDLER_ULR','https://www.hygea-consult.be/cron_sms.php'); 
define ('CRON_API_KEY','vBEU5KwCcq0T5xfFaLmwaH1IuJm9LH1d'); 
//Twilio API

define ('TWILIO_SID','AC2770dbf4dd4fc7862917505391edb542'); 
define ('TWILIO_TOKEN','e3df054685bcf036fa535c6aeece83a7'); 


// Nom appli & Logo
define('APPLI_NAME', 'Eve');
define('APPLI_LOGO', 'assets/img/icons/logo.png');

//Power By API 

define('PWBI_KEY', 'API_TEST');
//Config SQL
if (LOCAL ==true) {
    $config = array(
        'hostname' => 'mysql',
        'database' => 'eve 1.6',
        'username' => 'root',
        'password' => 'rootpassword'
    );
} else {
    $config = array(
        'hostname' => 'hygea-consult.be.mysql',
        'database' => 'hygea_consult_besiberian',
        'username' => 'hygea_consult_besiberian',
        'password' => 'hgc2023!db'
    );
}



?>