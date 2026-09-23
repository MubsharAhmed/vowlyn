<?php

declare(strict_types=1);

namespace App\Filament\Resources\OutreachCampaigns\Actions;

use App\Models\OutreachCampaign;
use App\Services\OutreachDispatcher;
use App\Support\OutreachQuota;
use App\Support\OutreachWindow;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\HtmlString;

/**
 * Starting a campaign is the one place a confirmation is worth the click: it is
 * the moment a sequence stops being a draft and starts reaching strangers.
 */
final class ResumeCampaignAction
{
    public static function make(): Action
    {
        return Action::make('resumeCampaign')
            ->label('Start sending')
            ->icon(Heroicon::OutlinedPlayCircle)
            ->color('success')
            ->visible(fn (OutreachCampaign $record): bool => $record->status !== OutreachCampaign::STATUS_ACTIVE)
            ->requiresConfirmation()
            ->modalHeading('Start sending this campaign?')
            ->modalDescription(fn (OutreachCampaign $record): Htmlable => new HtmlString(self::preview($record)))
            ->modalSubmitActionLabel('Start sending')
            ->action(function (OutreachCampaign $record): void {
                $record->update([
                    'status' => OutreachCampaign::STATUS_ACTIVE,
                    'started_at' => $record->started_at ?? now(),
                    'paused_at' => null,
                ]);

                Notification::make()
                    ->title('Campaign is live')
                    ->body('The next scheduled run will send what is due: '.OutreachQuota::summary($record).'.')
                    ->success()
                    ->send();
            });
    }

    private static function preview(OutreachCampaign $record): string
    {
        $due = app(OutreachDispatcher::class)->dueCount($record);
        $closure = OutreachWindow::closure();

        $lines = [
            $due.' person(s) are waiting on a step right now.',
            'Today\'s allowance: '.OutreachQuota::summary($record).'.',
            $closure === null
                ? 'The sending window is open, so the next run will send immediately.'
                : 'The window is shut ('.$closure.'), so the first sends go out '.OutreachWindow::nextOpenLabel().'.',
            'Only '.$record->steps()->count().' step(s) are in the sequence; the last one closes each person\'s sequence.',
            'Anyone who replies, bounces or opts out is removed automatically and will not receive the follow-up.',
        ];

        $html = '<ul style="margin:0;padding-left:18px;line-height:1.8;">';

        foreach ($lines as $line) {
            $html .= '<li>'.e($line).'</li>';
        }

        return $html.'</ul>';
    }
}
