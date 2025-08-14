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
    
    protected static ?string $pollingInterval = null;
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
            if ($user->hasRole(['Super Admin', 'Admin', 'Supply Chain Manager', 'QA Manager'])) {
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
                ->whereNotNull('time_0800')->whereNotNull('time_1100')->whereNotNull('time_1400')->whereNotNull('time_1700')
                ->whereNotNull('temp_0800')->whereNotNull('temp_1100')->whereNotNull('temp_1400')->whereNotNull('temp_1700')
                ->whereNotNull('rh_0800')->whereNotNull('rh_1100')->whereNotNull('rh_1400')->whereNotNull('rh_1700')
                ->count())
                ->color('warning')
                ->url(route('filament.dashboard.resources.temperature-humidities.reviewed')),
            Stat::make('Pending Acknowledged', $getBaseQuery()->where('is_acknowledged', false)
                ->whereNotNull('time_0800')->whereNotNull('time_1100')->whereNotNull('time_1400')->whereNotNull('time_1700')
                ->whereNotNull('temp_0800')->whereNotNull('temp_1100')->whereNotNull('temp_1400')->whereNotNull('temp_1700')
                ->whereNotNull('rh_0800')->whereNotNull('rh_1100')->whereNotNull('rh_1400')->whereNotNull('rh_1700')
                ->count())
                ->color('info')
                ->url(route('filament.dashboard.resources.temperature-humidities.acknowledged')),        
            ];
    }
}
