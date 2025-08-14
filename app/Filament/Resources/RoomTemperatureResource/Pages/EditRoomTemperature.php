<?php

namespace App\Filament\Resources\RoomTemperatureResource\Pages;

use App\Filament\Resources\RoomTemperatureResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRoomTemperature extends EditRecord
{
    protected static string $resource = RoomTemperatureResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
