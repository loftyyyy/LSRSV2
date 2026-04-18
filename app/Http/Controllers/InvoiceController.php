<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreInvoiceRequest;
use App\Http\Requests\UpdateInvoiceRequest;
use App\Models\Invoice;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class InvoiceController extends Controller
{
    /**
     * Display Reports Page
     */
    public function report(Request $request): JsonResponse
    {

        $startDate = $request->get('start_date', Carbon::now()->startOfMonth());
        $endDate = $request->get('end_date', Carbon::now()->endOfMonth());
        $reportType = $request->get('report_type', 'daily'); // daily, weekly, monthly

        $query = Invoice::with(['customer', 'payments', 'invoiceItems'])
            ->whereBetween('invoice_date', [$startDate, $endDate]);

        // Summary statistics
        $summary = [
            'total_invoices' => $query->count(),
            'total_amount' => $query->sum('total_amount'),
            'total_paid' => $query->sum('amount_paid'),
            'total_balance_due' => $query->sum('balance_due'),
            'fully_paid_count' => $query->clone()->where('balance_due', '<=', 0)->count(),
            'pending_payment_count' => $query->clone()->where('balance_due', '>', 0)->count(),
        ];

        // Group by date based on report type
        $groupedData = $this->groupInvoicesByPeriod($query->get(), $reportType);

        // Payment method breakdown
        $paymentMethodBreakdown = DB::table('payments')
            ->join('invoices', 'payments.invoice_id', '=', 'invoices.invoice_id')
            ->whereBetween('payments.payment_date', [$startDate, $endDate])
            ->select('payment_method', DB::raw('COUNT(*) as count'), DB::raw('SUM(amount) as total'))
            ->groupBy('payment_method')
            ->get();

        // Invoice type breakdown
        $invoiceTypeBreakdown = $query->clone()
            ->select('invoice_type', DB::raw('COUNT(*) as count'), DB::raw('SUM(total_amount) as total'))
            ->groupBy('invoice_type')
            ->get();

        return response()->json([
            'summary' => $summary,
            'grouped_data' => $groupedData,
            'payment_method_breakdown' => $paymentMethodBreakdown,
            'invoice_type_breakdown' => $invoiceTypeBreakdown,
            'period' => [
                'start_date' => $startDate,
                'end_date' => $endDate,
                'report_type' => $reportType,
            ],
        ]);

    }

    /**
     * Helper function to group invoices by period
     */
    private function groupInvoicesByPeriod($invoices, $reportType)
    {
        return $invoices->groupBy(function ($invoice) use ($reportType) {
            switch ($reportType) {
                case 'daily':
                    return $invoice->invoice_date->format('Y-m-d');
                case 'weekly':
                    return $invoice->invoice_date->format('Y-W');
                case 'monthly':
                    return $invoice->invoice_date->format('Y-m');
                default:
                    return $invoice->invoice_date->format('Y-m-d');
            }
        })->map(function ($group) {
            return [
                'count' => $group->count(),
                'total_amount' => $group->sum('total_amount'),
                'amount_paid' => $group->sum('amount_paid'),
                'balance_due' => $group->sum('balance_due'),
            ];
        });
    }

    /**
     * Generate invoice/receipt PDF for a specific transaction
     */
    public function generateInvoicePDF(Invoice $invoice)
    {
        $invoice->load(['customer', 'reservation', 'rental', 'invoiceItems', 'payments', 'createdBy']);

        $pdf = Pdf::loadView('invoices.invoice-pdf', [
            'invoice' => $invoice,
            'company' => [
                'name' => 'Love and Style Rental System',
                'address' => 'Your Company Address',
                'phone' => 'Your Phone Number',
                'email' => 'Your Email',
            ],
        ]);

        return $pdf->download('invoice-'.$invoice->invoice_number.'.pdf');
    }

    /**
     * Create PDF for reports
     */
    public function generatePDF(Request $request)
    {
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth());
        $endDate = $request->get('end_date', Carbon::now()->endOfMonth());
        $reportType = $request->get('report_type', 'daily');

        $invoices = Invoice::with(['customer', 'payments', 'invoiceItems'])
            ->whereBetween('invoice_date', [$startDate, $endDate])
            ->get();

        $summary = [
            'total_invoices' => $invoices->count(),
            'total_amount' => $invoices->sum('total_amount'),
            'total_paid' => $invoices->sum('amount_paid'),
            'total_balance_due' => $invoices->sum('balance_due'),
            'fully_paid_count' => $invoices->where('balance_due', '<=', 0)->count(),
            'pending_payment_count' => $invoices->where('balance_due', '>', 0)->count(),
        ];

        $pdf = Pdf::loadView('reports.billing-report', [
            'invoices' => $invoices,
            'summary' => $summary,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'report_type' => $reportType,
            'generated_at' => Carbon::now(),
        ]);

        return $pdf->download('billing-report-'.Carbon::now()->format('Y-m-d').'.pdf');
    }

    /**
     * Generate CSV for reports
     */
    public function generateCSV(Request $request)
    {
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth());
        $endDate = $request->get('end_date', Carbon::now()->endOfMonth());

        $invoices = Invoice::with(['customer', 'payments', 'invoiceItems', 'rental', 'reservation'])
            ->whereBetween('invoice_date', [$startDate, $endDate])
            ->get();

        // Calculate statistics
        $statistics = [
            'total_invoices' => $invoices->count(),
            'total_amount' => $invoices->sum('total_amount'),
            'total_paid' => $invoices->sum('amount_paid'),
            'total_balance_due' => $invoices->sum('balance_due'),
            'fully_paid_count' => $invoices->where('balance_due', '<=', 0)->count(),
            'pending_payment_count' => $invoices->where('balance_due', '>', 0)->count(),
        ];

        // Set headers for CSV download
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="invoice-report-'.Carbon::now()->format('Y-m-d').'.csv"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () use ($invoices, $statistics, $startDate, $endDate) {
            $output = fopen('php://output', 'w');
fputs($output, chr(0xEF) . chr(0xBB) . chr(0xBF)); // BOM for UTF-8

            // Add report header
            fputcsv($output, ['Invoice/Billing Report']);
            fputcsv($output, ['Generated at', Carbon::now()->format('Y-m-d H:i:s')]);
            fputcsv($output, ['Date Range', Carbon::parse($startDate)->format('Y-m-d').' to '.Carbon::parse($endDate)->format('Y-m-d')]);
            fputcsv($output, []); // Empty row

            // Add statistics
            fputcsv($output, ['Report Statistics']);
            fputcsv($output, ['Total Invoices', $statistics['total_invoices']]);
            fputcsv($output, ['Total Amount', number_format($statistics['total_amount'], 2)]);
            fputcsv($output, ['Total Paid', number_format($statistics['total_paid'], 2)]);
            fputcsv($output, ['Total Balance Due', number_format($statistics['total_balance_due'], 2)]);
            fputcsv($output, ['Fully Paid Invoices', $statistics['fully_paid_count']]);
            fputcsv($output, ['Pending Payment Invoices', $statistics['pending_payment_count']]);
            fputcsv($output, []); // Empty row

            // Add invoice data header
            fputcsv($output, ['Invoice Details']);
            fputcsv($output, [
                'Invoice ID',
                'Invoice Number',
                'Customer Name',
                'Customer Email',
                'Invoice Type',
                'Invoice Date',
                'Due Date',
                'Subtotal',
                'Discount',
                'Tax',
                'Total Amount',
                'Amount Paid',
                'Balance Due',
                'Payment Status',
                'Related Rental ID',
                'Related Reservation ID',
            ]);

            // Add invoice data rows
            foreach ($invoices as $invoice) {
                $customerName = $invoice->customer
                    ? $invoice->customer->first_name.' '.$invoice->customer->last_name
                    : 'N/A';
                $customerEmail = $invoice->customer->email ?? 'N/A';
                $paymentStatus = $invoice->balance_due <= 0 ? 'Paid' : ($invoice->amount_paid > 0 ? 'Partial' : 'Unpaid');

                fputcsv($output, [
                    $invoice->invoice_id,
                    $invoice->invoice_number ?? 'N/A',
                    $customerName,
                    $customerEmail,
                    ucfirst($invoice->invoice_type ?? 'N/A'),
                    $invoice->invoice_date ? Carbon::parse($invoice->invoice_date)->format('Y-m-d') : '',
                    $invoice->due_date ? Carbon::parse($invoice->due_date)->format('Y-m-d') : '',
                    number_format($invoice->subtotal ?? 0, 2),
                    number_format($invoice->discount ?? 0, 2),
                    number_format($invoice->tax ?? 0, 2),
                    number_format($invoice->total_amount ?? 0, 2),
                    number_format($invoice->amount_paid ?? 0, 2),
                    number_format($invoice->balance_due ?? 0, 2),
                    $paymentStatus,
                    $invoice->rental_id ?? 'N/A',
                    $invoice->reservation_id ?? 'N/A',
                ]);
            }

            fclose($output);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Display Invoice Page
     */
    public function showInvoicePage(): View
    {
        return view('invoices.index');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Invoice::with(['customer', 'reservation', 'rental', 'status', 'createdBy']);

        // Search functionality
        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                    ->orWhereHas('customer', function ($customerQuery) use ($search) {
                        $customerQuery->where('first_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        // Filter by customer
        if ($request->has('customer_id')) {
            $query->where('customer_id', $request->get('customer_id'));
        }

        // Filter by invoice type
        if ($request->has('invoice_type')) {
            $query->where('invoice_type', $request->get('invoice_type'));
        }

        // Filter by payment status (balance_due > 0 means unpaid)
        if ($request->has('payment_status')) {
            $paymentStatus = $request->get('payment_status');
            if ($paymentStatus === 'paid') {
                $query->where('balance_due', '<=', 0);
            } elseif ($paymentStatus === 'unpaid') {
                $query->where('balance_due', '>', 0);
            }
        }

        // Pagination
        $perPage = $request->get('per_page', 15);
        $invoices = $query->orderBy('invoice_date', 'desc')->paginate($perPage);

        return response()->json($invoices);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreInvoiceRequest $request): JsonResponse
    {
        DB::beginTransaction();

        try {
            $invoice = Invoice::create($request->validated());

            // If invoice items are provided, create them
            if ($request->has('items')) {
                foreach ($request->items as $item) {
                    $invoice->invoiceItems()->create($item);
                }
            }

            $invoice->load(['customer', 'invoiceItems', 'reservation', 'rental', 'createdBy']);

            DB::commit();

            return response()->json([
                'message' => 'Invoice created successfully',
                'data' => $invoice,
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Failed to create invoice',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Invoice $invoice): JsonResponse
    {
        $invoice->load([
            'customer',
            'reservation',
            'rental',
            'invoiceItems.item',
            'payments',
            'status',
            'createdBy',
        ]);

        return response()->json([
            'data' => $invoice,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateInvoiceRequest $request, Invoice $invoice): JsonResponse
    {
        $invoice->update($request->validated());

        $invoice->load(['customer', 'invoiceItems', 'reservation', 'rental', 'createdBy']);

        return response()->json([
            'message' => 'Invoice updated successfully',
            'data' => $invoice,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Invoice $invoice): JsonResponse
    {
        // Check if invoice has payments
        $paymentCount = $invoice->payments()->count();

        if ($paymentCount > 0) {
            return response()->json([
                'message' => "Cannot delete invoice. It has {$paymentCount} payment(s) associated with it.",
            ], 422);
        }

        $invoice->delete();

        return response()->json([
            'message' => 'Invoice deleted successfully',
        ]);
    }

    /**
     * Get rental fee details for a customer
     */
    public function getRentalFeeDetails(Request $request): JsonResponse
    {
        $invoiceId = $request->get('invoice_id');
        $reservationId = $request->get('reservation_id');
        $rentalId = $request->get('rental_id');

        $query = Invoice::with(['invoiceItems', 'customer', 'reservation', 'rental']);

        if ($invoiceId) {
            $invoice = $query->find($invoiceId);
        } elseif ($reservationId) {
            $invoice = $query->where('reservation_id', $reservationId)->first();
        } elseif ($rentalId) {
            $invoice = $query->where('rental_id', $rentalId)->first();
        } else {
            return response()->json(['message' => 'Please provide invoice_id, reservation_id, or rental_id'], 400);
        }

        if (! $invoice) {
            return response()->json(['message' => 'Invoice not found'], 404);
        }

        $rentalFees = $invoice->invoiceItems()
            ->where('item_type', 'rental_fee')
            ->get();

        $penalties = $invoice->invoiceItems()
            ->whereIn('item_type', ['penalty', 'late_fee'])
            ->get();

        $otherCharges = $invoice->invoiceItems()
            ->whereNotIn('item_type', ['rental_fee', 'penalty', 'late_fee'])
            ->get();

        return response()->json([
            'invoice' => $invoice,
            'breakdown' => [
                'rental_fees' => $rentalFees,
                'penalties' => $penalties,
                'other_charges' => $otherCharges,
            ],
            'totals' => [
                'subtotal' => $invoice->subtotal,
                'discount' => $invoice->discount,
                'tax' => $invoice->tax,
                'total_amount' => $invoice->total_amount,
                'amount_paid' => $invoice->amount_paid,
                'balance_due' => $invoice->balance_due,
            ],
        ]);
    }

    /**
     * Monitor all completed and pending payments
     */
    public function monitorPayments(Request $request): JsonResponse
    {
        $status = $request->get('status', 'all'); // all, completed, pending

        $query = Invoice::with(['customer', 'payments', 'reservation', 'rental', 'status']);

        if ($status === 'completed') {
            $query->where('balance_due', '<=', 0);
        } elseif ($status === 'pending') {
            $query->where('balance_due', '>', 0);
        }

        // Search functionality
        if ($request->has('search') && ! empty($request->get('search'))) {
            $searchTerm = $request->get('search');
            $query->where(function ($q) use ($searchTerm) {
                $q->where('invoice_number', 'like', "%{$searchTerm}%")
                    ->orWhere('total_amount', 'like', "%{$searchTerm}%")
                    ->orWhere('balance_due', 'like', "%{$searchTerm}%")
                    ->orWhereHas('customer', function ($customerQuery) use ($searchTerm) {
                        $customerQuery->where('first_name', 'like', "%{$searchTerm}%")
                            ->orWhere('last_name', 'like', "%{$searchTerm}%")
                            ->orWhere('email', 'like', "%{$searchTerm}%");
                    })
                    ->orWhereHas('payments', function ($paymentQuery) use ($searchTerm) {
                        $paymentQuery->where('payment_reference', 'like', "%{$searchTerm}%");
                    });
            });
        }

        // Additional filters
        if ($request->has('customer_id')) {
            $query->where('customer_id', $request->get('customer_id'));
        }

        if ($request->has('date_from')) {
            $query->where('invoice_date', '>=', $request->get('date_from'));
        }

        if ($request->has('date_to')) {
            $query->where('invoice_date', '<=', $request->get('date_to'));
        }

        $perPage = $request->get('per_page', 15);
        $invoices = $query->orderBy('invoice_date', 'desc')->paginate($perPage);

        $summary = [
            'total_completed' => Invoice::where('balance_due', '<=', 0)->count(),
            'total_pending' => Invoice::where('balance_due', '>', 0)->count(),
            'total_pending_amount' => Invoice::where('balance_due', '>', 0)->sum('balance_due'),
        ];

        return response()->json([
            'invoices' => $invoices,
            'summary' => $summary,
        ]);
    }
}
