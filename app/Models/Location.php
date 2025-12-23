<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Location extends Model
{
    protected $guarded = ['id'];

    public function rooms()
    {
        return $this->hasMany(Room::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function getRouteKeyName()
    {
        return 'locationId';
    }

    public function temperatureHumidities()
    {
        return $this->hasMany(TemperatureHumidity::class);
    }

    public function temperatureDeviations()
    {
        return $this->hasMany(TemperatureDeviation::class);
    }

    /**
     * Boot the model and set up event listeners
     */
    protected static function boot()
    {
        parent::boot();

        // Clear cache when location is updated
        static::saved(function () {
            Cache::forget('locations.all');
        });

        static::deleted(function () {
            Cache::forget('locations.all');
        });
    }

    /**
     * Get a collection of all locations with caching
     */
    public static function getAllCached()
    {
        return Cache::remember('locations.all', 3600, function () {
            return self::all();
        });
    }
}
