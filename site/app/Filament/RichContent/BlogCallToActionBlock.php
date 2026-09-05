<?php

declare(strict_types=1);

namespace App\Filament\RichContent;

use Filament\Actions\Action;
use Filament\Forms\Components\RichEditor\RichContentCustomBlock;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;

final class BlogCallToActionBlock extends RichContentCustomBlock
{
    public static function getId(): string
    {
        return 'blog-cta';
    }

    public static function getLabel(): string
    {
        return 'Call to action';
    }

    public static function configureEditorAction(Action $action): Action
    {
        return $action->schema([
            Select::make('tone')
                ->options(['primary' => 'Primary', 'dark' => 'Dark'])
                ->default('primary')
                ->required(),
            TextInput::make('heading')->required()->maxLength(120),
            Textarea::make('description')->required()->rows(3)->maxLength(320),
            TextInput::make('button_label')->required()->default('Start a project')->maxLength(60),
            TextInput::make('button_url')->required()->url()->default(url('/#contact'))->maxLength(2048),
        ]);
    }

    /** @param array<string, mixed> $config */
    public static function toPreviewHtml(array $config): string
    {
        return view('filament.rich-content.blog-cta-preview', ['config' => $config])->render();
    }

    /** @param array<string, mixed> $config @param array<string, mixed> $data */
    public static function toHtml(array $config, array $data): string
    {
        return view('components.blog.call-to-action', ['config' => $config])->render();
    }
}
