<?php

namespace App\Filament\Resources\Games\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\FileUpload;
use Filament\Schemas\Components\Section;

class GameForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                Section::make('Basic Information')
                    ->description('General game details shown in the store.')
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->label('Game Name')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),
                        TextInput::make('short_description')
                            ->label('Short Description')
                            ->required()
                            ->maxLength(300)
                            ->columnSpanFull(),
                        Textarea::make('long_description')
                            ->label('Full Description')
                            ->required()
                            ->rows(5)
                            ->columnSpanFull(),
                    ]),

                Section::make('Pricing & Discount')
                    ->columns(2)
                    ->schema([
                        TextInput::make('price')
                            ->label('Price (€)')
                            ->numeric()
                            ->required()
                            ->minValue(0)
                            ->prefix('€'),
                        Select::make('discounts_id')
                            ->relationship('discounts', 'name')
                            ->label('Active Discount')
                            ->nullable()
                            ->searchable()
                            ->preload(),
                        Hidden::make('count_boughts')
                            ->default(0),
                    ]),

                Section::make('Game Images')
                    ->description('Upload images for this game. Each image needs a type (portrait, gallery, description).')
                    ->schema([
                        Repeater::make('images')
                            ->relationship('images')
                            ->schema([
                                Select::make('type')
                                    ->label('Image Type')
                                    ->options([
                                        'portrait'    => 'Portrait (main cover)',
                                        'gallery'     => 'Gallery',
                                        'description' => 'Description',
                                    ])
                                    ->required()
                                    ->native(false),
                                FileUpload::make('url')
                                    ->label('Image')
                                    ->disk('public')
                                    ->directory('game_images')
                                    ->image()
                                    ->imageEditor()
                                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/gif', 'image/webp'])
                                    ->maxSize(5120)
                                    ->helperText('Max 5 MB · JPG, PNG, GIF, WEBP')
                                    ->required(),
                            ])
                            ->columns(2)
                            ->addActionLabel('Add Image')
                            ->defaultItems(1)
                            ->reorderable()
                            ->collapsible(),
                    ]),

                Section::make('System Requirements')
                    ->description('Define minimum and recommended specs. Add one entry per requirement tier.')
                    ->schema([
                        Repeater::make('requirements')
                            ->relationship('requirements')
                            ->schema([
                                Select::make('type')
                                    ->label('Tier')
                                    ->options([
                                        'minimum'     => 'Minimum',
                                        'recommended' => 'Recommended',
                                    ])
                                    ->required()
                                    ->native(false)
                                    ->columnSpanFull(),
                                TextInput::make('os')
                                    ->label('OS')
                                    ->required()
                                    ->maxLength(100)
                                    ->placeholder('Windows 10 64-bit'),
                                TextInput::make('cpu')
                                    ->label('CPU')
                                    ->required()
                                    ->maxLength(150)
                                    ->placeholder('Intel Core i5-8400'),
                                TextInput::make('ram')
                                    ->label('RAM')
                                    ->required()
                                    ->maxLength(20)
                                    ->placeholder('8 GB'),
                                TextInput::make('gpu')
                                    ->label('GPU')
                                    ->required()
                                    ->maxLength(150)
                                    ->placeholder('NVIDIA GTX 1060'),
                                TextInput::make('storage')
                                    ->label('Storage')
                                    ->required()
                                    ->maxLength(20)
                                    ->placeholder('50 GB SSD'),
                            ])
                            ->columns(2)
                            ->addActionLabel('Add Requirements Set')
                            ->defaultItems(1)
                            ->reorderable()
                            ->collapsible(),
                    ]),

            ]);
    }
}
