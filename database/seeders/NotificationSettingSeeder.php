<?php

namespace Database\Seeders;

use App\Models\NotificationSetting;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class NotificationSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            [
                'event_key' => 'temperature_deviation',
                'event_name' => 'Temperature Deviation (Standard)',
                'description' => 'Sent when a standard temperature deviation is created.',
                'roles' => ['QA Manager', 'QA Supervisor', 'QA Staff', 'Supply Chain Manager'],
            ],
            [
                'event_key' => 'temperature_deviation_high_priority',
                'event_name' => 'Temperature Deviation (High Priority)',
                'description' => 'Sent when a high priority temperature deviation is created.',
                'roles' => ['Admin', 'Super Admin'],
            ],
            [
                'event_key' => 'temperature_humidity_review',
                'event_name' => 'Temperature & Humidity Review',
                'description' => 'Sent when temperature and humidity data is ready for review.',
                'roles' => ['Supply Chain Manager'],
            ],
            [
                'event_key' => 'temperature_humidity_acknowledge',
                'event_name' => 'Temperature & Humidity Acknowledgment',
                'description' => 'Sent when temperature and humidity data is ready for acknowledgment.',
                'roles' => ['QA Manager'],
            ],
        ];

        foreach ($settings as $item) {
            $setting = NotificationSetting::firstOrCreate(
                ['event_key' => $item['event_key']],
                [
                    'event_name' => $item['event_name'],
                    'description' => $item['description'],
                ]
            );

            foreach ($item['roles'] as $roleName) {
                $role = Role::where('name', $roleName)->first();
                if ($role) {
                    $setting->roles()->syncWithoutDetaching([$role->id]);
                }
            }
        }
    }
}