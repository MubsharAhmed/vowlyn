<?php

declare(strict_types=1);

namespace App\Filament\Resources\OutreachProspects\Schemas;

use App\Models\OutreachProspect;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Callout;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

final class OutreachProspectForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Callout::make()
                ->color('danger')
                ->icon(Heroicon::OutlinedNoSymbol)
                ->heading('This address is on the opt-out list')
                ->description('No campaign can email it, whatever its status says. To allow contact again, remove it from the opt-out list screen — that is deliberately a separate, deliberate step.')
                ->visible(fn (?OutreachProspect $record): bool => (bool) $record?->isSuppressed())
                ->columnSpanFull(),

            Section::make('Who they are')
                ->description('The more of this is filled in, the less the email reads like a mailshot — {first_name}, {company} and {industry} are what carry the message.')
                ->schema([
                    Grid::make(2)->schema([
                        TextInput::make('company')
                            ->maxLength(150),

                        TextInput::make('contact_name')
                            ->label('Contact name')
                            ->maxLength(120),

                        TextInput::make('email')
                            ->label('Email address')
                            ->email()
                            ->required()
                            ->maxLength(180)
                            ->unique(ignoreRecord: true)
                            ->helperText('Unique across the list — the same person cannot be approached twice.'),

                        TextInput::make('role')
                            ->label('Role')
                            ->maxLength(120)
                            ->placeholder('Director'),

                        TextInput::make('website')
                            ->maxLength(200)
                            ->placeholder('example.com'),

                        TextInput::make('linkedin_url')
                            ->label('LinkedIn')
                            ->maxLength(200)
                            ->url(),
                    ]),
                ]),

            Section::make('Where they came from')
                ->schema([
                    Grid::make(3)->schema([
                        TextInput::make('industry')
                            ->maxLength(120)
                            ->placeholder('Accountancy'),

                        TextInput::make('region')
                            ->maxLength(120)
                            ->placeholder('Manchester'),

                        TextInput::make('source')
                            ->label('Source')
                            ->maxLength(60)
                            ->placeholder('Local directory')
                            ->helperText('Kept because “where did you get my address?” gets asked.'),
                    ]),

                    Textarea::make('notes')
                        ->rows(3)
                        ->columnSpanFull(),
                ]),

            Section::make('Status')
                ->description('Status decides whether they can be contacted and whether their sequences keep running.')
                ->schema([
                    Select::make('status')
                        ->options(OutreachProspect::statuses())
                        ->required()
                        ->native(false)
                        ->helperText('Choosing “Unsubscribed” also adds them to the opt-out list and stops their sequences. Choosing “Not interested” or “Bounced” stops them too. Removing somebody from the opt-out list is done on the Opt-out list screen.')
                        ->columnSpanFull(),
                ]),
        ]);
    }
}
