<?php

namespace Database\Seeders;

use Illuminate\Support\Str;
use App\Models\SerialNumber;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class SerialNumberSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        SerialNumber::create([
            'serialNumberId' => Str::orderedUuid(),
            'room_id' => 1,
            'serial_number' => 'Y835PR',
        ]);
        SerialNumber::create([
            'serialNumberId' => Str::orderedUuid(),
            'room_id' => 2,
            'serial_number' => 'T481CA',
        ]);
        SerialNumber::create([
            'serialNumberId' => Str::orderedUuid(),
            'room_id' => 3,
            'serial_number' => 'F14107',
        ]);
        SerialNumber::create([
            'serialNumberId' => Str::orderedUuid(),
            'room_id' => 4,
            'serial_number' => 'F11276',
        ]);
        SerialNumber::create([
            'serialNumberId' => Str::orderedUuid(),
            'room_id' => 5,
            'serial_number' => 'C804TS',
        ]);
        SerialNumber::create([
            'serialNumberId' => Str::orderedUuid(),
            'room_id' => 6,
            'serial_number' => 'W943GB',
        ]);
        SerialNumber::create([
            'serialNumberId' => Str::orderedUuid(),
            'room_id' => 7,
            'serial_number' => 'P530AL',
        ]);
        SerialNumber::create([
            'serialNumberId' => Str::orderedUuid(),
            'room_id' => 8,
            'serial_number' => 'B690EK',
        ]);
        SerialNumber::create([
            'serialNumberId' => Str::orderedUuid(),
            'room_id' => 9,
            'serial_number' => 'G357QD',
        ]);
        SerialNumber::create([
            'serialNumberId' => Str::orderedUuid(),
            'room_id' => 10,
            'serial_number' => 'J596VM',
        ]);
        SerialNumber::create([
            'serialNumberId' => Str::orderedUuid(),
            'room_id' => 11,
            'serial_number' => 'Q647TS',
        ]);
        SerialNumber::create([
            'serialNumberId' => Str::orderedUuid(),
            'room_id' => 12,
            'serial_number' => 'D279FF',
        ]);
        SerialNumber::create([
            'serialNumberId' => Str::orderedUuid(),
            'room_id' => 13,
            'serial_number' => '160618766',
        ]);
        SerialNumber::create([
            'serialNumberId' => Str::orderedUuid(),
            'room_id' => 14,
            'serial_number' => 'PX47102',
        ]);
        SerialNumber::create([
            'serialNumberId' => Str::orderedUuid(),
            'room_id' => 15,
            'serial_number' => 'F-16778',
        ]);
        SerialNumber::create([
            'serialNumberId' => Str::orderedUuid(),
            'room_id' => 16,
            'serial_number' => 'F-16779',
        ]);
        SerialNumber::create([
            'serialNumberId' => Str::orderedUuid(),
            'room_id' => 17,
            'serial_number' => 'M115HE',
        ]);
        SerialNumber::create([
            'serialNumberId' => Str::orderedUuid(),
            'room_id' => 18,
            'serial_number' => 'F-14404',
        ]);
        SerialNumber::create([
            'serialNumberId' => Str::orderedUuid(),
            'room_id' => 19,
            'serial_number' => 'F-14397',
        ]);
    }
}
