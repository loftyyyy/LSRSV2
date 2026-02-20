<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\PaymentStatus;
use App\Models\Rental;
use App\Models\DepositReturn;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use Illuminate\Support\Facades\DB;

class DepositService
{
    /**
     * Collect deposit when item is released to customer
     * 
     * @param Rental $rental
     * @param float $amount
     * @param int $collectedBy User ID
     * @return Rental
     */
    public function collectDeposit(
        Rental $rental,
        float $amount,
        int $collectedBy,
        string $paymentMethod,
        ?string $notes = null
    ): Rental
    {
        if ($amount <= 0) {
            throw new \InvalidArgumentException('Deposit amount must be greater than zero');
        }

        if ($rental->deposit_status !== 'not_collected') {
            throw new \RuntimeException('Deposit has already been collected or processed for this rental');
        }

        return DB::transaction(function () use ($rental, $amount, $collectedBy, $paymentMethod, $notes) {
            $paidStatusId = $this->getPaymentStatusId('paid');
            if (!$paidStatusId) {
                throw new \RuntimeException('Paid payment status is not configured.');
            }

            $invoiceType = $rental->reservation_id ? 'reservation' : 'rental';
            $invoice = $this->findOrCreateInvoice($rental, $invoiceType, $collectedBy);

            $depositLine = $invoice->invoiceItems()
                ->where('item_type', 'deposit')
                ->first();

            if ($depositLine) {
                $depositLine->update([
                    'description' => 'Security deposit collected',
                    'item_id' => $rental->item_id,
                    'quantity' => 1,
                    'unit_price' => $amount,
                    'total_price' => $amount,
                ]);
            } else {
                $invoice->invoiceItems()->create([
                    'description' => 'Security deposit collected',
                    'item_type' => 'deposit',
                    'item_id' => $rental->item_id,
                    'quantity' => 1,
                    'unit_price' => $amount,
                    'total_price' => $amount,
                ]);
            }

            $payment = Payment::create([
                'invoice_id' => $invoice->invoice_id,
                'payment_reference' => $this->generateReference('DEP-PAY'),
                'amount' => $amount,
                'payment_method' => $paymentMethod,
                'payment_date' => now(),
                'notes' => $notes ?: 'Deposit collected during release',
                'processed_by' => $collectedBy,
                'status_id' => $paidStatusId,
            ]);

            $rental->update([
                'deposit_amount' => $amount,
                'deposit_status' => 'held',
                'deposit_collected_by' => $collectedBy,
                'deposit_collected_at' => $payment->payment_date,
            ]);

            $this->recalculateInvoiceTotals($invoice);

            return $rental->fresh();
        });
    }

    /**
     * Process full deposit return (no deductions)
     * 
     * @param Rental $rental
     * @param string $returnMethod
     * @param int $processedBy User ID
     * @param string|null $reference Transaction reference
     * @return DepositReturn
     */
    public function returnFullDeposit(
        Rental $rental, 
        string $returnMethod, 
        int $processedBy, 
        ?string $reference = null
    ): DepositReturn {
        $this->validateCanReturn($rental);

        return DB::transaction(function () use ($rental, $returnMethod, $processedBy, $reference) {
            // Create deposit return record
            $depositReturn = DepositReturn::create([
                'rental_id' => $rental->rental_id,
                'customer_id' => $rental->customer_id,
                'original_deposit_amount' => $rental->deposit_amount,
                'amount_returned' => $rental->deposit_amount,
                'amount_deducted' => 0,
                'deductions_breakdown' => [],
                'return_method' => $returnMethod,
                'return_reference' => $reference,
                'status' => 'processed',
                'notes' => 'Full deposit returned - no deductions',
                'processed_by' => $processedBy,
                'processed_at' => now(),
            ]);

            // Update rental
            $rental->update([
                'deposit_status' => 'returned_full',
                'deposit_returned_amount' => $rental->deposit_amount,
                'deposit_deducted_amount' => 0,
            ]);

            return $depositReturn;
        });
    }

    /**
     * Process partial deposit return (with deductions)
     * 
     * @param Rental $rental
     * @param array $deductions Array of ['type' => 'damage', 'amount' => 500, 'reason' => '...']
     * @param string $returnMethod
     * @param int $processedBy
     * @param int|null $inspectionId
     * @param string|null $reference
     * @return DepositReturn
     */
    public function returnPartialDeposit(
        Rental $rental,
        array $deductions,
        string $returnMethod,
        int $processedBy,
        ?int $inspectionId = null,
        ?string $reference = null
    ): DepositReturn {
        $this->validateCanReturn($rental);

        return DB::transaction(function () use ($rental, $deductions, $returnMethod, $processedBy, $inspectionId, $reference) {
            $totalDeductions = collect($deductions)->sum('amount');
            
            if ($totalDeductions >= $rental->deposit_amount) {
                throw new \InvalidArgumentException('Total deductions cannot exceed deposit amount');
            }

            $amountReturned = $rental->deposit_amount - $totalDeductions;

            // Create deposit return record
            $depositReturn = DepositReturn::create([
                'rental_id' => $rental->rental_id,
                'customer_id' => $rental->customer_id,
                'original_deposit_amount' => $rental->deposit_amount,
                'amount_returned' => $amountReturned,
                'amount_deducted' => $totalDeductions,
                'deductions_breakdown' => $deductions,
                'return_method' => $returnMethod,
                'return_reference' => $reference,
                'inspection_id' => $inspectionId,
                'status' => 'processed',
                'notes' => 'Partial deposit return with deductions',
                'processed_by' => $processedBy,
                'processed_at' => now(),
            ]);

            // Update rental
            $rental->update([
                'deposit_status' => 'returned_partial',
                'deposit_returned_amount' => $amountReturned,
                'deposit_deducted_amount' => $totalDeductions,
            ]);

            // Create deduction invoice for accounting visibility
            $this->createDeductionInvoice($rental, $deductions, $processedBy);

            return $depositReturn;
        });
    }

    /**
     * Forfeit entire deposit (no-show, cancellation, or total damage)
     * 
     * @param Rental $rental
     * @param string $reason
     * @param int $processedBy
     * @return DepositReturn
     */
    public function forfeitDeposit(
        Rental $rental,
        string $reason,
        int $processedBy
    ): DepositReturn {
        if ($rental->deposit_status !== 'held') {
            throw new \RuntimeException('Cannot forfeit: deposit is not currently held');
        }

        return DB::transaction(function () use ($rental, $reason, $processedBy) {
            $depositReturn = DepositReturn::create([
                'rental_id' => $rental->rental_id,
                'customer_id' => $rental->customer_id,
                'original_deposit_amount' => $rental->deposit_amount,
                'amount_returned' => 0,
                'amount_deducted' => $rental->deposit_amount,
                'deductions_breakdown' => [
                    [
                        'type' => 'forfeiture',
                        'amount' => $rental->deposit_amount,
                        'reason' => $reason,
                    ]
                ],
                'return_method' => 'forfeiture',
                'status' => 'processed',
                'notes' => "Deposit forfeited: {$reason}",
                'processed_by' => $processedBy,
                'processed_at' => now(),
            ]);

            $rental->update([
                'deposit_status' => 'forfeited',
                'deposit_returned_amount' => 0,
                'deposit_deducted_amount' => $rental->deposit_amount,
            ]);

            return $depositReturn;
        });
    }

    /**
     * Get deposit summary for a rental
     * 
     * @param Rental $rental
     * @return array
     */
    public function getDepositSummary(Rental $rental): array
    {
        return [
            'deposit_amount' => $rental->deposit_amount,
            'deposit_status' => $rental->deposit_status,
            'amount_held' => $rental->deposit_status === 'held' ? $rental->deposit_amount : 0,
            'amount_returned' => $rental->deposit_returned_amount,
            'amount_deducted' => $rental->deposit_deducted_amount,
            'remaining' => $rental->remaining_deposit,
            'can_return' => $rental->hasDepositHeld() && $rental->return_date !== null,
            'collected_at' => $rental->deposit_collected_at,
            'collected_by' => $rental->depositCollectedBy?->name,
        ];
    }

    /**
     * Validate that deposit can be returned
     */
    private function validateCanReturn(Rental $rental): void
    {
        if ($rental->deposit_status !== 'held') {
            throw new \RuntimeException('Cannot return: deposit is not currently held');
        }

        if ($rental->deposit_amount <= 0) {
            throw new \RuntimeException('Cannot return: no deposit amount held');
        }

        // Optional: Check if item has been returned
        if ($rental->return_date === null) {
            throw new \RuntimeException('Cannot return: rental item has not been returned yet');
        }
    }

    /**
     * Create invoice for deduction amounts (for accounting records)
     */
    private function createDeductionInvoice(Rental $rental, array $deductions, int $createdBy): void
    {
        $totalDeductions = collect($deductions)->sum('amount');
        
        if ($totalDeductions <= 0) {
            return;
        }

        $pendingStatusId = $this->getPaymentStatusId('pending');
        if (!$pendingStatusId) {
            throw new \RuntimeException('Pending payment status is not configured.');
        }

        $invoice = Invoice::create([
            'invoice_number' => $this->generateReference('INV-DEDUCT'),
            'customer_id' => $rental->customer_id,
            'reservation_id' => $rental->reservation_id,
            'rental_id' => $rental->rental_id,
            'subtotal' => $totalDeductions,
            'discount' => 0,
            'tax' => 0,
            'total_amount' => $totalDeductions,
            'amount_paid' => $totalDeductions,
            'balance_due' => 0,
            'invoice_date' => now(),
            'due_date' => now(),
            'invoice_type' => 'final',
            'created_by' => $createdBy,
            'status_id' => $pendingStatusId,
        ]);

        foreach ($deductions as $deduction) {
            InvoiceItem::create([
                'invoice_id' => $invoice->invoice_id,
                'description' => $deduction['reason'] ?? 'Deposit deduction',
                'item_type' => $this->mapDeductionType($deduction['type']),
                'item_id' => $rental->item_id,
                'quantity' => 1,
                'unit_price' => $deduction['amount'],
                'total_price' => $deduction['amount'],
            ]);
        }
    }

    /**
     * Map deduction type to invoice item type
     */
    private function mapDeductionType(string $type): string
    {
        $mapping = [
            'damage' => 'damage_fee',
            'late' => 'late_fee',
            'cleaning' => 'cleaning_fee',
            'missing' => 'damage_fee',
            'other' => 'other',
        ];

        return $mapping[$type] ?? 'other';
    }

    private function findOrCreateInvoice(Rental $rental, string $invoiceType, int $createdBy): Invoice
    {
        $pendingStatusId = $this->getPaymentStatusId('pending');
        if (!$pendingStatusId) {
            throw new \RuntimeException('Pending payment status is not configured.');
        }

        return Invoice::firstOrCreate(
            [
                'customer_id' => $rental->customer_id,
                'reservation_id' => $rental->reservation_id,
                'rental_id' => $rental->rental_id,
                'invoice_type' => $invoiceType,
            ],
            [
                'invoice_number' => $this->generateReference('INV'),
                'subtotal' => 0,
                'discount' => 0,
                'tax' => 0,
                'total_amount' => 0,
                'amount_paid' => 0,
                'balance_due' => 0,
                'invoice_date' => now(),
                'due_date' => now()->addDays(7),
                'created_by' => $createdBy,
                'status_id' => $pendingStatusId,
            ]
        );
    }

    private function recalculateInvoiceTotals(Invoice $invoice): void
    {
        $subtotal = (float) $invoice->invoiceItems()->sum('total_price');
        $tax = (float) $invoice->tax;
        $discount = (float) $invoice->discount;
        $total = max(($subtotal + $tax) - $discount, 0);

        $amountPaid = (float) Payment::where('invoice_id', $invoice->invoice_id)
            ->whereHas('status', fn ($query) => $query->whereRaw('LOWER(status_name) = ?', ['paid']))
            ->sum('amount');

        $invoice->update([
            'subtotal' => $subtotal,
            'total_amount' => $total,
            'amount_paid' => min($amountPaid, $total),
            'balance_due' => max($total - $amountPaid, 0),
        ]);
    }

    private function getPaymentStatusId(string $name): ?int
    {
        return PaymentStatus::whereRaw('LOWER(status_name) = ?', [strtolower($name)])->value('status_id');
    }

    private function generateReference(string $prefix): string
    {
        return $prefix . '-' . now()->format('YmdHis') . '-' . strtoupper(substr(bin2hex(random_bytes(3)), 0, 6));
    }
}
