<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Http\Requests\StorePaymentRequest;
use App\Http\Requests\UpdatePaymentRequest;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Barryvdh\DomPDF\Facade\Pdf;

class PaymentController extends Controller
{

    /**
     * Display Reports Page
     */
    public function report(Request $request):JsonResponse
    {
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth());
        $endDate = $request->get('end_date', Carbon::now()->endOfMonth());
        $reportType = $request->get('report_type', 'daily'); // daily, weekly, monthly

        $query = Payment::with(['invoice', 'invoice.customer', 'processedBy', 'status'])
            ->whereBetween('payment_date', [$startDate, $endDate]);

        // Summary statistics
        $summary = [
            'total_payments' => $query->count(),
            'total_amount_collected' => $query->sum('amount'),
            'completed_payments' => $query->clone()->whereHas('status', function ($q) {
                $q->where('status_name', 'completed');
            })->count(),
            'pending_payments' => $query->clone()->whereHas('status', function ($q) {
                $q->where('status_name', 'pending');
            })->count(),
        ];

        // Group by date based on report type
        $groupedData = $this->groupPaymentsByPeriod($query->get(), $reportType);

        // Payment method breakdown
        $paymentMethodBreakdown = $query->clone()
            ->select('payment_method', DB::raw('COUNT(*) as count'), DB::raw('SUM(amount) as total'))
            ->groupBy('payment_method')
            ->get();

        // Payment status breakdown
        $statusBreakdown = DB::table('payments')
            ->join('payment_statuses', 'payments.status_id', '=', 'payment_statuses.status_id')
            ->whereBetween('payments.payment_date', [$startDate, $endDate])
            ->select('payment_statuses.status_name', DB::raw('COUNT(*) as count'), DB::raw('SUM(payments.amount) as total'))
            ->groupBy('payment_statuses.status_name')
            ->get();

        // Top customers by payment amount
        $topCustomers = DB::table('payments')
            ->join('invoices', 'payments.invoice_id', '=', 'invoices.invoice_id')
            ->join('customers', 'invoices.customer_id', '=', 'customers.customer_id')
            ->whereBetween('payments.payment_date', [$startDate, $endDate])
            ->select(
                'customers.customer_id',
                DB::raw("CONCAT(customers.first_name, ' ', customers.last_name) as customer_name"),
                DB::raw('COUNT(payments.payment_id) as payment_count'),
                DB::raw('SUM(payments.amount) as total_paid')
            )
            ->groupBy('customers.customer_id', 'customers.first_name', 'customers.last_name')
            ->orderByDesc('total_paid')
            ->limit(10)
            ->get();

        return response()->json([
            'summary' => $summary,
            'grouped_data' => $groupedData,
            'payment_method_breakdown' => $paymentMethodBreakdown,
            'status_breakdown' => $statusBreakdown,
            'top_customers' => $topCustomers,
            'period' => [
                'start_date' => $startDate,
                'end_date' => $endDate,
                'report_type' => $reportType,
            ]
        ]);

    }


    /**
     * Helper function to group payments by period
     */
    private function groupPaymentsByPeriod($payments, $reportType)
    {
        return $payments->groupBy(function ($payment) use ($reportType) {
            switch ($reportType) {
                case 'daily':
                    return $payment->payment_date->format('Y-m-d');
                case 'weekly':
                    return $payment->payment_date->format('Y-W');
                case 'monthly':
                    return $payment->payment_date->format('Y-m');
                default:
                    return $payment->payment_date->format('Y-m-d');
            }
        })->map(function ($group) {
            return [
                'count' => $group->count(),
                'total_amount' => $group->sum('amount'),
                'cash' => $group->where('payment_method', 'cash')->sum('amount'),
                'card' => $group->where('payment_method', 'card')->sum('amount'),
                'gcash' => $group->where('payment_method', 'gcash')->sum('amount'),
                'bank_transfer' => $group->where('payment_method', 'bank_transfer')->sum('amount'),
            ];
        });
    }

    /**
     * Create PDF for reports
     */
    public function generatePDF(Request $request)
    {
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth());
        $endDate = $request->get('end_date', Carbon::now()->endOfMonth());
        $reportType = $request->get('report_type', 'daily');

        $payments = Payment::with(['invoice', 'invoice.customer', 'processedBy', 'status'])
            ->whereBetween('payment_date', [$startDate, $endDate])
            ->get();

        $summary = [
            'total_payments' => $payments->count(),
            'total_amount_collected' => $payments->sum('amount'),
            'completed_payments' => $payments->where('status.status_name', 'completed')->count(),
            'pending_payments' => $payments->where('status.status_name', 'pending')->count(),
        ];

        $paymentMethodBreakdown = $payments->groupBy('payment_method')->map(function ($group) {
            return [
                'count' => $group->count(),
                'total' => $group->sum('amount'),
            ];
        });

        $pdf = Pdf::loadView('reports.payment-report', [
            'payments' => $payments,
            'summary' => $summary,
            'payment_method_breakdown' => $paymentMethodBreakdown,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'report_type' => $reportType,
            'generated_at' => Carbon::now(),
        ]);

        return $pdf->download('payment-report-' . Carbon::now()->format('Y-m-d') . '.pdf');

    }

    /**
     * Generate receipt PDF for a specific payment
     */
    public function generateReceiptPDF(Payment $payment)
    {
        $payment->load(['invoice', 'invoice.customer', 'invoice.invoiceItems', 'processedBy', 'status']);

        $pdf = Pdf::loadView('payments.receipt-pdf', [
            'payment' => $payment,
            'company' => [
                'name' => 'Love and Style Rental System',
                'address' => 'Your Company Address',
                'phone' => 'Your Phone Number',
                'email' => 'Your Email',
            ]
        ]);

        return $pdf->download('receipt-' . $payment->payment_reference . '.pdf');
    }

    /**
     * Monitor all completed and pending payments
     */
    public function monitorPayments(Request $request): JsonResponse
    {
        $status = $request->get('status', 'all'); // all, completed, pending

        $query = Payment::with(['invoice', 'invoice.customer', 'processedBy', 'status']);

        if ($status === 'completed') {
            $query->whereHas('status', function ($q) {
                $q->where('status_name', 'completed');
            });
        } elseif ($status === 'pending') {
            $query->whereHas('status', function ($q) {
                $q->where('status_name', 'pending');
            });
        }

        // Additional filters
        if ($request->has('payment_method')) {
            $query->where('payment_method', $request->get('payment_method'));
        }

        if ($request->has('date_from')) {
            $query->where('payment_date', '>=', $request->get('date_from'));
        }

        if ($request->has('date_to')) {
            $query->where('payment_date', '<=', $request->get('date_to'));
        }

        if ($request->has('processed_by')) {
            $query->where('processed_by', $request->get('processed_by'));
        }

        $perPage = $request->get('per_page', 15);
        $payments = $query->orderBy('payment_date', 'desc')->paginate($perPage);

        $summary = [
            'total_completed' => Payment::whereHas('status', function ($q) {
                $q->where('status_name', 'completed');
            })->count(),
            'total_pending' => Payment::whereHas('status', function ($q) {
                $q->where('status_name', 'pending');
            })->count(),
            'total_completed_amount' => Payment::whereHas('status', function ($q) {
                $q->where('status_name', 'completed');
            })->sum('amount'),
            'total_pending_amount' => Payment::whereHas('status', function ($q) {
                $q->where('status_name', 'pending');
            })->sum('amount'),
        ];

        return response()->json([
            'payments' => $payments,
            'summary' => $summary,
        ]);
    }

     /**
     * Process and log payment in the system
     */
    public function processPayment(StorePaymentRequest $request): JsonResponse
    {
        DB::beginTransaction();

        try {
            // Generate unique payment reference
            $paymentReference = 'PAY-' . strtoupper(Str::random(10));

            // Create payment record
            $paymentData = $request->validated();
            $paymentData['payment_reference'] = $paymentReference;
            $paymentData['payment_date'] = now();

            $payment = Payment::create($paymentData);

            // Update invoice amount_paid and balance_due
            $invoice = Invoice::findOrFail($payment->invoice_id);
            $invoice->amount_paid += $payment->amount;
            $invoice->balance_due = $invoice->total_amount - $invoice->amount_paid;

            // Update payment status based on balance
            if ($invoice->balance_due <= 0) {
                $invoice->payment_status = 'paid';
            } elseif ($invoice->amount_paid > 0) {
                $invoice->payment_status = 'partial';
            }

            $invoice->save();

            $payment->load(['invoice', 'invoice.customer', 'status', 'processedBy']);

            DB::commit();

            return response()->json([
                'message' => 'Payment processed successfully',
                'data' => $payment,
                'invoice' => $invoice
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to process payment',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display Payment Page
     */
    public function showPaymentPage(): View
    {
        return view('payments.index');
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Payment::with(['invoice', 'status', 'processedBy']);

        // Search functionality
        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('payment_reference', 'like', "%{$search}%")
                    ->orWhere('notes', 'like', "%{$search}%")
                    ->orWhereHas('invoice', function ($invoiceQuery) use ($search) {
                        $invoiceQuery->where('invoice_number', 'like', "%{$search}%");
                    });
            });
        }

        // Filter by invoice_id
        if ($request->has('invoice_id')) {
            $query->where('invoice_id', $request->get('invoice_id'));
        }

        // Filter by status
        if ($request->has('status_id')) {
            $query->where('status_id', $request->get('status_id'));
        }

        // Filter by payment_method
        if ($request->has('payment_method')) {
            $query->where('payment_method', $request->get('payment_method'));
        }

        // Filter by date range
        if ($request->has('payment_date_from')) {
            $query->where('payment_date', '>=', $request->get('payment_date_from'));
        }
        if ($request->has('payment_date_to')) {
            $query->where('payment_date', '<=', $request->get('payment_date_to'));
        }

        // Pagination
        $perPage = $request->get('per_page', 15);
        $payments = $query->orderBy('payment_date', 'desc')->paginate($perPage);

        return response()->json($payments);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePaymentRequest $request): JsonResponse
    {
        return $this->processPayment($request);
    }

    /**
     * Display the specified resource.
     */
    public function show(Payment $payment): JsonResponse
    {
        $payment->load(['invoice', 'invoice.customer', 'invoice.invoiceItems', 'status', 'processedBy']);

        return response()->json([
            'data' => $payment
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePaymentRequest $request, Payment $payment): JsonResponse
    {
        DB::beginTransaction();

        try {
            $oldAmount = $payment->amount;
            $payment->update($request->validated());

            // If amount changed, update invoice
            if ($oldAmount != $payment->amount) {
                $invoice = $payment->invoice;
                $invoice->amount_paid = $invoice->amount_paid - $oldAmount + $payment->amount;
                $invoice->balance_due = $invoice->total_amount - $invoice->amount_paid;

                // Update payment status
                if ($invoice->balance_due <= 0) {
                    $invoice->payment_status = 'paid';
                } elseif ($invoice->amount_paid > 0) {
                    $invoice->payment_status = 'partial';
                } else {
                    $invoice->payment_status = 'unpaid';
                }

                $invoice->save();
            }

            $payment->load(['invoice', 'status', 'processedBy']);

            DB::commit();

            return response()->json([
                'message' => 'Payment updated successfully',
                'data' => $payment
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to update payment',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Payment $payment): JsonResponse
    {
        DB::beginTransaction();

        try {
            // Update invoice before deleting payment
            $invoice = $payment->invoice;
            $invoice->amount_paid -= $payment->amount;
            $invoice->balance_due = $invoice->total_amount - $invoice->amount_paid;

            // Update payment status
            if ($invoice->balance_due <= 0) {
                $invoice->payment_status = 'paid';
            } elseif ($invoice->amount_paid > 0) {
                $invoice->payment_status = 'partial';
            } else {
                $invoice->payment_status = 'unpaid';
            }

            $invoice->save();

            $payment->delete();

            DB::commit();

            return response()->json([
                'message' => 'Payment deleted successfully'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to delete payment',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
