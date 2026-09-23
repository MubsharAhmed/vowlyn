<?php

declare(strict_types=1);

namespace App\Filament\Resources\ContactRequests\Schemas;

use App\Models\ContactRequest;
use App\Support\MailDelivery;
use App\Support\ServiceCatalog;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Callout;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class ContactRequestForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                // Only shown while replies would be written to the log instead of
                // delivered, so nobody mistakes a recorded reply for a sent one.
                Callout::make()
                    ->color('warning')
                    ->icon(Heroicon::OutlinedExclamationTriangle)
                    ->heading('Replies cannot be delivered yet')
                    ->description(function (): string {
                        return MailDelivery::problem()
                            .' Add the studio\'s mail credentials to the server\'s .env file, then run'
                            .' php artisan config:cache. Until then, copy the lead\'s address and answer'
                            .' from a normal mailbox — sending from here would only write the message to'
                            .' the application log.';
                    })
                    ->visible(fn (): bool => ! MailDelivery::isLive())
                    ->columnSpanFull(),

                Section::make('Contact details')
                    ->description('Information provided by the lead.')
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('name')
                                ->required()
                                ->maxLength(120),
                            TextInput::make('email')
                                ->label('Email address')
                                ->email()
                                ->required()
                                ->maxLength(160),
                        ]),
                        Select::make('service')
                            ->options(ServiceCatalog::options())
                            ->required()
                            ->native(false),
                        Textarea::make('brief')
                            ->label('Project brief')
                            ->required()
                            ->rows(8)
                            ->columnSpanFull(),
                    ]),

                Section::make('Pipeline')
                    ->description('Track this request through the response workflow.')
                    ->schema([
                        Grid::make(2)->schema([
                            Select::make('status')
                                ->options(ContactRequest::statuses())
                                ->default(ContactRequest::STATUS_NEW)
                                ->required()
                                ->native(false),
                            DateTimePicker::make('replied_at')
                                ->label('Replied at')
                                ->seconds(false),
                        ]),
                    ]),

                Section::make('Metadata')
                    ->description('Captured automatically at submission.')
                    ->collapsed()
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('ip_address')
                                ->label('IP address')
                                ->disabled(),
                            TextInput::make('user_agent')
                                ->disabled(),
                        ]),
                    ]),
            ]);
    }
}
