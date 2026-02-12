<?php

declare(strict_types=1);

use App\Controllers\HomeController;
use App\Router;

return static function (Router $router, callable $securityMiddleware): void {
    $router->get('/', [HomeController::class, 'index'], [$securityMiddleware]);

    $router->group('/api/v1', [$securityMiddleware], function (Router $router): void {
        $router->get('/health', [HomeController::class, 'health']);
        $router->get('/modules/{module}', [HomeController::class, 'routeDemo']);
    });
};
