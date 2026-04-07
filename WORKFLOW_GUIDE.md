# Complete LSRSV2 Workflow Guide - From Reservation to Rental

**Purpose**: Understand the complete flow from creating a reservation to tracking rentals  
**Status**: Step-by-step explanation with real examples

---

## Table of Contents

1. [High-Level Overview](#high-level-overview)
2. [Step-by-Step Workflow](#step-by-step-workflow)
3. [Pending → Confirmed (Reservation)](#pending--confirmed-reservation)
4. [Customer & Item Selection](#customer--item-selection)
5. [Release Item (Create Rental)](#release-item-create-rental)
6. [Track in Rental System](#track-in-rental-system)
7. [API Endpoints & Examples](#api-endpoints--examples)

---

## High-Level Overview

```
RESERVATION FLOW (Planning Phase)
├─ Step 1: Create Reservation (PENDING)
│  └─ Customer + Items + Dates selected
├─ Step 2: Confirm Reservation (CONFIRMED)
│  └─ Management approves the booking
└─ Step 3: Prepare Items (READY FOR RELEASE)

RENTAL FLOW (Execution Phase)
├─ Step 4: Release Item to Customer (CREATE RENTAL)
│  └─ Physical item goes to customer
│  └─ Invoice & deposit created
├─ Step 5: Collect Payment
│  └─ Payment processed
└─ Step 6: Track Rental & Return
   └─ Monitor due dates, extensions, returns
```

---

## Step-by-Step Workflow

### Phase 1: RESERVATION (Planning)

#### Step 1: Create Reservation (Status = PENDING)

**What happens**:
- Customer wants to rent items for specific dates
- Reservation is created in the system
- Status is automatically set to "pending"
- Items are tentatively reserved (not yet confirmed)

**Who can do this**: Clerk, Admin  
**Where**: Reservations page → New Reservation button

**Form Fields**:
- Customer (select from list)
- Reservation Date (today's date)
- Start Date (when customer wants items)
- End Date (when customer returns items)
- Items to Rent (select items & quantities)

**Example**:
```
Reservation #1001
├─ Customer: John Doe
├─ Status: PENDING ⚠️ (not confirmed yet!)
├─ Reservation Date: 2026-04-08
├─ Start Date: 2026-04-15
├─ End Date: 2026-04-20
└─ Items:
   ├─ Wedding Dress (qty: 1)
   └─ Veil (qty: 1)
```

**Database Status**: `status_id = 1` (pending)

---

#### Step 2: Confirm Reservation (Status = PENDING → CONFIRMED)

**⚠️ THIS IS THE KEY STEP YOU'RE ASKING ABOUT!**

**What happens**:
1. Confirmation means: "We approve this reservation, customer can proceed"
2. Items are now officially reserved (not available for other customers)
3. `confirmed_at` timestamp is recorded (when confirmed)
4. `confirmed_by` records which staff member confirmed it
5. Status changes from PENDING to CONFIRMED

**How to Confirm**:

**Method 1: Via API (Postman/Insomnia)**
```
POST /api/reservations/1001/confirm

Response:
{
  "message": "Reservation confirmed successfully",
  "data": {
    "reservation_id": 1001,
    "status": {
      "status_name": "confirmed"  ← CHANGED!
    },
    "confirmed_at": "2026-04-08T10:30:00",
    "confirmed_by": 5  ← Staff member ID who confirmed it
  }
}
```

**Method 2: Via UI (Button on Reservation Page)**
- Go to Reservations
- Find the reservation
- Click "Confirm" button
- System automatically sets:
  - `confirmed_at` = NOW()
  - `confirmed_by` = Your user ID

**After Confirmation**:
✅ Status = CONFIRMED  
✅ Items are locked for this customer  
✅ Ready to release items to customer  

**Database Changes**:
```sql
UPDATE reservations
SET status_id = 2,  -- 2 = confirmed
    confirmed_at = NOW(),
    confirmed_by = 5  -- current user
WHERE reservation_id = 1001;
```

---

#### Why Confirmation is Important

```
WITHOUT Confirmation:
├─ Reservation = pending
├─ Items = NOT reserved (can sell to someone else)
├─ Cannot release items to customer
└─ Risk: Double-booking

WITH Confirmation:
├─ Reservation = confirmed
├─ Items = reserved (locked for this customer)
├─ CAN release items to customer
└─ Safe: No double-booking
```

---

### Phase 2: RENTAL (Execution)

#### Step 3: Release Item to Customer

**⚠️ THIS IS WHERE CUSTOMER GETS THE PHYSICAL ITEMS!**

**Prerequisites**:
- ✅ Reservation must be CONFIRMED (not pending!)
- ✅ Release date must be within reservation dates
- ✅ Item must be available in inventory

**What happens**:
1. Physical item is given to customer
2. Rental record is created (tracks the rental)
3. Invoice is generated (with deposit + rental fee)
4. Deposit is collected (if needed)
5. Item status changes to "rented"
6. Customer tracking begins

**How to Release an Item**:

**Method 1: Via API**
```
POST /api/rentals/release-item

Request Body:
{
  "item_id": 42,  ← Physical item to give out
  "customer_id": 99,  ← Customer getting the item
  "reservation_id": 1001,  ← Link to reservation
  "released_date": "2026-04-15",  ← When customer picks up
  "due_date": "2026-04-20",  ← When customer must return
  "deposit_payment_method": "cash",
  "collect_deposit": true
}

Response (Success):
{
  "message": "Item released successfully",
  "data": {
    "rental_id": 5001,
    "item_id": 42,
    "customer_id": 99,
    "status": "rented",
    "deposit_amount": 5000,
    "invoice": {
      "invoice_id": 201,
      "total_amount": 7500,  ← rental fee + deposit
      "items": [
        {
          "description": "Rental: Wedding Dress",
          "amount": 2500
        },
        {
          "description": "Security Deposit",
          "amount": 5000
        }
      ]
    }
  }
}
```

**Method 2: Via UI**
- Go to Rentals
- Click "Release Item" button
- Modal opens with fields:
  - Select Item (dropdown)
  - Customer (auto-filled or select)
  - Release Date
  - Due Date
  - Deposit Amount (READ-ONLY from config)
  - Payment Method
- Click "Release"

**What Gets Created**:
1. ✅ Rental record (#5001)
2. ✅ Invoice (#201) with line items
3. ✅ Payment record (if deposit collected)
4. ✅ Inventory movement log

---

## Pending → Confirmed (Reservation)

### Where is the Confirm Button?

**Location 1: Reservations Table**
```
Reservations Page
├─ List of all reservations
├─ Find the PENDING reservation
└─ Action Buttons:
   ├─ View Details
   ├─ Edit
   ├─ Confirm ← CLICK THIS
   └─ Cancel
```

**Location 2: Reservation Details**
```
Reservation #1001 Details
├─ Status: PENDING
├─ Customer: John Doe
├─ Dates: 2026-04-15 to 2026-04-20
├─ Items: Wedding Dress, Veil
└─ Action Buttons:
   ├─ Confirm ← CLICK THIS
   ├─ Cancel
   └─ Edit
```

### Step-by-Step: How to Confirm

```
1. Open Reservations page
   ↓
2. Find PENDING reservation (status shows yellow/orange icon)
   ↓
3. Click "Confirm" button
   ↓
4. System asks: "Are you sure?"
   ↓
5. Click "Yes, Confirm"
   ↓
6. Status changes to CONFIRMED ✅
   ↓
7. Success message: "Reservation confirmed successfully"
```

### What Changes After Confirmation

**Visible Changes**:
- Status badge changes from "PENDING" to "CONFIRMED"
- Color changes from yellow/orange to green
- `confirmed_at` field shows: "2026-04-08 10:30:00"
- `confirmed_by` field shows: "Ahmed (Admin)"

**Behind the Scenes**:
- Items are locked (can't be used elsewhere)
- Release button becomes active
- System prevents cancellation in some cases
- Audit log records the action

---

## Customer & Item Selection

### How Do Customers Get Into the System?

**Step 1: Customer Must Exist First**

Customers are registered separately BEFORE creating a reservation.

**Where to Add Customers**:
```
Customers Page
├─ New Customer button
└─ Add their info:
   ├─ First Name
   ├─ Last Name
   ├─ Email
   ├─ Phone
   ├─ Address
   └─ Save
```

**Example**:
```
Customer #99
├─ Name: John Doe
├─ Email: john@example.com
├─ Phone: 555-1234
└─ Status: Active
```

---

### How Are Customers Linked to Rentals?

**Flow**:
```
Customer Created (#99)
        ↓
Reservation Created → Select Customer (#99)
        ↓
Reservation Confirmed
        ↓
Release Item → Customer (#99) automatically linked
        ↓
Rental Created → Customer (#99) in rental record
        ↓
Rental Tracking → All activities linked to Customer (#99)
```

**In the Rental Tracking System**:
```
Rental #5001
├─ Customer: John Doe (#99) ← This is the customer
├─ Item: Wedding Dress
├─ Dates: 2026-04-15 to 2026-04-20
├─ Status: Rented
└─ Customer Details (expandable):
   ├─ Phone: 555-1234
   ├─ Email: john@example.com
   ├─ Address: 123 Main St
   └─ Previous Rentals: 3
```

---

### How Are Items Selected?

**Items (Inventory)**:
- Items are physical products in your inventory
- Each item has a SKU, type, size, color, etc.
- Items are grouped by Variant (e.g., "Wedding Dress Size M Red")

**Example Items**:
```
Item #42 (Wedding Dress)
├─ SKU: WD-001
├─ Variant: Wedding Dress Size M
├─ Color: White
├─ Status: Available
├─ Rental Price: 2500
├─ Deposit: 5000
└─ Current Location: Storage Shelf A

Item #43 (Veil)
├─ SKU: V-001
├─ Variant: Veil Standard
├─ Color: White
├─ Status: Available
├─ Rental Price: 1000
├─ Deposit: 2000
└─ Current Location: Storage Shelf B
```

**How Items Flow into Rentals**:

```
When Creating Reservation:
├─ Browse Available Items
│  ├─ Filter by type: "Wedding Dresses"
│  ├─ Filter by size: "M"
│  ├─ Filter by dates: "2026-04-15 to 2026-04-20"
│  └─ Available: 3 dresses
├─ Select Item #42
├─ Add to Reservation
└─ Item #42 shows as "Reserved"

When Releasing Item:
├─ Select Physical Item #42 (dropdown)
├─ System checks:
│  ├─ Is it available? YES ✅
│  ├─ Is it within date range? YES ✅
│  └─ Does it belong to reserved variant? YES ✅
├─ Give item to customer
└─ Item #42 status changes to "Rented"
```

---

## Release Item (Create Rental)

### What Exactly is a Rental?

**Simple Definition**:
A Rental = Contract between customer and shop for temporary item ownership

**Rental Includes**:
```
Rental Record
├─ Customer: John Doe
├─ Item: Wedding Dress
├─ Dates: 2026-04-15 to 2026-04-20
├─ Rental Fee: 2500
├─ Deposit: 5000
├─ Invoice: #201
├─ Status: Rented
└─ Tracking:
   ├─ Released: 2026-04-15
   ├─ Due: 2026-04-20
   ├─ Returned: (pending)
   └─ Late? (pending)
```

---

### Release Item: The Complete Process

**Before Release**:
```
Reservation #1001 (CONFIRMED)
├─ Customer: John Doe
├─ Status: Confirmed
├─ Items: Wedding Dress, Veil
├─ Dates: 2026-04-15 to 2026-04-20
└─ Deposit Configured: 5000 (per item)
```

**During Release**:
```
1. Select Physical Item #42 (Wedding Dress)
   ├─ System verifies:
   │  ├─ Is CONFIRMED? YES ✅
   │  ├─ Release date 2026-04-15 >= start 2026-04-15? YES ✅
   │  ├─ Release date 2026-04-15 <= end 2026-04-20? YES ✅
   │  └─ Item available? YES ✅
   └─ All checks pass! ✅

2. System Creates Rental #5001
   ├─ Customer: John Doe
   ├─ Item: Wedding Dress #42
   ├─ Status: Rented
   └─ Deposit Status: Pending Collection

3. System Creates Invoice #201
   ├─ Line Item 1: Rental Fee = 2500
   ├─ Line Item 2: Deposit = 5000
   └─ Total Due: 7500

4. System Collects Deposit (if selected)
   ├─ Payment created for 5000
   ├─ Payment method: Cash
   └─ Deposit Status: Held

5. System Updates Inventory
   ├─ Item #42 status: Available → Rented
   └─ Item #42 location: Storage → Customer

6. System Records Audit Trail
   ├─ Action: Released
   ├─ By: Ahmed (Staff)
   ├─ Time: 2026-04-08 14:30:00
   └─ Notes: Wedding Dress released to John Doe
```

**After Release**:
```
Rental #5001 (Now Tracked)
├─ Status: Rented
├─ Customer: John Doe
├─ Item: Wedding Dress #42
├─ Released: 2026-04-15
├─ Due: 2026-04-20
├─ Days Rented: 5 days
├─ Rental Fee: 2500 (paid ✅)
├─ Deposit: 5000 (held)
├─ Invoice Balance: 0 (paid in full)
└─ Actions Available:
   ├─ Extend Due Date
   ├─ Collect Additional Payment
   ├─ Mark as Returned
   └─ View Payment History
```

---

## Track in Rental System

### Where Do Rentals Appear?

**Location 1: Rentals Dashboard**
```
Rentals Page
├─ Filter Options
│  ├─ Status: Rented, Returned, Overdue
│  ├─ Customer
│  ├─ Date Range
│  └─ Apply Filters
├─ Rentals Table
│  ├─ Rental #5001 (John Doe, Wedding Dress, Due: 2026-04-20)
│  ├─ Rental #5002 (Jane Smith, Tuxedo, Due: 2026-04-19)
│  └─ ... more rentals
└─ Actions (per rental)
   ├─ View Details
   ├─ Extend
   ├─ Collect Payment
   └─ Mark Returned
```

**Location 2: Customer Profile**
```
Customer: John Doe (#99)
├─ Contact Info
├─ Active Rentals:
│  ├─ Rental #5001: Wedding Dress (Rented, Due: 2026-04-20)
│  └─ Rental #5002: Veil (Rented, Due: 2026-04-20)
├─ Rental History:
│  ├─ Rental #4995: Wedding Dress (Returned: 2026-03-10, On-time ✅)
│  ├─ Rental #4988: Tuxedo (Returned: 2026-02-15, On-time ✅)
│  └─ ... more history
└─ Statistics:
   ├─ Total Rentals: 5
   ├─ On-time Returns: 5 (100%)
   └─ Total Spent: 15000
```

---

### Tracking Rental Status

**Rental Status Options**:
```
Status: Rented
├─ Item given to customer
├─ Due date is in future
├─ Actions:
│  ├─ Extend due date
│  ├─ Collect additional payment
│  └─ Mark as Returned (when customer returns)

Status: Overdue
├─ Item NOT returned yet
├─ Due date has PASSED
├─ Warning: Red alert ⚠️
├─ Actions:
│  ├─ Apply late fees
│  ├─ Send reminder to customer
│  ├─ Collect late payment
│  └─ Mark as Returned

Status: Returned
├─ Item returned by customer
├─ Return condition recorded
├─ Actions:
│  ├─ Return deposit (if no damage)
│  ├─ Deduct from deposit (if damage)
│  └─ View return receipt
```

---

### How to Mark Item as Returned

**Method 1: Via UI**
```
Rentals Page
├─ Find rental: "John Doe - Wedding Dress - Due: 2026-04-20"
├─ Click "Mark as Returned" button
├─ Modal opens:
│  ├─ Item Condition: (Excellent / Good / Fair / Damaged)
│  ├─ Return Notes: (optional)
│  ├─ Damage Report: (if damaged)
│  └─ Return Button
└─ System records:
   ├─ Return Date: NOW()
   ├─ Item Condition
   ├─ Rental Status: Returned
   └─ Deposit: Return to customer
```

**Method 2: Via API**
```
POST /api/rentals/5001/return

Request:
{
  "return_condition": "excellent",
  "return_notes": "Item in perfect condition"
}

Response:
{
  "rental_id": 5001,
  "status": "returned",
  "return_date": "2026-04-20",
  "refund": 5000,
  "message": "Rental completed, deposit refunded"
}
```

---

## API Endpoints & Examples

### 1. Create Reservation

**Endpoint**: `POST /api/reservations`

```json
{
  "customer_id": 99,
  "start_date": "2026-04-15",
  "end_date": "2026-04-20",
  "items": [
    {
      "variant_id": 10,
      "quantity": 1,
      "rental_price": 2500
    },
    {
      "variant_id": 11,
      "quantity": 1,
      "rental_price": 1000
    }
  ]
}
```

**Response**:
```json
{
  "message": "Reservation created successfully",
  "data": {
    "reservation_id": 1001,
    "status": {
      "status_name": "pending"
    },
    "customer_id": 99,
    "start_date": "2026-04-15",
    "end_date": "2026-04-20"
  }
}
```

---

### 2. Confirm Reservation

**Endpoint**: `POST /api/reservations/1001/confirm`

**No Request Body Needed**

**Response**:
```json
{
  "message": "Reservation confirmed successfully",
  "data": {
    "reservation_id": 1001,
    "status": {
      "status_name": "confirmed"
    },
    "confirmed_at": "2026-04-08T10:30:00",
    "confirmed_by": 5
  }
}
```

---

### 3. Release Item

**Endpoint**: `POST /api/rentals/release-item`

```json
{
  "item_id": 42,
  "customer_id": 99,
  "reservation_id": 1001,
  "released_date": "2026-04-15",
  "due_date": "2026-04-20",
  "deposit_payment_method": "cash",
  "collect_deposit": true
}
```

**Response**:
```json
{
  "data": {
    "rental_id": 5001,
    "item_id": 42,
    "customer_id": 99,
    "status": "rented",
    "released_date": "2026-04-15",
    "due_date": "2026-04-20",
    "deposit_amount": 5000,
    "rental_fee": 2500,
    "invoice": {
      "invoice_id": 201,
      "total_amount": 7500,
      "items": [
        {
          "description": "Rental: Wedding Dress",
          "amount": 2500
        },
        {
          "description": "Security Deposit",
          "amount": 5000
        }
      ]
    }
  }
}
```

---

### 4. Mark Item as Returned

**Endpoint**: `POST /api/rentals/5001/return`

```json
{
  "return_condition": "excellent",
  "return_notes": "Item returned in perfect condition",
  "actual_return_date": "2026-04-20"
}
```

**Response**:
```json
{
  "message": "Item marked as returned",
  "data": {
    "rental_id": 5001,
    "status": "returned",
    "return_date": "2026-04-20",
    "days_rented": 5,
    "on_time": true,
    "deposit_refunded": 5000,
    "total_paid": 7500
  }
}
```

---

## Common Questions Answered

### Q1: "I created a reservation but it shows PENDING. Now what?"

**A**: The reservation is pending approval. You need to **CONFIRM** it.

**Steps**:
1. Find the reservation
2. Click "Confirm" button
3. Status changes to CONFIRMED ✅
4. Now you can release items

---

### Q2: "How do customers get into the system?"

**A**: Customers are registered separately in the Customers section BEFORE creating a reservation.

**Steps**:
1. Go to Customers page
2. Click "New Customer"
3. Enter their info (name, phone, email, address)
4. Save
5. Now they appear in dropdown when creating reservations

---

### Q3: "I confirmed the reservation but I can't release items. Why?"

**A**: Check these things:

1. ✅ Is reservation status CONFIRMED? (not pending)
2. ✅ Is release_date within reservation dates?
   - Release date must be >= Start Date
   - Release date must be <= End Date
3. ✅ Is the item available in inventory?
   - Status must be "Available" (not Rented, Retired, etc.)
4. ✅ Does the item have a deposit configured?
   - Item/Variant must have deposit_amount > 0

---

### Q4: "Where do I see all active rentals?"

**A**: Go to **Rentals Page** → All active rentals are listed there with:
- Customer name
- Item name
- Due date
- Status (Rented, Overdue, Returned)
- Actions (Extend, Collect Payment, Mark Returned)

---

### Q5: "What happens when I release an item?"

**A**: System automatically:
1. Creates Rental record
2. Creates Invoice with rental fee + deposit
3. Collects deposit (if selected)
4. Changes item status to "Rented"
5. Updates customer's rental history
6. Locks items (can't use elsewhere)
7. Records audit trail

---

### Q6: "How do I track payments for a rental?"

**A**: Each rental has an Invoice with payment tracking:

1. Go to Rental details
2. See Invoice section
3. View payments made
4. See remaining balance
5. Collect additional payments if needed

**Invoice Example**:
```
Invoice #201
├─ Customer: John Doe
├─ Date: 2026-04-08
├─ Line Items:
│  ├─ Wedding Dress Rental: 2500 (paid ✅)
│  └─ Security Deposit: 5000 (paid ✅)
├─ Total: 7500
├─ Paid: 7500 ✅
└─ Balance: 0
```

---

## Visual Workflow Diagram

```
DAY 1: RESERVATION PHASE
┌─────────────────────────────────┐
│ Create Reservation              │
│ • Customer: John Doe            │
│ • Items: Wedding Dress + Veil   │
│ • Dates: Apr 15-20              │
│ • Status: PENDING 🟠            │
└────────────┬────────────────────┘
             │
             ↓
┌─────────────────────────────────┐
│ Confirm Reservation             │
│ • Click "Confirm" button        │
│ • Status: CONFIRMED 🟢          │
│ • confirmed_at: Now             │
│ • Items locked for John         │
└────────────┬────────────────────┘

DAY 2: RELEASE PHASE
             ↓
┌─────────────────────────────────┐
│ Release Items to Customer       │
│ • Physical items given out      │
│ • Creates Rental #5001          │
│ • Creates Invoice #201          │
│ • Collects Deposit: 5000        │
│ • Item status: Rented           │
└────────────┬────────────────────┘
             │
             ↓
┌─────────────────────────────────┐
│ Rental Tracking Begins          │
│ • Customer has items            │
│ • Due date: Apr 20              │
│ • Rental Fee: 2500 ✅ paid      │
│ • Deposit: 5000 ✅ held         │
└────────────┬────────────────────┘

DAY 5: RETURN PHASE
             ↓
┌─────────────────────────────────┐
│ Customer Returns Items          │
│ • Click "Mark Returned"         │
│ • Confirm condition: Excellent  │
│ • Rental complete ✅            │
│ • Refund deposit: 5000          │
│ • Status: Returned              │
└─────────────────────────────────┘
```

---

## Summary

**The Complete Flow**:

1. **Create Reservation** (PENDING)
   - Choose customer
   - Select items & dates
   
2. **Confirm Reservation** (CONFIRMED)
   - Approve the booking
   - Lock items for customer
   
3. **Release Items** (Create RENTAL)
   - Give physical items to customer
   - Create invoice & collect deposit
   
4. **Track Rental**
   - Monitor due date
   - Collect payments
   
5. **Mark Returned**
   - Customer returns items
   - Refund deposit (if no damage)
   - Complete rental

**Key Points**:
- ✅ Customers must be created first
- ✅ Reservation must be CONFIRMED before releasing
- ✅ Release creates a RENTAL (the tracking record)
- ✅ Customer is automatically linked to rental
- ✅ All tracked in Rentals page

---

**Do you have any specific questions about any step?**
