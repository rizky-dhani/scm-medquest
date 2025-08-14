<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\User;
use App\Models\TemperatureHumidity;
use App\Mail\TemperatureHumidityBulkNotification;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

final class TemperatureHumidityNotificationService
{
    public function sendDailySummaryNotifications(): void
    {
        // Get count of data ready for review
        $reviewCount = TemperatureHumidity::query()
            ->where('is_reviewed', false)
            ->whereNotNull('time_0800')->whereNotNull('time_1100')
            ->whereNotNull('time_1400')->whereNotNull('time_1700')
            ->whereNotNull('time_2000')->whereNotNull('time_2300')
            ->whereNotNull('time_0200')->whereNotNull('time_0500')
            ->whereNotNull('temp_0800')->whereNotNull('temp_1100')
            ->whereNotNull('temp_1400')->whereNotNull('temp_1700')
            ->whereNotNull('temp_2000')->whereNotNull('temp_2300')
            ->whereNotNull('temp_0200')->whereNotNull('temp_0500')
            ->whereNotNull('rh_0800')->whereNotNull('rh_1100')
            ->whereNotNull('rh_1400')->whereNotNull('rh_1700')
            ->whereNotNull('rh_2000')->whereNotNull('rh_2300')
            ->whereNotNull('rh_0200')->whereNotNull('rh_0500')
            ->count();

        // Get count of data ready for acknowledgment
        $acknowledgeCount = TemperatureHumidity::query()
            ->where('is_reviewed', true)
            ->where('is_acknowledged', false)
            ->count();

        // Only send notifications if there's data to process
        if ($reviewCount > 0) {
            $this->sendBulkReviewNotification($reviewCount);
        } else {
            Log::info('No temperature humidity data ready for review - skipping notification');
        }

        if ($acknowledgeCount > 0) {
            $this->sendBulkAcknowledgmentNotification($acknowledgeCount);
        } else {
            Log::info('No temperature humidity data ready for acknowledgment - skipping notification');
        }

        // Log summary
        Log::info('Daily temperature humidity notification check completed', [
            'review_count' => $reviewCount,
            'acknowledge_count' => $acknowledgeCount,
            'notifications_sent' => ($reviewCount > 0 ? 1 : 0) + ($acknowledgeCount > 0 ? 1 : 0),
        ]);
    }

    private function sendBulkReviewNotification(int $count): void
    {
        try {
            $supplyChainManagers = User::role('Supply Chain Manager')->get();

            if ($supplyChainManagers->isEmpty()) {
                Log::warning('No Supply Chain Managers found to send review notifications');
                return;
            }

            foreach ($supplyChainManagers as $manager) {
                if ($manager->email) {
                    Mail::to($manager->email)->send(
                        new TemperatureHumidityBulkNotification($count, 'review')
                    );
                }
            }

            Log::info('Bulk review notifications sent', [
                'count' => $count,
                'managers_notified' => $supplyChainManagers->count(),
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send bulk review notifications', [
                'error' => $e->getMessage(),
                'count' => $count,
            ]);
        }
    }

    private function sendBulkAcknowledgmentNotification(int $count): void
    {
        try {
            $qaManagers = User::role('QA Manager')->get();

            if ($qaManagers->isEmpty()) {
                Log::warning('No QA Managers found to send acknowledgment notifications');
                return;
            }

            foreach ($qaManagers as $manager) {
                if ($manager->email) {
                    Mail::to($manager->email)->send(
                        new TemperatureHumidityBulkNotification($count, 'acknowledge')
                    );
                }
            }

            Log::info('Bulk acknowledgment notifications sent', [
                'count' => $count,
                'managers_notified' => $qaManagers->count(),
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send bulk acknowledgment notifications', [
                'error' => $e->getMessage(),
                'count' => $count,
            ]);
        }
    }

    public function checkAndSendNotifications(TemperatureHumidity $temperatureHumidity): void
    {
        if (!$this->areAllFieldsFilled($temperatureHumidity)) {
            return;
        }

        // Send review notification if not yet reviewed
        if (!$temperatureHumidity->is_reviewed) {
            Log::info('Temperature humidity data complete - ready for review', [
                'id' => $temperatureHumidity->id,
                'location' => $temperatureHumidity->location->location_name ?? 'Unknown',
                'date' => $temperatureHumidity->date,
            ]);
        }

        // Send acknowledgment notification if reviewed but not acknowledged
        if ($temperatureHumidity->is_reviewed && !$temperatureHumidity->is_acknowledged) {
            Log::info('Temperature humidity data reviewed - ready for acknowledgment', [
                'id' => $temperatureHumidity->id,
                'location' => $temperatureHumidity->location->location_name ?? 'Unknown',
                'date' => $temperatureHumidity->date,
            ]);
        }
    }

    private function areAllFieldsFilled(TemperatureHumidity $temperatureHumidity): bool
    {
        $requiredFields = [
            'time_0800', 'time_1100', 'time_1400', 'time_1700',
            'time_2000', 'time_2300', 'time_0200', 'time_0500',
            'temp_0800', 'temp_1100', 'temp_1400', 'temp_1700',
            'temp_2000', 'temp_2300', 'temp_0200', 'temp_0500',
            'rh_0800', 'rh_1100', 'rh_1400', 'rh_1700',
            'rh_2000', 'rh_2300', 'rh_0200', 'rh_0500'
        ];

        foreach ($requiredFields as $field) {
            if (is_null($temperatureHumidity->{$field})) {
                return false;
            }
        }

        return true;
    }
}
