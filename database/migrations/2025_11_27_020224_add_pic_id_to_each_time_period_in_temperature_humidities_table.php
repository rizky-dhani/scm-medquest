<?php

use App\Models\User;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('temperature_humidities', function (Blueprint $table) {
            $table->foreignIdFor(User::class, 'pic_0200_id')->nullable()->after('pic_0200')->constrained('users');
            $table->foreignIdFor(User::class, 'pic_0500_id')->nullable()->after('pic_0500')->constrained('users');
            $table->foreignIdFor(User::class, 'pic_0800_id')->nullable()->after('pic_0800')->constrained('users');
            $table->foreignIdFor(User::class, 'pic_1100_id')->nullable()->after('pic_1100')->constrained('users');
            $table->foreignIdFor(User::class, 'pic_1400_id')->nullable()->after('pic_1400')->constrained('users');
            $table->foreignIdFor(User::class, 'pic_1700_id')->nullable()->after('pic_1700')->constrained('users');
            $table->foreignIdFor(User::class, 'pic_2000_id')->nullable()->after('pic_2000')->constrained('users');
            $table->foreignIdFor(User::class, 'pic_2300_id')->nullable()->after('pic_2300')->constrained('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('temperature_humidities', function (Blueprint $table) {
            //
        });
    }
};
