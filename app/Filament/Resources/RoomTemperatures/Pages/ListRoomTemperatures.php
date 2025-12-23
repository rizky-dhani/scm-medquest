<?php

namespace App\Filament\Resources\RoomTemperatures\Pages;

use Filament\Actions\CreateAction;
use App\Filament\Resources\RoomTemperatures\RoomTemperatureResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRoomTemperatures extends ListRecords
{
    protected static string $resource = RoomTemperatureResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
