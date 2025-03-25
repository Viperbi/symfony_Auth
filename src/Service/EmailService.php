<?php

namespace App\Service;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

final class EmailService
{
    private PHPMailer $mailer;
    public function __construct(
        private readonly string $username,
        private readonly string $password,
        private readonly string $smtp,
        private readonly string $port,
    ) {
        $this->mailer = new PHPMailer(true);
    }

    /**
     * Méthode pour envoyer des emails
     * 
     * @param string $receiver param qui va recevoir l'adresse du destinataire du mail
     * @param string $subject param qui va recevoir le sujet du mail
     * @param string $body param qui va recevoir le contenu du mail
     * 
     */
    public function sendEmail(string $receiver, string $subject, string $body): void
    {
        try {
            $this->config();
            $this->mailer->setFrom($this->username, 'Mailer');
            $this->mailer->addAddress($receiver);
            $this->mailer->isHTML(true);
            $this->mailer->Subject = $subject;
            $this->mailer->Body    = $body;
            $this->mailer->send();
        } catch (Exception $e) {
            echo "Message could not be sent. Mailer Error: {$this->mailer->ErrorInfo}";
        }
    }

    private function config(): void
    {
        $this->mailer->SMTPDebug = SMTP::DEBUG_SERVER;
        $this->mailer->isSMTP();
        $this->mailer->Host       = $this->smtp;
        $this->mailer->SMTPAuth   = true;
        $this->mailer->Username   = $this->username;
        $this->mailer->Password   = $this->password;
        $this->mailer->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $this->mailer->Port       = $this->port;
    }
}
