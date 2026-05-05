<?php

use App\Http\Controllers\RentalController;
use Illuminate\Support\Facades\Route;

// Authenticated API routes
Route::middleware('auth')->group(function () {
    // Rental reports (specific routes first)
    Route::get('/rentals/reports/csv', [RentalController::class, 'generateCSV']);
    
    // Rental settings
    Route::get('/rentals/settings', [RentalController::class, 'getSettings']);
    Route::post('/rentals/settings', [RentalController::class, 'updateSettings']);
    
    // Rental release
    Route::post('/rentals/release', [RentalController::class, 'releaseItem']);
    
    // Bulk operations
    Route::post('/rentals/bulk-extend', [RentalController::class, 'bulkExtend']);
    Route::post('/rentals/bulk-return', [RentalController::class, 'bulkReturn']);
    Route::post('/rentals/batch-check-overdue', [RentalController::class, 'batchCheckOverdue']);
    
    // General rental endpoints
    Route::get('/rentals', [RentalController::class, 'index']);
    Route::get('/rentals/{rental}', [RentalController::class, 'show']);
    Route::post('/rentals/{rental}/return', [RentalController::class, 'processReturn']);
    Route::post('/rentals/{rental}/extend', [RentalController::class, 'extendRental']);
    Route::post('/rentals/{rental}/cancel', [RentalController::class, 'cancel']);
    Route::delete('/rentals/{rental}', [RentalController::class, 'destroy']);
});
