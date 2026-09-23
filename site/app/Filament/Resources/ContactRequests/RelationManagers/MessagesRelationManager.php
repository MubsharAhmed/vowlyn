<?php

declare(strict_types=1);

namespace App\Filament\Resources\ContactRequests\RelationManagers;

use App\Models\ContactRequestMessage;
use App\Support\MailDelivery;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

/**
 * The outbound history: every reply sent to this lead, and what happened to it.
 *
 * Read-only on purpose. A record of what was actually sent stops being evidence
 * the moment it can be edited, and the reply dialog is the only place a message
 * should ever be composed.
 */
final class MessagesRelationManager extends RelationManager
{
    protected static string $relationship = 'messages';

    protected static ?string $title = 'Sent replies';

    /**
     * Render with the page rather than lazily. The whole point of this list is
     * answering "did we reply to this lead, and did it arrive?" — that should
     * never depend on a second request succeeding after the page has loaded.
     */
    protected static bool $isLazy = false;

    public function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('subject')
                    ->label('Subject')
                    ->searchable()
                    ->wrap()
                    ->description(function (ContactRequestMessage $record): string {
                        if ($record->sent_at === null) {
                            return 'Not sent';
                        }

                        // "Sent" next to "Logged only" would contradict itself.
                        return ($record->wasDelivered() ? 'Delivered ' : 'Recorded ')
                            .$record->sent_at->diffForHumans();
                    }),

                TextColumn::make('status')
                    ->label('Result')
                    ->badge()
                    ->formatStateUsing(fn (string $state, ContactRequestMessage $record): string => $record->statusLabel())
                    ->color(fn (ContactRequestMessage $record): string => match (true) {
                        $record->failed() => 'danger',
                        $record->wasDelivered() => 'success',
                        default => 'warning',
                    }),

                TextColumn::make('transport')
                    ->label('Mailer')
                    ->badge()
                    ->color(fn (string $state): string => MailDelivery::delivers($state) ? 'gray' : 'warning'),

                TextColumn::make('error')
                    ->label('Error')
                    ->limit(48)
                    ->tooltip(fn (?string $state): ?string => $state)
                    ->placeholder('—')
                    ->wrap()
                    ->toggleable(),

                TextColumn::make('body')
                    ->label('Message')
                    ->limit(60)
                    ->tooltip(fn (?string $state): ?string => $state)
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->recordActions([
                ViewAction::make()
                    ->label('Read')
                    ->modalHeading(fn (ContactRequestMessage $record): string => $record->subject)
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Close')
                    ->schema([
                        TextInput::make('to_email')->label('Sent to')->disabled(),
                        TextInput::make('to_name')->label('Name')->disabled(),
                        TextInput::make('status')
                            ->label('Result')
                            ->formatStateUsing(fn (string $state, ContactRequestMessage $record): string => $record->statusLabel())
                            ->disabled(),
                        TextInput::make('transport')
                            ->label('Mailer')
                            ->helperText(fn (ContactRequestMessage $record): ?string => $record->wasDelivered()
                                ? null
                                : 'This mailer records messages instead of delivering them.')
                            ->disabled(),
                        DateTimePicker::make('sent_at')
                            ->label('Sent at')
                            ->seconds(false)
                            ->disabled(),
                        Textarea::make('error')
                            ->label('Failure reason')
                            ->rows(2)
                            ->visible(fn (ContactRequestMessage $record): bool => $record->failed())
                            ->disabled()
                            ->columnSpanFull(),
                        Textarea::make('body')
                            ->label('Message as sent')
                            ->rows(16)
                            ->disabled()
                            ->columnSpanFull(),
                    ]),
            ])
            ->emptyStateHeading('No replies sent yet')
            ->emptyStateDescription('Use “Send reply” above to answer this enquiry by email.')
            ->emptyStateIcon('heroicon-o-envelope-open');
    }
}
