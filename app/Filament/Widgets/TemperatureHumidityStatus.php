<?php

namespace App\Filament\Widgets;

use Carbon\Carbon;
use App\Models\TemperatureHumidity;
use App\Traits\HasLocationBasedAccess;
use Illuminate\Support\Facades\Auth;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class TemperatureHumidityStatus extends BaseWidget
{
    use HasLocationBasedAccess;

    protected ?string $pollingInterval = null;
    protected static bool $isLazy = false;
    protected ?string $heading = 'Temperature & Humidity';
    protected static ?int $sort = 0;

    protected function getStats(): array
    {
        $user = Auth::user();

        // Build base query with location filtering
        $getBaseQuery = function() use ($user) {
            $query = TemperatureHumidity::query();

            // Apply location-based filtering
            if ($user->hasRole(['Super Admin', 'Admin', 'Supply Chain Manager', 'QA Manager', 'QA Staff'])) {
                // Can see all locations
                return $query;
            } elseif ($user->location_id) {
                // Regular users can only see their assigned location
                return $query->where('location_id', $user->location_id);
            } else {
                // Users without assigned location see nothing
                return $query->whereRaw('1 = 0');
            }
        };

        return [
            Stat::make('Total Data (this month)', $getBaseQuery()->whereMonth('date', Carbon::now()->month)->count())
                ->color('dark')
                ->url(route('filament.dashboard.resources.temperature-humidities.index')),
            Stat::make('Pending Review',
                $getBaseQuery()->where('is_reviewed', false)
                ->whereNotNull('time_0200')
                ->whereNotNull('time_0500')
                ->whereNotNull('time_0800')
                ->whereNotNull('time_1100')
                ->whereNotNull('time_1400')
                ->whereNotNull('time_1700')
                ->whereNotNull('time_2000')
                ->whereNotNull('time_2300')
                ->whereNotNull('temp_0200')
                ->whereNotNull('temp_0500')
                ->whereNotNull('temp_0800')
                ->whereNotNull('temp_1100')
                ->whereNotNull('temp_1400')
                ->whereNotNull('temp_1700')
                ->whereNotNull('temp_2000')
                ->whereNotNull('temp_2300')
                ->whereNotNull('pic_0200')
                ->whereNotNull('pic_0500')
                ->whereNotNull('pic_0800')
                ->whereNotNull('pic_1100')
                ->whereNotNull('pic_1400')
                ->whereNotNull('pic_1700')
                ->whereNotNull('pic_2000')
                ->whereNotNull('pic_2300')
                ->count())
                ->color('warning')
                ->url(route('filament.dashboard.resources.temperature-humidities.reviewed')),
            Stat::make('Pending Acknowledged', $getBaseQuery()->where('is_acknowledged', false)
                ->whereNotNull('time_0200')
                ->whereNotNull('time_0500')
                ->whereNotNull('time_0800')
                ->whereNotNull('time_1100')
                ->whereNotNull('time_1400')
                ->whereNotNull('time_1700')
                ->whereNotNull('time_2000')
                ->whereNotNull('time_2300')
                ->whereNotNull('temp_0200')
                ->whereNotNull('temp_0500')
                ->whereNotNull('temp_0800')
                ->whereNotNull('temp_1100')
                ->whereNotNull('temp_1400')
                ->whereNotNull('temp_1700')
                ->whereNotNull('temp_2000')
                ->whereNotNull('temp_2300')
                ->whereNotNull('pic_0200')
                ->whereNotNull('pic_0500')
                ->whereNotNull('pic_0800')
                ->whereNotNull('pic_1100')
                ->whereNotNull('pic_1400')
                ->whereNotNull('pic_1700')
                ->whereNotNull('pic_2000')
                ->whereNotNull('pic_2300')
                ->count())
                ->color('info')
                ->url(route('filament.dashboard.resources.temperature-humidities.acknowledged')),
            ];
    }
}
