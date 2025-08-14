<?php

namespace App\Filament\Resources\TemperatureHumidityResource\Pages;

use Carbon\Carbon;
use Filament\Actions;
use Filament\Actions\Action;
use App\Models\RoomTemperature;
use App\Models\TemperatureHumidity;
use Illuminate\Support\Facades\Auth;
use App\Traits\HasLocationBasedAccess;
use Illuminate\Database\Eloquent\Model;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\TemperatureHumidityResource;
use App\Filament\Resources\TemperatureDeviationResource;
use Filament\Notifications\Actions\Action as NotificationAction;

class EditTemperatureHumidity extends EditRecord
{
    use HasLocationBasedAccess;
    
    protected static string $resource = TemperatureHumidityResource::class;

    public function mount(int | string $record): void
    {
        parent::mount($record);
        
        // Check if user can access this record's location
        if (!static::canAccessLocation($this->record->location_id)) {
            abort(403, 'You do not have permission to access this record.');
        }
    }
    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Ensure temperature fields are set
        $tempFields = ['temp_0200', 'temp_0500', 'temp_0800', 'temp_1100', 'temp_1400', 'temp_1700', 'temp_2000', 'temp_2300'];
        foreach ($tempFields as $temp) {
            $data[$temp] = $data[$temp] ?? null;
        }
        // 🔄 Get temperature range from the selected location
        $location = RoomTemperature::find($data['room_temperature_id']);
        $minTemp = $location->temperature_start;
        $maxTemp = $location->temperature_end;
        // ✅ Auto-fill signature into current time window's PIC field
        // $now = Carbon::now('Asia/Jakarta');      
        // $signature = auth()->user()->hasRole('Security') 
        //     ? auth()->user()->name 
        //     : auth()->user()->initial . ' ' . strtoupper(now('Asia/Jakarta')->format('d M Y'));


        // $timeWindows = [
        //     'pic_0200' => ['start' => '02:00:00', 'end' => '02:30:59'],
        //     'pic_0500' => ['start' => '05:00:00', 'end' => '05:30:59'],
        //     'pic_0800' => ['start' => '08:00:00', 'end' => '08:30:59'],
        //     'pic_1100' => ['start' => '11:00:00', 'end' => '11:30:59'],
        //     'pic_1400' => ['start' => '14:00:00', 'end' => '14:30:59'],
        //     'pic_1700' => ['start' => '17:00:00', 'end' => '17:30:59'],
        //     'pic_2000' => ['start' => '20:00:00', 'end' => '20:30:59'],
        //     'pic_2300' => ['start' => '23:00:00', 'end' => '23:30:59'],
        // ];

        // foreach ($timeWindows as $picField => $window) {
        //     $start = Carbon::createFromTimeString($window['start'], 'Asia/Jakarta');
        //     $end = Carbon::createFromTimeString($window['end'], 'Asia/Jakarta');

        //     if ($now->between($start, $end)) {
        //         $data[$picField] = $signature;
        //         break;
        //     }
        // }
            
        // Trigger deviation if value out of range
        $deviationDetected = false;
        foreach ($tempFields as $field) {
            if (!is_null($data[$field]) && ($data[$field] < $minTemp || $data[$field] > $maxTemp)) {
                $deviationDetected = true;
                break;
            }
        }

        if ($deviationDetected) {
            session()->put('deviation_triggered', true);
        }

        return $data;
    }
    protected function afterSave(): void
    {
        $record = $this->record;
        // Get temperature range from related location
        $location = $record->location;
        $minTemp = $location->temperature_start;
        $maxTemp = $location->temperature_end;
        $now = Carbon::now('Asia/Jakarta');

        $timeWindows = [
            'temp_0200' => ['start' => '02:00:00', 'end' => '02:30:59', 'time_field' => 'time_0200'],
            'temp_0500' => ['start' => '05:00:00', 'end' => '05:30:59', 'time_field' => 'time_0500'],
            'temp_0800' => ['start' => '08:00:00', 'end' => '08:30:59', 'time_field' => 'time_0800'],
            'temp_1100' => ['start' => '11:00:00', 'end' => '11:30:59', 'time_field' => 'time_1100'],
            'temp_1400' => ['start' => '14:00:00', 'end' => '14:30:59', 'time_field' => 'time_1400'],
            'temp_1700' => ['start' => '17:00:00', 'end' => '17:30:59', 'time_field' => 'time_1700'],
            'temp_2000' => ['start' => '20:00:00', 'end' => '20:30:59', 'time_field' => 'time_2000'],
            'temp_2300' => ['start' => '23:00:00', 'end' => '23:30:59', 'time_field' => 'time_2300'],
        ];

        foreach ($timeWindows as $tempField => $window) {
            $start = Carbon::createFromTimeString($window['start'], 'Asia/Jakarta');
            $end = Carbon::createFromTimeString($window['end'], 'Asia/Jakarta');

            if ($now->between($start, $end)) {
                $tempValue = $record->$tempField;
                $timeValue = $record->{$window['time_field']};

                if (!is_null($tempValue) && ($tempValue < $minTemp || $tempValue > $maxTemp)) {
                    session()->put('deviation_triggered', true);
                    session()->put('deviation_data', [[ // wrap in array to match expected format
                        'temperature_id' => $record->id,
                        'location_id' => $record->location_id,
                        'room_id' => $record->room_id,
                        'room_temperature_id' => $record->room_temperature_id,
                        'serial_number_id' => $record->serial_number_id,
                        'time' => $timeValue,
                        'temperature_deviation' => $tempValue,
                    ]]);
                }

                break; // Only evaluate the current time window
            }
        }
        
        $recipient = auth()->user();
        Notification::make()
            ->success()
            ->title('Temperature & Humidity for '. $location->location_name .' updated')
            ->body('Please check the Temperature & Humidity page for more details.')
            ->actions([
                NotificationAction::make('View')
                    ->url(TemperatureHumidityResource::getUrl('view', ['record' => $record]))
                    ->icon('heroicon-o-eye')
                    ->color('success')
                    ->close()
                    ->markAsRead()
            ])
            ->sendToDatabase($recipient);
    }
    protected function getRedirectUrl(): string
    {
        // Check if deviation was triggered
        if (session()->pull('deviation_triggered', false)) {
            $deviations = session()->pull('deviation_data', []);

            if (!empty($deviations) && isset($deviations[0])) {
                return TemperatureDeviationResource::getUrl('create', [
                    'temp_id' => $deviations[0]['temperature_id'],
                    'location_id' => $this->record->location_id,
                    'time' => $deviations[0]['time'],
                    'temperature_deviation' => $deviations[0]['temperature_deviation'],
                ]);
            }
        }

        // Default fallback
        return $this->getResource()::getUrl('index');
    }
    protected function getHeaderActions(): array
    {
        return [
            Action::make('is_reviewed')
                ->label('Mark as Reviewed')
                ->visible(fn () => Auth::user()->hasRole('Supply Chain Manager'))
                ->action(function (Model $record) {
                    $record->update([
                        'is_reviewed' => true,
                        'reviewed_by' => auth()->user()->initial . ' ' . strtoupper(now('Asia/Jakarta')->format('d M Y')),
                        'reviewed_at' => now('Asia/Jakarta'),
                    ]);
                Notification::make()
                    ->title('Success!')
                    ->body('Marked as reviewed successfully by Supply Chain Manager.')
                    ->success()
                    ->send();
                })
                ->requiresConfirmation()
                ->color('success')
                ->icon('heroicon-o-check'),
            Action::make('is_acknowledged')
                ->label('Mark as Acknowledged')
                ->visible(fn () => Auth::user()->hasRole(['QA Manager']))
                ->action(function (Model $record) {
                    $record->update([
                        'is_acknowledged' => true,
                        'acknowledged_by' => auth()->user()->initial . ' ' . strtoupper(now('Asia/Jakarta')->format('d M Y')),
                        'acknowledged_at' => now('Asia/Jakarta'),
                    ]);
                Notification::make()
                    ->title('Success!')
                    ->body('Marked as acknowledged successfully by QA Manager.')
                    ->success()
                    ->send();
                })
                ->requiresConfirmation()
                ->color('info')
                ->icon('heroicon-o-check'),
        ];
    }
    
    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Temperature & Humidity successfully updated');
    }
}
