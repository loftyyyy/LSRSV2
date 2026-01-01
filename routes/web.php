<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\RentalController;
use App\Http\Controllers\ReservationController;
use Illuminate\Support\Facades\Route;

Route::get('/landing', function () {
    return view('welcome');
});

Route::get('/register', [AuthController::class,  'showRegisterForm'])->name('register');
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'showDashboardPage'])->name('dashboard');
    Route::get('/customers', [CustomerController::class, 'showCustomerPage'])->name('customers');
    Route::get('/inventories', [InventoryController::class, 'showInventoryPage'])->name('inventories');
    Route::get('/reservations', [ReservationController::class, 'showReservationPage'])->name('reservations');
    Route::get('/rentals', [RentalController::class, 'showRentalPage'])->name('rentals');
    Route::get('/invoices', [InvoiceController::class, 'showInvoicePage'])->name('invoices');
    Route::get('/payments', [PaymentController::class, 'showPaymentPage'])->name('payments');

    // Internal API endpoints (session-authenticated)
    Route::prefix('api')->group(function () {

        // Customer CRUD
        Route::get('/customers', [CustomerController::class, 'index']);
        Route::post('/customers', [CustomerController::class, 'store']);
        Route::get('/customers/{customer}', [CustomerController::class, 'show']);
        Route::put('/customers/{customer}', [CustomerController::class, 'update']);
        Route::delete('/customers/{customer}', [CustomerController::class, 'destroy']);

        // Customer Additional Actions
        Route::post('/customers/{customer}/deactivate', [CustomerController::class, 'deactivate']);
        Route::post('/customers/{customer}/reactivate', [CustomerController::class, 'reactivate']);
        Route::get('/customers/{customer}/rental-history', [CustomerController::class, 'rentalHistory']);

        // Customer Reports
        Route::get('/customers/reports/generate', [CustomerController::class, 'report']);
        Route::get('/customers/reports/pdf', [CustomerController::class, 'generatePDF']);


        // Inventory CRUD
        Route::get('/inventories', [InventoryController::class, 'index']);
        Route::post('/inventories', [InventoryController::class, 'store']);
        Route::get('/inventories/{id}', [InventoryController::class, 'show']);
        Route::put('/inventories/{id}', [InventoryController::class, 'update']);
        Route::delete('/inventories/{id}', [InventoryController::class, 'destroy']);

        // Invoice CRUD
        Route::get('/invoices', [InvoiceController::class, 'index']);
        Route::post('/invoices', [InvoiceController::class, 'store']);
        Route::get('/invoices/{id}', [InvoiceController::class, 'show']);
        Route::put('/invoices/{id}', [InvoiceController::class, 'update']);
        Route::delete('/invoices/{id}', [InvoiceController::class, 'destroy']);

        // Payment CRUD
        Route::get('/payments', [PaymentController::class, 'index']);
        Route::post('/payments', [PaymentController::class, 'store']);
        Route::get('/payments/{id}', [PaymentController::class, 'show']);
        Route::put('/payments/{id}', [PaymentController::class, 'update']);
        Route::delete('/payments/{id}', [PaymentController::class, 'destroy']);

        // Rental CRUD
        Route::get('/rentals', [RentalController::class, 'index']);
        Route::post('/rentals', [RentalController::class, 'store']);
        Route::get('/rentals/{id}', [RentalController::class, 'show']);
        Route::put('/rentals/{id}', [RentalController::class, 'update']);
        Route::delete('/rentals/{id}', [RentalController::class, 'destroy']);

        // Reservation CRUD
        Route::get('/reservations', [ReservationController::class, 'index']);
        Route::post('/reservations', [ReservationController::class, 'store']);
        Route::get('/reservations/{id}', [ReservationController::class, 'show']);
        Route::put('/reservations/{id}', [ReservationController::class, 'update']);
        Route::delete('/reservations/{id}', [ReservationController::class, 'destroy']);
    });
});
