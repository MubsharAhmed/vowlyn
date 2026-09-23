<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\ContactRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;

/**
 * An answer to a website enquiry, sent by hand from the admin panel.
 *
 * Subject and body arrive as plain text that the administrator has already
 * reviewed. Both are escaped when rendered, so neither the administrator's
 * typing nor the lead's own details can inject markup into the message.
 */
final class LeadReply extends Mailable
{
    use Queueable;
    use SerializesModels;

    public function __construct(
        public readonly ContactRequest $contactRequest,
        public readonly string $subjectLine,
        public readonly string $bodyText,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            // A newline in a subject would let a header be injected, so fold it.
            subject: Str::of($this->subjectLine)->replace(["\r", "\n"], ' ')->squish()->limit(180, '')->toString(),
            replyTo: [
                new Address(
                    (string) config('mail.reply_to.address'),
                    (string) config('mail.reply_to.name'),
                ),
            ],
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.lead-reply',
            text: 'emails.lead-reply-text',
            with: [
                'bodyText' => $this->bodyText,
                'contactRequest' => $this->contactRequest,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
