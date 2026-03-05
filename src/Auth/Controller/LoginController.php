<?php

namespace App\Azhoras\Auth\Controller;

use App\Azhoras\Controller\AbstractController;
use App\Azhoras\Auth\Services\AuthService;
use App\Azhoras\Http\RequestHandler;

class LoginController extends AbstractController
{
    public function __construct(
        RequestHandler           $request,
        private readonly AuthService $authService,
    ) {
        parent::__construct($request);
    }

    public function index(): void
    {
        $this->render('auth/loginPage');
    }

    public function store(): void
    {
        $data = $this->request->all();

        if (empty($data['email']) || empty($data['password'])) {
            $this->render('auth/loginPage', ['errors' => ['Preencha todos os campos.']]);
            return;
        }

        try {
            $success = $this->authService->login($data['email'], $data['password']);

            if (!$success) {
                $this->render('auth/loginPage', ['errors' => ['E-mail ou senha inválidos.']]);
                return;
            }

            $this->redirect('/dashboard');
        } catch (\Exception $e) {
            $this->render('auth/loginPage', ['errors' => [$e->getMessage()]]);
        }
    }

    public function logout(): void
    {
        session_destroy();
        $this->redirect('/login');
    }
}