<?php

use App\Models\TemperatureHumidity;
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
        Schema::create('temperature_deviations', function (Blueprint $table) {
            $table->id();
            $table->uuid('temperatureDeviationId')->unique()->nullable();
            $table->foreignId('temperature_humidity_id')->nullable()->constrained('temperature_humidities')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('location_id')->constrained('locations')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('room_id')->constrained('rooms')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('room_temperature_id')->constrained('room_temperatures')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('serial_number_id')->constrained('serial_numbers')->cascadeOnDelete()->cascadeOnUpdate();
            $table->date('date');
            $table->time('time');
            $table->decimal('temperature_deviation', 5, 2);
            $table->integer('length_temperature_deviation')->nullable();
            $table->string('deviation_reason')->nullable();
            $table->string('pic');
            $table->string('risk_analysis')->nullable();
            $table->string('analyzer_pic')->nullable();
            $table->boolean('is_reviewed')->default(false);
            $table->string('reviewed_by')->nullable();
            $table->dateTime('reviewed_at')->nullable();
            $table->boolean('is_acknowledged')->default(false);
            $table->string('acknowledged_by')->nullable();
            $table->dateTime('acknowledged_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('temperature_deviations');
    }
};
