<?php

namespace App\Models;

use App\Events\TemperatureDeviationCreated;
use App\Services\TemperatureDeviationNotificationService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TemperatureDeviation extends Model
{
    protected $guarded = ['id'];

    protected static function booted(): void
    {
        static::created(function ($temperatureDeviation) {
            // Dispatch event to trigger notifications
            event(new TemperatureDeviationCreated($temperatureDeviation));

            // Send notification immediately after creation
            dispatch(function () use ($temperatureDeviation) {
                app(TemperatureDeviationNotificationService::class)
                    ->sendDeviationNotification($temperatureDeviation);
            })->afterResponse();
        });
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function roomTemperature(): BelongsTo
    {
        return $this->belongsTo(RoomTemperature::class);
    }

    public function temperatureHumidity(): BelongsTo
    {
        return $this->belongsTo(TemperatureHumidity::class);
    }

    public function serialNumber(): BelongsTo
    {
        return $this->belongsTo(SerialNumber::class,'serial_number_id', 'id');
    }
}
