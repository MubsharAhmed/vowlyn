<?php

declare(strict_types=1);

namespace App\Filament\Resources\BlogPosts\Pages;

use App\Filament\Resources\BlogPosts\BlogPostResource;
use App\Models\BlogPost;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\URL;

final class EditBlogPost extends EditRecord
{
    protected static string $resource = BlogPostResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if ($data['status'] === BlogPost::STATUS_PUBLISHED && blank($data['published_at'] ?? null)) {
            $data['published_at'] = now();
        }

        return $data;
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('preview')
                ->icon('heroicon-o-arrow-top-right-on-square')
                ->url(fn (): string => $this->record->isPubliclyVisible()
                    ? route('blog.show', $this->record->slug)
                    : URL::temporarySignedRoute('blog.preview', now()->addMinutes(30), ['post' => $this->record]))
                ->openUrlInNewTab(),
            DeleteAction::make(),
            RestoreAction::make(),
            ForceDeleteAction::make(),
        ];
    }
}
