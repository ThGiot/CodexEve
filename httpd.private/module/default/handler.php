<?php

// ----------------------------------------------------------------
// Module : Admin
// ----------------------------------------------------------------


require 'config.php';
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../sql.php';
require __DIR__ . '/../../module/permissions.php';
switch($data['page']){
    case 10 :
        include __DIR__ . '/page/medteam_recover.php';
    break;
    default:   
    echo $page;
        include __DIR__ . '/page/default.php';
    break;
}

?>
