<?php

namespace App\Filament\Resources\BlogArticles\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Illuminate\Support\Str;
use Filament\Forms\Components\RichEditor;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;


class BlogArticleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1) // 🧱 Make the schema use full width
            ->components([
                Tabs::make('Blog Tabs')
                    ->tabs([
                        // 📰 Main Content Tab
                        Tab::make('Content')
                            ->icon('heroicon-o-document-text')
                            ->schema([
                                TextInput::make('title')
                                    ->label('Title')
                                    ->required()
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn($state, callable $set) =>
                                        $set('slug', Str::slug($state))
                                    )
                                    ->maxLength(255)
                                    ->columnSpanFull(),

                                TextInput::make('slug')
                                    ->label('Slug')
                                    ->disabled()
                                    ->dehydrated()
                                    ->required()
                                    ->unique(ignoreRecord: true)
                                    ->columnSpanFull(),

                                Textarea::make('excerpt')
                                    ->label('Excerpt')
                                    ->rows(3)
                                    ->required()
                                    ->maxLength(250)
                                    ->columnSpanFull(),

                                RichEditor::make('content')
                                    ->label('Content')
                                    ->toolbarButtons([
                                        'bold',
                                        'italic',
                                        'underline',
                                        'strike',
                                        'link',
                                        'orderedList',
                                        'bulletList',
                                        'blockquote',
                                        'codeBlock',
                                        'undo',
                                        'redo',
                                    ])
                                    ->required()
                                    ->columnSpanFull(),
                            ])
                            ->columns(1),

                        // 🖼️ Media Tab
                        Tab::make('Media')
                            ->icon('heroicon-o-photo')
                            ->schema([
                                SpatieMediaLibraryFileUpload::make('thumbnail')->label('Main Image')->required()->collection('thumbnail')->columnSpanFull()->disk('public'),

                                SpatieMediaLibraryFileUpload::make('image')->label('Main Image')->required()->collection('image')->columnSpanFull()->disk('public'),
                            ])
                            ->columns(1),

                        // 📅 Meta & Category Tab
                        Tab::make('Details')
                            ->icon('heroicon-o-calendar')
                            ->schema([
                                DatePicker::make('date')
                                    ->label('Publish Date')
                                    ->required()
                                    ->columnSpanFull(),

                                Select::make('category_id')
                                    ->label('Category')
                                    ->relationship('category', 'name')
                                    ->required()
                                    ->columnSpanFull(),

                                Select::make('tags')
                                    ->label('Tags')
                                    ->multiple()
                                    ->relationship('tags', 'name')
                                    ->columnSpanFull(),
                            ])
                            ->columns(1),

                        // 🔍 SEO Tab
                        Tab::make('SEO Meta')
                            ->icon('heroicon-o-globe-alt')
                            ->schema([
                                TextInput::make('metaTitle')
                                    ->label('Meta Title')
                                    ->placeholder('Enter SEO-friendly title')
                                    ->maxLength(255)
                                    ->columnSpanFull(),

                                Textarea::make('metaDescription')
                                    ->label('Meta Description')
                                    ->placeholder('Write a short description for search engines (max 160 chars)')
                                    ->rows(3)
                                    ->maxLength(500)
                                    ->columnSpanFull(),

                                TextInput::make('metaKeywords')
                                    ->label('Meta Keywords')
                                    ->placeholder('e.g. web design, laravel, filament')
                                    ->maxLength(255)
                                    ->columnSpanFull(),
                            ])
                            ->columns(1),
                    ])
                    ->persistTabInQueryString() // remembers last opened tab
                    ->columnSpanFull()
            ]);
    }
}
