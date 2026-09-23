<?php

declare(strict_types=1);

namespace App\Filament\Resources\OutreachProspects\Tables;

use App\Filament\Resources\OutreachProspects\Actions\ProspectActions;
use App\Models\OutreachMessage;
use App\Models\OutreachProspect;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

final class OutreachProspectsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query): Builder => $query
                ->withCount(['messages as sent_count' => fn (Builder $inner) => $inner->where('status', OutreachMessage::STATUS_SENT)])
                ->withCount('enrollments'))
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('company')
                    ->label('Company')
                    ->searchable(['company', 'contact_name', 'email'])
                    ->sortable()
                    ->weight('semibold')
                    ->limit(30)
                    ->tooltip(fn (OutreachProspect $record): string => trim(sprintf(
                        '%s%s%s',
                        $record->company ?: $record->email,
                        $record->contact_name ? ' — '.$record->contact_name : '',
                        $record->role ? ', '.$record->role : '',
                    ), ' ,'))
                    ->placeholder('—')
                    ->description(fn (OutreachProspect $record): string => Str::limit(trim(sprintf(
                        '%s%s',
                        $record->contact_name ?: $record->email,
                        $record->role ? ', '.$record->role : '',
                    ), ', '), 34)),

                TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->copyable()
                    ->copyMessage('Email copied')
                    ->icon('heroicon-m-envelope'),

                TextColumn::make('industry')
                    ->badge()
                    ->color('gray')
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('region')
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn (string $state, OutreachProspect $record): string => $record->statusLabel())
                    ->color(fn (OutreachProspect $record): string => match ($record->status) {
                        OutreachProspect::STATUS_INTERESTED, OutreachProspect::STATUS_CONVERTED => 'success',
                        OutreachProspect::STATUS_REPLIED => 'info',
                        OutreachProspect::STATUS_NOT_INTERESTED, OutreachProspect::STATUS_UNSUBSCRIBED, OutreachProspect::STATUS_BOUNCED => 'danger',
                        OutreachProspect::STATUS_CONTACTED => 'warning',
                        default => 'gray',
                    }),

                TextColumn::make('sent_count')
                    ->label('Sent')
                    ->numeric()
                    ->sortable()
                    ->description(fn (OutreachProspect $record): string => $record->enrollments_count.' campaign(s)'),

                TextColumn::make('last_contacted_at')
                    ->label('Last contacted')
                    ->since()
                    ->sortable()
                    ->placeholder('Never'),

                TextColumn::make('source')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->placeholder('—'),

                TextColumn::make('created_at')
                    ->label('Added')
                    ->since()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(OutreachProspect::statuses())
                    ->placeholder('Every status'),

                TernaryFilter::make('last_contacted_at')
                    ->label('Contacted before')
                    ->placeholder('Anyone')
                    ->trueLabel('Already emailed')
                    ->falseLabel('Never emailed')
                    ->queries(
                        true: fn (Builder $query) => $query->whereNotNull('last_contacted_at'),
                        false: fn (Builder $query) => $query->whereNull('last_contacted_at'),
                    ),

                SelectFilter::make('industry')
                    ->options(fn (): array => OutreachProspect::query()
                        ->whereNotNull('industry')
                        ->distinct()
                        ->orderBy('industry')
                        ->pluck('industry', 'industry')
                        ->all())
                    ->placeholder('Every industry'),
            ])
            ->recordActions([
                ProspectActions::addToCampaignRecord(),
                ProspectActions::markRepliedRecord(),
                ProspectActions::markBounced(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    ProspectActions::addToCampaignBulk(),
                    ProspectActions::markRepliedBulk(),
                    ProspectActions::suppressBulk(),
                    DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading('No prospects yet')
            ->emptyStateDescription('Import a list from a spreadsheet, or add people one at a time. Nothing is emailed until a campaign is started.')
            ->emptyStateIcon('heroicon-o-user-group');
    }
}
