<?php

namespace App\Azhoras\Auth\Services;

use App\Azhoras\Auth\Interfaces\HourBankRepositoryInterface;
use App\Azhoras\Auth\Validators\HourBankValidator;

class HourBankService
{
    // Adicional CLT
    private const RATE_WEEKDAY  = 1.50; // 50% adicional dias úteis
    private const RATE_WEEKEND  = 2.00; // 100% adicional domingos/feriados
    private const RATE_NIGHT    = 1.20; // 20% adicional noturno

    public function __construct(
        private readonly HourBankRepositoryInterface $repository,
        private readonly HourBankValidator           $validator,
    ) {}

    public function launch(array $data, int $userId): bool
    {
        $minutes    = $this->validator->calculateMinutes($data['start_time'], $data['end_time']);
        $isWeekend  = $this->isWeekend($data['reference_date']);
        $isHoliday  = !empty($data['is_holiday']);
        $isNight    = $this->validator->isNightShift($data['start_time'], $data['end_time']);

        if ($minutes <= 0) {
            throw new \InvalidArgumentException("O total de horas deve ser maior que zero.");
        }

        // Define adicional conforme CLT
        $rate = self::RATE_WEEKDAY;

        if ($isWeekend || $isHoliday) {
            $rate = self::RATE_WEEKEND;
        }

        if ($isNight) {
            $rate *= self::RATE_NIGHT; // acumula adicional noturno
        }

        // Verifica saldo antes de lançar débito
        if ($data['type'] === 'debit') {
            $balance = $this->repository->getBalance($userId);
            if ($balance < $minutes) {
                throw new \RuntimeException(
                    "Saldo insuficiente. Saldo atual: " . $this->formatMinutes($balance)
                );
            }
        }

        return $this->repository->save([
            'user_id'        => $userId,
            'type'           => $data['type'],
            'minutes'        => $minutes,
            'reference_date' => $data['reference_date'],
            'start_time'     => $data['start_time'],
            'end_time'       => $data['end_time'],
            'is_weekend'     => $isWeekend ? 1 : 0,
            'is_holiday'     => $isHoliday ? 1 : 0,
            'is_night'       => $isNight   ? 1 : 0,
            'additional_rate' => $rate,
            'justification'  => $data['justification'],
            'created_by'     => $userId,
        ]);
    }

    public function approve(int $id, int $approvedBy): bool
    {
        return $this->repository->approve($id, $approvedBy);
    }

    public function reject(int $id, int $approvedBy): bool
    {
        return $this->repository->reject($id, $approvedBy);
    }

    public function getBalanceFormatted(int $userId): string
    {
        return $this->formatMinutes($this->repository->getBalance($userId));
    }

    private function isWeekend(string $date): bool
    {
        $dayOfWeek = (int) date('N', strtotime($date)); // 6 = sábado, 7 = domingo
        return $dayOfWeek >= 6;
    }

    private function formatMinutes(int $minutes): string
    {
        return sprintf('%dh %02dmin', intdiv($minutes, 60), $minutes % 60);
    }
}
