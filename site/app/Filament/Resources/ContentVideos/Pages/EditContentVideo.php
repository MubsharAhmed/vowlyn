<?php

declare(strict_types=1);

namespace App\Filament\Resources\ContentVideos\Pages;

use App\Filament\Resources\ContentVideos\ContentVideoResource;
use App\Services\ContentVideoService;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

final class EditContentVideo extends EditRecord
{
    protected static string $resource = ContentVideoResource::class;

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        return app(ContentVideoService::class)->save($record, $data);
    }

    protected function afterSave(): void
    {
        $this->fillForm();
    }

    protected function getHeaderActions(): array
    {
        return [Action::make('view_page')->label('View content page')->url(url('/content-creation#work'))->openUrlInNewTab(), DeleteAction::make()->label('Move to Trash'), RestoreAction::make(), ForceDeleteAction::make()];
    }
}
