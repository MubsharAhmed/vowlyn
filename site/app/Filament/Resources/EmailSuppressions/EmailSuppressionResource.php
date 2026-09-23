<?php

declare(strict_types=1);

namespace App\Filament\Resources\EmailSuppressions;

use App\Filament\Resources\EmailSuppressions\Pages\CreateEmailSuppression;
use App\Filament\Resources\EmailSuppressions\Pages\EditEmailSuppression;
use App\Filament\Resources\EmailSuppressions\Pages\ListEmailSuppressions;
use App\Models\EmailSuppression;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use UnitEnum;

/**
 * The opt-out list, which is the last word on who may be emailed.
 *
 * It exists as its own screen rather than as a flag on a prospect because it has
 * to hold people who were never prospects, and because removing somebody from it
 * is a decision with consequences — an address that asked us to stop, or that
 * bounced, is a fact about the address, not about our list.
 */
final class EmailSuppressionResource extends Resource
{
    protected static ?string $model = EmailSuppression::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedNoSymbol;

    protected static string|UnitEnum|null $navigationGroup = 'Outreach';

    protected static ?int $navigationSort = 3;

    protected static ?string $navigationLabel = 'Opt-out list';

    protected static ?string $modelLabel = 'opt-out';

    protected static ?string $recordTitleAttribute = 'email';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Address')
                ->description('Everyone on this list is excluded from every campaign, whatever any prospect record says.')
                ->columns(2)
                ->schema([
                    TextInput::make('email')
                        ->label('Email address')
                        ->email()
                        ->required()
                        ->maxLength(180)
                        ->unique(ignoreRecord: true)
                        ->columnSpanFull(),

                    Select::make('reason')
                        ->options(EmailSuppression::reasons())
                        ->required()
                        ->native(false)
                        ->default(EmailSuppression::REASON_ASKED),

                    TextInput::make('note')
                        ->label('Note')
                        ->maxLength(200),

                    TextInput::make('source')
                        ->label('Where it came from')
                        ->maxLength(60)
                        ->placeholder('admin panel or opt-out link')
                        ->columnSpanFull(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('suppressed_at', 'desc')
            ->columns([
                TextColumn::make('email')
                    ->label('Address')
                    ->searchable()
                    ->copyable()
                    ->copyMessage('Email copied')
                    ->icon('heroicon-m-no-symbol'),

                TextColumn::make('reason')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => EmailSuppression::reasonLabel($state))
                    ->color(fn (string $state): string => match ($state) {
                        EmailSuppression::REASON_UNSUBSCRIBED => 'info',
                        EmailSuppression::REASON_BOUNCED, EmailSuppression::REASON_COMPLAINT => 'danger',
                        default => 'gray',
                    }),

                TextColumn::make('note')
                    ->wrap()
                    ->placeholder('—')
                    ->toggleable(),

                TextColumn::make('source')
                    ->label('Added from')
                    ->badge()
                    ->color('gray')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->placeholder('—'),

                TextColumn::make('suppressed_at')
                    ->label('Added')
                    ->since()
                    ->sortable()
                    ->placeholder('—'),
            ])
            ->filters([
                SelectFilter::make('reason')
                    ->options(EmailSuppression::reasons())
                    ->placeholder('Every reason'),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make()
                    ->label('Allow contact again')
                    ->modalHeading('Allow this address to be emailed again?')
                    ->modalDescription('Removing the row does not contact anybody — it only stops blocking them. If this person asked not to be contacted, or their address bounces, the right answer is to leave it here.')
                    ->modalSubmitActionLabel('Allow contact again'),
            ])
            ->emptyStateHeading('Nobody has opted out')
            ->emptyStateDescription('Addresses appear here automatically when somebody uses the unsubscribe link in an email.')
            ->emptyStateIcon('heroicon-o-no-symbol');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListEmailSuppressions::route('/'),
            'create' => CreateEmailSuppression::route('/create'),
            'edit' => EditEmailSuppression::route('/{record}/edit'),
        ];
    }
}
