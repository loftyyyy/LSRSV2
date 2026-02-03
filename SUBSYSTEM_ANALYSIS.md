# LSRSV2 Codebase Architecture Analysis

**Project Type:** Laravel 12 + Vue.js/Blade Template Rental Management System  
**Total PHP Code:** ~30,760 lines  
**HTTP Code:** ~6,439 lines  
**Last Analysis:** February 3, 2025

---

## EXECUTIVE SUMMARY

The **LSRSV2** (Love & Styles Rental System v2) is a comprehensive web application for managing formal wear rentals. It implements a complete business workflow including customer management, inventory tracking, reservations, rentals, invoicing, and payment processing.

### Overall Architecture Status
- **Completeness:** ~85% (mostly feature-complete with refinements ongoing)
- **Stability:** High (models and migrations complete)
- **Code Quality:** Medium (needs cleanup and error handling improvements)
- **Test Coverage:** Minimal (only example tests exist)

---

## SUBSYSTEM IDENTIFICATION

The application is organized into **8 major subsystems**, each handling a distinct business domain:

### 1. **AUTHENTICATION & AUTHORIZATION** 
**Status:** ✅ COMPLETE (95%)

**Components:**
- AuthController (login, registration, password reset)
- OtpController (OTP-based password recovery)
- OtpService (email-based OTP generation)
- Auth Middleware integration

**Implementation Details:**
- User model with Laravel Authenticatable trait
- Password hashing with Hash facade
- Session-based authentication
- OTP email verification (via OtpMail)
- Rate limiting on password verification (5 attempts per 15 minutes)

**Known Issues:**
- ⚠️ Missing `use Throwable` in CustomerController
- ⚠️ Password verification vulnerable to brute force (needs stronger rate limiting)
- ⚠️ OTP implementation could be enhanced with token expiry

**Dependencies:**
- User model
- Email system (Laravel Mail)
- Session management

---

### 2. **CUSTOMER MANAGEMENT** 
**Status:** ✅ COMPLETE (90%)

**Components:**
- `Customer` model
- `CustomerStatus` model
- `CustomerController` (CRUD + reporting)
- `CustomerStatusController` (status management)
- Store/Update Request validation

**Data Structure:**
- customers: customer_id, name, contact, address, measurements (JSON), status_id
- customer_statuses: status_id, status_name

**Implementation Details:**
- Full CRUD operations
- Status management (active/inactive)
- Customer statistics
- Rental history tracking
- PDF report generation
- Registration trend analysis
- Deactivate/reactivate workflows

**Known Issues:**
- ⚠️ Hardcoded status IDs in JavaScript (lines 272-274)
- ⚠️ Race condition in customer creation
- ⚠️ Missing error handling for API calls in JS
- ⚠️ Report method returns wrong type annotation

**Dependencies:**
- Reservation subsystem
- Rental subsystem
- Invoice subsystem

---

### 3. **INVENTORY MANAGEMENT** 
**Status:** ✅ COMPLETE (92%)

**Components:**
- `Inventory` model
- `InventoryStatus` model
- `InventoryImage` model
- `InventoryStatusController`
- `InventoryController` (CRUD + advanced features)
- `InventoryImageController` (image uploads)

**Data Structure:**
- inventories: item_id, item_type (gown/suit), sku, name, size, color, design, condition, rental_price, status_id
- inventory_images: image paths and metadata
- inventory_statuses: status tracking

**Implementation Details:**
- Automatic SKU generation (GWN-001, SUT-001, etc.)
- Condition tracking (good/damaged/under repair/retired)
- Image management for items
- Bulk status updates
- Availability checking
- Missing image identification
- Financial metrics (total value, rental performance)

**Database Optimization:**
- Performance indexes added (migration 2026_01_30_155531)

**Dependencies:**
- Reservation subsystem (check availability)
- Rental subsystem (track rented items)
- Invoice subsystem (rental pricing)

---

### 4. **RESERVATION MANAGEMENT** 
**Status:** ⚠️ MOSTLY COMPLETE (85%)

**Components:**
- `Reservation` model
- `ReservationStatus` model
- `ReservationItem` model
- `ReservationController` (full CRUD + workflow)
- `ReservationItemController`

**Data Structure:**
- reservations: reservation_id, customer_id, reserved_by (user), status_id, dates
- reservation_items: links items to reservations
- reservation_statuses: status tracking

**Implementation Details:**
- Browse available items
- Check item details (availability, images)
- Create/update reservations with date ranges
- Confirm reservations
- Cancel reservations
- Item availability validation
- PDF report generation
- Status tracking

**Critical Issues:**
- ⚠️ **CRITICAL:** Missing `use Illuminate\Support\Facades\Auth;` (lines 208, 587, 642)
- ⚠️ **CRITICAL:** Missing `use App\Models\ReservationItem;` (line 236)
- ⚠️ **HIGH:** TODO comment - inventory status not updated on cancellation
- ⚠️ Missing error handling in store() method
- ⚠️ Return type mismatch in generatePDF()

**Dependencies:**
- Customer subsystem
- Inventory subsystem
- Rental subsystem
- Invoice subsystem
- User model

---

### 5. **RENTAL MANAGEMENT** 
**Status:** ✅ COMPLETE (88%)

**Components:**
- `Rental` model
- `RentalStatus` model
- `RentalController` (full CRUD + workflows)

**Data Structure:**
- rentals: rental_id, reservation_id, item_id, customer_id, released_by, dates, extension tracking, return info, status_id

**Implementation Details:**
- Release items to customers
- Track rental status (active, returned, overdue)
- Extend rental periods with reason tracking
- Process returns with condition notes
- Overdue rental detection and batch checking
- Customer/item rental history
- Late fee calculations
- PDF report generation

**Features:**
- Extension count and tracking
- Original due date preservation (for late fee calculations)
- Multiple staff tracking (released_by, returned_to, extended_by)
- Comprehensive return documentation

**Dependencies:**
- Customer, Inventory, Reservation subsystems
- Invoice subsystem
- User model

---

### 6. **INVOICING & BILLING** 
**Status:** ⚠️ MOSTLY COMPLETE (82%)

**Components:**
- `Invoice` model
- `InvoiceItem` model
- `InvoiceController` (CRUD + workflows)
- `InvoiceItemController`

**Data Structure:**
- invoices: invoice_id, customer_id, rental_id, reservation_id, amounts (subtotal/tax/discount/total/paid/balance), dates, type, status_id
- invoice_items: line items linking to invoices

**Implementation Details:**
- Three invoice types: reservation, rental, final
- Rental fee breakdown
- Penalty/late fee tracking
- Invoice PDF generation
- Payment monitoring
- Partial payment handling

**Critical Issues:**
- ⚠️ **CRITICAL:** Missing `use Carbon\Carbon;` (lines 21-22, 120-121)
- ⚠️ **HIGH:** Return type mismatch in generatePDF()
- ⚠️ **HIGH:** Missing error handling in store()
- ⚠️ Incomplete `isPaid()` method
- ⚠️ Duplicate code in report() and generatePDF()

**Dependencies:**
- Customer, Rental, Reservation subsystems
- Payment subsystem
- User model

---

### 7. **PAYMENT PROCESSING** 
**Status:** ✅ COMPLETE (85%)

**Components:**
- `Payment` model
- `PaymentStatus` model
- `PaymentController` (CRUD + processing)
- `PaymentStatusController`

**Data Structure:**
- payments: payment_id, invoice_id, amount, payment_method (cash/card/bank/gcash/paymaya), processed_by, status_id

**Implementation Details:**
- Multiple payment methods supported
- Payment reference tracking
- Staff tracking (processed_by)
- Status management
- Receipt PDF generation
- Payment monitoring
- Balance update on payment

**Dependencies:**
- Invoice subsystem
- User model

---

### 8. **DASHBOARD & REPORTING** 
**Status:** ✅ COMPLETE (90%)

**Components:**
- `DashboardController` (metrics and visualization)
- Dashboard Blade template
- 6 Chart.js visualizations
- Multiple report pages (customers, inventory, rentals)
- PDF report generation

**Features:**
- Real-time KPI metrics
- Top performers tracking
- Trend analysis
- Occupancy rates
- Overdue tracking
- Date-range filtered reports
- Dynamic chart rendering
- Theme-aware visualizations (dark/light mode support)
- PDF export capability

**Recent Improvements:**
- ✅ Fixed chart theme switching (all 22 charts)
- ✅ Dark/light mode support for all visualizations
- ✅ Generic color updater function
- ✅ MutationObserver-based theme detection

**Dependencies:**
- A
