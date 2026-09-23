<?php

declare(strict_types=1);

namespace App\Filament\Resources\MailTemplates;

use App\Filament\Resources\MailTemplates\Pages\CreateMailTemplate;
use App\Filament\Resources\MailTemplates\Pages\EditMailTemplate;
use App\Filament\Resources\MailTemplates\Pages\ListMailTemplates;
use App\Models\MailTemplate;
use App\Support\ServiceCatalog;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ReplicateAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use UnitEnum;

/**
 * The replies the studio can send, one per service ("profession").
 *
 * A lead should never receive a generic acknowledgement, and nobody should be
 * writing the same first paragraph twice. Each service gets a professional
 * starting point here; the sender edits it in the reply dialog before it goes
 * out, and more templates can be added for a service or for work we do not
 * offer yet.
 */
final class MailTemplateResource extends Resource
{
    protected static ?string $model = MailTemplate::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedEnvelope;

    protected static string|UnitEnum|null $navigationGroup = 'Leads';

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationLabel = 'Mail templates';

    protected static ?string $modelLabel = 'reply template';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('1. Which enquiries this reply is for')
                ->description('The reply picker offers templates written for the lead\'s service first, then general ones.')
                ->columns(2)
                ->schema([
                    Select::make('service')
                        ->label('Service')
                        ->options(ServiceCatalog::options())
                        ->placeholder('Any service — general reply')
                        ->helperText('Leave empty to offer this for every enquiry.')
                        ->native(false),

                    TextInput::make('name')
                        ->label('Template name')
                        ->required()
                        ->maxLength(120)
                        ->helperText('Only shown inside the panel, e.g. “Web app — first reply”.'),

                    Toggle::make('is_active')
                        ->label('Offer this when replying')
                        ->default(true)
                        ->helperText('Switch off to keep a draft without it appearing in the reply dialog.')
                        ->columnSpanFull(),
                ]),

            Section::make('2. The message')
                ->description('Write it as you would to a person. It is plain text, and line breaks are preserved.')
                ->schema([
                    TextInput::make('subject')
                        ->required()
                        ->maxLength(180),

                    Textarea::make('body')
                        ->label('Message')
                        ->required()
                        ->minLength(20)
                        ->maxLength(5000)
                        ->rows(20)
                        ->columnSpanFull(),
                ]),

            Section::make('Placeholders')
                ->description('These are filled in from the enquiry before the message is shown in the reply dialog.')
                ->collapsed()
                ->schema([
                    Textarea::make('token_reference')
                        ->label('Available placeholders')
                        ->disabled()
                        ->dehydrated(false)
                        ->rows(6)
                        ->default(implode("\n", [
                            '{name}    → the person who enquired',
                            '{company} → their company, or “your team”',
                            '{service} → the service they asked about',
                            '{studio}  → '.(string) config('mail.reply_to.name'),
                        ]))
                        ->columnSpanFull(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('service')
            ->columns([
                TextColumn::make('name')
                    ->label('Template')
                    ->searchable()
                    ->sortable()
                    ->wrap()
                    ->description(fn (MailTemplate $record): string => $record->serviceLabel()),

                TextColumn::make('subject')
                    ->label('Subject line')
                    ->limit(54)
                    ->tooltip(fn (MailTemplate $record): string => $record->subject)
                    ->toggleable(),

                TextColumn::make('service')
                    ->label('Service')
                    ->badge()
                    ->formatStateUsing(fn (?string $state, MailTemplate $record): string => $record->serviceLabel())
                    ->color(fn (?string $state): string => $state === null ? 'gray' : 'primary'),

                IconColumn::make('is_active')
                    ->label('Offered')
                    ->boolean(),

                TextColumn::make('updated_at')
                    ->label('Updated')
                    ->since()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('service')
                    ->label('Service')
                    ->options(ServiceCatalog::options())
                    ->placeholder('Any service'),
                TernaryFilter::make('is_active')->label('Offered when replying'),
            ])
            ->recordActions([
                EditAction::make(),
                ReplicateAction::make()
                    ->label('Duplicate')
                    ->modalDescription('Creates a copy you can rewrite for another service.')
                    ->beforeReplicaSaved(function (MailTemplate $replica): void {
                        $replica->name = $replica->name.' (copy)';
                    }),
                DeleteAction::make(),
            ])
            ->emptyStateHeading('No reply templates yet')
            ->emptyStateDescription('Add one professional reply per service so answering a lead takes a minute, not an afternoon.')
            ->emptyStateIcon('heroicon-o-envelope');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListMailTemplates::route('/'),
            'create' => CreateMailTemplate::route('/create'),
            'edit' => EditMailTemplate::route('/{record}/edit'),
        ];
    }
}
