<?php

declare(strict_types=1);

namespace App;

use ReflectionFunction;
use ReflectionMethod;
use RuntimeException;

class Router
{
    private array $routes = [];
    private array $groupStack = [];

    public function get(string $path, callable|array $handler, array $middleware = []): void
    {
        $this->match(['GET'], $path, $handler, $middleware);
    }

    public function post(string $path, callable|array $handler, array $middleware = []): void
    {
        $this->match(['POST'], $path, $handler, $middleware);
    }

    public function match(array $methods, string $path, callable|array $handler, array $middleware = []): void
    {
        [$fullPath, $groupMiddleware] = $this->resolveGroupContext($path);

        foreach ($methods as $method) {
            $this->routes[strtoupper($method)][] = [
                'pattern' => $fullPath,
                'handler' => $handler,
                'middleware' => [...$groupMiddleware, ...$middleware],
            ];
        }
    }

    public function group(string $prefix, array $middleware, callable $register): void
    {
        $this->groupStack[] = ['prefix' => $this->normalize($prefix), 'middleware' => $middleware];
        $register($this);
        array_pop($this->groupStack);
    }

    public function dispatch(string $method, string $uri): mixed
    {
        $path = $this->normalize((string) (parse_url($uri, PHP_URL_PATH) ?: '/'));

        foreach ($this->routes[strtoupper($method)] ?? [] as $route) {
            $params = $this->extractParams($route['pattern'], $path);
            if ($params === null) {
                continue;
            }

            foreach ($route['middleware'] as $mw) {
                $this->invokeCallable($mw, $params);
            }

            return $this->resolveHandler($route['handler'], $params);
        }

        http_response_code(404);
        return view('pages/error', ['status' => 404, 'message' => 'Страница не найдена']);
    }

    private function resolveGroupContext(string $path): array
    {
        $prefix = '';
        $middleware = [];

        foreach ($this->groupStack as $group) {
            $prefix .= $group['prefix'];
            $middleware = [...$middleware, ...$group['middleware']];
        }

        $fullPath = rtrim($prefix, '/') . '/' . ltrim($path, '/');
        return [$this->normalize($fullPath), $middleware];
    }

    private function normalize(string $path): string
    {
        $trimmed = '/' . trim($path, '/');
        $normalized = preg_replace('#/+#', '/', $trimmed) ?: '/';
        return $normalized === '' ? '/' : $normalized;
    }

    private function extractParams(string $pattern, string $path): ?array
    {
        $regex = preg_replace('/\{([a-zA-Z_][a-zA-Z0-9_-]*)\}/', '(?P<$1>[^/]+)', $pattern);
        $regex = '#^' . $regex . '$#';

        if (!preg_match($regex, $path, $matches)) {
            return null;
        }

        return array_filter($matches, static fn ($k) => !is_int($k), ARRAY_FILTER_USE_KEY);
    }

    private function resolveHandler(callable|array $handler, array $params): mixed
    {
        if (is_callable($handler)) {
            return $this->invokeCallable($handler, $params);
        }

        [$class, $method] = $handler;
        if (!class_exists($class)) {
            throw new RuntimeException("Controller not found: {$class}");
        }

        $controller = new $class();
        if (!method_exists($controller, $method)) {
            throw new RuntimeException("Method {$method} not found in {$class}");
        }

        $reflection = new ReflectionMethod($controller, $method);
        return $reflection->getNumberOfParameters() > 0
            ? $controller->{$method}($params)
            : $controller->{$method}();
    }

    private function invokeCallable(callable $callable, array $params): mixed
    {
        $reflection = new ReflectionFunction(\Closure::fromCallable($callable));
        return $reflection->getNumberOfParameters() > 0 ? $callable($params) : $callable();
    }
}
