<?php

namespace App\Filament\Resources\BlogArticles;

use App\Filament\Resources\BlogArticles\Pages\CreateBlogArticle;
use App\Filament\Resources\BlogArticles\Pages\EditBlogArticle;
use App\Filament\Resources\BlogArticles\Pages\ListBlogArticles;
use App\Filament\Resources\BlogArticles\Schemas\BlogArticleForm;
use App\Filament\Resources\BlogArticles\Tables\BlogArticlesTable;
use App\Models\BlogArticle;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;


class BlogArticleResource extends Resource
{
    protected static ?string $model = BlogArticle::class;

    protected static string | UnitEnum | null $navigationGroup = 'Blog';
    protected static ?string $navigationLabel = 'Articles';
    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-document-text';

    protected static ?int $navigationSort = 1;

    // protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return BlogArticleForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return BlogArticlesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListBlogArticles::route('/'),
            'create' => CreateBlogArticle::route('/create'),
            'edit' => EditBlogArticle::route('/{record}/edit'),
        ];
    }
}
