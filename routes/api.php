<?php

use App\Http\Controllers\RentalController;
use Illuminate\Support\Facades\Route;

// Authenticated API routes
Route::middleware('auth')->group(function () {
    // Rental Release
    Route::post('/rentals/release', [RentalController::class, 'releaseItem']);
});
