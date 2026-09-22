<?php

declare(strict_types=1);

namespace App\Filament\Resources\ContentWorks\Pages;

use App\Filament\Resources\ContentWorks\ContentWorkResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

final class ListContentWorks extends ListRecords
{
    protected static string $resource = ContentWorkResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()->label('Add creative work')];
    }
}
