<?php

namespace App\Filament\Resources\LocationResource\Pages;

use Filament\Actions;
use Illuminate\Support\Str;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\LocationResource;

class CreateLocation extends CreateRecord
{
    protected static string $resource = LocationResource::class;
    protected function getRedirectUrl(): string
    {
        return LocationResource::getUrl('index');
    }
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['locationId'] = Str::orderedUuid();
        return $data;
    }
}
