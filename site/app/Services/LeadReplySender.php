<?php

declare(strict_types=1);

namespace App\Services;

use App\Mail\LeadReply;
use App\Models\ContactRequest;
use App\Models\ContactRequestMessage;
use App\Models\MailTemplate;
use App\Support\MailDelivery;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Throwable;

/**
 * Sends a hand-written reply to a lead and records what happened.
 *
 * Sending is synchronous on purpose. The administrator is standing in front of
 * the result, so "sent" has to mean the mailer accepted it there and then — a
 * queued reply would leave the panel claiming success while a stopped worker
 * silently dropped it. The log row is created before the attempt so a failure
 * is still visible in the history.
 */
final class LeadReplySender
{
    public function send(
        ContactRequest $contactRequest,
        ?MailTemplate $template,
        string $subject,
        string $body,
    ): ContactRequestMessage {
        $transport = MailDelivery::transport();

        $message = $contactRequest->messages()->create([
            'mail_template_id' => $template?->id,
            'to_email' => $contactRequest->email,
            'to_name' => $contactRequest->name,
            'subject' => $subject,
            'body' => $body,
            'transport' => $transport,
            'status' => ContactRequestMessage::STATUS_SENDING,
        ]);

        try {
            Mail::to($contactRequest->email, $contactRequest->name)
                ->send(new LeadReply($contactRequest, $subject, $body));

            $message->update([
                'status' => ContactRequestMessage::STATUS_SENT,
                'error' => null,
                'sent_at' => now(),
            ]);

            // A message handed to the log transport was accepted but never
            // delivered, so the lead must not be filed as answered — otherwise
            // the panel quietly loses track of who is still waiting for a reply.
            if ($message->wasDelivered()) {
                $contactRequest->update([
                    'status' => ContactRequest::STATUS_REPLIED,
                    'replied_at' => now(),
                ]);
            }
        } catch (Throwable $e) {
            report($e);

            $message->update([
                'status' => ContactRequestMessage::STATUS_FAILED,
                'error' => Str::limit($e->getMessage(), 490),
            ]);
        }

        return $message->refresh();
    }
}
