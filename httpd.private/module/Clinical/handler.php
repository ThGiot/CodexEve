<?php
require 'config.php';
require_once __DIR__ . '/../../config.php';
require __DIR__ . '/../../sql.php';
require __DIR__ . '/../../module/auth.php';
if($auth == false) exit('echec auth.handler in module Connect');
require __DIR__ . '/../../module/permissions.php';


switch($permissions){
   
    //Import Excell
    case array (1,'1') : 
    case array (2,'1') : 
    case array (3,'1') : 
        include __DIR__ . '/page/import_excell.php';
    break;

    //Liste SMS
    case array (1,'2') : 
    case array (2,'2') : 
    case array (3,'2') : 
    default :
        include __DIR__ . '/page/liste_sms.php';
    break;

    //Manage SMS
    case array (1,'3') : 
    case array (2,'3') : 
        include __DIR__ . '/page/manage_sms.php';
    break;

 
}

?>