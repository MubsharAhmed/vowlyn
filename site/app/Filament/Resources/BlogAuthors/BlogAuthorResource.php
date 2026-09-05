<?php

declare(strict_types=1);

namespace App\Filament\Resources\BlogAuthors;

use App\Filament\Resources\BlogAuthors\Pages\CreateBlogAuthor;
use App\Filament\Resources\BlogAuthors\Pages\EditBlogAuthor;
use App\Filament\Resources\BlogAuthors\Pages\ListBlogAuthors;
use App\Models\BlogAuthor;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\KeyValue;
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
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use UnitEnum;

final class BlogAuthorResource extends Resource
{
    protected static ?string $model = BlogAuthor::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserCircle;

    protected static string|UnitEnum|null $navigationGroup = 'Blog';

    protected static ?int $navigationSort = 3;

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $navigationLabel = 'Authors';

    protected static ?string $modelLabel = 'blog author';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Public author profile')->schema([
                Grid::make(2)->schema([
                    TextInput::make('name')
                        ->required()->maxLength(120)->live(onBlur: true)
                        ->afterStateUpdated(function (Get $get, Set $set, ?string $old, ?string $state): void {
                            if (blank($get('slug')) || $get('slug') === Str::slug((string) $old)) {
                                $set('slug', Str::slug((string) $state));
                            }
                        }),
                    TextInput::make('slug')->required()->alphaDash()->maxLength(120)->unique(ignoreRecord: true),
                    TextInput::make('job_title')->maxLength(120),
                    Toggle::make('is_active')->default(true),
                ]),
                Textarea::make('bio')->rows(5)->maxLength(1000)->columnSpanFull(),
                FileUpload::make('avatar')
                    ->disk('public')->directory('blog/authors')->visibility('public')
                    ->image()->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                    ->maxSize(3072)->imageEditor()->circleCropper()
                    ->imageResizeMode('cover')->imageResizeTargetWidth('800')->imageResizeTargetHeight('800')
                    ->preventFilePathTampering(),
                KeyValue::make('same_as')
                    ->label('Public profile links')
                    ->keyLabel('Network')
                    ->valueLabel('Full URL')
                    ->helperText('Examples: LinkedIn, GitHub, X. These links support author identity in structured data.'),
            ]),
        ])->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            ImageColumn::make('avatar')->disk('public')->circular(),
            TextColumn::make('name')->searchable()->sortable()->weight('semibold'),
            TextColumn::make('job_title')->searchable(),
            TextColumn::make('posts_count')->counts('posts')->label('Posts')->sortable(),
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
            'index' => ListBlogAuthors::route('/'),
            'create' => CreateBlogAuthor::route('/create'),
            'edit' => EditBlogAuthor::route('/{record}/edit'),
        ];
    }
}
