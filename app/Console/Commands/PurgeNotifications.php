<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PurgeNotifications extends Command
{
    protected $signature = 'notifications:purge 
                            {--days=30 : Number of days to keep notifications}
                            {--filament : Purge Filament notifications table}
                            {--logs : Purge notification logs table}
                            {--all : Purge both tables}';

    protected $description = 'Purge old notifications from database';

    public function handle(): int
    {
        $days = (int) $this->option('days');
        $purgeFilament = $this->option('all') || $this->option('filament');
        $purgeLogs = $this->option('all') || $this->option('logs');

        if (!$purgeFilament && !$purgeLogs) {
            $this->error('Please specify --filament, --logs, or --all');
            return Command::FAILURE;
        }

        $this->info("Purging notifications older than {$days} days...");

        $cutoffDate = now()->subDays($days);

        $purgedCount = 0;

        if ($purgeFilament) {
            $filamentCount = DB::table('notifications')
                ->where('created_at', '<', $cutoffDate)
                ->delete();

            $purgedCount += $filamentCount;
            $this->info("Purged {$filamentCount} Filament notifications");
        }

        if ($purgeLogs) {
            $logsCount = DB::table('notification_logs')
                ->where('created_at', '<', $cutoffDate)
                ->delete();

            $purgedCount += $logsCount;
            $this->info("Purged {$logsCount} notification logs");
        }

        Log::info("Purged {$purgedCount} notifications older than {$days} days");

        $this->info("Total records purged: {$purgedCount}");

        return Command::SUCCESS;
    }
}
