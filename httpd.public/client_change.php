<?php
session_start();
require_once __DIR__ . '/../httpd.private/config.php';

$json = file_get_contents('php://input');

// Decode the JSON data
$data = json_decode($json, true);
require  PRIVATE_PATH.'/action/client_change.php';




?>