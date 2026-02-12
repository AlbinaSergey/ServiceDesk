<?php

declare(strict_types=1);

namespace App\Middleware;

class SecurityHeadersMiddleware
{
    public function __invoke(array $params = []): void
    {
        header('X-Frame-Options: DENY');
        header('X-Content-Type-Options: nosniff');
        header('Referrer-Policy: strict-origin-when-cross-origin');
        header("Content-Security-Policy: default-src 'self' https://cdn.jsdelivr.net; style-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net;");
    }
}
