<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoomTemperature extends Model
{
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
}
