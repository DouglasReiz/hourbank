<?php

if (!function_exists('createRoute')) {
    function createRoute(string $controller, string $action, string $method = 'GET', ?string $name = null): array
    {
        return [
            'method'     => $method,
            'controller' => $controller,
            'action'     => $action,
            'name'       => $name,
        ];
    }
}

function route(string $name): string
{
    static $namedRoutes = null;

    if ($namedRoutes === null) {
        $namedRoutes = include __DIR__ . '/named_routes.php';
    }

    if (!isset($namedRoutes[$name])) {
        throw new \InvalidArgumentException("Rota nomeada '{$name}' não encontrada.");
    }

    return $namedRoutes[$name];
}