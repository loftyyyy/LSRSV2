<?php

namespace App\Http\Controllers;

use App\Models\InventoryStatus;
use App\Http\Requests\StoreInventoryStatusRequest;
use App\Http\Requests\UpdateInventoryStatusRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InventoryStatusController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $query = InventoryStatus::query();

        // Search functionality
        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where('status_name', 'like', "%{$search}%");
        }

        // For status tables, usually return all without pagination
        // But allow pagination if requested
        if ($request->has('paginate') && $request->get('paginate') === 'true') {
            $perPage = $request->get('per_page', 15);
            $statuses = $query->paginate($perPage);
        } else {
            $statuses = $query->get();
        }

        return response()->json($statuses);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreInventoryStatusRequest $request): JsonResponse
    {
        $inventoryStatus = InventoryStatus::create($request->validated());

        return response()->json([
            'message' => 'Inventory status created successfully',
            'data' => $inventoryStatus
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(InventoryStatus $inventoryStatus): JsonResponse
    {
        $inventoryStatus->load('inventories');

        return response()->json([
            'data' => $inventoryStatus
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateInventoryStatusRequest $request, InventoryStatus $inventoryStatus): JsonResponse
    {
        $inventoryStatus->update($request->validated());

        return response()->json([
            'message' => 'Inventory status updated successfully',
            'data' => $inventoryStatus
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(InventoryStatus $inventoryStatus): JsonResponse
    {
        // Check if status is being used by any inventory items
        $inventoryCount = $inventoryStatus->inventories()->count();

        if ($inventoryCount > 0) {
            return response()->json([
                'message' => "Cannot delete inventory status. It is currently assigned to {$inventoryCount} inventory item(s)."
            ], 422);
        }

        $inventoryStatus->delete();

        return response()->json([
            'message' => 'Inventory status deleted successfully'
        ]);
    }
}
