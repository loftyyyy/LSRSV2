<?php

use App\Http\Controllers\RentalController;
use Illuminate\Support\Facades\Schedule;

// Check for overdue rentals every hour
Schedule::call(function () {
    app(RentalController::class)->batchCheckOverdue();
})->hourly();

// Check and create rental notifications daily at 8 AM
Schedule::command('rentals:check-notifications')
    ->dailyAt('08:00')
    ->withoutOverlapping()
    ->runInBackground();
