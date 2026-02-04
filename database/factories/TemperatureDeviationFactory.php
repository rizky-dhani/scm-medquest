<?php

namespace Database\Factories;

use App\Models\Location;
use App\Models\Room;
use App\Models\RoomTemperature;
use App\Models\SerialNumber;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TemperatureDeviation>
 */
class TemperatureDeviationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'temperatureDeviationId' => Str::orderedUuid(),
            'location_id' => Location::factory(),
            'room_id' => Room::factory(),
            'serial_number_id' => SerialNumber::factory(),
            'room_temperature_id' => RoomTemperature::factory(),
            'date' => now()->format('Y-m-d'),
            'time' => now()->format('H:i:s'),
            'temperature_deviation' => fake()->randomFloat(1, 1, 10),
            'deviation_reason' => fake()->sentence(),
            'length_temperature_deviation' => fake()->numberBetween(10, 120) . ' minutes',
            'pic' => fake()->name(),
        ];
    }
}
