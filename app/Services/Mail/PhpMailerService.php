<?php

namespace App\Services\Mail;

use PHPMailer\PHPMailer\Exception as PhpMailerException;
use PHPMailer\PHPMailer\PHPMailer;

/**
 * PHPMailer over SMTP — primary outbound transport for the app.
 * Configure via MAIL_PHPMAILER_* in .env.
 */
class PhpMailerService
{
    public function isConfigured(): bool
    {
        if (! filter_var(config('mail.phpmailer.enabled'), FILTER_VALIDATE_BOOL)) {
            return false;
        }

        $host = trim((string) config('mail.phpmailer.host'));

        return $host !== '';
    }

    public function send(
        string $toEmail,
        string $toName,
        string $subject,
        string $html,
        ?string $replyToEmail = null,
        ?string $replyToName = null,
    ): void {
        $mail = new PHPMailer(true);

        try {
            $mail->CharSet = PHPMailer::CHARSET_UTF8;
            $mail->isSMTP();
            $mail->Host = (string) config('mail.phpmailer.host');
            $mail->Port = (int) config('mail.phpmailer.port');
            $mail->SMTPAuth = true;
            $mail->Username = (string) config('mail.phpmailer.username');
            $mail->Password = (string) config('mail.phpmailer.password');

            $encryption = strtolower(trim((string) config('mail.phpmailer.encryption')));
            if ($encryption === 'ssl') {
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            } elseif ($encryption === 'tls') {
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            } else {
                $mail->SMTPSecure = false;
                $mail->SMTPAutoTLS = false;
            }

            $fromAddress = (string) config('mail.from.address');
            $fromName = (string) config('mail.from.name');

            $mail->setFrom($fromAddress, $fromName);
            $mail->addAddress($toEmail, $toName);

            if ($replyToEmail) {
                $mail->addReplyTo($replyToEmail, $replyToName ?: $replyToEmail);
            }

            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = $html;
            $mail->AltBody = strip_tags($html);

            $mail->send();
        } catch (PhpMailerException $e) {
            throw $e;
        }
    }
}
