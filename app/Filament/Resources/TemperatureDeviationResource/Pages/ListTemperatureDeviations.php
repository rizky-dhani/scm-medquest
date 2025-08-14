<?php

namespace App\Filament\Resources\TemperatureDeviationResource\Pages;

use Filament\Actions;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\TemperatureDeviationResource;

class ListTemperatureDeviations extends ListRecords
{
    protected static string $resource = TemperatureDeviationResource::class;
    protected static ?string $title = 'All Temperature Deviations';
    public function getBreadcrumb(): string
    {
        return 'All'; // or any label you want
    }
    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
            ->label('New Temperature Deviation')
            ->color('success')
            ->visible(fn() => auth()->user()->hasRole(['Supply Chain Officer', 'Security'])),
        ];
    }
}
