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
        Schema::create('notification_logs', function (Blueprint $table) {
            $table->id();
            $table->string('notification_type'); // e.g. 'temperature_deviation'
            $table->string('mailable_class'); // e.g. 'TemperatureDeviationNotification'
            $table->morphs('notifiable'); // Who received the notification (user, email, etc.)
            $table->string('recipient_email'); // The actual email address
            $table->json('data')->nullable(); // Additional data about the notification
            $table->string('status')->default('sent'); // sent, failed, pending
            $table->text('error_message')->nullable(); // If failed, store the error
            $table->timestamp('sent_at')->nullable(); // When the notification was sent
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notification_logs');
    }
};
