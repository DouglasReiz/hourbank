<?php

namespace App\Azhoras\Infrastructure\Database;

use PDO;
use PDOException;

class Connection
{
    private static ?PDO $instance = null;

    private function __construct() {}

    public static function getInstance(): PDO
    {
        if (self::$instance === null) {
            $host     = $_ENV['DB_HOST']     ?? 'localhost';
            $dbname   = $_ENV['DB_NAME']     ?? 'azhoras';
            $username = $_ENV['DB_USER']     ?? 'root';
            $password = $_ENV['DB_PASSWORD'] ?? '';

            try {
                self::$instance = new PDO(
                    "mysql:host={$host};dbname={$dbname};charset=utf8mb4",
                    $username,
                    $password,
                    [
                        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                        PDO::ATTR_EMULATE_PREPARES   => false,
                    ]
                );
            } catch (PDOException $e) {
                throw new \RuntimeException("Falha na conexão: " . $e->getMessage());
            }
        }

        return self::$instance;
    }
}
