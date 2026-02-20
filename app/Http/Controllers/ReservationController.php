<?php

namespace App\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Reservation;
use App\Models\Inventory;
use App\Models\InventoryStatus;
use App\Models\InventoryVariant;
use App\Models\ReservationItem;
use App\Models\ReservationStatus;
use App\Http\Requests\StoreReservationRequest;
use App\Http\Requests\UpdateReservationRequest;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class ReservationController extends Controller
{

    /**
     * Returns a comprehensive report data
     */
    public function report(Request $request):JsonResponse
    {
        $query = Reservation::with(['customer', 'status', 'reservedBy', 'items.item', 'items.variant']);

        // Filter by date range
        if ($request->has('start_date')) {
            $query->where('reservation_date', '>=', $request->get('start_date'));
        }
        if ($request->has('end_date')) {
            $query->where('reservation_date', '<=', $request->get('end_date'));
        }

        // Filter by status
        if ($request->has('status_id')) {
            $query->where('status_id', $request->get('status_id'));
        }
        if ($request->has('status')) {
            $statusName = $request->get('status');
            $query->whereHas('status', function ($statusQuery) use ($statusName) {
                $statusQuery->whereRaw('LOWER(status_name) = ?', [strtolower($statusName)]);
            });
        }

        // Filter by customer
        if ($request->has('customer_id')) {
            $query->where('customer_id', $request->get('customer_id'));
        }

        // Filter by reserved by (clerk/admin)
        if ($request->has('reserved_by')) {
            $query->where('reserved_by', $request->get('reserved_by'));
        }

        // Order by reservation date
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
            'by_month' => $reservations->groupBy(function ($reservation) {
                return $reservation->reservation_date->format('Y-m');
            })->map(function ($group) {
                return $group->count();
            }),
            'by_clerk' => $reservations->groupBy('reservedBy.name')->map(function ($group) {
                return $group->count();
            }),
            'average_items_per_reservation' => $reservations->count() > 0
                ? round($reservations->sum(function ($r) { return $r->items->count(); }) / $reservations->count(), 2)
                : 0,
        ];

        return response()->json([
            'summary' => $summary,
            'reservations' => $reservations,
            'filters' => $request->only(['start_date', 'end_date', 'status_id', 'status', 'customer_id', 'reserved_by'])
        ]);
    }

    /**
     * Create PDF for reports
     */
    public function generatePDF(Request $request)
    {
         $query = Reservation::with(['customer', 'status', 'reservedBy', 'items.item', 'items.variant']);

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
        if ($request->has('status')) {
            $statusName = $request->get('status');
            $query->whereHas('status', function ($statusQuery) use ($statusName) {
                $statusQuery->whereRaw('LOWER(status_name) = ?', [strtolower($statusName)]);
            });
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

        $filters = $request->only(['start_date', 'end_date', 'status_id', 'status', 'customer_id', 'reserved_by']);

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
     * Display Reservation Reports Page
     */
    public function showReportsPage(): View
    {
        return view('reservations.reports');
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Reservation::with(['customer', 'status', 'reservedBy', 'items.item', 'items.variant']);

        // Search functionality
        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('reservation_id', 'like', "%{$search}%")
                    ->orWhereHas('customer', function ($customerQuery) use ($search) {
                        $customerQuery->where('first_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    })
                    ->orWhereHas('items.variant', function ($variantQuery) use ($search) {
                        $variantQuery->where('name', 'like', "%{$search}%")
                            ->orWhere('design', 'like', "%{$search}%")
                            ->orWhere('color', 'like', "%{$search}%")
                            ->orWhere('size', 'like', "%{$search}%");
                    });
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

        // Filter by status name
        if ($request->has('status')) {
            $statusName = $request->get('status');
            $query->whereHas('status', function ($statusQuery) use ($statusName) {
                $statusQuery->whereRaw('LOWER(status_name) = ?', [strtolower($statusName)]);
            });
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

        // Sort
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        // Pagination
        $perPage = $request->get('per_page', 15);
        $reservations = $query->paginate($perPage);

        return response()->json($reservations);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreReservationRequest $request): JsonResponse
    {
        try {
            DB::beginTransaction();

            // Set the clerk who created the reservation and default status
            $validatedData = $request->validated();
            $validatedData['reserved_by'] = Auth::id();
            $validatedData['reservation_date'] = now();

            // Set default status to 'pending' if not provided
            if (!isset($validatedData['status_id'])) {
                $pendingStatus = ReservationStatus::where('status_name', 'pending')->first();
                $validatedData['status_id'] = $pendingStatus?->status_id ?? 1;
            }

            // Create the reservation
            $reservation = Reservation::create($validatedData);

            // Add items to reservation if provided
            if ($request->has('items') && !empty($request->items)) {
                foreach ($request->items as $itemData) {
                    $variant = InventoryVariant::findOrFail($itemData['variant_id']);
                    $requestedQuantity = (int) ($itemData['quantity'] ?? 1);

                    if ($requestedQuantity < 1) {
                        DB::rollBack();
                        return response()->json([
                            'message' => 'Quantity must be at least 1',
                            'error' => 'INVALID_QUANTITY',
                        ], 422);
                    }

                    $availableCount = $this->getAvailableVariantUnitsForDateRange(
                        $variant->variant_id,
                        $validatedData['start_date'],
                        $validatedData['end_date']
                    );

                    if ($availableCount < $requestedQuantity) {
                        DB::rollBack();
                        return response()->json([
                            'message' => "Variant '{$variant->name}' has only {$availableCount} available unit(s) for the selected dates",
                            'error' => 'VARIANT_NOT_AVAILABLE',
                            'variant_id' => $variant->variant_id,
                        ], 422);
                    }

                    ReservationItem::create([
                        'reservation_id' => $reservation->reservation_id,
                        'item_id' => null,
                        'variant_id' => $variant->variant_id,
                        'quantity' => $requestedQuantity,
                        'rental_price' => $itemData['rental_price'] ?? $variant->rental_price,
                        'notes' => $itemData['notes'] ?? null
                    ]);
                }
            }

            DB::commit();

            $reservation->load(['customer', 'status', 'reservedBy', 'items.item', 'items.variant']);

            return response()->json([
                'message' => 'Reservation created successfully',
                'data' => $reservation
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Reservation creation failed:', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);
            return response()->json([
                'message' => 'Failed to create reservation',
                'error' => $e->getMessage()
            ], 500);
        }
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
            'items.item',
            'items.variant',
            'rentals',
            'invoices'
        ]);

        return response()->json([
            'data' => $reservation
        ]);
    }

    /**
     * Updates an existing reservation.
     */
    public function update(UpdateReservationRequest $request, Reservation $reservation): JsonResponse
    {
        try {
            DB::beginTransaction();

            // Only pending reservations can be updated
            if (strtolower($reservation->status->status_name) !== 'pending') {
                return response()->json([
                    'message' => 'Only pending reservations can be updated'
                ], 422);
            }

            // Update reservation basic info
            $validatedData = $request->validated();
            $reservation->update($validatedData);

            // Update items if provided
            if ($request->has('items')) {
                // Remove old items if updating
                if ($request->get('replace_items', false)) {
                    $reservation->items()->delete();
                }

                // Add new/updated items
                foreach ($request->items as $itemData) {
                    $variant = InventoryVariant::findOrFail($itemData['variant_id']);
                    $requestedQuantity = (int) ($itemData['quantity'] ?? 1);

                    if ($requestedQuantity < 1) {
                        DB::rollBack();
                        return response()->json([
                            'message' => 'Quantity must be at least 1',
                            'error' => 'INVALID_QUANTITY',
                        ], 422);
                    }

                    $availableCount = $this->getAvailableVariantUnitsForDateRange(
                        $variant->variant_id,
                        $validatedData['start_date'] ?? $reservation->start_date,
                        $validatedData['end_date'] ?? $reservation->end_date,
                        $reservation->reservation_id
                    );

                    if ($availableCount < $requestedQuantity) {
                        DB::rollBack();
                        return response()->json([
                            'message' => "Variant '{$variant->name}' has only {$availableCount} available unit(s) for the selected dates",
                            'error' => 'VARIANT_NOT_AVAILABLE',
                            'variant_id' => $variant->variant_id,
                        ], 422);
                    }

                    if (isset($itemData['reservation_item_id'])) {
                        ReservationItem::where('reservation_item_id', $itemData['reservation_item_id'])
                            ->update([
                                'item_id' => null,
                                'variant_id' => $variant->variant_id,
                                'quantity' => $requestedQuantity,
                                'rental_price' => $itemData['rental_price'] ?? $variant->rental_price,
                                'notes' => $itemData['notes'] ?? null
                            ]);
                    } else {
                        ReservationItem::create([
                            'reservation_id' => $reservation->reservation_id,
                            'item_id' => null,
                            'variant_id' => $variant->variant_id,
                            'quantity' => $requestedQuantity,
                            'rental_price' => $itemData['rental_price'] ?? $variant->rental_price,
                            'notes' => $itemData['notes'] ?? null
                        ]);
                    }
                }
            }

            DB::commit();

            $reservation->load(['customer', 'status', 'reservedBy', 'items.item', 'items.variant']);

            return response()->json([
                'message' => 'Reservation updated successfully',
                'data' => $reservation
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to update reservation',
                'error' => $e->getMessage()
            ], 500);
        }
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
        $query = InventoryVariant::query();

        if ($request->has('item_type')) {
            $query->where('item_type', $request->get('item_type'));
        }

        // Filter by size
        if ($request->has('size')) {
            $query->where('size', $request->get('size'));
        }

        // Filter by color
        if ($request->has('color')) {
            $query->where('color', $request->get('color'));
        }

        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('design', 'like', "%{$search}%")
                    ->orWhere('size', 'like', "%{$search}%")
                    ->orWhere('color', 'like', "%{$search}%");
            });
        }

        $variants = $query
            ->orderBy('name')
            ->paginate($request->get('per_page', 20));

        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        $availableOnly = filter_var($request->get('available_only', true), FILTER_VALIDATE_BOOLEAN);

        $mapped = $variants->getCollection()->map(function ($variant) use ($startDate, $endDate) {
            $availableCount = $this->getAvailableVariantUnitsForDateRange(
                $variant->variant_id,
                $startDate,
                $endDate
            );

            $representativeItem = Inventory::with(['status', 'images' => function ($q) {
                $q->where('is_primary', true)->orWhereIn('image_id', function ($subQ) {
                    $subQ->selectRaw('MIN(image_id)')
                        ->from('inventory_images')
                        ->groupBy('item_id');
                });
            }])
                ->where('variant_id', $variant->variant_id)
                ->orderBy('item_id')
                ->first();

            return [
                'variant_id' => $variant->variant_id,
                'item_type' => $variant->item_type,
                'name' => $variant->name,
                'size' => $variant->size,
                'color' => $variant->color,
                'design' => $variant->design,
                'rental_price' => $variant->rental_price,
                'deposit_amount' => $variant->deposit_amount,
                'is_sellable' => $variant->is_sellable,
                'selling_price' => $variant->selling_price,
                'available_quantity' => $availableCount,
                'total_units' => $variant->total_units,
                'representative_item_id' => $representativeItem?->item_id,
                'representative_sku' => $representativeItem?->sku,
                'status' => $representativeItem?->status,
                'images' => $representativeItem?->images ?? [],
            ];
        });

        if ($availableOnly) {
            $mapped = $mapped->filter(fn ($variant) => $variant['available_quantity'] > 0)->values();
        }

        $variants->setCollection($mapped);

        return response()->json([
            'data' => $variants,
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
        $variant = InventoryVariant::findOrFail($itemId);
        $representativeItem = Inventory::with(['status', 'images'])->where('variant_id', $variant->variant_id)->orderBy('item_id')->first();

        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        $availableCount = $this->getAvailableVariantUnitsForDateRange(
            $variant->variant_id,
            $startDate,
            $endDate
        );

        $isAvailableForDates = $availableCount > 0;

        $nextAvailableDate = Reservation::whereHas('items', function ($query) use ($variant) {
            $query->where('variant_id', $variant->variant_id);
        })
            ->whereDate('end_date', '>=', now()->toDateString())
            ->whereHas('status', function ($statusQ) {
                $statusQ->whereRaw('LOWER(status_name) != ?', ['cancelled']);
            })
            ->orderBy('end_date')
            ->value('end_date');

        $nextAvailableDate = $nextAvailableDate ? Carbon::parse($nextAvailableDate)->addDay() : null;

        return response()->json([
            'data' => [
                'variant_id' => $variant->variant_id,
                'name' => $variant->name,
                'item_type' => $variant->item_type,
                'size' => $variant->size,
                'color' => $variant->color,
                'design' => $variant->design,
                'rental_price' => $variant->rental_price,
                'deposit_amount' => $variant->deposit_amount,
                'is_sellable' => $variant->is_sellable,
                'selling_price' => $variant->selling_price,
                'total_units' => $variant->total_units,
                'representative_sku' => $representativeItem?->sku,
                'status' => $representativeItem?->status,
                'images' => $representativeItem?->images ?? [],
                'created_at' => $variant->created_at,
                'updated_at' => $variant->updated_at,
            ],
            'availability' => [
                'is_currently_available' => $availableCount > 0,
                'next_available_date' => $nextAvailableDate,
                'is_available_for_requested_dates' => $isAvailableForDates,
                'upcoming_reservations' => ReservationItem::where('variant_id', $variant->variant_id)->count(),
                'available_quantity' => $availableCount,
            ],
            'message' => 'Item details retrieved successfully'
        ]);
    }

    /**
     * @param Request $request
     * @param Reservation $reservation
     * @return JsonResponse
     * Cancels a reservation by updating its status
     */
    public function cancelReservation(Request $request, Reservation $reservation): JsonResponse
    {
        // Check if reservation can be cancelled
        if (strtolower($reservation->status->status_name) === 'completed') {
            return response()->json([
                'message' => 'Cannot cancel a completed reservation'
            ], 422);
        }

        if (strtolower($reservation->status->status_name) === 'cancelled') {
            return response()->json([
                'message' => 'Reservation is already cancelled'
            ], 422);
        }

        // Check if reservation has active rentals
        $hasActiveRentals = $reservation->rentals()
            ->whereNull('return_date')
            ->exists();

        if ($hasActiveRentals) {
            return response()->json([
                'message' => 'Cannot cancel reservation with active rentals. Please return all items first.'
            ], 422);
        }

        // Find cancelled status
        $cancelledStatus = \App\Models\ReservationStatus::whereRaw('LOWER(status_name) = ?', ['cancelled'])->first();

        if (!$cancelledStatus) {
            return response()->json([
                'message' => 'Cancelled status not found in system'
            ], 500);
        }

        // Update reservation status to cancelled
        $reservation->update([
            'status_id' => $cancelledStatus->status_id,
            'cancellation_reason' => $request->get('cancellation_reason'),
            'cancelled_at' => now(),
            'cancelled_by' => Auth::id()
        ]);

        // Update inventory status for all reserved items
        // Change reserved items back to available
        $availableStatusId = InventoryStatus::whereRaw('LOWER(status_name) = ?', ['available'])
            ->value('status_id');

        foreach ($reservation->items as $reservationItem) {
            if ($availableStatusId && $reservationItem->item_id) {
                $reservationItem->item()->update([
                    'status_id' => $availableStatusId
                ]);
            }
        }

        $reservation->load(['customer', 'status', 'reservedBy', 'items.item', 'items.variant']);

        return response()->json([
            'message' => 'Reservation cancelled successfully',
            'data' => $reservation
        ]);
    }

    /**
     * Helper method: Check item availability for a date range
     */
    private function getAvailableVariantUnitsForDateRange(int $variantId, ?string $startDate, ?string $endDate, ?int $excludeReservationId = null): int
    {
        $rentableUnits = Inventory::where('variant_id', $variantId)
            ->whereHas('status', function ($query) {
                $query->whereRaw('LOWER(status_name) NOT IN (?, ?)', ['retired', 'sold']);
            })
            ->count();

        if (!$startDate || !$endDate) {
            $availableStatusId = InventoryStatus::whereRaw('LOWER(status_name) = ?', ['available'])->value('status_id');
            if (!$availableStatusId) {
                return 0;
            }

            return Inventory::where('variant_id', $variantId)
                ->where('status_id', $availableStatusId)
                ->count();
        }

        $reservedUnits = ReservationItem::where('variant_id', $variantId)
            ->whereHas('reservation', function ($query) use ($startDate, $endDate, $excludeReservationId) {
                $query->where(function ($q) use ($startDate, $endDate) {
                    $q->whereBetween('start_date', [$startDate, $endDate])
                        ->orWhereBetween('end_date', [$startDate, $endDate])
                        ->orWhere(function ($innerQ) use ($startDate, $endDate) {
                            $innerQ->where('start_date', '<=', $startDate)
                                ->where('end_date', '>=', $endDate);
                        });
                })->whereHas('status', function ($statusQ) {
                    $statusQ->whereRaw('LOWER(status_name) != ?', ['cancelled']);
                });

                if ($excludeReservationId) {
                    $query->where('reservation_id', '!=', $excludeReservationId);
                }
            })
            ->sum('quantity');

        $rentedUnits = DB::table('rentals')
            ->join('inventories', 'rentals.item_id', '=', 'inventories.item_id')
            ->where('inventories.variant_id', $variantId)
            ->whereDate('rentals.released_date', '<=', $endDate)
            ->whereDate(DB::raw('COALESCE(rentals.return_date, rentals.due_date)'), '>=', $startDate)
            ->count();

        return max($rentableUnits - $reservedUnits - $rentedUnits, 0);
    }

    /**
     * Confirm a pending reservation
     */
    public function confirmReservation(Reservation $reservation): JsonResponse
    {
        if (strtolower($reservation->status->status_name) !== 'pending') {
            return response()->json([
                'message' => 'Only pending reservations can be confirmed'
            ], 422);
        }

        $confirmedStatus = \App\Models\ReservationStatus::whereRaw('LOWER(status_name) = ?', ['confirmed'])->first();

        $reservation->update([
            'status_id' => $confirmedStatus->status_id,
            'confirmed_at' => now(),
            'confirmed_by' => Auth::id()
        ]);

        $reservation->load(['customer', 'status', 'reservedBy', 'items.item', 'items.variant']);

        return response()->json([
            'message' => 'Reservation confirmed successfully',
            'data' => $reservation
        ]);
    }

}
