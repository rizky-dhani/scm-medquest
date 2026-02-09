<?php

use App\Models\Location;
use App\Models\Room;
use App\Models\RoomTemperature;
use App\Models\SerialNumber;
use App\Models\TemperatureHumidity;

test('temperature humidity print view has new columns', function () {
    // Create required related models
    $location = Location::factory()->create();
    $room = Room::factory()->for($location)->create();
    $serialNumber = SerialNumber::factory()->for($room)->create();
    $roomTemperature = RoomTemperature::factory()->create();

    $record = TemperatureHumidity::create([
        'temperatureId' => (string) \Illuminate\Support\Str::uuid(),
        'location_id' => $location->id,
        'room_id' => $room->id,
        'serial_number_id' => $serialNumber->id,
        'room_temperature_id' => $roomTemperature->id,
        'period' => now()->startOfMonth(),
        'date' => now()->format('Y-m-d'),
        'reviewed_by' => 'Reviewer Name',
        'acknowledged_by' => 'Acknowledger Name',
    ]);

    $groupedTempHumidity = collect([$room->id => collect([$record])]);

    $view = view('print.temperature-humidity', [
        'groupedTempHumidity' => $groupedTempHumidity,
    ])->render();

    expect($view)->toContain('Reviewed By')
        ->toContain('Acknowledged By')
        ->toContain('Reviewer Name')
        ->toContain('Acknowledger Name');
});
