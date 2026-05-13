<?php

namespace App\Services\Mail;

use RuntimeException;

/**
 * Outbound mail via PHPMailer SMTP only (.env: MAIL_PHPMAILER_* and MAIL_FROM_*).
 */
class OutboundMailService
{
    public function __construct(
        private readonly PhpMailerService $phpmailer,
    ) {
    }

    public function send(
        string $toEmail,
        string $toName,
        string $subject,
        string $html,
        ?string $replyToEmail = null,
        ?string $replyToName = null,
    ): void {
        if (! $this->phpmailer->isConfigured()) {
            throw new RuntimeException(
                'Mail is not configured: set MAIL_PHPMAILER_ENABLED=true, MAIL_PHPMAILER_HOST, and credentials (MAIL_PHPMAILER_*), plus MAIL_FROM_ADDRESS and MAIL_FROM_NAME in .env.'
            );
        }

        $this->phpmailer->send($toEmail, $toName, $subject, $html, $replyToEmail, $replyToName);
    }
}
