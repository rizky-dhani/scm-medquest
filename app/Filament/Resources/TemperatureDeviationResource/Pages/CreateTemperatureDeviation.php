<?php

namespace App\Filament\Resources\TemperatureDeviationResource\Pages;

use Carbon\Carbon;
use App\Models\Location;
use Illuminate\Support\Str;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\TemperatureDeviationResource;

class CreateTemperatureDeviation extends CreateRecord
{
    protected static string $resource = TemperatureDeviationResource::class;
    public function mount(): void
    {
        parent::mount();

        $locationId = request()->get('location_id');
        $roomId = request()->get('room_id');
        $roomTemperatureId = request()->get('room_temperature_id');
        $serialNumberId = request()->get('serial_number_id');
        $time = request()->get('time');
        if ($locationId && $roomId && $roomTemperatureId && $serialNumberId) {
            $this->form->fill([
                'temperature_humidity_id' => request()->get('temp_id'),
                'location_id' => $locationId,
                'room_id' => $roomId,
                'room_temperature_id' => $roomTemperatureId,
                'serial_number_id' => $serialNumberId,
                'date' => Carbon::now(),
                'time' => $time ? Carbon::createFromFormat('H:i', $time)->format('H:i') : null,
                'temperature_deviation' => request()->get('temperature_deviation'),
            ]);
        }
    }
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['temperatureDeviationId'] = Str::orderedUuid();
        $data['date'] = Carbon::now();
        $data['temperature_deviation'] = request()->get('temperature_deviation') ?? $data['temperature_deviation'];
        $data['pic'] = auth()->user()->hasRole('Security') 
            ? auth()->user()->name 
            : auth()->user()->initial . ' ' . strtoupper(now('Asia/Jakarta')->format('d M Y'));
        return $data;
    }
    protected function getRedirectUrl(): string
    {
        // Default fallback
        return $this->getResource()::getUrl('index');
    }
    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Temperature Deviation successfully created');
    }
}
