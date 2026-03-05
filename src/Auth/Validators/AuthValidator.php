<?php

namespace App\Azhoras\Auth\Validators;

class AuthValidator
{
    public function validate(array $data): array
    {
        $errors = [];

        if (empty($data['nome'])) {
            $errors[] = "Nome é obrigatório.";
        }

        if (empty($data['cpf'])) {
            $errors[] = "CPF é obrigatório.";
        }

        if (empty($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = "E-mail inválido.";
        }

        if (empty($data['cargo'])) {
            $errors[] = "Cargo é obrigatório.";
        }

        if (empty($data['setor'])) {
            $errors[] = "Setor é obrigatório.";
        }

        if (empty($data['password']) || strlen($data['password']) < 8) {
            $errors[] = "A senha deve ter pelo menos 8 caracteres.";
        }

        if ($data['password'] !== $data['password_confirmation']) {
            $errors[] = "As senhas não conferem.";
        }

        return $errors;
    }
}
