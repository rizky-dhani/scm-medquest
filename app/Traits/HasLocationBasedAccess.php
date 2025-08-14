<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

trait HasLocationBasedAccess
{
    /**
     * Apply location-based filtering to the query
     */
    public static function applyLocationFilter(Builder $query): Builder
    {
        $user = Auth::user();
        
        // Super Admin, Admin, Supply Chain Manager, and QA Manager can see all records
        if ($user->hasRole(['Super Admin', 'Admin', 'Supply Chain Manager', 'QA Manager'])) {
            return $query;
        }
        
        // Regular users can only see records from their assigned location
        if ($user->location_id) {
            return $query->where('location_id', $user->location_id);
        }
        
        // Users without assigned location see nothing
        return $query->whereRaw('1 = 0');
    }

    /**
     * Get accessible location IDs for the current user
     */
    public static function getAccessibleLocationIds(): array
    {
        $user = Auth::user();
        
        // Super Admin, Admin, Supply Chain Manager, and QA Manager can access all locations
        if ($user->hasRole(['Super Admin', 'Admin', 'Supply Chain Manager', 'QA Manager'])) {
            return \App\Models\Location::pluck('id')->toArray();
        }
        
        // Regular users can only access their assigned location
        return $user->location_id ? [$user->location_id] : [];
    }

    /**
     * Check if user can access a specific location
     */
    public static function canAccessLocation(int $locationId): bool
    {
        return in_array($locationId, static::getAccessibleLocationIds());
    }
}
