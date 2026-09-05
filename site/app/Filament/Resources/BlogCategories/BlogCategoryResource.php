<?php

declare(strict_types=1);

namespace App\Filament\Resources\BlogCategories;

use App\Filament\Resources\BlogCategories\Pages\CreateBlogCategory;
use App\Filament\Resources\BlogCategories\Pages\EditBlogCategory;
use App\Filament\Resources\BlogCategories\Pages\ListBlogCategories;
use App\Models\BlogCategory;
use App\Support\ServiceCatalog;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use UnitEnum;

final class BlogCategoryResource extends Resource
{
    protected static ?string $model = BlogCategory::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedFolder;

    protected static string|UnitEnum|null $navigationGroup = 'Blog';

    protected static ?int $navigationSort = 2;

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $navigationLabel = 'Categories';

    protected static ?string $modelLabel = 'blog category';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Category')->schema([
                Grid::make(2)->schema([
                    TextInput::make('name')
                        ->required()->maxLength(120)->live(onBlur: true)
                        ->afterStateUpdated(function (Get $get, Set $set, ?string $old, ?string $state): void {
                            if (blank($get('slug')) || $get('slug') === Str::slug((string) $old)) {
                                $set('slug', Str::slug((string) $state));
                            }
                        }),
                    TextInput::make('slug')->required()->alphaDash()->maxLength(120)->unique(ignoreRecord: true),
                ]),
                Textarea::make('description')
                    ->required()->rows(4)->maxLength(1000)
                    ->helperText('Explain the useful theme connecting posts in this category.'),
                Grid::make(2)->schema([
                    Select::make('related_service_slug')
                        ->label('Related Vowlyn service')
                        ->options(ServiceCatalog::options())
                        ->searchable()->native(false)
                        ->helperText('Article pages in this category will recommend this service.'),
                    Toggle::make('is_active')->default(true),
                ]),
            ]),
            Section::make('Search appearance')->schema([
                TextInput::make('seo_title')->maxLength(70),
                Textarea::make('seo_description')->rows(3)->maxLength(320),
            ])->collapsed(),
        ])->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('name')->searchable()->sortable()->weight('semibold'),
            TextColumn::make('slug')->searchable()->copyable(),
            TextColumn::make('posts_count')->counts('posts')->label('Posts')->sortable(),
            TextColumn::make('related_service_slug')
                ->label('Related service')
                ->formatStateUsing(fn (?string $state): string => ServiceCatalog::options()[$state] ?? '—')
                ->toggleable(),
            IconColumn::make('is_active')->boolean(),
        ])->recordActions([
            EditAction::make(),
            DeleteAction::make(),
        ])->toolbarActions([
            DeleteBulkAction::make(),
        ])->defaultSort('name');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListBlogCategories::route('/'),
            'create' => CreateBlogCategory::route('/create'),
            'edit' => EditBlogCategory::route('/{record}/edit'),
        ];
    }
}
