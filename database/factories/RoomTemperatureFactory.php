<?php

namespace Database\Factories;

use App\Models\Room;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\RoomTemperature>
 */
class RoomTemperatureFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'roomTemperatureId' => Str::orderedUuid(),
            'room_id' => Room::factory(),
            'temperature_start' => (string) fake()->numberBetween(2, 8),
            'temperature_end' => (string) fake()->numberBetween(20, 25),
        ];
    }
}
