<?php

namespace App\Filament\Resources\Users\Tables;

use Filament\Actions\EditAction;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->sortable(),
                TextColumn::make('nickname')->searchable()->sortable()->label('Username'),
                TextColumn::make('email')->searchable()->sortable()->label('Email'),
                TextColumn::make('role')->badge()->color(fn (string $state): string => match ($state) {
                    'admin' => 'danger',
                    'client' => 'success',
                    default => 'gray',
                })->sortable()->label('Role'),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                // No bulk delete: admin cannot delete users
            ])
            ->paginated([10, 25, 50])
            ->defaultPaginationPageOption(10)
            ->searchable();
    }
}
