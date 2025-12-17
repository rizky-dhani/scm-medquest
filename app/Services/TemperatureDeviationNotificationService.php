<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\User;
use App\Models\TemperatureDeviation;
use App\Models\NotificationLog;
use App\Mail\TemperatureDeviationNotification;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

final class TemperatureDeviationNotificationService
{
    public function sendDeviationNotification(TemperatureDeviation $temperatureDeviation): void
    {
        $recipients = User::role(['QA Manager', 'Supply Chain Manager'])->get();

        if ($recipients->isEmpty()) {
            Log::warning('No users found to send temperature deviation notifications');
            $this->logNotification('temperature_deviation', $temperatureDeviation, null, false, 'No recipients found');
            return;
        }

        $successfulNotifications = 0;
        $failedNotifications = 0;

        foreach ($recipients as $user) {
            if ($user->email) {
                try {
                    Mail::to($user->email)->send(
                        new TemperatureDeviationNotification($temperatureDeviation)
                    );

                    // Log successful notification
                    $this->logNotification('temperature_deviation', $temperatureDeviation, $user, true);
                    $successfulNotifications++;
                } catch (\Exception $e) {
                    // Log failed notification
                    $this->logNotification('temperature_deviation', $temperatureDeviation, $user, false, $e->getMessage());
                    $failedNotifications++;
                }
            }
        }

        Log::info('Temperature deviation notifications processed', [
            'deviation_id' => $temperatureDeviation->id,
            'temperature_deviation' => $temperatureDeviation->temperature_deviation,
            'users_notified' => $successfulNotifications,
            'users_failed' => $failedNotifications,
            'total_tried' => $recipients->count(),
        ]);
    }

    public function sendHighPriorityDeviationNotification(TemperatureDeviation $temperatureDeviation): void
    {
        $highLevelManagers = User::role(['Admin', 'Super Admin'])->get();

        if ($highLevelManagers->isEmpty()) {
            Log::warning('No high-level managers found to send high priority temperature deviation notifications');
            $this->logNotification('high_priority_deviation', $temperatureDeviation, null, false, 'No high-level managers found');
            return;
        }

        $successfulNotifications = 0;
        $failedNotifications = 0;

        foreach ($highLevelManagers as $user) {
            if ($user->email) {
                try {
                    Mail::to($user->email)->send(
                        new TemperatureDeviationNotification($temperatureDeviation, true)
                    );

                    // Log successful notification
                    $this->logNotification('high_priority_deviation', $temperatureDeviation, $user, true);
                    $successfulNotifications++;
                } catch (\Exception $e) {
                    // Log failed notification
                    $this->logNotification('high_priority_deviation', $temperatureDeviation, $user, false, $e->getMessage());
                    $failedNotifications++;
                }
            }
        }

        Log::info('High priority temperature deviation notifications processed', [
            'deviation_id' => $temperatureDeviation->id,
            'temperature_deviation' => $temperatureDeviation->temperature_deviation,
            'users_notified' => $successfulNotifications,
            'users_failed' => $failedNotifications,
            'total_tried' => $highLevelManagers->count(),
        ]);
    }

    private function logNotification(
        string $notificationType,
        TemperatureDeviation $temperatureDeviation,
        ?User $user = null,
        bool $success,
        ?string $errorMessage = null
    ): void {
        $logData = [
            'notification_type' => $notificationType,
            'mailable_class' => TemperatureDeviationNotification::class,
            'recipient_email' => $user?->email,
            'data' => [
                'temperature_deviation_id' => $temperatureDeviation->id,
                'temperature_deviation_value' => $temperatureDeviation->temperature_deviation,
                'location_id' => $temperatureDeviation->location_id,
                'room_id' => $temperatureDeviation->room_id,
                'date' => $temperatureDeviation->date,
                'is_high_priority' => $notificationType === 'high_priority_deviation',
            ],
            'status' => $success ? 'sent' : 'failed',
            'error_message' => $errorMessage,
            'sent_at' => now(),
        ];

        if ($user) {
            $logData['notifiable_type'] = User::class;
            $logData['notifiable_id'] = $user->id;
        }

        NotificationLog::create($logData);
    }
}