<?php

namespace App\Filament\Resources\TemperatureDeviationResource\Pages;

use Carbon\Carbon;
use App\Models\Room;
use Filament\Actions;
use App\Models\Location;
use App\Models\SerialNumber;
use Filament\Actions\Action;
use App\Models\RoomTemperature;
use Filament\Actions\CreateAction;
use App\Models\TemperatureDeviation;
use Maatwebsite\Excel\Facades\Excel;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Filament\Resources\Pages\ListRecords;
use Filament\Notifications\Notification;
use App\Exports\TemperatureDeviationExport;
use App\Filament\Resources\TemperatureDeviationResource;

class ListTemperatureDeviations extends ListRecords
{
    protected static string $resource = TemperatureDeviationResource::class;
    protected static ?string $title = 'All Temperature Deviations';
    public function getBreadcrumb(): string
    {
        return 'All'; // or any label you want
    }
    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
            ->label('New Temperature Deviation')
            ->color('success')
            ->visible(fn() => auth()->user()->hasRole(['Supply Chain Officer', 'Security'])),
            Action::make('custom_export')
                ->label('Export')
                ->icon('heroicon-o-arrow-down-tray')
                ->form([
                    Select::make('location_id')->label('Location')->options(Location::pluck('location_name', 'id'))->searchable()->reactive()->required(),
                    Select::make('month_type')
                        ->label('Month Type')
                        ->options([
                            'this_month' => 'This Month',
                            'choose' => 'Choose Month',
                        ])
                        ->default('this_month')
                        ->reactive(),

                    DatePicker::make('chosen_month')->label('Choose Month')->displayFormat('F Y')->visible(fn($get) => $get('month_type') === 'choose')->required(fn($get) => $get('month_type') === 'choose'),
                ])
                ->action(function (array $data) {
                    $location_id = $data['location_id'];
                    $location = Location::find($location_id);
                    if ($data['month_type'] === 'this_month') {
                        $month = now()->month;
                        $year = now()->year;
                    } else {
                        $chosenMonth = Carbon::parse($data['chosen_month']);
                        $month = $chosenMonth->month;
                        $year = $chosenMonth->year;
                    }
                    $records = TemperatureDeviation::query()
                        ->where('location_id', $location_id)
                        ->whereMonth('date', $month)
                        ->whereYear('date', $year)
                        ->with(['location', 'room', 'serialNumber', 'roomTemperature'])
                        ->get();
                    
                    // Check if there are any records to export
                    if ($records->isEmpty()) {
                        // Show alert message
                        Notification::make()
                            ->warning()
                            ->title('No data is found')
                            ->send();
                        return;
                    }

                    $monthName = strtoupper(Carbon::createFromDate($year, $month)->format('M'));
                    $sluggedLocation = strtoupper($location->location_name);
                    $filename = "TemperatureDeviation_{$monthName}{$year}_{$sluggedLocation}.xlsx";

                    return Excel::download(new TemperatureDeviationExport($records), $filename);
                }),
        ];
    }
}
