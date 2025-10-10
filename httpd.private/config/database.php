<?php

if (!defined('LOCAL')) {
    define('LOCAL', true); // fallback si non défini
}

return [
    'db' => LOCAL
        ? [
            'hostname' => 'mysql',
            'database' => 'eve 1.6',
            'username' => 'root',
            'password' => 'rootpassword'
        ]
        : [
            'hostname' => 'hygea-consult.be.mysql',
            'database' => 'hygea_consult_besiberian',
            'username' => 'hygea_consult_besiberian',
            'password' => 'hgc2023!db'
        ]
];
