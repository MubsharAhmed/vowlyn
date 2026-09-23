<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * One email in a sequence.
 *
 * Bodies are plain text with tokens. Storing plain text rather than HTML is a
 * deliberate choice for outreach: a message that looks hand-typed gets replies,
 * and an HTML mailshot with a template header is exactly what spam filters and
 * recipients are trained to dismiss.
 */
final class OutreachCampaignStep extends Model
{
    protected $fillable = ['position', 'delay_days', 'subject', 'body'];

    protected function casts(): array
    {
        return [
            'position' => 'integer',
            'delay_days' => 'integer',
        ];
    }

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(OutreachCampaign::class, 'outreach_campaign_id');
    }

    /**
     * strtr() replaces each token exactly once, so a value that happens to
     * contain another token is never re-substituted.
     *
     * @param  array<string, string>  $tokens
     */
    public function renderSubject(array $tokens): string
    {
        return strtr($this->subject, $tokens);
    }

    /**
     * @param  array<string, string>  $tokens
     */
    public function renderBody(array $tokens): string
    {
        return strtr($this->body, $tokens);
    }

    /**
     * The copy as one specific person would receive it — used by the panel
     * preview, and by the sender immediately before it hands the message over.
     *
     * @return array{subject: string, body: string}
     */
    public function renderFor(OutreachProspect $prospect): array
    {
        $tokens = $prospect->tokens();

        return [
            'subject' => $this->renderSubject($tokens),
            'body' => $this->renderBody($tokens),
        ];
    }

    public function label(): string
    {
        $wait = match (true) {
            $this->position === 1 && $this->delay_days === 0 => 'sent immediately',
            $this->position === 1 => "sent {$this->delay_days} day(s) after enrolling",
            $this->delay_days === 0 => 'sent the same day as the one before',
            default => "sent {$this->delay_days} day(s) after the one before",
        };

        return "Step {$this->position} · {$wait}";
    }
}
