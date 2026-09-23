<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

/**
 * One person's place in one sequence.
 *
 * Separate from the prospect because the same person may be approached by a
 * later campaign with a different offer, and separate from the message log
 * because this holds the thing a log cannot: where the sequence has got to for
 * this person.
 */
final class OutreachEnrollment extends Model
{
    public const STATUS_ACTIVE = 'active';

    public const STATUS_REPLIED = 'replied';

    public const STATUS_INTERESTED = 'interested';

    public const STATUS_NOT_INTERESTED = 'not_interested';

    public const STATUS_UNSUBSCRIBED = 'unsubscribed';

    public const STATUS_BOUNCED = 'bounced';

    public const STATUS_FINISHED = 'finished';

    public const STATUS_PAUSED = 'paused';

    protected $fillable = [
        'outreach_campaign_id',
        'outreach_prospect_id',
        'current_step',
        'status',
        'stop_reason',
        'next_send_at',
        'replied_at',
        'enrolled_at',
    ];

    protected function casts(): array
    {
        return [
            'current_step' => 'integer',
            'next_send_at' => 'datetime',
            'replied_at' => 'datetime',
            'enrolled_at' => 'datetime',
        ];
    }

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(OutreachCampaign::class, 'outreach_campaign_id');
    }

    public function prospect(): BelongsTo
    {
        return $this->belongsTo(OutreachProspect::class, 'outreach_prospect_id');
    }

    /**
     * @return HasMany<OutreachMessage, $this>
     */
    public function messages(): HasMany
    {
        return $this->hasMany(OutreachMessage::class);
    }

    /**
     * @return array<string, string>
     */
    public static function statuses(): array
    {
        return [
            self::STATUS_ACTIVE => 'Waiting',
            self::STATUS_REPLIED => 'Replied',
            self::STATUS_INTERESTED => 'Interested',
            self::STATUS_NOT_INTERESTED => 'Not interested',
            self::STATUS_UNSUBSCRIBED => 'Unsubscribed',
            self::STATUS_BOUNCED => 'Bounced',
            self::STATUS_FINISHED => 'Sequence complete',
            self::STATUS_PAUSED => 'Paused',
        ];
    }

    /**
     * Statuses that stop the sequence. Everything except `active` and `paused`
     * ends it permanently.
     */
    public static function stoppedStatuses(): array
    {
        return [
            self::STATUS_REPLIED,
            self::STATUS_INTERESTED,
            self::STATUS_NOT_INTERESTED,
            self::STATUS_UNSUBSCRIBED,
            self::STATUS_BOUNCED,
            self::STATUS_FINISHED,
        ];
    }

    public function statusLabel(): string
    {
        return self::statuses()[$this->status] ?? Str::headline((string) $this->status);
    }

    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    public function isStopped(): bool
    {
        return in_array($this->status, self::stoppedStatuses(), true);
    }

    /**
     * End the sequence for this person, with the reason recorded.
     *
     * Idempotent by design: a reply, an unsubscribe and a bounce can all arrive
     * for the same person, and the first explanation is the one worth keeping.
     */
    public function stop(string $status, ?string $reason = null): void
    {
        if ($this->isStopped() && $this->stop_reason !== null) {
            return;
        }

        $attributes = [
            'status' => $status,
            'stop_reason' => $reason,
            'next_send_at' => null,
        ];

        if (in_array($status, [self::STATUS_REPLIED, self::STATUS_INTERESTED], true)) {
            $attributes['replied_at'] = $this->replied_at ?? now();
        }

        $this->update($attributes);
    }

    public function resume(): void
    {
        $this->update([
            'status' => self::STATUS_ACTIVE,
            'stop_reason' => null,
            'next_send_at' => now(),
        ]);
    }

    /**
     * @param  Builder<OutreachEnrollment>  $query
     */
    public function scopeDue(Builder $query): void
    {
        $query->where('status', self::STATUS_ACTIVE)
            ->where(fn (Builder $inner) => $inner->whereNull('next_send_at')->orWhere('next_send_at', '<=', now()));
    }
}
