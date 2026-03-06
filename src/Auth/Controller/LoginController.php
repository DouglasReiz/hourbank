<?php

namespace App\Azhoras\Auth\Controller;

use App\Azhoras\Controller\AbstractController;
use App\Azhoras\Auth\Services\AuthService;
use App\Azhoras\Auth\Services\MicrosoftAuthService;
use App\Azhoras\Http\RequestHandler;

class LoginController extends AbstractController
{
    public function __construct(
        RequestHandler           $request,
        private readonly AuthService $authService,
        private readonly MicrosoftAuthService $microsoftAuthService,
    ) {
        parent::__construct($request);
    }

    public function index(): void
    {
        $this->render('auth/loginPage');
    }

    // Redireciona para a tela de login da Microsoft
    public function microsoftRedirect(): void
    {
        $url = $this->microsoftAuthService->getAuthorizationUrl();
        $this->redirect($url);
    }

    // Callback após login na Microsoft
    public function microsoftCallback(): void
    {
        $code  = $this->request->get('code');
        $state = $this->request->get('state');

        if (!$code) {
            $this->render('auth/loginPage', ['errors' => ['Login com Microsoft cancelado.']]);
            return;
        }

        try {
            $microsoftUser = $this->microsoftAuthService->handleCallback($code, $state);
            $success       = $this->authService->loginOrRegisterWithMicrosoft($microsoftUser);

            if (!$success) {
                $this->render('auth/loginPage', ['errors' => ['Não foi possível autenticar com a Microsoft.']]);
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
