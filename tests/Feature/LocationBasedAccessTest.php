<?php

use App\Models\User;
use App\Models\Location;
use App\Models\TemperatureHumidity;
use App\Models\TemperatureDeviation;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

beforeEach(function () {
    // Create roles
    Role::create(['name' => 'Super Admin']);
    Role::create(['name' => 'Admin']);
    Role::create(['name' => 'User']);
    Role::create(['name' => 'Supply Chain Manager']);
    Role::create(['name' => 'QA Manager']);
    
    // Create locations
    $this->location1 = Location::create([
        'location_name' => 'Jakarta',
        'locationId' => 'JKT001'
    ]);
    
    $this->location2 = Location::create([
        'location_name' => 'Surabaya', 
        'locationId' => 'SBY001'
    ]);
});

test('admin user can see all temperature humidity records', function () {
    // Create admin user
    $admin = User::create([
        'name' => 'Admin User',
        'initial' => 'AU',
        'email' => 'admin@medquest.co.id',
        'password' => Hash::make('password'),
        'location_id' => null,
        'password_change_required' => false,
    ]);
    $admin->assignRole('Admin');

    // Create temperature humidity records for different locations
    $tempHumidity1 = TemperatureHumidity::create([
        'date' => now(),
        'period' => now(),
        'location_id' => $this->location1->id,
    ]);
    
    $tempHumidity2 = TemperatureHumidity::create([
        'date' => now(),
        'period' => now(),
        'location_id' => $this->location2->id,
    ]);

    // Admin should see all records
    $this->actingAs($admin);
    
    $accessibleIds = $admin->getAccessibleLocationIds();
    expect($accessibleIds)->toContain($this->location1->id, $this->location2->id);
    
    expect($admin->hasAccessToLocation($this->location1->id))->toBeTrue();
    expect($admin->hasAccessToLocation($this->location2->id))->toBeTrue();
});

test('location user can only see their location records', function () {
    // Create location-specific user
    $user = User::create([
        'name' => 'Location User',
        'initial' => 'LU',
        'email' => 'user@medquest.co.id',
        'password' => Hash::make('password'),
        'location_id' => $this->location1->id,
        'password_change_required' => false,
    ]);
    $user->assignRole('User');

    // Create temperature humidity records for different locations
    $tempHumidity1 = TemperatureHumidity::create([
        'date' => now(),
        'period' => now(),
        'location_id' => $this->location1->id,
    ]);
    
    $tempHumidity2 = TemperatureHumidity::create([
        'date' => now(),
        'period' => now(),
        'location_id' => $this->location2->id,
    ]);

    // User should only see their location's records
    $this->actingAs($user);
    
    $accessibleIds = $user->getAccessibleLocationIds();
    expect($accessibleIds)->toEqual([$this->location1->id]);
    expect($accessibleIds)->not->toContain($this->location2->id);
    
    expect($user->hasAccessToLocation($this->location1->id))->toBeTrue();
    expect($user->hasAccessToLocation($this->location2->id))->toBeFalse();
});

test('user without location cannot see any records', function () {
    // Create user without location
    $user = User::create([
        'name' => 'No Location User',
        'initial' => 'NL',
        'email' => 'nolocation@medquest.co.id',
        'password' => Hash::make('password'),
        'location_id' => null,
        'password_change_required' => false,
    ]);
    $user->assignRole('User');

    // User without location should see no records
    $this->actingAs($user);
    
    $accessibleIds = $user->getAccessibleLocationIds();
    expect($accessibleIds)->toBeEmpty();
    
    expect($user->hasAccessToLocation($this->location1->id))->toBeFalse();
    expect($user->hasAccessToLocation($this->location2->id))->toBeFalse();
});

test('temperature deviation filtering works for location users', function () {
    // Create location-specific user
    $user = User::create([
        'name' => 'Location User',
        'initial' => 'LU',
        'email' => 'user@medquest.co.id',
        'password' => Hash::make('password'),
        'location_id' => $this->location1->id,
        'password_change_required' => false,
    ]);
    $user->assignRole('User');

    // Create temperature deviation records for different locations
    $tempDeviation1 = TemperatureDeviation::create([
        'date' => now(),
        'time' => now(),
        'location_id' => $this->location1->id,
        'temperature_deviation' => 25.5,
    ]);
    
    $tempDeviation2 = TemperatureDeviation::create([
        'date' => now(),
        'time' => now(),
        'location_id' => $this->location2->id,
        'temperature_deviation' => 26.0,
    ]);

    // User should only access their location's records
    $this->actingAs($user);
    
    expect($user->hasAccessToLocation($this->location1->id))->toBeTrue();
    expect($user->hasAccessToLocation($this->location2->id))->toBeFalse();
});

test('super admin can access all locations', function () {
    // Create super admin user
    $superAdmin = User::create([
        'name' => 'Super Admin',
        'initial' => 'SA',
        'email' => 'superadmin@medquest.co.id',
        'password' => Hash::make('password'),
        'location_id' => $this->location1->id, // Even with assigned location
        'password_change_required' => false,
    ]);
    $superAdmin->assignRole('Super Admin');

    // Super Admin should see all records regardless of their assigned location
    $this->actingAs($superAdmin);
    
    $accessibleIds = $superAdmin->getAccessibleLocationIds();
    expect($accessibleIds)->toContain($this->location1->id, $this->location2->id);
    
    expect($superAdmin->hasAccessToLocation($this->location1->id))->toBeTrue();
    expect($superAdmin->hasAccessToLocation($this->location2->id))->toBeTrue();
});
