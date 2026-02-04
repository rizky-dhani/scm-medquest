<?php

use App\Models\Location;
use App\Models\Room;
use App\Models\SerialNumber;
use App\Models\RoomTemperature;
use App\Models\TemperatureDeviation;
use App\Models\User;
use App\Models\NotificationSetting;
use Illuminate\Support\Facades\Mail;
use Spatie\Permission\Models\Role;

test('all QA roles receive temperature deviation notification', function () {
    Mail::fake();

    // Ensure roles exist
    $qaManagerRole = Role::firstOrCreate(['name' => 'QA Manager']);
    $qaSupervisorRole = Role::firstOrCreate(['name' => 'QA Supervisor']);
    $qaStaffRole = Role::firstOrCreate(['name' => 'QA Staff']);
    $scmRole = Role::firstOrCreate(['name' => 'Supply Chain Manager']);

    // Create users with these roles
    $qaManager = User::factory()->create(['email' => 'manager@qa.com', 'is_active' => true]);
    $qaManager->assignRole($qaManagerRole);

    $qaSupervisor = User::factory()->create(['email' => 'supervisor@qa.com', 'is_active' => true]);
    $qaSupervisor->assignRole($qaSupervisorRole);

    $qaStaff = User::factory()->create(['email' => 'staff@qa.com', 'is_active' => true]);
    $qaStaff->assignRole($qaStaffRole);

    $scm = User::factory()->create(['email' => 'scm@test.com', 'is_active' => true]);
    $scm->assignRole($scmRole);

    // Ensure notification setting is configured correctly
    $setting = NotificationSetting::firstOrCreate(['event_key' => 'temperature_deviation'], [
        'event_name' => 'Temperature Deviation (Standard)',
        'description' => 'Test description'
    ]);
    $setting->roles()->sync([$qaManagerRole->id, $qaSupervisorRole->id, $qaStaffRole->id, $scmRole->id]);

    // Create a temperature deviation using factory
    $deviation = TemperatureDeviation::factory()->create([
        'location_id' => Location::factory(),
        'room_id' => Room::factory(),
        'serial_number_id' => SerialNumber::factory(),
        'room_temperature_id' => RoomTemperature::factory(),
    ]);

    // Call the service directly to avoid afterResponse issues in test
    app(App\Services\TemperatureDeviationNotificationService::class)
        ->sendDeviationNotification($deviation);

    // Check that emails were sent to all recipients
    Mail::assertSent(\App\Mail\TemperatureDeviationNotification::class, function ($mail) use ($qaManager) {
        return $mail->hasTo($qaManager->email);
    });

    Mail::assertSent(\App\Mail\TemperatureDeviationNotification::class, function ($mail) use ($qaSupervisor) {
        return $mail->hasTo($qaSupervisor->email);
    });

    Mail::assertSent(\App\Mail\TemperatureDeviationNotification::class, function ($mail) use ($qaStaff) {
        return $mail->hasTo($qaStaff->email);
    });

    Mail::assertSent(\App\Mail\TemperatureDeviationNotification::class, function ($mail) use ($scm) {
        return $mail->hasTo($scm->email);
    });
});
