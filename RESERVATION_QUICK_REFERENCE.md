# RESERVATION SUBSYSTEM - QUICK REFERENCE GUIDE

## Current Workflow Map

```
┌─────────────────────────────────────────────────────────────────────┐
│                    RESERVATION LIFECYCLE                            │
└─────────────────────────────────────────────────────────────────────┘

                        CREATE RESERVATION
                                │
                                ▼
                    Status: PENDING 
                    (Awaiting Confirmation)
                    │
        ┌───────────┴───────────┐
        │                       │
        ▼                       ▼
   Confirm ✓              Cancel ✗
   (confirmReservation)   (cancelReservation)
        │                       │
        ▼                       ▼
   CONFIRMED         CANCELLED (Terminal)
   (Ready to Release)  └─ No active rentals
   │
   └─ Release Item 1
        │
        ├─ Create Rental
        ├─ Create Invoice
        ├─ Collect Deposit
        ├─ Update Item Status → 'rented'
        ├─ Update Allocation → 'released'
        ├─ Update Fulfillment → 'fulfilled'
        │
        ├─ All items released?
        │  ├─ YES → Reservation → COMPLETED (Terminal)
        │  └─ NO  → Still CONFIRMED (Ready for next item)
        │
        └─ Release Item 2, 3, ... (repeat)


STATUS STATES:
  ① PENDING ......... Awaiting confirmation
  ② CONFIRMED ...... Ready for item release  
  ③ COMPLETED ...... All items released (auto)
  ④ CANCELLED ...... Explicitly cancelled
  ⑤ EXPIRED ........ (Defined but not implemented)
```

---

## Answer to Your Questions

### Q1: "How do we confirm a reservation?"
**A:** Call this endpoint:
```
POST /api/reservations/{id}/confirm
```

**Location in UI:** (You'll need to add/check this in your frontend)

**What Happens:**
- Status changes: PENDING → CONFIRMED
- Records who confirmed and when ⚠️ (Data not being saved to DB currently)
- Reservation becomes ready for item release

---

### Q2: "Should it be confirmed when releasing items?"
**A:** YES - Confirmation should be a PREREQUISITE for release.

**Current Problem:** The system doesn't check this - you CAN release items even if the reservation is still "pending"

**Recommended Fix:** Add this guard in RentalReleaseService:
```php
if ($reservation) {
    if (strtolower($reservation->status?->status_name) !== 'confirmed') {
        return [
            'error' => "Reservation must be confirmed before releasing items",
            'code' => 422,
        ];
    }
}
```

---

### Q3: "How does it work currently?"
**A:**
1. Create Reservation → Status = PENDING ✓
2. Call confirmReservation() → Status = CONFIRMED ✓
3. Release Item button → Creates rental + updates reservation ⚠️
4. When last item released → Status auto-changes to COMPLETED ✓

**Flow:** Pending → Confirmed → (Release Items) → Completed

---

### Q4: "What do you recommend?"

#### Immediate (Do Now)
```
✓ Add confirmation check before release
✓ Add missing database columns
✓ Fix ENUM constraint on fulfillment_status
```

#### Short-term (This Week)
```
✓ Validate release dates within reservation dates
✓ Implement expiry feature for old reservations
✓ Update tests and documentation
```

#### Long-term (Next Sprint)
```
✓ Customer notifications (email/SMS)
✓ Reservation dashboard
✓ Analytics and reporting
```

---

## Database Issues (Must Fix)

### Issue 1: Missing Columns in `reservations` Table
```sql
Current columns: ❌
  - confirmed_at (not exists)
  - confirmed_by (not exists)
  - cancelled_at (not exists)
  - cancelled_by (not exists)
  - cancellation_reason (not exists)

Status: Data is being set in code but LOST in database!
```

**Fix:** Run this migration
```php
Schema::table('reservations', function (Blueprint $table) {
    $table->timestamp('confirmed_at')->nullable()->after('end_date');
    $table->foreignId('confirmed_by')->nullable()->constrained('users', 'user_id');
    $table->timestamp('cancelled_at')->nullable();
    $table->foreignId('cancelled_by')->nullable()->constrained('users', 'user_id');
    $table->text('cancellation_reason')->nullable();
    $table->softDeletes();  // For audit trail
});
```

### Issue 2: ENUM Constraint Violation
```
Table: reservation_items
Column: fulfillment_status
Current allowed values: ['pending', 'fulfilled', 'cancelled']

Code tries to use: 'partial'  ❌ INVALID!
```

**Fix:** Expand ENUM:
```php
Schema::table('reservation_items', function (Blueprint $table) {
    $table->enum('fulfillment_status', 
        ['pending', 'partial', 'fulfilled', 'cancelled'])->change();
});
```

---

## Status Transition Rules

```
FROM PENDING:
  ├─ Can transition TO: CONFIRMED (via confirmReservation endpoint)
  └─ Can transition TO: CANCELLED (via cancelReservation endpoint)

FROM CONFIRMED:
  ├─ Can transition TO: COMPLETED (automatic when releasing all items)
  └─ Can transition TO: CANCELLED (via cancelReservation - if no active rentals)

FROM COMPLETED:
  └─ Terminal state - NO transitions allowed

FROM CANCELLED:
  └─ Terminal state - NO transitions allowed

FROM EXPIRED:
  └─ Terminal state - NO transitions allowed (when implemented)
```

---

## Reservation Item Fulfillment

```
When you release an item:

Before Release:
  ReservationItem.fulfillment_status = "pending"
  
During Release:
  ├─ ReservationItemAllocation created
  ├─ allocation_status = "released"
  └─ Count releases vs. quantity
  
After Release:
  ├─ If quantity released >= quantity ordered
  │  └─ ReservationItem.fulfillment_status = "fulfilled"
  │
  ├─ If quantity released < quantity ordered
  │  └─ ReservationItem.fulfillment_status = "partial"
  │
  └─ If ALL ReservationItems fulfilled
     └─ Reservation status = "COMPLETED"
```

---

## API Endpoints for Reservations

```
CREATE:
  POST /api/reservations
  Body: {
    customer_id, start_date, end_date, items: [
      { variant_id, quantity, rental_price }
    ]
  }

CONFIRM:
  POST /api/reservations/{id}/confirm
  Response: Confirmed reservation with status = "confirmed"

CANCEL:
  DELETE /api/reservations/{id}
  or
  POST /api/reservations/{id}/cancel
  Requires: No active rentals

RELEASE ITEM:
  POST /api/rentals/release-item
  Body: {
    item_id, customer_id, released_date, due_date,
    reservation_id, collect_deposit, deposit_payment_method
  }
  Effect: Auto-updates reservation status if all items released

VIEW:
  GET /api/reservations
  GET /api/reservations/{id}
  Includes: Status, items, allocations, fulfillment tracking
```

---

## Decision Tree: Should I Confirm Before Release?

```
                    ┌─────────────────┐
                    │  Release Item?  │
                    └────────┬────────┘
                             │
                    ┌────────┴────────┐
                    │                 │
            Has reservation?      No reservation?
                    │                 │
                    ▼                 ▼
          ┌─────────────────┐   Just create
          │ Reservation    │   rental (walk-in)
          │ Status?        │   ✓ Works fine
          └────────┬────────┘
                   │
        ┌──────────┼──────────┐
        │          │          │
    PENDING?  CONFIRMED?  COMPLETED?
        │          │          │
        ▼          ▼          ▼
    ❌ BLOCK    ✓ ALLOW    ❌ BLOCK
    "Must       "Ready     "Already
    confirm    for        complete"
    first"     release"

RECOMMENDATION: Always check confirmation
                status before release!
```

---

## Timeline: When to Do What

```
IMMEDIATE (Today):
  - Review this analysis
  - Decide on confirmation requirement
  - Plan database migrations

THIS WEEK:
  - Add confirmation check to RentalReleaseService
  - Run database migrations
  - Test entire workflow
  - Update documentation

NEXT WEEK:
  - Implement expiry feature
  - Add validation rules
  - Complete test coverage

NEXT SPRINT:
  - Customer notifications
  - UI/UX improvements
  - Analytics dashboard
```

---

## Files You Need to Update

```
HIGH PRIORITY:
  □ app/Services/RentalReleaseService.php
    └─ Add confirmation check
    └─ Add date range validation
  
  □ database/migrations/
    └─ Add missing columns to reservations
    └─ Fix ENUM in reservation_items
  
  □ app/Http/Controllers/ReservationController.php
    └─ Verify confirmReservation() logic

MEDIUM PRIORITY:
  □ tests/Feature/ReservationTest.php (create if needed)
    └─ Test confirmation workflow
    └─ Test release with non-confirmed
  
  □ RESERVATION_ANALYSIS.md
    └─ Reference document (already created)

LOW PRIORITY:
  □ Frontend UI (add confirm button if missing)
  □ API documentation
  □ Customer notifications
```

---

## Key Takeaways

### What's Working ✓
- Reservations can be created and confirmed
- Items can be released against reservations
- Auto-completion when all items released
- Fulfillment tracking at item level
- Good data model with proper relationships

### What Needs Fixing ⚠️
- Missing database columns (data loss currently happening)
- No confirmation requirement before release
- ENUM constraint violation
- No expiry implementation
- Date range validation missing

### What You Should Do 🎯
1. **Add confirmation check** (Prevents business logic errors)
2. **Fix database schema** (Prevents data loss)
3. **Add validation** (Prevents audit trail problems)
4. **Implement expiry** (Handles forgotten reservations)

### Recommended Confirmation Flow
```
1. Customer creates reservation (PENDING)
2. Staff reviews and confirms (CONFIRMED)
3. Staff releases items as needed (CONFIRMED → COMPLETED when all released)
4. Auto-expire if not confirmed within 7 days
```

This gives you proper business logic, audit trail, and prevents accidental releases.

---

**See RESERVATION_ANALYSIS.md for detailed implementation guide and code examples.**
