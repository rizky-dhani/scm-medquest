<?php

namespace App\Filament\Resources\TemperatureHumidityResource\Pages;

use Carbon\Carbon;
use App\Models\Room;
use Filament\Actions;
use App\Models\Location;
use App\Models\SerialNumber;
use Filament\Actions\Action;
use App\Models\RoomTemperature;
use Filament\Actions\CreateAction;
use App\Models\TemperatureHumidity;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Filament\Resources\Pages\ListRecords;
use App\Exports\TemperatureHumidityExport;
use App\Exports\AllLocationsTemperatureHumidityExport;
use App\Filament\Resources\TemperatureHumidityResource;

class ListTemperatureHumidities extends ListRecords
{
    protected static string $resource = TemperatureHumidityResource::class;
    protected static ?string $title = 'All Temperature & Humidity';
    public function getBreadcrumb(): string
    {
        return 'All'; // or any label you want
    }
    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
            ->label('New Temperature & Humidity')
            ->color('success')
            ->visible(fn() => auth()->user()->hasRole(['Supply Chain Officer', 'Security'])),
            Action::make('custom_export')
                ->label('Export')
                ->icon('heroicon-o-arrow-down-tray')
                ->form([
                    Select::make('room_id')
                        ->label('Room')
                        ->relationship('room', 'room_name')
                        ->options(function (callable $get) {
                            $locationId = auth()->user()->location_id;
                        
                        if (!$locationId) {
                            return [];
                        }

                        return Room::where('location_id', $locationId)
                            ->pluck('room_name', 'id');
                    })
                    ->searchable()
                    ->reactive()
                    ->preload()
                    ->required()
                    ->afterStateUpdated(function ($state, callable $set) {
                        $set('serial_number_id', null);
                    }),

                    Select::make('serial_number_id')
                        ->label('Serial Number')
                        ->options(function (callable $get) {
                            $roomId = $get('room_id');

                            if (!$roomId) {
                                return [];
                            }

                            return SerialNumber::where('room_id', $roomId)
                                ->pluck('serial_number', 'id');
                        })
                        ->searchable()
                        ->required()
                        ->disabled(fn (callable $get) => ! $get('room_id'))
                        ->preload()
                        ->required(),
                    Select::make('room_temperature_id')
                        ->label('Room Temperature Standards')
                        ->relationship('roomTemperature', 'temperature_start')
                        ->options(function (callable $get) {
                            $roomId = $get('room_id');
                            if (!$roomId) {
                                return [];
                            }
                            return RoomTemperature::where('room_id', $roomId)
                                ->get()
                                ->mapWithKeys(function ($item) {
                                    $label = "{$item->temperature_start}°C to {$item->temperature_end}°C";
                                    return [$item->id => $label];
                        })
                                ->toArray();
                        })
                        ->searchable()
                        ->reactive()
                        ->preload()
                        ->required()
                        ->disabled(fn (callable $get) => ! $get('room_id')),
                    Select::make('month_type')
                        ->label('Month Type')
                        ->options([
                            'this_month' => 'This Month',
                            'choose' => 'Choose Month',
                        ])
                        ->default('this_month')
                        ->reactive(),

                    DatePicker::make('chosen_month')
                        ->label('Choose Month')
                        ->displayFormat('F Y')
                        ->visible(fn ($get) => $get('month_type') === 'choose')
                        ->required(fn ($get) => $get('month_type') === 'choose'),
                ])
                ->action(function (array $data) {
                    $user = Auth::user();
                    $location_id = $user->location_id;
                    $room_id = $data['room_id'] ?? null;
                    $serial_number_id = $data['serial_number_id'] ?? null;
                    $room_temperature_id = $data['room_temperature_id'] ?? null;
                    $room = Room::find($room_id);
                    $serialNumber = SerialNumber::find($serial_number_id);

                    if ($data['month_type'] === 'this_month') {
                        $month = now()->month;
                        $year = now()->year;
                    } else {
                        $chosenMonth = Carbon::parse($data['chosen_month']);
                        $month = $chosenMonth->month;
                        $year = $chosenMonth->year;
                    }
                    $records = TemperatureHumidity::query()
                        ->where([
                            ['location_id', '=', $location_id],
                            ['room_id', '=', $room_id],
                            ['serial_number_id', '=', $serial_number_id],
                            ['room_temperature_id', '=', $room_temperature_id],
                        ])
                        ->whereMonth('period', $month)
                        ->whereYear('period', $year)
                        ->with(['location', 'room', 'serialNumber', 'roomTemperature'])
                        ->get();

                    $monthName = strtoupper(Carbon::createFromDate($year, $month)->format('M'));
                    $sluggedLocation = strtoupper($room->room_name.'_'.$serialNumber->serial_number);
                    $filename = "TemperatureHumidity_{$monthName}{$year}_{$sluggedLocation}.xlsx";

                    return Excel::download(new TemperatureHumidityExport($records, $room->room_name), $filename);
                }),
            Action::make('export_all_locations')
                ->label('Export All Locations')
                ->icon('heroicon-o-arrow-down-tray')
                ->visible(function () {
                    $user = Auth::user();
                    return $user->location_id === null && 
                        $user->hasRole(['Supply Chain Manager', 'QA Manager']);
                })
                ->form([
                    Select::make('month_type_all')
                        ->label('Month Type')
                        ->options([
                            'this_month' => 'This Month',
                            'choose' => 'Choose Month',
                        ])
                        ->default('this_month')
                        ->reactive(),

                    DatePicker::make('chosen_month_all')
                        ->label('Choose Month')
                        ->displayFormat('F Y')
                        ->visible(fn ($get) => $get('month_type_all') === 'choose')
                        ->required(fn ($get) => $get('month_type_all') === 'choose'),
                ])
                ->action(function (array $data) {
                    if ($data['month_type_all'] === 'this_month') {
                        $month = now()->month;
                        $year = now()->year;
                    } else {
                        $chosenMonth = Carbon::parse($data['chosen_month_all']);
                        $month = $chosenMonth->month;
                        $year = $chosenMonth->year;
                    }

                    // Get all locations
                    $locations = Location::all();
                    $exportData = [];

                    foreach ($locations as $location) {
                        // Get all records for this location and month
                        $records = TemperatureHumidity::query()
                            ->where('location_id', $location->id)
                            ->whereMonth('period', $month)
                            ->whereYear('period', $year)
                            ->with(['location', 'room', 'serialNumber', 'roomTemperature'])
                            ->get();

                        if ($records->isNotEmpty()) {
                            $exportData[$location->id] = [
                                'records' => $records,
                                'location' => $location,
                                'location_name' => $location->location_name
                            ];
                        }
                    }

                    $monthName = strtoupper(Carbon::createFromDate($year, $month)->format('M'));
                    $filename = "TemperatureHumidity_ALL_{$monthName}{$year}.xlsx";

                    return Excel::download(new AllLocationsTemperatureHumidityExport($exportData), $filename);
                })
        ];
    }
}
