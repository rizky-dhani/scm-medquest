<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('room_temperatures', function (Blueprint $table) {
            $table->id();
            $table->uuid('roomTemperatureId')->unique();
            $table->foreignId('room_id')->constrained('rooms')->cascadeOnDelete()->cascadeOnUpdate();
            $table->tinyText('temperature_start');
            $table->tinyText('temperature_end');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('room_temperatures');
    }
};
