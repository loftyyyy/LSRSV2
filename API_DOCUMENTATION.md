# Payment Subsystem API Documentation

## Overview

The Payment Subsystem provides comprehensive payment processing, tracking, and reporting functionality for the LSRSV2 gown/suit rental management system. It integrates with the Rental and Invoice systems to manage:

- Payment processing for multiple payment methods (Cash, Card, GCash, PayMaya, Bank Transfer)
- Payment reversals (void and refund operations)
- Invoice-based billing and tracking
- Collection reports and statistics
- Overdue payment monitoring
- Customer payment reminders

## Payment Methods

The system supports the following payment methods:
- **cash** - Direct cash payment
- **card** - Credit/Debit card payment
- **gcash** - GCash mobile payment
- **paymaya** - PayMaya mobile wallet
- **bank_transfer** - Bank transfer payment

## API Endpoints

### 1. Process Payment

**Endpoint:** `POST /api/payments`

**Description:** Process a payment for an invoice and update invoice balance.

**Request Body:**
```json
{
  "invoice_id": 1,
  "amount": 150.00,
  "payment_method": "cash",
  "notes": "Payment received in store"
}
```

**Parameters:**
- `invoice_id` (required, integer): The ID of the invoice being paid
- `amount` (required, numeric): The amount being paid
- `payment_method` (required, string): One of: cash, card, gcash, paymaya, bank_transfer
- `notes` (optional, string): Additional notes about the payment

**Response (201 Created):**
```json
{
  "payment_id": 1,
  "invoice_id": 1,
  "amount": 150.00,
  "payment_method": "cash",
  "payment_date": "2026-04-08",
  "status": "paid",
  "notes": "Payment received in store",
  "created_by": 1,
  "created_at": "2026-04-08T12:00:00Z"
}
```

**Error Responses:**
- `422 Unprocessable Entity`: Invalid amount, invoice not found, or invoice already fully paid
- `500 Internal Server Error`: Payment processing failure

---

### 2. Void Payment

**Endpoint:** `POST /api/payments/{payment}/void`

**Description:** Void a previously processed payment, restoring the invoice balance.

**Request Body:**
```json
{
  "reason": "Payment recorded in error"
}
```

**Parameters:**
- `reason` (optional, string): Reason for voiding the payment

**Response (200 OK):**
```json
{
  "payment_id": 1,
  "status": "voided",
  "previous_status": "paid",
  "voided_at": "2026-04-08T13:00:00Z",
  "voided_by": 1,
  "invoice": {
    "invoice_id": 1,
    "balance_due": 150.00,
    "amount_paid": 0
  }
}
```

**Error Responses:**
- `404 Not Found`: Payment not found
- `422 Unprocessable Entity`: Payment cannot be voided (already voided/refunded)
- `500 Internal Server Error`: Void operation failed

---

### 3. Process Refund

**Endpoint:** `POST /api/payments/{payment}/refund`

**Description:** Process a full or partial refund for a payment.

**Request Body:**
```json
{
  "refund_amount": 75.00,
  "refund_type": "partial",
  "refund_method": "bank_transfer",
  "reason": "Customer overpaid"
}
```

**Parameters:**
- `refund_amount` (optional, numeric): Amount to refund (if not provided, full amount refunded)
- `refund_type` (required, string): "full" or "partial"
- `refund_method` (optional, string): Method to refund (cash, bank_transfer, gcash, paymaya, check)
- `reason` (optional, string): Reason for refund
- `refund_reference` (optional, string): Reference number for refund tracking
- `refund_notes` (optional, string): Additional notes

**Response (200 OK):**
```json
{
  "refund_id": 1,
  "payment_id": 1,
  "refund_amount": 75.00,
  "refund_type": "partial",
  "refund_method": "bank_transfer",
  "refund_date": "2026-04-08",
  "status": "refunded",
  "invoice": {
    "invoice_id": 1,
    "balance_due": 25.00,
    "amount_paid": 75.00
  }
}
```

**Error Responses:**
- `404 Not Found`: Payment not found
- `422 Unprocessable Entity`: Invalid refund amount or payment cannot be refunded
- `500 Internal Server Error`: Refund processing failed

---

### 4. Get Payment Summary/Statistics

**Endpoint:** `GET /api/payments/summary`

**Description:** Get payment statistics and summary for a date range.

**Query Parameters:**
- `start_date` (optional, date): Start date (format: YYYY-MM-DD)
- `end_date` (optional, date): End date (format: YYYY-MM-DD)

**Response (200 OK):**
```json
{
  "date_range": {
    "from": "2026-04-01",
    "to": "2026-04-08"
  },
  "total_payments": 25,
  "total_amount_paid": 3750.00,
  "payment_breakdown": {
    "cash": {
      "count": 10,
      "amount": 1500.00
    },
    "card": {
      "count": 8,
      "amount": 1200.00
    },
    "gcash": {
      "count": 5,
      "amount": 750.00
    },
    "paymaya": {
      "count": 2,
      "amount": 300.00
    },
    "bank_transfer": {
      "count": 0,
      "amount": 0
    }
  },
  "status_breakdown": {
    "paid": 23,
    "pending": 2,
    "failed": 0
  }
}
```

---

### 5. Get Daily Collection Report

**Endpoint:** `GET /api/payments/daily-collection`

**Description:** Get daily collection details by payment method.

**Query Parameters:**
- `date` (optional, date): Specific date (format: YYYY-MM-DD, defaults to today)

**Response (200 OK):**
```json
{
  "date": "2026-04-08",
  "daily_total": 1200.00,
  "payment_methods": {
    "cash": {
      "amount": 500.00,
      "count": 5,
      "average": 100.00
    },
    "card": {
      "amount": 400.00,
      "count": 4,
      "average": 100.00
    },
    "gcash": {
      "amount": 200.00,
      "count": 2,
      "average": 100.00
    },
    "paymaya": {
      "amount": 100.00,
      "count": 1,
      "average": 100.00
    },
    "bank_transfer": {
      "amount": 0,
      "count": 0,
      "average": 0
    }
  },
  "payment_count": 12
}
```

---

### 6. Get Overdue Payments

**Endpoint:** `GET /api/payments/overdue`

**Description:** Get list of invoices with overdue payments.

**Query Parameters:**
- `days_overdue` (optional, integer): Minimum days overdue (default: 7)

**Response (200 OK):**
```json
{
  "days_threshold": 7,
  "total_overdue": 3,
  "total_overdue_amount": 1500.00,
  "overdue_invoices": [
    {
      "invoice_id": 1,
      "invoice_number": "INV-2026-000001",
      "customer": {
        "customer_id": 1,
        "first_name": "John",
        "last_name": "Doe",
        "email": "john@example.com",
        "phone": "09123456789"
      },
      "amount": 500.00,
      "amount_paid": 0,
      "balance_due": 500.00,
      "due_date": "2026-03-28",
      "days_overdue": 11,
      "invoice_type": "rental"
    }
  ]
}
```

---

### 7. Get Payment Methods

**Endpoint:** `GET /api/payments/methods`

**Description:** Get list of available payment methods.

**Response (200 OK):**
```json
{
  "payment_methods": [
    {
      "code": "cash",
      "label": "Cash",
      "description": "Direct cash payment"
    },
    {
      "code": "card",
      "label": "Credit/Debit Card",
      "description": "Card payment via POS/terminal"
    },
    {
      "code": "gcash",
      "label": "GCash",
      "description": "GCash mobile payment"
    },
    {
      "code": "paymaya",
      "label": "PayMaya",
      "description": "PayMaya mobile wallet"
    },
    {
      "code": "bank_transfer",
      "label": "Bank Transfer",
      "description": "Bank account transfer"
    }
  ]
}
```

---

### 8. Get Payment Statuses

**Endpoint:** `GET /api/payments/statuses`

**Description:** Get list of available payment statuses.

**Response (200 OK):**
```json
{
  "statuses": [
    {
      "code": "pending",
      "label": "Pending",
      "description": "Payment pending processing"
    },
    {
      "code": "paid",
      "label": "Paid",
      "description": "Payment successfully processed"
    },
    {
      "code": "failed",
      "label": "Failed",
      "description": "Payment processing failed"
    },
    {
      "code": "refunded",
      "label": "Refunded",
      "description": "Payment has been refunded"
    },
    {
      "code": "voided",
      "label": "Voided",
      "description": "Payment has been voided"
    }
  ]
}
```

---

### 9. Get Rental Fee Details

**Endpoint:** `GET /api/invoices/{invoice}/fee-details` or `GET /api/payments/rental-fee-details`

**Description:** Get breakdown of rental fees including rental fee, deposit, and penalties for an invoice.

**Response (200 OK):**
```json
{
  "invoice_id": 1,
  "invoice_number": "INV-2026-000001",
  "customer": {
    "customer_id": 1,
    "first_name": "John",
    "last_name": "Doe"
  },
  "rental": {
    "rental_id": 1,
    "item_name": "Wedding Gown - Classic White",
    "sku": "GOWN-001",
    "released_date": "2026-04-01",
    "due_date": "2026-04-08",
    "original_due_date": "2026-04-08",
    "return_date": null
  },
  "fees": {
    "rental_fee": {
      "description": "Rental: Wedding Gown - Classic White",
      "amount": 100.00
    },
    "security_deposit": {
      "description": "Security Deposit: Wedding Gown",
      "amount": 500.00
    },
    "late_penalty": {
      "description": "Late rental penalty (3 days)",
      "amount": 30.00,
      "calculated": true,
      "days_late": 3
    },
    "damage_charges": {
      "description": "Damage assessment charges",
      "amount": 50.00,
      "calculated": false
    }
  },
  "summary": {
    "subtotal": 680.00,
    "discount": 0,
    "tax": 0,
    "total": 680.00,
    "amount_paid": 100.00,
    "balance_due": 580.00
  }
}
```

---

### 10. Generate Billing Report

**Endpoint:** `GET /api/payments/billing-report`

**Description:** Generate billing report for a date range in various formats.

**Query Parameters:**
- `start_date` (required, date): Start date (format: YYYY-MM-DD)
- `end_date` (required, date): End date (format: YYYY-MM-DD)
- `report_type` (optional, string): "daily", "weekly", or "monthly" (default: "daily")

**Response (200 OK):**
```json
{
  "report_type": "daily",
  "date_range": {
    "from": "2026-04-01",
    "to": "2026-04-08"
  },
  "generated_at": "2026-04-08T12:00:00Z",
  "summary": {
    "total_invoices": 25,
    "total_revenue": 3750.00,
    "total_deposits": 2500.00,
    "total_late_fees": 150.00,
    "total_penalties": 75.00,
    "total_paid": 3500.00,
    "total_outstanding": 500.00,
    "collection_rate": 0.9333
  },
  "daily_breakdown": [
    {
      "date": "2026-04-01",
      "invoices_created": 3,
      "total_amount": 450.00,
      "total_collected": 450.00,
      "by_method": {
        "cash": 150.00,
        "card": 200.00,
        "gcash": 100.00,
        "paymaya": 0,
        "bank_transfer": 0
      }
    }
  ]
}
```

---

## Item Release API (New Flow)

### Release Item to Customer

**Endpoint:** `POST /api/rentals/release-item`

**Description:** Release a rented item to customer with automatic invoice and deposit collection.

**Request Body:**
```json
{
  "item_id": 42,
  "customer_id": 5,
  "released_date": "2026-04-08",
  "due_date": "2026-04-15",
  "reservation_id": null,
  "collect_deposit": true,
  "deposit_payment_method": "cash",
  "release_notes": "Item released to customer at counter"
}
```

**Parameters:**
- `item_id` (required, integer): Physical inventory item ID (explicit, no auto-lookup)
- `customer_id` (required, integer): Customer ID
- `released_date` (required, date): Item release date
- `due_date` (required, date): Item due date (must be after released_date)
- `reservation_id` (optional, integer): Associated reservation ID
- `collect_deposit` (optional, boolean): Whether to collect deposit immediately (default: true)
- `deposit_payment_method` (optional, string): Payment method for deposit (required if collect_deposit=true)
- `deposit_payment_notes` (optional, string): Notes about deposit payment
- `release_notes` (optional, string): Additional notes about the release

**Response (201 Created):**
```json
{
  "message": "Item released successfully to customer",
  "data": {
    "rental_id": 1,
    "item_id": 42,
    "customer_id": 5,
    "reservation_id": null,
    "released_date": "2026-04-08",
    "due_date": "2026-04-15",
    "original_due_date": "2026-04-15",
    "return_date": null,
    "deposit_amount": 500.00,
    "deposit_status": "held",
    "deposit_collected_by": 1,
    "deposit_collected_at": "2026-04-08T12:00:00Z",
    "status": "rented",
    "extension_count": 0,
    "customer": {
      "customer_id": 5,
      "first_name": "Jane",
      "last_name": "Smith"
    },
    "item": {
      "item_id": 42,
      "sku": "GOWN-042",
      "name": "Wedding Gown - Classic White"
    }
  }
}
```

**Error Responses:**

**404 Not Found:**
```json
{
  "message": "Physical item #99999 not found in inventory.",
  "code": 404
}
```

**422 Unprocessable Entity:**
```json
{
  "message": "Item #GOWN-042 is currently rented. Only available items can be released.",
  "code": 422
}
```

```json
{
  "message": "Item variant 'Wedding Gown' does not have a configured deposit amount. Please configure a deposit amount in the variant settings.",
  "code": 422
}
```

---

## Error Handling

All API endpoints follow standard HTTP status codes:

| Status Code | Meaning |
|---|---|
| 200 | OK - Request successful |
| 201 | Created - Resource created successfully |
| 400 | Bad Request - Invalid request format |
| 404 | Not Found - Resource not found |
| 422 | Unprocessable Entity - Validation error |
| 500 | Internal Server Error - Server error |

Error responses include detailed messages:

```json
{
  "message": "Error description",
  "error": "Additional details or code",
  "errors": {
    "field_name": ["Validation error message"]
  }
}
```

---

## Authentication

All endpoints require authentication. Include the user's authentication token in the request header:

```
Authorization: Bearer {token}
```

---

## Key Features

### 1. Multi-Method Payment Processing
- Supports 5 different payment methods
- Automatic invoice balance updates
- Payment status tracking

### 2. Payment Reversals
- Void payments to reverse transactions
- Partial/full refund capabilities
- Audit trail for all reversals

### 3. Reporting
- Daily collection reports
- Billing reports with configurable date ranges
- Overdue payment tracking
- Statistical summaries

### 4. Item Release Workflow
- Explicit item ID requirement (no auto-selection fallbacks)
- Automatic deposit collection through PaymentService
- Automatic invoice generation
- Integrated payment processing

### 5. Invoice Management
- Multiple line item support
- Balance tracking and updates
- Payment progress monitoring

---

## Integration Examples

### Example 1: Complete Item Release with Deposit Collection

```bash
curl -X POST http://localhost:8000/api/rentals/release-item \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{
    "item_id": 42,
    "customer_id": 5,
    "released_date": "2026-04-08",
    "due_date": "2026-04-15",
    "collect_deposit": true,
    "deposit_payment_method": "cash",
    "release_notes": "Counter release"
  }'
```

### Example 2: Process Payment

```bash
curl -X POST http://localhost:8000/api/payments \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{
    "invoice_id": 1,
    "amount": 150.00,
    "payment_method": "card",
    "notes": "Card payment at counter"
  }'
```

### Example 3: Get Daily Collection Report

```bash
curl -X GET "http://localhost:8000/api/payments/daily-collection?date=2026-04-08" \
  -H "Authorization: Bearer {token}"
```

### Example 4: Process Refund

```bash
curl -X POST http://localhost:8000/api/payments/1/refund \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{
    "refund_amount": 75.00,
    "refund_type": "partial",
    "refund_method": "bank_transfer",
    "reason": "Customer overpaid",
    "refund_notes": "Transferred to customer bank account"
  }'
```

---

## Notes

- All date parameters should be in `YYYY-MM-DD` format
- All amounts are in the system's default currency
- Payment methods are case-sensitive (lowercase)
- Transactions are atomic - all-or-nothing processing
- All modifications are logged with user ID for audit trails
