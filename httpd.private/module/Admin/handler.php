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
    case array(1,2) : 
 
        if(isset($data['client_id'])) {
            include __DIR__ . '/page/client_vieuw.php';
        }else{
            include __DIR__ . '/page/client.php';
        }
    break;

    // page 21 : Manage module via Client_vieuw
    case array(1,21) :
   
        include __DIR__ . '/page/module_manage.php';
    break;

    
}
?>
