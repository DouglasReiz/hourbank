<?php

namespace App\Azhoras\Http\Middleware;

class ManagerMiddleware
{
    public function handle(): void
    {
        if (empty($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }

        if (!in_array($_SESSION['user_role'], ['manager', 'admin'])) {
            http_response_code(403);
            // redireciona para dashboard com erro
            header('Location: /dashboard?error=unauthorized');
            exit;
        }
    }
}