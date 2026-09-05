<?php

declare(strict_types=1);

namespace App\Filament\Resources\ContentVideos\Pages;

use App\Filament\Resources\ContentVideos\ContentVideoResource;
use App\Models\ContentVideo;
use App\Services\ContentVideoService;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

final class CreateContentVideo extends CreateRecord
{
    protected static string $resource = ContentVideoResource::class;

    protected static bool $canCreateAnother = false;

    protected function handleRecordCreation(array $data): Model
    {
        return app(ContentVideoService::class)->save(new ContentVideo, $data);
    }

    protected function getRedirectUrl(): string
    {
        return self::getResource()::getUrl('edit', ['record' => $this->record]);
    }
}
