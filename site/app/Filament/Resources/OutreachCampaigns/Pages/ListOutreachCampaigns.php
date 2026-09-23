<?php

declare(strict_types=1);

namespace App\Filament\Resources\OutreachCampaigns\Pages;

use App\Filament\Resources\OutreachCampaigns\Actions\SendDueNowAction;
use App\Filament\Resources\OutreachCampaigns\OutreachCampaignResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

final class ListOutreachCampaigns extends ListRecords
{
    protected static string $resource = OutreachCampaignResource::class;

    protected function getHeaderActions(): array
    {
        return [
            SendDueNowAction::make(),
            CreateAction::make()->label('New campaign'),
        ];
    }
}
