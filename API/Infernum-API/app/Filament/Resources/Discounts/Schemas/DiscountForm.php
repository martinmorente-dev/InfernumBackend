<?php

namespace App\Filament\Resources\Discounts\Schemas;

use Filament\Schemas\Schema;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DateTimePicker;

class DiscountForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Discount Name')
                    ->required()
                    ->maxLength(255),
                TextInput::make('percentage')
                    ->label('Percentage (%)')
                    ->numeric()
                    ->required(),
                DateTimePicker::make('valid_at')
                    ->label('Valid From')
                    ->required(),
                DateTimePicker::make('expires_at')
                    ->label('Expires At')
                    ->required(),
            ]);
    }
}
