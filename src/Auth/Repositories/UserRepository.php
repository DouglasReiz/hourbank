<?php

namespace App\Azhoras\Auth\Repositories;

use App\Azhoras\Auth\Interfaces\UserRepositoryInterface;
use App\Azhoras\Infrastructure\Database\Connection;

class UserRepository implements UserRepositoryInterface
{
    private \PDO $pdo;

    public function __construct()
    {
        $this->pdo = Connection::getInstance();
    }

    public function findByEmail(string $email): ?array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE email = :email LIMIT 1");
        $stmt->execute([':email' => $email]);
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);

        return $result ?: null;
    }

    public function save(array $data): bool
    {
        $stmt = $this->pdo->prepare("
        INSERT INTO users (name, email, password, cpf, cargo, setor)
        VALUES (:name, :email, :password, :cpf, :cargo, :setor)
        ");

        return $stmt->execute([
            ':name'      => $data['name'],
            ':email'     => $data['email'],
            ':password'  => $data['password'],
            ':cpf'       => $data['cpf'],
            ':cargo'     => $data['cargo'],
            ':setor'     => $data['setor'],
        ]);
    }

    public function findAll(): array
    {
        $stmt = $this->pdo->prepare("
        SELECT 
            u.id,
            u.name,
            u.email,
            u.cargo,
            u.setor,
            u.role,
            COUNT(h.id) AS total_lancamentos,
            SUM(CASE WHEN h.status = 'pending' THEN 1 ELSE 0 END) AS total_pendentes,
            b.total_minutes
        FROM users u
        LEFT JOIN hour_bank h ON h.user_id = u.id
        LEFT JOIN hour_bank_balance b ON b.user_id = u.id
        GROUP BY u.id
        ORDER BY u.name ASC
    ");
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
