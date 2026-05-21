# Security Implementation Examples - Copy & Paste Code

Use these examples to quickly add security features to your controllers.

---

## 1. Add Audit Logging to AuthController

**File**: `app/Http/Controllers/AuthController.php`

Add this at the top:
```php
use App\Services\AuditService;
use Illuminate\Support\Facades\RateLimiter;
```

Update your `login()` method:
```php
public function login(Request $request)
{
    // Validate input
    $validated = $request->validate([
        'email' => 'required|email',
        'password' => 'required|min:8',
    ]);

    // Check rate limiting
    $key = "auth.login.{$request->ip()}";
    if (RateLimiter::tooManyAttempts($key, 5)) {
        AuditService::log('login_rate_limited', changes: [
            'email' => $validated['email'],
            'ip' => $request->ip(),
        ]);
        return back()->withErrors('Too many login attempts. Try again in 15 minutes.');
    }

    // Attempt login
    if (Auth::attempt($validated, $request->boolean('remember'))) {
        // Clear rate limit on success
        RateLimiter::clear($key);

        // Log successful login
        AuditService::log('login_success');

        // Regenerate session token for CSRF protection
        $request->session()->regenerate();

        return redirect()->intended('/dashboard');
    }

    // Log failed attempt
    RateLimiter::hit($key, 900); // 15 minutes
    AuditService::log('login_failed', changes: [
        'email' => $validated['email'],
        'ip' => $request->ip(),
    ]);

    return back()->withErrors('Invalid credentials.');
}

public function logout(Request $request)
{
    // Log logout
    AuditService::log('logout');

    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect('/');
}
```

---

## 2. Add Audit Logging to CustomerController

**File**: `app/Http/Controllers/CustomerController.php`

Add at the top:
```php
use App\Services\AuditService;
```

Update `store()` method:
```php
public function store(StoreCustomerRequest $request)
{
    // Authorize
    $this->authorize('create', Customer::class);

    // Create customer
    $customer = Customer::create($request->validated());

    // Log creation
    AuditService::log('create', $customer, [
        'name' => $customer->name,
        'email' => $customer->email,
    ]);

    return response()->json([
        'message' => 'Customer created successfully',
        'customer' => $customer,
    ], 201);
}

public function update(UpdateCustomerRequest $request, Customer $customer)
{
    // Authorize
    $this->authorize('update', $customer);

    // Store old values
    $oldValues = $customer->toArray();

    // Update
    $customer->update($request->validated());

    // Find what changed
    $newValues = $customer->toArray();
    $changes = [];
    foreach ($oldValues as $key => $value) {
        if ($newValues[$key] !== $value) {
            $changes[$key] = ['old' => $value, 'new' => $newValues[$key]];
        }
    }

    // Log update
    AuditService::log('update', $customer, $changes);

    return response()->json([
        'message' => 'Customer updated successfully',
        'customer' => $customer,
    ]);
}

public function destroy(Customer $customer)
{
    // Authorize
    $this->authorize('delete', $customer);

    // Log deletion (before deleting!)
    AuditService::log('delete', $customer, [
        'name' => $customer->name,
        'email' => $customer->email,
    ]);

    $customer->delete();

    return response()->json([
        'message' => 'Customer deleted successfully',
    ]);
}
```

---

## 3. Add Rate Limiting to Routes

**File**: `routes/web.php`

Update authentication routes:
```php
// ===== UNAUTHENTICATED ROUTES =====
Route::middleware('guest')->group(function () {
    // Login with rate limiting (5 attempts per 15 minutes)
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])
        ->middleware('throttle:5,15');  // 5 attempts per 15 minutes

    // Registration with rate limiting
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])
        ->middleware('throttle:3,60');  // 3 registrations per hour

    // OTP routes with strict limits
    Route::post('/otp/generate-otp', [OtpController::class, 'generateOtp'])
        ->middleware('throttle:3,600');  // 3 per 10 minutes (prevent email spam)
    Route::post('/otp/verify-otp', [OtpController::class, 'verifyOtp'])
        ->middleware('throttle:5,900');  // 5 attempts per 15 minutes
    Route::post('/otp/resend-otp', [OtpController::class, 'resendOtp'])
        ->middleware('throttle:2,600');  // 2 resends per 10 minutes
});

// ===== AUTHENTICATED ROUTES =====
Route::middleware('auth')->group(function () {
    // General dashboard
    Route::get('/dashboard', [DashboardController::class, 'index']);

    // Admin routes (with additional authorization)
    Route::middleware('admin')->group(function () {
        Route::get('/customers', [CustomerController::class, 'index']);
        Route::post('/api/customers', [CustomerController::class, 'store'])
            ->middleware('throttle:30,60');  // 30 requests per minute
        Route::put('/api/customers/{customer}', [CustomerController::class, 'update'])
            ->middleware('throttle:30,60');
        Route::delete('/api/customers/{customer}', [CustomerController::class, 'destroy'])
            ->middleware('throttle:10,60');  // Delete is more restricted
    });

    // User logout
    Route::post('/logout', [AuthController::class, 'logout']);
});
```

---

## 4. Monitor Audit Logs in Controller

Create a new controller for admin dashboard:

**File**: `app/Http/Controllers/SecurityController.php`

```php
<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\View\View;

class SecurityController extends Controller
{
    /**
     * Show recent audit logs
     */
    public function auditLogs(): View
    {
        // Check admin
        if (!auth()->user()->is_admin) {
            abort(403);
        }

        // Get recent logs
        $logs = AuditLog::with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(50);

        return view('security.audit-logs', compact('logs'));
    }

    /**
     * Show failed login attempts
     */
    public function failedLogins(): View
    {
        if (!auth()->user()->is_admin) {
            abort(403);
        }

        $failedLogins = AuditLog::where('action', 'login_failed')
            ->where('created_at', '>=', now()->subHours(24))
            ->orderBy('created_at', 'desc')
            ->paginate(50);

        return view('security.failed-logins', compact('failedLogins'));
    }

    /**
     * Detect suspicious activity
     */
    public function suspiciousActivity()
    {
        if (!auth()->user()->is_admin) {
            abort(403);
        }

        // Find IPs with many failed logins
        $suspiciousIps = AuditLog::where('action', 'login_failed')
            ->where('created_at', '>=', now()->subHours(1))
            ->groupBy('ip_address')
            ->selectRaw('ip_address, count(*) as attempts')
            ->having('attempts', '>=', 5)
            ->get();

        return response()->json($suspiciousIps);
    }

    /**
     * Export audit logs to CSV
     */
    public function exportAuditLogs()
    {
        if (!auth()->user()->is_admin) {
            abort(403);
        }

        $logs = AuditLog::all();

        $csv = "ID,User ID,Action,Model Type,Model ID,IP Address,Created At\n";
        foreach ($logs as $log) {
            $csv .= "{$log->id},{$log->user_id},{$log->action},{$log->model_type},{$log->model_id},{$log->ip_address},{$log->created_at}\n";
        }

        return response($csv)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="audit_logs_' . now()->format('Y-m-d') . '.csv"');
    }
}
```

Add route:
```php
// In routes/web.php
Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/admin/audit-logs', [SecurityController::class, 'auditLogs']);
    Route::get('/admin/failed-logins', [SecurityController::class, 'failedLogins']);
    Route::get('/admin/suspicious-activity', [SecurityController::class, 'suspiciousActivity']);
    Route::get('/admin/export-audit-logs', [SecurityController::class, 'exportAuditLogs']);
});
```

---

## 5. Blade Template for Audit Logs

**File**: `resources/views/security/audit-logs.blade.php`

```blade
<div class="container mx-auto px-4">
    <h1 class="text-2xl font-bold mb-4">Audit Logs</h1>

    <!-- Filter -->
    <form method="GET" class="mb-4">
        <div class="grid grid-cols-4 gap-4">
            <input type="text" name="user_id" placeholder="User ID" class="border px-2 py-1">
            <input type="text" name="action" placeholder="Action" class="border px-2 py-1">
            <input type="text" name="ip_address" placeholder="IP Address" class="border px-2 py-1">
            <button type="submit" class="bg-blue-500 text-white px-4 py-1">Filter</button>
        </div>
    </form>

    <!-- Table -->
    <table class="w-full border-collapse border">
        <thead>
            <tr class="bg-gray-100">
                <th class="border px-4 py-2">Time</th>
                <th class="border px-4 py-2">User</th>
                <th class="border px-4 py-2">Action</th>
                <th class="border px-4 py-2">Model</th>
                <th class="border px-4 py-2">IP Address</th>
                <th class="border px-4 py-2">Details</th>
            </tr>
        </thead>
        <tbody>
            @forelse($logs as $log)
                <tr>
                    <td class="border px-4 py-2">{{ $log->created_at->format('Y-m-d H:i:s') }}</td>
                    <td class="border px-4 py-2">
                        @if($log->user)
                            {{ $log->user->name }}
                        @else
                            System
                        @endif
                    </td>
                    <td class="border px-4 py-2">
                        <span class="inline-block px-2 py-1 rounded text-white text-sm"
                              style="background-color: {{ $this->getActionColor($log->action) }}">
                            {{ $log->action }}
                        </span>
                    </td>
                    <td class="border px-4 py-2">
                        @if($log->model_type)
                            {{ class_basename($log->model_type) }} #{{ $log->model_id }}
                        @else
                            -
                        @endif
                    </td>
                    <td class="border px-4 py-2">{{ $log->ip_address }}</td>
                    <td class="border px-4 py-2">
                        @if($log->changes)
                            <details>
                                <summary>View Changes</summary>
                                <pre class="bg-gray-100 p-2 mt-2 text-xs">{{ json_encode($log->changes, JSON_PRETTY_PRINT) }}</pre>
                            </details>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="border px-4 py-2 text-center">No audit logs found</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Pagination -->
    <div class="mt-4">
        {{ $logs->links() }}
    </div>
</div>

@php
    function getActionColor($action) {
        return match($action) {
            'login_success' => '#10b981',    // green
            'login_failed' => '#ef4444',     // red
            'logout' => '#3b82f6',           // blue
            'create' => '#8b5cf6',           // purple
            'update' => '#f59e0b',           // amber
            'delete' => '#dc2626',           // dark red
            default => '#6b7280',            // gray
        };
    }
@endphp
```

---

## 6. Secure Payment Processing Example

**File**: `app/Http/Controllers/PaymentController.php`

```php
public function store(StorePaymentRequest $request)
{
    // Verify user authorization
    $this->authorize('create', Payment::class);

    // Start database transaction
    DB::beginTransaction();

    try {
        // Get invoice
        $invoice = Invoice::findOrFail($request->invoice_id);

        // Validate payment amount
        if ($request->amount <= 0) {
            throw new \Exception('Payment amount must be positive');
        }

        if ($request->amount > $invoice->total) {
            throw new \Exception('Payment amount exceeds invoice total');
        }

        // Create payment record
        $payment = Payment::create([
            'invoice_id' => $invoice->id,
            'processed_by' => auth()->id(),
            'amount' => $request->amount,
            'payment_method' => $request->payment_method,
            'reference_number' => $this->generateReference(),
            'status' => 'completed',
        ]);

        // Update invoice status
        $invoice->update(['status' => 'paid']);

        // Log payment
        AuditService::log('create_payment', $payment, [
            'invoice_id' => $invoice->id,
            'amount' => $request->amount,
            'method' => $request->payment_method,
        ]);

        DB::commit();

        return response()->json([
            'message' => 'Payment processed successfully',
            'payment' => $payment,
        ], 201);

    } catch (\Exception $e) {
        DB::rollBack();

        // Log failed payment attempt
        AuditService::log('payment_failed', changes: [
            'invoice_id' => $request->invoice_id,
            'error' => $e->getMessage(),
        ]);

        return response()->json([
            'message' => 'Payment processing failed: ' . $e->getMessage(),
        ], 422);
    }
}

private function generateReference(): string
{
    return 'PAY-' . now()->format('YmdHis') . '-' . random_int(1000, 9999);
}
```

---

## 7. Test Security Features

**File**: `tests/Feature/SecurityTest.php`

```php
<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SecurityTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_attempt_is_audited()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
        ]);

        // Attempt login
        $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        // Check audit log
        $this->assertDatabaseHas('audit_logs', [
            'user_id' => $user->id,
            'action' => 'login_success',
        ]);
    }

    public function test_failed_login_is_audited()
    {
        User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
        ]);

        // Attempt login with wrong password
        $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'wrongpassword',
        ]);

        // Check audit log
        $this->assertDatabaseHas('audit_logs', [
            'action' => 'login_failed',
        ]);
    }

    public function test_rate_limit_on_login()
    {
        // Make 6 login attempts
        for ($i = 0; $i < 6; $i++) {
            $response = $this->post('/login', [
                'email' => 'test@example.com',
                'password' => 'wrongpassword',
            ]);
        }

        // 6th attempt should be rate limited (429)
        $this->assertEquals(429, $response->getStatusCode());
    }

    public function test_security_headers_present()
    {
        $response = $this->get('/');

        $response->assertHeader('X-Frame-Options', 'SAMEORIGIN');
        $response->assertHeader('X-Content-Type-Options', 'nosniff');
        $response->assertHeader('X-XSS-Protection');
    }

    public function test_debug_mode_not_exposed()
    {
        // Visit an undefined route
        $response = $this->get('/undefined-route-that-does-not-exist');

        // Should not contain debug information
        $this->assertStringNotContainsString('APP_DEBUG', $response->getContent());
        $this->assertStringNotContainsString('Stack trace', $response->getContent());
    }
}
```

Run tests:
```bash
php artisan test
```

---

## ✅ Quick Copy-Paste Implementation Summary

1. **Controllers**: Copy audit logging to login/logout and CRUD operations
2. **Routes**: Add `->middleware('throttle:X,Y')` to rate-limited endpoints
3. **Models**: Add `$encrypted` or `$casts` for sensitive data
4. **Services**: Use `AuditService::log()` for important operations
5. **Tests**: Verify security features work as expected

You're now equipped to implement production-grade security! 🔐
