<?php

namespace App\Azhoras\Controller;

use App\Azhoras\Auth\Repositories\UserRepository;
use App\Azhoras\Auth\Validators\ProfileValidator;
use App\Azhoras\Http\Middleware\AuthMiddleware;
use App\Azhoras\Http\RequestHandler;

class ProfileController extends AbstractController
{
    public function __construct(
        RequestHandler               $request,
        private readonly AuthMiddleware   $middleware,
        private readonly UserRepository   $userRepository,
        private readonly ProfileValidator $validator,
    ) {
        parent::__construct($request);
    }

    public function index(): void
    {
        $this->middleware->handle();
        $this->render('profile/complete');
    }

    public function store(): void
    {
        $this->middleware->handle();

        $data   = $this->request->all();
        $errors = $this->validator->validate($data);

        if (!empty($errors)) {
            $this->render('profile/complete', ['errors' => $errors]);
            return;
        }

        try {
            $this->userRepository->update($_SESSION['user_id'], [
                'cargo' => $data['cargo'],
                'setor' => $data['setor'],
                'cpf'   => $data['cpf'],
            ]);

            // Atualiza a sessão
            $_SESSION['user_cargo']        = $data['cargo'];
            $_SESSION['user_setor']        = $data['setor'];
            $_SESSION['profile_complete']  = true;

            $this->redirect('/dashboard');
        } catch (\Exception $e) {
            $this->render('profile/complete', ['errors' => [$e->getMessage()]]);
        }
    }
}
