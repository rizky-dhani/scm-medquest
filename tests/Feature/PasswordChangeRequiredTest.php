<?php

use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

beforeEach(function () {
    // Create roles
    Role::create(['name' => 'Super Admin']);
    Role::create(['name' => 'Admin']);
    Role::create(['name' => 'User']);
});

test('newly created user requires password change', function () {
    // Create a user with password change required
    $user = User::create([
        'name' => 'Test User',
        'initial' => 'TU',
        'email' => 'test@medquest.co.id',
        'password' => Hash::make('Scm2025!'),
        'password_change_required' => true,
    ]);

    expect($user->requiresPasswordChange())->toBeTrue();
    expect($user->password_change_required)->toBeTrue();
});

test('user can mark password as changed', function () {
    // Create a user with password change required
    $user = User::create([
        'name' => 'Test User',
        'initial' => 'TU',
        'email' => 'test@medquest.co.id',
        'password' => Hash::make('Scm2025!'),
        'password_change_required' => true,
    ]);

    // Mark password as changed
    $user->markPasswordAsChanged();

    expect($user->fresh()->requiresPasswordChange())->toBeFalse();
    expect($user->fresh()->password_change_required)->toBeFalse();
    expect($user->fresh()->password_changed_at)->not->toBeNull();
});

test('middleware redirects user with password change required', function () {
    // Create a user with password change required
    $user = User::create([
        'name' => 'Test User',
        'initial' => 'TU',
        'email' => 'test@medquest.co.id',
        'password' => Hash::make('Scm2025!'),
        'password_change_required' => true,
    ]);

    // Attempt to access dashboard
    $response = $this->actingAs($user)->get('/');

    // Should be redirected to first-time password change page
    $response->assertRedirect(route('filament.dashboard.pages.first-time-password-change'));
    $response->assertSessionHas('password_change_required');
});

test('user without password change required can access dashboard', function () {
    // Create a user without password change required
    $user = User::create([
        'name' => 'Test User',
        'initial' => 'TU',
        'email' => 'test@medquest.co.id',
        'password' => Hash::make('NewPassword123!'),
        'password_change_required' => false,
    ]);

    $user->assignRole('Admin');

    // Should be able to access dashboard
    $response = $this->actingAs($user)->get('/');
    
    // Should not be redirected
    $response->assertOk();
});

test('user can access edit profile page when password change required', function () {
    // Create a user with password change required
    $user = User::create([
        'name' => 'Test User',
        'initial' => 'TU',
        'email' => 'test@medquest.co.id',
        'password' => Hash::make('Scm2025!'),
        'password_change_required' => true,
    ]);

    // Should be able to access edit profile page
    $response = $this->actingAs($user)->get(route('filament.dashboard.pages.edit-profile'));
    
    $response->assertOk();
});

test('admin can force password change for user', function () {
    // Create admin user
    $admin = User::create([
        'name' => 'Admin User',
        'initial' => 'AU',
        'email' => 'admin@medquest.co.id',
        'password' => Hash::make('AdminPass123!'),
        'password_change_required' => false,
    ]);
    $admin->assignRole('Admin');

    // Create regular user
    $user = User::create([
        'name' => 'Regular User',
        'initial' => 'RU',
        'email' => 'user@medquest.co.id',
        'password' => Hash::make('UserPass123!'),
        'password_change_required' => false,
    ]);
    $user->assignRole('User');

    expect($user->requiresPasswordChange())->toBeFalse();

    // Admin forces password change
    $user->update(['password_change_required' => true]);

    expect($user->fresh()->requiresPasswordChange())->toBeTrue();
});

test('password reset requires password change', function () {
    // Create user
    $user = User::create([
        'name' => 'Test User',
        'initial' => 'TU',
        'email' => 'test@medquest.co.id',
        'password' => Hash::make('OldPassword123!'),
        'password_change_required' => false,
    ]);

    expect($user->requiresPasswordChange())->toBeFalse();

    // Reset password (simulating admin action)
    $user->update([
        'password' => Hash::make('Scm2025!'),
        'password_change_required' => true,
    ]);

    expect($user->fresh()->requiresPasswordChange())->toBeTrue();
});
