<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * These FK constraints are set programmatically by the model's saving event handler,
     * never from user input. Dropping them eliminates 8 extra FK validation checks
     * on every UPDATE, which was causing 300s max_execution_timeouts on the
     * production Windows VM (Laragon + MariaDB + PHP-FPM).
     *
     * See: Model.php:2324 (getIncrementing) → performUpdate → locked by FK validation
     */
    public function up(): void
    {
        Schema::table('temperature_humidities', function (Blueprint $table) {
            $table->dropForeign(['pic_0200_id']);
            $table->dropForeign(['pic_0500_id']);
            $table->dropForeign(['pic_0800_id']);
            $table->dropForeign(['pic_1100_id']);
            $table->dropForeign(['pic_1400_id']);
            $table->dropForeign(['pic_1700_id']);
            $table->dropForeign(['pic_2000_id']);
            $table->dropForeign(['pic_2300_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('temperature_humidities', function (Blueprint $table) {
            $table->foreign('pic_0200_id')->references('id')->on('users');
            $table->foreign('pic_0500_id')->references('id')->on('users');
            $table->foreign('pic_0800_id')->references('id')->on('users');
            $table->foreign('pic_1100_id')->references('id')->on('users');
            $table->foreign('pic_1400_id')->references('id')->on('users');
            $table->foreign('pic_1700_id')->references('id')->on('users');
            $table->foreign('pic_2000_id')->references('id')->on('users');
            $table->foreign('pic_2300_id')->references('id')->on('users');
        });
    }
};
