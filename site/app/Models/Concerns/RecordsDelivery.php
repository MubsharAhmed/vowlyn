<?php

declare(strict_types=1);

namespace App\Models\Concerns;

use App\Support\MailDelivery;

/**
 * Outcome helpers for a row in an outbound email log.
 *
 * Both the lead replies and the outreach messages record which mailer handled
 * them, and in both cases "accepted by the mailer" is not the same as "reached
 * a person". Keeping the distinction in one place means the panel can never
 * claim a delivery that did not happen on one screen while being honest about it
 * on another.
 *
 * The using class is expected to define STATUS_SENT and STATUS_FAILED.
 */
trait RecordsDelivery
{
    public function failed(): bool
    {
        return $this->status === static::STATUS_FAILED;
    }

    public function statusIsSent(): bool
    {
        return $this->status === static::STATUS_SENT;
    }

    /**
     * True only when the mailer actually carried the message. A message handled
     * by the log or array transport was accepted but never delivered.
     */
    public function wasDelivered(): bool
    {
        return $this->statusIsSent() && MailDelivery::delivers($this->transport);
    }
}
