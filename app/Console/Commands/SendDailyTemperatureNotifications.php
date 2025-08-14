<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\TemperatureHumidityNotificationService;

final class SendDailyTemperatureNotifications extends Command
{
    protected $signature = 'temperature:send-daily-notifications';
    protected $description = 'Send daily summary notifications for temperature humidity data that needs review/acknowledgment';

    public function handle(TemperatureHumidityNotificationService $notificationService): int
    {
        $this->info('Sending daily temperature humidity notifications...');
        
        $notificationService->sendDailySummaryNotifications();
        
        $this->info('Daily notifications sent successfully!');
        
        return Command::SUCCESS;
    }
}
