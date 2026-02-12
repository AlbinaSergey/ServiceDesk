<?php

declare(strict_types=1);

require_once __DIR__ . '/bootstrap/env.php';
require_once __DIR__ . '/Services/DatabaseService.php';
require_once __DIR__ . '/Services/MigrationService.php';

use App\Services\DatabaseService;
use App\Services\MigrationService;

loadEnv(dirname(__DIR__) . '/.env');

$migrationService = new MigrationService(new DatabaseService());
$applied = $migrationService->run();

echo json_encode([
    'applied_count' => count($applied),
    'applied' => $applied,
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . PHP_EOL;
