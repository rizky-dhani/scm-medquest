<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TemperatureDeviation extends Model
{
    protected $guarded = ['id'];

    public function location()
    {
        return $this->belongsTo(Location::class);
    }
    public function room()
    {
        return $this->belongsTo(Room::class);
    }
    public function roomTemperature()
    {
        return $this->belongsTo(RoomTemperature::class);
    }
    public function temperatureHumidity()
    {
        return $this->belongsTo(TemperatureHumidity::class);
    }
    public function serialNumber()
    {
        return $this->belongsTo(SerialNumber::class,'serial_number_id', 'id');
    }
}
