<?php

namespace App\Azhoras\Auth\Interfaces;

interface HourBankRepositoryInterface
{
    public function save(array $data): bool;
    public function findByUser(int $userId): array;
    public function findPending(): array;
    public function approve(int $id, int $approvedBy): bool;
    public function reject(int $id, int $approvedBy): bool;
    public function getBalance(int $userId): int;
}
