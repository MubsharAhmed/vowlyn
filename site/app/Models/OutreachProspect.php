<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

/**
 * A person or company we are approaching, as opposed to somebody who got in
 * touch with us first.
 *
 * The distinction matters in two places. One is the token set: a cold email has
 * to name the company and the industry to avoid reading as a mailshot, because
 * the recipient has no reason to know who we are. The other is reachability —
 * a lead may always be answered, but a prospect who asked us to stop must never
 * be contacted again, by any campaign, no matter how the list was built.
 */
final class OutreachProspect extends Model
{
    use SoftDeletes;

    public const STATUS_NEW = 'new';

    public const STATUS_CONTACTED = 'contacted';

    public const STATUS_REPLIED = 'replied';

    public const STATUS_INTERESTED = 'interested';

    public const STATUS_NOT_INTERESTED = 'not_interested';

    public const STATUS_UNSUBSCRIBED = 'unsubscribed';

    public const STATUS_BOUNCED = 'bounced';

    public const STATUS_CONVERTED = 'converted';

    /**
     * Personalisation tokens available in outreach copy. Values are raw text;
     * everything is escaped when the email is rendered.
     */
    public const TOKENS = ['{first_name}', '{name}', '{company}', '{role}', '{industry}', '{region}', '{studio}'];

    /**
     * Statuses that mean "do not contact this person again".
     */
    public const STATUSES_BLOCKED = [
        self::STATUS_NOT_INTERESTED,
        self::STATUS_UNSUBSCRIBED,
        self::STATUS_BOUNCED,
    ];

    protected $fillable = [
        'company',
        'contact_name',
        'email',
        'role',
        'industry',
        'region',
        'website',
        'linkedin_url',
        'source',
        'status',
        'notes',
        'last_contacted_at',
        'replied_at',
    ];

    protected function casts(): array
    {
        return [
            'last_contacted_at' => 'datetime',
            'replied_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        self::creating(function (self $prospect): void {
            $prospect->email = self::normaliseEmail($prospect->email);

            // Every prospect needs a working way out of the list, so the token
            // is minted on creation rather than lazily on first send. Data
            // imported by hand must not be able to skip it.
            if (blank($prospect->unsubscribe_token)) {
                $prospect->unsubscribe_token = self::freshToken();
            }
        });
    }

    /**
     * @return HasMany<OutreachEnrollment, $this>
     */
    public function enrollments(): HasMany
    {
        return $this->hasMany(OutreachEnrollment::class);
    }

    /**
     * @return HasMany<OutreachMessage, $this>
     */
    public function messages(): HasMany
    {
        return $this->hasMany(OutreachMessage::class);
    }

    public static function normaliseEmail(?string $email): string
    {
        return mb_strtolower(trim((string) $email));
    }

    public static function freshToken(): string
    {
        do {
            $token = Str::random(40);
        } while (self::withTrashed()->where('unsubscribe_token', $token)->exists());

        return $token;
    }

    /**
     * @return array<string, string>
     */
    public static function statuses(): array
    {
        return [
            self::STATUS_NEW => 'New',
            self::STATUS_CONTACTED => 'Contacted',
            self::STATUS_REPLIED => 'Replied',
            self::STATUS_INTERESTED => 'Interested',
            self::STATUS_CONVERTED => 'Became a client',
            self::STATUS_NOT_INTERESTED => 'Not interested',
            self::STATUS_UNSUBSCRIBED => 'Unsubscribed',
            self::STATUS_BOUNCED => 'Bounced',
        ];
    }

    public function statusLabel(): string
    {
        return self::statuses()[$this->status] ?? Str::headline((string) $this->status);
    }

    public function firstName(): ?string
    {
        $name = trim((string) $this->contact_name);

        if ($name === '') {
            return null;
        }

        return Str::of($name)->explode(' ')->first() ?: null;
    }

    public function displayName(): string
    {
        return $this->contact_name ?: ($this->company ?: $this->email);
    }

    public function blockedFromContact(): bool
    {
        return in_array($this->status, self::STATUSES_BLOCKED, true);
    }

    public function isSuppressed(): bool
    {
        return EmailSuppression::covers($this->email);
    }

    /**
     * Whether this person may receive outreach at all, ignoring campaign-level
     * reasons such as the daily limit.
     */
    public function isReachable(): bool
    {
        return ! $this->blockedFromContact() && ! $this->isSuppressed();
    }

    /**
     * Values substituted into outreach copy before rendering.
     *
     * @return array<string, string>
     */
    public function tokens(): array
    {
        return [
            '{first_name}' => $this->firstName() ?? 'there',
            '{name}' => $this->contact_name ?: ($this->company ?: 'there'),
            '{company}' => $this->company ?: 'your team',
            '{role}' => $this->role ?: 'your team',
            '{industry}' => $this->industry ?: 'your industry',
            '{region}' => $this->region ?: 'your area',
            '{studio}' => (string) config('mail.reply_to.name'),
        ];
    }

    /**
     * Tokens that would read as a generic phrase because we have no value for
     * them. Surfaced before sending, since a cold email that opens with "Hi
     * there" to a named recipient is a wasted send.
     *
     * @return array<int, string>
     */
    public function genericTokens(): array
    {
        $missing = [];

        foreach (['{first_name}' => $this->firstName(), '{company}' => $this->company, '{role}' => $this->role, '{industry}' => $this->industry, '{region}' => $this->region] as $token => $value) {
            if (blank($value)) {
                $missing[] = $token;
            }
        }

        return $missing;
    }

    /**
     * @param  Builder<OutreachProspect>  $query
     */
    public function scopeReachable(Builder $query): void
    {
        $query->whereNotIn('status', self::STATUSES_BLOCKED);
    }

    /**
     * @param  Builder<OutreachProspect>  $query
     */
    public function scopeInterested(Builder $query): void
    {
        $query->whereIn('status', [self::STATUS_REPLIED, self::STATUS_INTERESTED, self::STATUS_CONVERTED]);
    }
}
