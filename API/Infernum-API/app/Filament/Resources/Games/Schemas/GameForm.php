<?php

namespace App\Filament\Resources\Games\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Hidden;

class GameForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Game Name')
                    ->required()
                    ->maxLength(255),
                TextInput::make('short_description')
                    ->label('Short Description')
                    ->required()
                    ->maxLength(300),
                Textarea::make('long_description')
                    ->label('Full Description')
                    ->required(),
                TextInput::make('price')
                    ->label('Price (€)')
                    ->numeric()
                    ->required()
                    ->minValue(0),
                // count_boughts defaults to 0 on create; not shown in form
                Hidden::make('count_boughts')
                    ->default(0),
                Select::make('discounts_id')
                    ->relationship('discounts', 'name')
                    ->label('Discount')
                    ->nullable(),

                Repeater::make('images')
                    ->relationship('images')
                    ->schema([
                        TextInput::make('url')
                            ->label('Image URL')
                            ->required()
                            ->maxLength(500),
                        Select::make('type')
                            ->label('Image Type')
                            ->options([
                                'portrait' => 'Portrait',
                                'gallery' => 'Gallery',
                                'description' => 'Description',
                            ])
                            ->required(),
                    ])
                    ->label('Game Images')
                    ->defaultItems(1),

                Repeater::make('requirements')
                    ->relationship('requirements')
                    ->schema([
                        Select::make('type')
                            ->label('Requirement Type')
                            ->options([
                                'minimum' => 'Minimum',
                                'recommended' => 'Recommended',
                            ])
                            ->required(),
                        TextInput::make('os')
                            ->label('Operating System')
                            ->required()
                            ->maxLength(100),
                        TextInput::make('cpu')
                            ->label('CPU')
                            ->required()
                            ->maxLength(150),
                        TextInput::make('ram')
                            ->label('RAM')
                            ->required()
                            ->maxLength(20),
                        TextInput::make('gpu')
                            ->label('GPU')
                            ->required()
                            ->maxLength(150),
                        TextInput::make('storage')
                            ->label('Storage')
                            ->required()
                            ->maxLength(20),
                    ])
                    ->label('System Requirements')
                    ->grid(2),
            ]);
    }
}
