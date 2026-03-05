<?php

namespace App\Azhoras\Controller;

use App\Azhoras\Auth\Repositories\HourBankRepository;
use App\Azhoras\Auth\Services\HourBankService;
use App\Azhoras\Http\Middleware\AuthMiddleware;
use App\Azhoras\Http\RequestHandler;

class DashboardController extends AbstractController
{
    public function __construct(
        RequestHandler $request,
        private readonly AuthMiddleware $middleware,
        private readonly HourBankRepository  $hourBankRepository,
        private readonly HourBankService     $hourBankService,
    ) {
        parent::__construct($request);
    }

    public function index(): void
    {
        $this->middleware->handle(); // redireciona para /login se não autenticado

        $userId  = $_SESSION['user_id'];

        $user = [
            'id'    => $_SESSION['user_id'],
            'name'  => $_SESSION['user_name'],
            'email' => $_SESSION['user_email'],
        ];

        // Busca os 5 lançamentos mais recentes
        $entries = array_slice($this->hourBankRepository->findByUser($userId), 0, 5);
        $balance = $this->hourBankService->getBalanceFormatted($userId);

        $this->render('dashboard', compact('user', 'entries', 'balance'));
    }
}
