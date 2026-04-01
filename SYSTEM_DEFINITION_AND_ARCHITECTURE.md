# LSRSV2 - System Definition and Architecture Document

> **Love & Styles Rental System Version 2**  
> A Comprehensive Rental Management System for Formal Wear

---

## Table of Contents

1. [System Definition and Implementation Preparation](#1-system-definition-and-implementation-preparation)
   - [1.1 System Scope](#11-system-scope)
   - [1.2 Key Features](#12-key-features)
   - [1.3 Operational Constraints](#13-operational-constraints)
   - [1.4 Development Tools and Technologies](#14-development-tools-and-technologies)
   - [1.5 Laravel Coding Plan](#15-laravel-coding-plan)
   - [1.6 Coding Standards](#16-coding-standards)
   - [1.7 Repository Structure](#17-repository-structure)
2. [Architecture and Module Breakdown](#2-architecture-and-module-breakdown)
   - [2.1 High-Level System Architecture](#21-high-level-system-architecture)
   - [2.2 System Flow Diagram](#22-system-flow-diagram)
   - [2.3 Module Breakdown by Subsystem](#23-module-breakdown-by-subsystem)
3. [Setup and Execution Guide](#3-setup-and-execution-guide)

---

## 1. System Definition and Implementation Preparation

### 1.1 System Scope

#### Project Overview

**LSRSV2 (Love & Styles Rental System Version 2)** is a web-based rental management system designed specifically for Love & Styles, a formal wear rental business specializing in gowns and suits. The system automates the complete rental lifecycle from customer registration through item return, including reservation management, billing, and reporting.

#### Target Users

| User Type | Role Description |
|-----------|------------------|
| **Administrator** | Full system access, user management, system configuration |
| **Staff/Clerk** | Day-to-day operations: reservations, rentals, payments, customer service |
| **Manager** | Reports, dashboard analytics, inventory oversight |

#### Business Domain

- **Industry**: Formal Wear Rental (Gowns & Suits)
- **Operations**: Walk-in and reservation-based rentals
- **Location**: Single-store operation with potential for multi-branch expansion

#### System Boundaries

**In Scope:**
- Customer registration and management with body measurements
- Inventory tracking for gowns and suits
- Reservation and booking management
- Rental processing (release, extension, return)
- Invoicing and payment processing
- Dashboard analytics and reporting
- PDF generation for invoices, receipts, and reports

**Out of Scope:**
- E-commerce / online customer self-service portal
- Mobile application
- Multi-currency support
- Third-party payment gateway integration (manual payment recording only)
- Delivery/logistics management

---

### 1.2 Key Features

#### Core Feature Matrix

| # | Feature | Description | Priority | Status |
|---|---------|-------------|----------|--------|
| 1 | **Authentication & Security** | User login, registration, OTP-based password recovery, session management | Critical | 95% |
| 2 | **Customer Management** | CRUD operations, body measurements, rental history, status tracking | Critical | 90% |
| 3 | **Inventory Management** | Item CRUD, SKU generation, availability tracking, image management, condition monitoring | Critical | 92% |
| 4 | **Reservation System** | Browse items, create/confirm/cancel reservations, date validation, item allocation | High | 85% |
| 5 | **Rental Management** | Release items, track extensions, process returns, overdue detection, deposit handling | High | 88% |
| 6 | **Invoicing & Billing** | Generate invoices, calculate totals with tax/discounts, partial payments, PDF export | High | 82% |
| 7 | **Payment Processing** | Multiple payment methods, receipt generation, payment status tracking | High | 85% |
| 8 | **Dashboard & Reports** | KPI metrics, 22 chart visualizations, PDF report generation | Medium | 90% |

#### Feature Details

##### 1. Authentication & Security
- Session-based authentication using Laravel's built-in auth
- Password hashing with bcrypt (cost factor 12)
- OTP verification via email for password recovery
- Rate limiting on login and OTP verification attempts
- CSRF protection on all forms

##### 2. Customer Management
- Full CRUD with soft delete capability
- JSON-stored body measurements (bust, waist, hips, height, etc.)
- Customer status workflow (Active, Inactive, Blacklisted)
- Rental history and transaction tracking
- PDF customer report generation

##### 3. Inventory Management
- Automatic SKU generation pattern: `{TYPE}-{NUMBER}` (e.g., GWN-001, SUT-001)
- Item types: Gowns (GWN) and Suits (SUT)
- Condition states: Excellent, Good, Fair, Poor, Damaged
- Multiple images per item with primary image support
- Variant grouping for similar items
- Availability checking against reservations and active rentals
- Optional selling price for items marked as sellable

##### 4. Reservation System
- Browse available items by date range
- Create reservations with multiple items
- Automatic availability conflict detection
- Reservation status workflow: Pending → Confirmed → Completed/Cancelled
- Deposit requirement calculation

##### 5. Rental Management
- Convert reservations to active rentals
- Item release with staff tracking
- Extension requests with reason documentation
- Return processing with condition assessment
- Automatic overdue detection and flagging
- Deposit states: Held, Returned, Forfeited

##### 6. Invoicing & Billing
- Invoice types: Reservation, Rental, Final
- Line item management with quantities and unit prices
- Discount support (percentage or fixed amount)
- Tax calculation (configurable rate)
- Balance tracking for partial payments
- PDF invoice generation with company branding

##### 7. Payment Processing
- Payment methods: Cash, Credit Card, Bank Transfer, GCash, PayMaya
- Payment amount validation against invoice balance
- Receipt PDF generation
- Payment history per invoice

##### 8. Dashboard & Reports
- Real-time KPI cards (revenue, rentals, customers, etc.)
- 22 interactive charts with Chart.js
- Dark/light theme support
- Date range filtering
- PDF export for all reports

---

### 1.3 Operational Constraints

#### Technical Constraints

| Constraint | Specification |
|------------|---------------|
| **Server Environment** | PHP 8.2+ with required extensions |
| **Database** | MySQL 8.0+ or MariaDB 10.6+ |
| **Web Server** | Apache 2.4+ or Nginx 1.18+ |
| **Memory** | Minimum 512MB PHP memory limit |
| **Storage** | Minimum 10GB for application and uploads |
| **SSL** | Required for production deployment |

#### Business Constraints

| Constraint | Description |
|------------|-------------|
| **Operating Hours** | System designed for business-hour operations |
| **Single Currency** | PHP (Philippine Peso) only |
| **Single Branch** | Initial deployment for single location |
| **Manual Payments** | No real-time payment gateway integration |
| **Data Retention** | 7-year retention policy for financial records |

#### Performance Requirements

| Metric | Target |
|--------|--------|
| **Page Load Time** | < 3 seconds |
| **API Response Time** | < 500ms |
| **Concurrent Users** | Support 50 simultaneous users |
| **Database Queries** | < 20 queries per page |
| **Uptime** | 99.5% availability |

---

### 1.4 Development Tools and Technologies

#### Backend Stack

| Technology | Version | Purpose |
|------------|---------|---------|
| **PHP** | 8.2+ | Server-side programming language |
| **Laravel Framework** | 12.0 | MVC web application framework |
| **Composer** | 2.x | PHP dependency management |
| **Laravel DomPDF** | 3.1 | PDF document generation |
| **Predis** | 3.3 | Redis client for caching/sessions |

#### Frontend Stack

| Technology | Version | Purpose |
|------------|---------|---------|
| **TailwindCSS** | 4.0 | Utility-first CSS framework |
| **Vite** | 7.0 | Frontend build tool and dev server |
| **Alpine.js** | 3.x | Lightweight JavaScript framework |
| **Chart.js** | 4.4 | Interactive chart library |
| **Axios** | 1.11 | HTTP client for AJAX requests |

#### Database

| Technology | Version | Purpose |
|------------|---------|---------|
| **MySQL** | 8.0+ | Primary relational database |
| **Redis** | 7.x | Session storage and caching (optional) |

#### Development Tools

| Tool | Purpose |
|------|---------|
| **Laravel Pint** | PHP code style fixer (PSR-12) |
| **PHPUnit** | Unit and feature testing |
| **Laravel Pail** | Real-time log viewer |
| **Laravel Sail** | Docker development environment |
| **Faker** | Test data generation |
| **Mockery** | Test mocking library |

#### IDE and Environment

| Tool | Recommendation |
|------|----------------|
| **IDE** | PhpStorm, VS Code with PHP extensions |
| **Version Control** | Git with GitHub/GitLab |
| **API Testing** | Postman, Insomnia |
| **Database Client** | TablePlus, DBeaver, phpMyAdmin |

---

### 1.5 Laravel Coding Plan

#### MVC Structure Overview

```
app/
├── Http/
│   ├── Controllers/          # Handle HTTP requests
│   │   ├── AuthController.php
│   │   ├── CustomerController.php
│   │   ├── InventoryController.php
│   │   ├── ReservationController.php
│   │   ├── RentalController.php
│   │   ├── InvoiceController.php
│   │   ├── PaymentController.php
│   │   └── DashboardController.php
│   ├── Middleware/           # Request filtering
│   │   └── Authenticate.php
│   └── Requests/             # Form validation
│       ├── StoreCustomerRequest.php
│       ├── UpdateCustomerRequest.php
│       └── ...
├── Models/                   # Eloquent ORM models
│   ├── User.php
│   ├── Customer.php
│   ├── Inventory.php
│   ├── Reservation.php
│   ├── Rental.php
│   ├── Invoice.php
│   └── Payment.php
├── Services/                 # Business logic services
│   ├── DepositService.php
│   └── OtpService.php
└── Policies/                 # Authorization policies
```

#### Routing Structure

**Route Organization (`routes/web.php`):**

```php
<?php

use Illuminate\Support\Facades\Route;

// Guest Routes (Unauthenticated)
Route::middleware('guest')->group(function () {
    Route::get('login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('login', [AuthController::class, 'login']);
    Route::get('register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('register', [AuthController::class, 'register']);
    // Password reset with OTP
    Route::prefix('password')->group(function () {
        Route::get('forgot', [AuthController::class, 'showForgotPassword']);
        Route::post('forgot', [AuthController::class, 'sendOtp']);
        Route::get('verify', [AuthController::class, 'showVerifyOtp']);
        Route::post('verify', [OtpController::class, 'verify']);
        Route::get('reset', [AuthController::class, 'showResetPassword']);
        Route::post('reset', [AuthController::class, 'resetPassword']);
    });
});

// Authenticated Routes
Route::middleware('auth')->group(function () {
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');
    
    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/metrics', [DashboardController::class, 'metrics']);
    
    // Resource Routes (CRUD)
    Route::resource('customers', CustomerController::class);
    Route::resource('inventories', InventoryController::class);
    Route::resource('reservations', ReservationController::class);
    Route::resource('rentals', RentalController::class);
    Route::resource('invoices', InvoiceController::class);
    Route::resource('payments', PaymentController::class);
    
    // Custom Actions
    Route::prefix('rentals')->group(function () {
        Route::post('{rental}/release', [RentalController::class, 'release']);
        Route::post('{rental}/return', [RentalController::class, 'return']);
        Route::post('{rental}/extend', [RentalController::class, 'extend']);
    });
    
    // PDF Generation
    Route::get('invoices/{invoice}/pdf', [InvoiceController::class, 'generatePdf']);
    Route::get('payments/{payment}/receipt', [PaymentController::class, 'generateReceipt']);
});
```

#### Controller Pattern

**Standard Controller Structure:**

```php
<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCustomerRequest;
use App\Http\Requests\UpdateCustomerRequest;
use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    /**
     * Display a listing of customers.
     */
    public function index(Request $request)
    {
        $customers = Customer::with('status')
            ->filter($request->only(['search', 'status']))
            ->latest()
            ->paginate(15);
            
        return view('customers.index', compact('customers'));
    }

    /**
     * Show the form for creating a new customer.
     */
    public function create()
    {
        return view('customers.create');
    }

    /**
     * Store a newly created customer.
     */
    public function store(StoreCustomerRequest $request)
    {
        $customer = Customer::create($request->validated());
        
        return redirect()
            ->route('customers.show', $customer)
            ->with('success', 'Customer created successfully.');
    }

    /**
     * Display the specified customer.
     */
    public function show(Customer $customer)
    {
        $customer->load(['rentals', 'reservations', 'invoices']);
        
        return view('customers.show', compact('customer'));
    }

    /**
     * Show the form for editing the customer.
     */
    public function edit(Customer $customer)
    {
        return view('customers.edit', compact('customer'));
    }

    /**
     * Update the specified customer.
     */
    public function update(UpdateCustomerRequest $request, Customer $customer)
    {
        $customer->update($request->validated());
        
        return redirect()
            ->route('customers.show', $customer)
            ->with('success', 'Customer updated successfully.');
    }

    /**
     * Remove the specified customer.
     */
    public function destroy(Customer $customer)
    {
        $customer->delete();
        
        return redirect()
            ->route('customers.index')
            ->with('success', 'Customer deleted successfully.');
    }
}
```

#### Model Pattern

**Standard Model Structure:**

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'address',
        'measurements',
        'customer_status_id',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'measurements' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the customer's full name.
     */
    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    /**
     * Get the status that owns the customer.
     */
    public function status(): BelongsTo
    {
        return $this->belongsTo(CustomerStatus::class, 'customer_status_id');
    }

    /**
     * Get the reservations for the customer.
     */
    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }

    /**
     * Get the rentals for the customer.
     */
    public function rentals(): HasMany
    {
        return $this->hasMany(Rental::class);
    }

    /**
     * Get the invoices for the customer.
     */
    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    /**
     * Scope a query to filter customers.
     */
    public function scopeFilter($query, array $filters)
    {
        return $query
            ->when($filters['search'] ?? null, function ($query, $search) {
                $query->where(function ($query) use ($search) {
                    $query->where('first_name', 'like', "%{$search}%")
                          ->orWhere('last_name', 'like', "%{$search}%")
                          ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->when($filters['status'] ?? null, function ($query, $status) {
                $query->where('customer_status_id', $status);
            });
    }
}
```

#### Migration Pattern

**Standard Migration Structure:**

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('first_name', 100);
            $table->string('last_name', 100);
            $table->string('email')->unique();
            $table->string('phone', 20)->nullable();
            $table->text('address')->nullable();
            $table->json('measurements')->nullable();
            $table->foreignId('customer_status_id')
                  ->constrained()
                  ->onDelete('restrict');
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes for performance
            $table->index(['first_name', 'last_name']);
            $table->index('email');
            $table->index('customer_status_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
```

#### Form Request Validation

**Validation Request Structure:**

```php
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCustomerRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Or implement policy check
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'unique:customers,email'],
            'phone' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:500'],
            'measurements' => ['nullable', 'array'],
            'measurements.bust' => ['nullable', 'numeric', 'min:0', 'max:200'],
            'measurements.waist' => ['nullable', 'numeric', 'min:0', 'max:200'],
            'measurements.hips' => ['nullable', 'numeric', 'min:0', 'max:200'],
            'measurements.height' => ['nullable', 'numeric', 'min:0', 'max:300'],
            'customer_status_id' => ['required', 'exists:customer_statuses,id'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'first_name.required' => 'First name is required.',
            'email.unique' => 'This email address is already registered.',
        ];
    }
}
```

#### Authentication Implementation

```php
// AuthController.php - Key Methods

public function login(Request $request)
{
    $credentials = $request->validate([
        'email' => ['required', 'email'],
        'password' => ['required'],
    ]);

    if (Auth::attempt($credentials, $request->boolean('remember'))) {
        $request->session()->regenerate();
        return redirect()->intended(route('dashboard'));
    }

    return back()->withErrors([
        'email' => 'The provided credentials do not match our records.',
    ])->onlyInput('email');
}

public function logout(Request $request)
{
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    
    return redirect()->route('login');
}
```

---

### 1.6 Coding Standards

#### PSR-12 Compliance

This project follows **PSR-12: Extended Coding Style** as enforced by Laravel Pint.

##### File Structure
- Files MUST use only `<?php` tag
- Files MUST use only UTF-8 without BOM
- Files MUST end with a single blank line
- Class opening braces MUST go on the same line

##### Namespace and Imports
```php
<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCustomerRequest;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomerController extends Controller
{
    // ...
}
```

##### Indentation and Spacing
- Use 4 spaces for indentation, NOT tabs
- One blank line after namespace declaration
- One blank line after use imports
- One blank line between methods

#### Naming Conventions

| Element | Convention | Example |
|---------|------------|---------|
| **Classes** | PascalCase | `CustomerController`, `InvoiceService` |
| **Methods** | camelCase | `getFullName()`, `calculateTotal()` |
| **Variables** | camelCase | `$customerName`, `$totalAmount` |
| **Constants** | SCREAMING_SNAKE_CASE | `MAX_RETRY_ATTEMPTS` |
| **Database Tables** | snake_case, plural | `customers`, `inventory_images` |
| **Database Columns** | snake_case | `first_name`, `created_at` |
| **Routes** | kebab-case | `/customers/{customer}/rental-history` |
| **Config Keys** | snake_case | `mail.from.address` |
| **Blade Views** | kebab-case | `customer-form.blade.php` |

#### Controller Standards

```php
// DO: Use dependency injection
public function __construct(
    private CustomerService $customerService
) {}

// DO: Use Form Request validation
public function store(StoreCustomerRequest $request)

// DO: Use route model binding
public function show(Customer $customer)

// DO: Return proper responses
return response()->json($data, 201);
return redirect()->route('customers.index')->with('success', 'Created');

// DON'T: Put business logic in controllers
// Move complex logic to Service classes
```

#### Model Standards

```php
// DO: Define fillable explicitly
protected $fillable = ['first_name', 'last_name', 'email'];

// DO: Use attribute casting
protected $casts = [
    'measurements' => 'array',
    'is_active' => 'boolean',
    'birth_date' => 'date',
];

// DO: Define relationships with return types
public function status(): BelongsTo
{
    return $this->belongsTo(CustomerStatus::class);
}

// DO: Use scopes for reusable queries
public function scopeActive($query)
{
    return $query->where('status_id', 1);
}

// DO: Use accessors and mutators
protected function fullName(): Attribute
{
    return Attribute::make(
        get: fn () => "{$this->first_name} {$this->last_name}",
    );
}
```

#### Validation Rules

```php
// Standard validation patterns used in this project

// Required fields
'field' => ['required', 'string', 'max:255']

// Email with uniqueness
'email' => ['required', 'email', 'unique:users,email']

// Update with unique ignore
'email' => ['required', 'email', Rule::unique('users')->ignore($this->user)]

// Numeric ranges
'price' => ['required', 'numeric', 'min:0', 'max:999999.99']

// Date validation
'start_date' => ['required', 'date', 'after_or_equal:today']
'end_date' => ['required', 'date', 'after:start_date']

// Foreign key existence
'customer_id' => ['required', 'exists:customers,id']

// Enum/In validation
'type' => ['required', 'in:gown,suit']
'status' => ['required', Rule::in(['pending', 'confirmed', 'cancelled'])]

// Array validation
'items' => ['required', 'array', 'min:1']
'items.*.id' => ['required', 'exists:inventories,id']
'items.*.quantity' => ['required', 'integer', 'min:1']

// Conditional validation
'deposit_amount' => ['required_if:requires_deposit,true', 'numeric', 'min:0']

// File validation
'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048']
```

#### Documentation Standards

##### PHPDoc for Methods
```php
/**
 * Calculate the total rental cost including extensions.
 *
 * @param  \App\Models\Rental  $rental
 * @param  int  $extensionDays
 * @return float
 *
 * @throws \InvalidArgumentException
 */
public function calculateRentalCost(Rental $rental, int $extensionDays = 0): float
{
    // Implementation
}
```

##### Inline Comments
```php
// Calculate overdue days (negative if not yet due)
$overdueDays = now()->diffInDays($rental->due_date, false);

// Apply late fee only if actually overdue
if ($overdueDays > 0) {
    $lateFee = $overdueDays * self::DAILY_LATE_FEE;
}
```

#### Git Commit Standards

```
Format: <type>(<scope>): <description>

Types:
- feat: New feature
- fix: Bug fix
- docs: Documentation changes
- style: Code style changes (formatting)
- refactor: Code refactoring
- test: Adding tests
- chore: Maintenance tasks

Examples:
feat(customers): add body measurements tracking
fix(rentals): resolve overdue calculation for extended rentals
docs(api): update invoice endpoints documentation
refactor(inventory): extract availability checking to service
```

---

### 1.7 Repository Structure

```
LSRSV2/
├── app/
│   ├── Http/
│   │   ├── Controllers/           # 18 controllers
│   │   │   ├── AuthController.php
│   │   │   ├── OtpController.php
│   │   │   ├── DashboardController.php
│   │   │   ├── CustomerController.php
│   │   │   ├── CustomerStatusController.php
│   │   │   ├── InventoryController.php
│   │   │   ├── InventoryImageController.php
│   │   │   ├── InventoryStatusController.php
│   │   │   ├── ReservationController.php
│   │   │   ├── ReservationItemController.php
│   │   │   ├── ReservationStatusController.php
│   │   │   ├── RentalController.php
│   │   │   ├── RentalStatusController.php
│   │   │   ├── InvoiceController.php
│   │   │   ├── InvoiceItemController.php
│   │   │   ├── PaymentController.php
│   │   │   └── PaymentStatusController.php
│   │   ├── Middleware/
│   │   │   └── Authenticate.php
│   │   └── Requests/              # 28 form request validators
│   │       ├── Auth/
│   │       ├── Customer/
│   │       ├── Inventory/
│   │       ├── Reservation/
│   │       ├── Rental/
│   │       ├── Invoice/
│   │       └── Payment/
│   ├── Mail/
│   │   └── OtpMail.php
│   ├── Models/                    # 20 Eloquent models
│   │   ├── User.php
│   │   ├── Customer.php
│   │   ├── CustomerStatus.php
│   │   ├── Inventory.php
│   │   ├── InventoryVariant.php
│   │   ├── InventoryImage.php
│   │   ├── InventoryStatus.php
│   │   ├── InventoryMovement.php
│   │   ├── Reservation.php
│   │   ├── ReservationItem.php
│   │   ├── ReservationItemAllocation.php
│   │   ├── ReservationStatus.php
│   │   ├── Rental.php
│   │   ├── RentalStatus.php
│   │   ├── Invoice.php
│   │   ├── InvoiceItem.php
│   │   ├── Payment.php
│   │   ├── PaymentStatus.php
│   │   ├── DepositReturn.php
│   │   └── Item.php
│   ├── Policies/                  # Authorization policies
│   ├── Providers/
│   │   └── AppServiceProvider.php
│   ├── Services/                  # Business logic services
│   │   ├── DepositService.php
│   │   └── OtpService.php
│   └── View/
│       └── Components/
├── bootstrap/
│   ├── app.php
│   └── providers.php
├── config/                        # Configuration files
│   ├── app.php
│   ├── auth.php
│   ├── cache.php
│   ├── database.php
│   ├── filesystems.php
│   ├── logging.php
│   ├── mail.php
│   ├── queue.php
│   ├── services.php
│   └── session.php
├── database/
│   ├── factories/                 # Model factories for testing
│   ├── migrations/                # 19 database migrations
│   │   ├── 0001_01_01_000000_create_users_table.php
│   │   ├── 0001_01_01_000001_create_cache_table.php
│   │   ├── 0001_01_01_000002_create_jobs_table.php
│   │   ├── 2024_01_01_000001_create_customer_statuses_table.php
│   │   ├── 2024_01_01_000002_create_payment_statuses_table.php
│   │   ├── 2024_01_01_000003_create_rental_statuses_table.php
│   │   ├── 2024_01_01_000004_create_inventory_statuses_table.php
│   │   ├── 2024_01_01_000005_create_reservation_statuses_table.php
│   │   ├── 2024_01_01_000006_create_customers_table.php
│   │   ├── 2024_01_01_000007_create_inventories_table.php
│   │   ├── 2024_01_01_000008_create_reservations_table.php
│   │   ├── 2024_01_01_000009_create_rentals_table.php
│   │   ├── 2024_01_01_000010_create_invoices_table.php
│   │   ├── 2024_01_01_000011_create_payments_table.php
│   │   ├── 2024_01_01_000012_create_reservation_items_table.php
│   │   ├── 2024_01_01_000013_create_invoice_items_table.php
│   │   ├── 2024_01_01_000014_create_inventory_images_table.php
│   │   ├── 2024_01_01_000015_add_performance_indexes.php
│   │   └── 2024_01_01_000016_create_deposit_returns_table.php
│   └── seeders/                   # 16 database seeders
│       ├── DatabaseSeeder.php
│       ├── CustomerStatusSeeder.php
│       ├── CustomerSeeder.php
│       ├── InventoryStatusSeeder.php
│       ├── InventorySeeder.php
│       └── ...
├── public/
│   ├── index.php                  # Application entry point
│   ├── .htaccess
│   └── build/                     # Compiled assets (Vite)
├── resources/
│   ├── css/
│   │   └── app.css               # TailwindCSS entry
│   ├── js/
│   │   └── app.js                # JavaScript entry
│   ├── icons/                     # SVG icons
│   └── views/
│       ├── layouts/
│       │   └── app.blade.php     # Main layout
│       ├── components/
│       │   ├── card.blade.php
│       │   ├── sidebar.blade.php
│       │   └── ...
│       ├── auth/
│       │   ├── login.blade.php
│       │   ├── register.blade.php
│       │   └── ...
│       ├── dashboard/
│       ├── customers/
│       ├── inventories/
│       ├── reservations/
│       ├── rentals/
│       ├── invoices/
│       ├── payments/
│       ├── reports/
│       └── mail/
├── routes/
│   ├── web.php                    # Web routes (90+ endpoints)
│   ├── api.php                    # API routes
│   └── console.php                # Console commands
├── storage/
│   ├── app/
│   │   └── public/               # Uploaded files
│   ├── framework/
│   │   ├── cache/
│   │   ├── sessions/
│   │   └── views/
│   └── logs/
├── tests/
│   ├── Feature/
│   └── Unit/
├── .env.example                   # Environment template
├── .gitignore
├── artisan                        # CLI tool
├── composer.json                  # PHP dependencies
├── package.json                   # Node dependencies
├── phpunit.xml                    # Test configuration
├── vite.config.js                 # Vite configuration
├── tailwind.config.js             # TailwindCSS configuration
└── README.md                      # Project documentation
```

---

## 2. Architecture and Module Breakdown

### 2.1 High-Level System Architecture

```
┌─────────────────────────────────────────────────────────────────────────────┐
│                              PRESENTATION LAYER                              │
│  ┌─────────────────────────────────────────────────────────────────────────┐│
│  │                         Web Browser (Client)                            ││
│  │  ┌─────────────┐  ┌─────────────┐  ┌─────────────┐  ┌─────────────┐    ││
│  │  │  Dashboard  │  │  Customers  │  │  Inventory  │  │  Rentals    │    ││
│  │  └─────────────┘  └─────────────┘  └─────────────┘  └─────────────┘    ││
│  │  ┌─────────────┐  ┌─────────────┐  ┌─────────────┐  ┌─────────────┐    ││
│  │  │Reservations │  │  Invoices   │  │  Payments   │  │   Reports   │    ││
│  │  └─────────────┘  └─────────────┘  └─────────────┘  └─────────────┘    ││
│  └─────────────────────────────────────────────────────────────────────────┘│
│                    TailwindCSS + Alpine.js + Chart.js                        │
└─────────────────────────────────────────────────────────────────────────────┘
                                      │
                                      ▼
┌─────────────────────────────────────────────────────────────────────────────┐
│                              APPLICATION LAYER                               │
│  ┌─────────────────────────────────────────────────────────────────────────┐│
│  │                        Laravel Framework (PHP 8.2+)                     ││
│  │                                                                         ││
│  │  ┌──────────────────────────────────────────────────────────────────┐  ││
│  │  │                           ROUTING                                 │  ││
│  │  │                    routes/web.php (90+ endpoints)                 │  ││
│  │  └──────────────────────────────────────────────────────────────────┘  ││
│  │                                  │                                      ││
│  │                                  ▼                                      ││
│  │  ┌──────────────────────────────────────────────────────────────────┐  ││
│  │  │                         MIDDLEWARE                                │  ││
│  │  │     Authentication │ CSRF Protection │ Rate Limiting              │  ││
│  │  └──────────────────────────────────────────────────────────────────┘  ││
│  │                                  │                                      ││
│  │                                  ▼                                      ││
│  │  ┌──────────────────────────────────────────────────────────────────┐  ││
│  │  │                        CONTROLLERS (18)                           │  ││
│  │  │  Auth │ Dashboard │ Customer │ Inventory │ Reservation │ Rental  │  ││
│  │  │  Invoice │ Payment │ Status Controllers                          │  ││
│  │  └──────────────────────────────────────────────────────────────────┘  ││
│  │                                  │                                      ││
│  │              ┌───────────────────┼───────────────────┐                  ││
│  │              ▼                   ▼                   ▼                  ││
│  │  ┌─────────────────┐  ┌─────────────────┐  ┌─────────────────┐         ││
│  │  │  Form Requests  │  │    Services     │  │   Blade Views   │         ││
│  │  │  (Validation)   │  │ (Business Logic)│  │   (Templates)   │         ││
│  │  │     (28)        │  │      (2+)       │  │     (28+)       │         ││
│  │  └─────────────────┘  └─────────────────┘  └─────────────────┘         ││
│  │                                  │                                      ││
│  │                                  ▼                                      ││
│  │  ┌──────────────────────────────────────────────────────────────────┐  ││
│  │  │                         MODELS (20)                               │  ││
│  │  │           Eloquent ORM with Relationships & Scopes                │  ││
│  │  └──────────────────────────────────────────────────────────────────┘  ││
│  └─────────────────────────────────────────────────────────────────────────┘│
└─────────────────────────────────────────────────────────────────────────────┘
                                      │
                                      ▼
┌─────────────────────────────────────────────────────────────────────────────┐
│                                DATA LAYER                                    │
│  ┌─────────────────────────────────────────────────────────────────────────┐│
│  │                         MySQL 8.0+ Database                             ││
│  │                                                                         ││
│  │  ┌─────────────┐  ┌─────────────┐  ┌─────────────┐  ┌─────────────┐    ││
│  │  │    users    │  │  customers  │  │ inventories │  │reservations │    ││
│  │  └─────────────┘  └─────────────┘  └─────────────┘  └─────────────┘    ││
│  │  ┌─────────────┐  ┌─────────────┐  ┌─────────────┐  ┌─────────────┐    ││
│  │  │   rentals   │  │  invoices   │  │  payments   │  │   images    │    ││
│  │  └─────────────┘  └─────────────┘  └─────────────┘  └─────────────┘    ││
│  │  ┌───────────────────────────────────────────────────────────────────┐ ││
│  │  │                    Status Tables (5)                               │ ││
│  │  │   customer_statuses │ inventory_statuses │ reservation_statuses   │ ││
│  │  │   rental_statuses │ payment_statuses                               │ ││
│  │  └───────────────────────────────────────────────────────────────────┘ ││
│  │                         15 Core Tables + 5 Status Tables                ││
│  │                         30+ Foreign Key Constraints                     ││
│  │                         15+ Performance Indexes                         ││
│  └─────────────────────────────────────────────────────────────────────────┘│
│                                                                              │
│  ┌────────────────────────────┐  ┌────────────────────────────────────────┐ │
│  │   File Storage (Local)     │  │   Redis Cache (Optional)               │ │
│  │   storage/app/public       │  │   Sessions, Cache, Queues              │ │
│  └────────────────────────────┘  └────────────────────────────────────────┘ │
└─────────────────────────────────────────────────────────────────────────────┘
```

### 2.2 System Flow Diagram

```
┌─────────────────────────────────────────────────────────────────────────────┐
│                          RENTAL SYSTEM WORKFLOW                              │
└─────────────────────────────────────────────────────────────────────────────┘

┌─────────────┐     ┌─────────────┐     ┌─────────────┐     ┌─────────────┐
│  CUSTOMER   │────▶│ RESERVATION │────▶│   RENTAL    │────▶│   RETURN    │
│ MANAGEMENT  │     │   SYSTEM    │     │  PROCESS    │     │  PROCESS    │
└─────────────┘     └─────────────┘     └─────────────┘     └─────────────┘
      │                   │                   │                   │
      ▼                   ▼                   ▼                   ▼
┌─────────────┐     ┌─────────────┐     ┌─────────────┐     ┌─────────────┐
│  Register   │     │Browse Items │     │Release Item │     │Check Return │
│  Customer   │     │  by Date    │     │ to Customer │     │  Condition  │
└─────────────┘     └─────────────┘     └─────────────┘     └─────────────┘
      │                   │                   │                   │
      ▼                   ▼                   ▼                   ▼
┌─────────────┐     ┌─────────────┐     ┌─────────────┐     ┌─────────────┐
│    Store    │     │   Create    │     │   Update    │     │   Update    │
│Measurements │     │ Reservation │     │  Inventory  │     │  Inventory  │
└─────────────┘     └─────────────┘     │   Status    │     │  Condition  │
                          │             └─────────────┘     └─────────────┘
                          ▼                   │                   │
                    ┌─────────────┐           │                   │
                    │  Confirm /  │           │                   │
                    │   Cancel    │           │                   │
                    └─────────────┘           │                   │
                          │                   │                   │
                          └─────────┬─────────┴───────────────────┘
                                    │
                                    ▼
┌─────────────────────────────────────────────────────────────────────────────┐
│                            BILLING WORKFLOW                                  │
└─────────────────────────────────────────────────────────────────────────────┘

     ┌─────────────┐          ┌─────────────┐          ┌─────────────┐
     │   CREATE    │─────────▶│   RECORD    │─────────▶│   CLOSE     │
     │   INVOICE   │          │   PAYMENT   │          │   INVOICE   │
     └─────────────┘          └─────────────┘          └─────────────┘
           │                        │                        │
           ▼                        ▼                        ▼
     ┌─────────────┐          ┌─────────────┐          ┌─────────────┐
     │ Add Items   │          │ Select      │          │ Verify      │
     │ Calculate   │          │ Payment     │          │ Full        │
     │ Totals      │          │ Method      │          │ Payment     │
     └─────────────┘          └─────────────┘          └─────────────┘
           │                        │                        │
           ▼                        ▼                        ▼
     ┌─────────────┐          ┌─────────────┐          ┌─────────────┐
     │ Apply       │          │ Update      │          │ Process     │
     │ Discounts   │          │ Balance     │          │ Deposit     │
     │ & Tax       │          │             │          │ Return      │
     └─────────────┘          └─────────────┘          └─────────────┘
           │                        │                        │
           ▼                        ▼                        ▼
     ┌─────────────┐          ┌─────────────┐          ┌─────────────┐
     │ Generate    │          │ Generate    │          │ Generate    │
     │ Invoice PDF │          │ Receipt PDF │          │ Final PDF   │
     └─────────────┘          └─────────────┘          └─────────────┘
```

### 2.3 Module Breakdown by Subsystem

---

#### Module 1: Authentication & User Management

**Purpose:** Secure user authentication and session management

**Components:**
| Component | Type | File Path | Responsibility |
|-----------|------|-----------|----------------|
| AuthController | Controller | `app/Http/Controllers/AuthController.php` | Login, logout, registration, password reset |
| OtpController | Controller | `app/Http/Controllers/OtpController.php` | OTP generation, verification, resend |
| OtpService | Service | `app/Services/OtpService.php` | OTP business logic |
| OtpMail | Mailable | `app/Mail/OtpMail.php` | OTP email template |
| User | Model | `app/Models/User.php` | User entity |

**Database Tables:**
```
users
├── id (PK)
├── name
├── email (unique)
├── email_verified_at
├── password (hashed)
├── remember_token
├── otp_code
├── otp_expires_at
└── timestamps
```

**Routes:**
```
GET    /login                  → AuthController@showLogin
POST   /login                  → AuthController@login
GET    /register               → AuthController@showRegister
POST   /register               → AuthController@register
POST   /logout                 → AuthController@logout
GET    /password/forgot        → AuthController@showForgotPassword
POST   /password/forgot        → AuthController@sendOtp
GET    /password/verify        → AuthController@showVerifyOtp
POST   /password/verify        → OtpController@verify
POST   /password/resend-otp    → OtpController@resend
GET    /password/reset         → AuthController@showResetPassword
POST   /password/reset         → AuthController@resetPassword
```

**Status Workflow:**
```
[Guest] ──login──▶ [Authenticated] ──logout──▶ [Guest]
                         │
                    [Password Reset]
                         │
            ┌────────────┼────────────┐
            ▼            ▼            ▼
      [Request OTP] [Verify OTP] [Reset Password]
```

---

#### Module 2: Customer Management

**Purpose:** Manage customer information, measurements, and history

**Components:**
| Component | Type | File Path | Responsibility |
|-----------|------|-----------|----------------|
| CustomerController | Controller | `app/Http/Controllers/CustomerController.php` | CRUD, reports, history |
| CustomerStatusController | Controller | `app/Http/Controllers/CustomerStatusController.php` | Status management |
| Customer | Model | `app/Models/Customer.php` | Customer entity |
| CustomerStatus | Model | `app/Models/CustomerStatus.php` | Status entity |
| StoreCustomerRequest | Request | `app/Http/Requests/StoreCustomerRequest.php` | Create validation |
| UpdateCustomerRequest | Request | `app/Http/Requests/UpdateCustomerRequest.php` | Update validation |

**Database Tables:**
```
customers                          customer_statuses
├── id (PK)                        ├── id (PK)
├── first_name                     ├── name
├── last_name                      ├── description
├── email (unique)                 └── timestamps
├── phone
├── address
├── measurements (JSON)
│   ├── bust
│   ├── waist
│   ├── hips
│   ├── height
│   └── ...
├── customer_status_id (FK)
├── timestamps
└── deleted_at
```

**Routes:**
```
GET    /customers                    → CustomerController@index
GET    /customers/create             → CustomerController@create
POST   /customers                    → CustomerController@store
GET    /customers/{customer}         → CustomerController@show
GET    /customers/{customer}/edit    → CustomerController@edit
PUT    /customers/{customer}         → CustomerController@update
DELETE /customers/{customer}         → CustomerController@destroy
GET    /customers/{customer}/history → CustomerController@history
GET    /customers/{customer}/report  → CustomerController@report (PDF)
GET    /customers/stats              → CustomerController@stats
```

**Status Workflow:**
```
[Active] ◀──────▶ [Inactive]
    │                 │
    └────────┬────────┘
             ▼
       [Blacklisted]
```

---

#### Module 3: Inventory Management

**Purpose:** Manage rental items (gowns/suits), images, and availability

**Components:**
| Component | Type | File Path | Responsibility |
|-----------|------|-----------|----------------|
| InventoryController | Controller | `app/Http/Controllers/InventoryController.php` | CRUD, availability, bulk ops |
| InventoryImageController | Controller | `app/Http/Controllers/InventoryImageController.php` | Image management |
| InventoryStatusController | Controller | `app/Http/Controllers/InventoryStatusController.php` | Status management |
| Inventory | Model | `app/Models/Inventory.php` | Inventory item entity |
| InventoryImage | Model | `app/Models/InventoryImage.php` | Image entity |
| InventoryStatus | Model | `app/Models/InventoryStatus.php` | Status entity |
| InventoryVariant | Model | `app/Models/InventoryVariant.php` | Variant grouping |
| InventoryMovement | Model | `app/Models/InventoryMovement.php` | Movement tracking |

**Database Tables:**
```
inventories                        inventory_images
├── id (PK)                        ├── id (PK)
├── sku (unique, auto-gen)         ├── inventory_id (FK)
├── name                           ├── image_path
├── description                    ├── is_primary
├── type (gown/suit)               └── timestamps
├── size
├── color                          inventory_statuses
├── rental_price                   ├── id (PK)
├── selling_price (nullable)       ├── name
├── is_sellable                    ├── description
├── condition                      └── timestamps
├── inventory_status_id (FK)
├── inventory_variant_id (FK)      inventory_variants
├── timestamps                     ├── id (PK)
└── deleted_at                     ├── name
                                   └── timestamps
```

**Routes:**
```
GET    /inventories                        → InventoryController@index
GET    /inventories/create                 → InventoryController@create
POST   /inventories                        → InventoryController@store
GET    /inventories/{inventory}            → InventoryController@show
GET    /inventories/{inventory}/edit       → InventoryController@edit
PUT    /inventories/{inventory}            → InventoryController@update
DELETE /inventories/{inventory}            → InventoryController@destroy
GET    /inventories/available              → InventoryController@available
POST   /inventories/bulk-update            → InventoryController@bulkUpdate
POST   /inventories/{inventory}/images     → InventoryImageController@store
DELETE /inventories/images/{image}         → InventoryImageController@destroy
PUT    /inventories/images/{image}/primary → InventoryImageController@setPrimary
```

**SKU Generation Pattern:**
```
Type: Gown  → GWN-001, GWN-002, GWN-003, ...
Type: Suit  → SUT-001, SUT-002, SUT-003, ...
```

**Status Workflow:**
```
[Available] ──reserve──▶ [Reserved] ──release──▶ [Rented]
     ▲                                              │
     │                                              │
     └────────────────return─────────────────────────┘
     │
     ▼
[Under Maintenance] ◀──damage──▶ [Retired]
```

---

#### Module 4: Reservation System

**Purpose:** Manage item reservations and bookings

**Components:**
| Component | Type | File Path | Responsibility |
|-----------|------|-----------|----------------|
| ReservationController | Controller | `app/Http/Controllers/ReservationController.php` | CRUD, browse, confirm/cancel |
| ReservationItemController | Controller | `app/Http/Controllers/ReservationItemController.php` | Item management |
| ReservationStatusController | Controller | `app/Http/Controllers/ReservationStatusController.php` | Status management |
| Reservation | Model | `app/Models/Reservation.php` | Reservation entity |
| ReservationItem | Model | `app/Models/ReservationItem.php` | Reserved items |
| ReservationItemAllocation | Model | `app/Models/ReservationItemAllocation.php` | Item allocation |
| ReservationStatus | Model | `app/Models/ReservationStatus.php` | Status entity |

**Database Tables:**
```
reservations                       reservation_items
├── id (PK)                        ├── id (PK)
├── reservation_number (unique)    ├── reservation_id (FK)
├── customer_id (FK)               ├── inventory_id (FK)
├── start_date                     ├── quantity
├── end_date                       ├── unit_price
├── notes                          └── timestamps
├── deposit_required
├── reservation_status_id (FK)     reservation_statuses
├── created_by (FK → users)        ├── id (PK)
├── timestamps                     ├── name
└── deleted_at                     ├── description
                                   └── timestamps
```

**Routes:**
```
GET    /reservations                         → ReservationController@index
GET    /reservations/create                  → ReservationController@create
POST   /reservations                         → ReservationController@store
GET    /reservations/{reservation}           → ReservationController@show
GET    /reservations/{reservation}/edit      → ReservationController@edit
PUT    /reservations/{reservation}           → ReservationController@update
DELETE /reservations/{reservation}           → ReservationController@destroy
GET    /reservations/browse                  → ReservationController@browse
POST   /reservations/{reservation}/confirm   → ReservationController@confirm
POST   /reservations/{reservation}/cancel    → ReservationController@cancel
POST   /reservations/{reservation}/items     → ReservationItemController@store
DELETE /reservations/items/{item}            → ReservationItemController@destroy
```

**Status Workflow:**
```
                    ┌──────────────────────────────────┐
                    │                                  │
                    ▼                                  │
[Draft] ──save──▶ [Pending] ──confirm──▶ [Confirmed] ──┤
                    │                         │        │
                    │                         │        │
                 cancel                   convert      │
                    │                         │        │
                    ▼                         ▼        │
              [Cancelled]               [Completed] ◀──┘
                                        (Rental Created)
```

---

#### Module 5: Rental Management

**Purpose:** Manage active rentals, releases, returns, and extensions

**Components:**
| Component | Type | File Path | Responsibility |
|-----------|------|-----------|----------------|
| RentalController | Controller | `app/Http/Controllers/RentalController.php` | CRUD, release, return, extend |
| RentalStatusController | Controller | `app/Http/Controllers/RentalStatusController.php` | Status management |
| DepositService | Service | `app/Services/DepositService.php` | Deposit calculations |
| Rental | Model | `app/Models/Rental.php` | Rental entity |
| RentalStatus | Model | `app/Models/RentalStatus.php` | Status entity |
| DepositReturn | Model | `app/Models/DepositReturn.php` | Deposit return tracking |

**Database Tables:**
```
rentals                            rental_statuses
├── id (PK)                        ├── id (PK)
├── rental_number (unique)         ├── name
├── reservation_id (FK)            ├── description
├── customer_id (FK)               └── timestamps
├── inventory_id (FK)
├── start_date                     deposit_returns
├── due_date                       ├── id (PK)
├── return_date (nullable)         ├── rental_id (FK)
├── extension_days                 ├── amount
├── extension_reason               ├── reason
├── rental_price                   ├── processed_by (FK → users)
├── deposit_amount                 ├── processed_at
├── deposit_status                 └── timestamps
├── return_condition
├── return_notes
├── rental_status_id (FK)
├── released_by (FK → users)
├── released_at
├── returned_by (FK → users)
├── returned_at
├── timestamps
└── deleted_at
```

**Routes:**
```
GET    /rentals                       → RentalController@index
GET    /rentals/create                → RentalController@create
POST   /rentals                       → RentalController@store
GET    /rentals/{rental}              → RentalController@show
GET    /rentals/{rental}/edit         → RentalController@edit
PUT    /rentals/{rental}              → RentalController@update
DELETE /rentals/{rental}              → RentalController@destroy
POST   /rentals/{rental}/release      → RentalController@release
POST   /rentals/{rental}/return       → RentalController@return
POST   /rentals/{rental}/extend       → RentalController@extend
GET    /rentals/overdue               → RentalController@overdue
GET    /rentals/active                → RentalController@active
```

**Status Workflow:**
```
[Pending] ──release──▶ [Active] ──return──▶ [Returned]
    │                     │                     │
    │                     │                     │
    │                  extend                   │
    │                     │                     │
    │                     ▼                     │
    │               [Extended]                  │
    │                     │                     │
    │                     │                     │
    │                  return                   │
    │                     │                     │
    │                     ▼                     │
    │               [Returned]                  │
    │                     │                     │
    │                     │                     │
    │            ┌────────┴────────┐            │
    │            ▼                 ▼            │
    │     [Deposit Held]    [Deposit Returned]  │
    │            │                              │
    │            ▼                              │
    │     [Deposit Forfeited]                   │
    │                                           │
    └─────────────cancel─────────────────────────
                    │
                    ▼
              [Cancelled]

** Overdue Detection (Automatic) **
[Active] ──due_date passed──▶ [Overdue] (flagged)
```

**Deposit States:**
```
held      → Deposit collected, awaiting return
returned  → Deposit refunded to customer
forfeited → Deposit retained (damage/non-return)
```

---

#### Module 6: Invoicing & Billing

**Purpose:** Generate and manage invoices with line items

**Components:**
| Component | Type | File Path | Responsibility |
|-----------|------|-----------|----------------|
| InvoiceController | Controller | `app/Http/Controllers/InvoiceController.php` | CRUD, PDF generation |
| InvoiceItemController | Controller | `app/Http/Controllers/InvoiceItemController.php` | Line item management |
| Invoice | Model | `app/Models/Invoice.php` | Invoice entity |
| InvoiceItem | Model | `app/Models/InvoiceItem.php` | Line item entity |

**Database Tables:**
```
invoices                           invoice_items
├── id (PK)                        ├── id (PK)
├── invoice_number (unique)        ├── invoice_id (FK)
├── customer_id (FK)               ├── description
├── reservation_id (FK, nullable)  ├── quantity
├── rental_id (FK, nullable)       ├── unit_price
├── invoice_type                   ├── subtotal
├── subtotal                       └── timestamps
├── discount_type
├── discount_value
├── discount_amount
├── tax_rate
├── tax_amount
├── total
├── amount_paid
├── balance
├── due_date
├── notes
├── payment_status_id (FK)
├── created_by (FK → users)
├── timestamps
└── deleted_at
```

**Routes:**
```
GET    /invoices                      → InvoiceController@index
GET    /invoices/create               → InvoiceController@create
POST   /invoices                      → InvoiceController@store
GET    /invoices/{invoice}            → InvoiceController@show
GET    /invoices/{invoice}/edit       → InvoiceController@edit
PUT    /invoices/{invoice}            → InvoiceController@update
DELETE /invoices/{invoice}            → InvoiceController@destroy
GET    /invoices/{invoice}/pdf        → InvoiceController@generatePdf
GET    /invoices/monitoring           → InvoiceController@monitoring
POST   /invoices/{invoice}/items      → InvoiceItemController@store
DELETE /invoices/items/{item}         → InvoiceItemController@destroy
```

**Invoice Types:**
```
reservation → Created when reservation is confirmed (deposit invoice)
rental      → Created when items are released
final       → Created upon return (includes balance, late fees)
```

**Calculation Logic:**
```
Subtotal      = Σ (item.quantity × item.unit_price)
Discount      = (discount_type == 'percentage') 
                  ? subtotal × (discount_value / 100)
                  : discount_value
Tax Amount    = (subtotal - discount) × (tax_rate / 100)
Total         = subtotal - discount + tax_amount
Balance       = total - amount_paid
```

---

#### Module 7: Payment Processing

**Purpose:** Record and track payments against invoices

**Components:**
| Component | Type | File Path | Responsibility |
|-----------|------|-----------|----------------|
| PaymentController | Controller | `app/Http/Controllers/PaymentController.php` | CRUD, receipt generation |
| PaymentStatusController | Controller | `app/Http/Controllers/PaymentStatusController.php` | Status management |
| Payment | Model | `app/Models/Payment.php` | Payment entity |
| PaymentStatus | Model | `app/Models/PaymentStatus.php` | Status entity |

**Database Tables:**
```
payments                           payment_statuses
├── id (PK)                        ├── id (PK)
├── payment_number (unique)        ├── name
├── invoice_id (FK)                ├── description
├── amount                         └── timestamps
├── payment_method
├── payment_date
├── reference_number
├── notes
├── payment_status_id (FK)
├── processed_by (FK → users)
├── timestamps
└── deleted_at
```

**Routes:**
```
GET    /payments                       → PaymentController@index
GET    /payments/create                → PaymentController@create
POST   /payments                       → PaymentController@store
GET    /payments/{payment}             → PaymentController@show
GET    /payments/{payment}/edit        → PaymentController@edit
PUT    /payments/{payment}             → PaymentController@update
DELETE /payments/{payment}             → PaymentController@destroy
GET    /payments/{payment}/receipt     → PaymentController@generateReceipt
```

**Payment Methods:**
```
cash         → Cash payment
card         → Credit/Debit card
bank         → Bank transfer
gcash        → GCash mobile payment
paymaya      → PayMaya mobile payment
```

**Payment Status Workflow:**
```
[Pending] ──process──▶ [Completed]
    │                      │
    │                      │
    ▼                      ▼
[Failed]              [Refunded]
    │
    ▼
[Cancelled]
```

---

#### Module 8: Dashboard & Reporting

**Purpose:** Provide analytics, KPIs, and exportable reports

**Components:**
| Component | Type | File Path | Responsibility |
|-----------|------|-----------|----------------|
| DashboardController | Controller | `app/Http/Controllers/DashboardController.php` | Metrics, charts |
| (Report Views) | Blade | `resources/views/reports/` | Report templates |

**Routes:**
```
GET  /                           → DashboardController@index
GET  /dashboard/metrics          → DashboardController@metrics (JSON)
GET  /dashboard/kpis             → DashboardController@kpis
GET  /dashboard/charts/{type}    → DashboardController@chart
GET  /reports/customers          → DashboardController@customerReport
GET  /reports/inventory          → DashboardController@inventoryReport
GET  /reports/rentals            → DashboardController@rentalReport
GET  /reports/revenue            → DashboardController@revenueReport
```

**KPI Metrics:**
```
┌─────────────────────────────────────────────────────────────────────┐
│                        DASHBOARD KPIs                                │
├─────────────────────────────────────────────────────────────────────┤
│  Total Revenue (Today/Week/Month/Year)                               │
│  Active Rentals Count                                                │
│  Pending Reservations Count                                          │
│  Overdue Rentals Count                                               │
│  New Customers (This Month)                                          │
│  Available Inventory Count                                           │
│  Revenue Growth (% vs Previous Period)                               │
│  Average Rental Duration                                             │
│  Payment Collection Rate                                             │
│  Customer Retention Rate                                             │
└─────────────────────────────────────────────────────────────────────┘
```

**Chart Types (22 Total):**
```
Revenue Charts:
├── Daily Revenue (Line)
├── Weekly Revenue (Bar)
├── Monthly Revenue (Bar)
├── Revenue by Payment Method (Pie)
└── Revenue Trend (Line)

Rental Charts:
├── Rentals by Status (Doughnut)
├── Rentals by Item Type (Pie)
├── Daily Rental Activity (Line)
├── Popular Items (Horizontal Bar)
└── Rental Duration Distribution (Histogram)

Customer Charts:
├── New vs Returning (Pie)
├── Customer Growth (Line)
├── Top Customers by Revenue (Bar)
└── Customer by Status (Doughnut)

Inventory Charts:
├── Inventory by Status (Doughnut)
├── Inventory by Type (Pie)
├── Inventory Utilization (Gauge)
├── Low Stock Alerts (Bar)
└── Condition Distribution (Pie)

Reservation Charts:
├── Reservations by Status (Doughnut)
├── Booking Trend (Line)
└── Conversion Rate (Gauge)
```

---

## 3. Setup and Execution Guide

### Prerequisites

- PHP 8.2 or higher
- Composer 2.x
- Node.js 18.x or higher with npm
- MySQL 8.0+ or MariaDB 10.6+
- Git

### Installation Steps

#### 1. Clone the Repository
```bash
git clone https://github.com/your-org/LSRSV2.git
cd LSRSV2
```

#### 2. Install PHP Dependencies
```bash
composer install
```

#### 3. Install Node Dependencies
```bash
npm install
```

#### 4. Environment Setup
```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

#### 5. Configure Environment Variables
Edit `.env` file with your settings:
```env
APP_NAME="Love & Styles RMS"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=lsrsv2
DB_USERNAME=your_username
DB_PASSWORD=your_password

MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your_email@gmail.com
MAIL_PASSWORD=your_app_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@lovestyles.com
MAIL_FROM_NAME="Love & Styles"
```

#### 6. Database Setup
```bash
# Create database (in MySQL)
mysql -u root -p -e "CREATE DATABASE lsrsv2 CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# Run migrations
php artisan migrate

# Seed initial data
php artisan db:seed
```

#### 7. Storage Link
```bash
php artisan storage:link
```

#### 8. Build Frontend Assets
```bash
# Development
npm run dev

# Production
npm run build
```

#### 9. Start Development Server
```bash
# Option 1: PHP built-in server
php artisan serve

# Option 2: Concurrent servers (PHP + Vite)
npm run dev:all
```

### Default Credentials

After seeding, use these credentials:
```
Email: admin@lovestyles.com
Password: password
```

### Verification Commands

```bash
# Check Laravel installation
php artisan --version

# Verify database connection
php artisan db:show

# Check route list
php artisan route:list

# Run tests
php artisan test

# Check code style
./vendor/bin/pint --test
```

### Production Deployment

```bash
# Optimize for production
composer install --optimize-autoloader --no-dev
php artisan config:cache
php artisan route:cache
php artisan view:cache
npm run build

# Set permissions (Linux)
chmod -R 755 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

---

## Document Information

| Field | Value |
|-------|-------|
| **Document Title** | LSRSV2 System Definition and Architecture |
| **Version** | 2.0 |
| **Last Updated** | March 2026 |
| **Author** | Development Team |
| **Status** | Implementation Ready |

---

*End of Document*
