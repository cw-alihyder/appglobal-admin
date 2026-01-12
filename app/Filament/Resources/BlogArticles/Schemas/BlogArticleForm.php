<?php

namespace App\Filament\Resources\BlogArticles\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;
use Kahusoftware\FilamentCkeditorField\CKEditor;

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
                                    ->maxLength(255)
                                    ->columnSpanFull(),

                                TextInput::make('slug')
                                    ->label('Slug')
                                    ->required()
                                    ->unique(ignoreRecord: true)
                                    ->columnSpanFull(),

                                Textarea::make('excerpt')
                                    ->label('Excerpt')
                                    ->rows(3)
                                    ->required()
                                    ->maxLength(250)
                                    ->columnSpanFull(),

                                CKEditor::make('content')->dehydrateStateUsing(function ($state) {
                                    if (empty($state)) {
                                        return $state;
                                    }

                                    return preg_replace_callback('/<a\s+(?:[^>]*?\s+)?href="([^"]*)"([^>]*)>/i', function ($matches) {
                                        $url = $matches[1];
                                        $attributes = $matches[2];

                                        // 1. Correctly extract the host from the link in the content
                                        $linkHost = parse_url($url, PHP_URL_HOST);

                                        // 2. Define your own domain and allowed domains
                                        $myHost = request()->getHost();
                                        $allowedHosts = ['appsglobal.co', $myHost];

                                        // 3. Logic: If it has a host, and that host is NOT in our allowed list
                                        if ($linkHost && ! in_array($linkHost, $allowedHosts)) {
                                            // Check if rel is already there to avoid doubling up
                                            if (! Str::contains($attributes, 'rel=')) {
                                                return "<a href=\"{$url}\" rel=\"nofollow\"{$attributes}>";
                                            }
                                        }

                                        return $matches[0];
                                    }, $state);
                                }),
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
                                                                TextInput::make('meta_title')
                                                                    ->label('Meta Title')
                                                                    ->placeholder('Enter SEO-friendly title')
                                                                    ->maxLength(255)
                                                                    ->columnSpanFull(),

                                                                Textarea::make('meta_description')
                                                                    ->label('Meta Description')
                                                                    ->placeholder('Write a short description for search engines (max 160 chars)')
                                                                    ->rows(3)
                                                                    ->maxLength(500)
                                                                    ->columnSpanFull(),

                                                                TextInput::make('meta_keywords')
                                                                    ->label('Meta Keywords')
                                                                    ->placeholder('e.g. web design, laravel, filament')
                                                                    ->maxLength(255)
                                                                    ->columnSpanFull(),
                                                            ])
                            ->columns(1),
                    ])
                    ->persistTabInQueryString() // remembers last opened tab
                    ->columnSpanFull(),
            ]);
    }
}
