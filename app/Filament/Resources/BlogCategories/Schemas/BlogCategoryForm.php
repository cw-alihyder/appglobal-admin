<?php

namespace App\Filament\Resources\BlogCategories\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;


class BlogCategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')


            ]);
    }
}
