  
<?php
require 'config.php';
require_once __DIR__ . '/../../config.php';
require __DIR__ . '/../../sql.php';
require __DIR__ . '/../../module/auth.php';
if($auth == false) exit();
//require __DIR__ . '/../../classes/NavigationBar.php';

// Aller recupérer les informations de navigations du module en cours d'utilisation

$navData = [
  [
      'label' => 'Gestion SUPER ADMIN ',
      'pages' => [
          [
              'name' => 'Client',
              'icon' => 'fas fa-users',
              'link' => '2'
          
          ],],],
         
          
  // Ajouter plus d'éléments ici...
];
$navData = json_encode($navData);
print_r($navData);
?>