<?php

namespace App\Azhoras\Auth\Validators;

class HourBankValidator
{
    private const MAX_EXTRA_MINUTES_PER_DAY = 120; // 2h em minutos
    private const NIGHT_HOUR_START          = 22;
    private const NIGHT_HOUR_END            = 5;

    public function validate(array $data): array
    {
        $errors = [];

        if (empty($data['reference_date']) || !strtotime($data['reference_date'])) {
            $errors[] = "Data inválida.";
        }

        if (empty($data['start_time']) || empty($data['end_time'])) {
            $errors[] = "Horário de início e término são obrigatórios.";
        }

        if (!in_array($data['type'] ?? '', ['credit', 'debit'])) {
            $errors[] = "Tipo inválido.";
        }

        if (empty($data['justification'])) {
            $errors[] = "Justificativa é obrigatória.";
        }

        // Valida limite de 2h extras por dia
        if (!empty($data['start_time']) && !empty($data['end_time'])) {
            $minutes = $this->calculateMinutes($data['start_time'], $data['end_time']);

            if ($minutes <= 0) {
                $errors[] = "O horário de término deve ser maior que o início.";
            }

            if ($data['type'] === 'credit' && $minutes > self::MAX_EXTRA_MINUTES_PER_DAY) {
                $errors[] = "Limite de 2h extras por dia excedido (CLT Art. 59). Em casos de força maior informe no campo de justificativa e aguarde aprovação do gestor.";
            }
        }

        return $errors;
    }

    public function calculateMinutes(string $start, string $end): int
    {
        $startTime = strtotime($start);
        $endTime   = strtotime($end);
        return (int) (($endTime - $startTime) / 60);
    }

    public function isNightShift(string $start, string $end): bool
    {
        $startHour = (int) date('H', strtotime($start));
        $endHour   = (int) date('H', strtotime($end));

        return $startHour >= self::NIGHT_HOUR_START || $endHour <= self::NIGHT_HOUR_END;
    }
}
