<?php

namespace App\Http\Controllers;

use App\Models\InvoiceItem;
use App\Models\Rental;
use App\Http\Requests\StoreRentalRequest;
use App\Http\Requests\UpdateRentalRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Carbon\Carbon;


class RentalController extends Controller
{


    /**
     * Display Reports Page
     */
    public function report(Request $request):JsonResponse
    {
        $query = Rental::with(['customer', 'item', 'status', 'reservation', 'releasedBy', 'invoices.invoiceItems']);

        // Apply date range filters
        if($request->has('date_from')){
            $query->where('released_date', '>=', Carbon::parse($request->get('date_from')));
        }
        if($request->has('date_to')){
            $query->where('released_date', '<=', Carbon::parse($request->get('date_to')));
        }

        // Filter by status
        if($request->has('status_id')){
            $query->where('status_id', $request->get('status_id'));
        }

        // Filter by rental status (active/returned/overdue)
        if($request->has('rental_status')){
            $rentalStatus = $request->get('rental_status');
            if($rentalStatus === 'active'){
                $query->whereNull('return_date')->where('due_date', '>=', Carbon::now());
            }
            elseif($rentalStatus === 'returned'){
                $query->whereNotNull('return_date');
            }
            elseif($rentalStatus === 'overdue'){
                $query->whereNull('return_date')->where('due_date', '<', Carbon::now());
            }
        }

        $rentals = $query->get();

        // Calculate Analytics
        $totalRentals = $rentals->count();
        $activeRentals = $rentals->whereNull('return_date')->where('due_date', '>=', Carbon::now())->count();
        $returnedOnTimeRentals = $rentals->whereNotNull('return_date')->where('due_date', '<', Carbon::now())->count();
        $returnedOverdueRentals = $rentals->whereNotNull('return_date')->where('due_date', '>', Carbon::now())->count();
        $overdueRentals = $rentals->whereNull('return_date')->where('due_date', '<', Carbon::now())->count();


        // Calculate total penalties from invoice items
        $totalPenalties = $this->getTotalPenaltiesFromInvoices($rentals);


    }

    /**
     * Get total penalties from invoice items
     */
    public function getTotalPenaltiesFromInvoices($rentals): float
    {
        $rentalIds = $rentals->pluck('rental_id');

        return InvoiceItem::whereIn('invoice_id', function($query) use ($rentalIds){
            $query->select('invoice_id')->from('invoices')->whereIn('rental_id', $rentalIds);
        })->whereIn('item_type', ['penalty', 'late_fee'])->sum('total_price');
    }


    /**
     * Create PDF for reports
     */
    public function generatePDF(Request $request)
    {
        // Get filtered request based on request parameters
        $query = Rental::with(['customer', 'item', 'status', 'reservation', 'releasedBy', 'invoices.invoiceItems']);

    }
    /**
     * Display Rental Page
     */
    public function showRentalPage():View
    {
        return view('rentals.index');
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
                });
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
            'invoices'
        ]);

        return response()->json([
            'data' => $rental
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
     * Cancel a rental
     */
    public function cancel(Rental $rental): JsonResponse
    {
        // Check if rental was already returned
        if($rental->return_date !== null){
            return response()->json([
                'message' => 'Cannot cancel a rental that has already been returned.'
            ], 422);
        }

        // Check if rental has invoices that are already paid
        $paidInvoices = $rental->invoices()->where('payment_status', 'paid')->count();
        if($paidInvoices > 0){
            return response()->json([
                'message' => 'Cannot cancel rental. It has paid invoices associated with it.'

            ], 422);
        }

        // Update the rental status to cancelled
        $cancelledStatus = \App\Models\RentalStatus::where('status_name', 'Cancelled')->first();
        if(!$cancelledStatus){
            return response()->json([
                'message' => 'Cancelled status not found in the system.'
            ], 500);

        }

        $rental->update([
            'status_id' => $cancelledStatus->status_id,
            'return_notes' => 'Rental cancelled on ' . now()->format('Y-m-d H:i:s')
        ]);

        // If there's a reservation, update its status too
        if($rental->reservation){
            $rental->reservation->update([
                'reservation_status' => 'Cancelled'
            ]);
        }

        $rental->load(['customer', 'item', 'status', 'reservation', 'releasedBy']);
        return response()->json([
            'message' => 'Rental cancelled successfully',
            'data' => $rental
        ]);
    }

    /**
     * Does overdue check for each rental
     */
    public function checkOverdue(Rental $rental): void
    {
        // Check if rental hasn't been returned
        if($rental->return_date === null){
            $now = Carbon::now();
            $dueDate = Carbon::parse($rental->due_date);

            // if Rental is overdue
            if($now->greaterThan($dueDate)){
                $overdueStatus = \App\Models\RentalStatus::where('status_name', 'Overdue')->first();

                if($overdueStatus && $rental->status_id !== $overdueStatus->status_id){
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
     * Calculates penalties for late returns ( A helper function )
     */
    public function calculatePenalty(Rental $rental): float
    {

        if($rental->return_date !== null) {
            $returnDate = Carbon::parse($rental->return_date);
            $dueDate =  Carbon::parse($rental->due_date);

            // If returned on time or early, no penalty
            if($returnDate->lessThanOrEqualTo($dueDate)) {
                return 0;
            }

            // Calculate days late
            $daysLate = $returnDate->diffInDays($dueDate);
        } else {

            // Rental is still active
            $now = Carbon::now();
            $dueDate = Carbon::parse($rental->due_date);

            if($now->lessThanOrEqualTo($dueDate)) {
                return 0;
            }

            $daysLate = $now->diffInDays($dueDate);
        }

        $penaltyPerDay = 50.00;
        $penalty = $daysLate * $penaltyPerDay;

        // Optional, I could add a grace period (first day could be free)
        //
        //

        return $penalty;
    }

    /**
     * @param Rental $rental
     * @return void
     *
     */
    public function createOrUpdatePenaltyInvoice(Rental $rental): void
    {

    }
    /**
     * @param Rental $rental
     * @param Request $request
     * @return JsonResponse
     * Process return and finalize penalties
     * Call this when a rental is being returned
     */
    public function processReturn(Rental $rental, Request $request): JsonResponse
    {
        DB::transaction(function () use ($rental, $request) {
            // Update rental with return information
            $rental->update([
                'return_date' => $request->input('return_date', now()),
                'returned_to' => auth()->id(),
                'return_notes' => $request->input('return_notes')
            ]);

            // Calculate and create final penalty if returned late
            $this->createOrUpdatePenaltyInvoice($rental);

            // Update rental status to returned
            $returnedStatus = \App\Models\RentalStatus::where('status_name', 'returned')->first();
            if($returnedStatus) {
                $rental->update(['status_id' => $returnedStatus->status_id]);
            }
        });

        $rental->load(['customer', 'item', 'status', 'invoices.invoiceItems']);

        return response()->json([
            'message' =>'Rental return processed successfully',
            'data' => $rental,
            'penalty_charged' => $this->calculatePenalty($rental)
        ]);
    }

    /**
     * @return JsonResponse
     * Checks all the rentals and identifies overdue rentals
     */
    public function batchCheckOverdue(): JsonResponse
    {
        $activeRentals = Rental::whereNull('return_date')->get();
        $overDueCount = 0;
        $penaltiesCreated = 0;

        foreach ($activeRentals as $rental) {
            $wasOverdue = Carbon::parse($rental->due_date)->lessThan(Carbon::now());

            $this->checkOverdue($rental);

            if($wasOverdue) {
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


}
