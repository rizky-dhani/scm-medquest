<?php

namespace App\Filament\Resources\RoomTemperatureResource\Pages;

use App\Filament\Resources\RoomTemperatureResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRoomTemperatures extends ListRecords
{
    protected static string $resource = RoomTemperatureResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
