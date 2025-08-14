<?php

namespace App\Filament\Resources\SerialNumberResource\Pages;

use App\Filament\Resources\SerialNumberResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSerialNumbers extends ListRecords
{
    protected static string $resource = SerialNumberResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
