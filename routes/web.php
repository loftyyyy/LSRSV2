<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\InventoryImageController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\RentalController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\OtpController;
use Illuminate\Support\Facades\Route;

Route::middleware(['guest'])->group(function () {

    Route::get('/register', [AuthController::class,  'showRegisterForm'])->name('register');
    Route::get('/', [AuthController::class, 'showLoginForm'])->name('loginForm');
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('loginForm');
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login'])->name('login');

    Route::prefix('otp')->group(function () {
        Route::post('/generate-otp', [OtpController::class, 'generateOtp']);
        Route::post('/verify-otp', [OtpController::class, 'verifyOtp']);
        Route::post('/resend-otp', [OtpController::class, 'resendOtp']);
        Route::post('/delete-otp', [OtpController::class, 'deleteOtp']);
        Route::post('/reset-password', [AuthController::class, 'resetPassword']);
    });

});



  Route::middleware('auth')->group(function () {
      Route::get('/dashboard', [DashboardController::class, 'showDashboardPage'])->name('dashboard');
      Route::get('/customers', [CustomerController::class, 'showCustomerPage'])->name('customers');
      Route::get('/customers/reports', [CustomerController::class, 'showReportsPage'])->name('customers.reports');
       Route::get('/inventories', [InventoryController::class, 'showInventoryPage'])->name('inventories');
       Route::get('/inventories/reports', [InventoryController::class, 'showReportsPage'])->name('inventories.reports');
       Route::get('/reservations', [ReservationController::class, 'showReservationPage'])->name('reservations');
       Route::get('/rentals', [RentalController::class, 'showRentalPage'])->name('rentals');
       Route::get('/rentals/reports', [RentalController::class, 'showReportsPage'])->name('rentals.reports');
       Route::get('/invoices', [InvoiceController::class, 'showInvoicePage'])->name('invoices');
       Route::get('/payments', [PaymentController::class, 'showPaymentPage'])->name('payments');

     Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Internal API endpoints (session-authenticated)
    Route::prefix('api')->group(function () {

        // ============================================
        // DASHBOARD ROUTES
        // ============================================
        Route::get('/dashboard/metrics', [DashboardController::class, 'getMetrics']);

        // ============================================
        // AUTH ROUTES
        // ============================================
        Route::post('/verify-password', [AuthController::class, 'verifyPassword'])->middleware('throttle:5,15');

        // ============================================
        // CUSTOMER ROUTES
        // ============================================

         // Customer Reports
          Route::get('/customers/reports/generate', [CustomerController::class, 'report']);
          Route::get('/customers/reports/pdf', [CustomerController::class, 'generatePDF']);
          Route::get('/customers/reports/registration-trend', [CustomerController::class, 'getRegistrationTrend']);

         // Customer Stats
        Route::get('/customers/stats', [CustomerController::class, 'stats']);
        Route::get('/customers/statuses', [CustomerController::class, 'statuses']);

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


        // ============================================
        // INVENTORY ROUTES
        // ============================================

         // Inventory Reports
         Route::get('/inventories/reports/generate', [InventoryController::class, 'report']);
         Route::get('/inventories/reports/pdf', [InventoryController::class, 'generatePDF']);
         Route::get('/inventories/reports/statistics', [InventoryController::class, 'getStatistics']);
         Route::get('/inventories/reports/metrics', [InventoryController::class, 'getMetrics']);

        // Inventory Available Items (before CRUD to avoid conflicts)
        Route::get('/inventories/available', [InventoryController::class, 'getAvailableItems']);

        // Inventory Bulk Operations
        Route::put('/inventories/update/bulk', [InventoryController::class, 'bulkUpdateStatus']);

        // Inventory CRUD
        Route::get('/inventories', [InventoryController::class, 'index']);
        Route::post('/inventories', [InventoryController::class, 'store']);
        Route::get('/inventories/{inventory}', [InventoryController::class, 'show']);
        Route::put('/inventories/{inventory}', [InventoryController::class, 'update']);
        Route::delete('/inventories/{inventory}', [InventoryController::class, 'destroy']);

        // Inventory Additional Actions
        Route::get('/inventories/{inventory}/availability', [InventoryController::class, 'checkAvailability']);
        Route::put('/inventories/{inventory}/status', [InventoryController::class, 'updateStatus']);
        Route::put('/inventories/{inventory}/condition', [InventoryController::class, 'updateCondition']);

        // Inventory Image
        Route::get('/inventories/missing-images', [InventoryController::class, 'getItemsMissingImages']);
        Route::get('/{inventory}/images', [InventoryImageController::class, 'index']);



        // ============================================
        // RESERVATION ROUTES
        // ============================================

        // Reservation Reports
        Route::get('/reservations/reports/generate', [ReservationController::class, 'report']);
        Route::get('/reservations/reports/pdf', [ReservationController::class, 'generatePDF']);

        // Browse & Check Available Items
        Route::get('/reservations/items/browse', [ReservationController::class, 'browseAvailableItems']);
        Route::get('/reservations/items/{itemId}/details', [ReservationController::class, 'checkItemDetails']);

        // Reservation CRUD
        Route::get('/reservations', [ReservationController::class, 'index']);
        Route::post('/reservations', [ReservationController::class, 'store']);
        Route::get('/reservations/{reservation}', [ReservationController::class, 'show']);
        Route::put('/reservations/{reservation}', [ReservationController::class, 'update']);
        Route::delete('/reservations/{reservation}', [ReservationController::class, 'destroy']);

        // Reservation Actions
        Route::post('/reservations/{reservation}/confirm', [ReservationController::class, 'confirmReservation']);
        Route::post('/reservations/{reservation}/cancel', [ReservationController::class, 'cancelReservation']);


        // ============================================
        // RENTAL ROUTES
        // ============================================

         // Rental Reports
         Route::get('/rentals/reports/generate', [RentalController::class, 'report']);
         Route::get('/rentals/reports/pdf', [RentalController::class, 'generatePDF']);
         Route::get('/rentals/reports/metrics', [RentalController::class, 'getMetrics']);

        // Rental Batch Operations
        Route::post('/rentals/batch/check-overdue', [RentalController::class, 'batchCheckOverdue']);

        // Rental Overdue List
        Route::get('/rentals/overdue/list', [RentalController::class, 'getOverdueRentals']);

        // Rental History
        Route::get('/rentals/customer/{customerId}/history', [RentalController::class, 'customerHistory']);
        Route::get('/rentals/item/{itemId}/history', [RentalController::class, 'itemHistory']);

        // Rental Release (before CRUD to avoid conflicts)
        Route::post('/rentals/release', [RentalController::class, 'releaseItem']);

        // Rental CRUD
        Route::get('/rentals', [RentalController::class, 'index']);
        Route::post('/rentals', [RentalController::class, 'store']);
        Route::get('/rentals/{rental}', [RentalController::class, 'show']);
        Route::put('/rentals/{rental}', [RentalController::class, 'update']);
        Route::delete('/rentals/{rental}', [RentalController::class, 'destroy']);

        // Rental Actions
        Route::post('/rentals/{rental}/return', [RentalController::class, 'processReturn']);
        Route::post('/rentals/{rental}/extend', [RentalController::class, 'extendRental']);
        Route::post('/rentals/{rental}/cancel', [RentalController::class, 'cancel']);
        Route::post('/rentals/{rental}/check-overdue', [RentalController::class, 'checkOverdue']);


        // ============================================
        // INVOICE ROUTES
        // ============================================

        // Invoice Reports
        Route::get('/invoices/reports/generate', [InvoiceController::class, 'report']);
        Route::get('/invoices/reports/pdf', [InvoiceController::class, 'generatePDF']);
        Route::get('/invoices/reports/invoice/{invoice}', [InvoiceController::class, 'generateInvoicePDF']);

        // Invoice Details & Monitoring (before CRUD)
        Route::get('/invoices/details', [InvoiceController::class, 'getRentalFeeDetails']);
        Route::get('/invoices/monitor', [InvoiceController::class, 'monitorPayments']);

        // Invoice CRUD
        Route::get('/invoices', [InvoiceController::class, 'index']);
        Route::post('/invoices', [InvoiceController::class, 'store']);
        Route::get('/invoices/{invoice}', [InvoiceController::class, 'show']);
        Route::put('/invoices/{invoice}', [InvoiceController::class, 'update']);
        Route::delete('/invoices/{invoice}', [InvoiceController::class, 'destroy']);


        // ============================================
        // PAYMENT ROUTES
        // ============================================

        // Payment Reports
        Route::get('/payments/reports/generate', [PaymentController::class, 'report']);
        Route::get('/payments/reports/pdf', [PaymentController::class, 'generatePDF']);

        // Payment Monitoring (before CRUD)
        Route::get('/payments/monitor', [PaymentController::class, 'monitorPayments']);

        // Payment CRUD
        Route::get('/payments', [PaymentController::class, 'index']);
        Route::post('/payments', [PaymentController::class, 'store']);
        Route::get('/payments/{payment}', [PaymentController::class, 'show']);
        Route::put('/payments/{payment}', [PaymentController::class, 'update']);
        Route::delete('/payments/{payment}', [PaymentController::class, 'destroy']);

        // Payment Actions
        Route::get('/payments/{payment}/receipt', [PaymentController::class, 'generateReceiptPDF']);

    });
 });
