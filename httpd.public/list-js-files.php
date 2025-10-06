<?php

session_start();
if(!isset($_SESSION['user'])){
    exit('le client n\'est pas connecté');
}



$dir = "assets/js/node";
$jsFiles = array();

foreach (glob($dir . "/*.js") as $file) {
  if (basename($file) != "node.js") {
    $jsFiles[] = basename($file, ".js");
  }
}

echo json_encode($jsFiles);
?>