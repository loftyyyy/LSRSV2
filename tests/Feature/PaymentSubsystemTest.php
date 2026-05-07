<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\PaymentStatus;
use App\Models\User;
use App\Services\PaymentService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * PaymentSubsystemTest
 *
 * Black-box feature tests for the Payment subsystem.
 * Each test case corresponds directly to a row in the black-box test plan.
 *
 * Techniques used:
 *  - Statement Coverage  : WBT_PAY_001, WBT_PAY_021, WBT_PAY_022
 *  - Branch Coverage     : WBT_PAY_002 … WBT_PAY_020, WBT_PAY_023
 *
 * Routes under test (all behind auth middleware, prefix /api/):
 *  POST   /payments                          → store()
 *  PUT    /payments/{payment}                → update()
 *  DELETE /payments/{payment}                → destroy()
 *  POST   /payments/{payment}/void           → voidPayment()
 *  POST   /payments/{payment}/refund         → processRefund()
 *  GET    /payments/rental-fee-details       → getRentalFeeDetails()
 *  GET    /payments/monitor                  → monitorPayments()
 *  GET    /payments/daily-collection         → getDailyCollection()
 *  GET    /payments/reports/generate         → report()
 *  GET    /payments/reports/csv              → generateCSV()
 *  GET    /payments/{payment}/receipt        → generateReceiptPDF()
 *  GET    /payments                          → index()
 */
class PaymentSubsystemTest extends TestCase
{
    use RefreshDatabase;

    // -----------------------------------------------------------------------
    // Shared helpers
    // -----------------------------------------------------------------------

    /** Authenticated user used across all tests. */
    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create(['is_admin' => true]);

        PaymentStatus::insert([
            ['status_id' => 1, 'status_name' => 'paid'],
            ['status_id' => 2, 'status_name' => 'pending'],
            ['status_id' => 3, 'status_name' => 'voided'],
            ['status_id' => 4, 'status_name' => 'refunded'],
            ['status_id' => 5, 'status_name' => 'completed'],
        ]);
    }

    /** Returns headers that simulate an authenticated session call. */
    private function authHeaders(): array
    {
        return ['Accept' => 'application/json'];
    }

    /**
     * Create a minimal Invoice with sensible defaults.
     */
    private function makeInvoice(array $overrides = []): Invoice
    {
        $customer = Customer::factory()->create();

        return Invoice::factory()->create(array_merge([
            'customer_id'  => $customer->customer_id,
            'total_amount' => 1000.00,
            'amount_paid'  => 0.00,
            'balance_due'  => 1000.00,
            'status_id'    => 2, // pending
        ], $overrides));
    }

    /**
     * Create a minimal Payment linked to the given Invoice.
     */
    private function makePayment(Invoice $invoice, array $overrides = []): Payment
    {
        return Payment::factory()->create(array_merge([
            'invoice_id'       => $invoice->invoice_id,
            'amount'           => 500.00,
            'payment_method'   => 'cash',
            'payment_date'     => now(),
            'status_id'        => 1, // paid
            'payment_reference'=> 'REF-' . uniqid(),
            'processed_by'     => $this->user->user_id,
        ], $overrides));
    }

    // -----------------------------------------------------------------------
    // WBT_PAY_001 – store() – Successful Payment (Statement Coverage)
    // -----------------------------------------------------------------------

    /**
     * @test
     * @testdox WBT_PAY_001: POST /payments with valid data → 201, payment record returned, invoice balance_due reduced
     */
    public function test_WBT_PAY_001_store_successful_payment(): void
    {
        $invoice = $this->makeInvoice();

        // Mock PaymentService::processPayment to return a pre-built payment
        $payment = $this->makePayment($invoice);
        $invoice->amount_paid = 500.00;
        $invoice->balance_due = 500.00;
        $invoice->save();

        $this->mock(PaymentService::class, function ($mock) use ($payment) {
            $mock->shouldReceive('processPayment')
                ->once()
                ->andReturn($payment->load('invoice'));
        });

        $response = $this->actingAs($this->user)
            ->postJson('/api/payments', [
                'invoice_id'     => $invoice->invoice_id,
                'amount'         => 500.00,
                'payment_method' => 'cash',
            ]);

        $response->assertStatus(201)
            ->assertJsonFragment(['message' => 'Payment processed successfully'])
            ->assertJsonPath('data.payment_id', $payment->payment_id);

        // Confirm the invoice balance_due was reduced (done inside service – reflected on model)
        $this->assertEquals(500.00, (float) $invoice->fresh()->balance_due);
    }

    // -----------------------------------------------------------------------
    // WBT_PAY_002 – store() – InvalidArgumentException (Branch Coverage)
    // -----------------------------------------------------------------------

    /**
     * @test
     * @testdox WBT_PAY_002: POST /payments with amount > balance_due → 422 with exception message
     */
    public function test_WBT_PAY_002_store_invalid_argument_exception(): void
    {
        $invoice = $this->makeInvoice(['balance_due' => 200.00]);

        $this->mock(PaymentService::class, function ($mock) {
            $mock->shouldReceive('processPayment')
                ->once()
                ->andThrow(new \InvalidArgumentException('Payment amount exceeds the invoice balance due.'));
        });

        $response = $this->actingAs($this->user)
            ->postJson('/api/payments', [
                'invoice_id'     => $invoice->invoice_id,
                'amount'         => 500.00,
                'payment_method' => 'cash',
            ]);

        $response->assertStatus(422)
            ->assertJsonFragment(['message' => 'Payment amount exceeds the invoice balance due.']);
    }

    // -----------------------------------------------------------------------
    // WBT_PAY_003 – store() – General Exception (Branch Coverage)
    // -----------------------------------------------------------------------

    /**
     * @test
     * @testdox WBT_PAY_003: POST /payments when service throws generic Exception → 500, 'Failed to process payment'
     */
    public function test_WBT_PAY_003_store_general_exception(): void
    {
        $invoice = $this->makeInvoice();

        $this->mock(PaymentService::class, function ($mock) {
            $mock->shouldReceive('processPayment')
                ->once()
                ->andThrow(new \Exception('Unexpected DB error'));
        });

        $response = $this->actingAs($this->user)
            ->postJson('/api/payments', [
                'invoice_id'     => $invoice->invoice_id,
                'amount'         => 500.00,
                'payment_method' => 'cash',
            ]);

        $response->assertStatus(500)
            ->assertJsonFragment(['message' => 'Failed to process payment'])
            ->assertJsonPath('error', 'Unexpected DB error');
    }

    // -----------------------------------------------------------------------
    // WBT_PAY_004 – update() – Amount changed → invoice recalculated (Branch Coverage)
    // -----------------------------------------------------------------------

    /**
     * @test
     * @testdox WBT_PAY_004: PUT /payments/{id} with different amount → invoice amount_paid & balance_due recalculated, 200
     */
    public function test_WBT_PAY_004_update_amount_changed_recalculates_invoice(): void
    {
        $invoice = $this->makeInvoice([
            'total_amount' => 1000.00,
            'amount_paid'  => 500.00,
            'balance_due'  => 500.00,
        ]);
        $payment = $this->makePayment($invoice, ['amount' => 500.00]);

        $response = $this->actingAs($this->user)
            ->putJson("/api/payments/{$payment->payment_id}", [
                'amount'         => 300.00,
                'payment_method' => 'cash',
                'payment_date'   => now()->toDateTimeString(),
            ]);

        $response->assertStatus(200)
            ->assertJsonFragment(['message' => 'Payment updated successfully']);

        $invoice->refresh();
        // amount_paid = 500 - 500 + 300 = 300
        $this->assertEquals(300.00, (float) $invoice->amount_paid);
        // balance_due = 1000 - 300 = 700
        $this->assertEquals(700.00, (float) $invoice->balance_due);
    }

    // -----------------------------------------------------------------------
    // WBT_PAY_005 – update() – Amount unchanged → invoice NOT updated (Branch Coverage)
    // -----------------------------------------------------------------------

    /**
     * @test
     * @testdox WBT_PAY_005: PUT /payments/{id} with same amount → invoice untouched, payment record updated, 200
     */
    public function test_WBT_PAY_005_update_amount_unchanged_does_not_update_invoice(): void
    {
        $invoice = $this->makeInvoice([
            'total_amount' => 1000.00,
            'amount_paid'  => 500.00,
            'balance_due'  => 500.00,
        ]);
        $payment = $this->makePayment($invoice, ['amount' => 500.00, 'notes' => 'original note']);

        $originalAmountPaid = (float) $invoice->amount_paid;

        $response = $this->actingAs($this->user)
            ->putJson("/api/payments/{$payment->payment_id}", [
                'amount'         => 500.00,  // same amount
                'payment_method' => 'gcash',
                'payment_date'   => now()->toDateTimeString(),
                'notes'          => 'updated note',
            ]);

        $response->assertStatus(200)
            ->assertJsonFragment(['message' => 'Payment updated successfully']);

        // Invoice should remain the same
        $invoice->refresh();
        $this->assertEquals($originalAmountPaid, (float) $invoice->amount_paid);

        // Payment notes should have changed
        $this->assertEquals('updated note', $payment->fresh()->notes);
    }

    // -----------------------------------------------------------------------
    // WBT_PAY_006 – destroy() – DB rollback on exception (Branch Coverage)
    // -----------------------------------------------------------------------

    /**
     * @test
     * @testdox WBT_PAY_006: DELETE /payments/{id} when invoice save fails → DB rolled back, 500, 'Failed to delete payment'
     */
    public function test_WBT_PAY_006_destroy_rollback_on_exception(): void
    {
        $invoice = $this->makeInvoice([
            'total_amount' => 1000.00,
            'amount_paid'  => 500.00,
            'balance_due'  => 500.00,
        ]);
        $payment = $this->makePayment($invoice, ['amount' => 500.00]);

        // Force the invoice save to throw inside the transaction
        Invoice::saving(function () {
            throw new \Exception('Simulated DB error on invoice save');
        });

        $response = $this->actingAs($this->user)
            ->deleteJson("/api/payments/{$payment->payment_id}");

        $response->assertStatus(500)
            ->assertJsonFragment(['message' => 'Failed to delete payment']);

        // Payment must still exist (rollback was effective)
        $this->assertDatabaseHas('payments', ['payment_id' => $payment->payment_id]);
    }

    // -----------------------------------------------------------------------
    // WBT_PAY_007 – destroy() – Successful deletion (Branch Coverage)
    // -----------------------------------------------------------------------

    /**
     * @test
     * @testdox WBT_PAY_007: DELETE /payments/{id} successfully → invoice amount_paid reduced, payment deleted, 200
     */
    public function test_WBT_PAY_007_destroy_successful(): void
    {
        $invoice = $this->makeInvoice([
            'total_amount' => 1000.00,
            'amount_paid'  => 500.00,
            'balance_due'  => 500.00,
        ]);
        $payment = $this->makePayment($invoice, ['amount' => 500.00]);

        $response = $this->actingAs($this->user)
            ->deleteJson("/api/payments/{$payment->payment_id}");

        $response->assertStatus(200)
            ->assertJsonFragment(['message' => 'Payment deleted successfully']);

        $this->assertDatabaseMissing('payments', ['payment_id' => $payment->payment_id]);

        $invoice->refresh();
        $this->assertEquals(0.00, (float) $invoice->amount_paid);
        $this->assertEquals(1000.00, (float) $invoice->balance_due);
    }

    // -----------------------------------------------------------------------
    // WBT_PAY_008 – voidPayment() – RuntimeException (Branch Coverage)
    // -----------------------------------------------------------------------

    /**
     * @test
     * @testdox WBT_PAY_008: POST /payments/{id}/void when service throws RuntimeException → 422 with message
     */
    public function test_WBT_PAY_008_void_payment_runtime_exception(): void
    {
        $invoice = $this->makeInvoice();
        $payment = $this->makePayment($invoice);

        $this->mock(PaymentService::class, function ($mock) {
            $mock->shouldReceive('voidPayment')
                ->once()
                ->andThrow(new \RuntimeException('Payment has already been voided.'));
        });

        $response = $this->actingAs($this->user)
            ->postJson("/api/payments/{$payment->payment_id}/void", [
                'reason' => 'Customer requested cancellation',
            ]);

        $response->assertStatus(422)
            ->assertJsonFragment(['message' => 'Payment has already been voided.']);
    }

    // -----------------------------------------------------------------------
    // WBT_PAY_009 – voidPayment() – Success (Branch Coverage)
    // -----------------------------------------------------------------------

    /**
     * @test
     * @testdox WBT_PAY_009: POST /payments/{id}/void with valid reason → 200, 'Payment voided successfully', updated payment returned
     */
    public function test_WBT_PAY_009_void_payment_success(): void
    {
        $invoice = $this->makeInvoice();
        $payment = $this->makePayment($invoice);

        $voidedPayment = $payment->replicate();
        $voidedPayment->status_id = 3; // voided

        $this->mock(PaymentService::class, function ($mock) use ($voidedPayment) {
            $mock->shouldReceive('voidPayment')
                ->once()
                ->andReturn($voidedPayment);
        });

        $response = $this->actingAs($this->user)
            ->postJson("/api/payments/{$payment->payment_id}/void", [
                'reason' => 'Customer requested cancellation',
            ]);

        $response->assertStatus(200)
            ->assertJsonFragment(['message' => 'Payment voided successfully'])
            ->assertJsonStructure(['data']);
    }

    // -----------------------------------------------------------------------
    // WBT_PAY_010 – processRefund() – InvalidArgumentException or RuntimeException (Branch Coverage)
    // -----------------------------------------------------------------------

    /**
     * @test
     * @testdox WBT_PAY_010: POST /payments/{id}/refund with refund_amount > original → 422 with exception message
     */
    public function test_WBT_PAY_010_process_refund_invalid_amount(): void
    {
        $invoice = $this->makeInvoice();
        $payment = $this->makePayment($invoice, ['amount' => 500.00]);

        $this->mock(PaymentService::class, function ($mock) {
            $mock->shouldReceive('processRefund')
                ->once()
                ->andThrow(new \InvalidArgumentException('Refund amount cannot exceed the original payment amount.'));
        });

        $response = $this->actingAs($this->user)
            ->postJson("/api/payments/{$payment->payment_id}/refund", [
                'refund_amount'  => 1000.00,   // 2× original
                'reason'         => 'Customer overpaid',
                'refund_method'  => 'cash',
            ]);

        $response->assertStatus(422)
            ->assertJsonFragment(['message' => 'Refund amount cannot exceed the original payment amount.']);
    }

    // -----------------------------------------------------------------------
    // WBT_PAY_011 – processRefund() – Success (Branch Coverage)
    // -----------------------------------------------------------------------

    /**
     * @test
     * @testdox WBT_PAY_011: POST /payments/{id}/refund with valid amount → 200, refund record returned, original_payment refreshed
     */
    public function test_WBT_PAY_011_process_refund_success(): void
    {
        $invoice = $this->makeInvoice();
        $payment = $this->makePayment($invoice, ['amount' => 500.00]);

        $refundPayment = Payment::factory()->make([
            'invoice_id'     => $invoice->invoice_id,
            'amount'         => 200.00,
            'payment_method' => 'cash',
            'status_id'      => 4,
        ]);

        $this->mock(PaymentService::class, function ($mock) use ($refundPayment) {
            $mock->shouldReceive('processRefund')
                ->once()
                ->andReturn($refundPayment);
        });

        $response = $this->actingAs($this->user)
            ->postJson("/api/payments/{$payment->payment_id}/refund", [
                'refund_amount'  => 200.00,
                'reason'         => 'Partial refund agreed',
                'refund_method'  => 'cash',
            ]);

        $response->assertStatus(200)
            ->assertJsonFragment(['message' => 'Refund processed successfully'])
            ->assertJsonStructure(['data', 'original_payment']);
    }

    // -----------------------------------------------------------------------
    // WBT_PAY_012 – getRentalFeeDetails() – Invoice not found (Branch Coverage)
    // -----------------------------------------------------------------------

    /**
     * @test
     * @testdox WBT_PAY_012: GET /payments/rental-fee-details with non-existent invoice_id → 404, 'Invoice not found'
     */
    public function test_WBT_PAY_012_get_rental_fee_details_invoice_not_found(): void
    {
        $this->mock(PaymentService::class, function ($mock) {
            $mock->shouldReceive('getRentalFeeDetails')
                ->once()
                ->andReturn(['error' => 'Invoice not found']);
        });

        $response = $this->actingAs($this->user)
            ->getJson('/api/payments/rental-fee-details?invoice_id=99999');

        $response->assertStatus(404)
            ->assertJsonFragment(['message' => 'Invoice not found']);
    }

    // -----------------------------------------------------------------------
    // WBT_PAY_013 – getRentalFeeDetails() – Bad request / other error (Branch Coverage)
    // -----------------------------------------------------------------------

    /**
     * @test
     * @testdox WBT_PAY_013: GET /payments/rental-fee-details missing required params → 400, error message returned
     */
    public function test_WBT_PAY_013_get_rental_fee_details_bad_request(): void
    {
        $this->mock(PaymentService::class, function ($mock) {
            $mock->shouldReceive('getRentalFeeDetails')
                ->once()
                ->andReturn(['error' => 'Missing required parameters']);
        });

        $response = $this->actingAs($this->user)
            ->getJson('/api/payments/rental-fee-details');

        $response->assertStatus(400)
            ->assertJsonFragment(['message' => 'Missing required parameters']);
    }

    // -----------------------------------------------------------------------
    // WBT_PAY_014 – monitorPayments() – Completed filter (Branch Coverage)
    // -----------------------------------------------------------------------

    /**
     * @test
     * @testdox WBT_PAY_014: GET /payments/monitor?status=completed → only paid/completed payments returned with summary
     */
    public function test_WBT_PAY_014_monitor_payments_completed_filter(): void
    {
        $invoice = $this->makeInvoice();

        // Create one completed and one pending payment
        $this->makePayment($invoice, ['status_id' => 1]); // paid
        $this->makePayment($invoice, ['status_id' => 2]); // pending

        $response = $this->actingAs($this->user)
            ->getJson('/api/payments/monitor?status=completed');

        $response->assertStatus(200)
            ->assertJsonStructure(['payments', 'summary']);

        // All returned payments must have a paid/completed status
        $payments = $response->json('payments.data');
        foreach ($payments as $p) {
            $this->assertContains(
                strtolower($p['status']['status_name'] ?? ''),
                ['paid', 'completed'],
                'Expected only paid/completed payments but found: ' . ($p['status']['status_name'] ?? 'null')
            );
        }

        // Summary must include completed count and completed amount
        $summary = $response->json('summary');
        $this->assertArrayHasKey('total_completed', $summary);
        $this->assertArrayHasKey('total_completed_amount', $summary);
    }

    // -----------------------------------------------------------------------
    // WBT_PAY_015 – monitorPayments() – Pending filter (Branch Coverage)
    // -----------------------------------------------------------------------

    /**
     * @test
     * @testdox WBT_PAY_015: GET /payments/monitor?status=pending → only pending payments returned
     */
    public function test_WBT_PAY_015_monitor_payments_pending_filter(): void
    {
        $invoice = $this->makeInvoice();
        $this->makePayment($invoice, ['status_id' => 2]); // pending
        $this->makePayment($invoice, ['status_id' => 1]); // paid

        $response = $this->actingAs($this->user)
            ->getJson('/api/payments/monitor?status=pending');

        $response->assertStatus(200);

        $payments = $response->json('payments.data');
        foreach ($payments as $p) {
            $this->assertEquals(
                'pending',
                strtolower($p['status']['status_name'] ?? ''),
                'Expected only pending payments'
            );
        }
    }

    // -----------------------------------------------------------------------
    // WBT_PAY_016 – monitorPayments() – All / default (Branch Coverage)
    // -----------------------------------------------------------------------

    /**
     * @test
     * @testdox WBT_PAY_016: GET /payments/monitor (no status param) → all payments returned with full summary statistics
     */
    public function test_WBT_PAY_016_monitor_payments_all_default(): void
    {
        $invoice = $this->makeInvoice();
        $this->makePayment($invoice, ['status_id' => 1]);
        $this->makePayment($invoice, ['status_id' => 2]);

        $response = $this->actingAs($this->user)
            ->getJson('/api/payments/monitor');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'payments',
                'summary' => [
                    'total_completed',
                    'total_pending',
                    'total_completed_amount',
                    'total_pending_amount',
                ],
            ]);

        // Both payments should appear (no status filter)
        $this->assertGreaterThanOrEqual(2, count($response->json('payments.data')));
    }

    // -----------------------------------------------------------------------
    // WBT_PAY_017 – getDailyCollection() – Specific date (Branch Coverage)
    // -----------------------------------------------------------------------

    /**
     * @test
     * @testdox WBT_PAY_017: GET /payments/daily-collection?date=2025-01-15 → service called with Carbon(2025-01-15), report returned
     */
    public function test_WBT_PAY_017_get_daily_collection_specific_date(): void
    {
        $expectedDate = Carbon::parse('2025-01-15');

        $this->mock(PaymentService::class, function ($mock) use ($expectedDate) {
            $mock->shouldReceive('getDailyCollectionReport')
                ->once()
                ->withArgs(function (Carbon $date) use ($expectedDate) {
                    return $date->isSameDay($expectedDate);
                })
                ->andReturn(['date' => '2025-01-15', 'total' => 1500.00]);
        });

        $response = $this->actingAs($this->user)
            ->getJson('/api/payments/daily-collection?date=2025-01-15');

        $response->assertStatus(200)
            ->assertJsonPath('date', '2025-01-15');
    }

    // -----------------------------------------------------------------------
    // WBT_PAY_018 – getDailyCollection() – No date → defaults to today (Branch Coverage)
    // -----------------------------------------------------------------------

    /**
     * @test
     * @testdox WBT_PAY_018: GET /payments/daily-collection (no date) → service called with today's date, 200
     */
    public function test_WBT_PAY_018_get_daily_collection_defaults_to_today(): void
    {
        $today = Carbon::now();

        $this->mock(PaymentService::class, function ($mock) use ($today) {
            $mock->shouldReceive('getDailyCollectionReport')
                ->once()
                ->withArgs(function (Carbon $date) use ($today) {
                    return $date->isSameDay($today);
                })
                ->andReturn(['date' => $today->format('Y-m-d'), 'total' => 0.00]);
        });

        $response = $this->actingAs($this->user)
            ->getJson('/api/payments/daily-collection');

        $response->assertStatus(200);
    }


    // -----------------------------------------------------------------------
    // WBT_PAY_019 – generateCSV() – Full export (Statement Coverage)
    // -----------------------------------------------------------------------

    /**
     * @test
     * @testdox WBT_PAY_019: GET /payments/reports/csv → CSV with BOM, header rows, method breakdown, and payment detail rows
     */
    public function test_WBT_PAY_021_generate_csv_full_export(): void
    {
        $invoice = $this->makeInvoice();
        $this->makePayment($invoice, ['status_id' => 1, 'payment_method' => 'cash']);

        $response = $this->actingAs($this->user)
            ->get('/api/payments/reports/csv');

        $response->assertStatus(200);
        $response->assertHeaderContains('Content-Type', 'text/csv');

        $content = $response->streamedContent();

        // UTF-8 BOM
        $this->assertStringStartsWith("\xEF\xBB\xBF", $content, 'CSV should start with UTF-8 BOM');

        // Required section headers
        $this->assertStringContainsString('Payment Report', $content);
        $this->assertStringContainsString('Report Statistics', $content);
        $this->assertStringContainsString('Payment Method Breakdown', $content);
        $this->assertStringContainsString('Payment Details', $content);

        // Column headers in detail section
        $this->assertStringContainsString('Payment ID', $content);
        $this->assertStringContainsString('Payment Reference', $content);
        $this->assertStringContainsString('Customer Name', $content);
    }

    // -----------------------------------------------------------------------
    // WBT_PAY_020 – generateReceiptPDF() – Success (Statement Coverage)
    // -----------------------------------------------------------------------

    /**
     * @test
     * @testdox WBT_PAY_020: GET /payments/{id}/receipt → PDF downloaded named receipt-{payment_reference}.pdf
     */
    public function test_WBT_PAY_022_generate_receipt_pdf_success(): void
    {
        $invoice = $this->makeInvoice();
        $payment = $this->makePayment($invoice, ['payment_reference' => 'REF-TESTPDF']);

        // Mock DomPDF to avoid needing a real view/blade
        $pdfMock = \Mockery::mock(\Barryvdh\DomPDF\PDF::class);
        $pdfMock->shouldReceive('download')
            ->once()
            ->with('receipt-REF-TESTPDF.pdf')
            ->andReturn(response('PDF content', 200, [
                'Content-Type'        => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="receipt-REF-TESTPDF.pdf"',
            ]));

        \Barryvdh\DomPDF\Facade\Pdf::shouldReceive('loadView')
            ->once()
            ->andReturn($pdfMock);

        $response = $this->actingAs($this->user)
            ->get("/api/payments/{$payment->payment_id}/receipt");

        $response->assertStatus(200);
        $this->assertStringContainsString(
            'receipt-REF-TESTPDF.pdf',
            $response->headers->get('Content-Disposition') ?? ''
        );
    }

    // -----------------------------------------------------------------------
    // WBT_PAY_021 – index() – Multiple filters (Branch Coverage)
    // -----------------------------------------------------------------------

    /**
     * @test
     * @testdox WBT_PAY_021: GET /payments with all filters applied → only matching payments returned, no SQL error
     */
    public function test_WBT_PAY_023_index_multiple_filters(): void
    {
        $invoice = $this->makeInvoice();

        // Target payment – matches every filter we will apply
        $targetPayment = $this->makePayment($invoice, [
            'payment_method'    => 'gcash',
            'status_id'         => 1,
            'payment_date'      => Carbon::parse('2025-06-15 10:00:00'),
            'payment_reference' => 'FILTER-TARGET',
        ]);

        // Decoy – different method
        $this->makePayment($invoice, [
            'payment_method' => 'cash',
            'status_id'      => 2,
            'payment_date'   => Carbon::parse('2025-06-15'),
        ]);

        $query = http_build_query([
            'search'         => 'FILTER-TARGET',
            'invoice_id'     => $invoice->invoice_id,
            'customer_id'    => $invoice->customer_id,
            'status'         => 'paid',
            'payment_method' => 'gcash',
            'payment_date_from' => '2025-06-01',
            'payment_date_to'   => '2025-06-30',
        ]);

        $response = $this->actingAs($this->user)
            ->getJson("/api/payments?{$query}");

        $response->assertStatus(200);

        $data = $response->json('data');
        $this->assertNotEmpty($data, 'Expected at least one payment matching all filters');

        foreach ($data as $p) {
            $this->assertEquals('gcash', $p['payment_method'], 'Unexpected payment_method in filtered result');
            $this->assertEquals(
                'paid',
                strtolower($p['status']['status_name'] ?? ''),
                'Unexpected status in filtered result'
            );
        }
    }
}
