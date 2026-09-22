<?php

declare(strict_types=1);

namespace App\Filament\Resources\ContentWorks\Pages;

use App\Filament\Resources\ContentWorks\ContentWorkResource;
use App\Models\ContentWork;
use App\Services\ContentWorkImageService;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

final class CreateContentWork extends CreateRecord
{
    protected static string $resource = ContentWorkResource::class;

    protected static bool $canCreateAnother = false;

    protected function handleRecordCreation(array $data): Model
    {
        return app(ContentWorkImageService::class)->save(new ContentWork, $data);
    }

    protected function getRedirectUrl(): string
    {
        return self::getResource()::getUrl('edit', ['record' => $this->record]);
    }
}
