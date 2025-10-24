<?php

namespace App\Filament\Resources\BlogArticles\Tables;

use App\Models\BlogArticle; // <- import your model if needed elsewhere
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Actions\ForceDeleteAction;
// removed unused "Action" import

class BlogArticlesTable
{
    public static function configure(Table $table): Table
    {
       return $table
        ->columns([
            ImageColumn::make('thumbnail')
                ->label('Thumbnail')
                ->circular()
                ->size(40),

            TextColumn::make('title')
                ->label('Title')
                ->searchable()
                ->sortable()
                ->limit(50),

            TextColumn::make('category.name')
                ->label('Category')
                ->sortable()
                ->badge()
                ->color('info'),

            TextColumn::make('date')
                ->label('Publish Date')
                ->date('M d, Y')
                ->sortable(),

            TextColumn::make('createdAt')
                ->label('Created')
                ->dateTime('M d, Y H:i'),

            TextColumn::make('updatedAt')
                ->label('Updated')
                ->dateTime('M d, Y H:i')
                ->toggleable(isToggledHiddenByDefault: true),
        ])

        ->filters([
            SelectFilter::make('categoryId')
                ->label('Category')
                ->relationship('category', 'name'),

            TrashedFilter::make(),
        ])

        ->recordActions([
            EditAction::make(),
        ])

        ->toolbarActions([
        ]);
    }
}
