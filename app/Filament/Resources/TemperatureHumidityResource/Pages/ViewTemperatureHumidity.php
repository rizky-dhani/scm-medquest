<?php

namespace App\Filament\Resources\TemperatureHumidityResource\Pages;

use Carbon\Carbon;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Infolists\Infolist;
use App\Models\TemperatureHumidity;
use Illuminate\Support\Facades\Auth;
use Filament\Pages\Actions\EditAction;
use Illuminate\Database\Eloquent\Model;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use App\Filament\Resources\TemperatureHumidityResource;
use App\Filament\Resources\TemperatureDeviationResource;

class ViewTemperatureHumidity extends ViewRecord
{
    protected static string $resource = TemperatureHumidityResource::class;
    protected function getHeaderActions(): array
    {
        return [
            
            EditAction::make()
                ->visible(fn () => Auth::user()->hasRole('Supply Chain Officer')),
            Action::make('view_deviations')
                ->label('View Deviations')
                ->icon('heroicon-o-exclamation-triangle')
                ->color('warning')
                ->url(fn (TemperatureHumidity $record) => 
                    $record->temperatureDeviations()->exists() 
                        ? TemperatureDeviationResource::getUrl('view', ['record' => $record->temperatureDeviations()->first()->id])
                        : TemperatureDeviationResource::getUrl('index')
                )
                ->visible(fn (TemperatureHumidity $record) => $record->temperatureDeviations()->exists()),
            Action::make('is_reviewed')
                ->label('Mark as Reviewed')
                    ->visible(function (TemperatureHumidity $record) {
                        $isAcknowledged = $record->is_acknowledged == false && $record->time_0800 != null && $record->time_1100 != null && $record->time_1400 != null && $record->time_1700 != null && $record->temp_0800 != null && $record->temp_1100 != null && $record->temp_1400 != null && $record->temp_1700 != null;    
                        $admin = Auth::user()->hasRole(['Supply Chain Manager']);
                        return $isAcknowledged && $admin;
                    })
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
                    ->visible(function (TemperatureHumidity $record) {
                        $isAcknowledged = $record->is_acknowledged == false && $record->time_0800 != null && $record->time_1100 != null && $record->time_1400 != null && $record->time_1700 != null && $record->temp_0800 != null && $record->temp_1100 != null && $record->temp_1400 != null && $record->temp_1700 != null;    
                        $admin = Auth::user()->hasRole(['QA Manager']);
                        return $isAcknowledged && $admin;
                    })
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

    
    public static function infolists(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Date & Period')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('date')
                            ->label('Date')
                            ->formatStateUsing(fn ($record) => Carbon::parse($record->date)->format('d/m/Y')),
                        TextEntry::make('period')
                            ->label('Period')
                            ->formatStateUsing(fn ($record) => strtoupper(Carbon::parse($record->period)->format('M Y'))),
                    ]),
                Section::make('Reviewed & Acknowledged')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('reviewed_by')
                            ->label('Reviewed By')
                            ->formatStateUsing(fn ($record) => $record->reviewed_by ? $record->reviewed_by : '-'),
                        TextEntry::make('acknowledged_by')
                            ->label('Acknowledged By')
                            ->formatStateUsing(fn ($record) => $record->acknowledged_by ? $record->acknowledged_by : '-'),
                    ]),
                Section::make('Location & Storage Temperature Standards')
                    ->columns(4)
                    ->schema([
                        TextEntry::make('location_id')
                            ->label('Location')
                            ->formatStateUsing(fn ($record) => $record->location->location_name),
                            TextEntry::make('room_id')
                                ->label('Room')
                                ->formatStateUsing(fn ($record) => $record->room->room_name),
                            TextEntry::make('serial_number_id')
                                ->label('Serial Number')
                                ->formatStateUsing(fn ($record) => $record->serialNumber->serial_number),
                        TextEntry::make('room_temperature_id')
                            ->label('Storage Temperature Standards')
                            ->formatStateUsing(fn ($record) => $record->roomTemperature->temperature_start.'°C to '.$record->roomTemperature->temperature_end.'°C'),
                    ]),
                Section::make('Time Range')
                    ->columns(2)
                    ->schema([
                        Section::make('02:00')
                        ->columns(4)
                        ->schema([
                            TextEntry::make('time_0200')
                                ->label('Time')
                                ->formatStateUsing(fn ($record) => $record->time_0200 ? Carbon::parse($record->time_0200)->format('H:i') : '-'),
                            TextEntry::make('temp_0200')
                                ->label('Temperature')
                                ->formatStateUsing(fn ($record) => $record->temp_0200.' °C' ?? '-'),
                            TextEntry::make('rh_0200')
                                ->label('Humidity')
                                ->formatStateUsing(fn ($record) => $record->rh_0200 !== null ? $record->rh_0200.'%' : 'N/A'),
                            TextEntry::make('pic_0200')
                                ->label('PIC')
                        ]),
                        Section::make('05:00')
                        ->columns(4)
                        ->schema([
                            TextEntry::make('time_0500')
                                ->label('Time')
                                ->formatStateUsing(fn ($record) => $record->time_0500 ? Carbon::parse($record->time_0500)->format('H:i') : '-'),
                            TextEntry::make('temp_0500')
                                ->label('Temperature')
                                ->formatStateUsing(fn ($record) => $record->temp_0500.' °C' ?? '-'),
                            TextEntry::make('rh_0500')
                                ->label('Humidity')
                                ->formatStateUsing(fn ($record) => $record->rh_0500 !== null ? $record->rh_0500.'%' : 'N/A'),
                            TextEntry::make('pic_0500')
                                ->label('PIC')
                        ]),
                        Section::make('08:00')
                        ->columns(4)
                        ->schema([
                            TextEntry::make('time_0800')
                                ->label('Time')
                                ->formatStateUsing(fn ($record) => $record->time_0800 ? Carbon::parse($record->time_0800)->format('H:i') : '-'),
                            TextEntry::make('temp_0800')
                                ->label('Temperature')
                                ->formatStateUsing(fn ($record) => $record->temp_0800.' °C' ?? '-'),
                            TextEntry::make('rh_0800')
                                ->label('Humidity')
                                ->formatStateUsing(fn ($record) => $record->rh_0800 !== null ? $record->rh_0800.'%' : 'N/A'),
                            TextEntry::make('pic_0800')
                                ->label('PIC')
                        ]),
                        Section::make('11:00')
                        ->columns(4)
                        ->schema([
                            TextEntry::make('time_1100')
                                ->label('Time')
                                ->formatStateUsing(fn ($record) => $record->time_1100 ? Carbon::parse($record->time_1100)->format('H:i') : '-'),
                            TextEntry::make('temp_1100')
                                ->label('Temperature')
                                ->formatStateUsing(fn ($record) => $record->temp_1100.' °C' ?? '-'),
                            TextEntry::make('rh_1100')
                                ->label('Humidity')
                                ->formatStateUsing(fn ($record) => $record->rh_1100 !== null ? $record->rh_1100.'%' : 'N/A'),
                            TextEntry::make('pic_1100')
                                ->label('PIC')
                        ]),
                        Section::make('14:00')
                        ->columns(4)
                        ->schema([
                            TextEntry::make('time_1400')
                                ->label('Time')
                                ->formatStateUsing(fn ($record) => $record->time_1400 ? Carbon::parse($record->time_1400)->format('H:i') : '-'),
                            TextEntry::make('temp_1400')
                                ->label('Temperature')
                                ->formatStateUsing(fn ($record) => $record->temp_1400.' °C' ?? '-'),
                            TextEntry::make('rh_1400')
                                ->label('Humidity')
                                ->formatStateUsing(fn ($record) => $record->rh_1400 !== null ? $record->rh_1400.'%' : 'N/A'),
                            TextEntry::make('pic_1400')
                                ->label('PIC')
                        ]),
                        Section::make('17:00')
                        ->columns(4)
                        ->schema([
                            TextEntry::make('time_1700')
                                ->label('Time')
                                ->formatStateUsing(fn ($record) => $record->time_1700 ? Carbon::parse($record->time_1700)->format('H:i') : '-'),
                            TextEntry::make('temp_1700')
                                ->label('Temperature')
                                ->formatStateUsing(fn ($record) => $record->temp_1700.' °C' ?? '-'),
                            TextEntry::make('rh_1700')
                                ->label('Humidity')
                                ->formatStateUsing(fn ($record) => $record->rh_1700 !== null ? $record->rh_1700.'%' : 'N/A'),
                            TextEntry::make('pic_1700')
                                ->label('PIC')
                        ]),
                        Section::make('20:00')
                        ->columns(4)
                        ->schema([
                            TextEntry::make('time_2000')
                                ->label('Time')
                                ->formatStateUsing(fn ($record) => $record->time_2000 ? Carbon::parse($record->time_2000)->format('H:i') : '-'),
                            TextEntry::make('temp_2000')
                                ->label('Temperature')
                                ->formatStateUsing(fn ($record) => $record->temp_2000.' °C' ?? '-'),
                            TextEntry::make('rh_2000')
                                ->label('Humidity')
                                ->formatStateUsing(fn ($record) => $record->rh_2000 !== null ? $record->rh_2000.'%' : 'N/A'),
                            TextEntry::make('pic_2000')
                                ->label('PIC')
                        ]),
                        Section::make('23:00')
                        ->columns(4)
                        ->schema([
                            TextEntry::make('time_2300')
                                ->label('Time')
                                ->formatStateUsing(fn ($record) => $record->time_2300 ? Carbon::parse($record->time_2300)->format('H:i') : '-'),
                            TextEntry::make('temp_2300')
                                ->label('Temperature')
                                ->formatStateUsing(fn ($record) => $record->temp_2300.' °C' ?? '-'),
                            TextEntry::make('rh_2300')
                                ->label('Humidity')
                                ->formatStateUsing(fn ($record) => $record->rh_2300 !== null ? $record->rh_2300.'%' : 'N/A'),
                            TextEntry::make('pic_2300')
                                ->label('PIC')
                        ]),
                    ]),
            ]);
    }
}
