<?php

namespace App\Filament\Resources\Discounts\Tables;

use Filament\Actions\EditAction;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;

class DiscountsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->sortable()->label('ID'),
                TextColumn::make('name')->searchable()->sortable()->label('Discount Name'),
                TextColumn::make('percentage')->numeric()->suffix('%')->sortable()->label('Percentage'),
                TextColumn::make('valid_at')->dateTime()->sortable()->label('Valid From'),
                TextColumn::make('expires_at')->dateTime()->sortable()->label('Expires At'),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                // No bulk delete for discounts
            ])
            ->paginated([10, 25, 50])
            ->defaultPaginationPageOption(10)
            ->searchable();
    }
}
