<?php

use Illuminate\Support\Facades\Route;
use App\Mail\TemperatureHumidityBulkNotification;
use App\Http\Controllers\Auth\LogoutController;

// Email preview routes for development
Route::get('/email-preview/review', function () {
    $mailable = new TemperatureHumidityBulkNotification(5, 'review');
    return $mailable;
});

Route::get('/email-preview/acknowledgment', function () {
    $mailable = new TemperatureHumidityBulkNotification(3, 'acknowledgment');
    return $mailable;
});

// Custom logout routes
Route::post('/logout/403', [LogoutController::class, 'logoutFrom403'])->name('logout.403');
