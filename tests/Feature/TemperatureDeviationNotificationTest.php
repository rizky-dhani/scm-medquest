<?php

use App\Models\Location;
use App\Models\Room;
use App\Models\SerialNumber;
use App\Models\RoomTemperature;
use App\Models\TemperatureDeviation;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;

test('temperature deviation creates notification', function () {
    Mail::fake();

    // Create required related models
    $location = Location::factory()->create();
    $room = Room::factory()->for($location)->create();
    $serialNumber = SerialNumber::factory()->for($room)->create();
    $roomTemperature = RoomTemperature::factory()->create();

    // Create a temperature deviation
    $deviation = TemperatureDeviation::create([
        'location_id' => $location->id,
        'room_id' => $room->id,
        'serial_number_id' => $serialNumber->id,
        'room_temperature_id' => $roomTemperature->id,
        'date' => now(),
        'time' => now()->toTimeString(),
        'temperature_deviation' => 5.5,
        'deviation_reason' => 'Test reason',
        'length_temperature_deviation' => '30 minutes'
    ]);

    // Assert that the deviation was created successfully
    $this->assertNotNull($deviation->id);

    // Check that the email notification was sent
    Mail::assertQueued(\App\Mail\TemperatureDeviationNotification::class);
});
