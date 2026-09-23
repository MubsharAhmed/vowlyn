<?php

declare(strict_types=1);

namespace App\Filament\Resources\OutreachProspects\RelationManagers;

use App\Models\OutreachEnrollment;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

/**
 * Which sequences this person is in, and what stopped the ones that stopped.
 *
 * Read-only: the controls for a sequence live with the campaign, where the rest
 * of that sequence is visible. Reading here, acting there.
 */
final class EnrollmentsRelationManager extends RelationManager
{
    protected static string $relationship = 'enrollments';

    protected static ?string $title = 'Campaigns they are in';

    protected static bool $isLazy = false;

    public function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query): Builder => $query
                // The closure on a relation receives the relation, not a query
                // builder, so the count is eager-loaded without typing it.
                ->with(['campaign' => fn ($relation) => $relation->withCount('steps')]))
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('campaign.name')
                    ->label('Campaign')
                    ->wrap()
                    ->description(fn (OutreachEnrollment $record): string => 'Step '.$record->current_step
                        .' of '.(int) ($record->campaign?->steps_count ?? 0)),

                TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn (string $state, OutreachEnrollment $record): string => $record->statusLabel())
                    ->color(fn (OutreachEnrollment $record): string => match ($record->status) {
                        OutreachEnrollment::STATUS_ACTIVE => 'info',
                        OutreachEnrollment::STATUS_REPLIED, OutreachEnrollment::STATUS_INTERESTED => 'success',
                        OutreachEnrollment::STATUS_UNSUBSCRIBED, OutreachEnrollment::STATUS_BOUNCED => 'danger',
                        OutreachEnrollment::STATUS_PAUSED => 'warning',
                        default => 'gray',
                    }),

                TextColumn::make('next_send_at')
                    ->label('Due')
                    ->since()
                    ->placeholder('—'),

                TextColumn::make('stop_reason')
                    ->label('Why it stopped')
                    ->wrap()
                    ->placeholder('—')
                    ->toggleable(),
            ])
            ->emptyStateHeading('Not in any campaign')
            ->emptyStateDescription('Add them to a campaign from the Actions column on the Prospects list, or from inside the campaign.')
            ->emptyStateIcon('heroicon-o-paper-airplane');
    }
}
