<?php

namespace App\Filament\Resources\Rooms\RelationManagers;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Actions\CreateAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Forms;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class RoomTemperaturesRelationManager extends RelationManager
{
    protected static string $relationship = 'roomTemperatures';
    protected static ?string $recordTitleAttribute = 'id';
    public function isReadOnly(): bool 
    { 
        return false; 
    }
    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('temperature_start')
                    ->label('Temperature Start')
                    ->numeric()
                    ->required(),
                TextInput::make('temperature_end')
                    ->label('Temperature End')
                    ->numeric()
                    ->required(),
            ]);
    }
    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->headerActions([
                CreateAction::make(),
            ])
            ->columns([
                TextColumn::make('temperature_start')
                    ->label('Temperature Start')
                    ->formatStateUsing(fn ($state) => "{$state}°C"),
                TextColumn::make('temperature_end')
                    ->label('Temperature End')
                    ->formatStateUsing(fn ($state) => "{$state}°C"),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
} 