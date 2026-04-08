<?php

namespace Database\Seeders;

use App\Models\Invoice;
use App\Models\Payment;
use App\Models\PaymentStatus;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class PaymentSeeder extends Seeder
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

        // Get all invoices
        $invoices = Invoice::with('status')->get();

        foreach ($invoices as $invoice) {
            // Only create payments for paid invoices
            if ($invoice->status?->status_name !== 'paid' || $invoice->total_amount <= 0) {
                continue;
            }

            // Skip if payment already exists
            if ($invoice->payments()->exists()) {
                continue;
            }

            Payment::create([
                'invoice_id' => $invoice->invoice_id,
                'amount' => $invoice->total_amount,
                'payment_method' => ['cash', 'card', 'gcash', 'paymaya', 'bank_transfer'][array_rand(['cash', 'card', 'gcash', 'paymaya', 'bank_transfer'])],
                'status_id' => $paidStatusId,
                'payment_date' => Carbon::parse($invoice->invoice_date)->addHours(1),
                'notes' => 'Seeded payment for rental invoice',
                'processed_by' => $invoice->created_by,
            ]);
        }
    }
}
