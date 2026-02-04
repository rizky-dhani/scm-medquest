<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class RoomTemperature extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function getRouteKeyName()
    {
        return 'roomTemperatureId';
    }

    public function room()
    {
        return $this->belongsTo(Room::class, 'room_id', 'id');
    }

    public function temperatureHumidities()
    {
        return $this->hasMany(TemperatureHumidity::class, 'room_temperature_id', 'id');
    }

    public function temperatureDeviations()
    {
        return $this->hasMany(TemperatureDeviation::class, 'room_temperature_id', 'id');
    }

    /**
     * Boot the model and set up event listeners
     */
    protected static function boot()
    {
        parent::boot();

        // Clear cache when room temperature is updated
        static::saved(function () {
            Cache::forget('room-temperatures.all');
        });

        static::deleted(function () {
            Cache::forget('room-temperatures.all');
        });
    }

    /**
     * Get a collection of all room temperatures with caching
     */
    public static function getAllCached()
    {
        return Cache::remember('room-temperatures.all', 3600, function () {
            return self::all();
        });
    }

    /**
     * Get room temperatures by room with caching
     */
    public static function getByRoomCached($roomId)
    {
        return Cache::remember("room-temperatures.room.{$roomId}", 3600, function () use ($roomId) {
            return self::where('room_id', $roomId)->get();
        });
    }
}
