<?php

declare(strict_types=1);

namespace App\Filament\Resources\MailTemplates\Pages;

use App\Filament\Resources\MailTemplates\MailTemplateResource;
use Filament\Resources\Pages\CreateRecord;

final class CreateMailTemplate extends CreateRecord
{
    protected static string $resource = MailTemplateResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
