<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

/**
 * A sequence of emails sent to a set of prospects.
 *
 * Only an `active` campaign sends anything. Draft, paused and finished are all
 * inert, which is what makes it safe to build a sequence and enrol people into
 * it before deciding to start — the alternative, where saving a campaign starts
 * emailing, is how people wake up to messages they had not meant to send.
 */
final class OutreachCampaign extends Model
{
    public const STATUS_DRAFT = 'draft';

    public const STATUS_ACTIVE = 'active';

    public const STATUS_PAUSED = 'paused';

    public const STATUS_FINISHED = 'finished';

    /**
     * Statuses an enrollment can hold that mean the person engaged.
     */
    public const ENGAGED_STATUSES = [
        OutreachEnrollment::STATUS_REPLIED,
        OutreachEnrollment::STATUS_INTERESTED,
    ];

    protected $fillable = [
        'name',
        'goal',
        'status',
        'daily_limit',
        'notes',
        'started_at',
        'paused_at',
        'finished_at',
    ];

    protected function casts(): array
    {
        return [
            'started_at' => 'datetime',
            'paused_at' => 'datetime',
            'finished_at' => 'datetime',
            'daily_limit' => 'integer',
        ];
    }

    /**
     * @return HasMany<OutreachCampaignStep, $this>
     */
    public function steps(): HasMany
    {
        return $this->hasMany(OutreachCampaignStep::class)->orderBy('position');
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

    /**
     * @return array<string, string>
     */
    public static function statuses(): array
    {
        return [
            self::STATUS_DRAFT => 'Draft — not sending',
            self::STATUS_ACTIVE => 'Active — sending',
            self::STATUS_PAUSED => 'Paused',
            self::STATUS_FINISHED => 'Finished',
        ];
    }

    public function statusLabel(): string
    {
        return self::statuses()[$this->status] ?? Str::headline((string) $this->status);
    }

    public function isSending(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    public function stepAt(int $position): ?OutreachCampaignStep
    {
        return $this->steps->firstWhere('position', $position);
    }

    public function stepCount(): int
    {
        return $this->steps->count();
    }

    /**
     * The seven-day shape of the campaign, for the panel summary.
     *
     * @return array<string, int>
     */
    public function progress(): array
    {
        $enrollments = $this->enrollments();

        return [
            'enrolled' => (clone $enrollments)->count(),
            'active' => (clone $enrollments)->where('status', OutreachEnrollment::STATUS_ACTIVE)->count(),
            'replied' => (clone $enrollments)->whereIn('status', self::ENGAGED_STATUSES)->count(),
            'sent' => $this->messages()->where('status', OutreachMessage::STATUS_SENT)->count(),
            'stopped' => (clone $enrollments)->whereNotIn('status', [OutreachEnrollment::STATUS_ACTIVE, OutreachEnrollment::STATUS_FINISHED])->count(),
        ];
    }

    /**
     * @param  Builder<OutreachCampaign>  $query
     */
    public function scopeSending(Builder $query): void
    {
        $query->where('status', self::STATUS_ACTIVE);
    }
}
