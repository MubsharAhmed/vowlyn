<?php

declare(strict_types=1);

namespace App\Support;

/**
 * Whether outbound email is actually going anywhere.
 *
 * Laravel's default mailer is `log`, which accepts a message and writes it to
 * the log file instead of delivering it. That is fine for development but
 * dangerous for a lead reply: the admin panel would report success while the
 * lead heard nothing. Everything that reports the outcome of a send asks this
 * class first so the wording stays honest.
 */
final class MailDelivery
{
    /** Transports that accept a message without delivering it anywhere. */
    private const CAPTURE_ONLY = ['log', 'array', 'null'];

    public static function transport(): string
    {
        return (string) config('mail.default');
    }

    public static function delivers(?string $transport): bool
    {
        if ($transport === null || in_array($transport, self::CAPTURE_ONLY, true)) {
            return false;
        }

        // An smtp mailer with no host or credentials is a half-finished
        // configuration, which fails the same way as no configuration at all.
        if ($transport === 'smtp') {
            return filled(config('mail.mailers.smtp.host'))
                && filled(config('mail.mailers.smtp.username'));
        }

        // ses, postmark, resend and mailgun are keyed by credentials we cannot
        // verify here; treat them as configured and let a send failure surface.
        return true;
    }

    public static function isLive(): bool
    {
        return self::delivers(self::transport());
    }

    /**
     * A sentence an administrator can act on, for the warning shown in the panel.
     */
    public static function problem(): string
    {
        $transport = self::transport();

        if ($transport === 'smtp' && ! self::isLive()) {
            return 'SMTP is selected but no host or username is set, so replies cannot be delivered yet.';
        }

        return 'Email is not configured yet, so replies are written to the application log instead of being delivered.';
    }
}
