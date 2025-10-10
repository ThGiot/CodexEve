  
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
          'label' => 'La Grille',
          'pages' => [
              [
                  'name' => 'La Grille',
                  'icon' => 'fas fa-file-download',
                  'link' => '1'
              ],
              [
                'name' => 'Ajouter un poste',
                'icon' => 'fas fa-user-plus',
                'link' => '10'
              ],
              [
                'name' => 'Liste des postes',
                'icon' => 'fas fa-users-cog',
                'link' => '20'
              ],
              [
                'name' => 'Gestions des horaires',
                'icon' => 'fas fa-clock',
                'link' => '30'
              ],
              [
                'name' => 'Gestions des Zones',
                'icon' => 'fas fa-map-marked-alt',
                'link' => '40'
              ],
              [
                'name' => 'Matériel',
                'icon' => 'fas fa-map-marked-alt',
                'link' => '80'
              ]
              
            
          ]
              ],
              [
                'label' => 'Gestion RH',
                'pages' => [
                  [
                    'name' => 'Gestions des Assosiation',
                    'icon' => 'fas fa-th-list',
                    'link' => '50'
                  ],
                    [
                      'name' => 'Besoins RH',
                      'icon' => 'fas fa-user-plus',
                      'link' => '60'
                    ],
                    [
                      'name' => 'Catering',
                      'icon' => 'fas fa-user-plus',
                      'link' => '70'
                    ]
                 
                ]
            ]
      

    ];
  break;
  case 3:
  
      $navData = [
        [
            'label' => 'Grid',
            'pages' => [
                [
                    'name' => 'La Grille',
                    'icon' => 'fas fa-file-download',
                    'link' => '1'
                ]
            ]
        ]
        
      ];
    break;
}

$navData = json_encode($navData);
print_r($navData);
?>