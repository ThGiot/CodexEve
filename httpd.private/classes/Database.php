<?php

namespace App;

use PDO;
use PDOException;

class Database {
    private static ?PDO $connection = null;

    public static function getConnection() {
        if (self::$connection === null) {
            $config = require_once dirname(__DIR__, 1) . '/config/database.php';
                $config = $config['db'];
         
            $dsn = 'mysql:host=' . $config['hostname'] . ';dbname=' . $config['database'] . ';charset=utf8mb4';
           
            try {
                self::$connection = new PDO($dsn, $config['username'], $config['password'], [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]);
            } catch (PDOException $e) {
                die("Erreur de connexion : " . $e->getMessage());
            }
        }

        return self::$connection;
    }

    public static function closeConnection(): void {
        self::$connection = null;
    }
}
