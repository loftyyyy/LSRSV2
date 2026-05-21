# Quick Security Improvements - 3-Hour Implementation Guide

## 📋 Pre-Implementation Checklist

- [ ] Back up current database
- [ ] Commit current code to git
- [ ] Have new Gmail app password ready (if using Gmail)
- [ ] Estimate implementation time: ~3 hours

---

## ⏱️ TIER 1: CRITICAL (45 Minutes)

### 1. Fix Exposed Credentials (15 minutes)

```bash
# Step 1: Regenerate Gmail App Password
# Visit: https://myaccount.google.com/apppasswords
# Generate for "Mail" → copy the 16-char password

# Step 2: Update .env
nano .env

# Find and update:
MAIL_PASSWORD=your_new_app_password    # Replace kvwaskodsxiyffkb

# Step 3: Verify credentials work
php artisan tinker
Mail::raw('Test email', function($message) {
    $message->to('test@example.com');
});
```

**✅ Done**: Credentials no longer exposed

---

### 2. Disable Debug Mode (5 minutes)

```bash
# Edit .env
APP_DEBUG=false
APP_ENV=local  # Change to 'production' when deploying

# Verify in browser - no stack traces on errors
php artisan serve
# Visit any undefined route to test
```

**✅ Done**: Debug mode disabled

---

### 3. Set Database Password (10 minutes)

```bash
# On your MySQL server:
mysql -u root -p
CREATE USER 'lsrs_dev'@'localhost' IDENTIFIED BY 'MySecurePass123!';
GRANT ALL ON LSRSV2.* TO 'lsrs_dev'@'localhost';
FLUSH PRIVILEGES;
EXIT;

# Update .env
DB_USERNAME=lsrs_dev
DB_PASSWORD=MySecurePass123!

# Test connection
php artisan migrate --dry-run
```

**✅ Done**: Database secured with strong password

---

### 4. Reduce Session Timeout (2 minutes)

```bash
# Edit .env
SESSION_LIFETIME=30          # Changed from 120
SESSION_SAME_SITE=strict     # Changed from lax

# Test: Create new session, verify logs out after 30 min
```

**✅ Done**: Sessions now expire after 30 minutes

---

### Phase 1 Verification
```bash
# Test everything still works
php artisan serve

# Visit http://localhost:8000/login
# Test login flow
# Test no debug errors on bad requests
```

**Time Elapsed**: ~45 minutes ✅

---

## ⏱️ TIER 2: HIGH-IMPACT (1 Hour)

### 5. Add Security Headers (20 minutes)

The middleware file is already created at:
`app/Http/Middleware/SecurityHeadersMiddleware.php`

Now register it:

```bash
# Edit app/Http/Kernel.php
nano app/Http/Kernel.php
```

Find this line (~line 20):
```php
protected $middleware = [
```

Add to the array:
```php
protected $middleware = [
    // ... existing middleware
    \App\Http\Middleware\SecurityHeadersMiddleware::class,  // ← Add this
];
```

**Test it works**:
```bash
# In new terminal
php artisan serve

# In another terminal
curl -I http://localhost:8000 | grep -E "X-Frame|X-Content|X-XSS"

# You should see:
# X-Frame-Options: SAMEORIGIN
# X-Content-Type-Options: nosniff
# X-XSS-Protection: 1; mode=block
```

**✅ Done**: Security headers added

---

### 6. Add Rate Limiting (25 minutes)

**Option A: Simple Throttle (Recommended for quick start)**

Edit `routes/web.php` and find the authentication routes section:

```php
// Find these lines in routes/web.php around line 20-30
Route::middleware('guest')->group(function () {
    Route::get('login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    // ... etc
```

Update to:
```php
// Guest routes with rate limiting
Route::middleware(['guest', 'throttle:5,15'])->group(function () {
    // 5 attempts per 15 minutes
    Route::get('login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

// OTP routes with stricter limit
Route::post('/otp/generate-otp', [OtpController::class, 'generateOtp'])
    ->middleware(['guest', 'throttle:3,10']);  // 3 per 10 min
Route::post('/otp/verify-otp', [OtpController::class, 'verifyOtp'])
    ->middleware(['guest', 'throttle:5,15']);  // 5 per 15 min
```

**Test it works**:
```bash
# In Postman or curl, make 6 POST requests to /login
# On the 6th request, you should get 429 Too Many Requests
curl -X POST http://localhost:8000/login -d 'email=test@test.com&password=test'
# ... repeat 5+ times
```

**✅ Done**: Rate limiting active

---

### 7. Enforce HTTPS in Production (5 minutes)

The code is already in `app/Providers/AppServiceProvider.php`, but let's verify:

```bash
nano app/Providers/AppServiceProvider.php
```

You should see in the `boot()` method:
```php
public function boot(): void
{
    // Force HTTPS in production
    if ($this->app->environment('production')) {
        URL::forceScheme('https');
    }
}
```

If not there, add it. This is already implemented ✅

Create `.env.production` for production deployment (already created, see `SECURITY_IMPROVEMENTS.md`)

**✅ Done**: HTTPS enforced for production

---

### Phase 2 Verification
```bash
php artisan serve

# Test security headers
curl -I http://localhost:8000 | grep "X-"

# Test rate limiting
for i in {1..6}; do curl -X POST http://localhost:8000/login; done
# Should get 429 on 6th attempt
```

**Time Elapsed**: ~1 hour 45 minutes ✅

---

## ⏱️ TIER 3: ESSENTIAL (1.5 Hours)

### 8. Audit Logging (45 minutes)

Files already created:
- `app/Models/AuditLog.php`
- `app/Services/AuditService.php`
- `database/migrations/2026_05_21_132212_create_audit_logs_table.php`

**Step 1: Run migration**
```bash
php artisan migrate

# Verify table created
php artisan tinker
>>> Schema::getTables()
```

**Step 2: Use audit logging in a controller**

Edit `app/Http/Controllers/AuthController.php`:

Find the `login()` method and add:
```php
use App\Services\AuditService;

public function login(Request $request)
{
    // ... existing login code ...

    if (!Auth::attempt($credentials)) {
        // Log failed attempt
        AuditService::log('login_failed', changes: [
            'email' => $request->email,
            'ip' => $request->ip(),
        ]);

        return back()->withErrors('Invalid credentials');
    }

    // Log successful login
    AuditService::log('login_success');

    return redirect('/dashboard');
}

public function logout()
{
    AuditService::log('logout');
    Auth::logout();
    // ... rest of logout code ...
}
```

**Step 3: View audit logs**
```bash
php artisan tinker

# See all logins
>>> App\Models\AuditLog::where('action', 'login_success')->get()

# See failed attempts
>>> App\Models\AuditLog::where('action', 'login_failed')->get()

# See by user
>>> App\Models\AuditLog::where('user_id', 1)->latest()->get()
```

**✅ Done**: Audit logging active

---

### 9. Sensitive Data Encryption (30 minutes)

**Option A: For new installations (recommended)**

Edit `app/Models/Customer.php`:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    // ... existing code ...

    protected $casts = [
        // Encrypt these fields automatically
        'phone_number' => 'encrypted',
        'address' => 'encrypted',
        'city' => 'encrypted',
        'measurements' => 'encrypted:json',  // For JSON data
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}
```

**Option B: For existing data (requires migration)**

Skip for now if you have existing data. See `SECURITY_IMPROVEMENTS.md` for detailed guide.

**Test encryption**:
```bash
php artisan tinker

# Create customer
$customer = App\Models\Customer::create([
    'name' => 'John Doe',
    'email' => 'john@example.com',
    'phone_number' => '+1234567890',
]);

# Retrieve and verify auto-decryption
>>> $customer->phone_number
# Shows decrypted value

# Check database directly - should be encrypted
>>> DB::table('customers')->where('id', 1)->first()
# phone_number shows encrypted blob
```

**✅ Done**: Sensitive data encrypted

---

### 10. Input Sanitization (20 minutes)

Create sanitization middleware (can be skipped if only using Blade escaping):

Edit `routes/web.php` and add input validation to all POST routes:

```php
// Example: Customer creation
Route::post('/api/customers', function (Request $request) {
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|max:255',
        'phone_number' => 'required|string|max:20',
    ]);
    
    // Validated input is automatically sanitized
    $customer = Customer::create($validated);
    return response()->json($customer);
});
```

**✅ Done**: Input validation active (sanitization via validation)

---

### Phase 3 Verification
```bash
# Verify audit table
php artisan tinker
>>> DB::table('audit_logs')->count()

# Make test login/logout
# Check new audit entries
>>> App\Models\AuditLog::latest(5)->get()

# Test encryption
>>> $customer = App\Models\Customer::first()
>>> $customer->phone_number  # Should show plaintext
>>> DB::table('customers')->find($customer->id)->phone_number  # Shows encrypted
```

**Time Elapsed**: ~3 hours ✅

---

## ✅ Post-Implementation Checklist

- [ ] All tests still pass: `php artisan test`
- [ ] Login/logout flow works
- [ ] Session timeout works (wait 30+ min or manually test)
- [ ] Rate limiting blocks after 5 attempts
- [ ] Security headers present: `curl -I http://localhost:8000 | grep X-`
- [ ] Audit logs show login/logout events
- [ ] Sensitive data encrypted in database
- [ ] No debug errors shown: `APP_DEBUG=false` verified
- [ ] Database using non-root user with strong password

---

## 🚀 Production Deployment Checklist

Before deploying to production:

1. **Generate new encryption key**
   ```bash
   php artisan key:generate
   ```

2. **Update for production environment**
   ```bash
   cp .env.production .env
   # Edit with production values
   ```

3. **Run all migrations**
   ```bash
   php artisan migrate --force
   ```

4. **Clear caches**
   ```bash
   php artisan cache:clear
   php artisan config:clear
   php artisan view:clear
   php artisan route:clear
   ```

5. **Cache configuration**
   ```bash
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```

6. **Test HTTPS**
   ```bash
   curl -I https://yourdomain.com | grep -E "X-|Strict"
   ```

7. **Monitor audit logs**
   ```bash
   # Check for suspicious activity daily
   php artisan tinker
   >>> App\Models\AuditLog::where('action', 'login_failed')
   >>>     ->where('created_at', '>=', now()->subHours(24))->get()
   ```

---

## 📚 Additional Resources

- **Laravel Security**: https://laravel.com/docs/security
- **OWASP Top 10**: https://owasp.org/www-project-top-ten/
- **Authentication Best Practices**: https://cheatsheetseries.owasp.org/cheatsheets/Authentication_Cheat_Sheet.html

---

## 🆘 Troubleshooting

### Security headers not showing
```bash
# Check middleware registered
grep -r "SecurityHeadersMiddleware" app/Http/Kernel.php

# Verify response
curl -v http://localhost:8000 2>&1 | grep -i "x-frame"
```

### Rate limiting not working
```bash
# Check throttle route applied
grep -r "throttle" routes/web.php

# Test directly
php artisan tinker
>>> Illuminate\Support\Facades\RateLimiter::forgetByTag('auth')
>>> Illuminate\Support\Facades\RateLimiter::clear()
```

### Encryption not working
```bash
# Verify APP_KEY set
grep APP_KEY .env

# Check casts on model
grep -A 10 "protected \$casts" app/Models/Customer.php
```

---

**You now have a significantly more secure LSRS application!** 🎉
