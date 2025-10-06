  
<?php
require 'config.php';
require_once __DIR__ . '/../../config.php';
require __DIR__ . '/../../sql.php';


// Aller recupérer les informations de navigations du module en cours d'utilisation

$navData = [
  [
      'label' => ' Accueil',
      'pages' => [
          [
              'name' => 'Accueil',
              'icon' => 'fas fa-users',
              'link' => '0'
          
          ],],],
         
          
  // Ajouter plus d'éléments ici...
];
$navData = json_encode($navData);
print_r($navData);
?>