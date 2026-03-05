<?php

namespace App\Azhoras\Auth\Controller;

use App\Azhoras\Auth\Services\AuthService;
use App\Azhoras\Auth\Validators\AuthValidator;
use App\Azhoras\Controller\AbstractController;
use App\Azhoras\Http\RequestHandler;

class RegisterController extends AbstractController
{
    public function __construct(
        RequestHandler $request,
        private readonly AuthService   $authService,
        private readonly AuthValidator $validator,
    ) {
        parent::__construct($request);
    }

    public function index(): void
    {
        $this->render('auth/registerPage');
    }

    public function store(): void
    {
        $data   = $this->request->all();
        $errors = $this->validator->validate($data);

        if (!empty($errors)) {
            $this->render('auth/registerPage', ['errors' => $errors]);
            return;
        }

        try {
            $this->authService->register($data);
            $this->redirect('/login');
        } catch (\Exception $e) {
            $this->render('auth/registerPage', ['errors' => [$e->getMessage()]]);
        }
    }
}
