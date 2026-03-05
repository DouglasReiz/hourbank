<?php

declare(strict_types=1);

namespace App\Azhoras\Controller;

use App\Azhoras\Http\RequestHandler;

abstract class AbstractController
{
    protected RequestHandler $request;

    public function __construct(RequestHandler $request)
    {
        $this->request = $request;
    }

    protected function render(string $viewName, array $data = []): void
    {
        $path = dirname(__DIR__) . "/View/{$viewName}.php";

        if (!file_exists($path)) {
            throw new \RuntimeException("View '{$viewName}' não encontrada em: {$path}");
        }

        // Extrai as variáveis no escopo atual
        extract($data, EXTR_SKIP);

        include dirname(__DIR__) . "/View/_partials/head.php";
        include $path;
        include dirname(__DIR__) . "/View/_partials/footer.php";
    }

    protected function redirect(string $url): void
    {
        $url = trim($url); // ← remove espaços e quebras de linha
        header("Location: {$url}");
        exit;
    }
}
