<?php

namespace App\Azhoras\Auth\Repositories;

use App\Azhoras\Auth\Interfaces\HourBankRepositoryInterface;
use App\Azhoras\Infrastructure\Database\Connection;

class HourBankRepository implements HourBankRepositoryInterface
{
    private \PDO $pdo;

    public function __construct()
    {
        $this->pdo = Connection::getInstance();
    }

    public function save(array $data): bool
    {
        $stmt = $this->pdo->prepare("
        INSERT INTO hour_bank 
            (user_id, type, minutes, reference_date, start_time, end_time,
             is_weekend, is_holiday, is_night, additional_rate, justification, created_by, status)
        VALUES 
            (:user_id, :type, :minutes, :reference_date, :start_time, :end_time,
             :is_weekend, :is_holiday, :is_night, :additional_rate, :justification, :created_by, 'pending')
    ");

        return $stmt->execute([
            ':user_id'         => $data['user_id'],
            ':type'            => $data['type'],
            ':minutes'         => $data['minutes'],
            ':reference_date'  => $data['reference_date'],
            ':start_time'      => $data['start_time'],
            ':end_time'        => $data['end_time'],
            ':is_weekend'      => $data['is_weekend'],
            ':is_holiday'      => $data['is_holiday'],
            ':is_night'        => $data['is_night'],
            ':additional_rate' => $data['additional_rate'],
            ':justification'   => $data['justification'],
            ':created_by'      => $data['created_by'],
        ]);
    }

    public function findByUser(int $userId): array
    {
        $stmt = $this->pdo->prepare("
            SELECT h.*, 
                   CONCAT(FLOOR(h.minutes / 60), 'h ', LPAD(MOD(h.minutes, 60), 2, '0'), 'min') AS hours_formatted
            FROM hour_bank h
            WHERE h.user_id = :user_id
            ORDER BY h.reference_date DESC
        ");
        $stmt->execute([':user_id' => $userId]);
        return $stmt->fetchAll();
    }

    public function findPending(): array
    {
        $stmt = $this->pdo->prepare("
            SELECT h.*, u.name AS user_name,
                   CONCAT(FLOOR(h.minutes / 60), 'h ', LPAD(MOD(h.minutes, 60), 2, '0'), 'min') AS hours_formatted
            FROM hour_bank h
            JOIN users u ON u.id = h.user_id
            WHERE h.status = 'pending'
            ORDER BY h.created_at ASC
        ");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function approve(int $id, int $approvedBy): bool
    {
        $stmt = $this->pdo->prepare("
            UPDATE hour_bank 
            SET status = 'approved', approved_by = :approved_by, approved_at = NOW()
            WHERE id = :id AND status = 'pending'
        ");
        return $stmt->execute([':id' => $id, ':approved_by' => $approvedBy]);
    }

    public function reject(int $id, int $approvedBy): bool
    {
        $stmt = $this->pdo->prepare("
            UPDATE hour_bank 
            SET status = 'rejected', approved_by = :approved_by, approved_at = NOW()
            WHERE id = :id AND status = 'pending'
        ");
        return $stmt->execute([':id' => $id, ':approved_by' => $approvedBy]);
    }

    public function getBalance(int $userId): int
    {
        $stmt = $this->pdo->prepare("
            SELECT total_minutes FROM hour_bank_balance WHERE user_id = :user_id
        ");
        $stmt->execute([':user_id' => $userId]);
        $result = $stmt->fetch();
        return $result ? (int) $result['total_minutes'] : 0;
    }

    public function findPendingByUser(int $userId): array
    {
        $stmt = $this->pdo->prepare("
        SELECT h.*,
               u.name AS user_name,
               CONCAT(FLOOR(h.minutes / 60), 'h ', LPAD(MOD(h.minutes, 60), 2, '0'), 'min') AS hours_formatted
        FROM hour_bank h
        JOIN users u ON u.id = h.user_id
        WHERE h.user_id = :user_id AND h.status = 'pending'
        ORDER BY h.created_at ASC
    ");
        $stmt->execute([':user_id' => $userId]);
        return $stmt->fetchAll();
    }
}
