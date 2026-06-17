<?php

namespace App\Models;

use App\Services\TemperatureHumidityNotificationService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class TemperatureHumidity extends Model
{
    protected $guarded = ['id'];

    protected static array $picUserCache = [];

    protected static function booted(): void
    {
        static::saving(function ($temperatureHumidity) {
            // Cache user and role checks to avoid redundant DB/cache calls
            $currentTime = Carbon::now('Asia/Jakarta')->format('H:i:s');
            $user = auth()->user();
            $isSuperAdmin = $user?->hasRole('Super Admin');
            $isSecurity = $user?->hasRole('Security');
            $nowFormatted = strtoupper(now('Asia/Jakarta')->format('d M Y'));

            $timeSlots = [
                '0200' => '02:00:00',
                '0500' => '05:00:00',
                '0800' => '08:00:00',
                '1100' => '11:00:00',
                '1400' => '14:00:00',
                '1700' => '17:00:00',
                '2000' => '20:00:00',
                '2300' => '23:00:00',
            ];

            foreach ($timeSlots as $slot => $windowStart) {
                $timeField = "time_{$slot}";
                $picField = "pic_{$slot}";
                $picIdField = "pic_{$slot}_id";

                if (empty($temperatureHumidity->{$timeField}) || ! empty($temperatureHumidity->{$picField})) {
                    continue;
                }

                $windowEnd = substr($windowStart, 0, 2).':30:59';

                if (! $isSuperAdmin && ($currentTime < $windowStart || $currentTime >= $windowEnd)) {
                    continue;
                }

                $temperatureHumidity->{$picField} = $isSecurity
                    ? $user->name
                    : $user->initial.' '.$nowFormatted;
                $temperatureHumidity->{$picIdField} = $user->id;
            }
        });

        static::updated(function (TemperatureHumidity $temperatureHumidity) {
            // Check if any of the monitored fields were updated
            $monitoredFields = [
                'time_0800', 'time_1100', 'time_1400', 'time_1700',
                'time_2000', 'time_2300', 'time_0200', 'time_0500',
                'temp_0800', 'temp_1100', 'temp_1400', 'temp_1700',
                'temp_2000', 'temp_2300', 'temp_0200', 'temp_0500',
                'rh_0800', 'rh_1100', 'rh_1400', 'rh_1700',
                'rh_2000', 'rh_2300', 'rh_0200', 'rh_0500',
                'is_reviewed',
            ];

            $hasRelevantChanges = false;
            foreach ($monitoredFields as $field) {
                if ($temperatureHumidity->wasChanged($field)) {
                    $hasRelevantChanges = true;
                    break;
                }
            }

            if ($hasRelevantChanges) {
                // Dispatch notification check asynchronously to avoid blocking the update
                dispatch(function () use ($temperatureHumidity) {
                    app(TemperatureHumidityNotificationService::class)
                        ->checkAndSendNotifications($temperatureHumidity);
                })->afterResponse();
            }
        });
    }

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

    public function temperatureDeviations()
    {
        return $this->hasMany(TemperatureDeviation::class);
    }

    public function serialNumber()
    {
        return $this->belongsTo(SerialNumber::class);
    }

    // PIC relationships - users who filled temperature data at different time slots

    public function pic0200User()
    {
        return $this->belongsTo(User::class, 'pic_0200');
    }

    public function pic0500User()
    {
        return $this->belongsTo(User::class, 'pic_0500');
    }

    public function pic0800User()
    {
        return $this->belongsTo(User::class, 'pic_0800');
    }

    public function pic1100User()
    {
        return $this->belongsTo(User::class, 'pic_1100');
    }

    public function pic1400User()
    {
        return $this->belongsTo(User::class, 'pic_1400');
    }

    public function pic1700User()
    {
        return $this->belongsTo(User::class, 'pic_1700');
    }

    public function pic2000User()
    {
        return $this->belongsTo(User::class, 'pic_2000');
    }

    public function pic2300User()
    {
        return $this->belongsTo(User::class, 'pic_2300');
    }

    /**
     * Format PIC signature based on user role
     * Handles both old signature strings and new user IDs
     */
    public function formatPicSignature($picField)
    {
        $picValue = $this->{$picField};
        if (! $picValue) {
            return '-';
        }

        // Check if it's a numeric value (user ID) or string (old signature)
        if (is_numeric($picValue)) {
            // New format: user ID
            $user = static::$picUserCache[$picValue] ??= User::find($picValue);
            if (! $user) {
                return '-';
            }

            // Security role shows full name, others show initial + date format
            if ($user->hasRole('Security')) {
                return $user->name;
            } else {
                return $user->initial.' '.strtoupper($this->created_at->format('d M Y'));
            }
        } else {
            // Old format: return existing signature string as-is
            return $picValue;
        }
    }
}
