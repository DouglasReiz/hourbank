<?php

namespace App\Azhoras\Auth\Interfaces;

interface UserRepositoryInterface{
    public function findByEmail(string $email): ?array;
    public function save(array $data): bool;
}