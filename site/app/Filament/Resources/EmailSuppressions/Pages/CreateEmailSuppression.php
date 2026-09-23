<?php

declare(strict_types=1);

namespace App\Filament\Resources\EmailSuppressions\Pages;

use App\Filament\Resources\EmailSuppressions\EmailSuppressionResource;
use App\Models\EmailSuppression;
use App\Models\OutreachEnrollment;
use App\Models\OutreachProspect;
use App\Services\OutreachDispatcher;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

/**
 * Adding an address by hand, which is what happens when somebody phones rather
 * than clicks, or when a team member notices a bounce that the panel cannot.
 * Their sequences stop at the same moment the row is written.
 */
final class CreateEmailSuppression extends CreateRecord
{
    protected static string $resource = EmailSuppressionResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['email'] = EmailSuppression::normalise($data['email'] ?? null);
        $data['suppressed_at'] = now();
        $data['source'] = $data['source'] ?? 'admin panel';

        return $data;
    }

    protected function afterCreate(): void
    {
        /** @var EmailSuppression $suppression */
        $suppression = $this->record;

        $prospect = OutreachProspect::query()->where('email', $suppression->email)->first();

        if ($prospect === null) {
            return;
        }

        $prospect->update(['status' => OutreachProspect::STATUS_UNSUBSCRIBED]);

        $stopped = app(OutreachDispatcher::class)->stopAllFor(
            $prospect,
            OutreachEnrollment::STATUS_UNSUBSCRIBED,
            'Added to the opt-out list by hand ('.EmailSuppression::reasonLabel($suppression->reason).').',
        );

        if ($stopped > 0) {
            Notification::make()
                ->title($stopped.' running sequence(s) stopped')
                ->body('They will not be emailed again by any campaign.')
                ->success()
                ->send();
        }
    }
}
