<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Http\Controllers\RentalController;
protected function schedule(Schedule $schedule)
{
    // Check for overdue rentals every hour
    $schedule->call(function () {
        app(RentalController::class)->batchCheckOverdue();
    })->hourly();
}
