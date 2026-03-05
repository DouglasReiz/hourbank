<?php

use App\Azhoras\Auth\Controller\LoginController;
use App\Azhoras\Auth\Controller\RegisterController;
use App\Azhoras\Controller\DashboardController;
use App\Azhoras\Controller\HourBankController;
use App\Azhoras\Controller\IndexController;
use App\Azhoras\Controller\ManagerController;

return [
    '/' => createRoute(IndexController::class, 'index', 'GET', 'home'),
    '/login' => createRoute(LoginController::class, 'index', 'GET', 'login'),
    '/login/store' => createRoute(LoginController::class, 'store', 'POST', 'login.store'),
    '/register' => createRoute(RegisterController::class, 'index', 'GET', 'register'),
    '/register/store' => createRoute(RegisterController::class, 'store', 'POST', 'register.store'),
    '/logout' => createRoute(LoginController::class, 'logout', 'GET', 'logout'),
    '/dashboard' => createRoute(DashboardController::class, 'index', 'GET', 'dashboard'),

    //Rotas do banco de horas

    '/hour-bank'         => createRoute(HourBankController::class, 'index',   'GET',  'hour-bank'),
    '/hour-bank/store'   => createRoute(HourBankController::class, 'store',   'POST', 'hour-bank.store'),
    '/hour-bank/pending' => createRoute(HourBankController::class, 'pending', 'GET',  'hour-bank.pending'),
    '/hour-bank/approve' => createRoute(HourBankController::class, 'approve', 'POST', 'hour-bank.approve'),
    '/hour-bank/reject'  => createRoute(HourBankController::class, 'reject',  'POST', 'hour-bank.reject'),

    //Rota de Gestor
    '/manager/users' => createRoute(ManagerController::class, 'users', 'GET', 'manager.users'),
    '/hour-bank/pending/user/{user_id}' => createRoute(HourBankController::class, 'pendingByUser', 'GET', 'hour-bank.pending.user'),
];
