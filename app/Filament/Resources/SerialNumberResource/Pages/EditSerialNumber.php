<?php

namespace App\Filament\Resources\SerialNumberResource\Pages;

use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\SerialNumberResource;

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
