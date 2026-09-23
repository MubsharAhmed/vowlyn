<?php

declare(strict_types=1);

namespace App\Filament\Resources\OutreachCampaigns\Pages;

use App\Filament\Resources\OutreachCampaigns\Actions\CheckCopyAction;
use App\Filament\Resources\OutreachCampaigns\Actions\SendDueNowAction;
use App\Filament\Resources\OutreachCampaigns\OutreachCampaignResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

final class EditOutreachCampaign extends EditRecord
{
    protected static string $resource = OutreachCampaignResource::class;

    protected function getHeaderActions(): array
    {
        return [
            SendDueNowAction::make(),
            CheckCopyAction::make(),
            DeleteAction::make(),
        ];
    }
}
