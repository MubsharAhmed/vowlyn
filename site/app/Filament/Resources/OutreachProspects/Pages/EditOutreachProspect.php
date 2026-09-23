<?php

declare(strict_types=1);

namespace App\Filament\Resources\OutreachProspects\Pages;

use App\Filament\Resources\OutreachProspects\OutreachProspectResource;
use App\Models\EmailSuppression;
use App\Models\OutreachEnrollment;
use App\Models\OutreachProspect;
use App\Services\OutreachDispatcher;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

/**
 * Editing a prospect, with the consequence that the form cannot express on its
 * own: choosing a status that means "do not contact" has to stop the sequences
 * that are already running.
 *
 * Without this hook the panel would happily record that somebody is not
 * interested while a follow-up sat waiting to go out to them on Thursday — a
 * mismatch that is invisible in every screen and obvious to the recipient.
 */
final class EditOutreachProspect extends EditRecord
{
    protected static string $resource = OutreachProspectResource::class;

    private ?string $previousStatus = null;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function beforeSave(): void
    {
        // Still the stored value here — the update happens after this hook.
        $this->previousStatus = $this->record->status;
    }

    protected function afterSave(): void
    {
        $record = $this->record;

        if ($this->previousStatus === $record->status || ! $record->blockedFromContact()) {
            return;
        }

        $dispatcher = app(OutreachDispatcher::class);

        if ($record->status === OutreachProspect::STATUS_UNSUBSCRIBED) {
            EmailSuppression::add(
                $record->email,
                EmailSuppression::REASON_ASKED,
                'Marked unsubscribed in the panel',
                'admin panel',
            );

            $dispatcher->stopAllFor(
                $record,
                OutreachEnrollment::STATUS_UNSUBSCRIBED,
                'Marked unsubscribed in the panel.',
            );

            Notification::make()
                ->title('Opt-out recorded')
                ->body('They are on the opt-out list and every sequence for them has stopped.')
                ->success()
                ->send();

            return;
        }

        if ($record->status === OutreachProspect::STATUS_BOUNCED) {
            EmailSuppression::add(
                $record->email,
                EmailSuppression::REASON_BOUNCED,
                'Marked bounced in the panel',
                'admin panel',
            );
        }

        $dispatcher->stopAllFor(
            $record,
            $record->status === OutreachProspect::STATUS_BOUNCED
                ? OutreachEnrollment::STATUS_BOUNCED
                : OutreachEnrollment::STATUS_NOT_INTERESTED,
            'Status set to “'.$record->statusLabel().'”.',
        );

        Notification::make()
            ->title('Sequences stopped')
            ->body('Nobody will email this person again while they are marked “'.$record->statusLabel().'”.')
            ->success()
            ->send();
    }
}
