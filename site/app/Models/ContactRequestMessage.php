<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Concerns\RecordsDelivery;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * One outbound email sent to a lead from the admin panel.
 *
 * The row is written before the message is handed to the mailer so a failed
 * send is still recorded — otherwise a lead can look unanswered when a reply
 * was attempted and lost.
 */
final class ContactRequestMessage extends Model
{
    use RecordsDelivery;

    public const STATUS_SENDING = 'sending';

    public const STATUS_SENT = 'sent';

    public const STATUS_FAILED = 'failed';

    protected $fillable = [
        'mail_template_id',
        'to_email',
        'to_name',
        'subject',
        'body',
        'transport',
        'status',
        'error',
        'sent_at',
    ];

    protected function casts(): array
    {
        return ['sent_at' => 'datetime'];
    }

    public function contactRequest(): BelongsTo
    {
        return $this->belongsTo(ContactRequest::class);
    }

    public function mailTemplate(): BelongsTo
    {
        return $this->belongsTo(MailTemplate::class);
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            self::STATUS_SENT => $this->wasDelivered() ? 'Sent' : 'Logged only',
            self::STATUS_FAILED => 'Failed',
            default => 'Sending',
        };
    }
}
