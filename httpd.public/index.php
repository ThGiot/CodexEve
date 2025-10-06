<?php
session_start([
    'cookie_secure' => true,
    'cookie_httponly' => true,
]);
//session_regenerate_id(true);
require_once __DIR__ . '/../httpd.private/config.php';
require_once PRIVATE_PATH . '/sql.php';


// Vérification de la présence d'une connexion active sinon on envoie vers login.php
if(isset($_POST['action']) AND $_POST['action'] == 'connexion_send'){
    require_once PRIVATE_PATH . '/action/connexion.php';
}

if(!isset($_SESSION['user'])){   
    include 'login.php';
    exit();
    
}



//Il y a une connexion active, on rend donc une pages

include 'template/header.php';
include 'template/navbar.php';
include 'template/name_logo.php';
include 'template/notification.php';
include 'template/navbar_vertical.php';
include 'template/nav_profile.php';
include 'template/content.php';
include 'template/footer.php';

?>