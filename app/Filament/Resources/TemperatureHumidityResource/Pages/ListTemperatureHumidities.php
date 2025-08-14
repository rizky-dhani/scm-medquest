<?php

namespace App\Filament\Resources\TemperatureHumidityResource\Pages;

use Filament\Actions;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\TemperatureHumidityResource;

class ListTemperatureHumidities extends ListRecords
{
    protected static string $resource = TemperatureHumidityResource::class;
    protected static ?string $title = 'All Temperature & Humidity';
    public function getBreadcrumb(): string
    {
        return 'All'; // or any label you want
    }
    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
            ->label('New Temperature & Humidity')
            ->color('success')
            ->visible(fn() => auth()->user()->hasRole(['Supply Chain Officer', 'Security'])),
        ];
    }
}
