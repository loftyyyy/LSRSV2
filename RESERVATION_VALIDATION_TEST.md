# Reservation Validation Test Scenarios

## Overview
This document describes manual test scenarios to verify that the new Reservation validation logic is working correctly after the database migrations and model updates.

## Prerequisites
- Database migrations have been run successfully (2026_04_08_add_audit_columns_to_reservations_table, 2026_04_08_update_fulfillment_status_enum)
- Reservation model updated with new fields and relationships
- RentalReleaseService validation logic is integrated

## Test Scenarios

### Scenario 1: Confirm Pending Reservation
**Objective**: Verify that confirmReservation() method properly saves confirmed_at and confirmed_by

**Setup**:
1. Create a new reservation with status 'pending'
2. Get the reservation ID

**Steps**:
1. Call POST `/api/reservations/{reservation_id}/confirm`
2. Check response

**Expected Result**:
- HTTP 200 response
- response.data.status.status_name = "confirmed"
- response.data.confirmed_at = current datetime
- response.data.confirmed_by = current user ID
- Database row: confirmed_at and confirmed_by columns are populated

**Error Cases to Test**:
- Try confirming a reservation that is already confirmed → Should return error "Only pending reservations can be confirmed"
- Try confirming a completed reservation → Should return error

---

### Scenario 2: Cancel Reservation with Reason
**Objective**: Verify that cancelReservation() method properly saves cancelled_at, cancelled_by, and cancellation_reason

**Setup**:
1. Create a pending reservation
2. Confirm it
3. Ensure no active rentals are associated

**Steps**:
1. Call POST `/api/reservations/{reservation_id}/cancel`
2. Include request body: `{"cancellation_reason": "Customer requested cancellation"}`
3. Check response

**Expected Result**:
- HTTP 200 response
- response.data.status.status_name = "cancelled"
- response.data.cancelled_at = current datetime
- response.data.cancelled_by = current user ID
- response.data.cancellation_reason = "Customer requested cancellation"
- Database row: cancelled_at, cancelled_by, cancellation_reason columns are populated

**Error Cases to Test**:
- Try cancelling a completed reservation → Should return error "Cannot cancel a completed reservation"
- Try cancelling a reservation with active rentals → Should return error "Cannot cancel reservation with active rentals"

---

### Scenario 3: Release Item on Pending (Unconfirmed) Reservation
**Objective**: Verify that releaseItem() rejects release when reservation is not confirmed

**Setup**:
1. Create a pending reservation with items
2. Create/assign a physical inventory item
3. Do NOT confirm the reservation

**Steps**:
1. Call POST `/api/rentals/release-item`
2. Include request body with item_id, customer_id, reservation_id (pending), dates
3. Check response

**Expected Result**:
- HTTP 422 response (validation error)
- response.error contains: "must be confirmed before releasing items"
- response.error contains: "Current status: pending"
- response.error suggests: "Please confirm the reservation first using the confirmReservation endpoint"
- No rental record is created
- No invoice is created

---

### Scenario 4: Release Item Before Reservation Start Date
**Objective**: Verify that releaseItem() rejects release when date is before reservation start_date

**Setup**:
1. Create and confirm a reservation
2. Reservation start_date = 2026-04-15, end_date = 2026-04-20
3. Create/assign physical inventory item

**Steps**:
1. Call POST `/api/rentals/release-item`
2. Include released_date = 2026-04-14 (before start_date)
3. Check response

**Expected Result**:
- HTTP 422 response
- response.error contains: "Release date (2026-04-14) cannot be before reservation start date (2026-04-15)"
- response.error contains: "Items must be released within the reservation date range"
- No rental record is created

---

### Scenario 5: Release Item After Reservation End Date
**Objective**: Verify that releaseItem() rejects release when date is after reservation end_date

**Setup**:
1. Create and confirm a reservation
2. Reservation start_date = 2026-04-15, end_date = 2026-04-20
3. Create/assign physical inventory item

**Steps**:
1. Call POST `/api/rentals/release-item`
2. Include released_date = 2026-04-21 (after end_date)
3. Check response

**Expected Result**:
- HTTP 422 response
- response.error contains: "Release date (2026-04-21) cannot be after reservation end date (2026-04-20)"
- response.error contains: "Items must be released within the reservation date range"
- No rental record is created

---

### Scenario 6: Successfully Release Item on Confirmed Reservation (Within Date Range)
**Objective**: Verify that releaseItem() successfully releases when all conditions are met

**Setup**:
1. Create and confirm a reservation
2. Reservation start_date = 2026-04-15, end_date = 2026-04-20
3. Create/assign physical inventory item with deposit amount configured
4. released_date = 2026-04-18 (within range)

**Steps**:
1. Call POST `/api/rentals/release-item`
2. Include item_id, customer_id, reservation_id, released_date = 2026-04-18, due_date = 2026-04-20
3. Check response

**Expected Result**:
- HTTP 201 response (or 200 depending on implementation)
- Rental record is created with status = "rented"
- Invoice is created with line items (rental fee + deposit)
- Item status changes to "rented"
- response.data.released_date = 2026-04-18
- response.data.deposit_status = "held" or "pending_collection" (depending on payment method)

---

### Scenario 7: Verify Soft Deletes Work (Optional)
**Objective**: Verify that soft deletes are properly working for audit trail

**Setup**:
1. Create a reservation
2. Delete it using DELETE endpoint

**Steps**:
1. Call DELETE `/api/reservations/{reservation_id}`
2. Check response
3. Query database for the reservation

**Expected Result**:
- Reservation is not returned in normal queries (soft delete)
- Reservation.deleted_at is populated
- Using `withTrashed()` should return the soft-deleted record
- Audit trail is preserved for compliance

---

## Testing Checklist

- [ ] Scenario 1: Confirm pending reservation saves confirmed_at and confirmed_by
- [ ] Scenario 2: Cancel reservation saves cancelled_at, cancelled_by, cancellation_reason
- [ ] Scenario 3: Release item on pending reservation is rejected with proper error
- [ ] Scenario 4: Release item before start_date is rejected with proper error
- [ ] Scenario 5: Release item after end_date is rejected with proper error
- [ ] Scenario 6: Release item on confirmed reservation (within dates) succeeds
- [ ] Scenario 7 (Optional): Soft deletes work and deleted_at is populated
- [ ] All error messages are clear and actionable
- [ ] No regressions in existing functionality

---

## Database Verification Queries

### Verify New Columns Exist
```sql
-- Check reservations table structure
DESCRIBE reservations;

-- Should include:
-- confirmed_at (DATETIME, nullable)
-- confirmed_by (BIGINT UNSIGNED, nullable, FK to users)
-- cancelled_at (DATETIME, nullable)
-- cancelled_by (BIGINT UNSIGNED, nullable, FK to users)
-- cancellation_reason (TEXT, nullable)
-- expiry_date (DATE, nullable)
-- expiry_checked_at (DATETIME, nullable)
-- deleted_at (DATETIME, nullable)
```

### Verify ENUM Update
```sql
-- Check fulfillment_status ENUM values
SELECT COLUMN_TYPE FROM INFORMATION_SCHEMA.COLUMNS 
WHERE TABLE_NAME = 'reservation_items' AND COLUMN_NAME = 'fulfillment_status';

-- Should include: pending, partial, fulfilled, cancelled
```

### Query for Test Data
```sql
-- Find a pending reservation
SELECT * FROM reservations WHERE status_id = (SELECT status_id FROM reservation_statuses WHERE status_name = 'pending') LIMIT 1;

-- Find a confirmed reservation
SELECT * FROM reservations WHERE status_id = (SELECT status_id FROM reservation_statuses WHERE status_name = 'confirmed') LIMIT 1;

-- Find reservations with audit data populated
SELECT reservation_id, status_id, confirmed_at, confirmed_by, cancelled_at, cancelled_by 
FROM reservations WHERE confirmed_at IS NOT NULL OR cancelled_at IS NOT NULL;
```

---

## Known Issues & Notes

1. **Test Suite**: Current test suite has SQLite driver issues in test environment. Manual testing recommended.
2. **Payment Integration**: Ensure PaymentService is properly integrated with RentalReleaseService for deposit collection.
3. **Audit Trail**: All operations (confirm, cancel, release) now include who performed them and when.
4. **Soft Deletes**: Queries must be reviewed to handle soft-deleted records appropriately.

---

## Next Steps After Validation

1. Update any queries that need to exclude soft-deleted reservations
2. Implement auto-expiry feature using expiry_date and expiry_checked_at
3. Add customer notifications (email/SMS) for confirmation, cancellation, and expiry
4. Create dashboard for viewing reservation status and audit trails
5. Implement bulk operations (confirm/cancel multiple reservations)
