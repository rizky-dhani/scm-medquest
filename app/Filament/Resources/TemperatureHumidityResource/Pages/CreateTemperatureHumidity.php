<?php

namespace App\Filament\Resources\TemperatureHumidityResource\Pages;

use App\Models\RoomTemperature;
use Carbon\Carbon;
use App\Models\Location;
use Illuminate\Support\Str;
use Filament\Notifications\Notification;
use Filament\Notifications\Actions\Action;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\TemperatureHumidityResource;
use App\Filament\Resources\TemperatureDeviationResource;

class CreateTemperatureHumidity extends CreateRecord
{
    protected static string $resource = TemperatureHumidityResource::class;
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['temperatureId'] = Str::orderedUuid();
        $data['serial_number_id'] = $data['serial_number_id'] ?? null; // Ensure serial number is set
        $data['location_id'] = $data['location_id'] ?? null; // Ensure location is set
        $data['room_id'] = $data['room_id'] ?? null; // Ensure room is set
        $data['room_temperature_id'] = $data['room_temperature_id'] ?? null; // Ensure room temperature is set
        // Ensure temperature fields are set
        $tempFields = ['temp_0800', 'temp_1100', 'temp_1400', 'temp_1700', 'temp_2000', 'temp_2300', 'temp_0200', 'temp_0500'];
        foreach ($tempFields as $temp) {
            $data[$temp] = $data[$temp] ?? null;
        }
        // Store observed temperature range
        $roomTemp = RoomTemperature::find($data['room_temperature_id']);
        $minTemp = $roomTemp->temperature_start;
        $maxTemp = $roomTemp->temperature_end;

        $deviationDetected = false;

        // Validate temperature fields
        foreach ($tempFields as $field) {
            if (!is_null($data[$field]) && ($data[$field] < $minTemp || $data[$field] > $maxTemp)) {
                $deviationDetected = true;
                break; // Stop checking once a deviation is found
            }
        }

        if ($deviationDetected) {
            session()->put('deviation_triggered', true);
        }


        // ✅ Automatically insert user ID into the right PIC field
        $now = Carbon::now('Asia/Jakarta');
        $signature = auth()->user()->hasRole('Security')
            ? auth()->user()->name
            : auth()->user()->initial . ' ' . strtoupper(now('Asia/Jakarta')->format('d M Y'));

        $timeWindows = [
            'pic_0200' => ['start' => '02:00:00', 'end' => '02:30:59'],
            'pic_0500' => ['start' => '05:00:00', 'end' => '05:30:59'],
            'pic_0800' => ['start' => '08:00:00', 'end' => '08:30:59'],
            'pic_1100' => ['start' => '11:00:00', 'end' => '11:30:59'],
            'pic_1400' => ['start' => '14:00:00', 'end' => '14:30:59'],
            'pic_1700' => ['start' => '17:00:00', 'end' => '17:30:59'],
            'pic_2000' => ['start' => '20:00:00', 'end' => '20:30:59'],
            'pic_2300' => ['start' => '23:00:00', 'end' => '23:30:59'],
        ];

        foreach ($timeWindows as $picField => $window) {
            $start = Carbon::createFromTimeString($window['start'], 'Asia/Jakarta');
            $end = Carbon::createFromTimeString($window['end'], 'Asia/Jakarta');

            if ($now->between($start, $end)) {
                $data[$picField] = $signature;
                break; // Only apply to current window
            }
        }

        return $data;
    }
    protected function afterCreate()
    {
        // Retrieve the created Temperature Humidity record and set temperature and time that triggers deviation
        $temperatureHumidity = $this->record;
        // Retrieve the temperature range from the location
        $location = $temperatureHumidity->location;
        $room = $temperatureHumidity->room;
        $roomTemp = $temperatureHumidity->roomTemperature;
        $minTemp = $roomTemp->temperature_start;
        $maxTemp = $roomTemp->temperature_end;
        // Define temperature fields linked to their respective time fields
        $timeSlots = [
            'temp_0200' => 'time_0200',
            'temp_0500' => 'time_0500',
            'temp_0800' => 'time_0800',
            'temp_1100' => 'time_1100',
            'temp_1400' => 'time_1400',
            'temp_1700' => 'time_1700',
            'temp_2000' => 'time_2000',
            'temp_2300' => 'time_2300',
        ];

        // Check all temperature fields and store deviations
        foreach ($timeSlots as $tempField => $timeField) {
            $tempValue = $temperatureHumidity->$tempField; // Get temperature value
            $inputtedTime = $temperatureHumidity->$timeField ?? 'Unknown Time'; // Get user-inputted time
            
            if (!is_null($tempValue) && ($tempValue < $minTemp || $tempValue > $maxTemp)) {
                $deviationData[] = [
                    'temperature_id' => $temperatureHumidity->id,
                    'location_id' => $temperatureHumidity->location_id,
                    'room_id' => $temperatureHumidity->room_id,
                    'room_temperature_id' => $temperatureHumidity->room_temperature_id,
                    'serial_number_id' => $temperatureHumidity->serial_number_id,
                    'time' => $inputtedTime,
                    'temperature_deviation' => $tempValue,
                ];
            }
        }
        if (!empty($deviationData)) {
        session()->put('deviation_triggered', true);
        session()->put('deviation_data', $deviationData);
        }

        $recipient = auth()->user();
        Notification::make()
            ->success()
            ->title('New Temperature & Humidity for '.$room->room_name.' '. $location->location_name .' created')
            ->body('Please check the Temperature & Humidity page for more details.')
            ->actions([
                Action::make('View')
                    ->url(TemperatureHumidityResource::getUrl('view', ['record' => $temperatureHumidity]))
                    ->icon('heroicon-o-eye')
                    ->color('success')
                    ->markAsRead()
                    ->close()
            ])
            ->sendToDatabase($recipient);
    }
    protected function getRedirectUrl(): string
    {
        // Check if deviation was triggered
        if (session()->pull('deviation_triggered', false)) {
            // Default to false
            $deviations = session()->pull('deviation_data', []);
            return TemperatureDeviationResource::getUrl('create',[
                'temp_id' => $deviations[0]['temperature_id'],
                'location_id' => $deviations[0]['location_id'],
                'room_id' => $deviations[0]['room_id'],
                'room_temperature_id' => $deviations[0]['room_temperature_id'],
                'serial_number_id' => $deviations[0]['serial_number_id'],
                'time' => $deviations[0]['time'],
                'temperature_deviation' => $deviations[0]['temperature_deviation']
            ]); // Redirect to Deviation form
        }

        return $this->getResource()::getUrl('index'); // Default redirect after successful save
    }
    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Temperature & Humidity successfully created');
    }
}
