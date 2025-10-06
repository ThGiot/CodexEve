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
        include __DIR__ . '/page/la_grille.php';
    break;

    case array(1,10) : 
    case array(2,10) : 
        include __DIR__ . '/page/poste_add.php';
    break;

    case array(1,20) : 
    case array(2,20) : 
        include __DIR__ . '/page/poste_liste.php';
    break;

    case array(1,201) : 
    case array(2,201) : 
        include __DIR__ . '/page/poste_manage.php';
    break;

    case array(1,30) : 
    case array(2,30) : 
        include __DIR__ . '/page/horaire_liste.php';
    break;

    case array(1,301) : 
    case array(2,301) : 
        include __DIR__ . '/page/horaire_manage_js.php';
    break;

    case array(1,40) : 
    case array(2,40) : 
        include __DIR__ . '/page/zone_liste.php';
    break;

    case array(1,50) : 
    case array(2,50) : 
        include __DIR__ . '/page/association_liste.php';
    break;

    case array(1,501) : 
    case array(2,501) : 
        include __DIR__ . '/page/association_manage.php';
    break;


    
}
?>
