<?php

use App\Mail\TemperatureHumidityBulkNotification;
use App\Models\User;
use App\Models\TemperatureHumidity;
use App\Models\Location;
use App\Services\TemperatureHumidityNotificationService;
use Illuminate\Support\Facades\Mail;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    // Create roles
    Role::create(['name' => 'Supply Chain Manager']);
    Role::create(['name' => 'QA Manager']);
});

test('sends review notification when data exists', function () {
    Mail::fake();

    // Create a Supply Chain Manager
    $user = User::factory()->create();
    $user->assignRole('Supply Chain Manager');

    // Create a location
    $location = Location::factory()->create();

    // Create temperature humidity data that needs review
    TemperatureHumidity::factory()->create([
        'location_id' => $location->id,
        'is_reviewed' => false,
        'is_acknowledged' => false,
        'time_0800' => '08:00',
        'temp_0800' => 25.0,
        'rh_0800' => 60.0,
        'time_1100' => '11:00',
        'temp_1100' => 26.0,
        'rh_1100' => 62.0,
        // Fill remaining required fields
        'time_1400' => '14:00',
        'temp_1400' => 27.0,
        'rh_1400' => 58.0,
        'time_1700' => '17:00',
        'temp_1700' => 26.5,
        'rh_1700' => 60.5,
        'time_2000' => '20:00',
        'temp_2000' => 25.5,
        'rh_2000' => 61.0,
        'time_2300' => '23:00',
        'temp_2300' => 24.0,
        'rh_2300' => 62.5,
        'time_0200' => '02:00',
        'temp_0200' => 23.5,
        'rh_0200' => 63.0,
        'time_0500' => '05:00',
        'temp_0500' => 24.5,
        'rh_0500' => 61.5,
    ]);

    $service = app(TemperatureHumidityNotificationService::class);
    $service->sendDailySummaryNotifications();

    // Assert email was sent
    Mail::assertSent(TemperatureHumidityBulkNotification::class, function ($mail) use ($user) {
        return $mail->hasTo($user->email) && $mail->type === 'review';
    });
});

test('sends acknowledgment notification when data exists', function () {
    Mail::fake();

    // Create a QA Manager
    $user = User::factory()->create();
    $user->assignRole('QA Manager');

    // Create a location
    $location = Location::factory()->create();

    // Create temperature humidity data that needs acknowledgment
    TemperatureHumidity::factory()->create([
        'location_id' => $location->id,
        'is_reviewed' => true,
        'is_acknowledged' => false,
        'time_0800' => '08:00',
        'temp_0800' => 25.0,
        'rh_0800' => 60.0,
        'time_1100' => '11:00',
        'temp_1100' => 26.0,
        'rh_1100' => 62.0,
        // Fill remaining required fields
        'time_1400' => '14:00',
        'temp_1400' => 27.0,
        'rh_1400' => 58.0,
        'time_1700' => '17:00',
        'temp_1700' => 26.5,
        'rh_1700' => 60.5,
        'time_2000' => '20:00',
        'temp_2000' => 25.5,
        'rh_2000' => 61.0,
        'time_2300' => '23:00',
        'temp_2300' => 24.0,
        'rh_2300' => 62.5,
        'time_0200' => '02:00',
        'temp_0200' => 23.5,
        'rh_0200' => 63.0,
        'time_0500' => '05:00',
        'temp_0500' => 24.5,
        'rh_0500' => 61.5,
    ]);

    $service = app(TemperatureHumidityNotificationService::class);
    $service->sendDailySummaryNotifications();

    // Assert email was sent
    Mail::assertSent(TemperatureHumidityBulkNotification::class, function ($mail) use ($user) {
        return $mail->hasTo($user->email) && $mail->type === 'acknowledgment';
    });
});

test('does not send notification when no data exists', function () {
    Mail::fake();

    // Create managers but no data
    $scManager = User::factory()->create();
    $scManager->assignRole('Supply Chain Manager');
    
    $qaManager = User::factory()->create();
    $qaManager->assignRole('QA Manager');

    $service = app(TemperatureHumidityNotificationService::class);
    $service->sendDailySummaryNotifications();

    // Assert no emails were sent
    Mail::assertNotSent(TemperatureHumidityBulkNotification::class);
});
