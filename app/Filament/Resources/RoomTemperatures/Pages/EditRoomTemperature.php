<?php

namespace App\Filament\Resources\RoomTemperatures\Pages;

use Filament\Actions\DeleteAction;
use App\Filament\Resources\RoomTemperatures\RoomTemperatureResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRoomTemperature extends EditRecord
{
    protected static string $resource = RoomTemperatureResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
