<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
}
