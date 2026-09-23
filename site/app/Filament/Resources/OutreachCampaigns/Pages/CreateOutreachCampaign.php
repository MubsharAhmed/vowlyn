<?php

declare(strict_types=1);

namespace App\Filament\Resources\OutreachCampaigns\Pages;

use App\Filament\Resources\OutreachCampaigns\OutreachCampaignResource;
use App\Models\OutreachCampaign;
use Filament\Resources\Pages\CreateRecord;

final class CreateOutreachCampaign extends CreateRecord
{
    protected static string $resource = OutreachCampaignResource::class;

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Campaign saved as a draft — nothing is sending yet';
    }

    /**
     * Every new campaign begins as a draft, whatever the form says.
     *
     * The form exposes the status for editing an existing campaign, but the act
     * of creating one should never be the act of starting to email strangers:
     * those two intents are easy to confuse and impossible to un-send.
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['status'] = OutreachCampaign::STATUS_DRAFT;

        return $data;
    }
}
