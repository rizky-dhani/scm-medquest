<?php

namespace App\Filament\Resources\SerialNumberResource\Pages;

use Filament\Actions;
use Illuminate\Support\Str;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\SerialNumberResource;

class CreateSerialNumber extends CreateRecord
{
    protected static string $resource = SerialNumberResource::class;
    protected function mutateFormDataBeforeCreate(array $data): array 
    {
        $data['serialNumberId'] = Str::orderedUuid();
        return $data;
    }
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
    protected function getCreatedNotification(): Notification
    {
        return Notification::make()
            ->title('Serial Number successfully created');
    }
}
