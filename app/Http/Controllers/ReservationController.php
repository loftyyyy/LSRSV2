<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\Item;
use App\Http\Requests\StoreReservationRequest;
use App\Http\Requests\UpdateReservationRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReservationController extends Controller
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
    public function generatePDF(Request $request)
    {
         $query = Reservation::with(['customer', 'status', 'reservedBy', 'items.item']);

        // Apply same filters as report method
        if ($request->has('start_date')) {
            $query->where('reservation_date', '>=', $request->get('start_date'));
        }
        if ($request->has('end_date')) {
            $query->where('reservation_date', '<=', $request->get('end_date'));
        }
        if ($request->has('status_id')) {
            $query->where('status_id', $request->get('status_id'));
        }
        if ($request->has('customer_id')) {
            $query->where('customer_id', $request->get('customer_id'));
        }
        if ($request->has('reserved_by')) {
            $query->where('reserved_by', $request->get('reserved_by'));
        }

        $query->orderBy('reservation_date', 'desc');
        $reservations = $query->get();

        // Calculate summary statistics
        $summary = [
            'total_reservations' => $reservations->count(),
            'by_status' => $reservations->groupBy('status.status_name')->map(function ($group) {
                return $group->count();
            }),
            'total_items_reserved' => $reservations->sum(function ($reservation) {
                return $reservation->items->sum('quantity');
            }),
            'total_revenue' => $reservations->sum(function ($reservation) {
                return $reservation->items->sum(function ($item) {
                    return ($item->rental_price ?? 0) * ($item->quantity ?? 1);
                });
            }),
        ];

        $filters = $request->only(['start_date', 'end_date', 'status_id', 'customer_id', 'reserved_by']);

        // Generate PDF
        $pdf = Pdf::loadView('reports.reservations', [
            'reservations' => $reservations,
            'summary' => $summary,
            'filters' => $filters,
            'generated_at' => now(),
        ]);

        $pdf->setPaper('a4', 'landscape');

        return $pdf->download('reservations_report_' . now()->format('Y-m-d_His') . '.pdf');
    }
    /**
     * Display Reservation Page
     */
    public function showReservationPage(): View
    {
        return view('reservations.index');
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Reservation::with(['customer', 'status', 'reservedBy', 'items.item']);

        // Search functionality
        if ($request->has('search')) {
            $search = $request->get('search');
            $query->whereHas('customer', function ($customerQuery) use ($search) {
                $customerQuery->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filter by customer
        if ($request->has('customer_id')) {
            $query->where('customer_id', $request->get('customer_id'));
        }

        // Filter by status
        if ($request->has('status_id')) {
            $query->where('status_id', $request->get('status_id'));
        }

        // Filter by date range
        if ($request->has('start_date_from')) {
            $query->where('start_date', '>=', $request->get('start_date_from'));
        }
        if ($request->has('start_date_to')) {
            $query->where('start_date', '<=', $request->get('start_date_to'));
        }
        if ($request->has('end_date_from')) {
            $query->where('end_date', '>=', $request->get('end_date_from'));
        }
        if ($request->has('end_date_to')) {
            $query->where('end_date', '<=', $request->get('end_date_to'));
        }

        // Pagination
        $perPage = $request->get('per_page', 15);
        $reservations = $query->orderBy('created_at', 'desc')->paginate($perPage);

        return response()->json($reservations);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreReservationRequest $request): JsonResponse
    {
        $reservation = Reservation::create($request->validated());

        $reservation->load(['customer', 'status', 'reservedBy', 'items']);

        return response()->json([
            'message' => 'Reservation created successfully',
            'data' => $reservation
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Reservation $reservation): JsonResponse
    {
        $reservation->load([
            'customer',
            'status',
            'reservedBy',
            'items',
            'rentals',
            'invoices'
        ]);

        return response()->json([
            'data' => $reservation
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateReservationRequest $request, Reservation $reservation): JsonResponse
    {
        $reservation->update($request->validated());

        $reservation->load(['customer', 'status', 'reservedBy', 'items']);

        return response()->json([
            'message' => 'Reservation updated successfully',
            'data' => $reservation
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Reservation $reservation): JsonResponse
    {
        // Check if reservation has active rentals
        $hasActiveRentals = $reservation->rentals()
            ->whereNull('return_date')
            ->exists();

        // Check if reservation has invoices
        $hasInvoices = $reservation->invoices()->count() > 0;

        if ($hasActiveRentals || $hasInvoices) {
            $reasons = [];
            if ($hasActiveRentals) {
                $reasons[] = 'active rentals';
            }
            if ($hasInvoices) {
                $reasons[] = 'invoices';
            }
            return response()->json([
                'message' => 'Cannot delete reservation. It has ' . implode(' and ', $reasons) . ' associated with it.'
            ], 422);
        }

        $reservation->delete();

        return response()->json([
            'message' => 'Reservation deleted successfully'
        ]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * Returns available items filtered by type, size, color, and etc.
     */
    public function browseAvailableItems(Request $request): JsonResponse
    {
        $query = Item::with(['category', 'itemStatus']);

        // Filter by category (gowns, suits, and etc.)
        if ($request->has('category_id')) {
            $query->where('category_id', $request->get('category_id'));
        }

        // Filter by size
        if ($request->has('size')) {
            $query->where('size', $request->get('size'));
        }

        // Filter by color
        if ($request->has('color')) {
            $query->where('color', $request->get('color'));
        }

        // Filter by availability status (available items only)
        if ($request->get('available_only', true)) {
            $query->whereHas('itemStatus', function ($statusQuery) {
                $statusQuery->where('status_name', 'Available');
            });
        }

        // Search by name or code
        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('item_name', 'like', "%{$search}%")
                    ->orWhere('item_code', 'like', "%{$search}%");
            });
        }

        // Check availability for specific date range
        if ($request->has('start_date') && $request->has('end_date')) {
            $startDate = $request->get('start_date');
            $endDate = $request->get('end_date');

            // Exclude items that are already reserved in this date range
            $query->whereDoesntHave('reservationItems', function ($resQuery) use ($startDate, $endDate) {
                $resQuery->whereHas('reservation', function ($dateQuery) use ($startDate, $endDate) {
                    $dateQuery->where(function ($q) use ($startDate, $endDate) {
                        $q->whereBetween('start_date', [$startDate, $endDate])
                            ->orWhereBetween('end_date', [$startDate, $endDate])
                            ->orWhere(function ($innerQ) use ($startDate, $endDate) {
                                $innerQ->where('start_date', '<=', $startDate)
                                    ->where('end_date', '>=', $endDate);
                            });
                    })
                        ->whereHas('status', function ($statusQ) {
                            // Exclude cancelled reservations
                            $statusQ->where('status_name', '!=', 'Cancelled');
                        });
                });
            });
        }

        $items = $query->paginate($request->get('per_page', 20));

        return response()->json([
            'data' => $items,
            'message' => 'Available items retrieved successfully'
        ]);
    }

    /**
     * @param Request $request
     * @param $itemId
     * @return JsonResponse
     * Returns detailed information about a specific item
     */
    public function checkItemDetails(Request $request, $itemId): JsonResponse
    {
        $item = Item::with([
            'category',
            'itemStatus',
            'reservationItems.reservation' => function ($query) {
                $query->where('end_date', '>=', now())
                      ->whereHas('status', function ($statusQ) {
                          $statusQ->where('status_name', '!=', 'Cancelled');
                      });
            }
        ])->findOrFail($itemId);

        // Calculate next available date if item is currently reserved
        $nextAvailableDate = null;
        if ($item->reservationItems->isNotEmpty()) {
            $lastReservation = $item->reservationItems
                ->sortByDesc('reservation.end_date')
                ->first();

            if ($lastReservation && $lastReservation->reservation) {
                $nextAvailableDate = $lastReservation->reservation->end_date->addDay();
            }
        }

        // Check availability for specific date range
        $isAvailableForDates = true;
        if ($request->has('start_date') && $request->has('end_date')) {
            $startDate = $request->get('start_date');
            $endDate = $request->get('end_date');

            $conflictingReservations = $item->reservationItems()
                ->whereHas('reservation', function ($query) use ($startDate, $endDate) {
                    $query->where(function ($q) use ($startDate, $endDate) {
                        $q->whereBetween('start_date', [$startDate, $endDate])
                          ->orWhereBetween('end_date', [$startDate, $endDate])
                          ->orWhere(function ($innerQ) use ($startDate, $endDate) {
                              $innerQ->where('start_date', '<=', $startDate)
                                     ->where('end_date', '>=', $endDate);
                          });
                    })
                    ->whereHas('status', function ($statusQ) {
                        $statusQ->where('status_name', '!=', 'Cancelled');
                    });
                })
                ->count();

            $isAvailableForDates = $conflictingReservations === 0;
        }

        return response()->json([
            'data' => $item,
            'availability' => [
                'is_currently_available' => $item->itemStatus->status_name === 'Available',
                'next_available_date' => $nextAvailableDate,
                'is_available_for_requested_dates' => $isAvailableForDates,
                'upcoming_reservations' => $item->reservationItems->count()
            ],
            'message' => 'Item details retrieved successfully'
        ]);
    }
}
