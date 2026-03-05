<?php

use App\Azhoras\Auth\Interfaces\HourBankRepositoryInterface;
use App\Azhoras\Container\Container;
use App\Azhoras\Auth\Interfaces\UserRepositoryInterface;
use App\Azhoras\Auth\Repositories\HourBankRepository;
use App\Azhoras\Auth\Repositories\UserRepository;
use App\Azhoras\Http\Middleware\ManagerMiddleware;
use App\Azhoras\Http\RequestHandler;
use App\Azhoras\Infrastructure\Mail\Mailer;

$container = new Container();

// Liga a interface à implementação concreta
$container->bind(UserRepositoryInterface::class, fn($c) => new UserRepository());

$container->bind(HourBankRepositoryInterface::class, fn() => new HourBankRepository());

$container->bind(ManagerMiddleware::class, fn() => new ManagerMiddleware());

$request = new RequestHandler();
$container->bind(RequestHandler::class, fn() => $request);

$container->bind(Mailer::class, fn() => new Mailer());

return $container;