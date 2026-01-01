<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Http\Controllers\RentalController;

    // Check for overdue rentals every hour
    Schedule::call(function () {
        app(RentalController::class)->batchCheckOverdue();
    })->hourly();
