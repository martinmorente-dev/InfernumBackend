<?php

namespace App\Filament\Resources\Genres\Schemas;

use Filament\Schemas\Schema;

use Filament\Forms\Components\TextInput;

class GenreForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('type')
                    ->label('Genre Name')
                    ->required()
                    ->maxLength(255),
            ]);
    }
}
