<?php

namespace App\Azhoras\Auth\Validators;

class ProfileValidator
{
    public function validate(array $data): array
    {
        $errors = [];

        if (empty($data['cpf'])) {
            $errors[] = "CPF é obrigatório.";
        }

        if (empty($data['cargo'])) {
            $errors[] = "Cargo é obrigatório.";
        }

        if (empty($data['setor'])) {
            $errors[] = "Setor é obrigatório.";
        }

        return $errors;
    }
}
