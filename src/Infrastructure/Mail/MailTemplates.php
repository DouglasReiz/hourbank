<?php

namespace App\Azhoras\Infrastructure\Mail;

class MailTemplates
{
    public static function newLaunch(string $employeeName, string $date, string $hours, string $type, string $justification): string
    {
        $typeLabel = $type === 'credit' ? 'Crédito (Horas Extras)' : 'Débito (Compensação)';

        return "
        <div style='font-family:Inter,sans-serif;max-width:600px;margin:0 auto;padding:2rem;'>
            <div style='background:#6d00ef;padding:1.5rem;border-radius:12px 12px 0 0;'>
                <h1 style='color:#fff;margin:0;font-size:1.4rem;'>⏱ Novo Lançamento de Horas</h1>
            </div>
            <div style='background:#f8fafc;padding:2rem;border:1px solid #e2e8f0;border-radius:0 0 12px 12px;'>
                <p style='color:#64748b;margin-bottom:1.5rem;'>
                    Um novo lançamento foi registrado e aguarda sua aprovação.
                </p>
                <table style='width:100%;border-collapse:collapse;'>
                    <tr>
                        <td style='padding:0.75rem;border-bottom:1px solid #e2e8f0;font-weight:600;color:#6d00ef;width:40%;'>Colaborador</td>
                        <td style='padding:0.75rem;border-bottom:1px solid #e2e8f0;color:#09090a;'>{$employeeName}</td>
                    </tr>
                    <tr>
                        <td style='padding:0.75rem;border-bottom:1px solid #e2e8f0;font-weight:600;color:#6d00ef;'>Data</td>
                        <td style='padding:0.75rem;border-bottom:1px solid #e2e8f0;color:#09090a;'>{$date}</td>
                    </tr>
                    <tr>
                        <td style='padding:0.75rem;border-bottom:1px solid #e2e8f0;font-weight:600;color:#6d00ef;'>Total</td>
                        <td style='padding:0.75rem;border-bottom:1px solid #e2e8f0;color:#09090a;'>{$hours}</td>
                    </tr>
                    <tr>
                        <td style='padding:0.75rem;border-bottom:1px solid #e2e8f0;font-weight:600;color:#6d00ef;'>Tipo</td>
                        <td style='padding:0.75rem;border-bottom:1px solid #e2e8f0;color:#09090a;'>{$typeLabel}</td>
                    </tr>
                    <tr>
                        <td style='padding:0.75rem;font-weight:600;color:#6d00ef;'>Justificativa</td>
                        <td style='padding:0.75rem;color:#09090a;'>{$justification}</td>
                    </tr>
                </table>
                <div style='margin-top:2rem;text-align:center;'>
                    <a href='" . $_ENV['APP_URL'] . "/hour-bank/pending'
                       style='background:#6d00ef;color:#fff;padding:0.8rem 2rem;border-radius:8px;text-decoration:none;font-weight:600;'>
                        Ver Pendentes
                    </a>
                </div>
                <p style='margin-top:2rem;font-size:0.85rem;color:#64748b;text-align:center;'>
                    Sistema Azhoras — Este é um e-mail automático.
                </p>
            </div>
        </div>";
    }
}