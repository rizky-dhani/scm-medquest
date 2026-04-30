<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class ResetSuperadminPasswordSeeder extends Seeder
{
    /**
     * Reset the Superadmin password.
     *
     * Finds the superadmin user by email and updates only the password field.
     * The 'hashed' cast on the User model will automatically hash the password on save.
     * This seeder does not touch any other data.
     */
    public function run(): void
    {
        $superadmin = User::where('email', 'superadmin@medquest.co.id')->first();

        if (! $superadmin) {
            $this->command->warn('Superadmin user not found. Run UserSeeder first.');

            return;
        }

        $superadmin->password = 'Superadmin2026!';
        $superadmin->save();

        $this->command->info('Superadmin password has been reset successfully.');
    }
}
