<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Concerns\RecordsDelivery;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * One outreach email, and what happened to it.
 *
 * The row is written before the mailer is called, so a failed send is recorded
 * rather than vanishing. Refusals (`skipped`) are recorded for the same reason:
 * "why has this person never received anything?" should be answerable from the
 * history instead of by reading the code.
 */
final class OutreachMessage extends Model
{
    use RecordsDelivery;

    public const STATUS_SENDING = 'sending';

    public const STATUS_SENT = 'sent';

    public const STATUS_FAILED = 'failed';

    /** Refused before reaching the mailer — suppressed, cooling down, over quota. */
    public const STATUS_SKIPPED = 'skipped';

    /** The sequence ended for this person before this step was sent. */
    public const STATUS_CANCELLED = 'cancelled';

    protected $fillable = [
        'outreach_enrollment_id',
        'outreach_prospect_id',
        'outreach_campaign_id',
        'outreach_campaign_step_id',
        'step_position',
        'to_email',
        'subject',
        'body',
        'transport',
        'status',
        'error',
        'skip_reason',
        'sent_at',
    ];

    protected function casts(): array
    {
        return ['sent_at' => 'datetime'];
    }

    public function prospect(): BelongsTo
    {
        return $this->belongsTo(OutreachProspect::class, 'outreach_prospect_id');
    }

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(OutreachCampaign::class, 'outreach_campaign_id');
    }

    public function enrollment(): BelongsTo
    {
        return $this->belongsTo(OutreachEnrollment::class, 'outreach_enrollment_id');
    }

    public function step(): BelongsTo
    {
        return $this->belongsTo(OutreachCampaignStep::class, 'outreach_campaign_step_id');
    }

    public function isSkipped(): bool
    {
        return $this->status === self::STATUS_SKIPPED;
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            self::STATUS_SENT => $this->wasDelivered() ? 'Sent' : 'Logged only',
            self::STATUS_FAILED => 'Failed',
            self::STATUS_SKIPPED => 'Not sent',
            self::STATUS_CANCELLED => 'Cancelled',
            default => 'Sending',
        };
    }
}
