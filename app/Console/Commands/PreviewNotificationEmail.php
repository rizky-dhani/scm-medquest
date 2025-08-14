<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Mail\TemperatureHumidityBulkNotification;
use App\Models\User;

final class PreviewNotificationEmail extends Command
{
    protected $signature = 'temperature:preview-email {type=review : Type of notification (review or acknowledgment)}';
    protected $description = 'Preview the notification email template';

    public function handle(): int
    {
        $type = $this->argument('type');
        
        if (!in_array($type, ['review', 'acknowledgment'])) {
            $this->error('Type must be either "review" or "acknowledgment"');
            return Command::FAILURE;
        }

        // Create the mailable with sample data
        $mailable = new TemperatureHumidityBulkNotification(5, $type);

        // Render the email content
        $emailContent = $mailable->render();

        $this->info("Email Preview for {$type} notification:");
        $this->line('');
        $this->line('Subject: ' . $mailable->envelope()->subject);
        $this->line('');
        $this->line('HTML Content:');
        $this->line('=====================================');
        $this->line($emailContent);
        $this->line('=====================================');

        return Command::SUCCESS;
    }
}
