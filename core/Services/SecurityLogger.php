<?php

declare(strict_types=1);

namespace App\Services;

class SecurityLogger
{
    public function __construct(private readonly DatabaseService $database)
    {
    }

    public function log(string $eventType, string $message, string $level = 'warning', ?string $ipAddress = null): void
    {
        $stmt = $this->database->connection()->prepare(
            'INSERT INTO security_log (event_type, level, message, ip_address) VALUES (:event_type, :level, :message, :ip_address)'
        );

        $stmt->execute([
            'event_type' => $eventType,
            'level' => $level,
            'message' => $message,
            'ip_address' => $ipAddress,
        ]);
    }
}
