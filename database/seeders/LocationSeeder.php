<?php

namespace Database\Seeders;

use App\Models\Location;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class LocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Location::create([
            'locationId' => Str::orderedUuid(),
            'location_name' => 'Bizpark 1',
        ]);
        Location::create([
            'locationId' => Str::orderedUuid(),
            'location_name' => 'Bizpark 2',
        ]);
    }
}
