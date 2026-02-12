<?php

declare(strict_types=1);

function jsonResponse(array $payload, int $status = 200): never
{
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

function redirect(string $location): never
{
    header('Location: ' . $location);
    exit;
}

function view(string $page, array $data = []): void
{
    $viewsPath = dirname(__DIR__, 2) . '/themes/default';
    $pageFile = $viewsPath . '/' . $page . '.php';

    if (!is_file($pageFile)) {
        http_response_code(500);
        echo 'View not found';
        return;
    }

    extract($data, EXTR_SKIP);
    require $viewsPath . '/layouts/main.php';
}
