<?php

namespace App\Azhoras\Auth\Services;

use App\Azhoras\Auth\Interfaces\UserRepositoryInterface;
use Exception;

class AuthService
{
    private $repository;

    // Injeção de Dependência: O serviço depende da Interface, não da classe concreta
    public function __construct(UserRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function register(array $data): bool
    {
        if ($this->repository->findByEmail($data['email'])) {
            throw new \Exception("E-mail já cadastrado.");
        }

        return $this->repository->save([
            'name'      => $data['nome'],
            'email'     => $data['email'],
            'password'  => password_hash($data['password'], PASSWORD_BCRYPT),
            'cpf' => $data['cpf'],
            'cargo'     => $data['cargo'],
            'setor'     => $data['setor'],
        ]);
    }

    public function login(string $email, string $password): bool
    {
        $user = $this->repository->findByEmail($email);

        if (!$user || !password_verify($password, $user['password'])) {
            return false;
        }

        $_SESSION['user_id']    = $user['id'];
        $_SESSION['user_name']  = $user['name'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_cargo'] = $user['cargo'];
        $_SESSION['user_setor'] = $user['setor'];
        $_SESSION['user_role']  = $user['role'];

        return true;
    }

    public function loginOrRegisterWithMicrosoft(array $microsoftUser): bool
    {
        $email = $microsoftUser['email'];
        $name  = $microsoftUser['name'];

        $user = $this->repository->findByEmail($email);

        // Se não existe, cria automaticamente
        if (!$user) {
            $this->repository->save([
                'name'      => $name,
                'email'     => $email,
                'password'  => password_hash(bin2hex(random_bytes(16)), PASSWORD_BCRYPT), // senha aleatória
                'cpf'       => '',
                'cargo'     => 'A definir',
                'setor'     => 'ti',
            ]);

            $user = $this->repository->findByEmail($email);
        }

        if (!$user) {
            return false;
        }

        $_SESSION['user_id']    = $user['id'];
        $_SESSION['user_name']  = $user['name'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_cargo']    = $user['cargo'];
        $_SESSION['user_setor']    = $user['setor'];
        $_SESSION['user_role']     = $user['role'];
        $_SESSION['profile_complete'] = $user['cargo'] !== 'A definir';

        return true;
    }
}
