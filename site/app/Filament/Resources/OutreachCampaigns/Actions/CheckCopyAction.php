<?php

declare(strict_types=1);

namespace App\Filament\Resources\OutreachCampaigns\Actions;

use App\Models\OutreachCampaign;
use App\Models\OutreachCampaignStep;
use App\Support\OutreachLint;
use Filament\Actions\Action;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\HtmlString;

/**
 * Checks a whole sequence at once, which is the only place the step order is
 * known — and therefore the only place a rule like "the first email must not
 * pretend to be a reply" can be applied honestly.
 */
final class CheckCopyAction
{
    public static function make(): Action
    {
        return Action::make('checkCopy')
            ->label('Check the copy')
            ->icon(Heroicon::OutlinedClipboardDocumentCheck)
            ->color('gray')
            ->visible(fn (OutreachCampaign $record): bool => $record->steps()->exists())
            ->modalHeading('Pre-send check')
            ->modalDescription(fn (OutreachCampaign $record): Htmlable => new HtmlString(self::report($record)))
            ->modalSubmitAction(false)
            ->modalCancelActionLabel('Close');
    }

    private static function report(OutreachCampaign $campaign): string
    {
        $html = '';

        foreach (OutreachLint::configurationProblems() as $problem) {
            $html .= '<p style="margin:0 0 10px 0;color:#b91c1c;font-weight:600;">⚠ '.e($problem).'</p>';
        }

        $steps = $campaign->steps()->get();

        if ($steps->isEmpty()) {
            return $html.'<p style="margin:0;">There are no steps in this sequence yet.</p>';
        }

        foreach ($steps as $step) {
            $html .= self::stepReport($step);
        }

        return $html;
    }

    private static function stepReport(OutreachCampaignStep $step): string
    {
        $issues = OutreachLint::check($step->subject, $step->body, $step->position);

        $html = '<div style="margin:0 0 16px 0;padding-bottom:12px;border-bottom:1px solid #e2e8f0;">';
        $html .= '<div style="font-weight:600;margin-bottom:6px;color:#0f172a;">Step '.$step->position.' · '.e($step->subject).'</div>';
        $html .= '<div style="font-size:0.8rem;color:#64748b;margin-bottom:6px;">'.e($step->label()).'</div>';

        if ($issues === []) {
            $html .= '<div style="color:#047857;font-size:0.85rem;">✓ Nothing to flag.</div></div>';

            return $html;
        }

        $html .= '<ul style="margin:0;padding-left:18px;font-size:0.85rem;line-height:1.7;">';

        foreach ($issues as $issue) {
            $warning = $issue['level'] === OutreachLint::LEVEL_WARNING;

            $html .= '<li style="color:'.($warning ? '#b45309' : '#475569').';">'
                .($warning ? '⚠ ' : '· ').e($issue['message']).'</li>';
        }

        return $html.'</ul></div>';
    }
}
