<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
}
