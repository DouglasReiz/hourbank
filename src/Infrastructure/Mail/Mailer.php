<?php

namespace App\Azhoras\Infrastructure\Mail;

use Exception;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\PHPMailer;

class Mailer
{
    private PHPMailer $mail;

    public function __construct()
    {
        $this->mail = new PHPMailer(true);

        $this->mail->isSMTP();
        $this->mail->Host        = $_ENV['MAIL_HOST'];
        $this->mail->SMTPAuth    = true;
        $this->mail->Username    = $_ENV['MAIL_USERNAME'];
        $this->mail->Password    = $_ENV['MAIL_PASSWORD'];
        $this->mail->SMTPSecure  = $_ENV['MAIL_ENCRYPTION'] === 'tls' ? PHPMailer::ENCRYPTION_STARTTLS : PHPMailer::ENCRYPTION_SMTPS;
        $this->mail->Port        = (int) $_ENV['MAIL_PORT'];
        $this->mail->CharSet     = 'UTF-8';

        $this->mail->setFrom($_ENV['MAIL_FROM'], $_ENV['MAIL_FROM_NAME']);
    }

    public function send(string $to, string $toName, string $subject, string $body): bool
    {
        try {
            $this->mail->clearAddresses();
            $this->mail->addAddress($to, $toName);
            $this->mail->isHTML(true);
            $this->mail->Subject = $subject;
            $this->mail->Body    = $body;
            $this->mail->send();
            return true;
        } catch (Exception $e) {
            error_log("Erro ao enviar e-mail: " . $e->getMessage());
            return false;
        }
    }
}