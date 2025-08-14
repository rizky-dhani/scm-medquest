<?php

namespace App\Filament\Resources\RoomResource\Pages;

use Filament\Actions;
use Illuminate\Support\Str;
use App\Filament\Resources\RoomResource;
use Filament\Resources\Pages\CreateRecord;

class CreateRoom extends CreateRecord
{
    protected static string $resource = RoomResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['roomId'] = Str::orderedUuid();
        return $data;
    }
}
