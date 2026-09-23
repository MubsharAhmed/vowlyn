<?php

declare(strict_types=1);

namespace App\Filament\Resources\OutreachProspects\Actions;

use App\Models\EmailSuppression;
use App\Models\OutreachCampaign;
use App\Models\OutreachEnrollment;
use App\Models\OutreachProspect;
use App\Services\OutreachDispatcher;
use App\Services\OutreachListImporter;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

/**
 * Everything that can be done to a prospect, in one place.
 *
 * Each of these is really a state change that has to be applied consistently in
 * two places at once — the prospect's own status and the sequences they are in.
 * Keeping that in one file makes it obvious that, for instance, marking somebody
 * as replied always stops their follow-ups; the panel should never be able to
 * send a "just checking in" to a person who answered last week.
 */
final class ProspectActions
{
    public static function import(): Action
    {
        return Action::make('importProspects')
            ->label('Import a list')
            ->icon(Heroicon::OutlinedArrowUpTray)
            ->modalHeading('Import prospects from a CSV')
            ->modalDescription('The file needs a column named “email”. Other columns are matched by name: company, contact_name, role, industry, region, website, source, notes. Addresses on the opt-out list are never imported.')
            ->modalSubmitActionLabel('Import')
            ->schema([
                FileUpload::make('file')
                    ->label('CSV file')
                    ->required()
                    ->acceptedFileTypes(['text/csv', 'text/plain', 'application/csv', 'application/vnd.ms-excel'])
                    ->disk('local')
                    ->directory('outreach-imports')
                    ->visibility('private')
                    ->helperText('From Excel, Google Sheets or a CRM. Up to '.config('outreach.import.max_rows').' rows per file, comma or semicolon separated.'),

                Select::make('campaign_id')
                    ->label('Add everyone to a campaign')
                    ->options(fn (): array => OutreachCampaign::query()->orderBy('name')->pluck('name', 'id')->all())
                    ->placeholder('No — just import them')
                    ->native(false)
                    ->helperText('Anyone already in that campaign, on the opt-out list or marked not interested is skipped.'),
            ])
            ->action(function (array $data): void {
                $file = (string) ($data['file'] ?? '');
                $path = Storage::disk('local')->path($file);

                $campaign = filled($data['campaign_id'] ?? null)
                    ? OutreachCampaign::query()->find($data['campaign_id'])
                    : null;

                $result = app(OutreachListImporter::class)->import($path, $campaign);

                // Lists of names and email addresses do not belong on the
                // server once they have been read.
                Storage::disk('local')->delete($file);

                if ($result['missing_email_column']) {
                    Notification::make()
                        ->title('That file could not be read')
                        ->body($result['problems'][0] ?? 'No email column was found.')
                        ->danger()
                        ->persistent()
                        ->send();

                    return;
                }

                $parts = array_filter([
                    $result['imported'] > 0 ? $result['imported'].' new' : null,
                    $result['updated'] > 0 ? $result['updated'].' updated' : null,
                    $result['duplicates'] > 0 ? $result['duplicates'].' already here' : null,
                    $result['suppressed'] > 0 ? $result['suppressed'].' on the opt-out list' : null,
                    $result['invalid'] > 0 ? $result['invalid'].' invalid' : null,
                ]);

                $body = $parts === [] ? 'Nothing in the file could be imported.' : 'Imported: '.implode(', ', $parts).'.';

                if ($campaign !== null) {
                    $body .= ' Added to '.$campaign->name.': '.$result['enrolled'].'.';
                }

                if ($result['truncated']) {
                    $body .= ' The file was longer than '.config('outreach.import.max_rows').' rows, so the rest was left out.';
                }

                foreach (array_slice($result['problems'], 0, 3) as $problem) {
                    $body .= ' '.$problem;
                }

                $notification = Notification::make()
                    ->title('Import finished')
                    ->body($body);

                if ($result['imported'] + $result['updated'] === 0) {
                    $notification->warning();
                } else {
                    $notification->success();
                }

                $notification->persistent()->send();
            });
    }

    public static function addToCampaignRecord(): Action
    {
        return Action::make('addToCampaign')
            ->label('Add to a campaign')
            ->icon(Heroicon::OutlinedPaperAirplane)
            ->modalHeading('Add to a campaign')
            ->schema(self::campaignFields())
            ->action(fn (array $data, OutreachProspect $record) => self::enroll(
                collect([$record]),
                OutreachCampaign::query()->find($data['campaign_id']),
            ));
    }

    public static function addToCampaignBulk(): BulkAction
    {
        return BulkAction::make('addToCampaign')
            ->label('Add to a campaign')
            ->icon(Heroicon::OutlinedPaperAirplane)
            ->modalHeading('Add these people to a campaign')
            ->modalDescription('The campaign must be active before anything is sent. Adding people to a draft simply queues them.')
            ->schema(self::campaignFields())
            ->deselectRecordsAfterCompletion()
            ->action(fn (array $data, Collection $records) => self::enroll(
                $records,
                OutreachCampaign::query()->find($data['campaign_id']),
            ));
    }

    public static function markRepliedRecord(): Action
    {
        return Action::make('markReplied')
            ->label('They replied')
            ->icon(Heroicon::OutlinedChatBubbleLeftEllipsis)
            ->color('success')
            ->modalHeading('Mark as replied')
            ->modalDescription('This stops every sequence for this person, so the follow-up that was scheduled for next week is never sent.')
            ->modalSubmitActionLabel('Mark as replied')
            ->action(fn (OutreachProspect $record) => self::markReplied(collect([$record])));
    }

    public static function markRepliedBulk(): BulkAction
    {
        return BulkAction::make('markReplied')
            ->label('They replied')
            ->icon(Heroicon::OutlinedChatBubbleLeftEllipsis)
            ->color('success')
            ->requiresConfirmation()
            ->modalHeading('Mark as replied')
            ->modalDescription('This stops every sequence for these people. Doing this stops scheduled follow-ups going out to someone who has already answered.')
            ->action(fn (Collection $records) => self::markReplied($records));
    }

    public static function markBounced(): Action
    {
        return Action::make('markBounced')
            ->label('Address bounced')
            ->icon(Heroicon::OutlinedEnvelopeOpen)
            ->color('danger')
            ->modalHeading('Mark this address as bounced')
            ->modalDescription('A hard bounce means the address does not exist. It goes on the opt-out list, because repeatedly emailing a dead address is one of the quickest ways to damage the sending domain.')
            ->modalSubmitActionLabel('Mark as bounced')
            ->action(function (OutreachProspect $record): void {
                EmailSuppression::add($record->email, EmailSuppression::REASON_BOUNCED, 'Marked from the panel', 'admin panel');

                $record->update(['status' => OutreachProspect::STATUS_BOUNCED]);

                app(OutreachDispatcher::class)->stopAllFor(
                    $record,
                    OutreachEnrollment::STATUS_BOUNCED,
                    'The address bounced.',
                );

                Notification::make()
                    ->title('Address suppressed')
                    ->body($record->email.' will not be emailed again.')
                    ->success()
                    ->send();
            });
    }

    public static function suppressBulk(): BulkAction
    {
        return BulkAction::make('suppress')
            ->label('Add to opt-out list')
            ->icon(Heroicon::OutlinedNoSymbol)
            ->color('danger')
            ->requiresConfirmation()
            ->modalHeading('Add to the opt-out list')
            ->modalDescription('This is permanent by design: these addresses can never be emailed by any campaign, even if they appear on a list you import in future. Their sequences are stopped at the same time.')
            ->modalSubmitActionLabel('Add to the opt-out list')
            ->schema([
                Select::make('reason')
                    ->options(EmailSuppression::reasons())
                    ->default(EmailSuppression::REASON_ASKED)
                    ->required()
                    ->native(false),
                TextInput::make('note')
                    ->label('Note (optional)')
                    ->maxLength(200),
            ])
            ->deselectRecordsAfterCompletion()
            ->action(function (array $data, Collection $records): void {
                foreach ($records as $prospect) {
                    EmailSuppression::add(
                        $prospect->email,
                        $data['reason'],
                        $data['note'] ?? null,
                        'admin panel',
                    );

                    $prospect->update(['status' => OutreachProspect::STATUS_UNSUBSCRIBED]);

                    app(OutreachDispatcher::class)->stopAllFor(
                        $prospect,
                        OutreachEnrollment::STATUS_UNSUBSCRIBED,
                        'Added to the opt-out list from the panel.',
                    );
                }

                Notification::make()
                    ->title($records->count().' address(es) suppressed')
                    ->body('Their sequences are stopped and they will not be emailed again.')
                    ->success()
                    ->send();
            });
    }

    /**
     * @return array<int, Select>
     */
    private static function campaignFields(): array
    {
        return [
            Select::make('campaign_id')
                ->label('Campaign')
                ->options(fn (): array => OutreachCampaign::query()
                    ->orderByRaw("CASE WHEN status = 'active' THEN 0 ELSE 1 END")
                    ->orderBy('name')
                    ->pluck('name', 'id')
                    ->all())
                ->required()
                ->native(false)
                ->helperText('Active campaigns send on their next run; drafts wait until you start them.'),
        ];
    }

    /**
     * @param  Collection<int, OutreachProspect>  $prospects
     */
    private static function enroll(Collection $prospects, ?OutreachCampaign $campaign): void
    {
        if ($campaign === null) {
            Notification::make()
                ->title('No campaign selected')
                ->warning()
                ->send();

            return;
        }

        $result = app(OutreachDispatcher::class)->enrollMany($prospects, $campaign);

        $notification = Notification::make()
            ->title($result['enrolled'].' of '.$prospects->count().' added to '.$campaign->name)
            ->body($result['skipped'] === []
                ? ($campaign->isSending()
                    ? 'The next scheduled run will send what is due.'
                    : 'This campaign is '.$campaign->statusLabel().', so nothing will be sent until it is started.')
                : 'Left out: '.collect($result['skipped'])
                    ->map(fn (string $reason, string $email): string => $email.' ('.$reason.')')
                    ->take(4)
                    ->implode('; ')
                    .(count($result['skipped']) > 4 ? '; and '.(count($result['skipped']) - 4).' more' : ''))
            ->persistent();

        if ($result['enrolled'] > 0) {
            $notification->success();
        } else {
            $notification->warning()->title('Nobody was added');
        }

        $notification->send();
    }

    /**
     * @param  Collection<int, OutreachProspect>  $prospects
     */
    private static function markReplied(Collection $prospects): void
    {
        $dispatcher = app(OutreachDispatcher::class);

        foreach ($prospects as $prospect) {
            $prospect->update([
                'status' => OutreachProspect::STATUS_REPLIED,
                'replied_at' => $prospect->replied_at ?? now(),
            ]);

            $dispatcher->stopAllFor(
                $prospect,
                OutreachEnrollment::STATUS_REPLIED,
                'They replied — the sequence stopped.',
            );
        }

        Notification::make()
            ->title($prospects->count().' marked as replied')
            ->body('Any follow-ups for these people are cancelled. Answer them from Leads if they came through the website, or straight from your mailbox.')
            ->success()
            ->send();
    }
}
