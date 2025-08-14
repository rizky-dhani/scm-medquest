<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\RoomTemperature;
use Filament\Resources\Resource;
use Filament\Forms\Components\Section;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\RoomTemperatureResource\Pages;
use App\Filament\Resources\RoomTemperatureResource\RelationManagers;

class RoomTemperatureResource extends Resource
{
    protected static ?string $model = RoomTemperature::class;
    protected static ?string $navigationGroup = 'Location Management';
    protected static ?int $navigationSort = 3;
    public static function canViewAny(): bool
    {
        return auth()->user()->hasRole(['Super Admin', 'Admin']);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Room Temperature')
                    ->columns(3)
                    ->schema([
                        Forms\Components\Select::make('room_id')
                            ->relationship('room', 'room_name')
                            ->label('Room')
                            ->required(),
                        Forms\Components\TextInput::make('temperature_start')
                            ->label('Temperature Start (°C)')
                            ->numeric()
                            ->required(),
                        Forms\Components\TextInput::make('temperature_end')
                            ->label('Temperature End (°C)')
                            ->numeric()
                            ->required(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                
                TextColumn::make('room.room_name')
                    ->label('Room')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('temperature_start')
                    ->label('Temperature Range')
                    ->sortable()
                    ->searchable()
                    ->getStateUsing(function($record) {
                        return $record->temperature_start . '°C to ' . $record->temperature_end . '°C';
                    }),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRoomTemperatures::route('/'),
            'create' => Pages\CreateRoomTemperature::route('/create'),
            'edit' => Pages\EditRoomTemperature::route('/{record}/edit'),
        ];
    }
}
