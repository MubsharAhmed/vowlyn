<?php

declare(strict_types=1);

namespace App\Filament\Resources\OutreachCampaigns\RelationManagers;

use App\Models\OutreachCampaign;
use App\Models\OutreachEnrollment;
use App\Models\OutreachProspect;
use App\Services\OutreachDispatcher;
use App\Services\OutreachSender;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

/**
 * Who is in this sequence, where each of them is, and when they are next due.
 *
 * This is the screen that answers "what is about to happen?" — the question a
 * campaign list cannot answer, and the one people ask before letting a sequence
 * run. Rendered eagerly for the same reason the reply history is: it should not
 * depend on a second request after the page has loaded.
 */
final class EnrollmentsRelationManager extends RelationManager
{
    protected static string $relationship = 'enrollments';

    protected static ?string $title = 'People in this sequence';

    protected static bool $isLazy = false;

    public function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query): Builder => $query
                ->with('prospect')
                // A closure on a relationship receives the relation rather than
                // a query builder, so the step count is added without typing it.
                ->with(['campaign' => fn ($relation) => $relation->withCount('steps')])
                ->withCount('messages'))
            ->defaultSort('next_send_at')
            ->columns([
                TextColumn::make('prospect.company')
                    ->label('Company')
                    ->searchable()
                    ->wrap()
                    ->description(fn (OutreachEnrollment $record): string => $record->prospect?->contact_name ?? '—'),

                TextColumn::make('prospect.email')
                    ->label('Email')
                    ->searchable()
                    ->copyable()
                    ->copyMessage('Email copied'),

                TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn (string $state, OutreachEnrollment $record): string => $record->statusLabel())
                    ->color(fn (OutreachEnrollment $record): string => match ($record->status) {
                        OutreachEnrollment::STATUS_ACTIVE => 'info',
                        OutreachEnrollment::STATUS_REPLIED, OutreachEnrollment::STATUS_INTERESTED => 'success',
                        OutreachEnrollment::STATUS_UNSUBSCRIBED, OutreachEnrollment::STATUS_BOUNCED => 'danger',
                        OutreachEnrollment::STATUS_PAUSED => 'warning',
                        default => 'gray',
                    })
                    ->description(fn (OutreachEnrollment $record): ?string => $record->stop_reason),

                TextColumn::make('current_step')
                    ->label('Next step')
                    ->state(fn (OutreachEnrollment $record): string => sprintf(
                        '%d of %d',
                        $record->current_step,
                        (int) ($record->campaign?->steps_count ?? 0),
                    )),

                TextColumn::make('next_send_at')
                    ->label('Due')
                    ->since()
                    ->placeholder('—')
                    ->color(fn (OutreachEnrollment $record): string => $record->isActive()
                        && $record->next_send_at !== null
                        && $record->next_send_at->isPast() ? 'warning' : 'gray'),

                TextColumn::make('messages_count')
                    ->label('Sent')
                    ->numeric()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->headerActions([
                self::addPeopleAction(),
            ])
            ->recordActions([
                Action::make('sendNow')
                    ->label('Send now')
                    ->icon(Heroicon::OutlinedPaperAirplane)
                    ->visible(fn (OutreachEnrollment $record): bool => $record->isActive())
                    ->requiresConfirmation()
                    ->modalHeading('Send this person\'s next step now?')
                    ->modalDescription(fn (OutreachEnrollment $record): string => 'This sends step '.$record->current_step.' to '
                        .$record->prospect?->email.' immediately, ignoring the waiting time but not the sending window or the daily limit.')
                    ->action(function (OutreachEnrollment $record): void {
                        $message = app(OutreachSender::class)->send($record);

                        if ($message === null) {
                            Notification::make()
                                ->title('Nothing to send')
                                ->body('There is no step left for this person.')
                                ->warning()
                                ->send();

                            return;
                        }

                        $notification = Notification::make()
                            ->title($message->statusLabel())
                            ->body($message->skip_reason
                                ?? $message->error
                                ?? 'Subject: '.$message->subject);

                        match (true) {
                            $message->failed() => $notification->danger(),
                            $message->wasDelivered() => $notification->success(),
                            default => $notification->warning(),
                        };

                        $notification->send();
                    }),

                Action::make('stopSequence')
                    ->label('Stop')
                    ->icon(Heroicon::OutlinedStopCircle)
                    ->color('warning')
                    ->visible(fn (OutreachEnrollment $record): bool => $record->isActive() || $record->status === OutreachEnrollment::STATUS_PAUSED)
                    ->modalHeading('Stop this sequence')
                    ->modalSubmitActionLabel('Stop it')
                    ->schema([
                        Select::make('status')
                            ->label('How to stop')
                            ->options([
                                OutreachEnrollment::STATUS_PAUSED => 'Pause — I may resume this later',
                                OutreachEnrollment::STATUS_NOT_INTERESTED => 'Stop for good — do not contact again',
                            ])
                            ->default(OutreachEnrollment::STATUS_PAUSED)
                            ->required()
                            ->native(false),
                        TextInput::make('reason')
                            ->label('Reason (kept on the record)')
                            ->maxLength(200),
                    ])
                    ->action(function (array $data, OutreachEnrollment $record): void {
                        $record->stop($data['status'], $data['reason'] ?: 'Stopped by '.(auth()->user()?->name ?? 'the studio').'.');

                        if ($data['status'] === OutreachEnrollment::STATUS_NOT_INTERESTED && $record->prospect !== null) {
                            $record->prospect->update(['status' => OutreachProspect::STATUS_NOT_INTERESTED]);

                            app(OutreachDispatcher::class)->stopAllFor(
                                $record->prospect,
                                OutreachEnrollment::STATUS_NOT_INTERESTED,
                                'Marked as not interested.',
                            );
                        }

                        Notification::make()
                            ->title('Sequence stopped')
                            ->body('No further steps will be sent to this person.')
                            ->success()
                            ->send();
                    }),

                Action::make('resumeSequence')
                    ->label('Resume')
                    ->icon(Heroicon::OutlinedPlayCircle)
                    ->color('success')
                    ->visible(fn (OutreachEnrollment $record): bool => $record->status === OutreachEnrollment::STATUS_PAUSED)
                    ->requiresConfirmation()
                    ->modalDescription('The next step becomes due immediately, subject to the sending window and the daily limit.')
                    ->action(function (OutreachEnrollment $record): void {
                        if ($record->prospect !== null && ! $record->prospect->isReachable()) {
                            Notification::make()
                                ->title('This person cannot be contacted')
                                ->body('They are on the opt-out list, or marked as “'.$record->prospect->statusLabel().'”.')
                                ->danger()
                                ->send();

                            return;
                        }

                        $record->resume();

                        Notification::make()
                            ->title('Sequence resumed')
                            ->body('The next step is due now.')
                            ->success()
                            ->send();
                    }),
            ])
            ->emptyStateHeading('Nobody in this sequence yet')
            ->emptyStateDescription('Use “Add people” to pick prospects, or select a batch on the Prospects screen and add them to this campaign.')
            ->emptyStateIcon('heroicon-o-users');
    }

    /**
     * Adding people belongs to the campaign rather than to any row in it, so it
     * is a header action.
     */
    public static function addPeopleAction(): Action
    {
        return Action::make('enrollPeople')
            ->label('Add people')
            ->icon(Heroicon::OutlinedUserPlus)
            ->modalHeading('Add people to this sequence')
            ->modalDescription('Anyone on the opt-out list, already in this campaign, or marked as not interested is skipped automatically — you will be told who was left out.')
            ->modalSubmitActionLabel('Add them')
            ->schema([
                Select::make('prospects')
                    ->label('People')
                    ->multiple()
                    ->required()
                    ->searchable()
                    ->getSearchResultsUsing(fn (string $search): array => self::search($search))
                    ->getOptionLabelsUsing(fn (array $values): array => self::labels($values))
                    ->helperText('Search by company, name or email address.'),
            ])
            ->action(function (array $data, RelationManager $livewire): void {
                /** @var OutreachCampaign $campaign */
                $campaign = $livewire->getOwnerRecord();

                $prospects = OutreachProspect::query()->whereIn('id', $data['prospects'] ?? [])->get();
                $result = app(OutreachDispatcher::class)->enrollMany($prospects, $campaign);

                $notification = Notification::make()
                    ->title($result['enrolled'].' person(s) added')
                    ->body($result['skipped'] === []
                        ? 'The next run will send what is due for each of them.'
                        : 'Left out: '.collect($result['skipped'])
                            ->map(fn (string $reason, string $email): string => $email.' ('.$reason.')')
                            ->take(5)
                            ->implode(', ')
                            .(count($result['skipped']) > 5 ? ' and '.(count($result['skipped']) - 5).' more' : ''))
                    ->success();

                if ($result['enrolled'] === 0) {
                    $notification->warning()->title('Nobody was added');
                }

                $notification->send();
            });
    }

    /**
     * @return array<int, string>
     */
    private static function search(string $search): array
    {
        return OutreachProspect::query()
            ->reachable()
            ->where(fn (Builder $query) => $query
                ->where('company', 'like', '%'.$search.'%')
                ->orWhere('contact_name', 'like', '%'.$search.'%')
                ->orWhere('email', 'like', '%'.$search.'%'))
            ->orderBy('company')
            ->limit(25)
            ->get()
            ->mapWithKeys(fn (OutreachProspect $prospect): array => [$prospect->getKey() => self::label($prospect)])
            ->all();
    }

    /**
     * @param  array<int, int|string>  $values
     * @return array<int, string>
     */
    private static function labels(array $values): array
    {
        return OutreachProspect::query()
            ->whereIn('id', $values)
            ->get()
            ->mapWithKeys(fn (OutreachProspect $prospect): array => [$prospect->getKey() => self::label($prospect)])
            ->all();
    }

    private static function label(OutreachProspect $prospect): string
    {
        return trim(sprintf(
            '%s — %s',
            $prospect->company ?: $prospect->email,
            $prospect->contact_name ?: $prospect->email,
        ), ' —');
    }
}
