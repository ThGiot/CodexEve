<?php
return [
    [
        'label' => 'Procédures',
        'pages' => [
            [
                'name' => 'Liste des hopitaux',
                'icon' => 'fas fa-list',
                'link' => '1',
                'view' => 'hospital.php',
                'roles' => [1, 2],
            ],
            [
                'name' => 'Import Excel',
                'icon' => 'fas fa-file-excel',
                'link' => '2',
                'view' => 'import_excel.php',
                'roles' => [1, 2],
            ],
        ],
    ],
];
