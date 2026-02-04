<?php

namespace App\Filament\Resources\NotificationSettings\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class NotificationSettingsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('event_name')
                    ->label('Event')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('event_key')
                    ->label('Key')
                    ->searchable()
                    ->fontFamily('mono')
                    ->color('gray'),
                TextColumn::make('roles.name')
                    ->label('Recipient Roles')
                    ->badge()
                    ->color('primary')
                    ->searchable(),
                TextColumn::make('users.name')
                    ->label('Recipient Users')
                    ->badge()
                    ->color('success')
                    ->searchable(),
                TextColumn::make('updated_at')
                    ->label('Last Updated')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
            ]);
    }
}