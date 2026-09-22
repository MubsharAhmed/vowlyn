<?php

declare(strict_types=1);

namespace App\Filament\Resources\ContentWorks;

use App\Filament\Resources\ContentWorks\Pages\CreateContentWork;
use App\Filament\Resources\ContentWorks\Pages\EditContentWork;
use App\Filament\Resources\ContentWorks\Pages\ListContentWorks;
use App\Models\ContentWork;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
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
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use UnitEnum;

final class ContentWorkResource extends Resource
{
    protected static ?string $model = ContentWork::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedPhoto;

    protected static string|UnitEnum|null $navigationGroup = 'Website content';

    protected static ?string $navigationLabel = 'Creative work';

    protected static ?string $modelLabel = 'creative work';

    protected static ?string $recordTitleAttribute = 'title';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('1. Add the image')->description('JPG, PNG or WebP · 8 MB maximum · at least 480 × 320 px. The server removes metadata and creates lightweight WebP versions automatically.')->schema([
                View::make('filament.content-works.preview')->visible(fn (?ContentWork $record) => $record !== null),
                FileUpload::make('image_upload')->label('Work image')->image()->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                    ->maxSize(8192)->maxFiles(1)->storeFiles(false)->preventFilePathTampering()
                    ->required(fn (string $operation, ?ContentWork $record) => $operation === 'create' && ! $record?->image_path)
                    ->helperText('On edit, leave this empty to keep the current image.'),
                Checkbox::make('rights_confirmed')->label('I have permission to publish this image and the people, artwork, and brands shown in it.')
                    ->accepted(fn (string $operation, Get $get) => $operation === 'create' || filled($get('image_upload'))),
            ])->columnSpanFull(),
            Section::make('2. Describe the work')->columns(2)->schema([
                Select::make('discipline')->options(['photography' => 'Photography', 'design' => 'Design'])->required()->native(false),
                TextInput::make('title')->required()->maxLength(120),
                TextInput::make('client')->label('Client / project')->maxLength(120),
                TextInput::make('link_url')->label('Project link (optional)')->url()->maxLength(500)->placeholder('https://example.com'),
                Textarea::make('description')->required()->maxLength(500)->rows(3)->columnSpanFull(),
                Textarea::make('image_alt')->label('Image description for accessibility')->required()->maxLength(180)->rows(2)->columnSpanFull()
                    ->helperText('Describe what is visible, not what you hope visitors feel. Example: “Gold necklace on a cream display plinth.”'),
                TextInput::make('sort_order')->label('Display order')->numeric()->minValue(0)->maxValue(100000)->default(60)->required(),
            ])->columnSpanFull(),
            Section::make('3. Publish when ready')->description('Drafts and Trash stay hidden from the public content page.')->schema([
                Toggle::make('is_published')->label('Show on the content page')->default(false),
            ])->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            ImageColumn::make('thumbnail_url')->label('Preview')->imageHeight(64)->imageWidth(88),
            TextColumn::make('title')->searchable()->sortable()->wrap()->description(fn (ContentWork $record) => $record->client),
            TextColumn::make('discipline')->badge()->formatStateUsing(fn (string $state) => ucfirst($state)),
            IconColumn::make('is_published')->label('Published')->boolean(),
            TextColumn::make('sort_order')->label('Order')->sortable(),
            TextColumn::make('updated_at')->since()->sortable()->toggleable(isToggledHiddenByDefault: true),
        ])->defaultSort('sort_order')->reorderable('sort_order')->paginated([10, 25, 50])
            ->filters([
                SelectFilter::make('discipline')->options(['photography' => 'Photography', 'design' => 'Design']),
                TernaryFilter::make('is_published')->label('Published'), TrashedFilter::make(),
            ])->recordActions([
                EditAction::make(), DeleteAction::make()->label('Move to Trash'), RestoreAction::make(),
                ForceDeleteAction::make()->modalDescription('Permanently remove this record and its uploaded optimized images. This cannot be undone. Bundled production stills are preserved.'),
            ])->emptyStateHeading('Build your creative-work library')->emptyStateDescription('Upload photography or design work, add accessible details, and publish when ready.');
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->withoutGlobalScopes([SoftDeletingScope::class]);
    }

    public static function getPages(): array
    {
        return ['index' => ListContentWorks::route('/'), 'create' => CreateContentWork::route('/create'), 'edit' => EditContentWork::route('/{record}/edit')];
    }
}
