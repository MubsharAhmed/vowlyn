<?php

declare(strict_types=1);

namespace App\Filament\Resources\OutreachCampaigns\Tables;

use App\Filament\Resources\OutreachCampaigns\Actions\CheckCopyAction;
use App\Filament\Resources\OutreachCampaigns\Actions\PauseCampaignAction;
use App\Filament\Resources\OutreachCampaigns\Actions\ResumeCampaignAction;
use App\Filament\Resources\OutreachCampaigns\Actions\SendDueNowAction;
use App\Models\OutreachCampaign;
use App\Models\OutreachEnrollment;
use App\Models\OutreachMessage;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

final class OutreachCampaignsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            /*
            | Counts are aggregated in the query rather than lazily per row: a
            | campaign list that fires four queries per line is fine with five
            | campaigns and unusable with fifty.
            */
            ->modifyQueryUsing(fn (Builder $query): Builder => $query->withCount([
                'steps',
                'enrollments',
                'enrollments as replied_enrollments_count' => fn (Builder $inner) => $inner->whereIn('status', OutreachCampaign::ENGAGED_STATUSES),
                'messages as sent_messages_count' => fn (Builder $inner) => $inner->where('status', OutreachMessage::STATUS_SENT),
            ])->withMin(
                ['enrollments as next_due_at' => fn (Builder $inner) => $inner->where('status', OutreachEnrollment::STATUS_ACTIVE)],
                'next_send_at',
            ))
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('name')
                    ->label('Campaign')
                    ->searchable()
                    ->sortable()
                    ->weight('semibold')
                    ->limit(46)
                    ->tooltip(fn (OutreachCampaign $record): string => $record->name
                        .($record->goal ? ' — offering '.$record->goal : ''))
                    ->description(fn (OutreachCampaign $record): string => $record->goal
                        ? 'Offering '.Str::limit($record->goal, 34)
                        : 'No offer set'),

                TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => OutreachCampaign::statuses()[$state] ?? $state)
                    ->color(fn (string $state): string => match ($state) {
                        OutreachCampaign::STATUS_ACTIVE => 'success',
                        OutreachCampaign::STATUS_PAUSED => 'warning',
                        OutreachCampaign::STATUS_FINISHED => 'info',
                        default => 'gray',
                    }),

                TextColumn::make('next_due_at')
                    ->label('Next send')
                    // A queued send in a campaign that is not sending is not news,
                    // and “27 minutes ago” next to “Draft” reads as a fault.
                    ->state(fn (OutreachCampaign $record): mixed => $record->isSending() ? $record->next_due_at : null)
                    ->since()
                    ->placeholder('Not sending')
                    ->color(fn (OutreachCampaign $record): string => $record->isSending()
                        && $record->next_due_at !== null
                        && Carbon::parse($record->next_due_at)->isPast() ? 'warning' : 'gray')
                    ->tooltip(fn (OutreachCampaign $record): ?string => $record->isSending()
                        ? 'Earliest queued send in this campaign'
                        : null),

                TextColumn::make('enrollments_count')
                    ->label('People')
                    ->numeric()
                    ->sortable(),

                TextColumn::make('sent_messages_count')
                    ->label('Sent')
                    ->numeric()
                    ->sortable(),

                TextColumn::make('replied_enrollments_count')
                    ->label('Replied')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('reply_rate')
                    ->label('Reply rate')
                    ->state(fn (OutreachCampaign $record): string => self::replyRate($record))
                    ->badge()
                    ->color(fn (OutreachCampaign $record): string => ((int) $record->replied_enrollments_count) > 0 ? 'success' : 'gray'),

                TextColumn::make('steps_count')
                    ->label('Steps')
                    ->numeric()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->label('Created')
                    ->since()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(OutreachCampaign::statuses())
                    ->placeholder('Every status'),
            ])
            ->recordActions([
                SendDueNowAction::make(),
                ResumeCampaignAction::make(),
                PauseCampaignAction::make(),
                CheckCopyAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading('No outreach campaigns yet')
            ->emptyStateDescription('A campaign is a first email plus its follow-ups. Build it as a draft, add people, then start it when the copy is right.')
            ->emptyStateIcon('heroicon-o-paper-airplane');
    }

    /**
     * Replies as a share of the people approached, not of the messages sent —
     * one person receiving three follow-ups should not count as three chances to
     * reply. Anything else flatters the numbers.
     */
    private static function replyRate(OutreachCampaign $record): string
    {
        $people = (int) $record->enrollments_count;

        if ($people === 0) {
            return '—';
        }

        return round(((int) $record->replied_enrollments_count / $people) * 100).'%';
    }
}
