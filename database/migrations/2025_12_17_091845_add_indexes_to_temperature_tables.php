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
        // Add composite index on temperature_deviations table for date and location_id, room_id
        Schema::table('temperature_deviations', function (Blueprint $table) {
            $table->index(['date', 'location_id', 'room_id'], 'temperature_deviations_date_location_room_index');
            $table->index(['temperature_deviation'], 'temperature_deviations_temp_deviation_index');
        });

        // Add composite index on temperature_humidities table for date and location_id, room_id
        Schema::table('temperature_humidities', function (Blueprint $table) {
            $table->index(['date', 'location_id', 'room_id'], 'temperature_humidities_date_location_room_index');
            $table->index(['period'], 'temperature_humidities_period_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('temperature_deviations', function (Blueprint $table) {
            $table->dropIndex(['temperature_deviations_date_location_room_index']);
            $table->dropIndex(['temperature_deviations_temp_deviation_index']);
        });

        Schema::table('temperature_humidities', function (Blueprint $table) {
            $table->dropIndex(['temperature_humidities_date_location_room_index']);
            $table->dropIndex(['temperature_humidities_period_index']);
        });
    }
};
