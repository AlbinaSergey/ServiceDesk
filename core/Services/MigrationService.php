<?php

declare(strict_types=1);

namespace App\Services;

use PDO;
use RuntimeException;

class MigrationService
{
    public function __construct(private readonly DatabaseService $database)
    {
    }

    public function run(): array
    {
        $pdo = $this->database->connection();
        $this->ensureMigrationsTable($pdo);

        $applied = [];
        foreach ($this->collectMigrations() as $migration) {
            if ($this->isApplied($pdo, $migration['name'])) {
                continue;
            }

            $sql = file_get_contents($migration['path']);
            if ($sql === false) {
                throw new RuntimeException('Cannot read migration: ' . $migration['path']);
            }

            $pdo->beginTransaction();
            try {
                $pdo->exec($sql);
                $this->markApplied($pdo, $migration['name'], $migration['module']);
                $pdo->commit();
                $applied[] = $migration['name'];
            } catch (\Throwable $e) {
                $pdo->rollBack();
                throw $e;
            }
        }

        return $applied;
    }

    private function ensureMigrationsTable(PDO $pdo): void
    {
        $driver = strtolower($_ENV['DB_DRIVER'] ?? 'mysql');

        if ($driver === 'sqlite') {
            $pdo->exec(<<<SQL
CREATE TABLE IF NOT EXISTS migrations (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  name TEXT NOT NULL UNIQUE,
  module TEXT NULL,
  applied_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP
)
SQL);
            return;
        }

        $pdo->exec(<<<SQL
CREATE TABLE IF NOT EXISTS migrations (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(190) NOT NULL UNIQUE,
  module VARCHAR(120) NULL,
  applied_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
SQL);
    }

    private function isApplied(PDO $pdo, string $name): bool
    {
        $stmt = $pdo->prepare('SELECT 1 FROM migrations WHERE name = :name LIMIT 1');
        $stmt->execute(['name' => $name]);
        return (bool) $stmt->fetchColumn();
    }

    private function markApplied(PDO $pdo, string $name, ?string $module): void
    {
        $stmt = $pdo->prepare('INSERT INTO migrations (name, module) VALUES (:name, :module)');
        $stmt->execute(['name' => $name, 'module' => $module]);
    }

    private function collectMigrations(): array
    {
        $migrations = [];

        foreach (glob(dirname(__DIR__, 2) . '/core/migrations/*.sql') ?: [] as $path) {
            $migrations[] = ['name' => basename($path), 'module' => 'core', 'path' => $path];
        }

        foreach (glob(dirname(__DIR__, 2) . '/modules/*/migrations/*.sql') ?: [] as $path) {
            $module = basename(dirname(dirname($path)));
            $migrations[] = ['name' => $module . ':' . basename($path), 'module' => $module, 'path' => $path];
        }

        usort($migrations, static fn (array $a, array $b): int => strcmp($a['name'], $b['name']));
        return $migrations;
    }
}
