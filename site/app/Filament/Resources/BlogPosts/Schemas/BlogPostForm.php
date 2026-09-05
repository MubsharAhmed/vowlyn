<?php

declare(strict_types=1);

namespace App\Filament\Resources\BlogPosts\Schemas;

use App\Models\BlogPost;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

final class BlogPostForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Article')
                ->description('Write for people first. Use clear headings, practical examples, and original experience.')
                ->schema([
                    TextInput::make('title')
                        ->required()
                        ->maxLength(180)
                        ->live(onBlur: true)
                        ->afterStateUpdated(function (Get $get, Set $set, ?string $old, ?string $state): void {
                            if (blank($get('slug')) || $get('slug') === Str::slug((string) $old)) {
                                $set('slug', Str::slug((string) $state));
                            }
                        })
                        ->columnSpanFull(),
                    TextInput::make('slug')
                        ->prefix('/blog/')
                        ->required()
                        ->alphaDash()
                        ->maxLength(180)
                        ->unique(ignoreRecord: true)
                        ->helperText('Changing a published URL automatically creates a permanent redirect.')
                        ->columnSpanFull(),
                    Textarea::make('excerpt')
                        ->required()
                        ->rows(3)
                        ->maxLength(500)
                        ->helperText('A useful summary for article cards, feeds, and default search descriptions.')
                        ->columnSpanFull(),
                    RichEditor::make('content')
                        ->json()
                        ->required()
                        ->toolbarButtons([
                            ['bold', 'italic', 'underline', 'strike', 'link'],
                            ['h2', 'h3'],
                            ['blockquote', 'bulletList', 'orderedList', 'codeBlock', 'table'],
                            ['customBlocks'],
                            ['undo', 'redo'],
                        ])
                        ->helperText('Use the Blocks panel for images with required alt text and reusable project CTAs.')
                        ->columnSpanFull(),
                ]),

            Section::make('Publishing')
                ->schema([
                    Grid::make(2)->schema([
                        Select::make('blog_author_id')
                            ->relationship('author', 'name', modifyQueryUsing: fn ($query) => $query->where('is_active', true))
                            ->searchable()
                            ->preload()
                            ->required(),
                        Select::make('blog_category_id')
                            ->relationship('category', 'name', modifyQueryUsing: fn ($query) => $query->where('is_active', true))
                            ->searchable()
                            ->preload()
                            ->required(),
                        Select::make('status')
                            ->options(BlogPost::statuses())
                            ->default(BlogPost::STATUS_DRAFT)
                            ->required()
                            ->native(false)
                            ->live(),
                        DateTimePicker::make('published_at')
                            ->label('Publish date and time')
                            ->seconds(false)
                            ->required(fn (Get $get): bool => in_array($get('status'), [BlogPost::STATUS_PUBLISHED, BlogPost::STATUS_SCHEDULED], true))
                            ->helperText('Scheduled posts become public automatically when this time arrives.'),
                    ]),
                    TagsInput::make('tags')
                        ->suggestions(['AI', 'SaaS', 'Laravel', 'Web development', 'Mobile', 'Cloud', 'DevOps', 'Security', 'Strategy'])
                        ->helperText('Used for article metadata and internal organization; no thin public tag pages are created.'),
                    Toggle::make('is_featured')
                        ->label('Feature on the blog landing page')
                        ->default(false),
                ]),

            Section::make('Featured image')
                ->description('A strong original image improves article cards, social previews, Google Images, and Discover eligibility.')
                ->schema([
                    FileUpload::make('featured_image')
                        ->disk('public')
                        ->directory('blog/featured')
                        ->visibility('public')
                        ->image()
                        ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                        ->maxSize(6144)
                        ->imageEditor()
                        ->imageEditorAspectRatioOptions(['16:9'])
                        ->imageAspectRatio('16:9')
                        ->automaticallyOpenImageEditorForAspectRatio()
                        ->automaticallyResizeImagesMode('cover')
                        ->automaticallyResizeImagesToWidth('2400')
                        ->automaticallyResizeImagesToHeight('1350')
                        ->panelAspectRatio('16:9')
                        ->preventFilePathTampering()
                        ->required(fn (Get $get): bool => in_array($get('status'), [BlogPost::STATUS_PUBLISHED, BlogPost::STATUS_SCHEDULED], true))
                        ->helperText('Use a 16:9 JPG, PNG, or WebP. Best size: 2400×1350 px (minimum recommended: 1600×900 px). If the source ratio differs, the crop editor opens automatically.')
                        ->columnSpanFull(),
                    TextInput::make('featured_image_alt')
                        ->label('Image alternative text')
                        ->required(fn (Get $get): bool => filled($get('featured_image')))
                        ->maxLength(180)
                        ->helperText('Describe the image itself, not a list of keywords.')
                        ->columnSpanFull(),
                    Grid::make(2)->schema([
                        TextInput::make('featured_image_caption')->maxLength(240),
                        TextInput::make('featured_image_credit')->label('Source / credit')->maxLength(180),
                    ]),
                ]),

            Section::make('Search appearance')
                ->description('Defaults are generated automatically. Override only when a better search result is needed.')
                ->schema([
                    TextInput::make('seo_title')
                        ->label('SEO title')
                        ->maxLength(70)
                        ->helperText('Aim for a descriptive, concise title. Vowlyn is appended by default.'),
                    Textarea::make('seo_description')
                        ->label('Meta description')
                        ->rows(3)
                        ->maxLength(320)
                        ->helperText('Write a unique, accurate pitch for the article; Google may choose a different snippet.'),
                    TextInput::make('canonical_url')
                        ->url()
                        ->maxLength(2048)
                        ->helperText('Leave empty unless this article was originally published at another preferred URL.'),
                    Toggle::make('is_indexable')
                        ->label('Allow search engines to index this article')
                        ->default(true)
                        ->helperText('Turn off only for intentionally private or duplicate content.'),
                ])
                ->collapsed(),

            Section::make('Social sharing')
                ->description('Optional overrides for LinkedIn, X, Slack, and other link previews.')
                ->schema([
                    TextInput::make('social_title')->maxLength(100),
                    Textarea::make('social_description')->rows(3)->maxLength(320),
                    FileUpload::make('social_image')
                        ->disk('public')
                        ->directory('blog/social')
                        ->visibility('public')
                        ->image()
                        ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                        ->maxSize(6144)
                        ->imageEditor()
                        ->imageEditorAspectRatioOptions(['1200:630'])
                        ->imageAspectRatio('1200:630')
                        ->automaticallyOpenImageEditorForAspectRatio()
                        ->automaticallyResizeImagesMode('cover')
                        ->automaticallyResizeImagesToWidth('1200')
                        ->automaticallyResizeImagesToHeight('630')
                        ->panelAspectRatio('1200:630')
                        ->preventFilePathTampering()
                        ->helperText('Optional 1200×630 px override. If the source ratio differs, the crop editor opens automatically. The featured image is used by default.'),
                ])
                ->collapsed(),
        ])->columns(1);
    }
}
