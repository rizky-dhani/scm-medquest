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
        Schema::create('notification_settings', function (Blueprint $table) {
            $table->id();
            $table->string('event_key')->unique();
            $table->string('event_name');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('notification_setting_role', function (Blueprint $table) {
            $table->id();
            $table->foreignId('notification_setting_id')->constrained()->cascadeOnDelete();
            $table->foreignId('role_id')->constrained('roles')->cascadeOnDelete();
        });

        Schema::create('notification_setting_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('notification_setting_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notification_setting_user');
        Schema::dropIfExists('notification_setting_role');
        Schema::dropIfExists('notification_settings');
    }
};