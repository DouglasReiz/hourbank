<?php

namespace App\Azhoras\Auth\Models;

class User
{
    public function __construct(
        public readonly string  $email,
        public readonly string  $password,
        public readonly ?int    $id   = null,
        public readonly ?string $name = null,
    ) {}
}