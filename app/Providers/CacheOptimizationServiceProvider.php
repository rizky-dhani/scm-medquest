<?php

namespace App\Providers;

use App\Models\Location;
use App\Models\Room;
use App\Models\RoomTemperature;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Cache;

class CacheOptimizationServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Cache common queries for frequently accessed data

        // Override the get method for locations to use cache
        Location::updated(function ($location) {
            Cache::forget('locations.all');
        });

        Location::created(function ($location) {
            Cache::forget('locations.all');
        });

        Location::deleted(function ($location) {
            Cache::forget('locations.all');
        });

        // Override the get method for rooms to use cache
        Room::updated(function ($room) {
            Cache::forget('rooms.all');
            Cache::forget("rooms.location.{$room->location_id}");
        });

        Room::created(function ($room) {
            Cache::forget('rooms.all');
            Cache::forget("rooms.location.{$room->location_id}");
        });

        Room::deleted(function ($room) {
            Cache::forget('rooms.all');
            Cache::forget("rooms.location.{$room->location_id}");
        });

        // Override the get method for room temperatures to use cache
        RoomTemperature::updated(function ($roomTemperature) {
            Cache::forget('room-temperatures.all');
            Cache::forget("room-temperatures.room.{$roomTemperature->room_id}");
        });

        RoomTemperature::created(function ($roomTemperature) {
            Cache::forget('room-temperatures.all');
            Cache::forget("room-temperatures.room.{$roomTemperature->room_id}");
        });

        RoomTemperature::deleted(function ($roomTemperature) {
            Cache::forget('room-temperatures.all');
            Cache::forget("room-temperatures.room.{$roomTemperature->room_id}");
        });
    }
}
