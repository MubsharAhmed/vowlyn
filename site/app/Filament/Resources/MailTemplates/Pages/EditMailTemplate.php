<?php

declare(strict_types=1);

namespace App\Filament\Resources\MailTemplates\Pages;

use App\Filament\Resources\MailTemplates\MailTemplateResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ReplicateAction;
use Filament\Resources\Pages\EditRecord;

final class EditMailTemplate extends EditRecord
{
    protected static string $resource = MailTemplateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ReplicateAction::make()
                ->label('Duplicate')
                ->beforeReplicaSaved(function ($replica): void {
                    $replica->name = $replica->name.' (copy)';
                }),
            DeleteAction::make(),
        ];
    }
}
