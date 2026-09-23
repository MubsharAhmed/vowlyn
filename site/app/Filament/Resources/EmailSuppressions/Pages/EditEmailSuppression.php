<?php

declare(strict_types=1);

namespace App\Filament\Resources\EmailSuppressions\Pages;

use App\Filament\Resources\EmailSuppressions\EmailSuppressionResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

final class EditEmailSuppression extends EditRecord
{
    protected static string $resource = EmailSuppressionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->label('Allow contact again')
                ->modalHeading('Allow this address to be emailed again?')
                ->modalDescription('Removing the row does not contact anybody — it only stops blocking them. Sequences that were already stopped stay stopped, so re-adding them to a campaign is a deliberate act.'),
        ];
    }
}
