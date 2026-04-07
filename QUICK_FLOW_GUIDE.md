# 🎯 COMPLETE WORKFLOW EXPLANATION - QUICK REFERENCE

## The Flow in 5 Steps

```
STEP 1: CREATE RESERVATION (PENDING) 🟠
   ↓ (Customer & items selected, but not approved yet)
   │
STEP 2: CONFIRM RESERVATION 🟢
   ↓ (You click "Confirm" button - approval given)
   │
STEP 3: RELEASE ITEM TO CUSTOMER
   ↓ (Physical item given out - creates RENTAL)
   │
STEP 4: TRACK IN RENTAL SYSTEM
   ↓ (Monitor due date, collect payments)
   │
STEP 5: MARK RETURNED
   └─→ (Customer returns - complete rental)
```

---

## STEP 1: Create Reservation (Status = PENDING)

**Where**: Reservations Page → New Reservation

**What You Enter**:
- Customer name (John Doe)
- Items (Wedding Dress, Veil)
- Dates (Apr 15 to Apr 20)

**Result**: Reservation #1001 with status **PENDING** (yellow/orange icon)

**Why Pending?**: System is saying "This reservation is waiting for approval"

---

## STEP 2: Confirm Reservation (PENDING → CONFIRMED)

### ⚠️ THIS IS THE KEY STEP YOU ASKED ABOUT!

**Where**: Reservations Page or Reservation Details

**What You Do**:
1. Find the PENDING reservation
2. Click "Confirm" button
3. Click "Yes, Confirm" in dialog
4. Done! ✅

**What Happens Behind the Scenes**:
- Status changes: PENDING 🟠 → CONFIRMED 🟢
- `confirmed_at` recorded: "2026-04-08 10:30:00"
- `confirmed_by` recorded: "Ahmed (your name/ID)"
- Items are locked for this customer (no one else can book them)

**API Method** (if using Postman):
```
POST /api/reservations/1001/confirm

Response:
{
  "status": "confirmed",
  "confirmed_at": "2026-04-08T10:30:00",
  "confirmed_by": 5
}
```

**Why Confirm?**:
- Prevents double-booking
- Locks items for this customer
- Allows you to release items
- Shows management approved this booking

---

## STEP 3: Release Item to Customer

### This is where CUSTOMER enters RENTAL system!

**Where**: Rentals Page → Release Item button

**What You Do**:
1. Click "Release Item" button
2. Modal opens with form:
   - Select Item: Wedding Dress #42
   - Customer: John Doe (auto-filled)
   - Release Date: 2026-04-15
   - Due Date: 2026-04-20
   - Deposit Method: Cash
3. Click "Release"

**What Gets Created**:
```
✅ Rental #5001
   ├─ Customer: John Doe (NOW IN RENTAL SYSTEM)
   ├─ Item: Wedding Dress #42
   ├─ Status: Rented
   └─ Due: 2026-04-20

✅ Invoice #201
   ├─ Rental Fee: 2500
   ├─ Deposit: 5000
   └─ Total: 7500

✅ Payment Record
   ├─ Deposit collected: 5000
   └─ Method: Cash
```

**System Checks Before Releasing**:
```
✅ Is reservation CONFIRMED? YES (if not, shows error)
✅ Is release date within reservation dates? YES
   (2026-04-15 >= 2026-04-15 and <= 2026-04-20)
✅ Is item available? YES
✅ Does item have deposit configured? YES
```

---

## STEP 4: Track in Rental System

### Now Customer Appears in Rentals!

**Where**: Rentals Page

**What You See**:
```
Rentals Table
├─ Rental #5001
│  ├─ Customer: John Doe ← Customer appears here!
│  ├─ Item: Wedding Dress
│  ├─ Status: Rented
│  ├─ Due: 2026-04-20
│  └─ Actions: Extend | Collect Payment | Mark Returned
│
└─ ... more rentals
```

**Customer Details in Rental**:
```
Click on Rental #5001
├─ Customer: John Doe (#99)
├─ Phone: 555-1234
├─ Email: john@example.com
├─ Items Rented: 1
│  └─ Wedding Dress (Due: 2026-04-20)
├─ Invoice: #201
│  ├─ Rental Fee: 2500 (paid ✅)
│  ├─ Deposit: 5000 (held)
│  └─ Total: 7500
└─ Actions Available:
   ├─ Extend due date
   ├─ Collect additional payment
   ├─ Send reminder
   └─ Mark as returned
```

---

## STEP 5: Mark Returned

**When**: Customer returns the items

**Where**: Rentals Page → Click rental → Mark Returned

**What You Do**:
1. Click "Mark as Returned"
2. Select condition: Excellent/Good/Fair/Damaged
3. Add notes (optional)
4. Click "Confirm Return"

**What Happens**:
```
✅ Status: Rented → Returned
✅ Return Date: Recorded
✅ On-time?: Checked (returned before/on due date)
✅ Deposit: Refunded to customer (if no damage)
✅ Item: Status → Available (can be rented again)
```

---

## CUSTOMERS: How They Get Into the System

### Before Reservation: Add Customer

**Where**: Customers Page (main menu)

**What You Do**:
1. Click "New Customer"
2. Fill form:
   - First Name: John
   - Last Name: Doe
   - Email: john@example.com
   - Phone: 555-1234
   - Address: 123 Main St
3. Save

**Result**: Customer #99 created, now available in dropdowns

---

### During Reservation: Select Customer

**When Creating Reservation**:
```
New Reservation Form
├─ Customer: [Dropdown ▼] → Select John Doe
├─ Start Date: 2026-04-15
├─ End Date: 2026-04-20
├─ Items: [Select items]
└─ Create
```

---

### After Release: Customer Linked to Rental

```
Customer #99 (John Doe)
├─ Created: 2026-03-15
├─ Total Rentals: 1
├─ Active Rentals:
│  └─ Rental #5001: Wedding Dress (Due: 2026-04-20)
├─ Rental History:
│  └─ Rental #4995: Wedding Dress (Returned 2026-03-10)
└─ Payment History:
   ├─ Payment #1001: 7500 (Rental #5001)
   └─ Payment #995: 6500 (Rental #4995)
```

---

## ITEMS: How They Get Into Reservations

### Items in System

Each physical item has:
```
Item #42
├─ SKU: WD-001
├─ Variant: Wedding Dress Size M
├─ Color: White
├─ Status: Available
├─ Rental Price: 2500 ← Cost to rent
├─ Deposit: 5000 ← Damage guarantee
└─ Location: Shelf A
```

### When Creating Reservation: Select Items

```
New Reservation
├─ Customer: John Doe
├─ Dates: 2026-04-15 to 2026-04-20
├─ Items Available:
│  ├─ Wedding Dress M (Available: 3)
│  ├─ Wedding Dress L (Available: 2)
│  ├─ Veil (Available: 5)
│  └─ ...
├─ Select: Wedding Dress M (qty: 1)
├─ Select: Veil (qty: 1)
└─ Create Reservation
```

### When Releasing: Select Physical Item

```
Release Item Form
├─ Physical Item: [Dropdown ▼]
│  ├─ Item #42 (Wedding Dress M, Available)
│  ├─ Item #43 (Wedding Dress M, Available)
│  └─ Item #44 (Wedding Dress M, Available)
├─ Select: Item #42
├─ Customer: John Doe (auto-filled)
├─ Release Date: 2026-04-15
├─ Due Date: 2026-04-20
└─ Release
```

---

## Error Messages & Solutions

### Error: "Reservation must be confirmed before releasing items"

**Problem**: You tried to release an item but reservation is still PENDING

**Solution**:
1. Go to Reservations page
2. Find the PENDING reservation
3. Click "Confirm" button
4. Now try releasing again

---

### Error: "Release date cannot be before reservation start date"

**Problem**: You entered a release date before the reservation starts

**Solution**:
- Release Date must be >= Reservation Start Date
- Example:
  - Start Date: 2026-04-15
  - Release Date: Must be 2026-04-15 or later

---

### Error: "Item variant does not have a configured deposit amount"

**Problem**: Item doesn't have a deposit set up

**Solution**:
1. Go to Inventory
2. Find the item variant
3. Add deposit amount
4. Try releasing again

---

## Quick Checklist

### Before Confirming Reservation
- [ ] Customer exists in system
- [ ] Items are available for those dates
- [ ] Customer agreed to rental terms

### Before Releasing Item
- [ ] Reservation is CONFIRMED (not pending!)
- [ ] Release date is within reservation dates
- [ ] Physical item is available (status = Available)
- [ ] Item has deposit configured

### After Releasing Item
- [ ] Rental appears in Rentals page
- [ ] Invoice created with correct amounts
- [ ] Deposit collected (if selected)
- [ ] Item status changed to "Rented"

### When Customer Returns
- [ ] Mark item as returned
- [ ] Record condition
- [ ] Refund deposit (if no damage)
- [ ] Item status changed back to "Available"

---

## Visual Summary

```
PROCESS FLOW

┌─────────────────────┐
│  1. CREATE CUSTOMER │
│  Add to system      │
└──────────┬──────────┘
           │
           ↓
┌─────────────────────────┐
│ 2. CREATE RESERVATION   │
│ Status: PENDING 🟠      │
└──────────┬──────────────┘
           │
           ↓
┌─────────────────────────────┐
│ 3. CONFIRM RESERVATION      │
│ Status: CONFIRMED 🟢        │
│ Click "Confirm" button      │
└──────────┬──────────────────┘
           │
           ↓
┌──────────────────────────────┐
│ 4. RELEASE ITEM              │
│ Creates RENTAL               │
│ Creates INVOICE              │
│ Customer now in rental       │
│ system ✅                    │
└──────────┬───────────────────┘
           │
           ↓
┌─────────────────────────────────┐
│ 5. TRACK IN RENTAL SYSTEM       │
│ See in Rentals page             │
│ Monitor due date                │
│ Collect payments                │
└──────────┬──────────────────────┘
           │
           ↓
┌─────────────────────────────┐
│ 6. MARK RETURNED            │
│ Complete rental             │
│ Refund deposit              │
│ Item available again        │
└─────────────────────────────┘
```

---

## Key Takeaways

**Reservation vs Rental**:
- **Reservation**: Booking/planning phase (PENDING → CONFIRMED)
- **Rental**: Execution phase (Item released → Tracked → Returned)

**Status Changes**:
- Reservation: PENDING 🟠 → CONFIRMED 🟢
- Rental: (Created when item released) Rented → Returned

**Customer Location**:
- Created in: Customers page
- Used in: Reservations (select customer)
- Tracked in: Rentals (customer linked to rental)

**Key Actions**:
1. **Confirm** reservation (unlocks release button)
2. **Release** item (creates rental, links customer)
3. **Track** in rentals page
4. **Return** item (complete rental)

---

**✅ Now you understand the complete flow!**

**Next Steps**:
1. Create a test customer
2. Create a reservation for that customer
3. Confirm the reservation
4. Release an item
5. See customer appear in Rentals page

**Questions?** Check WORKFLOW_GUIDE.md for more details!
