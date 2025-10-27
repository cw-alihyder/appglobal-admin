<?php

namespace App\Filament\Resources\BlogCategories\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Illuminate\Support\Str;


class BlogCategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
    ->components([
        TextInput::make('name')
            ->label('Name')
            ->required()
            ->live(onBlur: true) // ensures slug updates after editing name
            ->afterStateUpdated(function ($state, callable $set) {
                if (filled($state)) {
                    $set('slug', Str::slug($state));
                }
            }),

        TextInput::make('slug')
            ->label('Slug')
            ->required()
            ->unique(ignoreRecord: true)
            ->maxLength(255)
            ->disabled(true) // disable editing only when updating
            ->dehydrated(), // ensures it's included in form submission
    ]);
    }
}
