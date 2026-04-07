<?php

namespace App\Http\Controllers;

use App\Http\Requests\ReleaseItemRequest;
use App\Http\Requests\StoreRentalRequest;
use App\Http\Requests\UpdateRentalRequest;
use App\Models\Inventory;
use App\Models\InventoryMovement;
use App\Models\InventoryStatus;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Payment;
use App\Models\PaymentStatus;
use App\Models\Rental;
use App\Models\RentalNotification;
use App\Models\RentalSetting;
use App\Models\RentalStatus;
use App\Models\Reservation;
use App\Models\ReservationItem;
use App\Models\ReservationItemAllocation;
use App\Models\ReservationStatus;
use App\Services\DepositService;
use App\Services\RentalReleaseService;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class RentalController extends Controller
{
    public function __construct(
        private readonly DepositService $depositService,
        private readonly RentalReleaseService $rentalReleaseService
    ) {}

    /**
     * Display Reports Page
     */
    public function report(Request $request): JsonResponse
    {
        $query = Rental::with(['customer', 'item', 'status', 'reservation', 'releasedBy', 'invoices.invoiceItems']);

        // Filter by confirmed reservations only (auto-confirmation requirement)
        $query->whereHas('reservation', function ($reservationQuery) {
            $reservationQuery->whereHas('status', function ($statusQuery) {
                $statusQuery->where('status_name', 'confirmed');
            });
        });

        // Apply date range filters
        if ($request->has('date_from')) {
            $query->where('released_date', '>=', Carbon::parse($request->get('date_from')));
        }
        if ($request->has('date_to')) {
            $query->where('released_date', '<=', Carbon::parse($request->get('date_to')));
        }

        // Filter by status
        if ($request->has('status_id')) {
            $query->where('status_id', $request->get('status_id'));
        }

        // Filter by rental status (active/returned/overdue)
        if ($request->has('rental_status')) {
            $rentalStatus = $request->get('rental_status');
            if ($rentalStatus === 'active') {
                $query->whereNull('return_date')->where('due_date', '>=', Carbon::now());
            } elseif ($rentalStatus === 'returned') {
                $query->whereNotNull('return_date');
            } elseif ($rentalStatus === 'overdue') {
                $query->whereNull('return_date')->where('due_date', '<', Carbon::now());
            }
        }

        $rentals = $query->get();

        // Calculate Analytics
        $totalRentals = $rentals->count();
        $activeRentals = $rentals->whereNull('return_date')->where('due_date', '>=', Carbon::now())->count();
        $returnedRentals = $rentals->whereNotNull('return_date')->count();
        $returnedOnTimeRentals = $rentals->filter(function ($rental) {
            return $rental->return_date !== null &&
                Carbon::parse($rental->return_date)->lessThanOrEqualTo(Carbon::parse($rental->due_date));
        })->count();
        $returnedOverdueRentals = $rentals->filter(function ($rental) {
            return $rental->return_date !== null &&
                Carbon::parse($rental->return_date)->greaterThan(Carbon::parse($rental->due_date));
        })->count();
        $overdueRentals = $rentals->whereNull('return_date')->where('due_date', '<', Carbon::now())->count();

        // Calculate total penalties from invoice items
        $totalPenalties = $this->getTotalPenaltiesFromInvoices($rentals);

        // Calculate revenue metrics
        $totalRevenue = $this->getTotalRevenueFromInvoices($rentals);

        return response()->json([
            'summary' => [
                'total_rentals' => $totalRentals,
                'active_rentals' => $activeRentals,
                'returned_rentals' => $returnedRentals,
                'returned_on_time' => $returnedOnTimeRentals,
                'returned_overdue' => $returnedOverdueRentals,
                'overdue_rentals' => $overdueRentals,
                'total_penalties' => $totalPenalties,
                'total_revenue' => $totalRevenue,
            ],
            'rentals' => $rentals,
        ]);
    }

    /**
     * Get total penalties from invoice items
     */
    private function getTotalPenaltiesFromInvoices($rentals): float
    {
        $rentalIds = $rentals->pluck('rental_id');

        return InvoiceItem::whereIn('invoice_id', function ($query) use ($rentalIds) {
            $query->select('invoice_id')->from('invoices')->whereIn('rental_id', $rentalIds);
        })->whereIn('item_type', ['penalty', 'late_fee'])->sum('total_price');
    }

    /**
     * Get total revenue from invoices
     */
    private function getTotalRevenueFromInvoices($rentals): float
    {
        $rentalIds = $rentals->pluck('rental_id');

        return Invoice::whereIn('rental_id', $rentalIds)
            ->where('payment_status', 'paid')
            ->sum('total_amount');
    }

    /**
     * Create PDF for reports
     */
    public function generatePDF(Request $request)
    {
        // Get filtered request based on request parameters
        $query = Rental::with(['customer', 'item', 'status', 'reservation', 'releasedBy', 'invoices.invoiceItems']);

        if ($request->has('date_from')) {
            $query->where('released_date', '>=', Carbon::parse($request->get('date_from')));
        }
        if ($request->has('date_to')) {
            $query->where('released_date', '<=', Carbon::parse($request->get('date_to')));
        }
        if ($request->has('status_id')) {
            $query->where('status_id', $request->get('status_id'));
        }

        $rentals = $query->get();

        // Calculate summary data
        $summary = [
            'total_rentals' => $rentals->count(),
            'active_rentals' => $rentals->where('return_date', null)->where('due_date', '>=', now())->count(),
            'returned_rentals' => $rentals->whereNotNull('return_date')->count(),
            'overdue_rentals' => $rentals->where('return_date', null)->where('due_date', '<', now())->count(),
            'total_penalties' => $this->getTotalPenaltiesFromInvoices($rentals),
            'total_revenue' => $this->getTotalRevenueFromInvoices($rentals),
            'date_from' => $request->get('date_from', 'All'),
            'date_to' => $request->get('date_to', 'All'),
        ];

        // Generate PDF using a view
        $pdf = Pdf::loadView('rentals.report-pdf', [
            'rentals' => $rentals,
            'summary' => $summary,
            'generated_at' => now()->format('Y-m-d H:i:s'),
        ]);

        return $pdf->download('rental-report-'.now()->format('Y-m-d').'.pdf');
    }

    /**
     * Generate CSV for reports
     */
    public function generateCSV(Request $request)
    {
        $query = Rental::with(['customer', 'item', 'status', 'reservation', 'releasedBy', 'invoices.invoiceItems']);

        // Apply date range filters
        if ($request->has('date_from')) {
            $query->where('released_date', '>=', Carbon::parse($request->get('date_from')));
        }
        if ($request->has('date_to')) {
            $query->where('released_date', '<=', Carbon::parse($request->get('date_to')));
        }

        // Filter by status
        if ($request->has('status_id')) {
            $query->where('status_id', $request->get('status_id'));
        }

        $rentals = $query->get();

        // Calculate statistics
        $statistics = [
            'total_rentals' => $rentals->count(),
            'active_rentals' => $rentals->whereNull('return_date')->where('due_date', '>=', Carbon::now())->count(),
            'returned_rentals' => $rentals->whereNotNull('return_date')->count(),
            'overdue_rentals' => $rentals->whereNull('return_date')->where('due_date', '<', Carbon::now())->count(),
            'total_revenue' => $this->getTotalRevenueFromInvoices($rentals),
            'total_penalties' => $this->getTotalPenaltiesFromInvoices($rentals),
        ];

        $dateFrom = $request->get('date_from');
        $dateTo = $request->get('date_to');

        // Set headers for CSV download
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="rental-report-'.now()->format('Y-m-d').'.csv"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () use ($rentals, $statistics, $dateFrom, $dateTo) {
            $output = fopen('php://output', 'w');

            // Add report header
            fputcsv($output, ['Rental Report']);
            fputcsv($output, ['Generated at', now()->format('Y-m-d H:i:s')]);
            fputcsv($output, ['Date Range', ($dateFrom ?? 'All').' to '.($dateTo ?? 'All')]);
            fputcsv($output, []); // Empty row

            // Add statistics
            fputcsv($output, ['Report Statistics']);
            fputcsv($output, ['Total Rentals', $statistics['total_rentals']]);
            fputcsv($output, ['Active Rentals', $statistics['active_rentals']]);
            fputcsv($output, ['Returned Rentals', $statistics['returned_rentals']]);
            fputcsv($output, ['Overdue Rentals', $statistics['overdue_rentals']]);
            fputcsv($output, ['Total Revenue', number_format($statistics['total_revenue'], 2)]);
            fputcsv($output, ['Total Penalties', number_format($statistics['total_penalties'], 2)]);
            fputcsv($output, []); // Empty row

            // Add rental data header
            fputcsv($output, ['Rental Details']);
            fputcsv($output, [
                'Rental ID',
                'Customer Name',
                'Customer Email',
                'Contact Number',
                'Item Name',
                'Item SKU',
                'Released Date',
                'Due Date',
                'Return Date',
                'Status',
                'Deposit Amount',
                'Total Amount',
                'Penalties',
                'Released By',
            ]);

            // Add rental data rows
            foreach ($rentals as $rental) {
                $customerName = $rental->customer
                    ? $rental->customer->first_name.' '.$rental->customer->last_name
                    : 'N/A';
                $customerEmail = $rental->customer->email ?? 'N/A';
                $contactNumber = $rental->customer->contact_number ?? 'N/A';
                $itemName = $rental->item->name ?? 'N/A';
                $itemSku = $rental->item->sku ?? 'N/A';
                $releasedBy = $rental->releasedBy->name ?? 'N/A';
                $totalAmount = $rental->invoices->sum('total_amount');
                $penalties = $rental->invoices->sum(function ($invoice) {
                    return $invoice->invoiceItems->whereIn('item_type', ['penalty', 'late_fee'])->sum('total_price');
                });

                fputcsv($output, [
                    $rental->rental_id,
                    $customerName,
                    $customerEmail,
                    $contactNumber,
                    $itemName,
                    $itemSku,
                    $rental->released_date ? Carbon::parse($rental->released_date)->format('Y-m-d') : '',
                    $rental->due_date ? Carbon::parse($rental->due_date)->format('Y-m-d') : '',
                    $rental->return_date ? Carbon::parse($rental->return_date)->format('Y-m-d') : 'Not Returned',
                    $rental->status->status_name ?? 'N/A',
                    number_format($rental->deposit_amount ?? 0, 2),
                    number_format($totalAmount, 2),
                    number_format($penalties, 2),
                    $releasedBy,
                ]);
            }

            fclose($output);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Display Rental Page
     */
    public function showRentalPage(): View
    {
        return view('rentals.index');
    }

    /**
     * Display Rental Reports Page
     */
    public function showReportsPage(): View
    {
        return view('rentals.reports');
    }

    /**
     * Get comprehensive rental metrics and analytics
     */
    public function getMetrics(): JsonResponse
    {
        // Basic Rental Stats
        $totalRentals = Rental::count();
        $activeRentals = Rental::whereHas('status', fn ($q) => $q->where('status_name', 'active'))->count();
        $completedRentals = Rental::whereHas('status', fn ($q) => $q->where('status_name', 'returned'))->count();
        $cancelledRentals = Rental::whereHas('status', fn ($q) => $q->where('status_name', 'cancelled'))->count();

        // Overdue & Late Returns
        $overdueRentals = Rental::whereNull('return_date')
            ->where('due_date', '<', now())
            ->count();

        // Calculate late returns properly (returned after due date)
        $lateReturnRentals = Rental::whereNotNull('return_date')
            ->whereColumn('return_date', '>', 'due_date')
            ->count();

        // On-time returns (returned on or before due date)
        $onTimeReturns = Rental::whereNotNull('return_date')
            ->whereColumn('return_date', '<=', 'due_date')
            ->count();

        // On-time return rate percentage
        $totalReturned = $onTimeReturns + $lateReturnRentals;
        $onTimeReturnRate = $totalReturned > 0 ? round(($onTimeReturns / $totalReturned) * 100, 1) : 0;

        // Duration Analysis
        $durationsInDays = Rental::get()->map(function ($rental) {
            $start = Carbon::parse($rental->released_date);
            $end = $rental->return_date ? Carbon::parse($rental->return_date) : Carbon::parse($rental->due_date);

            return max(1, $start->diffInDays($end) + 1);
        });

        $avgRentalDuration = $durationsInDays->count() > 0
            ? round($durationsInDays->avg(), 1)
            : 0;

        // Revenue Analysis
        $totalRentalRevenue = Rental::whereHas('invoices')
            ->with('invoices')
            ->get()
            ->sum(fn ($rental) => $rental->invoices->sum('total_amount')) ?? 0;

        $revenueThisMonth = Rental::where('created_at', '>=', now()->startOfMonth())
            ->whereHas('invoices')
            ->with('invoices')
            ->get()
            ->sum(fn ($rental) => $rental->invoices->sum('total_amount')) ?? 0;

        // Extension Analysis
        $rentalsWithExtensions = Rental::where('extension_count', '>', 0)->count();
        $totalExtensions = Rental::sum('extension_count');
        $avgExtensionsPerRental = $rentalsWithExtensions > 0
            ? round($totalExtensions / $rentalsWithExtensions, 1)
            : 0;
        $extensionRate = $totalRentals > 0
            ? round(($rentalsWithExtensions / $totalRentals) * 100, 1)
            : 0;

        // Deposit Analysis
        $depositsHeld = Rental::where('deposit_status', 'held')->sum('deposit_amount') ?? 0;
        $depositsReturned = Rental::where('deposit_status', 'returned')->sum('deposit_returned_amount') ?? 0;
        $depositsDeducted = Rental::whereIn('deposit_status', ['partial', 'forfeit'])
            ->sum('deposit_deducted_amount') ?? 0;

        // Penalty Analysis
        $totalPenalties = Invoice::whereHas('rental')
            ->whereHas('invoiceItems', fn ($q) => $q->where('item_type', 'penalty'))
            ->with('invoiceItems')
            ->get()
            ->sum(fn ($invoice) => $invoice->invoiceItems->where('item_type', 'penalty')->sum('amount')) ?? 0;

        $paidPenalties = Payment::whereHas('invoice', fn ($q) => $q->whereHas('invoiceItems', fn ($q2) => $q2->where('item_type', 'penalty')))
            ->sum('amount') ?? 0;

        $penaltyCollectionRate = $totalPenalties > 0
            ? round(($paidPenalties / $totalPenalties) * 100, 1)
            : 0;

        // Rental Status Distribution
        $rentalStatusDistribution = Rental::with('status')
            ->get()
            ->groupBy('status.status_name')
            ->map(fn ($rentals, $status) => [
                'status' => ucfirst($status),
                'count' => $rentals->count(),
            ])
            ->values();

        // Monthly Rental Trend (Last 12 months)
        $monthlyRentals = collect();
        for ($i = 11; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $count = Rental::whereMonth('created_at', $month->month)
                ->whereYear('created_at', $month->year)
                ->count();

            $monthlyRentals->push([
                'month' => $month->format('M'),
                'count' => $count,
            ]);
        }

        // Weekly Rental Revenue (Last 8 weeks)
        $weeklyRevenue = collect();
        for ($i = 7; $i >= 0; $i--) {
            $startOfWeek = now()->subWeeks($i)->startOfWeek();
            $endOfWeek = now()->subWeeks($i)->endOfWeek();

            $revenue = Rental::whereBetween('created_at', [$startOfWeek, $endOfWeek])
                ->whereHas('invoices')
                ->with('invoices')
                ->get()
                ->sum(fn ($rental) => $rental->invoices->sum('total_amount')) ?? 0;

            $weeklyRevenue->push([
                'week' => 'W'.$startOfWeek->weekOfYear,
                'revenue' => round($revenue, 2),
            ]);
        }

        // Top Customers by Rentals
        $topCustomers = Rental::with('customer')
            ->get()
            ->groupBy('customer_id')
            ->map(fn ($rentals, $customerId) => [
                'customer_id' => $customerId,
                'customer_name' => $rentals->first()->customer->first_name.' '.$rentals->first()->customer->last_name,
                'rental_count' => $rentals->count(),
                'total_spent' => $rentals->sum(fn ($rental) => $rental->invoices->sum('total_amount') ?? 0),
            ])
            ->sortByDesc('rental_count')
            ->take(8)
            ->values();

        // Top Items by Revenue
        $topItemsByRevenue = Rental::with('item', 'invoices')
            ->get()
            ->groupBy('item_id')
            ->map(fn ($rentals, $itemId) => [
                'item_id' => $itemId,
                'item_name' => $rentals->first()->item->name,
                'rental_count' => $rentals->count(),
                'total_revenue' => round($rentals->sum(fn ($rental) => $rental->invoices->sum('total_amount') ?? 0), 2),
            ])
            ->sortByDesc('total_revenue')
            ->take(8)
            ->values();

        // Rental Duration Distribution (for histogram approximation)
        $durationBuckets = [
            '1-2 days' => $durationsInDays->filter(fn ($days) => $days >= 1 && $days <= 2)->count(),
            '3-7 days' => $durationsInDays->filter(fn ($days) => $days >= 3 && $days <= 7)->count(),
            '1-2 weeks' => $durationsInDays->filter(fn ($days) => $days >= 8 && $days <= 14)->count(),
            '2-4 weeks' => $durationsInDays->filter(fn ($days) => $days >= 15 && $days <= 28)->count(),
            '1+ months' => $durationsInDays->filter(fn ($days) => $days >= 29)->count(),
        ];

        // Extension Trend (Last 6 months)
        $extensionTrend = collect();
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $count = Rental::whereMonth('last_extended_at', $month->month)
                ->whereYear('last_extended_at', $month->year)
                ->count();

            $extensionTrend->push([
                'month' => $month->format('M'),
                'count' => $count,
            ]);
        }

        // Return Performance (on-time vs late by month for last 6 months)
        $returnPerformance = collect();
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);

            $onTime = Rental::whereMonth('return_date', $month->month)
                ->whereYear('return_date', $month->year)
                ->whereNotNull('return_date')
                ->whereColumn('return_date', '<=', 'due_date')
                ->count();

            $late = Rental::whereMonth('return_date', $month->month)
                ->whereYear('return_date', $month->year)
                ->whereNotNull('return_date')
                ->whereColumn('return_date', '>', 'due_date')
                ->count();

            $returnPerformance->push([
                'month' => $month->format('M'),
                'on_time' => $onTime,
                'late' => $late,
            ]);
        }

        return response()->json([
            'kpis' => [
                'total_rentals' => $totalRentals,
                'active_rentals' => $activeRentals,
                'completed_rentals' => $completedRentals,
                'cancelled_rentals' => $cancelledRentals,
                'overdue_rentals' => $overdueRentals,
                'late_return_rentals' => $lateReturnRentals,
                'on_time_returns' => $onTimeReturns,
                'on_time_return_rate' => $onTimeReturnRate,
                'avg_rental_duration' => $avgRentalDuration,
                'total_rental_revenue' => round($totalRentalRevenue, 2),
                'revenue_this_month' => round($revenueThisMonth, 2),
                // Extension metrics
                'rentals_with_extensions' => $rentalsWithExtensions,
                'total_extensions' => $totalExtensions,
                'avg_extensions_per_rental' => $avgExtensionsPerRental,
                'extension_rate' => $extensionRate,
                // Deposit metrics
                'deposits_held' => round($depositsHeld, 2),
                'deposits_returned' => round($depositsReturned, 2),
                'deposits_deducted' => round($depositsDeducted, 2),
                // Penalty metrics
                'total_penalties' => round($totalPenalties, 2),
                'paid_penalties' => round($paidPenalties, 2),
                'penalty_collection_rate' => $penaltyCollectionRate,
            ],
            'rental_status_distribution' => $rentalStatusDistribution,
            'monthly_rentals' => $monthlyRentals,
            'weekly_revenue' => $weeklyRevenue,
            'top_customers' => $topCustomers,
            'top_items_by_revenue' => $topItemsByRevenue,
            'duration_distribution' => [
                ['duration' => '1-2 days', 'count' => $durationBuckets['1-2 days']],
                ['duration' => '3-7 days', 'count' => $durationBuckets['3-7 days']],
                ['duration' => '1-2 weeks', 'count' => $durationBuckets['1-2 weeks']],
                ['duration' => '2-4 weeks', 'count' => $durationBuckets['2-4 weeks']],
                ['duration' => '1+ months', 'count' => $durationBuckets['1+ months']],
            ],
            'extension_trend' => $extensionTrend,
            'return_performance' => $returnPerformance,
            'generated_at' => now()->format('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Rental::with(['customer', 'item', 'status', 'reservation', 'releasedBy']);

        // Filter by confirmed reservations only (auto-confirmation requirement)
        $query->whereHas('reservation', function ($reservationQuery) {
            $reservationQuery->whereHas('status', function ($statusQuery) {
                $statusQuery->where('status_name', 'confirmed');
            });
        });

        // Search functionality
        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->whereHas('customer', function ($customerQuery) use ($search) {
                    $customerQuery->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                })
                    ->orWhereHas('item', function ($itemQuery) use ($search) {
                        $itemQuery->where('sku', 'like', "%{$search}%")
                            ->orWhere('name', 'like', "%{$search}%");
                    })
                    ->orWhere('rental_id', 'like', "%{$search}%");
            });
        }

        // Filter by customer
        if ($request->has('customer_id')) {
            $query->where('customer_id', $request->get('customer_id'));
        }

        // Filter by item
        if ($request->has('item_id')) {
            $query->where('item_id', $request->get('item_id'));
        }

        // Filter by status
        if ($request->has('status_id')) {
            $query->where('status_id', $request->get('status_id'));
        }

        // Filter by rental status (active/returned)
        if ($request->has('rental_status')) {
            $rentalStatus = $request->get('rental_status');
            if ($rentalStatus === 'active') {
                $query->whereNull('return_date');
            } elseif ($rentalStatus === 'returned') {
                $query->whereNotNull('return_date');
            } elseif ($rentalStatus === 'overdue') {
                $query->whereNull('return_date')->where('due_date', '<', Carbon::now());
            }
        }

        // Filter by date range
        if ($request->has('released_date_from')) {
            $query->where('released_date', '>=', $request->get('released_date_from'));
        }
        if ($request->has('released_date_to')) {
            $query->where('released_date', '<=', $request->get('released_date_to'));
        }
        if ($request->has('due_date_from')) {
            $query->where('due_date', '>=', $request->get('due_date_from'));
        }
        if ($request->has('due_date_to')) {
            $query->where('due_date', '<=', $request->get('due_date_to'));
        }

        // Filter by item type (gown, suit, accessory, etc.)
        if ($request->has('item_type')) {
            $query->whereHas('item', function ($itemQuery) use ($request) {
                $itemQuery->where('item_type', $request->get('item_type'));
            });
        }

        // Sorting - only allow date fields for simplicity
        $allowedSortFields = ['created_at', 'released_date', 'due_date'];
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');

        if (! in_array($sortBy, $allowedSortFields)) {
            $sortBy = 'created_at';
        }
        if (! in_array($sortOrder, ['asc', 'desc'])) {
            $sortOrder = 'desc';
        }

        $query->orderBy($sortBy, $sortOrder);

        // Pagination
        $perPage = $request->get('per_page', 15);
        $rentals = $query->paginate($perPage);

        return response()->json($rentals);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRentalRequest $request): JsonResponse
    {
        $rental = Rental::create($request->validated());

        $rental->load(['customer', 'item', 'status', 'reservation', 'releasedBy']);

        return response()->json([
            'message' => 'Rental created successfully',
            'data' => $rental,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Rental $rental): JsonResponse
    {
        $rental->load([
            'customer',
            'item',
            'status',
            'reservation',
            'releasedBy',
            'returnedTo',
            'extendedBy',
            'invoices.invoiceItems',
        ]);

        // Add calculated penalty if overdue
        $penalty = $this->calculatePenalty($rental);

        return response()->json([
            'data' => $rental,
            'calculated_penalty' => $penalty,
            'is_overdue' => $rental->return_date === null && Carbon::parse($rental->due_date)->lessThan(Carbon::now()),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRentalRequest $request, Rental $rental): JsonResponse
    {
        $rental->update($request->validated());

        $rental->load(['customer', 'item', 'status', 'reservation', 'releasedBy']);

        return response()->json([
            'message' => 'Rental updated successfully',
            'data' => $rental,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Rental $rental): JsonResponse
    {
        // Check if rental has invoices
        $hasInvoices = $rental->invoices()->count() > 0;

        if ($hasInvoices) {
            return response()->json([
                'message' => 'Cannot delete rental. It has invoices associated with it.',
            ], 422);
        }

        $rental->delete();

        return response()->json([
            'message' => 'Rental deleted successfully',
        ]);
    }

    /**
     * BUSINESS ACTIVITY: Clerk logs when a gown or suit is released to the customer
     * Release an item to customer (converts reservation to rental)
     */
    /**
     * Release item to customer using RentalReleaseService
     *
     * NEW FLOW: Explicit item ID required (no auto-lookup)
     * - Deposit amount comes from item/variant (no manual override)
     * - Payment integrated through PaymentService
     */
    public function releaseItem(ReleaseItemRequest $request): JsonResponse
    {
        try {
            $result = $this->rentalReleaseService->releaseItem(
                $request->validated(),
                auth()->id() ?? 1
            );

            // Check if result is an error array
            if (is_array($result) && isset($result['error'])) {
                return response()->json([
                    'message' => $result['error'],
                ], $result['code'] ?? 422);
            }

            return response()->json([
                'message' => 'Item released successfully to customer',
                'data' => $result,
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to release item',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * BUSINESS ACTIVITY: Clerk records the return of rented items
     * Process return and finalize penalties
     */
    public function processReturn(Rental $rental, Request $request): JsonResponse
    {
        $request->validate([
            'return_date' => 'required|date',
            'return_notes' => 'nullable|string',
            'condition_notes' => 'nullable|string',
            'collect_rental_payment' => 'sometimes|boolean',
            'rental_payment_amount' => 'nullable|numeric|min:0',
            'rental_payment_method' => 'required_if:collect_rental_payment,true|in:cash,card,bank_transfer,gcash,paymaya',
            'rental_payment_notes' => 'nullable|string',
            'deposit_return_action' => 'sometimes|in:full,partial,forfeit,hold',
            'deposit_return_method' => 'required_if:deposit_return_action,full,partial|in:cash,bank_transfer,gcash,paymaya,check',
            'deposit_return_reference' => 'nullable|string|max:100',
            'deposit_return_notes' => 'nullable|string',
            'deductions' => 'required_if:deposit_return_action,partial|array',
            'deductions.*.type' => 'required_with:deductions|string',
            'deductions.*.amount' => 'required_with:deductions|numeric|min:0',
            'deductions.*.reason' => 'nullable|string',
        ]);

        // Check if rental was already returned
        if ($rental->return_date !== null) {
            return response()->json([
                'message' => 'This rental has already been returned.',
            ], 422);
        }

        DB::beginTransaction();
        try {
            $availableInventoryStatus = $this->findInventoryStatusByName('available');
            $rentedInventoryStatus = $this->findInventoryStatusByName('rented');
            if (! $availableInventoryStatus || ! $rentedInventoryStatus) {
                return response()->json([
                    'message' => 'Required inventory statuses (available/rented) are missing.',
                ], 500);
            }

            // Update rental with return information
            $rental->update([
                'return_date' => $request->input('return_date'),
                'returned_to' => auth()->id(),
                'return_notes' => $request->input('return_notes'),
            ]);

            // Calculate and create final penalty if returned late
            $this->createOrUpdatePenaltyInvoice($rental);

            $finalInvoice = $this->upsertFinalRentalInvoice($rental, auth()->id());

            if ($request->boolean('collect_rental_payment', false)) {
                $paymentAmount = $request->filled('rental_payment_amount')
                    ? (float) $request->input('rental_payment_amount')
                    : (float) $finalInvoice->balance_due;

                if ($paymentAmount > 0) {
                    $this->createInvoicePayment(
                        $finalInvoice,
                        $paymentAmount,
                        $request->input('rental_payment_method'),
                        auth()->id(),
                        $request->input('rental_payment_notes')
                    );
                }
            }

            $depositReturnAction = $request->input('deposit_return_action', 'hold');
            if ($depositReturnAction === 'full') {
                $this->depositService->returnFullDeposit(
                    $rental,
                    $request->input('deposit_return_method'),
                    auth()->id(),
                    $request->input('deposit_return_reference')
                );
            } elseif ($depositReturnAction === 'partial') {
                $this->depositService->returnPartialDeposit(
                    $rental,
                    $request->input('deductions', []),
                    $request->input('deposit_return_method'),
                    auth()->id(),
                    null,
                    $request->input('deposit_return_reference')
                );
            } elseif ($depositReturnAction === 'forfeit') {
                $this->depositService->forfeitDeposit(
                    $rental,
                    $request->input('deposit_return_notes', 'Deposit forfeited on return processing.'),
                    auth()->id()
                );
            }

            // Update rental status to returned
            $returnedStatus = $this->findRentalStatusByName('returned');
            if ($returnedStatus) {
                $rental->update(['status_id' => $returnedStatus->status_id]);
            }

            $reservationItemId = null;
            if ($rental->reservation_id) {
                $allocation = ReservationItemAllocation::where('item_id', $rental->item_id)
                    ->whereHas('reservationItem', function ($query) use ($rental) {
                        $query->where('reservation_id', $rental->reservation_id);
                    })
                    ->whereIn('allocation_status', ['allocated', 'released'])
                    ->orderByDesc('id')
                    ->first();

                if ($allocation) {
                    $allocation->update([
                        'allocation_status' => 'returned',
                        'returned_at' => now(),
                        'updated_by' => auth()->id(),
                    ]);

                    $reservationItemId = $allocation->reservation_item_id;
                    if ($allocation->reservationItem) {
                        $this->syncReservationItemFulfillment($allocation->reservationItem);
                    }
                }
            }

            if ($rental->item) {
                $fromStatusId = $rental->item->status_id;
                $rental->item->update(['status_id' => $availableInventoryStatus->status_id]);

                $returnNotes = $request->input('return_notes');
                if ($request->filled('condition_notes')) {
                    $returnNotes = trim(($returnNotes ? $returnNotes.' | ' : '').'Condition: '.$request->input('condition_notes'));
                }

                $this->createInventoryMovement(
                    $rental->item,
                    'return',
                    $fromStatusId ?: $rentedInventoryStatus->status_id,
                    $availableInventoryStatus->status_id,
                    [
                        'reservation_id' => $rental->reservation_id,
                        'reservation_item_id' => $reservationItemId,
                        'rental_id' => $rental->rental_id,
                        'notes' => $returnNotes,
                    ]
                );
            }

            DB::commit();

            $rental->load(['customer', 'item', 'status', 'invoices.invoiceItems']);

            return response()->json([
                'message' => 'Rental return processed successfully',
                'data' => $rental,
                'penalty_charged' => $this->calculatePenalty($rental),
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Failed to process return',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * BUSINESS ACTIVITY: System updates rental status
     * Extend rental period
     */
    public function extendRental(Rental $rental, Request $request): JsonResponse
    {
        $request->validate([
            'new_due_date' => 'required|date|after:due_date',
            'extension_reason' => 'nullable|string',
        ]);

        // Check if rental was already returned
        if ($rental->return_date !== null) {
            return response()->json([
                'message' => 'Cannot extend a rental that has already been returned.',
            ], 422);
        }

        // Check if rental is overdue
        if (Carbon::parse($rental->due_date)->lessThan(Carbon::now())) {
            return response()->json([
                'message' => 'Cannot extend an overdue rental. Please settle penalties first.',
            ], 422);
        }

        DB::beginTransaction();
        try {
            $rental->update([
                'due_date' => $request->new_due_date,
                'extension_count' => $rental->extension_count + 1,
                'extended_by' => auth()->id(),
                'last_extended_at' => now(),
                'extension_reason' => $request->extension_reason,
            ]);

            DB::commit();

            $rental->load(['customer', 'item', 'status', 'extendedBy']);

            return response()->json([
                'message' => 'Rental extended successfully',
                'data' => $rental,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Failed to extend rental',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * BULK OPERATION: Extend multiple rentals at once
     */
    public function bulkExtend(Request $request): JsonResponse
    {
        $request->validate([
            'rental_ids' => 'required|array|min:1',
            'rental_ids.*' => 'exists:rentals,rental_id',
            'days' => 'required|integer|min:1|max:30',
            'reason' => 'required|string|max:500',
        ]);

        $rentalIds = $request->input('rental_ids');
        $days = $request->input('days');
        $reason = $request->input('reason');

        $results = [
            'success' => [],
            'failed' => [],
        ];

        DB::beginTransaction();
        try {
            $rentals = Rental::whereIn('rental_id', $rentalIds)
                ->whereNull('return_date')
                ->get();

            foreach ($rentals as $rental) {
                // Skip if rental is overdue
                if (Carbon::parse($rental->due_date)->lessThan(Carbon::now())) {
                    $results['failed'][] = [
                        'rental_id' => $rental->rental_id,
                        'reason' => 'Rental is overdue. Settle penalties first.',
                    ];

                    continue;
                }

                // Calculate new due date
                $newDueDate = Carbon::parse($rental->due_date)->addDays($days);

                $rental->update([
                    'due_date' => $newDueDate,
                    'extension_count' => $rental->extension_count + 1,
                    'extended_by' => auth()->id(),
                    'last_extended_at' => now(),
                    'extension_reason' => $reason,
                ]);

                $results['success'][] = $rental->rental_id;
            }

            // Check for already returned rentals
            $returnedRentals = Rental::whereIn('rental_id', $rentalIds)
                ->whereNotNull('return_date')
                ->pluck('rental_id');

            foreach ($returnedRentals as $rentalId) {
                $results['failed'][] = [
                    'rental_id' => $rentalId,
                    'reason' => 'Rental has already been returned.',
                ];
            }

            DB::commit();

            $successCount = count($results['success']);
            $failedCount = count($results['failed']);

            return response()->json([
                'message' => "Extended {$successCount} rental(s) by {$days} days.".($failedCount > 0 ? " {$failedCount} failed." : ''),
                'results' => $results,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Failed to extend rentals',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * BULK OPERATION: Process returns for multiple rentals at once
     */
    public function bulkReturn(Request $request): JsonResponse
    {
        $request->validate([
            'rental_ids' => 'required|array|min:1',
            'rental_ids.*' => 'exists:rentals,rental_id',
            'return_date' => 'required|date',
            'return_notes' => 'nullable|string|max:1000',
        ]);

        $rentalIds = $request->input('rental_ids');
        $returnDate = $request->input('return_date');
        $returnNotes = $request->input('return_notes', '');

        $results = [
            'success' => [],
            'failed' => [],
        ];

        $availableInventoryStatus = $this->findInventoryStatusByName('available');
        $returnedStatus = $this->findRentalStatusByName('returned');

        if (! $availableInventoryStatus || ! $returnedStatus) {
            return response()->json([
                'message' => 'Required statuses (available/returned) are missing from the system.',
            ], 500);
        }

        DB::beginTransaction();
        try {
            $rentals = Rental::whereIn('rental_id', $rentalIds)
                ->whereNull('return_date')
                ->with(['item'])
                ->get();

            foreach ($rentals as $rental) {
                try {
                    // Update rental with return information
                    $rental->update([
                        'return_date' => $returnDate,
                        'returned_to' => auth()->id(),
                        'return_notes' => $returnNotes ? "[Bulk Return] {$returnNotes}" : '[Bulk Return]',
                        'status_id' => $returnedStatus->status_id,
                    ]);

                    // Calculate and create penalty if overdue
                    $this->createOrUpdatePenaltyInvoice($rental);

                    // Update inventory status back to available
                    if ($rental->item) {
                        $fromStatusId = $rental->item->status_id;
                        $rental->item->update(['status_id' => $availableInventoryStatus->status_id]);

                        // Create inventory movement record
                        $this->createInventoryMovement(
                            $rental->item,
                            'return',
                            $fromStatusId,
                            $availableInventoryStatus->status_id,
                            [
                                'rental_id' => $rental->rental_id,
                                'notes' => 'Bulk return processed',
                            ]
                        );
                    }

                    $results['success'][] = $rental->rental_id;

                } catch (\Exception $e) {
                    $results['failed'][] = [
                        'rental_id' => $rental->rental_id,
                        'reason' => $e->getMessage(),
                    ];
                }
            }

            // Check for already returned rentals
            $alreadyReturnedRentals = Rental::whereIn('rental_id', $rentalIds)
                ->whereNotNull('return_date')
                ->pluck('rental_id');

            foreach ($alreadyReturnedRentals as $rentalId) {
                $results['failed'][] = [
                    'rental_id' => $rentalId,
                    'reason' => 'Rental has already been returned.',
                ];
            }

            DB::commit();

            $successCount = count($results['success']);
            $failedCount = count($results['failed']);

            return response()->json([
                'message' => "Processed {$successCount} return(s).".($failedCount > 0 ? " {$failedCount} failed." : ''),
                'results' => $results,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Failed to process returns',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Cancel a rental
     */
    public function cancel(Rental $rental): JsonResponse
    {
        // Check if rental was already returned
        if ($rental->return_date !== null) {
            return response()->json([
                'message' => 'Cannot cancel a rental that has already been returned.',
            ], 422);
        }

        // Check if rental has invoices that are already paid
        $paidInvoices = $rental->invoices()->where('payment_status', 'paid')->count();
        if ($paidInvoices > 0) {
            return response()->json([
                'message' => 'Cannot cancel rental. It has paid invoices associated with it.',
            ], 422);
        }

        DB::beginTransaction();
        try {
            // Update the rental status to cancelled
            $cancelledStatus = $this->findRentalStatusByName('cancelled');
            if (! $cancelledStatus) {
                return response()->json([
                    'message' => 'Cancelled status not found in the system.',
                ], 500);
            }

            $rental->update([
                'status_id' => $cancelledStatus->status_id,
                'return_notes' => 'Rental cancelled on '.now()->format('Y-m-d H:i:s'),
            ]);

            // If there's a reservation, update its status too
            if ($rental->reservation) {
                $reservationCancelledStatus = $this->findReservationStatusByName('cancelled');
                $rental->reservation->update([
                    'status_id' => $reservationCancelledStatus?->status_id ?? $rental->reservation->status_id,
                ]);
            }

            // Update item availability back to available
            if ($rental->item) {
                $availableInventoryStatus = $this->findInventoryStatusByName('available');
                if ($availableInventoryStatus) {
                    $rental->item->update(['status_id' => $availableInventoryStatus->status_id]);
                }
            }

            DB::commit();

            $rental->load(['customer', 'item', 'status', 'reservation', 'releasedBy']);

            return response()->json([
                'message' => 'Rental cancelled successfully',
                'data' => $rental,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Failed to cancel rental',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * BUSINESS ACTIVITY: System automatically identifies overdue rentals
     * Does overdue check for each rental
     */
    public function checkOverdue(Rental $rental): void
    {
        // Check if rental hasn't been returned
        if ($rental->return_date === null) {
            $now = Carbon::now();
            $dueDate = Carbon::parse($rental->due_date);

            // if Rental is overdue
            if ($now->greaterThan($dueDate)) {
                $overdueStatus = RentalStatus::where('status_name', 'Overdue')->first();

                if ($overdueStatus && $rental->status_id !== $overdueStatus->status_id) {
                    $rental->update([
                        'status_id' => $overdueStatus->status_id,
                    ]);
                }

                // Calculate and create/update penalty invoice
                $this->createOrUpdatePenaltyInvoice($rental);
            }
        }
    }

    /**
     * BUSINESS ACTIVITY: System calculates penalties for late returns
     * Calculates penalties for late returns (A helper function)
     * Uses configurable penalty rates from rental_settings table
     */
    public function calculatePenalty(Rental $rental): float
    {
        // Get configurable penalty settings
        $penaltyPerDay = RentalSetting::getPenaltyRate();
        $gracePeriodHours = RentalSetting::getGracePeriodHours();
        $maxPenaltyDays = RentalSetting::getMaxPenaltyDays();

        if ($rental->return_date !== null) {
            $returnDate = Carbon::parse($rental->return_date);
            $dueDate = Carbon::parse($rental->due_date);

            // Apply grace period
            $dueWithGrace = $dueDate->copy()->addHours($gracePeriodHours);

            // If returned within grace period, no penalty
            if ($returnDate->lessThanOrEqualTo($dueWithGrace)) {
                return 0;
            }

            // Calculate days late (from original due date, not grace period)
            $daysLate = $returnDate->diffInDays($dueDate);
        } else {
            // Rental is still active
            $now = Carbon::now();
            $dueDate = Carbon::parse($rental->due_date);

            // Apply grace period
            $dueWithGrace = $dueDate->copy()->addHours($gracePeriodHours);

            if ($now->lessThanOrEqualTo($dueWithGrace)) {
                return 0;
            }

            $daysLate = $now->diffInDays($dueDate);
        }

        // Apply maximum penalty days cap if configured
        if ($maxPenaltyDays > 0 && $daysLate > $maxPenaltyDays) {
            $daysLate = $maxPenaltyDays;
        }

        $penalty = $daysLate * $penaltyPerDay;

        return $penalty;
    }

    /**
     * Create or update penalty invoice for an overdue rental
     * Stores the penalty in the invoice_items table
     */
    private function createOrUpdatePenaltyInvoice(Rental $rental): void
    {
        $penaltyAmount = $this->calculatePenalty($rental);

        if ($penaltyAmount <= 0) {
            return; // No penalty to add
        }

        DB::transaction(function () use ($rental, $penaltyAmount) {
            // Find or create an invoice for this rental
            $invoice = Invoice::firstOrCreate(
                [
                    'rental_id' => $rental->rental_id,
                    'invoice_type' => 'penalty',
                ],
                [
                    'customer_id' => $rental->customer_id,
                    'invoice_date' => now(),
                    'due_date' => now()->addDays(7),
                    'subtotal' => 0,
                    'tax_amount' => 0,
                    'total_amount' => 0,
                    'payment_status' => 'unpaid',
                    'notes' => 'Late return penalty invoice',
                ]
            );

            // Calculate days late for description
            $dueDate = Carbon::parse($rental->due_date);
            $currentDate = $rental->return_date ? Carbon::parse($rental->return_date) : Carbon::now();
            $daysLate = $currentDate->diffInDays($dueDate);

            // Check if penalty item already exists
            $existingPenaltyItem = InvoiceItem::where('invoice_id', $invoice->invoice_id)
                ->where('item_type', 'late_fee')
                ->first();

            if ($existingPenaltyItem) {
                // Update existing penalty
                $existingPenaltyItem->update([
                    'description' => "Late return penalty ({$daysLate} days @ ₱50/day)",
                    'quantity' => $daysLate,
                    'unit_price' => 50.00,
                    'total_price' => $penaltyAmount,
                ]);
            } else {
                // Create new penalty item
                InvoiceItem::create([
                    'invoice_id' => $invoice->invoice_id,
                    'description' => "Late return penalty ({$daysLate} days @ ₱50/day)",
                    'item_type' => 'late_fee',
                    'item_id' => $rental->item_id,
                    'quantity' => $daysLate,
                    'unit_price' => 50.00,
                    'total_price' => $penaltyAmount,
                ]);
            }

            // Recalculate invoice totals
            $invoice->refresh();
            $totalAmount = $invoice->invoiceItems()->sum('total_price');
            $invoice->update([
                'subtotal' => $totalAmount,
                'total_amount' => $totalAmount,
            ]);
        });
    }

    /**
     * BUSINESS ACTIVITY: System automatically identifies overdue rentals
     * Checks all the rentals and identifies overdue rentals (Batch process)
     */
    public function batchCheckOverdue(): JsonResponse
    {
        $activeRentals = Rental::whereNull('return_date')->get();
        $overDueCount = 0;
        $penaltiesCreated = 0;

        foreach ($activeRentals as $rental) {
            $wasOverdue = Carbon::parse($rental->due_date)->lessThan(Carbon::now());

            $this->checkOverdue($rental);

            if ($wasOverdue) {
                $overDueCount++;
                $penaltiesCreated++;
            }
        }

        return response()->json([
            'message' => 'Overdue check completed',
            'checked' => $activeRentals->count(),
            'overdue' => $overDueCount,
            'penalties_created_or_updated' => $penaltiesCreated,
        ]);
    }

    /**
     * BUSINESS ACTIVITY: Admin/clerk reviews rental history
     * Get rental history for a specific customer
     */
    public function customerHistory(Request $request, $customerId): JsonResponse
    {
        $query = Rental::with(['item', 'status', 'releasedBy', 'returnedTo', 'invoices'])
            ->where('customer_id', $customerId);

        // Additional filters
        if ($request->has('status_id')) {
            $query->where('status_id', $request->get('status_id'));
        }

        if ($request->has('date_from')) {
            $query->where('released_date', '>=', $request->get('date_from'));
        }

        if ($request->has('date_to')) {
            $query->where('released_date', '<=', $request->get('date_to'));
        }

        $rentals = $query->orderBy('released_date', 'desc')->get();

        // Calculate customer statistics
        $statistics = [
            'total_rentals' => $rentals->count(),
            'active_rentals' => $rentals->whereNull('return_date')->count(),
            'completed_rentals' => $rentals->whereNotNull('return_date')->count(),
            'overdue_count' => $rentals->where('return_date', null)->filter(function ($rental) {
                return Carbon::parse($rental->due_date)->lessThan(Carbon::now());
            })->count(),
            'total_penalties' => $this->getTotalPenaltiesFromInvoices($rentals),
        ];

        return response()->json([
            'customer_id' => $customerId,
            'statistics' => $statistics,
            'rentals' => $rentals,
        ]);
    }

    /**
     * BUSINESS ACTIVITY: Admin/clerk reviews rental history
     * Get rental history for a specific item
     */
    public function itemHistory(Request $request, $itemId): JsonResponse
    {
        $query = Rental::with(['customer', 'status', 'releasedBy', 'returnedTo', 'invoices'])
            ->where('item_id', $itemId);

        // Additional filters
        if ($request->has('date_from')) {
            $query->where('released_date', '>=', $request->get('date_from'));
        }

        if ($request->has('date_to')) {
            $query->where('released_date', '<=', $request->get('date_to'));
        }

        $rentals = $query->orderBy('released_date', 'desc')->get();

        // Calculate item statistics
        $statistics = [
            'total_rentals' => $rentals->count(),
            'current_status' => $rentals->whereNull('return_date')->first() ? 'Rented' : 'Available',
            'times_rented' => $rentals->whereNotNull('return_date')->count(),
            'times_overdue' => $rentals->filter(function ($rental) {
                return $rental->return_date !== null &&
                    Carbon::parse($rental->return_date)->greaterThan(Carbon::parse($rental->due_date));
            })->count(),
        ];

        return response()->json([
            'item_id' => $itemId,
            'statistics' => $statistics,
            'rentals' => $rentals,
        ]);
    }

    /**
     * Get all overdue rentals
     */
    public function getOverdueRentals(Request $request): JsonResponse
    {
        $query = Rental::with(['customer', 'item', 'status', 'releasedBy'])
            ->whereNull('return_date')
            ->where('due_date', '<', Carbon::now());

        $perPage = $request->get('per_page', 15);
        $overdueRentals = $query->orderBy('due_date', 'asc')->paginate($perPage);

        // Calculate total penalty for all overdue rentals
        $totalPendingPenalties = 0;
        foreach ($overdueRentals as $rental) {
            $totalPendingPenalties += $this->calculatePenalty($rental);
        }

        return response()->json([
            'overdue_rentals' => $overdueRentals,
            'total_pending_penalties' => $totalPendingPenalties,
        ]);
    }

    public function getDepositSummary(Rental $rental): JsonResponse
    {
        $rental->load(['customer', 'item', 'depositReturns.processedBy']);

        return response()->json([
            'data' => [
                'rental_id' => $rental->rental_id,
                'customer' => $rental->customer,
                'item' => $rental->item,
                'deposit' => $this->depositService->getDepositSummary($rental),
                'returns' => $rental->depositReturns,
            ],
        ]);
    }

    public function processDepositReturn(Rental $rental, Request $request): JsonResponse
    {
        $request->validate([
            'action' => 'required|in:full,partial,forfeit',
            'return_method' => 'required_if:action,full,partial|in:cash,bank_transfer,gcash,paymaya,check',
            'reference' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
            'deductions' => 'required_if:action,partial|array|min:1',
            'deductions.*.type' => 'required_with:deductions|string',
            'deductions.*.amount' => 'required_with:deductions|numeric|min:0',
            'deductions.*.reason' => 'nullable|string',
        ]);

        try {
            if ($request->action === 'full') {
                $result = $this->depositService->returnFullDeposit(
                    $rental,
                    $request->return_method,
                    auth()->id(),
                    $request->reference
                );
            } elseif ($request->action === 'partial') {
                $result = $this->depositService->returnPartialDeposit(
                    $rental,
                    $request->deductions,
                    $request->return_method,
                    auth()->id(),
                    null,
                    $request->reference
                );
            } else {
                $result = $this->depositService->forfeitDeposit(
                    $rental,
                    $request->notes ?? 'Deposit forfeited by staff action',
                    auth()->id()
                );
            }

            return response()->json([
                'message' => 'Deposit action processed successfully',
                'data' => $result,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Failed to process deposit action',
                'error' => $e->getMessage(),
            ], 422);
        }
    }

    private function findRentalStatusByName(string $name): ?RentalStatus
    {
        return RentalStatus::whereRaw('LOWER(status_name) = ?', [strtolower($name)])->first();
    }

    private function findInventoryStatusByName(string $name): ?InventoryStatus
    {
        return InventoryStatus::whereRaw('LOWER(status_name) = ?', [strtolower($name)])->first();
    }

    private function findReservationStatusByName(string $name): ?ReservationStatus
    {
        return ReservationStatus::whereRaw('LOWER(status_name) = ?', [strtolower($name)])->first();
    }

    private function findPaymentStatusByName(string $name): ?PaymentStatus
    {
        return PaymentStatus::whereRaw('LOWER(status_name) = ?', [strtolower($name)])->first();
    }

    private function syncReservationItemFulfillment(ReservationItem $reservationItem): void
    {
        $releasedCount = $reservationItem->allocations()
            ->whereIn('allocation_status', ['released', 'returned'])
            ->count();

        $newStatus = $releasedCount >= (int) $reservationItem->quantity
            ? 'fulfilled'
            : 'pending';

        if ($reservationItem->fulfillment_status !== $newStatus) {
            $reservationItem->update(['fulfillment_status' => $newStatus]);
        }
    }

    private function createInventoryMovement(
        Inventory $item,
        string $movementType,
        ?int $fromStatusId,
        ?int $toStatusId,
        array $context = []
    ): void {
        InventoryMovement::create([
            'item_id' => $item->item_id,
            'variant_id' => $item->variant_id,
            'reservation_id' => $context['reservation_id'] ?? null,
            'reservation_item_id' => $context['reservation_item_id'] ?? null,
            'rental_id' => $context['rental_id'] ?? null,
            'movement_type' => $movementType,
            'quantity' => 1,
            'from_status_id' => $fromStatusId,
            'to_status_id' => $toStatusId,
            'performed_by' => auth()->id(),
            'notes' => $context['notes'] ?? null,
        ]);
    }

    private function upsertFinalRentalInvoice(Rental $rental, int $createdBy): Invoice
    {
        $pendingPaymentStatus = $this->findPaymentStatusByName('pending');
        if (! $pendingPaymentStatus) {
            throw new \RuntimeException('Pending payment status not found.');
        }

        $invoice = Invoice::firstOrCreate(
            [
                'rental_id' => $rental->rental_id,
                'invoice_type' => 'final',
            ],
            [
                'invoice_number' => $this->generateInvoiceNumber('INV-FINAL'),
                'customer_id' => $rental->customer_id,
                'reservation_id' => $rental->reservation_id,
                'subtotal' => 0,
                'discount' => 0,
                'tax' => 0,
                'total_amount' => 0,
                'amount_paid' => 0,
                'balance_due' => 0,
                'invoice_date' => now(),
                'due_date' => now(),
                'created_by' => $createdBy,
                'status_id' => $pendingPaymentStatus->status_id,
            ]
        );

        $rentalDays = max(1, Carbon::parse($rental->released_date)->diffInDays(Carbon::parse($rental->return_date ?: now())) + 1);
        $rentalFee = round((float) $rental->item?->rental_price * $rentalDays, 2);

        $rentalFeeLine = $invoice->invoiceItems()->where('item_type', 'rental_fee')->first();
        if ($rentalFeeLine) {
            $rentalFeeLine->update([
                'description' => "Rental fee ({$rentalDays} day/s)",
                'item_id' => $rental->item_id,
                'quantity' => $rentalDays,
                'unit_price' => (float) $rental->item?->rental_price,
                'total_price' => $rentalFee,
            ]);
        } else {
            $invoice->invoiceItems()->create([
                'description' => "Rental fee ({$rentalDays} day/s)",
                'item_type' => 'rental_fee',
                'item_id' => $rental->item_id,
                'quantity' => $rentalDays,
                'unit_price' => (float) $rental->item?->rental_price,
                'total_price' => $rentalFee,
            ]);
        }

        $penalty = $this->calculatePenalty($rental);
        $lateFeeLine = $invoice->invoiceItems()->where('item_type', 'late_fee')->first();
        if ($penalty > 0) {
            if ($lateFeeLine) {
                $lateFeeLine->update([
                    'description' => 'Late return penalty',
                    'item_id' => $rental->item_id,
                    'quantity' => 1,
                    'unit_price' => $penalty,
                    'total_price' => $penalty,
                ]);
            } else {
                $invoice->invoiceItems()->create([
                    'description' => 'Late return penalty',
                    'item_type' => 'late_fee',
                    'item_id' => $rental->item_id,
                    'quantity' => 1,
                    'unit_price' => $penalty,
                    'total_price' => $penalty,
                ]);
            }
        } elseif ($lateFeeLine) {
            $lateFeeLine->delete();
        }

        $this->recalculateInvoiceTotals($invoice);

        return $invoice->fresh(['invoiceItems', 'payments']);
    }

    private function createInvoicePayment(
        Invoice $invoice,
        float $amount,
        string $paymentMethod,
        int $processedBy,
        ?string $notes = null
    ): Payment {
        $paidStatus = $this->findPaymentStatusByName('paid');
        if (! $paidStatus) {
            throw new \RuntimeException('Paid payment status not found.');
        }

        $payment = Payment::create([
            'invoice_id' => $invoice->invoice_id,
            'payment_reference' => $this->generateInvoiceNumber('PAY'),
            'amount' => $amount,
            'payment_method' => $paymentMethod,
            'payment_date' => now(),
            'notes' => $notes,
            'processed_by' => $processedBy,
            'status_id' => $paidStatus->status_id,
        ]);

        $this->recalculateInvoiceTotals($invoice);

        return $payment;
    }

    private function recalculateInvoiceTotals(Invoice $invoice): void
    {
        $subtotal = (float) $invoice->invoiceItems()->sum('total_price');
        $discount = (float) $invoice->discount;
        $tax = (float) $invoice->tax;
        $total = max(($subtotal + $tax) - $discount, 0);

        $paidStatus = $this->findPaymentStatusByName('paid');
        $amountPaid = 0;
        if ($paidStatus) {
            $amountPaid = (float) $invoice->payments()->where('status_id', $paidStatus->status_id)->sum('amount');
        }

        $invoice->update([
            'subtotal' => $subtotal,
            'total_amount' => $total,
            'amount_paid' => min($amountPaid, $total),
            'balance_due' => max($total - $amountPaid, 0),
        ]);
    }

    private function generateInvoiceNumber(string $prefix): string
    {
        return $prefix.'-'.now()->format('YmdHis').'-'.strtoupper(substr(bin2hex(random_bytes(3)), 0, 6));
    }

    // ============================================
    // RENTAL SETTINGS MANAGEMENT
    // ============================================

    /**
     * Get all rental settings grouped by category
     */
    public function getSettings(): JsonResponse
    {
        try {
            $settings = RentalSetting::all()->map(function ($setting) {
                return [
                    'setting_id' => $setting->setting_id,
                    'setting_key' => $setting->setting_key,
                    'setting_value' => $this->castSettingValue($setting->setting_value, $setting->setting_type),
                    'setting_type' => $setting->setting_type,
                    'setting_group' => $setting->setting_group,
                    'description' => $setting->description,
                ];
            });

            $grouped = $settings->groupBy('setting_group');

            return response()->json([
                'success' => true,
                'data' => [
                    'settings' => $settings,
                    'grouped' => $grouped,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load rental settings',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update rental settings
     */
    public function updateSettings(Request $request): JsonResponse
    {
        try {
            $settings = $request->input('settings', []);

            DB::transaction(function () use ($settings) {
                foreach ($settings as $key => $value) {
                    RentalSetting::setValue($key, $value);
                }
            });

            // Clear the settings cache
            RentalSetting::clearCache();

            return response()->json([
                'success' => true,
                'message' => 'Rental settings updated successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update rental settings',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Cast setting value to its appropriate type
     */
    private function castSettingValue(string $value, string $type): mixed
    {
        return match ($type) {
            'integer' => (int) $value,
            'decimal' => (float) $value,
            'boolean' => (bool) (int) $value,
            'json' => json_decode($value, true),
            default => $value,
        };
    }

    /**
     * Display the rental calendar page
     */
    public function showCalendarPage(): View
    {
        return view('rentals.calendar');
    }

    /**
     * Get calendar events for rentals
     */
    public function getCalendarEvents(Request $request): JsonResponse
    {
        try {
            $startDate = $request->input('start') ? Carbon::parse($request->input('start')) : Carbon::now()->startOfMonth()->subMonth();
            $endDate = $request->input('end') ? Carbon::parse($request->input('end')) : Carbon::now()->endOfMonth()->addMonth();

            // Get rentals within the date range (either released, due, or returned during this period)
            $rentals = Rental::with(['customer', 'item', 'status'])
                ->where(function ($query) use ($startDate, $endDate) {
                    // Released during period
                    $query->whereBetween('released_date', [$startDate, $endDate])
                        // OR due during period
                        ->orWhereBetween('due_date', [$startDate, $endDate])
                        // OR returned during period
                        ->orWhereBetween('return_date', [$startDate, $endDate]);
                })
                ->get();

            $events = [];
            $today = Carbon::now()->startOfDay();

            foreach ($rentals as $rental) {
                // Due date event (primary event)
                $dueDate = Carbon::parse($rental->due_date);
                $isOverdue = $rental->return_date === null && $dueDate->lt($today);
                $isReturned = $rental->return_date !== null;

                // Determine event type
                $eventType = 'due';
                $eventClass = 'fc-event-due';

                if ($isReturned) {
                    $eventType = 'returned';
                    $eventClass = 'fc-event-returned';
                } elseif ($isOverdue) {
                    $eventType = 'overdue';
                    $eventClass = 'fc-event-overdue';
                }

                // Add due date event
                $events[] = [
                    'id' => 'due-'.$rental->rental_id,
                    'title' => ($rental->customer->first_name ?? 'Unknown').' - '.($rental->item->item_name ?? 'Unknown'),
                    'start' => $rental->due_date,
                    'allDay' => true,
                    'className' => $eventClass,
                    'extendedProps' => [
                        'rentalId' => $rental->rental_id,
                        'customerName' => $rental->customer ? ($rental->customer->first_name.' '.$rental->customer->last_name) : 'Unknown',
                        'itemName' => $rental->item->item_name ?? 'Unknown',
                        'itemCode' => $rental->item->item_code ?? '',
                        'releasedDate' => $rental->released_date,
                        'dueDate' => $rental->due_date,
                        'returnDate' => $rental->return_date,
                        'eventType' => $eventType,
                    ],
                ];

                // Optionally add released date event (to show rental start)
                if ($rental->released_date) {
                    $releasedDate = Carbon::parse($rental->released_date);
                    if ($releasedDate->between($startDate, $endDate)) {
                        $events[] = [
                            'id' => 'released-'.$rental->rental_id,
                            'title' => '(Start) '.($rental->customer->first_name ?? 'Unknown'),
                            'start' => $rental->released_date,
                            'allDay' => true,
                            'className' => 'fc-event-released',
                            'extendedProps' => [
                                'rentalId' => $rental->rental_id,
                                'customerName' => $rental->customer ? ($rental->customer->first_name.' '.$rental->customer->last_name) : 'Unknown',
                                'itemName' => $rental->item->item_name ?? 'Unknown',
                                'itemCode' => $rental->item->item_code ?? '',
                                'releasedDate' => $rental->released_date,
                                'dueDate' => $rental->due_date,
                                'returnDate' => $rental->return_date,
                                'eventType' => 'released',
                            ],
                        ];
                    }
                }
            }

            // Calculate stats
            $stats = $this->getCalendarStats();

            return response()->json([
                'success' => true,
                'events' => $events,
                'stats' => $stats,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load calendar events',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get calendar statistics
     */
    private function getCalendarStats(): array
    {
        $today = Carbon::now()->startOfDay();
        $endOfWeek = Carbon::now()->endOfWeek();

        // Active rentals (not returned)
        $activeRentals = Rental::whereNull('return_date')->count();

        // Due today
        $dueToday = Rental::whereNull('return_date')
            ->whereDate('due_date', $today)
            ->count();

        // Due this week (remaining days including today)
        $dueThisWeek = Rental::whereNull('return_date')
            ->whereBetween('due_date', [$today, $endOfWeek])
            ->count();

        // Overdue (past due and not returned)
        $overdue = Rental::whereNull('return_date')
            ->where('due_date', '<', $today)
            ->count();

        return [
            'active' => $activeRentals,
            'due_today' => $dueToday,
            'due_this_week' => $dueThisWeek,
            'overdue' => $overdue,
        ];
    }

    // =========================================================================
    // NOTIFICATION METHODS
    // =========================================================================

    /**
     * Get notifications with optional filtering
     */
    public function getNotifications(Request $request): JsonResponse
    {
        try {
            $query = RentalNotification::with(['rental.customer', 'rental.item'])
                ->active()
                ->orderBy('created_at', 'desc');

            // Filter by read status
            if ($request->has('unread_only') && $request->boolean('unread_only')) {
                $query->unread();
            }

            // Filter by type
            if ($request->has('type')) {
                $query->ofType($request->input('type'));
            }

            // Filter by priority
            if ($request->has('priority')) {
                $query->withPriority($request->input('priority'));
            }

            // Pagination
            $perPage = $request->input('per_page', 20);
            $notifications = $query->paginate($perPage);

            // Get counts
            $counts = [
                'total' => RentalNotification::active()->count(),
                'unread' => RentalNotification::active()->unread()->count(),
                'overdue' => RentalNotification::active()->unread()->ofType(RentalNotification::TYPE_OVERDUE_ALERT)->count(),
                'due_reminders' => RentalNotification::active()->unread()->ofType(RentalNotification::TYPE_DUE_REMINDER)->count(),
            ];

            return response()->json([
                'success' => true,
                'notifications' => $notifications,
                'counts' => $counts,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load notifications',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Mark a notification as read
     */
    public function markNotificationRead(int $notificationId): JsonResponse
    {
        try {
            $notification = RentalNotification::findOrFail($notificationId);
            $notification->markAsRead();

            return response()->json([
                'success' => true,
                'message' => 'Notification marked as read',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to mark notification as read',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Dismiss a notification
     */
    public function dismissNotification(int $notificationId): JsonResponse
    {
        try {
            $notification = RentalNotification::findOrFail($notificationId);
            $notification->dismiss();

            return response()->json([
                'success' => true,
                'message' => 'Notification dismissed',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to dismiss notification',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Mark all notifications as read
     */
    public function markAllNotificationsRead(): JsonResponse
    {
        try {
            RentalNotification::active()
                ->unread()
                ->update([
                    'is_read' => true,
                    'read_at' => now(),
                ]);

            return response()->json([
                'success' => true,
                'message' => 'All notifications marked as read',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to mark all notifications as read',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Dismiss all notifications
     */
    public function dismissAllNotifications(): JsonResponse
    {
        try {
            RentalNotification::active()
                ->update([
                    'is_dismissed' => true,
                    'dismissed_at' => now(),
                ]);

            return response()->json([
                'success' => true,
                'message' => 'All notifications dismissed',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to dismiss all notifications',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get notification count for header badge
     */
    public function getNotificationCount(): JsonResponse
    {
        try {
            $unreadCount = RentalNotification::active()->unread()->count();
            $urgentCount = RentalNotification::active()
                ->unread()
                ->where('priority', RentalNotification::PRIORITY_URGENT)
                ->count();

            return response()->json([
                'success' => true,
                'unread_count' => $unreadCount,
                'urgent_count' => $urgentCount,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'unread_count' => 0,
                'urgent_count' => 0,
            ]);
        }
    }

    /**
     * Manually trigger notification check (for testing or manual refresh)
     */
    public function checkNotifications(): JsonResponse
    {
        try {
            $reminderDays = (int) RentalSetting::getValue('notification_due_days_before', 2);
            $overdueEnabled = (bool) RentalSetting::getValue('notification_overdue_enabled', true);
            $penaltyRate = (float) RentalSetting::getValue('penalty_rate_per_day', 50);

            $today = Carbon::now()->startOfDay();
            $reminderDate = $today->copy()->addDays($reminderDays);
            $created = 0;

            // Check for due reminders
            $dueRentals = Rental::with(['customer', 'item'])
                ->whereNull('return_date')
                ->whereBetween('due_date', [$today, $reminderDate])
                ->get();

            foreach ($dueRentals as $rental) {
                $dueDate = Carbon::parse($rental->due_date)->startOfDay();
                $daysUntilDue = $today->diffInDays($dueDate, false);

                if ($daysUntilDue > 0 && ! RentalNotification::existsForRentalToday($rental->rental_id, RentalNotification::TYPE_DUE_REMINDER)) {
                    RentalNotification::createDueReminder($rental, $daysUntilDue);
                    $created++;
                }
            }

            // Check for overdue alerts
            if ($overdueEnabled) {
                $overdueRentals = Rental::with(['customer', 'item'])
                    ->whereNull('return_date')
                    ->where('due_date', '<', $today)
                    ->get();

                foreach ($overdueRentals as $rental) {
                    $dueDate = Carbon::parse($rental->due_date)->startOfDay();
                    $daysOverdue = $dueDate->diffInDays($today);

                    if (! RentalNotification::existsForRentalToday($rental->rental_id, RentalNotification::TYPE_OVERDUE_ALERT)) {
                        $penaltyAmount = $daysOverdue * $penaltyRate;
                        RentalNotification::createOverdueAlert($rental, $daysOverdue, $penaltyAmount);
                        $created++;
                    }
                }
            }

            return response()->json([
                'success' => true,
                'message' => "Created {$created} new notification(s)",
                'created_count' => $created,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to check notifications',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
