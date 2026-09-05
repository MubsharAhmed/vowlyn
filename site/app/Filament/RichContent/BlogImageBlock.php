<?php

declare(strict_types=1);

namespace App\Filament\RichContent;

use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor\RichContentCustomBlock;
use Filament\Forms\Components\TextInput;

final class BlogImageBlock extends RichContentCustomBlock
{
    public static function getId(): string
    {
        return 'blog-image';
    }

    public static function getLabel(): string
    {
        return 'Editorial image';
    }

    public static function configureEditorAction(Action $action): Action
    {
        return $action->schema([
            FileUpload::make('path')
                ->label('Image')
                ->disk('public')
                ->directory('blog/content')
                ->visibility('public')
                ->image()
                ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                ->maxSize(5120)
                ->imageEditor()
                ->imageResizeMode('contain')
                ->imageResizeTargetWidth('1800')
                ->preventFilePathTampering()
                ->required(),
            TextInput::make('alt')
                ->label('Alternative text')
                ->helperText('Describe what the image shows. Leave decorative images out of articles instead of using empty text.')
                ->required()
                ->maxLength(180),
            TextInput::make('caption')->maxLength(240),
            TextInput::make('credit')->label('Source / credit')->maxLength(180),
        ]);
    }

    /** @param array<string, mixed> $config */
    public static function toPreviewHtml(array $config): string
    {
        return view('filament.rich-content.blog-image-preview', ['config' => $config])->render();
    }

    /** @param array<string, mixed> $config @param array<string, mixed> $data */
    public static function toHtml(array $config, array $data): string
    {
        return view('components.blog.editorial-image', ['config' => $config])->render();
    }
}
