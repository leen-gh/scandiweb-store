<?php

namespace App;

use PDO;

class Database {
    public static function connect(): PDO {
        $host = 'localhost';
        $db   = 'sweb-ecommerce';
        $user = 'root';
        $pass = 'root';
        $port = 3306;
        $charset = 'utf8mb4';

        $dsn = "mysql:host=$host;port=$port;dbname=$db;charset=$charset";
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ];

        return new PDO($dsn, $user, $pass, $options);
    }
}
