<?php

use Illuminate\Support\Facades\Route;
use App\Mail\TemperatureHumidityBulkNotification;

// Email preview routes for development
Route::get('/email-preview/review', function () {
    $mailable = new TemperatureHumidityBulkNotification(5, 'review');
    return $mailable;
});

Route::get('/email-preview/acknowledgment', function () {
    $mailable = new TemperatureHumidityBulkNotification(3, 'acknowledgment');
    return $mailable;
});
