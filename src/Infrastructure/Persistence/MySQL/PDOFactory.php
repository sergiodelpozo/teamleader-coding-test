<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\MySQL;

use PDO;
use PDOException;

final class PDOFactory
{
    public static function create(): PDO
    {
        $host = $_ENV['MYSQL_HOST'];
        $db = $_ENV['MYSQL_DB'];
        $user = $_ENV['MYSQL_USER'];
        $pass = $_ENV['MYSQL_PASSWORD'];
        $charset = $_ENV['MYSQL_CHARSET'] ?? 'utf8';

        $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        try {
            return new PDO($dsn, $user, $pass, $options);
        } catch (PDOException $e) {
            throw new \RuntimeException("Database connection failed: " . $e->getMessage());
        }
    }
}
