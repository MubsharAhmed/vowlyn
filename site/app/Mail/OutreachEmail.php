<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\OutreachProspect;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Headers;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;

/**
 * A first-contact email to somebody who has never written to us.
 *
 * Deliberately plainer than the reply template. A reply can carry the brand
 * because the recipient is expecting it; cold mail that arrives with a gradient
 * header, image blocks and a marketing footer looks like a mailshot and is
 * treated like one — by the recipient and by the filter. So there is no logo, no
 * tracking pixel, no styled button: just sentences from a person, plus the
 * opt-out and postal address that law and good manners both require.
 *
 * The opt-out is advertised through the List-Unsubscribe headers as well as in
 * the footer, which lets Gmail and Apple Mail show their own unsubscribe button
 * — an opt-out a recipient can take in one tap is far less likely to become a
 * spam complaint.
 */
final class OutreachEmail extends Mailable
{
    use Queueable;
    use SerializesModels;

    public function __construct(
        public readonly OutreachProspect $prospect,
        public readonly string $subjectLine,
        public readonly string $bodyText,
        public readonly ?string $campaignGoal = null,
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

    public function headers(): Headers
    {
        return new Headers(text: [
            'List-Unsubscribe' => '<'.$this->unsubscribeUrl().'>',
            'List-Unsubscribe-Post' => 'List-Unsubscribe=One-Click',
        ]);
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.outreach',
            text: 'emails.outreach-text',
            with: [
                'bodyText' => $this->bodyText,
                'prospect' => $this->prospect,
                'studio' => (string) config('mail.reply_to.name'),
                'postalAddress' => (string) config('outreach.footer.postal_address'),
                'goal' => $this->campaignGoal,
                'unsubscribeUrl' => $this->unsubscribeUrl(),
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }

    /**
     * The public opt-out address. An opaque token rather than a signed URL, so
     * the link keeps working after the application key is rotated — an
     * unsubscribe link that 403s is a compliance problem, not a cosmetic one.
     */
    public function unsubscribeUrl(): string
    {
        return route('outreach.unsubscribe', ['token' => $this->prospect->unsubscribe_token]);
    }
}
