<?php

namespace App\Filament\Resources\Games\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;

class GamesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->sortable()->label('ID'),
                TextColumn::make('name')->searchable()->sortable()->label('Game Name'),
                TextColumn::make('short_description')->limit(50)->label('Description')
                    ->tooltip(fn (TextColumn $column): ?string => $column->getState()),
                TextColumn::make('price')->money('EUR')->sortable()->label('Price'),
                TextColumn::make('count_boughts')->numeric(decimalPlaces: 0)->sortable()->label('Sales'),
                TextColumn::make('discounts.name')->label('Discount')->sortable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->paginated([10, 25, 50])
            ->defaultPaginationPageOption(10)
            ->searchable();
    }
}
