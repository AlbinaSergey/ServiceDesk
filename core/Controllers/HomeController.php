<?php

declare(strict_types=1);

namespace App\Controllers;

class HomeController
{
    public function index(): void
    {
        view('pages/home', ['title' => 'Портал ЕРЗСС (скелет)']);
    }

    public function health(): never
    {
        jsonResponse([
            'status' => 'ok',
            'service' => 'erzss-servicedesk',
            'time' => date('c'),
        ]);
    }

    public function routeDemo(array $params): never
    {
        jsonResponse([
            'status' => 'ok',
            'feature' => 'dynamic-route',
            'module' => $params['module'] ?? null,
        ]);
    }
}
