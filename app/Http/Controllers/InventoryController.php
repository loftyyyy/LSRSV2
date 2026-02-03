<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\InventoryStatus;
use App\Http\Requests\StoreInventoryRequest;
use App\Http\Requests\UpdateInventoryRequest;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class InventoryController extends Controller
{

    /**
     * Display Reports Page
     */
    public function report(Request $request): JsonResponse
    {
        $reportType = $request->get('report_type', 'inventory_summary');

        $reportData = match ($reportType) {
            'inventory_summary' => $this->getInventorySummaryReport($request),
            'availability_report' => $this->getAvailabilityReport($request),
            'rental_history' => $this->getRentalHistoryReport($request),
            'condition_report' => $this->getConditionReport($request),
            'revenue_by_item' => $this->getRevenueByItemReport($request),
            default => $this->getInventorySummaryReport($request)
        };

        return response()->json($reportData);
    }

    /**
     * Create PDF for reports
     */
    public function generatePDF(Request $request): JsonResponse
    {
        $reportType = $request->get('report_type', 'inventory_summary');
        $reportData = match ($reportType) {
            'inventory_summary' => $this->getInventorySummaryReport($request),
            'availability_report' => $this->getAvailabilityReport($request),
            'rental_history' => $this->getRentalHistoryReport($request),
            'condition_report' => $this->getConditionReport($request),
            'revenue_by_item' => $this->getRevenueByItemReport($request),
            default => $this->getInventorySummaryReport($request)
        };

        $pdf = PDF::loadView('reports.inventory_pdf', [
            'reportType' => $reportType,
            'data' => $reportData,
            'generatedAt' => now()->format('F d, Y h:i A')
        ]);

        return $pdf->download("inventory_report_{$reportType}_" . now()->format('Y-m-d') . ".pdf");
    }

     /**
      * Display Inventory Page
      */
     public function showInventoryPage(): View
     {
         return view('inventories.index');
     }

     /**
      * Display Inventory Reports Page
      */
     public function showReportsPage(): View
     {
         return view('inventories.reports');
     }

     /**
      * Get comprehensive inventory metrics and analytics
      */
     public function getMetrics(): JsonResponse
     {
         // Total Inventory Stats
         $totalItems = Inventory::count();
         $totalValue = Inventory::sum('purchase_price') ?? 0;

         // Status Distribution
         $availableItems = Inventory::whereHas('status', fn($q) => $q->where('status_name', 'available'))->count();
         $rentedItems = Inventory::whereHas('status', fn($q) => $q->where('status_name', 'rented'))->count();
         $damagedItems = Inventory::whereHas('status', fn($q) => $q->where('status_name', 'damaged'))->count();
         $maintenanceItems = Inventory::whereHas('status', fn($q) => $q->where('status_name', 'maintenance'))->count();

         // Condition Distribution
         $excellentCondition = Inventory::where('condition', 'excellent')->count();
         $goodCondition = Inventory::where('condition', 'good')->count();
         $fairCondition = Inventory::where('condition', 'fair')->count();
         $poorCondition = Inventory::where('condition', 'poor')->count();

         // Item Type Distribution
         $itemTypeDistribution = Inventory::selectRaw('item_type, COUNT(*) as count')
             ->groupBy('item_type')
             ->get()
             ->map(fn($item) => ['type' => $item->item_type, 'count' => $item->count])
             ->values();

         // Top Items by Rental Count
         $topItems = Inventory::with(['status', 'rentals'])
             ->withCount('rentals')
             ->orderBy('rentals_count', 'desc')
             ->limit(8)
             ->get()
             ->map(function ($item) {
                 return [
                     'item_id' => $item->item_id,
                     'name' => $item->name,
                     'sku' => $item->sku,
                     'rental_count' => $item->rentals_count,
                     'status' => $item->status->status_name ?? 'unknown',
                     'purchase_price' => $item->purchase_price,
                 ];
             });

         // Value Distribution by Item Type
         $valueByType = Inventory::selectRaw('item_type, SUM(purchase_price) as total_value, COUNT(*) as count')
             ->groupBy('item_type')
             ->orderBy('total_value', 'desc')
             ->limit(8)
             ->get()
             ->map(fn($item) => [
                 'type' => $item->item_type,
                 'value' => round($item->total_value, 2),
                 'count' => $item->count,
             ])
             ->values();

         // Monthly Rental Activity
         $monthlyRentals = collect();
         for ($i = 11; $i >= 0; $i--) {
             $month = now()->subMonths($i);
             $count = Inventory::whereHas('rentals', function ($q) use ($month) {
                 $q->whereMonth('created_at', $month->month)
                   ->whereYear('created_at', $month->year);
             })->distinct()->count();

             $monthlyRentals->push([
                 'month' => $month->format('M'),
                 'count' => $count,
             ]);
         }

         // Condition Distribution for Chart
         $conditionDistribution = [
             ['condition' => 'Excellent', 'count' => $excellentCondition],
             ['condition' => 'Good', 'count' => $goodCondition],
             ['condition' => 'Fair', 'count' => $fairCondition],
             ['condition' => 'Poor', 'count' => $poorCondition],
         ];

         // Status Distribution for Chart
         $statusDistribution = [
             ['status' => 'Available', 'count' => $availableItems],
             ['status' => 'Rented', 'count' => $rentedItems],
             ['status' => 'Maintenance', 'count' => $maintenanceItems],
             ['status' => 'Damaged', 'count' => $damagedItems],
         ];

         return response()->json([
             'kpis' => [
                 'total_items' => $totalItems,
                 'total_value' => round($totalValue, 2),
                 'available_items' => $availableItems,
                 'rented_items' => $rentedItems,
                 'damaged_items' => $damagedItems,
                 'maintenance_items' => $maintenanceItems,
                 'excellent_condition' => $excellentCondition,
                 'good_condition' => $goodCondition,
                 'fair_condition' => $fairCondition,
                 'poor_condition' => $poorCondition,
                 'occupancy_rate' => $totalItems > 0 ? round(($rentedItems / $totalItems) * 100, 2) : 0,
                 'value_at_risk' => round(Inventory::whereIn('status_id', [
                     Inventory::whereHas('status', fn($q) => $q->where('status_name', 'damaged'))->first()?->status_id ?? 0,
                 ])->sum('purchase_price'), 2),
             ],
             'top_items' => $topItems,
             'item_type_distribution' => $itemTypeDistribution,
             'value_by_type' => $valueByType,
             'monthly_rentals' => $monthlyRentals,
             'condition_distribution' => $conditionDistribution,
             'status_distribution' => $statusDistribution,
             'generated_at' => now()->format('Y-m-d H:i:s'),
         ]);
     }

     /**
      * Display a listing of the resource.
      */
    public function index(Request $request): JsonResponse
    {
        // Load primary image with inventory items
        $query = Inventory::with(['status', 'images' => function ($q) {
            $q->where('is_primary', true)
                ->orWhereIn('image_id', function ($subQ) {
                    $subQ->selectRaw('MIN(image_id)')
                        ->from('inventory_images')
                        ->groupBy('item_id')
                        ->havingRaw('MIN(is_primary) = 0');
                });
        }]);

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
         if ($request->has('status')) {
             $statusName = $request->get('status');
             $query->whereHas('status', function ($q) use ($statusName) {
                 $q->where('status_name', $statusName);
             });
         }

        // Filter items with/without images
        if ($request->has('has_images')) {
            if ($request->get('has_images') === 'true') {
                $query->has('images');
            } else {
                $query->doesntHave('images');
            }
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
         $data = $request->validated();
         
         // Set default status to 'available' if not provided
         if (empty($data['status_id'])) {
             $availableStatus = InventoryStatus::where('status_name', 'available')->first();
             if ($availableStatus) {
                 $data['status_id'] = $availableStatus->status_id;
             }
         }
         
         $inventory = Inventory::create($data);
         
         $inventory->load('status');

         return response()->json([
             'success' => true,
             'message' => 'Inventory item created successfully',
             'data' => $inventory
         ], 201);
     }

    /**
     * Display the specified resource.
     */
    public function show(Inventory $inventory): JsonResponse
    {
        $inventory->load([
            'status',
            'rentals',
            'reservationItems',
            'invoiceItems',
            'images' => function ($q) {
                $q->orderBy('is_primary', 'desc')
                    ->orderBy('display_order', 'asc');
            }
        ]);

        // Group images by view type for easy access
        $imagesByView = $inventory->images->groupBy('view_type');

        return response()->json([
            'data' => $inventory,
            'images_summary' => [
                'total_images' => $inventory->images->count(),
                'primary_image' => $inventory->images->where('is_primary', true)->first(),
                'by_view_type' => [
                    'front' => $imagesByView->get('front', collect())->count(),
                    'back' => $imagesByView->get('back', collect())->count(),
                    'side' => $imagesByView->get('side', collect())->count(),
                    'detail' => $imagesByView->get('detail', collect())->count(),
                    'full' => $imagesByView->get('full', collect())->count(),
                ],
                'missing_required_views' => $this->getMissingRequiredViews($inventory)
            ]
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

        // Delete all associated images
        $images = $inventory->images;
        foreach ($images as $image) {
            if (Storage::disk('public')->exists($image->image_path)) {
                Storage::disk('public')->delete($image->image_path);
            }
        }
        $inventory->images()->delete();

        // Delete the inventory folder
        $folderPath = 'inventory/' . $inventory->item_id;
        if (Storage::disk('public')->exists($folderPath)) {
            Storage::disk('public')->deleteDirectory($folderPath);
        }

        $inventory->delete();

        return response()->json([
            'message' => 'Inventory item and all associated images deleted successfully'
        ]);
    }

    /**
     * Get available inventory items for rental
     * Items that are not currently rented or reserved
     */
    public function getAvailableItems(Request $request): JsonResponse
    {
        $query = Inventory::with(['status', 'images' => function ($q) {
            $q->where('is_primary', true);
        }])
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
            'inventory_value' => Inventory::sum('rental_price') ?? 0,
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
                ->get(),
            'items_without_images' => Inventory::doesntHave('images')->count(),
            'items_missing_required_views' => $this->getItemsMissingRequiredViews()
        ];

        return response()->json($stats);
    }

    /**
     * Get inventory summary report
     */
    private function getInventorySummaryReport(Request $request): array
    {
        $query = Inventory::with(['status', 'images']);

        if ($request->has('item_type')) {
            $query->where('item_type', $request->get('item_type'));
        }

        $inventories = $query->get();

        return [
            'title' => 'Inventory Summary Report',
            'items' => $inventories,
            'total_count' => $inventories->count(),
            'total_value' => $inventories->sum('rental_price'),
            'by_status' => $inventories->groupBy('status.status_name'),
            'by_condition' => $inventories->groupBy('condition'),
            'items_with_complete_images' => $inventories->filter(function ($item) {
                return $this->hasCompleteImages($item);
            })->count()
        ];
    }

    /**
     * Get availability report
     */
    private function getAvailabilityReport(Request $request): array
    {
        $availableItems = Inventory::with(['status', 'images'])
            ->whereHas('status', function ($q) {
                $q->where('status_name', 'available');
            })
            ->get();

        return [
            'title' => 'Availability Report',
            'available_items' => $availableItems,
            'total_available' => $availableItems->count(),
            'by_type' => $availableItems->groupBy('item_type'),
            'by_size' => $availableItems->groupBy('size')
        ];
    }

    /**
     * Get rental history report
     */
    private function getRentalHistoryReport(Request $request): array
    {
        $query = Inventory::with(['rentals.customer', 'images']);

        if ($request->has('start_date') && $request->has('end_date')) {
            $query->whereHas('rentals', function ($q) use ($request) {
                $q->whereBetween('rental_date', [
                    $request->get('start_date'),
                    $request->get('end_date')
                ]);
            });
        }

        $inventories = $query->get();

        return [
            'title' => 'Rental History Report',
            'items' => $inventories,
            'total_rentals' => $inventories->sum(function ($item) {
                return $item->rentals->count();
            })
        ];
    }

    /**
     * Get condition report
     */
    private function getConditionReport(Request $request): array
    {
        $items = Inventory::with(['status', 'images'])->get();

        return [
            'title' => 'Condition Report',
            'items' => $items,
            'by_condition' => $items->groupBy('condition'),
            'maintenance_needed' => $items->where('condition', 'poor')
        ];
    }

    /**
     * Get revenue by item report
     */
    private function getRevenueByItemReport(Request $request): array
    {
        $query = Inventory::with(['rentals', 'invoiceItems', 'images']);

        if ($request->has('start_date') && $request->has('end_date')) {
            $startDate = $request->get('start_date');
            $endDate = $request->get('end_date');

            $query->whereHas('rentals', function ($q) use ($startDate, $endDate) {
                $q->whereBetween('rental_date', [$startDate, $endDate]);
            });
        }

        $inventories = $query->get()->map(function ($item) {
            $rentalCount = $item->rentals->count();
            $totalRevenue = $item->invoiceItems->sum('subtotal');

            return [
                'item' => $item,
                'rental_count' => $rentalCount,
                'total_revenue' => $totalRevenue,
                'average_revenue' => $rentalCount > 0 ? $totalRevenue / $rentalCount : 0
            ];
        })->sortByDesc('total_revenue');

        return [
            'title' => 'Revenue by Item Report',
            'items' => $inventories,
            'total_revenue' => $inventories->sum('total_revenue'),
            'total_rentals' => $inventories->sum('rental_count')
        ];
    }

    /**
     * Bulk update inventory status
     */
    public function bulkUpdateStatus(Request $request): JsonResponse
    {
        $request->validate([
            'item_ids' => 'required|array',
            'item_ids.*' => 'exists:inventories,item_id',
            'status_id' => 'required|exists:inventory_statuses,status_id'
        ]);

        $updated = Inventory::whereIn('item_id', $request->item_ids)
            ->update(['status_id' => $request->status_id]);

        return response()->json([
            'message' => "Successfully updated {$updated} items",
            'updated_count' => $updated
        ]);
    }

    /**
     * Check item availability for specific dates
     */
    public function checkAvailability(Request $request, Inventory $inventory): JsonResponse
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date'
        ]);

        $startDate = $request->start_date;
        $endDate = $request->end_date;

        // Check for conflicting rentals
        $hasRentals = $inventory->rentals()
            ->where(function ($q) use ($startDate, $endDate) {
                $q->whereBetween('rental_date', [$startDate, $endDate])
                    ->orWhereBetween('return_date', [$startDate, $endDate])
                    ->orWhere(function ($dateQ) use ($startDate, $endDate) {
                        $dateQ->where('rental_date', '<=', $startDate)
                            ->where('return_date', '>=', $endDate);
                    });
            })
            ->whereNull('return_date')
            ->exists();

        // Check for conflicting reservations
        $hasReservations = $inventory->reservationItems()
            ->whereHas('reservation', function ($q) use ($startDate, $endDate) {
                $q->where(function ($dateQ) use ($startDate, $endDate) {
                    $dateQ->whereBetween('event_date', [$startDate, $endDate])
                        ->orWhereBetween('expected_return_date', [$startDate, $endDate]);
                })->whereHas('status', function ($statusQ) {
                    $statusQ->where('status_name', '!=', 'cancelled');
                });
            })
            ->exists();

        $isAvailable = !$hasRentals && !$hasReservations && $inventory->status->status_name === 'available';

        return response()->json([
            'available' => $isAvailable,
            'item' => $inventory,
            'conflicts' => [
                'has_rentals' => $hasRentals,
                'has_reservations' => $hasReservations
            ]
        ]);
    }

    /**
     * Get items missing required image views
     */
    public function getItemsMissingImages(): JsonResponse
    {
        $items = Inventory::with(['images', 'status'])
            ->get()
            ->filter(function ($item) {
                return !$this->hasCompleteImages($item);
            })
            ->map(function ($item) {
                return [
                    'item' => $item,
                    'missing_views' => $this->getMissingRequiredViews($item)
                ];
            });

        return response()->json([
            'data' => $items->values(),
            'total_items_missing_images' => $items->count()
        ]);
    }

    /**
     * Helper: Check if item has all required image views
     */
    private function hasCompleteImages(Inventory $inventory): bool
    {
        $requiredViews = ['front', 'back', 'side'];
        $existingViews = $inventory->images->pluck('view_type')->toArray();

        foreach ($requiredViews as $view) {
            if (!in_array($view, $existingViews)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Helper: Get missing required views for an item
     */
    private function getMissingRequiredViews(Inventory $inventory): array
    {
        $requiredViews = ['front', 'back', 'side'];
        $existingViews = $inventory->images->pluck('view_type')->toArray();

        return array_values(array_diff($requiredViews, $existingViews));
    }

    /**
     * Helper: Get count of items missing required views
     */
    private function getItemsMissingRequiredViews(): int
    {
        return Inventory::with('images')
            ->get()
            ->filter(function ($item) {
                return !$this->hasCompleteImages($item);
            })
            ->count();
    }
}
