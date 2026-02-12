<?php

declare(strict_types=1);

namespace App\Services;

use Throwable;

class ErrorHandler
{
    public function register(bool $debug): void
    {
        set_exception_handler(function (Throwable $exception) use ($debug): void {
            $this->logError($exception);

            http_response_code(500);
            if ($debug) {
                echo '<h1>Unhandled exception</h1>';
                echo '<pre>' . htmlspecialchars((string) $exception) . '</pre>';
                return;
            }

            view('pages/error', ['status' => 500, 'message' => 'Внутренняя ошибка сервера']);
        });
    }

    public function logError(Throwable $exception, array $context = []): void
    {
        $logPath = dirname(__DIR__, 2) . '/storage/logs/error.log';
        $record = sprintf(
            "[%s] %s in %s:%d | context=%s\n",
            date('c'),
            $exception->getMessage(),
            $exception->getFile(),
            $exception->getLine(),
            json_encode($context, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
        );

        file_put_contents($logPath, $record, FILE_APPEND);
    }
}
