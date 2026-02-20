<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Inventory;
use App\Models\InventoryMovement;
use App\Models\InventoryStatus;
use App\Models\Reservation;
use App\Models\ReservationItem;
use App\Models\ReservationItemAllocation;
use App\Models\ReservationStatus;
use App\Models\Rental;
use App\Models\RentalStatus;
use App\Models\Payment;
use App\Models\PaymentStatus;
use App\Models\DepositReturn;
use App\Services\DepositService;
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
    public function __construct(private readonly DepositService $depositService)
    {
    }

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
             '1-2 days' => $durationsInDays->filter(fn ($days) => $days >= 1 && $days <= 2)->count(),
             '3-7 days' => $durationsInDays->filter(fn ($days) => $days >= 3 && $days <= 7)->count(),
             '1-2 weeks' => $durationsInDays->filter(fn ($days) => $days >= 8 && $days <= 14)->count(),
             '2-4 weeks' => $durationsInDays->filter(fn ($days) => $days >= 15 && $days <= 28)->count(),
             '1+ months' => $durationsInDays->filter(fn ($days) => $days >= 29)->count(),
         ];

         return response()->json([
             'kpis' => [
                 'total_rentals' => $totalRentals,
                 'active_rentals' => $activeRentals,
                 'completed_rentals' => $completedRentals,
                 'cancelled_rentals' => $cancelledRentals,
                 'overdue_rentals' => $overdueRentals,
                 'late_return_rentals' => $lateReturnRentals,
                 'avg_rental_duration' => $avgRentalDuration,
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
            'reservation_item_id' => 'nullable|exists:reservation_items,reservation_item_id',
            'variant_id' => 'nullable|exists:inventory_variants,variant_id',
            'item_id' => 'nullable|exists:inventories,item_id',
            'customer_id' => 'required|exists:customers,customer_id',
            'released_date' => 'required|date',
            'due_date' => 'required|date|after:released_date',
            'release_notes' => 'nullable|string',
            'collect_deposit' => 'sometimes|boolean',
            'deposit_amount' => 'nullable|numeric|min:0',
            'deposit_payment_method' => 'required_if:collect_deposit,true|in:cash,card,bank_transfer,gcash,paymaya',
            'deposit_payment_notes' => 'nullable|string',
        ]);

        if (!$request->item_id && !$request->reservation_item_id && !$request->variant_id) {
            return response()->json([
                'message' => 'Provide item_id, reservation_item_id, or variant_id when releasing an item.'
            ], 422);
        }

        DB::beginTransaction();
        try {
            $rentedStatus = $this->findRentalStatusByName('rented');
            if (!$rentedStatus) {
                return response()->json([
                    'message' => 'Rented Out status not found in the system.'
                ], 500);
            }

            $availableInventoryStatus = $this->findInventoryStatusByName('available');
            $rentedInventoryStatus = $this->findInventoryStatusByName('rented');
            if (!$availableInventoryStatus || !$rentedInventoryStatus) {
                return response()->json([
                    'message' => 'Required inventory statuses (available/rented) are missing.'
                ], 500);
            }

            $reservation = $request->reservation_id
                ? Reservation::find($request->reservation_id)
                : null;

            $reservationItem = null;
            if ($request->reservation_item_id) {
                $reservationItem = ReservationItem::with('reservation')
                    ->find($request->reservation_item_id);
                $reservation = $reservation ?: $reservationItem?->reservation;
            }

            $item = $request->item_id
                ? Inventory::findOrFail($request->item_id)
                : null;

            if (!$reservationItem && $reservation) {
                $variantIdForLookup = $request->variant_id ?: $item?->variant_id;
                if ($variantIdForLookup) {
                    $reservationItem = $reservation->items()
                        ->where('variant_id', $variantIdForLookup)
                        ->orderBy('reservation_item_id')
                        ->first();
                }
            }

            if (!$item) {
                $variantIdForLookup = $request->variant_id ?: $reservationItem?->variant_id;
                if ($variantIdForLookup) {
                    $item = Inventory::where('variant_id', $variantIdForLookup)
                        ->where('status_id', $availableInventoryStatus->status_id)
                        ->orderBy('item_id')
                        ->lockForUpdate()
                        ->first();
                }
            }

            if (!$item) {
                return response()->json([
                    'message' => 'No available physical item could be allocated for this release.'
                ], 422);
            }

            if ($reservationItem && $reservationItem->variant_id && $item->variant_id !== $reservationItem->variant_id) {
                return response()->json([
                    'message' => 'Selected item does not belong to the reservation variant.'
                ], 422);
            }

            $alreadyRented = Rental::where('item_id', $item->item_id)
                ->whereNull('return_date')
                ->exists();
            if ($alreadyRented || $item->status_id !== $availableInventoryStatus->status_id) {
                return response()->json([
                    'message' => 'Selected item is not currently available for release.'
                ], 422);
            }

            $fromStatusId = $item->status_id;
            $depositAmount = $request->filled('deposit_amount')
                ? (float) $request->input('deposit_amount')
                : (float) $item->deposit_amount;

            $rental = Rental::create([
                'reservation_id' => $reservation?->reservation_id ?? $request->reservation_id,
                'item_id' => $item->item_id,
                'customer_id' => $request->customer_id,
                'released_by' => auth()->id(),
                'released_date' => $request->released_date,
                'due_date' => $request->due_date,
                'original_due_date' => $request->due_date,
                'status_id' => $rentedStatus->status_id,
                'extension_count' => 0,
                'deposit_amount' => $depositAmount,
            ]);

            $shouldCollectDeposit = $request->boolean('collect_deposit', true);
            if ($shouldCollectDeposit) {
                if ($depositAmount <= 0) {
                    return response()->json([
                        'message' => 'Deposit amount must be greater than zero before releasing an item.'
                    ], 422);
                }

                $this->depositService->collectDeposit(
                    $rental,
                    $depositAmount,
                    auth()->id(),
                    $request->input('deposit_payment_method'),
                    $request->input('deposit_payment_notes')
                );
            } elseif ($rental->deposit_status !== 'held') {
                return response()->json([
                    'message' => 'Deposit must be collected before releasing the item.'
                ], 422);
            }

            $reservationItemId = null;
            if ($reservationItem) {
                $allocation = ReservationItemAllocation::firstOrNew([
                    'reservation_item_id' => $reservationItem->reservation_item_id,
                    'item_id' => $item->item_id,
                ]);

                if ($allocation->exists && $allocation->allocation_status === 'released' && $allocation->returned_at === null) {
                    return response()->json([
                        'message' => 'This item is already released for the reservation and has not been returned yet.'
                    ], 422);
                }

                $allocation->allocation_status = 'released';
                $allocation->allocated_at = $allocation->allocated_at ?: now();
                $allocation->released_at = now();
                $allocation->updated_by = auth()->id();
                $allocation->save();

                $reservationItemId = $reservationItem->reservation_item_id;
                $this->syncReservationItemFulfillment($reservationItem);
            }

            $item->update(['status_id' => $rentedInventoryStatus->status_id]);

            $this->createInventoryMovement(
                $item,
                'release',
                $fromStatusId,
                $rentedInventoryStatus->status_id,
                [
                    'reservation_id' => $reservation?->reservation_id,
                    'reservation_item_id' => $reservationItemId,
                    'rental_id' => $rental->rental_id,
                    'notes' => $request->release_notes,
                ]
            );

            if ($reservation) {
                $hasPendingItems = $reservation->items()
                    ->where('fulfillment_status', 'pending')
                    ->exists();

                $targetStatus = $hasPendingItems
                    ? $this->findReservationStatusByName('confirmed')
                    : ($this->findReservationStatusByName('completed') ?? $this->findReservationStatusByName('confirmed'));

                if ($targetStatus) {
                    $reservation->update(['status_id' => $targetStatus->status_id]);
                }
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
                'message' => 'This rental has already been returned.'
            ], 422);
        }

        DB::beginTransaction();
        try {
            $availableInventoryStatus = $this->findInventoryStatusByName('available');
            $rentedInventoryStatus = $this->findInventoryStatusByName('rented');
            if (!$availableInventoryStatus || !$rentedInventoryStatus) {
                return response()->json([
                    'message' => 'Required inventory statuses (available/rented) are missing.'
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
                    $returnNotes = trim(($returnNotes ? $returnNotes . ' | ' : '') . 'Condition: ' . $request->input('condition_notes'));
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
             $cancelledStatus = $this->findRentalStatusByName('cancelled');
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
                 $reservationCancelledStatus = $this->findReservationStatusByName('cancelled');
                 $rental->reservation->update([
                     'status_id' => $reservationCancelledStatus?->status_id ?? $rental->reservation->status_id
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
        if (!$pendingPaymentStatus) {
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
        if (!$paidStatus) {
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
        return $prefix . '-' . now()->format('YmdHis') . '-' . strtoupper(substr(bin2hex(random_bytes(3)), 0, 6));
    }

}
