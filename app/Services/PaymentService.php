<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\Payment;
use App\Models\PaymentStatus;
use App\Models\Reservation;
use App\Models\ReservationStatus;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PaymentService
{
    /**
     * Available payment methods
     */
    public const PAYMENT_METHODS = [
        'cash' => 'Cash',
        'card' => 'Credit/Debit Card',
        'gcash' => 'GCash',
        'paymaya' => 'PayMaya',
        'bank_transfer' => 'Bank Transfer',
    ];

    /**
     * Process a new payment
     *
     * @param  array  $data  Payment data
     * @param  int  $processedBy  User ID who processed the payment
     *
     * @throws \Exception
     */
    public function processPayment(array $data, int $processedBy): Payment
    {
        return DB::transaction(function () use ($data, $processedBy) {
            $invoice = Invoice::findOrFail($data['invoice_id']);

            // Validate payment amount
            if ($data['amount'] <= 0) {
                throw new \InvalidArgumentException('Payment amount must be greater than zero');
            }

            if ($data['amount'] > $invoice->balance_due) {
                throw new \InvalidArgumentException('Payment amount cannot exceed the balance due');
            }

            // Get payment status - use provided status_id or default to 'paid'
            $statusId = $data['status_id'] ?? $this->getPaymentStatusId('paid');
            if (! $statusId) {
                // Fallback to paid status if not provided
                $statusId = $this->getPaymentStatusId('paid');
                if (! $statusId) {
                    throw new \RuntimeException('Payment status configuration error');
                }
            }

            // Generate payment reference
            $paymentReference = $this->generatePaymentReference();

            // Create payment record
            $payment = Payment::create([
                'invoice_id' => $invoice->invoice_id,
                'payment_reference' => $paymentReference,
                'amount' => $data['amount'],
                'payment_method' => $data['payment_method'],
                'payment_date' => $data['payment_date'] ?? now(),
                'notes' => $data['notes'] ?? null,
                'processed_by' => $processedBy,
                'status_id' => $statusId,
            ]);

            // Update invoice totals
            $this->updateInvoiceAfterPayment($invoice, $payment->amount);

            // Auto-confirm reservation if deposit is paid and invoice is fully paid
            if ($invoice->reservation_id && $invoice->balance_due <= 0) {
                $reservation = Reservation::find($invoice->reservation_id);
                if ($reservation && strtolower($reservation->status->status_name ?? '') === 'pending') {
                    $confirmedStatus = ReservationStatus::where('status_name', 'confirmed')->first();
                    if ($confirmedStatus) {
                        $reservation->update([
                            'status_id' => $confirmedStatus->status_id,
                            'confirmed_at' => now(),
                            'confirmed_by' => $processedBy,
                        ]);
                    }
                }

                // Set inventory status to 'reserved' for all items in this reservation
                $this->updateReservationItemsStatusToReserved($invoice->reservation_id);

                // If there are already rentals associated with this reservation (e.g. manual confirmation before payment),
                // we should update their deposit status now that the deposit is fully paid
                $hasDeposit = $invoice->invoiceItems()->where('item_type', 'deposit')->exists();
                if ($hasDeposit) {
                    $rentals = \App\Models\Rental::where('reservation_id', $invoice->reservation_id)
                        ->where('deposit_status', 'not_collected')
                        ->get();

                    foreach ($rentals as $r) {
                        $r->update([
                            'deposit_status' => 'held',
                            'deposit_collected_by' => $processedBy,
                            'deposit_collected_at' => $payment->payment_date,
                        ]);
                    }
                }
            }

            // Auto-update rental status to 'rented' when rental fee invoice is fully paid
            if ($invoice->rental_id && $invoice->invoice_type === 'rental' && $invoice->balance_due <= 0) {
                $rental = \App\Models\Rental::find($invoice->rental_id);
                if ($rental) {
                    $rentedStatus = \App\Models\RentalStatus::whereRaw('LOWER(status_name) = ?', ['rented'])->first();
                    if ($rentedStatus && strtolower($rental->status?->status_name ?? '') !== 'rented') {
                        $rental->update([
                            'status_id' => $rentedStatus->status_id,
                        ]);
                    }

                    // Automatically hold deposit if this rental invoice included the deposit
                    $hasDeposit = $invoice->invoiceItems()->where('item_type', 'deposit')->exists();
                    if ($hasDeposit && $rental->deposit_status === 'not_collected') {
                        $rental->update([
                            'deposit_status' => 'held',
                            'deposit_collected_by' => $processedBy,
                            'deposit_collected_at' => $payment->payment_date,
                        ]);
                    }
                }
            }

            return $payment->fresh(['invoice', 'invoice.customer', 'status', 'processedBy']);
        });
    }

    /**
     * Void a payment (cancel it)
     */
    public function voidPayment(Payment $payment, string $reason, int $processedBy): Payment
    {
        return DB::transaction(function () use ($payment, $reason) {
            // Check if payment can be voided
            $currentStatus = $payment->status?->status_name;
            if (in_array(strtolower($currentStatus), ['voided', 'refunded'])) {
                throw new \RuntimeException('Payment has already been voided or refunded');
            }

            // Get voided status
            $voidedStatusId = $this->getPaymentStatusId('voided');
            if (! $voidedStatusId) {
                throw new \RuntimeException('Voided payment status is not configured');
            }

            // Update payment status
            $payment->update([
                'status_id' => $voidedStatusId,
                'notes' => $payment->notes
                    ? $payment->notes."\n\nVOIDED: ".$reason
                    : 'VOIDED: '.$reason,
            ]);

            // Reverse the invoice amounts
            $invoice = $payment->invoice;
            $invoice->amount_paid -= $payment->amount;
            $invoice->balance_due = $invoice->total_amount - $invoice->amount_paid;
            $invoice->save();

            return $payment->fresh(['invoice', 'status', 'processedBy']);
        });
    }

    /**
     * Process a refund
     */
    public function processRefund(
        Payment $payment,
        float $refundAmount,
        string $reason,
        string $refundMethod,
        int $processedBy
    ): Payment {
        return DB::transaction(function () use ($payment, $refundAmount, $reason, $refundMethod, $processedBy) {
            // Validate refund amount
            if ($refundAmount <= 0) {
                throw new \InvalidArgumentException('Refund amount must be greater than zero');
            }

            if ($refundAmount > $payment->amount) {
                throw new \InvalidArgumentException('Refund amount cannot exceed original payment amount');
            }

            // Check if payment can be refunded
            $currentStatus = $payment->status?->status_name;
            if (in_array(strtolower($currentStatus), ['voided', 'refunded', 'failed'])) {
                throw new \RuntimeException('This payment cannot be refunded');
            }

            // Get refunded status
            $refundedStatusId = $this->getPaymentStatusId('refunded');
            if (! $refundedStatusId) {
                throw new \RuntimeException('Refunded payment status is not configured');
            }

            // Create refund record (negative payment)
            $refundPayment = Payment::create([
                'invoice_id' => $payment->invoice_id,
                'payment_reference' => $this->generateRefundReference($payment->payment_reference),
                'amount' => -$refundAmount,
                'payment_method' => $refundMethod,
                'payment_date' => now(),
                'notes' => "REFUND for payment #{$payment->payment_reference}: {$reason}",
                'processed_by' => $processedBy,
                'status_id' => $refundedStatusId,
            ]);

            // Update original payment if fully refunded
            if ($refundAmount >= $payment->amount) {
                $payment->update([
                    'status_id' => $refundedStatusId,
                    'notes' => $payment->notes
                        ? $payment->notes."\n\nFULLY REFUNDED: ".$reason
                        : 'FULLY REFUNDED: '.$reason,
                ]);
            } else {
                $payment->update([
                    'notes' => $payment->notes
                        ? $payment->notes."\n\nPARTIALLY REFUNDED (₱".number_format($refundAmount, 2).'): '.$reason
                        : 'PARTIALLY REFUNDED (₱'.number_format($refundAmount, 2).'): '.$reason,
                ]);
            }

            // Update invoice
            $invoice = $payment->invoice;
            $invoice->amount_paid -= $refundAmount;
            $invoice->balance_due = $invoice->total_amount - $invoice->amount_paid;
            $invoice->save();

            return $refundPayment->fresh(['invoice', 'status', 'processedBy']);
        });
    }

    /**
     * Get rental fee details for a customer
     */
    public function getRentalFeeDetails(
        ?int $invoiceId = null,
        ?int $reservationId = null,
        ?int $rentalId = null,
        ?int $customerId = null
    ): array {
        $query = Invoice::with([
            'invoiceItems.item',
            'customer',
            'reservation.reservationItems.variant',
            'rental.item',
            'payments.status',
        ]);

        if ($invoiceId) {
            $invoice = $query->find($invoiceId);
        } elseif ($reservationId) {
            $invoice = $query->where('reservation_id', $reservationId)->first();
        } elseif ($rentalId) {
            $invoice = $query->where('rental_id', $rentalId)->first();
        } elseif ($customerId) {
            // Get all invoices for customer
            $invoices = $query->where('customer_id', $customerId)
                ->orderBy('invoice_date', 'desc')
                ->get();

            return $this->formatMultipleInvoiceFeeDetails($invoices);
        } else {
            return ['error' => 'Please provide invoice_id, reservation_id, rental_id, or customer_id'];
        }

        if (! $invoice) {
            return ['error' => 'Invoice not found'];
        }

        return $this->formatInvoiceFeeDetails($invoice);
    }

    /**
     * Get payment summary statistics
     */
    public function getPaymentSummary(?Carbon $startDate = null, ?Carbon $endDate = null): array
    {
        $startDate = $startDate ?? Carbon::now()->startOfMonth();
        $endDate = $endDate ?? Carbon::now()->endOfMonth();

        $query = Payment::whereBetween('payment_date', [$startDate, $endDate]);

        // Get completed payments
        $completedPayments = $query->clone()
            ->whereHas('status', function ($q) {
                $q->whereIn(DB::raw('LOWER(status_name)'), ['paid', 'completed']);
            });

        // Get pending payments
        $pendingPayments = $query->clone()
            ->whereHas('status', function ($q) {
                $q->where(DB::raw('LOWER(status_name)'), 'pending');
            });

        // Payment method breakdown
        $methodBreakdown = $completedPayments->clone()
            ->select('payment_method', DB::raw('COUNT(*) as count'), DB::raw('SUM(amount) as total'))
            ->groupBy('payment_method')
            ->get()
            ->mapWithKeys(function ($item) {
                return [
                    $item->payment_method => [
                        'count' => $item->count,
                        'total' => (float) $item->total,
                        'label' => self::PAYMENT_METHODS[$item->payment_method] ?? ucfirst($item->payment_method),
                    ],
                ];
            });

        return [
            'period' => [
                'start_date' => $startDate->format('Y-m-d'),
                'end_date' => $endDate->format('Y-m-d'),
            ],
            'summary' => [
                'total_transactions' => $query->count(),
                'total_amount' => (float) $query->sum('amount'),
                'completed_count' => $completedPayments->count(),
                'completed_amount' => (float) $completedPayments->sum('amount'),
                'pending_count' => $pendingPayments->count(),
                'pending_amount' => (float) $pendingPayments->sum('amount'),
            ],
            'by_method' => $methodBreakdown,
        ];
    }

    /**
     * Get pending payments that need follow-up
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getOverduePayments(int $daysOverdue = 7)
    {
        return Invoice::with(['customer', 'payments', 'rental', 'reservation'])
            ->where('balance_due', '>', 0)
            ->where('due_date', '<', Carbon::now()->subDays($daysOverdue))
            ->orderBy('due_date', 'asc')
            ->get();
    }

    /**
     * Get daily collection report
     */
    public function getDailyCollectionReport(?Carbon $date = null): array
    {
        $date = $date ?? Carbon::now();
        $startOfDay = $date->copy()->startOfDay();
        $endOfDay = $date->copy()->endOfDay();

        $payments = Payment::with(['invoice.customer', 'processedBy', 'status'])
            ->whereBetween('payment_date', [$startOfDay, $endOfDay])
            ->whereHas('status', function ($q) {
                $q->whereIn(DB::raw('LOWER(status_name)'), ['paid', 'completed']);
            })
            ->orderBy('payment_date', 'asc')
            ->get();

        $byMethod = $payments->groupBy('payment_method')->map(function ($group) {
            return [
                'count' => $group->count(),
                'total' => (float) $group->sum('amount'),
            ];
        });

        $byStaff = $payments->groupBy('processed_by')->map(function ($group) {
            $staffName = $group->first()->processedBy?->name ?? 'Unknown';

            return [
                'staff_name' => $staffName,
                'count' => $group->count(),
                'total' => (float) $group->sum('amount'),
            ];
        });

        return [
            'date' => $date->format('Y-m-d'),
            'total_collections' => (float) $payments->sum('amount'),
            'transaction_count' => $payments->count(),
            'by_method' => $byMethod,
            'by_staff' => $byStaff,
            'transactions' => $payments->map(function ($payment) {
                return [
                    'payment_id' => $payment->payment_id,
                    'reference' => $payment->payment_reference,
                    'amount' => (float) $payment->amount,
                    'method' => $payment->payment_method,
                    'customer' => $payment->invoice?->customer
                        ? $payment->invoice->customer->first_name.' '.$payment->invoice->customer->last_name
                        : 'N/A',
                    'invoice_number' => $payment->invoice?->invoice_number,
                    'time' => $payment->payment_date->format('H:i:s'),
                    'processed_by' => $payment->processedBy?->name ?? 'System',
                ];
            }),
        ];
    }

    /**
     * Generate billing report data
     *
     * @param  string  $reportType  daily|weekly|monthly
     */
    public function generateBillingReport(Carbon $startDate, Carbon $endDate, string $reportType = 'daily'): array
    {
        $invoices = Invoice::with(['customer', 'payments', 'invoiceItems', 'rental', 'reservation'])
            ->whereBetween('invoice_date', [$startDate, $endDate])
            ->orderBy('invoice_date', 'desc')
            ->get();

        $groupedData = $this->groupByPeriod($invoices, $reportType, 'invoice_date');

        // Invoice type breakdown
        $typeBreakdown = $invoices->groupBy('invoice_type')->map(function ($group) {
            return [
                'count' => $group->count(),
                'total_amount' => (float) $group->sum('total_amount'),
                'amount_paid' => (float) $group->sum('amount_paid'),
                'balance_due' => (float) $group->sum('balance_due'),
            ];
        });

        // Payment status breakdown
        $paidInvoices = $invoices->filter(fn ($i) => $i->balance_due <= 0);
        $partiallyPaidInvoices = $invoices->filter(fn ($i) => $i->balance_due > 0 && $i->amount_paid > 0);
        $unpaidInvoices = $invoices->filter(fn ($i) => $i->amount_paid == 0);

        return [
            'period' => [
                'start_date' => $startDate->format('Y-m-d'),
                'end_date' => $endDate->format('Y-m-d'),
                'report_type' => $reportType,
            ],
            'summary' => [
                'total_invoices' => $invoices->count(),
                'total_amount' => (float) $invoices->sum('total_amount'),
                'total_paid' => (float) $invoices->sum('amount_paid'),
                'total_balance_due' => (float) $invoices->sum('balance_due'),
                'collection_rate' => $invoices->sum('total_amount') > 0
                    ? round(($invoices->sum('amount_paid') / $invoices->sum('total_amount')) * 100, 2)
                    : 0,
            ],
            'status_breakdown' => [
                'paid' => [
                    'count' => $paidInvoices->count(),
                    'amount' => (float) $paidInvoices->sum('total_amount'),
                ],
                'partially_paid' => [
                    'count' => $partiallyPaidInvoices->count(),
                    'amount' => (float) $partiallyPaidInvoices->sum('total_amount'),
                    'balance_due' => (float) $partiallyPaidInvoices->sum('balance_due'),
                ],
                'unpaid' => [
                    'count' => $unpaidInvoices->count(),
                    'amount' => (float) $unpaidInvoices->sum('total_amount'),
                ],
            ],
            'by_type' => $typeBreakdown,
            'grouped_data' => $groupedData,
            'invoices' => $invoices,
        ];
    }

    /**
     * Update invoice after payment
     */
    private function updateInvoiceAfterPayment(Invoice $invoice, float $paymentAmount): void
    {
        $invoice->amount_paid += $paymentAmount;
        $invoice->balance_due = max($invoice->total_amount - $invoice->amount_paid, 0);

        // Update invoice status to 'paid' when fully paid
        if ($invoice->balance_due <= 0) {
            $paidStatus = PaymentStatus::whereRaw('LOWER(status_name) = ?', ['paid'])->first();
            if ($paidStatus) {
                $invoice->status_id = $paidStatus->status_id;
            }
        }

        $invoice->save();
    }

    /**
     * Format invoice fee details
     */
    private function formatInvoiceFeeDetails(Invoice $invoice): array
    {
        $items = $invoice->invoiceItems;

        $rentalFees = $items->where('item_type', 'rental_fee');
        $deposits = $items->where('item_type', 'deposit');
        $penalties = $items->whereIn('item_type', ['penalty', 'late_fee']);
        $damageFees = $items->whereIn('item_type', ['damage_fee', 'cleaning_fee']);
        $otherCharges = $items->whereNotIn('item_type', ['rental_fee', 'deposit', 'penalty', 'late_fee', 'damage_fee', 'cleaning_fee']);

        return [
            'invoice' => [
                'invoice_id' => $invoice->invoice_id,
                'invoice_number' => $invoice->invoice_number,
                'invoice_date' => $invoice->invoice_date?->format('Y-m-d'),
                'due_date' => $invoice->due_date?->format('Y-m-d'),
                'invoice_type' => $invoice->invoice_type,
            ],
            'customer' => $invoice->customer ? [
                'customer_id' => $invoice->customer->customer_id,
                'name' => $invoice->customer->first_name.' '.$invoice->customer->last_name,
                'email' => $invoice->customer->email,
                'contact_number' => $invoice->customer->contact_number,
            ] : null,
            'breakdown' => [
                'rental_fees' => $this->formatLineItems($rentalFees),
                'deposits' => $this->formatLineItems($deposits),
                'penalties' => $this->formatLineItems($penalties),
                'damage_fees' => $this->formatLineItems($damageFees),
                'other_charges' => $this->formatLineItems($otherCharges),
            ],
            'totals' => [
                'subtotal' => (float) $invoice->subtotal,
                'discount' => (float) $invoice->discount,
                'tax' => (float) $invoice->tax,
                'total_amount' => (float) $invoice->total_amount,
                'amount_paid' => (float) $invoice->amount_paid,
                'balance_due' => (float) $invoice->balance_due,
            ],
            'payment_history' => $invoice->payments->map(function ($payment) {
                return [
                    'payment_id' => $payment->payment_id,
                    'reference' => $payment->payment_reference,
                    'amount' => (float) $payment->amount,
                    'method' => $payment->payment_method,
                    'date' => $payment->payment_date?->format('Y-m-d H:i:s'),
                    'status' => $payment->status?->status_name,
                ];
            }),
        ];
    }

    /**
     * Format multiple invoices fee details
     */
    private function formatMultipleInvoiceFeeDetails($invoices): array
    {
        $totalAmount = $invoices->sum('total_amount');
        $totalPaid = $invoices->sum('amount_paid');
        $totalDue = $invoices->sum('balance_due');

        return [
            'summary' => [
                'total_invoices' => $invoices->count(),
                'total_amount' => (float) $totalAmount,
                'total_paid' => (float) $totalPaid,
                'total_balance_due' => (float) $totalDue,
            ],
            'invoices' => $invoices->map(function ($invoice) {
                return $this->formatInvoiceFeeDetails($invoice);
            }),
        ];
    }

    /**
     * Format line items
     */
    private function formatLineItems($items): array
    {
        return $items->map(function ($item) {
            return [
                'description' => $item->description,
                'item_type' => $item->item_type,
                'quantity' => $item->quantity,
                'unit_price' => (float) $item->unit_price,
                'total_price' => (float) $item->total_price,
                'item_name' => $item->item?->name,
            ];
        })->values()->toArray();
    }

    /**
     * Group data by period
     */
    private function groupByPeriod($collection, string $reportType, string $dateField)
    {
        return $collection->groupBy(function ($item) use ($reportType, $dateField) {
            $date = $item->{$dateField};
            if (! $date) {
                return 'Unknown';
            }

            switch ($reportType) {
                case 'daily':
                    return $date->format('Y-m-d');
                case 'weekly':
                    return $date->format('Y-W');
                case 'monthly':
                    return $date->format('Y-m');
                default:
                    return $date->format('Y-m-d');
            }
        })->map(function ($group) {
            return [
                'count' => $group->count(),
                'total_amount' => (float) $group->sum('total_amount'),
                'amount_paid' => (float) $group->sum('amount_paid'),
                'balance_due' => (float) $group->sum('balance_due'),
            ];
        });
    }

    /**
     * Get payment status ID by name
     */
    private function getPaymentStatusId(string $name): ?int
    {
        return PaymentStatus::whereRaw('LOWER(status_name) = ?', [strtolower($name)])->value('status_id');
    }

    /**
     * Generate payment reference
     */
    private function generatePaymentReference(): string
    {
        return 'PAY-'.now()->format('YmdHis').'-'.strtoupper(Str::random(6));
    }

    /**
     * Generate refund reference
     */
    private function generateRefundReference(string $originalReference): string
    {
        return 'REF-'.$originalReference.'-'.strtoupper(Str::random(4));
    }

    /**
     * Update inventory items status to 'reserved' for a reservation
     */
    private function updateReservationItemsStatusToReserved(int $reservationId): void
    {
        // Get the 'reserved' inventory status
        $reservedStatus = \App\Models\InventoryStatus::whereRaw('LOWER(status_name) = ?', ['reserved'])->first();
        if (! $reservedStatus) {
            return; // Status doesn't exist, skip
        }

        // Get all items in this reservation
        $reservationItems = \App\Models\ReservationItem::where('reservation_id', $reservationId)->get();

        foreach ($reservationItems as $reservationItem) {
            $allocatedCount = \App\Models\ReservationItemAllocation::where('reservation_item_id', $reservationItem->reservation_item_id)
                ->where('allocation_status', 'allocated')
                ->count();

            $needed = $reservationItem->quantity - $allocatedCount;

            if ($needed > 0) {
                // Find available items
                $availableItems = \App\Models\Inventory::where('variant_id', $reservationItem->variant_id)
                    ->whereHas('status', function ($q) {
                        $q->whereRaw('LOWER(status_name) = ?', ['available']);
                    })
                    ->limit($needed)
                    ->get();

                foreach ($availableItems as $invItem) {
                    \App\Models\ReservationItemAllocation::create([
                        'reservation_item_id' => $reservationItem->reservation_item_id,
                        'item_id' => $invItem->item_id,
                        'allocation_status' => 'allocated',
                        'allocated_at' => now(),
                        'updated_by' => \Illuminate\Support\Facades\Auth::id() ?? 1,
                    ]);

                    $invItem->update(['status_id' => $reservedStatus->status_id]);

                    // Update item_id on the reservation item if it's null (for single quantity items backward compatibility)
                    if (! $reservationItem->item_id && $reservationItem->quantity == 1) {
                        $reservationItem->update(['item_id' => $invItem->item_id]);
                    }
                }
            }
        }
    }

    /**
     * Get available payment methods
     */
    public static function getPaymentMethods(): array
    {
        return self::PAYMENT_METHODS;
    }
}
