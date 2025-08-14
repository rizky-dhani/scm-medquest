<?php

namespace App\Filament\Widgets;

use Carbon\Carbon;
use App\Models\TemperatureDeviation;
use App\Traits\HasLocationBasedAccess;
use Illuminate\Support\Facades\Auth;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class TemperatureDeviationStatus extends BaseWidget
{
    use HasLocationBasedAccess;
    
    protected static ?string $pollingInterval = null;
    protected static bool $isLazy = false;
    protected ?string $heading = 'Temperature Deviation';
    protected static ?int $sort = 1;
    
    protected function getStats(): array
    {
        $user = Auth::user();
        
        // Build base query with location filtering
        $getBaseQuery = function() use ($user) {
            $query = TemperatureDeviation::query();
            
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
                ->url(route('filament.dashboard.resources.temperature-deviations.index')),
            Stat::make('Pending Review', $getBaseQuery()->where('is_reviewed', false)->whereNotNull('length_temperature_deviation')->whereNotNull('risk_analysis')->count())
                ->color('warning')
                ->url(route('filament.dashboard.resources.temperature-deviations.reviewed')),
            Stat::make('Pending Acknowledged', $getBaseQuery()->where('is_acknowledged', false)->whereNotNull('length_temperature_deviation')->whereNotNull('risk_analysis')->count())
                ->color('info')
                ->url(route('filament.dashboard.resources.temperature-deviations.acknowledged')),        
            ];
    }
}
