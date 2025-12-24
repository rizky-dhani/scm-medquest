<?php

?><?php

use App\Http\Controllers\Auth\LogoutController;
use App\Mail\TemperatureHumidityBulkNotification;
use App\Models\TemperatureHumidity;
use Carbon\Carbon;
use Illuminate\Support\Facades\Route;
use Spatie\Browsershot\Browsershot;

// Email preview routes for development
Route::get('/email-preview/review', function () {
    $mailable = new TemperatureHumidityBulkNotification(5, 'review');

    return $mailable;
});

Route::get('/email-preview/acknowledgment', function () {
    $mailable = new TemperatureHumidityBulkNotification(3, 'acknowledgment');

    return $mailable;
});

Route::get('/temperature-humidities/bulk-export', function () {
    $ids = session()->get('export_ids', []);
    $tempHumidity = TemperatureHumidity::whereIn('id', $ids)->get();

    if ($tempHumidity->isEmpty()) {
        return redirect()->back()->with('warning', 'No records selected for export.');
    }
    $period = strtoupper(Carbon::parse($tempHumidity->first()->period)->format('MY'));
    $filename = 'TemperatureHumidity_'.strtoupper(str_replace(' ', '_', $tempHumidity->first()->room->room_name)).'_'.$period.'.pdf';
    $html = view('print.temperature-humidity', compact('tempHumidity'));
    $pdf = Browsershot::html($html)
        ->format('A4')
        ->margins(3, 3, 3, 3)
        ->landscape()
        ->showBackground()
        ->pdf();

    return response($pdf, 200)
        ->header('Content-Type', 'application/pdf')
        ->header('Content-Disposition', 'inline; filename="'.$filename.'"');

})->name('temperature-humidities.bulk-export');

// Custom logout routes
Route::post('/logout/403', [LogoutController::class, 'logoutFrom403'])->name('logout.403');

Route::get('/pdf/temp-humidity', function () {
    return view('print.header.temperature-humidity');
});
Route::get('/pdf/temp-deviation', function () {
    return view('print.header.temperature-deviation');
});
Route::get('/pdf/temp-humidity-body', function () {
    return view('print.temperature-humidity');
});
