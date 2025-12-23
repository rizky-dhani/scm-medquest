<?php

namespace App\Filament\Resources\SerialNumbers\Pages;

use Filament\Actions\CreateAction;
use App\Filament\Resources\SerialNumbers\SerialNumberResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSerialNumbers extends ListRecords
{
    protected static string $resource = SerialNumberResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
