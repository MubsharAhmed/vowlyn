<?php

declare(strict_types=1);

namespace App\Filament\Resources\OutreachCampaigns\Actions;

use App\Models\OutreachCampaign;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Support\Icons\Heroicon;

/**
 * Stopping is deliberately one click and no confirmation: the only thing worse
 * than sending outreach by accident is not being able to stop it quickly.
 */
final class PauseCampaignAction
{
    public static function make(): Action
    {
        return Action::make('pauseCampaign')
            ->label('Pause')
            ->icon(Heroicon::OutlinedPauseCircle)
            ->color('warning')
            ->visible(fn (OutreachCampaign $record): bool => $record->status === OutreachCampaign::STATUS_ACTIVE)
            ->action(function (OutreachCampaign $record): void {
                $record->update([
                    'status' => OutreachCampaign::STATUS_PAUSED,
                    'paused_at' => now(),
                ]);

                Notification::make()
                    ->title('Campaign paused')
                    ->body('The scheduled sender will skip this campaign until it is resumed. Nothing already sent is affected.')
                    ->success()
                    ->send();
            });
    }
}
