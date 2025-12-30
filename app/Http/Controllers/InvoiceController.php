<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Http\Requests\StoreInvoiceRequest;
use App\Http\Requests\UpdateInvoiceRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InvoiceController extends Controller
{

    /**
     * Display Reports Page
     */
    public function report(Request $request):JsonResponse
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
            ]
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
     * Create PDF for reports
     */
    public function generatePDF()
    {

    }
    /**
     * Display Invoice Page
     */
    public function showInvoicePage():View
    {
        return view('invoices.index');
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Invoice::with(['customer', 'reservation', 'rental', 'createdBy']);

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
        $invoices = $query->paginate($perPage);

        return response()->json($invoices);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreInvoiceRequest $request): JsonResponse
    {
        $invoice = Invoice::create($request->validated());

        $invoice->load(['customer', 'items', 'reservation', 'rental', 'createdBy']);

        return response()->json([
            'message' => 'Invoice created successfully',
            'data' => $invoice
        ], 201);
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
            'items',
            'payments',
            'createdBy'
        ]);

        return response()->json([
            'data' => $invoice
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateInvoiceRequest $request, Invoice $invoice): JsonResponse
    {
        $invoice->update($request->validated());

        $invoice->load(['customer', 'items', 'reservation', 'rental', 'createdBy']);

        return response()->json([
            'message' => 'Invoice updated successfully',
            'data' => $invoice
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
                'message' => "Cannot delete invoice. It has {$paymentCount} payment(s) associated with it."
            ], 422);
        }

        $invoice->delete();

        return response()->json([
            'message' => 'Invoice deleted successfully'
        ]);
    }
}
