<?php

namespace Database\Seeders;

use App\Models\Room;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class RoomSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Room::create([
            'roomId' => Str::orderedUuid(),
            'location_id' => 2,
            'room_name' => 'Gudang A35 Bizpark 2',
        ]);
        Room::create([
            'roomId' => Str::orderedUuid(),
            'location_id' => 2,
            'room_name' => 'Gudang 3 Bizpark 2',
        ]);
        Room::create([
            'roomId' => Str::orderedUuid(),
            'location_id' => 2,
            'room_name' => 'Gudang 2 Bizpark 2',
        ]);
        Room::create([
            'roomId' => Str::orderedUuid(),
            'location_id' => 2,
            'room_name' => 'Gudang 1 Bizpark 2',
        ]);
        Room::create([
            'roomId' => Str::orderedUuid(),
            'location_id' => 2,
            'room_name' => 'Ambient Quarantine Bizpark 2',
        ]);
        Room::create([
            'roomId' => Str::orderedUuid(),
            'location_id' => 2,
            'room_name' => 'Ambient D.3.E.3 Bizpark 2',
        ]);
        Room::create([
            'roomId' => Str::orderedUuid(),
            'location_id' => 2,
            'room_name' => 'Ambient B.1.A.2 Bizpark 2',
        ]);
        Room::create([
            'roomId' => Str::orderedUuid(),
            'location_id' => 1,
            'room_name' => 'Air Cond Storage 1',
        ]);
        Room::create([
            'roomId' => Str::orderedUuid(),
            'location_id' => 1,
            'room_name' => 'Air Cond Storage 2',
        ]);
        Room::create([
            'roomId' => Str::orderedUuid(),
            'location_id' => 1,
            'room_name' => 'Air Cond Storage 3',
        ]);
        Room::create([
            'roomId' => Str::orderedUuid(),
            'location_id' => 1,
            'room_name' => 'Cold Storage 1',
        ]);
        Room::create([
            'roomId' => Str::orderedUuid(),
            'location_id' => 1,
            'room_name' => 'Cold Storage 2',
        ]);
        Room::create([
            'roomId' => Str::orderedUuid(),
            'location_id' => 1,
            'room_name' => 'Cold Storage 3',
        ]);
        Room::create([
            'roomId' => Str::orderedUuid(),
            'location_id' => 1,
            'room_name' => 'Cold Storage 4',
        ]);
        Room::create([
            'roomId' => Str::orderedUuid(),
            'location_id' => 1,
            'room_name' => 'Freezer 1',
        ]);
        Room::create([
            'roomId' => Str::orderedUuid(),
            'location_id' => 1,
            'room_name' => 'Freezer Storage',
        ]);
        Room::create([
            'roomId' => Str::orderedUuid(),
            'location_id' => 1,
            'room_name' => 'Refrigerator 1',
        ]);
        Room::create([
            'roomId' => Str::orderedUuid(),
            'location_id' => 1,
            'room_name' => 'Freezer 6',
        ]);
        Room::create([
            'roomId' => Str::orderedUuid(),
            'location_id' => 1,
            'room_name' => 'Freezer Packaging Storage',
        ]);
        
    }
}
