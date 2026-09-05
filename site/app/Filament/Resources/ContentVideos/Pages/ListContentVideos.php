<?php

declare(strict_types=1);

namespace App\Filament\Resources\ContentVideos\Pages;

use App\Filament\Resources\ContentVideos\ContentVideoResource;
use App\Services\ContentVideoService;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

final class ListContentVideos extends ListRecords
{
    protected static string $resource = ContentVideoResource::class;

    public function getSubheading(): ?string
    {
        $used = round(app(ContentVideoService::class)->storageBytes() / 1048576, 1);

        return "Upload → describe → preview → publish. Uploaded media: {$used} MB / ".config('content-videos.storage_limit_mb').' MB (including Trash).';
    }

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()->label('Upload video')];
    }
}
