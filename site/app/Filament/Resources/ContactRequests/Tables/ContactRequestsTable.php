<?php

declare(strict_types=1);

namespace App\Filament\Resources\ContactRequests\Tables;

use App\Models\ContactRequest;
use App\Support\ServiceCatalog;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ContactRequestsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('created_at')
                    ->label('Received')
                    ->dateTime('M j, Y · g:i A')
                    ->sortable()
                    ->since()
                    ->tooltip(fn ($state) => $state?->format('F j, Y · H:i')),

                TextColumn::make('name')
                    ->searchable()
                    ->weight('semibold'),

                TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->copyable()
                    ->copyMessage('Email copied')
                    ->icon('heroicon-m-envelope'),

                TextColumn::make('service')
                    ->formatStateUsing(fn (?string $state): string => ServiceCatalog::options()[$state] ?? '—')
                    ->badge()
                    ->color('primary'),

                TextColumn::make('brief')
                    ->label('Brief')
                    ->limit(60)
                    ->tooltip(fn ($state) => $state)
                    ->wrap(),

                TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => ContactRequest::statuses()[$state] ?? $state)
                    ->color(fn (string $state): string => match ($state) {
                        ContactRequest::STATUS_NEW => 'primary',
                        ContactRequest::STATUS_REVIEWED => 'warning',
                        ContactRequest::STATUS_REPLIED => 'success',
                        ContactRequest::STATUS_ARCHIVED => 'gray',
                        default => 'gray',
                    }),

                TextColumn::make('replied_at')
                    ->label('Replied')
                    ->dateTime('M j, Y')
                    ->sortable()
                    ->placeholder('—')
                    ->toggleable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(ContactRequest::statuses())
                    ->multiple(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading('No contact requests yet')
            ->emptyStateDescription('Inbound leads from the website contact form will appear here.')
            ->emptyStateIcon('heroicon-o-inbox');
    }
}
