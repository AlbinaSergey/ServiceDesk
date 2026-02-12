<?php

declare(strict_types=1);

[$container, $router] = require dirname(__DIR__) . '/core/bootstrap/app.php';

$router->dispatch($_SERVER['REQUEST_METHOD'] ?? 'GET', $_SERVER['REQUEST_URI'] ?? '/');
