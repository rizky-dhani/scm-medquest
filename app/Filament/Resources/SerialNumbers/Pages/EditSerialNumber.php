<?php

namespace App\Filament\Resources\SerialNumbers\Pages;

use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\SerialNumbers\SerialNumberResource;

class EditSerialNumber extends EditRecord
{
    protected static string $resource = SerialNumberResource::class;
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
    
    protected function getSavedNotification(): Notification
    {
        return Notification::make()
            ->title('Serial Number successfully updated');
    }
}
