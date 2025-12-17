<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Services\TemperatureDeviationNotificationService;

class TemperatureDeviationNotificationServiceTest extends TestCase
{
    public function test_handle_method_exists()
    {
        $service = new TemperatureDeviationNotificationService();
        $this->assertTrue(method_exists($service, 'handle'));
    }
}