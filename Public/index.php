<?php

use App\Azhoras\Http\RequestHandler;

session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

require_once __DIR__ . '/../Config/helpers.php';

$container = require __DIR__ . '/../src/Container/bootstrap.php';

try {
    $routes = require __DIR__ . '/../Config/routes.php';

    if (!is_array($routes)) {
        throw new \RuntimeException("O arquivo de rotas não retornou um array válido.");
    }

    $url    = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $url    = ($url !== '/') ? rtrim($url, '/') : '/';
    $method = $_SERVER['REQUEST_METHOD'];
    $matchedRoute  = null;
    $routeParams   = [];

    foreach ($routes as $pattern => $route) {
        $regex = preg_replace('/\{([a-zA-Z_]+)\}/', '([^/]+)', $pattern);
        $regex = '#^' . $regex . '$#';

        if (preg_match($regex, $url, $matches) && strtoupper($route['method']) === strtoupper($method)) {
            array_shift($matches); // remove o match completo

            // extrai os nomes dos parâmetros
            preg_match_all('/\{([a-zA-Z_]+)\}/', $pattern, $paramNames);
            $routeParams  = array_combine($paramNames[1], $matches);
            $matchedRoute = $route;
            break;
        }
    }

    if (!$matchedRoute) {
        http_response_code(404);
        echo "Rota não encontrada.";
        exit;
    }

    // Injeta os parâmetros no RequestHandler
    $request = $container->make(\App\Azhoras\Http\RequestHandler::class);
    $request->setRouteParams($routeParams);

    $controller = $container->make($matchedRoute['controller']);
    $action     = $matchedRoute['action'];
    $controller->$action();
} catch (\Exception $e) {
    http_response_code(500);
    echo "Erro: " . $e->getMessage();
    error_log("Exception: " . $e->getMessage());
    error_log("Stack: " . $e->getTraceAsString());
}
