<?php

namespace Database\Seeders;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\PaymentStatus;
use App\Models\Rental;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class InvoiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $paidStatusId = PaymentStatus::whereRaw('LOWER(status_name) = ?', ['paid'])->value('status_id');
        $pendingStatusId = PaymentStatus::whereRaw('LOWER(status_name) = ?', ['pending'])->value('status_id');

        if (! $paidStatusId || ! $pendingStatusId) {
            return;
        }

        // Get all rentals
        $rentals = Rental::with(['customer', 'item', 'reservation'])->get();

        foreach ($rentals as $rental) {
            // Skip if invoice already exists
            if ($rental->invoices()->exists()) {
                continue;
            }

            $rentalFee = (float) $rental->item?->rental_price ?? 0;
            $depositAmount = (float) $rental->deposit_amount ?? 0;
            $totalAmount = $rentalFee + $depositAmount;

            // Determine status based on rental status
            $invoiceStatus = $rental->status?->status_name === 'returned' || $rental->status?->status_name === 'rented'
                ? $paidStatusId
                : $pendingStatusId;

            // Determine amount paid
            $amountPaid = ($invoiceStatus === $paidStatusId) ? $totalAmount : 0;
            $balanceDue = $totalAmount - $amountPaid;

            $invoice = Invoice::create([
                'invoice_number' => $this->generateInvoiceNumber(),
                'customer_id' => $rental->customer_id,
                'reservation_id' => $rental->reservation_id,
                'rental_id' => $rental->rental_id,
                'invoice_type' => 'rental',
                'invoice_date' => Carbon::parse($rental->released_date),
                'due_date' => Carbon::parse($rental->due_date)->addDays(7),
                'subtotal' => $totalAmount,
                'discount' => 0,
                'tax' => 0,
                'total_amount' => $totalAmount,
                'amount_paid' => $amountPaid,
                'balance_due' => $balanceDue,
                'created_by' => $rental->released_by,
                'status_id' => $invoiceStatus,
            ]);

            // Create rental fee line item
            InvoiceItem::create([
                'invoice_id' => $invoice->invoice_id,
                'description' => "Rental: {$rental->item->name} ({$rental->item->sku})",
                'item_type' => 'rental_fee',
                'item_id' => $rental->item_id,
                'quantity' => 1,
                'unit_price' => $rentalFee,
                'total_price' => $rentalFee,
            ]);

            // Create deposit line item
            InvoiceItem::create([
                'invoice_id' => $invoice->invoice_id,
                'description' => "Security Deposit: {$rental->item->name}",
                'item_type' => 'deposit',
                'item_id' => $rental->item_id,
                'quantity' => 1,
                'unit_price' => $depositAmount,
                'total_price' => $depositAmount,
            ]);
        }
    }

    /**
     * Generate unique invoice number
     */
    private function generateInvoiceNumber(): string
    {
        $prefix = 'INV-'.Carbon::now()->format('Y');
        $lastInvoice = Invoice::where('invoice_number', 'like', "{$prefix}%")
            ->orderByRaw('CAST(SUBSTRING(invoice_number, -6) AS UNSIGNED) DESC')
            ->first();

        if ($lastInvoice && preg_match('/-(\d{6})$/', $lastInvoice->invoice_number, $matches)) {
            $nextNumber = (int) $matches[1] + 1;
        } else {
            $nextNumber = 1;
        }

        return "{$prefix}-".str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
    }
}
