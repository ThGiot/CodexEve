<?php
use App\Controller\NavController;

require_once __DIR__ . '/../../../httpd.private/vendor/autoload.php';
require_once __DIR__ . '/../../../httpd.private/config.php';
require_once PRIVATE_PATH . '/sql.php';

$controller = new NavController($dbh);
$controller->render('Clinical');
