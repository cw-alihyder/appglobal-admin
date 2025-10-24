<?php

namespace App\Filament\Resources\BlogTags\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;

class BlogTagForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
            ]);
    }
}
