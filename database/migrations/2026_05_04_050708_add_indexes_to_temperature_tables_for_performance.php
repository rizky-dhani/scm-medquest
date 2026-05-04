<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Composite index for the afterStateUpdated callback:
        // TemperatureHumidity::where('room_id', $state)->whereDate('date', today())->exists()
        Schema::table('temperature_humidities', function (Blueprint $table) {
            $table->index(['room_id', 'date'], 'idx_th_room_id_date');
        });

        // Composite index for the table column deviation lookups:
        // $record->temperatureDeviations()->where('time', $time)->where('temperature_deviation', $temp)->first()
        Schema::table('temperature_deviations', function (Blueprint $table) {
            $table->index(['temperature_humidity_id', 'time', 'temperature_deviation'], 'idx_td_lookup');
        });
    }

    public function down(): void
    {
        Schema::table('temperature_humidities', function (Blueprint $table) {
            $table->dropIndex('idx_th_room_id_date');
        });

        Schema::table('temperature_deviations', function (Blueprint $table) {
            $table->dropIndex('idx_td_lookup');
        });
    }
};
