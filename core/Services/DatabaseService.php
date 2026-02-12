<?php

declare(strict_types=1);

namespace App\Services;

use PDO;

class DatabaseService
{
    private ?PDO $connection = null;

    public function connection(): PDO
    {
        if ($this->connection instanceof PDO) {
            return $this->connection;
        }

        $driver = strtolower($_ENV['DB_DRIVER'] ?? 'mysql');

        if ($driver === 'sqlite') {
            $path = $_ENV['DB_SQLITE_PATH'] ?? dirname(__DIR__, 2) . '/storage/database.sqlite';
            $dir = dirname($path);
            if (!is_dir($dir)) {
                mkdir($dir, 0775, true);
            }

            $pdo = new PDO('sqlite:' . $path);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            return $this->connection = $pdo;
        }

        $host = $_ENV['DB_HOST'] ?? '127.0.0.1';
        $port = (int) ($_ENV['DB_PORT'] ?? 3306);
        $database = $_ENV['DB_DATABASE'] ?? 'servicedesk';
        $username = $_ENV['DB_USERNAME'] ?? 'root';
        $password = $_ENV['DB_PASSWORD'] ?? '';

        $dsn = sprintf('mysql:host=%s;port=%d;dbname=%s;charset=utf8mb4', $host, $port, $database);
        $pdo = new PDO($dsn, $username, $password, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);

        return $this->connection = $pdo;
    }
}
