<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Http\Requests\StoreInventoryRequest;
use App\Http\Requests\UpdateInventoryRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InventoryController extends Controller
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
     * Display Inventory Page
     */
    public function showInventoryPage(): View
    {
        return view('inventories.index');
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Inventory::with(['status']);

        // Search functionality
        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('sku', 'like', "%{$search}%")
                    ->orWhere('name', 'like', "%{$search}%")
                    ->orWhere('size', 'like', "%{$search}%")
                    ->orWhere('color', 'like', "%{$search}%")
                    ->orWhere('design', 'like', "%{$search}%");
            });
        }

        // Filter by item type
        if ($request->has('item_type')) {
            $query->where('item_type', $request->get('item_type'));
        }

        // Filter by condition
        if ($request->has('condition')) {
            $query->where('condition', $request->get('condition'));
        }

        // Filter by status
        if ($request->has('status_id')) {
            $query->where('status_id', $request->get('status_id'));
        }

        // Sort
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        // Pagination
        $perPage = $request->get('per_page', 15);
        $inventories = $query->paginate($perPage);

        return response()->json($inventories);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreInventoryRequest $request): JsonResponse
    {
        $inventory = Inventory::create($request->validated());

        $inventory->load('status');

        return response()->json([
            'message' => 'Inventory item created successfully',
            'data' => $inventory
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Inventory $inventory): JsonResponse
    {
        $inventory->load(['status', 'rentals', 'reservationItems', 'invoiceItems', 'images']);

        return response()->json([
            'data' => $inventory
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateInventoryRequest $request, Inventory $inventory): JsonResponse
    {
        $inventory->update($request->validated());

        $inventory->load('status');

        return response()->json([
            'message' => 'Inventory item updated successfully',
            'data' => $inventory
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Inventory $inventory): JsonResponse
    {
        // Check if inventory item has active rentals
        $hasActiveRentals = $inventory->rentals()
            ->whereNull('return_date')
            ->exists();

        // Check if inventory item has active reservations
        $hasActiveReservations = $inventory->reservationItems()
            ->whereHas('reservation', function ($query) {
                $query->whereHas('status', function ($q) {
                    $q->where('status_name', '!=', 'cancelled');
                });
            })
            ->exists();

        if ($hasActiveRentals || $hasActiveReservations) {
            return response()->json([
                'message' => 'Cannot delete inventory item with active rentals or reservations'
            ], 422);
        }

        $inventory->delete();

        return response()->json([
            'message' => 'Inventory item deleted successfully'
        ]);
    }
     /**
     * Get available inventory items for rental
     * Items that are not currently rented or reserved
     */
    public function getAvailableItems(Request $request): JsonResponse
    {
        $query = Inventory::with(['status'])
            ->whereHas('status', function ($q) {
                $q->where('status_name', 'available');
            });

        // Filter by item type if specified
        if ($request->has('item_type')) {
            $query->where('item_type', $request->get('item_type'));
        }

        // Filter by date range for availability check
        if ($request->has('start_date') && $request->has('end_date')) {
            $startDate = $request->get('start_date');
            $endDate = $request->get('end_date');

            // Exclude items with active rentals in the date range
            $query->whereDoesntHave('rentals', function ($q) use ($startDate, $endDate) {
                $q->where(function ($subQ) use ($startDate, $endDate) {
                    $subQ->whereBetween('rental_date', [$startDate, $endDate])
                        ->orWhereBetween('return_date', [$startDate, $endDate])
                        ->orWhere(function ($dateQ) use ($startDate, $endDate) {
                            $dateQ->where('rental_date', '<=', $startDate)
                                ->where('return_date', '>=', $endDate);
                        });
                })->whereNull('return_date');
            });

            // Exclude items with active reservations in the date range
            $query->whereDoesntHave('reservationItems', function ($q) use ($startDate, $endDate) {
                $q->whereHas('reservation', function ($resQ) use ($startDate, $endDate) {
                    $resQ->where(function ($subQ) use ($startDate, $endDate) {
                        $subQ->whereBetween('event_date', [$startDate, $endDate])
                            ->orWhereBetween('expected_return_date', [$startDate, $endDate]);
                    })->whereHas('status', function ($statusQ) {
                        $statusQ->where('status_name', '!=', 'cancelled');
                    });
                });
            });
        }

        $items = $query->get();

        return response()->json([
            'data' => $items
        ]);
    }
    /**
     * Update inventory status
     */
    public function updateStatus(Request $request, Inventory $inventory): JsonResponse
    {
        $request->validate([
            'status_id' => 'required|exists:inventory_statuses,status_id'
        ]);

        $inventory->update(['status_id' => $request->status_id]);
        $inventory->load('status');

        return response()->json([
            'message' => 'Inventory status updated successfully',
            'data' => $inventory
        ]);
    }

     /**
     * Update inventory condition
     */
    public function updateCondition(Request $request, Inventory $inventory): JsonResponse
    {
        $request->validate([
            'condition' => 'required|in:excellent,good,fair,poor'
        ]);

        $inventory->update(['condition' => $request->condition]);
        $inventory->load('status');

        return response()->json([
            'message' => 'Inventory condition updated successfully',
            'data' => $inventory
        ]);
    }

     /**
     * Get inventory statistics and dashboard data
     */
    public function getStatistics(): JsonResponse
    {
        $stats = [
            'total_items' => Inventory::count(),
            'available_items' => Inventory::whereHas('status', function ($q) {
                $q->where('status_name', 'available');
            })->count(),
            'rented_items' => Inventory::whereHas('status', function ($q) {
                $q->where('status_name', 'rented');
            })->count(),
            'under_maintenance' => Inventory::whereHas('status', function ($q) {
                $q->where('status_name', 'maintenance');
            })->count(),
            'by_item_type' => Inventory::select('item_type', DB::raw('count(*) as count'))
                ->groupBy('item_type')
                ->get(),
            'by_condition' => Inventory::select('condition', DB::raw('count(*) as count'))
                ->groupBy('condition')
                ->get(),
            'low_stock_items' => Inventory::select('item_type', DB::raw('count(*) as count'))
                ->whereHas('status', function ($q) {
                    $q->where('status_name', 'available');
                })
                ->groupBy('item_type')
                ->having('count', '<', 5)
                ->get()
        ];

        return response()->json($stats);
    }
}
