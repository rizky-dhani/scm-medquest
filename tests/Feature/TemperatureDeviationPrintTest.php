<?php

use App\Models\TemperatureDeviation;
use App\Models\Location;
use App\Models\Room;
use App\Models\SerialNumber;
use App\Models\RoomTemperature;
use Illuminate\Support\Facades\View;

test('temperature deviation print view has new columns', function () {
    // Create required related models
    $location = Location::factory()->create();
    $room = Room::factory()->for($location)->create();
    $serialNumber = SerialNumber::factory()->for($room)->create();
    $roomTemperature = RoomTemperature::factory()->create();

    $deviation = TemperatureDeviation::factory()->create([
        'location_id' => $location->id,
        'room_id' => $room->id,
        'serial_number_id' => $serialNumber->id,
        'room_temperature_id' => $roomTemperature->id,
        'reviewed_by' => 'John Reviewer',
        'acknowledged_by' => 'Jane Acknowledger',
    ]);

    $groupedDeviations = collect([$room->id => collect([$deviation])]);

    $view = view('print.temperature-deviation', [
        'groupedDeviations' => $groupedDeviations,
    ])->render();

    expect($view)->toContain('Reviewed by')
        ->toContain('Acknowledged by')
        ->toContain('John Reviewer')
        ->toContain('Jane Acknowledger');
});
