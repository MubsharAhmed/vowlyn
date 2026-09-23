<?php

declare(strict_types=1);

namespace App\Filament\Resources\OutreachProspects\Pages;

use App\Filament\Resources\OutreachProspects\Actions\ProspectActions;
use App\Filament\Resources\OutreachProspects\OutreachProspectResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

final class ListOutreachProspects extends ListRecords
{
    protected static string $resource = OutreachProspectResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ProspectActions::import(),
            CreateAction::make()->label('Add a prospect'),
        ];
    }
}
