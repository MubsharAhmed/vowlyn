<?php

declare(strict_types=1);

namespace App\Filament\Resources\BlogPosts\Tables;

use App\Models\BlogPost;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

final class BlogPostsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('updated_at', 'desc')
            ->columns([
                ImageColumn::make('featured_image')
                    ->label('')
                    ->disk('public')
                    ->width(72)
                    ->height(44)
                    ->extraImgAttributes(['class' => 'rounded-lg object-cover']),
                TextColumn::make('title')
                    ->searchable()
                    ->sortable()
                    ->weight('semibold')
                    ->description(fn (BlogPost $record): string => '/blog/'.$record->slug)
                    ->wrap(),
                TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => BlogPost::statuses()[$state] ?? $state)
                    ->color(fn (string $state): string => match ($state) {
                        BlogPost::STATUS_PUBLISHED => 'success',
                        BlogPost::STATUS_SCHEDULED => 'warning',
                        BlogPost::STATUS_ARCHIVED => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('category.name')->label('Category')->badge()->color('primary')->sortable(),
                TextColumn::make('author.name')->label('Author')->searchable()->toggleable(),
                TextColumn::make('published_at')->label('Publish date')->dateTime('M j, Y · g:i A')->sortable()->placeholder('Not set'),
                IconColumn::make('is_featured')->label('Featured')->boolean()->toggleable(),
                TextColumn::make('updated_at')->label('Updated')->since()->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')->options(BlogPost::statuses())->multiple(),
                SelectFilter::make('blog_category_id')->label('Category')->relationship('category', 'name')->preload(),
                SelectFilter::make('blog_author_id')->label('Author')->relationship('author', 'name')->preload(),
                TernaryFilter::make('is_featured')->label('Featured'),
                TrashedFilter::make(),
            ])
            ->recordActions([
                ViewAction::make('viewLive')
                    ->label('View live')
                    ->url(fn (BlogPost $record): string => route('blog.show', $record->slug))
                    ->openUrlInNewTab()
                    ->visible(fn (BlogPost $record): bool => $record->isPubliclyVisible()),
                EditAction::make(),
                DeleteAction::make(),
                RestoreAction::make(),
                ForceDeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading('No blog posts yet')
            ->emptyStateDescription('Create the first useful, original article for the Vowlyn audience.')
            ->emptyStateIcon('heroicon-o-newspaper');
    }
}
