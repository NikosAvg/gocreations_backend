<?php
declare(strict_types=1);

namespace App;
use PDO;

final class Database{
    public static function connect(): PDO
    {
        $host = '127.0.0.1';
        $name = 'vehicles';
        $user = 'vehicles';
        $pass = 'secret';

        return new PDO(
            "mysql:host=$host;dbname=$name;charset=utf8mb4",
            $user,
            $pass,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]

        );
    }
}


?>
