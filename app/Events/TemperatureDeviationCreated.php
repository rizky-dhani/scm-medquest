<?php

namespace App\Events;

use App\Models\TemperatureDeviation;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TemperatureDeviationCreated
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public TemperatureDeviation $temperatureDeviation
    ) {}
}
