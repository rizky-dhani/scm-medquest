<?php

namespace Database\Seeders;

use Illuminate\Support\Str;
use App\Models\RoomTemperature;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class RoomTemperatureSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        RoomTemperature::create([
            'roomTemperatureId' => Str::orderedUuid(),
            'room_id' => 1,
            'temperature_start' => 15,
            'temperature_end' => 30,
        ]);
        RoomTemperature::create([
            'roomTemperatureId' => Str::orderedUuid(),
            'room_id' => 2,
            'temperature_start' => 15,
            'temperature_end' => 25,
        ]);
        RoomTemperature::create([
            'roomTemperatureId' => Str::orderedUuid(),
            'room_id' => 3,
            'temperature_start' => 15,
            'temperature_end' => 25,
        ]);
        
        RoomTemperature::create([
            'roomTemperatureId' => Str::orderedUuid(),
            'room_id' => 4,
            'temperature_start' => 15,
            'temperature_end' => 25,
        ]);
        RoomTemperature::create([
            'roomTemperatureId' => Str::orderedUuid(),
            'room_id' => 5,
            'temperature_start' => 15,
            'temperature_end' => 25,
        ]);
        RoomTemperature::create([
            'roomTemperatureId' => Str::orderedUuid(),
            'room_id' => 6,
            'temperature_start' => 15,
            'temperature_end' => 25,
        ]);
        RoomTemperature::create([
            'roomTemperatureId' => Str::orderedUuid(),
            'room_id' => 7,
            'temperature_start' => 15,
            'temperature_end' => 25,
        ]);
        RoomTemperature::create([
            'roomTemperatureId' => Str::orderedUuid(),
            'room_id' => 8,
            'temperature_start' => 15,
            'temperature_end' => 25,
        ]);
        RoomTemperature::create([
            'roomTemperatureId' => Str::orderedUuid(),
            'room_id' => 9,
            'temperature_start' => 15,
            'temperature_end' => 30,
        ]);
        RoomTemperature::create([
            'roomTemperatureId' => Str::orderedUuid(),
            'room_id' => 10,
            'temperature_start' => 15,
            'temperature_end' => 30,
        ]);
        RoomTemperature::create([
            'roomTemperatureId' => Str::orderedUuid(),
            'room_id' => 11,
            'temperature_start' => 2,
            'temperature_end' => 8,
        ]);
        RoomTemperature::create([
            'roomTemperatureId' => Str::orderedUuid(),
            'room_id' => 12,
            'temperature_start' => 2,
            'temperature_end' => 8,
        ]);
        RoomTemperature::create([
            'roomTemperatureId' => Str::orderedUuid(),
            'room_id' => 13,
            'temperature_start' => 2,
            'temperature_end' => 8,
        ]);
        RoomTemperature::create([
            'roomTemperatureId' => Str::orderedUuid(),
            'room_id' => 14,
            'temperature_start' => 2,
            'temperature_end' => 8,
        ]);
        RoomTemperature::create([
            'roomTemperatureId' => Str::orderedUuid(),
            'room_id' => 15,
            'temperature_start' => -35,
            'temperature_end' => -15,
        ]);
        RoomTemperature::create([
            'roomTemperatureId' => Str::orderedUuid(),
            'room_id' => 16,
            'temperature_start' => -35,
            'temperature_end' => -15,
        ]);
        RoomTemperature::create([
            'roomTemperatureId' => Str::orderedUuid(),
            'room_id' => 17,
            'temperature_start' => 2,
            'temperature_end' => 8,
        ]);
        RoomTemperature::create([
            'roomTemperatureId' => Str::orderedUuid(),
            'room_id' => 18,
            'temperature_start' => -25,
            'temperature_end' => -10,
        ]);
        RoomTemperature::create([
            'roomTemperatureId' => Str::orderedUuid(),
            'room_id' => 19,
            'temperature_start' => -25,
            'temperature_end' => -10,
        ]);
    }
}
