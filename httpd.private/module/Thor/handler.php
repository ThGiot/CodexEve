<?php

// ----------------------------------------------------------------
// Module : Admin
// ----------------------------------------------------------------


require 'config.php';
require_once __DIR__ . '/../../config.php';
require __DIR__ . '/../../sql.php';
require __DIR__ . '/../../module/auth.php';
if($auth == false) exit();
require __DIR__ . '/../../module/permissions.php';

// ----------------------------------------------------------------
// permissions : role,page
// ----------------------------------------------------------------



switch($permissions){
 
    default :     
    case array(1,1) : 
    case array(2,1) : 
        include __DIR__ . '/page/event_liste.php';
    break;

    case array(1,11) : 
    case array(2,11) : 
        include __DIR__ . '/page/event_add.php';
    break;

    case array(1,12) : 
    case array(2,12) : 
        include __DIR__ . '/page/event_manage.php';
    break;


    
}
?>
