<?php

declare(strict_types=1);

namespace App\Services;

class AuditService
{
    public function __construct(private readonly DatabaseService $database)
    {
    }

    public function log(
        string $action,
        string $entityType,
        string|int|null $entityId = null,
        ?int $actorId = null,
        array $payload = []
    ): void {
        $stmt = $this->database->connection()->prepare(
            'INSERT INTO audit_log (actor_id, action, entity_type, entity_id, payload) VALUES (:actor_id, :action, :entity_type, :entity_id, :payload)'
        );

        $stmt->execute([
            'actor_id' => $actorId,
            'action' => $action,
            'entity_type' => $entityType,
            'entity_id' => $entityId === null ? null : (string) $entityId,
            'payload' => $payload === [] ? null : json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
        ]);
    }
}
