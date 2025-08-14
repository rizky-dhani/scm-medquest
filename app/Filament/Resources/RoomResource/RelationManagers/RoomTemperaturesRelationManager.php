<?php

namespace App\Filament\Resources\RoomResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
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
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('temperature_start')
                    ->label('Temperature Start')
                    ->numeric()
                    ->required(),
                Forms\Components\TextInput::make('temperature_end')
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
                Tables\Actions\CreateAction::make(),
            ])
            ->columns([
                Tables\Columns\TextColumn::make('temperature_start')
                    ->label('Temperature Start')
                    ->formatStateUsing(fn ($state) => "{$state}°C"),
                Tables\Columns\TextColumn::make('temperature_end')
                    ->label('Temperature End')
                    ->formatStateUsing(fn ($state) => "{$state}°C"),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
} 