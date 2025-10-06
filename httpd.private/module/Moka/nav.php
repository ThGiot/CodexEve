  
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
          'label' => 'Prestataire',
          'pages' => [
              [
                  'name' => 'Liste',
                  'icon' => 'fas fa-address-book',
                  'link' => '10'
              ],
              [
                'name' => 'Ajouter',
                'icon' => 'fas fa-user-plus',
                'link' => '11'
              ],
              
          ]
      ],
      [
          'label' => 'LazyMode',
          'pages' => [
              [
                  'name' => 'Import',
                  'icon' => 'fas fa-download',
                  'link' => '20'
              ],
              [
                'name' => 'Générer & Envoyer',
                'icon' => 'fas fa-paper-plane',
                'link' => '21'
              ],
              [
                'name' => 'Analytique',
                'icon' => 'fas fa-hand-holding-usd',
                'link' => '22'
              ],
              [
                'name' => 'Activité',
                'icon' => 'fas fa-clinic-medical',
                'link' => '23'
            ]
          ]
      ],
      [
        'label' => 'Prestations',
        'pages' => [
            [
                'name' => 'Liste',
                'icon' => 'fas fa-list-ul',
                'link' => '30'
            ],
            [
              'name' => 'Ajout',
              'icon' => 'fas fa-plus',
              'link' => '31'
          ]
        ]
    ],
    [
      'label' => 'Facture',
      'pages' => [
          [
              'name' => 'Liste',
              'icon' => 'fas fa-file-invoice',
              'link' => '40'
          ],
          [
            'name' => 'Ajouter',
            'icon' => 'far fa-plus-square',
            'link' => '41'
          ],
          [
            'name' => 'Correction',
            'icon' => 'far fa-edit',
            'link' => '42'
          ],
          [
            'name' => 'Correction Archive',
            'icon' => 'fas fa-archive',
            'link' => '43'
          ],
          [
            'name' => 'En attente',
            'icon' => 'far fa-clock',
            'link' => '44'
        ]
      ]
  ],
  [
    'label' => 'Moka',
    'pages' => [
        [
            'name' => 'Mes Factures',
            'icon' => 'fas fa-file',
            'link' => '1'
        ],
        
        [
          'name' => 'Mes informations',
          'icon' => 'fas fa-user-edit',
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
          'label' => 'Factures',
          'pages' => [
              [
                  'name' => 'Mes Factures',
                  'icon' => 'fas fa-file',
                  'link' => '1'
              ],
                        ]
      ],
      [
        'label' => 'Heures supplémentaires',
        'pages' => [
            
            [
              'name' => 'Mes Heures supplémentaires',
              'icon' => 'fas fa-list-ul',
              'link' => '3'
            ],
            [
              'name' => 'Ajouter',
              'icon' => 'fas fa-plus',
              'link' => '31'
            ],
        ]
    ],

    [
      'label' => 'Mes Informtions ',
      'pages' => [
          [
            'name' => 'Modifier mes informations',
            'icon' => 'fas fa-user-edit',
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