<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * Addresses that must never receive outreach, whatever any list says.
 *
 * This is the one table the sender asks before every single message, and it is
 * deliberately not a flag on the prospect: an opt-out arrives by email, out of
 * band, and has to stick even when the person is re-imported later from a fresh
 * spreadsheet, or was never a prospect in the first place.
 */
final class EmailSuppression extends Model
{
    public const REASON_UNSUBSCRIBED = 'unsubscribed';

    public const REASON_BOUNCED = 'bounced';

    public const REASON_COMPLAINT = 'complaint';

    public const REASON_ASKED = 'asked';

    public const REASON_MANUAL = 'manual';

    protected $fillable = ['email', 'reason', 'source', 'note', 'suppressed_at'];

    protected function casts(): array
    {
        return ['suppressed_at' => 'datetime'];
    }

    /**
     * @return array<string, string>
     */
    public static function reasons(): array
    {
        return [
            self::REASON_UNSUBSCRIBED => 'Unsubscribed',
            self::REASON_ASKED => 'Asked not to be contacted',
            self::REASON_BOUNCED => 'Address bounced',
            self::REASON_COMPLAINT => 'Marked as spam',
            self::REASON_MANUAL => 'Added by hand',
        ];
    }

    public static function reasonLabel(?string $reason): string
    {
        return self::reasons()[$reason] ?? Str::headline((string) $reason);
    }

    public static function normalise(?string $email): string
    {
        return OutreachProspect::normaliseEmail($email);
    }

    public static function covers(?string $email): bool
    {
        $email = self::normalise($email);

        if ($email === '') {
            return false;
        }

        return self::query()->where('email', $email)->exists();
    }

    /**
     * Add an address to the list. Idempotent, and never downgrades a reason
     * that is already recorded — "they unsubscribed" outranks "added by hand".
     */
    public static function add(
        ?string $email,
        string $reason = self::REASON_MANUAL,
        ?string $note = null,
        ?string $source = null,
    ): ?self {
        $email = self::normalise($email);

        if ($email === '') {
            return null;
        }

        $existing = self::query()->where('email', $email)->first();

        if ($existing !== null) {
            return $existing;
        }

        return self::create([
            'email' => $email,
            'reason' => $reason,
            'note' => $note,
            'source' => $source ?? 'admin panel',
            'suppressed_at' => now(),
        ]);
    }
}
