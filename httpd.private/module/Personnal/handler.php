<?php

require_once __DIR__ . '/../../config.php';
require __DIR__ . '/../../sql.php';
if(!isset($_SESSION['user']))exit('pas de session trouvé in Personnal Hander');


switch($data['page']){
   
    //Import Excell
    case '1' : 
        include __DIR__ . '/page/profile.php';
    break;

 
}

?>