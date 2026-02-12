<?php

declare(strict_types=1);

namespace App\Bootstrap;

use RuntimeException;

class Container
{
    private array $bindings = [];
    private array $instances = [];

    public function bind(string $key, callable $factory): void
    {
        $this->bindings[$key] = $factory;
    }

    public function singleton(string $key, callable $factory): void
    {
        $this->bindings[$key] = function () use ($key, $factory) {
            if (!array_key_exists($key, $this->instances)) {
                $this->instances[$key] = $factory($this);
            }

            return $this->instances[$key];
        };
    }

    public function get(string $key): mixed
    {
        if (!isset($this->bindings[$key])) {
            throw new RuntimeException("Service not found: {$key}");
        }

        return ($this->bindings[$key])($this);
    }
}
