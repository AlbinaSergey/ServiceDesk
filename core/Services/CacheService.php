<?php

declare(strict_types=1);

namespace App\Services;

class CacheService
{
    private string $cacheDir;

    public function __construct(?string $cacheDir = null)
    {
        $this->cacheDir = $cacheDir ?? dirname(__DIR__, 2) . '/storage/cache';

        if (!is_dir($this->cacheDir)) {
            mkdir($this->cacheDir, 0775, true);
        }
    }

    public function put(string $key, mixed $value, int $ttlSeconds = 300): void
    {
        $payload = [
            'expires_at' => time() + max(1, $ttlSeconds),
            'value' => $value,
        ];

        file_put_contents($this->path($key), json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
    }

    public function get(string $key, mixed $default = null): mixed
    {
        $path = $this->path($key);
        if (!is_file($path)) {
            return $default;
        }

        $raw = file_get_contents($path);
        if ($raw === false) {
            return $default;
        }

        $payload = json_decode($raw, true);
        if (!is_array($payload) || !isset($payload['expires_at'])) {
            return $default;
        }

        if ((int) $payload['expires_at'] < time()) {
            @unlink($path);
            return $default;
        }

        return $payload['value'] ?? $default;
    }

    public function forget(string $key): void
    {
        $path = $this->path($key);
        if (is_file($path)) {
            unlink($path);
        }
    }

    public function flush(): void
    {
        foreach (glob($this->cacheDir . '/*.cache') ?: [] as $file) {
            @unlink($file);
        }
    }

    private function path(string $key): string
    {
        return $this->cacheDir . '/' . sha1($key) . '.cache';
    }
}
