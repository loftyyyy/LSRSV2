<?php

namespace App\Http\Controllers;

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

    }

    /**
     * Create PDF for reports
     */
    public function generatePDF()
    {

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
        //TODO: To be implemented
    }

    /**
     * Does overdue check for each rental
     */
    public function checkOverdue(Rental $rental): void
    {
        //TODO: To be implemented
    }

    /**
     * Calculates penalties for late returns ( A helper function )
     */
    public function calculatePenalty(Rental $rental): void
    {

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
