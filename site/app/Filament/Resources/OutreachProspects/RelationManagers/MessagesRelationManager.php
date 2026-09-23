<?php

declare(strict_types=1);

namespace App\Filament\Resources\OutreachProspects\RelationManagers;

use App\Models\OutreachMessage;
use App\Support\MailDelivery;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

/**
 * Every outreach email sent to this person, including the ones that were not
 * sent and why.
 *
 * Read-only, and eager for the same reason the lead reply history is: the
 * question this screen answers — "what have we actually said to them?" — should
 * not depend on a second request arriving after the page has loaded.
 */
final class MessagesRelationManager extends RelationManager
{
    protected static string $relationship = 'messages';

    protected static ?string $title = 'Outreach sent to them';

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
                    ->description(fn (OutreachMessage $record): string => trim(sprintf(
                        '%s · step %s%s',
                        $record->campaign?->name ?? 'Campaign removed',
                        $record->step_position ?? '?',
                        $record->sent_at !== null ? ' · '.$record->sent_at->diffForHumans() : '',
                    ))),

                TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn (string $state, OutreachMessage $record): string => $record->statusLabel())
                    ->color(fn (OutreachMessage $record): string => match (true) {
                        $record->failed() => 'danger',
                        $record->wasDelivered() => 'success',
                        $record->isSkipped() => 'gray',
                        default => 'warning',
                    })
                    ->description(fn (OutreachMessage $record): ?string => $record->skip_reason ?? $record->error),

                TextColumn::make('transport')
                    ->label('Mailer')
                    ->badge()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->color(fn (string $state): string => MailDelivery::delivers($state) ? 'gray' : 'warning'),
            ])
            ->recordActions([
                ViewAction::make()
                    ->label('Read')
                    ->modalHeading(fn (OutreachMessage $record): string => $record->subject)
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Close')
                    ->schema([
                        TextInput::make('to_email')->label('Sent to')->disabled(),
                        TextInput::make('status')
                            ->label('Result')
                            ->formatStateUsing(fn (string $state, OutreachMessage $record): string => $record->statusLabel())
                            ->disabled(),
                        TextInput::make('transport')
                            ->label('Mailer')
                            ->helperText(fn (OutreachMessage $record): ?string => $record->wasDelivered()
                                ? null
                                : 'This mailer records messages instead of delivering them.')
                            ->disabled(),
                        DateTimePicker::make('sent_at')
                            ->label('Sent at')
                            ->seconds(false)
                            ->disabled(),
                        Textarea::make('skip_reason')
                            ->label('Why it was not sent')
                            ->rows(2)
                            ->visible(fn (OutreachMessage $record): bool => filled($record->skip_reason) || filled($record->error))
                            ->formatStateUsing(fn (OutreachMessage $record): string => (string) ($record->skip_reason ?? $record->error))
                            ->disabled()
                            ->columnSpanFull(),
                        Textarea::make('body')
                            ->label('Message as sent')
                            ->rows(16)
                            ->disabled()
                            ->columnSpanFull(),
                    ]),
            ])
            ->emptyStateHeading('Nothing sent to this person yet')
            ->emptyStateDescription('Once a sequence reaches them, every message and its outcome appears here.')
            ->emptyStateIcon('heroicon-o-paper-airplane');
    }
}
