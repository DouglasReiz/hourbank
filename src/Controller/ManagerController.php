<?php

namespace App\Azhoras\Controller;

use App\Azhoras\Http\Middleware\ManagerMiddleware;
use App\Azhoras\Http\RequestHandler;
use App\Azhoras\Auth\Repositories\UserRepository;

class ManagerController extends AbstractController
{
    public function __construct(
        RequestHandler               $request,
        private readonly ManagerMiddleware $middleware,
        private readonly UserRepository    $userRepository,
    ) {
        parent::__construct($request);
    }

    public function users(): void
    {
        $this->middleware->handle();

        $users = $this->userRepository->findAll();

        $this->render('manager/users', compact('users'));
    }
}
