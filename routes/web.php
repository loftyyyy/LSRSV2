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
});

