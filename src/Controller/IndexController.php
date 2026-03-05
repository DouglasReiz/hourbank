<?php

namespace App\Azhoras\Controller;

use App\Azhoras\Http\RequestHandler;

class IndexController extends AbstractController
{
    protected RequestHandler $request;

    public function __construct(RequestHandler $request)
    {
        $this->request = $request;
    }

    public function index(): void
    {
        $this->render('index');
    }

    public function toDashboard():void
    {
        $this->render('dashboard');
    }
}
