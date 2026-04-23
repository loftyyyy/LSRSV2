<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePaymentRequest;
use App\Http\Requests\UpdatePaymentRequest;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\PaymentStatus;
use App\Services\PaymentService;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

/**
 * Controller handling payment processing and financial transactions.
 * 
 * This controller manages all payment-related functionality including:
 * - Payment processing and validation
 * - Refunds and voids
 * - Payment history and reporting
 * - Invoice payment tracking
 * - Payment method management
 * - Financial reporting and analytics
 * - PDF and CSV receipt/report generation
 */
class PaymentController extends Controller
{
    /**
     * @var App\Services\PaymentService Service for processing payments
     */
    protected PaymentService $paymentService;

    /**
     * Constructor for PaymentController.
     * 
     * Injects the PaymentService dependency for handling payment operations.
     * 
     * @param App\Services\PaymentService $paymentService The payment service instance
     */
    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    /**
     * Display the payment management page.
     * 
     * @return \Illuminate\View\View The payments index view
     */
    public function showPaymentPage(): View
    {
        $paymentStatuses = PaymentStatus::all();

        // Return the main payment management view
        return view('payments.index', compact('paymentStatuses'));
    }

    /**
     * Display a listing of payments with filtering and pagination.
     * 
     * Retrieves payments based on various filter criteria including search,
     * invoice ID, customer ID, status, payment method, and date range.
     * Results are paginated and ordered by payment date (descending).
     * 
     * @param \Illuminate\Http\Request $request The HTTP request containing filter parameters
     * @return \Illuminate\Http\JsonResponse JSON response with paginated payment data
     */
    public function index(Request $request): JsonResponse
    {
        // Base query with eager loading of related models
        $query = Payment::with(['invoice', 'invoice.customer', 'status', 'processedBy']);

        // Apply search filter if provided
        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('payment_reference', 'like', "%{$search}%")
                    ->orWhere('notes', 'like', "%{$search}%")
                    ->orWhereHas('invoice', function ($invoiceQuery) use ($search) {
                        $invoiceQuery->where('invoice_number', 'like', "%{$search}%");
                    })
                    ->orWhereHas('invoice.customer', function ($customerQuery) use ($search) {
                        $customerQuery->where('first_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        // Filter by invoice ID
        if ($request->has('invoice_id')) {
            $query->where('invoice_id', $request->get('invoice_id'));
        }

        // Filter by customer ID (through invoice relationship)
        if ($request->has('customer_id')) {
            $query->whereHas('invoice', function ($q) use ($request) {
                $q->where('customer_id', $request->get('customer_id'));
            });
        }

        // Filter by status ID
        if ($request->has('status_id')) {
            $query->where('status_id', $request->get('status_id'));
        }

        // Filter by status name (case-insensitive)
        if ($request->has('status')) {
            $status = $request->get('status');
            $query->whereHas('status', function ($q) use ($status) {
                $q->whereRaw('LOWER(status_name) = ?', [strtolower($status)]);
            });
        }

        // Filter by payment method
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

        // Apply pagination with default of 15 items per page
        $perPage = $request->get('per_page', 15);
        $payments = $query->orderBy('payment_date', 'desc')->paginate($perPage);

        // Return JSON response with paginated payment data
        return response()->json($payments);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePaymentRequest $request): JsonResponse
    {
        try {
            $payment = $this->paymentService->processPayment(
                $request->validated(),
                Auth::id()
            );

            return response()->json([
                'message' => 'Payment processed successfully',
                'data' => $payment,
                'invoice' => $payment->invoice,
            ], 201);
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to process payment',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Payment $payment): JsonResponse
    {
        $payment->load(['invoice', 'invoice.customer', 'invoice.invoiceItems', 'status', 'processedBy']);

        return response()->json([
            'data' => $payment,
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
                $invoice->save();
            }

            $payment->load(['invoice', 'status', 'processedBy']);

            DB::commit();

            return response()->json([
                'message' => 'Payment updated successfully',
                'data' => $payment,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Failed to update payment',
                'error' => $e->getMessage(),
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
            $invoice->save();

            $payment->delete();

            DB::commit();

            return response()->json([
                'message' => 'Payment deleted successfully',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Failed to delete payment',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Void a payment
     */
    public function voidPayment(Request $request, Payment $payment): JsonResponse
    {
        $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        try {
            $payment = $this->paymentService->voidPayment(
                $payment,
                $request->reason,
                Auth::id()
            );

            return response()->json([
                'message' => 'Payment voided successfully',
                'data' => $payment,
            ]);
        } catch (\RuntimeException $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to void payment',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Process a refund for a payment
     */
    public function processRefund(Request $request, Payment $payment): JsonResponse
    {
        $request->validate([
            'refund_amount' => 'required|numeric|min:0.01',
            'reason' => 'required|string|max:500',
            'refund_method' => 'required|string|in:'.implode(',', array_keys(PaymentService::PAYMENT_METHODS)),
        ]);

        try {
            $refundPayment = $this->paymentService->processRefund(
                $payment,
                $request->refund_amount,
                $request->reason,
                $request->refund_method,
                Auth::id()
            );

            return response()->json([
                'message' => 'Refund processed successfully',
                'data' => $refundPayment,
                'original_payment' => $payment->fresh(['status']),
            ]);
        } catch (\InvalidArgumentException|\RuntimeException $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to process refund',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get rental fee details for a customer
     */
    public function getRentalFeeDetails(Request $request): JsonResponse
    {
        $result = $this->paymentService->getRentalFeeDetails(
            $request->get('invoice_id'),
            $request->get('reservation_id'),
            $request->get('rental_id'),
            $request->get('customer_id')
        );

        if (isset($result['error'])) {
            $statusCode = $result['error'] === 'Invoice not found' ? 404 : 400;

            return response()->json(['message' => $result['error']], $statusCode);
        }

        return response()->json($result);
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
                $q->whereIn(DB::raw('LOWER(status_name)'), ['paid', 'completed']);
            });
        } elseif ($status === 'pending') {
            $query->whereHas('status', function ($q) {
                $q->whereRaw('LOWER(status_name) = ?', ['pending']);
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

        // Get summary statistics
        $summary = [
            'total_completed' => Payment::whereHas('status', function ($q) {
                $q->whereIn(DB::raw('LOWER(status_name)'), ['paid', 'completed']);
            })->count(),
            'total_pending' => Payment::whereHas('status', function ($q) {
                $q->whereRaw('LOWER(status_name) = ?', ['pending']);
            })->count(),
            'total_completed_amount' => (float) Payment::whereHas('status', function ($q) {
                $q->whereIn(DB::raw('LOWER(status_name)'), ['paid', 'completed']);
            })->sum('amount'),
            'total_pending_amount' => (float) Payment::whereHas('status', function ($q) {
                $q->whereRaw('LOWER(status_name) = ?', ['pending']);
            })->sum('amount'),
        ];

        return response()->json([
            'payments' => $payments,
            'summary' => $summary,
        ]);
    }

    /**
     * Get daily collection report
     */
    public function getDailyCollection(Request $request): JsonResponse
    {
        $date = $request->has('date')
            ? Carbon::parse($request->get('date'))
            : Carbon::now();

        $report = $this->paymentService->getDailyCollectionReport($date);

        return response()->json($report);
    }

    /**
     * Get overdue payments
     */
    public function getOverduePayments(Request $request): JsonResponse
    {
        $daysOverdue = $request->get('days_overdue', 7);
        $overdueInvoices = $this->paymentService->getOverduePayments($daysOverdue);

        return response()->json([
            'data' => $overdueInvoices,
            'count' => $overdueInvoices->count(),
            'total_overdue' => (float) $overdueInvoices->sum('balance_due'),
        ]);
    }

    /**
     * Get payment summary statistics
     */
    public function getSummary(Request $request): JsonResponse
    {
        $startDate = $request->has('start_date')
            ? Carbon::parse($request->get('start_date'))
            : Carbon::now()->startOfMonth();

        $endDate = $request->has('end_date')
            ? Carbon::parse($request->get('end_date'))
            : Carbon::now()->endOfMonth();

        $summary = $this->paymentService->getPaymentSummary($startDate, $endDate);

        return response()->json($summary);
    }

    /**
     * Get available payment methods
     */
    public function getPaymentMethods(): JsonResponse
    {
        return response()->json([
            'payment_methods' => PaymentService::getPaymentMethods(),
        ]);
    }

    /**
     * Get payment statuses
     */
    public function getPaymentStatuses(): JsonResponse
    {
        $statuses = PaymentStatus::all();

        return response()->json([
            'data' => $statuses,
        ]);
    }

    /**
     * Display Reports Page
     */
    public function report(Request $request): JsonResponse
    {
        $startDate = $request->has('start_date')
            ? Carbon::parse($request->get('start_date'))
            : Carbon::now()->startOfMonth();

        $endDate = $request->has('end_date')
            ? Carbon::parse($request->get('end_date'))
            : Carbon::now()->endOfMonth();

        $reportType = $request->get('report_type', 'daily'); // daily, weekly, monthly

        $query = Payment::with(['invoice', 'invoice.customer', 'processedBy', 'status'])
            ->whereBetween('payment_date', [$startDate, $endDate]);

        // Summary statistics
        $summary = [
            'total_payments' => $query->count(),
            'total_amount_collected' => (float) $query->sum('amount'),
            'completed_payments' => $query->clone()->whereHas('status', function ($q) {
                $q->whereIn(DB::raw('LOWER(status_name)'), ['paid', 'completed']);
            })->count(),
            'pending_payments' => $query->clone()->whereHas('status', function ($q) {
                $q->whereRaw('LOWER(status_name) = ?', ['pending']);
            })->count(),
        ];

        // Group by date based on report type
        $groupedData = $this->groupPaymentsByPeriod($query->get(), $reportType);

        // Payment method breakdown
        $paymentMethodBreakdown = $query->clone()
            ->select('payment_method', DB::raw('COUNT(*) as count'), DB::raw('SUM(amount) as total'))
            ->groupBy('payment_method')
            ->get()
            ->map(function ($item) {
                return [
                    'method' => $item->payment_method,
                    'label' => PaymentService::PAYMENT_METHODS[$item->payment_method] ?? ucfirst($item->payment_method),
                    'count' => $item->count,
                    'total' => (float) $item->total,
                ];
            });

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
                'customers.email',
                DB::raw('COUNT(payments.payment_id) as payment_count'),
                DB::raw('SUM(payments.amount) as total_paid')
            )
            ->groupBy('customers.customer_id', 'customers.first_name', 'customers.last_name', 'customers.email')
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
                'start_date' => $startDate->format('Y-m-d'),
                'end_date' => $endDate->format('Y-m-d'),
                'report_type' => $reportType,
            ],
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
                'total_amount' => (float) $group->sum('amount'),
                'cash' => (float) $group->where('payment_method', 'cash')->sum('amount'),
                'card' => (float) $group->where('payment_method', 'card')->sum('amount'),
                'gcash' => (float) $group->where('payment_method', 'gcash')->sum('amount'),
                'paymaya' => (float) $group->where('payment_method', 'paymaya')->sum('amount'),
                'bank_transfer' => (float) $group->where('payment_method', 'bank_transfer')->sum('amount'),
            ];
        });
    }

    /**
     * Create PDF for reports
     */
    public function generatePDF(Request $request)
    {
        $startDate = $request->has('start_date')
            ? Carbon::parse($request->get('start_date'))
            : Carbon::now()->startOfMonth();

        $endDate = $request->has('end_date')
            ? Carbon::parse($request->get('end_date'))
            : Carbon::now()->endOfMonth();

        $reportType = $request->get('report_type', 'daily');

        $payments = Payment::with(['invoice', 'invoice.customer', 'processedBy', 'status'])
            ->whereBetween('payment_date', [$startDate, $endDate])
            ->orderBy('payment_date', 'desc')
            ->get();

        $summary = [
            'total_payments' => $payments->count(),
            'total_amount_collected' => (float) $payments->sum('amount'),
            'completed_payments' => $payments->filter(fn ($p) => in_array(strtolower($p->status->status_name ?? ''), ['paid', 'completed'])
            )->count(),
            'pending_payments' => $payments->filter(fn ($p) => strtolower($p->status->status_name ?? '') === 'pending'
            )->count(),
        ];

        $paymentMethodBreakdown = $payments->groupBy('payment_method')->map(function ($group, $method) {
            return [
                'method' => $method,
                'label' => PaymentService::PAYMENT_METHODS[$method] ?? ucfirst($method),
                'count' => $group->count(),
                'total' => (float) $group->sum('amount'),
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

        return $pdf->download('payment-report-'.Carbon::now()->format('Y-m-d').'.pdf');
    }

    /**
     * Generate CSV for reports
     */
    public function generateCSV(Request $request)
    {
        $startDate = $request->has('start_date')
            ? Carbon::parse($request->get('start_date'))
            : Carbon::now()->startOfMonth();

        $endDate = $request->has('end_date')
            ? Carbon::parse($request->get('end_date'))
            : Carbon::now()->endOfMonth();

        $payments = Payment::with(['invoice', 'invoice.customer', 'processedBy', 'status'])
            ->whereBetween('payment_date', [$startDate, $endDate])
            ->orderBy('payment_date', 'desc')
            ->get();

        // Calculate statistics
        $statistics = [
            'total_payments' => $payments->count(),
            'total_amount_collected' => $payments->sum('amount'),
            'completed_payments' => $payments->filter(fn ($p) => in_array(strtolower($p->status->status_name ?? ''), ['paid', 'completed'])
            )->count(),
            'pending_payments' => $payments->filter(fn ($p) => strtolower($p->status->status_name ?? '') === 'pending'
            )->count(),
        ];

        // Payment method totals
        $methodTotals = [];
        foreach (PaymentService::PAYMENT_METHODS as $method => $label) {
            $methodTotals[$method] = [
                'label' => $label,
                'total' => (float) $payments->where('payment_method', $method)->sum('amount'),
            ];
        }

        // Set headers for CSV download
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="payment-report-'.Carbon::now()->format('Y-m-d').'.csv"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () use ($payments, $statistics, $methodTotals, $startDate, $endDate) {
            $output = fopen('php://output', 'w');
fputs($output, chr(0xEF) . chr(0xBB) . chr(0xBF)); // BOM for UTF-8

            // Add report header
            fputcsv($output, ['Payment Report']);
            fputcsv($output, ['Generated at', Carbon::now()->format('Y-m-d H:i:s')]);
            fputcsv($output, ['Date Range', Carbon::parse($startDate)->format('Y-m-d').' to '.Carbon::parse($endDate)->format('Y-m-d')]);
            fputcsv($output, []); // Empty row

            // Add statistics
            fputcsv($output, ['Report Statistics']);
            fputcsv($output, ['Total Payments', $statistics['total_payments']]);
            fputcsv($output, ['Total Amount Collected', number_format($statistics['total_amount_collected'], 2)]);
            fputcsv($output, ['Completed Payments', $statistics['completed_payments']]);
            fputcsv($output, ['Pending Payments', $statistics['pending_payments']]);
            fputcsv($output, []); // Empty row

            // Payment method breakdown
            fputcsv($output, ['Payment Method Breakdown']);
            foreach ($methodTotals as $method => $data) {
                fputcsv($output, [$data['label'], number_format($data['total'], 2)]);
            }
            fputcsv($output, []); // Empty row

            // Add payment data header
            fputcsv($output, ['Payment Details']);
            fputcsv($output, [
                'Payment ID',
                'Payment Reference',
                'Invoice Number',
                'Customer Name',
                'Customer Email',
                'Amount',
                'Payment Method',
                'Payment Date',
                'Status',
                'Processed By',
                'Notes',
            ]);

            // Add payment data rows
            foreach ($payments as $payment) {
                $customerName = $payment->invoice && $payment->invoice->customer
                    ? $payment->invoice->customer->first_name.' '.$payment->invoice->customer->last_name
                    : 'N/A';
                $customerEmail = $payment->invoice->customer->email ?? 'N/A';
                $invoiceNumber = $payment->invoice->invoice_number ?? 'N/A';
                $processedBy = $payment->processedBy->name ?? 'N/A';
                $methodLabel = PaymentService::PAYMENT_METHODS[$payment->payment_method] ?? ucfirst($payment->payment_method ?? 'N/A');

                fputcsv($output, [
                    $payment->payment_id,
                    $payment->payment_reference ?? 'N/A',
                    $invoiceNumber,
                    $customerName,
                    $customerEmail,
                    number_format($payment->amount, 2),
                    $methodLabel,
                    $payment->payment_date ? Carbon::parse($payment->payment_date)->format('Y-m-d H:i:s') : '',
                    $payment->status->status_name ?? 'N/A',
                    $processedBy,
                    $payment->notes ?? '',
                ]);
            }

            fclose($output);
        };

        return response()->stream($callback, 200, $headers);
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
                'name' => config('app.company_name', 'Love and Style Rental System'),
                'address' => config('app.company_address', 'Your Company Address'),
                'phone' => config('app.company_phone', 'Your Phone Number'),
                'email' => config('app.company_email', 'Your Email'),
            ],
        ]);

        return $pdf->download('receipt-'.$payment->payment_reference.'.pdf');
    }
}
