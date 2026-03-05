<?php

namespace App\Azhoras\Auth\Services;

use App\Azhoras\Auth\Interfaces\HourBankRepositoryInterface;
use App\Azhoras\Auth\Repositories\UserRepository;
use App\Azhoras\Auth\Validators\HourBankValidator;
use App\Azhoras\Infrastructure\Mail\Mailer;
use App\Azhoras\Infrastructure\Mail\MailTemplates;

class HourBankService
{
    // Adicional CLT
    private const RATE_WEEKDAY  = 1.50; // 50% adicional dias úteis
    private const RATE_WEEKEND  = 2.00; // 100% adicional domingos/feriados
    private const RATE_NIGHT    = 1.20; // 20% adicional noturno

    public function __construct(
        private readonly HourBankRepositoryInterface $repository,
        private readonly HourBankValidator           $validator,
        private readonly UserRepository              $userRepository,
        private readonly Mailer                      $mailer,
    ) {}

    public function launch(array $data, int $userId): bool
    {
        $minutes   = $this->validator->calculateMinutes($data['start_time'], $data['end_time']);
        $isWeekend = $this->isWeekend($data['reference_date']);
        $isHoliday = !empty($data['is_holiday']);
        $isNight   = $this->validator->isNightShift($data['start_time'], $data['end_time']);

        if ($minutes <= 0) {
            throw new \InvalidArgumentException("O total de horas deve ser maior que zero.");
        }

        $rate = self::RATE_WEEKDAY;
        if ($isWeekend || $isHoliday) $rate = self::RATE_WEEKEND;
        if ($isNight) $rate *= self::RATE_NIGHT;

        if ($data['type'] === 'debit') {
            $balance = $this->repository->getBalance($userId);
            if ($balance < $minutes) {
                throw new \RuntimeException("Saldo insuficiente. Saldo atual: " . $this->formatMinutes($balance));
            }
        }

        $saved = $this->repository->save([
            'user_id'         => $userId,
            'type'            => $data['type'],
            'minutes'         => $minutes,
            'reference_date'  => $data['reference_date'],
            'start_time'      => $data['start_time'],
            'end_time'        => $data['end_time'],
            'is_weekend'      => $isWeekend ? 1 : 0,
            'is_holiday'      => $isHoliday ? 1 : 0,
            'is_night'        => $isNight   ? 1 : 0,
            'additional_rate' => $rate,
            'justification'   => $data['justification'],
            'created_by'      => $userId,
        ]);

        if ($saved) {
            $this->notifyManagers($userId, $data, $minutes);
        }

        return $saved;
    }

    private function notifyManagers(int $userId, array $data, int $minutes): void
    {
        $managers  = $this->userRepository->findManagers();
        $employee  = $this->userRepository->findById($userId);

        if (!$employee) return;

        $body = MailTemplates::newLaunch(
            employeeName: $employee['name'],
            date: date('d/m/Y', strtotime($data['reference_date'])),
            hours: $this->formatMinutes($minutes),
            type: $data['type'],
            justification: $data['justification'],
        );

        foreach ($managers as $manager) {
            $this->mailer->send(
                to: $manager['email'],
                toName: $manager['name'],
                subject: "⏱ Novo lançamento de {$employee['name']} aguarda aprovação",
                body: $body,
            );
        }
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
