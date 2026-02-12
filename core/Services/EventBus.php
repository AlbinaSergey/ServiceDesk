<?php

declare(strict_types=1);

namespace App\Services;

class EventBus
{
    private array $listeners = [];

    public function __construct(private readonly ErrorHandler $errorHandler)
    {
    }

    public function listen(string $event, callable $listener): void
    {
        $this->listeners[$event][] = $listener;
    }

    public function dispatch(string $event, array $payload = []): void
    {
        foreach ($this->listeners[$event] ?? [] as $listener) {
            try {
                $listener($payload);
            } catch (\Throwable $exception) {
                $this->errorHandler->logError($exception, ['event' => $event]);
            }
        }
    }
}
