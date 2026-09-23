<?php

namespace App\Filament\Resources\ContactRequests\Pages;

use App\Filament\Resources\ContactRequests\Actions\SendReplyAction;
use App\Filament\Resources\ContactRequests\ContactRequestResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditContactRequest extends EditRecord
{
    protected static string $resource = ContactRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            SendReplyAction::make(),
            DeleteAction::make(),
        ];
    }
}
