  
<?php

require 'config.php';

require_once __DIR__ . '/../../config.php';
require __DIR__ . '/../../sql.php';
require __DIR__ . '/../../module/auth.php';

if($auth == false) exit('auth nav bar false');
require __DIR__ . '/../../module/role.php';


switch($role){

  case 1:
  case 2:
    $navData = [
      [
          'label' => 'Envois de SMS',
          'pages' => [
              [
                  'name' => 'Par import Excell',
                  'icon' => 'fas fa-file-download',
                  'link' => '1'
              ],
              [
                'name' => 'Log',
                'icon' => 'far fa-list-alt',
                'link' => '2'
            ]
          ]
      ],
      
      // Ajouter plus d'éléments ici...
    ];
  break;
  case 3 :
    $navData = [
      [
          'label' => 'Envois de SMS',
          'pages' => [
              [
                  'name' => 'Par import Excell',
                  'icon' => 'fas fa-file-download',
                  'link' => '1'
              ],
              [
                'name' => 'Log',
                'icon' => 'far fa-list-alt',
                'link' => '2'
            ]
          ]
      ],
      
      // Ajouter plus d'éléments ici...
    ];
}

$navData = json_encode($navData);
print_r($navData);
?>