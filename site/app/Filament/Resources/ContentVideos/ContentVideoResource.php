<?php

declare(strict_types=1);

namespace App\Filament\Resources\ContentVideos;

use App\Filament\Resources\ContentVideos\Pages\CreateContentVideo;
use App\Filament\Resources\ContentVideos\Pages\EditContentVideo;
use App\Filament\Resources\ContentVideos\Pages\ListContentVideos;
use App\Models\ContentVideo;
use App\Services\ContentVideoService;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\View;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use UnitEnum;

final class ContentVideoResource extends Resource
{
    protected static ?string $model = ContentVideo::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedFilm;

    protected static string|UnitEnum|null $navigationGroup = 'Website content';

    protected static ?string $navigationLabel = 'Content videos';

    protected static ?string $recordTitleAttribute = 'title';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('1. Upload your video')->description(fn () => trim(app(ContentVideoService::class)->serverWarnings().' MP4 · H.264 / AAC · up to 50 MB · up to 3 minutes · 1080p · 60 fps · 12 Mbps. A cover is generated automatically. Videos are not re-encoded on the server.'))->schema([
                View::make('filament.content-videos.preview')->visible(fn (?ContentVideo $record) => $record !== null),
                FileUpload::make('video_upload')->label('Video file')->acceptedFileTypes(['video/mp4', 'application/mp4'])
                    ->maxSize(51200)->maxFiles(1)->storeFiles(false)->preventFilePathTampering()->previewable(false)
                    ->required(fn (string $operation) => $operation === 'create')
                    ->helperText('Drag your MP4 here or choose a file. On edit, leave empty to keep the current video. Wait for the upload to finish before saving.'),
                FileUpload::make('poster_upload')->label('Custom cover image (optional)')->image()
                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])->maxSize(2048)
                    ->storeFiles(false)->preventFilePathTampering()
                    ->helperText('JPG, PNG or WebP, up to 2 MB and 4096 × 4096 pixels. Leave empty for an automatic cover; replacing a video generates a new cover.'),
                Checkbox::make('rights_confirmed')->label('I have permission to publish this video and its music, images and people.')
                    ->accepted(fn (string $operation, Get $get) => $operation === 'create' || filled($get('video_upload'))),
            ])->columnSpanFull(),
            Section::make('2. Describe the work')->columns(2)->schema([
                TextInput::make('title')->required()->maxLength(120)->helperText('The headline below the video.'),
                TextInput::make('client')->label('Client / project')->maxLength(120),
                TextInput::make('type')->label('Video category')->required()->maxLength(60)
                    ->datalist(['Brand film', 'Social reel', 'Detail cut', 'Short-form edit', 'Drone sequence', 'Product video', 'Interview']),
                TextInput::make('sort_order')->label('Display order')->numeric()->minValue(0)->maxValue(100000)->default(60)->required()
                    ->helperText('Lower numbers appear first. You can also drag rows to reorder the list.'),
                Textarea::make('note')->label('Short description')->required()->maxLength(500)->rows(3)->columnSpanFull(),
                Textarea::make('transcript')->label('Transcript / visual description (optional)')->maxLength(15000)->rows(5)->columnSpanFull()
                    ->helperText('Include spoken words and important visual information. Visitors can expand this text beneath the video. Add captions to the exported video when it contains speech.'),
            ])->columnSpanFull(),
            Section::make('3. Publish when ready')->description('Drafts and Trash are hidden from the content page. Media URLs are public: do not upload confidential material.')->schema([
                Toggle::make('is_published')->label('Show on the content page')->default(false),
                Toggle::make('is_featured')->label('Use in the hero')->default(false)
                    ->helperText('Only one featured video. It appears in the hero when published; otherwise the first published video is used.'),
            ])->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            ImageColumn::make('poster_url')->label('Cover')->imageHeight(72)->imageWidth(48),
            TextColumn::make('title')->searchable()->sortable()->wrap()->description(fn (ContentVideo $record) => $record->client),
            TextColumn::make('type')->label('Category')->searchable(),
            TextColumn::make('duration')->label('Length'),
            IconColumn::make('is_published')->label('Published')->boolean(),
            IconColumn::make('is_featured')->label('Hero')->boolean(),
            TextColumn::make('sort_order')->label('Order')->sortable(),
            TextColumn::make('updated_at')->since()->sortable()->toggleable(isToggledHiddenByDefault: true),
        ])->defaultSort('sort_order')->reorderable('sort_order')->paginated([10, 25, 50])
            ->filters([TernaryFilter::make('is_published')->label('Published'), TrashedFilter::make()])
            ->recordActions([
                EditAction::make(), DeleteAction::make()->label('Move to Trash'), RestoreAction::make(),
                ForceDeleteAction::make()->modalDescription('Permanently remove this record and its uploaded video and cover. This cannot be undone. Original bundled videos are preserved.'),
            ])->emptyStateHeading('Your video library starts here')->emptyStateDescription('Upload an MP4, add its details, preview it, and publish when ready.');
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->withoutGlobalScopes([SoftDeletingScope::class]);
    }

    public static function getPages(): array
    {
        return ['index' => ListContentVideos::route('/'), 'create' => CreateContentVideo::route('/create'), 'edit' => EditContentVideo::route('/{record}/edit')];
    }
}
