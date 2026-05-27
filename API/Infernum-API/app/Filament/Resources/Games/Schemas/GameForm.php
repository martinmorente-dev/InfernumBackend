<?php

namespace App\Filament\Resources\Games\Schemas;

use Filament\Schemas\Schema;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;

class GameForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                TextInput::make('short_description')
                    ->required()
                    ->maxLength(300),
                Textarea::make('long_description')
                    ->required(),
                TextInput::make('price')
                    ->numeric()
                    ->required(),
                TextInput::make('count_boughts')
                    ->numeric()
                    ->default(0)
                    ->required(),
                Select::make('discounts_id')
                    ->relationship('discounts', 'name')
                    ->nullable(),
            ]);
    }
}
