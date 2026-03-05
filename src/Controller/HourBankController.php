<?php

namespace App\Azhoras\Controller;

use App\Azhoras\Auth\Repositories\HourBankRepository;
use App\Azhoras\Auth\Services\HourBankService;
use App\Azhoras\Auth\Validators\HourBankValidator;
use App\Azhoras\Http\Middleware\AuthMiddleware;
use App\Azhoras\Http\Middleware\ManagerMiddleware;
use App\Azhoras\Http\RequestHandler;

class HourBankController extends AbstractController
{
    public function __construct(
        RequestHandler               $request,
        private readonly AuthMiddleware    $middleware,
        private readonly ManagerMiddleware $managerMiddleware,
        private readonly HourBankService   $service,
        private readonly HourBankValidator $validator,
        private readonly HourBankRepository $repository,
    ) {
        parent::__construct($request);
    }

    public function index(): void
    {
        $this->middleware->handle();

        $userId  = $_SESSION['user_id'];
        $entries = $this->repository->findByUser($userId);
        $balance = $this->service->getBalanceFormatted($userId);

        $this->render('hour-bank/index', compact('entries', 'balance'));
    }

    public function store(): void
    {
        $this->middleware->handle();

        $data   = $this->request->all();
        $errors = $this->validator->validate($data);

        if (!empty($errors)) {
            $userId  = $_SESSION['user_id'];
            $entries = $this->repository->findByUser($userId);
            $balance = $this->service->getBalanceFormatted($userId);
            $this->render('hour-bank/index', compact('entries', 'balance', 'errors'));
            return;
        }

        try {
            $this->service->launch($data, $_SESSION['user_id']);
            $this->redirect('/hour-bank');
        } catch (\Exception $e) {
            $userId  = $_SESSION['user_id'];
            $entries = $this->repository->findByUser($userId);
            $balance = $this->service->getBalanceFormatted($userId);
            $errors  = [$e->getMessage()];
            $this->render('hour-bank/index', compact('entries', 'balance', 'errors'));
        }
    }

    public function pending(): void
    {
        $this->managerMiddleware->handle(); // ← apenas gestores
        $entries = $this->repository->findPending();
        $this->render('hour-bank/pending', compact('entries'));
    }

    public function approve(): void
    {
        $this->managerMiddleware->handle();
        $id       = (int) $this->request->input('id');
        $redirect = trim(str_replace(["\n", "\r", " "], '', $this->request->input('redirect') ?? '/hour-bank/pending'));
        $this->service->approve($id, $_SESSION['user_id']);
        $this->redirect($redirect);
    }

    public function reject(): void
    {
        $this->managerMiddleware->handle();
        $id       = (int) $this->request->input('id');
        $redirect = trim(str_replace(["\n", "\r", " "], '', $this->request->input('redirect') ?? '/hour-bank/pending'));
        $this->service->reject($id, $_SESSION['user_id']);
        $this->redirect($redirect);
    }

    public function pendingByUser(): void
    {
        $this->managerMiddleware->handle();

        $userId  = (int) $this->request->param('user_id');

        if (!$userId) {
            $this->redirect('/manager/users');
        }

        $entries  = $this->repository->findPendingByUser($userId);
        $userName = !empty($entries) ? $entries[0]['user_name'] : 'Colaborador';

        $this->render('hour-bank/pending-user', compact('entries', 'userName', 'userId'));
    }
}
