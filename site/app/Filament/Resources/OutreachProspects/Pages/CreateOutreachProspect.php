<?php

declare(strict_types=1);

namespace App\Filament\Resources\OutreachProspects\Pages;

use App\Filament\Resources\OutreachProspects\OutreachProspectResource;
use App\Models\OutreachProspect;
use Filament\Resources\Pages\CreateRecord;

final class CreateOutreachProspect extends CreateRecord
{
    protected static string $resource = OutreachProspectResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['status'] = $data['status'] ?? OutreachProspect::STATUS_NEW;

        return $data;
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Prospect saved — add them to a campaign to start a sequence';
    }
}
