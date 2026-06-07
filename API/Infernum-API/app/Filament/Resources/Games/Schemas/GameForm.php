<?php

namespace App\Filament\Resources\Games\Schemas;

use Filament\Schemas\Schema;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Repeater;

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
                
                Repeater::make('images')
                    ->relationship('images')
                    ->schema([
                        TextInput::make('url')
                            ->required()
                            ->maxLength(500),
                        Select::make('type')
                            ->options([
                                'portrait' => 'Portrait',
                                'gallery' => 'Gallery',
                                'description' => 'Description',
                            ])
                            ->required(),
                    ])
                    ->label('Game Images')
                    ->collapsible()
                    ->defaultItems(1),

                Repeater::make('requirements')
                    ->relationship('requirements')
                    ->schema([
                        Select::make('type')
                            ->options([
                                'minimum' => 'Minimum',
                                'recommended' => 'Recommended',
                            ])
                            ->required(),
                        TextInput::make('os')
                            ->required()
                            ->maxLength(100),
                        TextInput::make('cpu')
                            ->required()
                            ->maxLength(150),
                        TextInput::make('ram')
                            ->required()
                            ->maxLength(20),
                        TextInput::make('gpu')
                            ->required()
                            ->maxLength(150),
                        TextInput::make('storage')
                            ->required()
                            ->maxLength(20),
                    ])
                    ->label('System Requirements')
                    ->collapsible()
                    ->grid(2),
            ]);
    }
}
