<?php

namespace App\Azhoras\Http\Middleware;

class AuthMiddleware
{
    public function handle(): void
    {

        if (empty($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }
    }
}