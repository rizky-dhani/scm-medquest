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
use Filament\Forms\Components\Grid;
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
                    Select::make('location_id')
                        ->label('Location')
                        ->relationship('location', 'location_name')
                        ->options(function () {
                            $user = auth()->user();
                        
                            // If user has a specific location, only show that location
                            if ($user->location_id) {
                                return Location::where('id', $user->location_id)
                                    ->pluck('location_name', 'id');
                            }

                            // If user has no specific location (admin), show all locations
                            return Location::pluck('location_name', 'id');
                        })
                        ->searchable()
                        ->reactive()
                        ->preload()
                        ->required()
                        ->afterStateUpdated(function ($state, callable $set) {
                            $set('room_id', null);
                        }),

                    Select::make('room_id')
                        ->label('Rooms')
                        ->multiple()
                        ->options(function (callable $get) {
                            $locationId = $get('location_id');
                        
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
                        ->disabled(fn (callable $get) => ! $get('location_id')),

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
                    $location_id = $data['location_id'] ?? null;
                    $room_ids = $data['room_id'] ?? [];

                    if ($data['month_type'] === 'this_month') {
                        $month = now()->month;
                        $year = now()->year;
                    } else {
                        $chosenMonth = Carbon::parse($data['chosen_month']);
                        $month = $chosenMonth->month;
                        $year = $chosenMonth->year;
                    }

                    // Build query with multiple room support
                    $query = TemperatureHumidity::query()
                        ->where('location_id', $location_id)
                        ->whereMonth('period', $month)
                        ->whereYear('period', $year)
                        ->with(['location', 'room', 'serialNumber', 'roomTemperature']);

                    if (!empty($room_ids)) {
                        $query->whereIn('room_id', $room_ids);
                    }

                    $records = $query->get();

                    $monthName = strtoupper(Carbon::createFromDate($year, $month)->format('M'));
                    $location = Location::find($location_id);
                    $filename = "TemperatureHumidity_{$monthName}{$year}_{$location->location_name}.xlsx";

                    return Excel::download(new TemperatureHumidityExport($records, $location->location_name), $filename);
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
