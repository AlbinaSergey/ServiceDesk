<?php

declare(strict_types=1);

use App\Bootstrap\Container;
use App\Middleware\SecurityHeadersMiddleware;
use App\Router;
use App\Services\DatabaseService;
use App\Services\ErrorHandler;
use App\Services\EventBus;
use App\Services\MigrationService;
use App\Services\AuditService;
use App\Services\SecurityLogger;

require_once dirname(__DIR__, 2) . '/core/bootstrap/env.php';

$autoloader = dirname(__DIR__, 2) . '/vendor/autoload.php';
if (is_file($autoloader)) {
    require_once $autoloader;
} else {
    spl_autoload_register(function (string $class): void {
        $prefix = 'App\\';
        $baseDir = dirname(__DIR__, 2) . '/core/';

        if (!str_starts_with($class, $prefix)) {
            return;
        }

        $relativeClass = substr($class, strlen($prefix));
        $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';

        if (is_file($file)) {
            require_once $file;
        }
    });

    require_once dirname(__DIR__) . '/helpers/response.php';
    require_once dirname(__DIR__) . '/helpers/sanitize.php';
}

loadEnv(dirname(__DIR__, 2) . '/.env');

$container = new Container();
$container->singleton(DatabaseService::class, fn () => new DatabaseService());
$container->singleton(MigrationService::class, fn (Container $c) => new MigrationService($c->get(DatabaseService::class)));
$container->singleton(ErrorHandler::class, fn () => new ErrorHandler());
$container->singleton(EventBus::class, fn (Container $c) => new EventBus($c->get(ErrorHandler::class)));
$container->singleton(AuditService::class, fn (Container $c) => new AuditService($c->get(DatabaseService::class)));
$container->singleton(SecurityLogger::class, fn (Container $c) => new SecurityLogger($c->get(DatabaseService::class)));
$container->singleton(SecurityHeadersMiddleware::class, fn () => new SecurityHeadersMiddleware());
$container->singleton(Router::class, fn () => new Router());

$debug = filter_var($_ENV['APP_DEBUG'] ?? false, FILTER_VALIDATE_BOOL);
$container->get(ErrorHandler::class)->register($debug);

$router = $container->get(Router::class);
$securityMiddleware = $container->get(SecurityHeadersMiddleware::class);

$registerRoutes = require dirname(__DIR__, 2) . '/config/routes.php';
$registerRoutes($router, $securityMiddleware);

return [$container, $router];
