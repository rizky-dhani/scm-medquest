<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Room extends Model
{
    protected $guarded = ['id'];

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public function roomTemperatures()
    {
        return $this->hasMany(RoomTemperature::class);
    }

    public function serialNumbers()
    {
        return $this->hasMany(SerialNumber::class);
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

        // Clear cache when room is updated
        static::saved(function () {
            Cache::forget('rooms.all');
            Cache::forget('rooms.by_location');
        });

        static::deleted(function () {
            Cache::forget('rooms.all');
            Cache::forget('rooms.by_location');
        });
    }

    /**
     * Get a collection of all rooms with caching
     */
    public static function getAllCached()
    {
        return Cache::remember('rooms.all', 3600, function () {
            return self::all();
        });
    }

    /**
     * Get rooms by location with caching
     */
    public static function getByLocationCached($locationId)
    {
        return Cache::remember("rooms.location.{$locationId}", 3600, function () use ($locationId) {
            return self::where('location_id', $locationId)->get();
        });
    }
}
