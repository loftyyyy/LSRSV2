<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Rental;
use App\Models\RentalStatus;
use App\Http\Requests\StoreRentalRequest;
use App\Http\Requests\UpdateRentalRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;


class RentalController extends Controller
{

    /**
     * Display Reports Page
     */
    public function report(Request $request): JsonResponse
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
            'generated_at' => now()->format('Y-m-d H:i:s')
        ]);

        return $pdf->download('rental-report-' . now()->format('Y-m-d') . '.pdf');
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
         $activeRentals = Rental::whereHas('status', fn($q) => $q->where('status_name', 'active'))->count();
         $completedRentals = Rental::whereHas('status', fn($q) => $q->where('status_name', 'returned'))->count();
         $cancelledRentals = Rental::whereHas('status', fn($q) => $q->where('status_name', 'cancelled'))->count();
         
         // Overdue & Late Returns
         $overdueRentals = Rental::where('return_date', '<', now())
             ->whereHas('status', fn($q) => $q->where('status_name', 'active'))
             ->count();
         
         $lateReturnRentals = Rental::where('return_date', '<', 'actual_return_date')
             ->whereHas('status', fn($q) => $q->where('status_name', 'returned'))
             ->count();

         // Duration Analysis
         $avgRentalDuration = Rental::selectRaw('AVG(DATEDIFF(DAY, rental_date, return_date)) as avg_days')
             ->first()?->avg_days ?? 0;

         // Revenue Analysis
         $totalRentalRevenue = Rental::whereHas('invoices')
             ->with('invoices')
             ->get()
             ->sum(fn($rental) => $rental->invoices->sum('total_amount')) ?? 0;

         $revenueThisMonth = Rental::where('created_at', '>=', now()->startOfMonth())
             ->whereHas('invoices')
             ->with('invoices')
             ->get()
             ->sum(fn($rental) => $rental->invoices->sum('total_amount')) ?? 0;

         // Rental Status Distribution
         $rentalStatusDistribution = Rental::with('status')
             ->get()
             ->groupBy('status.status_name')
             ->map(fn($rentals, $status) => [
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
                 ->sum(fn($rental) => $rental->invoices->sum('total_amount')) ?? 0;
             
             $weeklyRevenue->push([
                 'week' => 'W' . $startOfWeek->weekOfYear,
                 'revenue' => round($revenue, 2),
             ]);
         }

         // Top Customers by Rentals
         $topCustomers = Rental::with('customer')
             ->get()
             ->groupBy('customer_id')
             ->map(fn($rentals, $customerId) => [
                 'customer_id' => $customerId,
                 'customer_name' => $rentals->first()->customer->first_name . ' ' . $rentals->first()->customer->last_name,
                 'rental_count' => $rentals->count(),
                 'total_spent' => $rentals->sum(fn($rental) => $rental->invoices->sum('total_amount') ?? 0),
             ])
             ->sortByDesc('rental_count')
             ->take(8)
             ->values();

         // Top Items by Revenue
         $topItemsByRevenue = Rental::with('item', 'invoices')
             ->get()
             ->groupBy('item_id')
             ->map(fn($rentals, $itemId) => [
                 'item_id' => $itemId,
                 'item_name' => $rentals->first()->item->name,
                 'rental_count' => $rentals->count(),
                 'total_revenue' => round($rentals->sum(fn($rental) => $rental->invoices->sum('total_amount') ?? 0), 2),
             ])
             ->sortByDesc('total_revenue')
             ->take(8)
             ->values();

         // Rental Duration Distribution (for histogram approximation)
         $durationBuckets = [
             '1-2 days' => Rental::whereBetween(Rental::selectRaw('DATEDIFF(DAY, rental_date, return_date)'), [1, 2])->count(),
             '3-7 days' => Rental::whereBetween(Rental::selectRaw('DATEDIFF(DAY, rental_date, return_date)'), [3, 7])->count(),
             '1-2 weeks' => Rental::whereBetween(Rental::selectRaw('DATEDIFF(DAY, rental_date, return_date)'), [8, 14])->count(),
             '2-4 weeks' => Rental::whereBetween(Rental::selectRaw('DATEDIFF(DAY, rental_date, return_date)'), [15, 28])->count(),
             '1+ months' => Rental::where(Rental::selectRaw('DATEDIFF(DAY, rental_date, return_date)'), '>=', 29)->count(),
         ];

         return response()->json([
             'kpis' => [
                 'total_rentals' => $totalRentals,
                 'active_rentals' => $activeRentals,
                 'completed_rentals' => $completedRentals,
                 'cancelled_rentals' => $cancelledRentals,
                 'overdue_rentals' => $overdueRentals,
                 'late_return_rentals' => $lateReturnRentals,
                 'avg_rental_duration' => round($avgRentalDuration, 1),
                 'total_rental_revenue' => round($totalRentalRevenue, 2),
                 'revenue_this_month' => round($revenueThisMonth, 2),
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
             'generated_at' => now()->format('Y-m-d H:i:s'),
         ]);
     }

     /**
      * Display a listing of the resource.
      */
    public function index(Request $request): JsonResponse
    {
        $query = Rental::with(['customer', 'item', 'status', 'reservation', 'releasedBy']);

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

        // Pagination
        $perPage = $request->get('per_page', 15);
        $rentals = $query->orderBy('created_at', 'desc')->paginate($perPage);

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
            'data' => $rental
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
            'invoices.invoiceItems'
        ]);

        // Add calculated penalty if overdue
        $penalty = $this->calculatePenalty($rental);

        return response()->json([
            'data' => $rental,
            'calculated_penalty' => $penalty,
            'is_overdue' => $rental->return_date === null && Carbon::parse($rental->due_date)->lessThan(Carbon::now())
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
            'data' => $rental
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
                'message' => 'Cannot delete rental. It has invoices associated with it.'
            ], 422);
        }

        $rental->delete();

        return response()->json([
            'message' => 'Rental deleted successfully'
        ]);
    }

    /**
     * BUSINESS ACTIVITY: Clerk logs when a gown or suit is released to the customer
     * Release an item to customer (converts reservation to rental)
     */
    public function releaseItem(Request $request): JsonResponse
    {
        $request->validate([
            'reservation_id' => 'nullable|exists:reservations,reservation_id',
            'item_id' => 'required|exists:inventories,item_id',
            'customer_id' => 'required|exists:customers,customer_id',
            'released_date' => 'required|date',
            'due_date' => 'required|date|after:released_date',
            'release_notes' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            // Get the "Rented Out" status
            $rentedStatus = RentalStatus::where('status_name', 'Rented Out')->first();
            if (!$rentedStatus) {
                return response()->json([
                    'message' => 'Rented Out status not found in the system.'
                ], 500);
            }

            // Create the rental record
            $rental = Rental::create([
                'reservation_id' => $request->reservation_id,
                'item_id' => $request->item_id,
                'customer_id' => $request->customer_id,
                'released_by' => auth()->id(),
                'released_date' => $request->released_date,
                'due_date' => $request->due_date,
                'original_due_date' => $request->due_date,
                'status_id' => $rentedStatus->status_id,
                'extension_count' => 0,
            ]);

            // Update reservation status if exists
            if ($request->reservation_id) {
                $reservation = \App\Models\Reservation::find($request->reservation_id);
                if ($reservation) {
                    $reservation->update(['reservation_status' => 'Completed']);
                }
            }

            // Update item availability
            $item = \App\Models\Inventory::find($request->item_id);
            if ($item) {
                $item->update(['availability_status' => 'Rented']);
            }

            DB::commit();

            $rental->load(['customer', 'item', 'status', 'reservation', 'releasedBy']);

            return response()->json([
                'message' => 'Item released successfully to customer',
                'data' => $rental
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to release item',
                'error' => $e->getMessage()
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
        ]);

        // Check if rental was already returned
        if ($rental->return_date !== null) {
            return response()->json([
                'message' => 'This rental has already been returned.'
            ], 422);
        }

        DB::beginTransaction();
        try {
            // Update rental with return information
            $rental->update([
                'return_date' => $request->input('return_date'),
                'returned_to' => auth()->id(),
                'return_notes' => $request->input('return_notes'),
            ]);

            // Calculate and create final penalty if returned late
            $this->createOrUpdatePenaltyInvoice($rental);

            // Update rental status to returned
            $returnedStatus = RentalStatus::where('status_name', 'Returned')->first();
            if ($returnedStatus) {
                $rental->update(['status_id' => $returnedStatus->status_id]);
            }

            // Update item availability back to available
            if ($rental->item) {
                $rental->item->update(['availability_status' => 'Available']);
            }

            DB::commit();

            $rental->load(['customer', 'item', 'status', 'invoices.invoiceItems']);

            return response()->json([
                'message' => 'Rental return processed successfully',
                'data' => $rental,
                'penalty_charged' => $this->calculatePenalty($rental)
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to process return',
                'error' => $e->getMessage()
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
                'message' => 'Cannot extend a rental that has already been returned.'
            ], 422);
        }

        // Check if rental is overdue
        if (Carbon::parse($rental->due_date)->lessThan(Carbon::now())) {
            return response()->json([
                'message' => 'Cannot extend an overdue rental. Please settle penalties first.'
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
                'data' => $rental
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to extend rental',
                'error' => $e->getMessage()
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
                'message' => 'Cannot cancel a rental that has already been returned.'
            ], 422);
        }

        // Check if rental has invoices that are already paid
        $paidInvoices = $rental->invoices()->where('payment_status', 'paid')->count();
        if ($paidInvoices > 0) {
            return response()->json([
                'message' => 'Cannot cancel rental. It has paid invoices associated with it.'
            ], 422);
        }

        DB::beginTransaction();
        try {
            // Update the rental status to cancelled
            $cancelledStatus = RentalStatus::where('status_name', 'Cancelled')->first();
            if (!$cancelledStatus) {
                return response()->json([
                    'message' => 'Cancelled status not found in the system.'
                ], 500);
            }

            $rental->update([
                'status_id' => $cancelledStatus->status_id,
                'return_notes' => 'Rental cancelled on ' . now()->format('Y-m-d H:i:s')
            ]);

            // If there's a reservation, update its status too
            if ($rental->reservation) {
                $rental->reservation->update([
                    'reservation_status' => 'Cancelled'
                ]);
            }

            // Update item availability back to available
            if ($rental->item) {
                $rental->item->update(['availability_status' => 'Available']);
            }

            DB::commit();

            $rental->load(['customer', 'item', 'status', 'reservation', 'releasedBy']);

            return response()->json([
                'message' => 'Rental cancelled successfully',
                'data' => $rental
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to cancel rental',
                'error' => $e->getMessage()
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
     */
    public function calculatePenalty(Rental $rental): float
    {
        if ($rental->return_date !== null) {
            $returnDate = Carbon::parse($rental->return_date);
            $dueDate = Carbon::parse($rental->due_date);

            // If returned on time or early, no penalty
            if ($returnDate->lessThanOrEqualTo($dueDate)) {
                return 0;
            }

            // Calculate days late
            $daysLate = $returnDate->diffInDays($dueDate);
        } else {
            // Rental is still active
            $now = Carbon::now();
            $dueDate = Carbon::parse($rental->due_date);

            if ($now->lessThanOrEqualTo($dueDate)) {
                return 0;
            }

            $daysLate = $now->diffInDays($dueDate);
        }

        $penaltyPerDay = 50.00;
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
                    'invoice_type' => 'penalty'
                ],
                [
                    'customer_id' => $rental->customer_id,
                    'invoice_date' => now(),
                    'due_date' => now()->addDays(7),
                    'subtotal' => 0,
                    'tax_amount' => 0,
                    'total_amount' => 0,
                    'payment_status' => 'unpaid',
                    'notes' => 'Late return penalty invoice'
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
                    'total_price' => $penaltyAmount
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
                    'total_price' => $penaltyAmount
                ]);
            }

            // Recalculate invoice totals
            $invoice->refresh();
            $totalAmount = $invoice->invoiceItems()->sum('total_price');
            $invoice->update([
                'subtotal' => $totalAmount,
                'total_amount' => $totalAmount
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
            'penalties_created_or_updated' => $penaltiesCreated
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
            'rentals' => $rentals
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
            'rentals' => $rentals
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
            'total_pending_penalties' => $totalPendingPenalties
        ]);
    }

}
