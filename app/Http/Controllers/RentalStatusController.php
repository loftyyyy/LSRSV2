<?php

namespace App\Http\Controllers;

use App\Models\RentalStatus;
use App\Http\Requests\StoreRentalStatusRequest;
use App\Http\Requests\UpdateRentalStatusRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RentalStatusController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $query = RentalStatus::query();

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
    public function store(StoreRentalStatusRequest $request): JsonResponse
    {
        $rentalStatus = RentalStatus::create($request->validated());

        return response()->json([
            'message' => 'Rental status created successfully',
            'data' => $rentalStatus
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(RentalStatus $rentalStatus): JsonResponse
    {
        $rentalStatus->load('rentals');

        return response()->json([
            'data' => $rentalStatus
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRentalStatusRequest $request, RentalStatus $rentalStatus): JsonResponse
    {
        $rentalStatus->update($request->validated());

        return response()->json([
            'message' => 'Rental status updated successfully',
            'data' => $rentalStatus
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(RentalStatus $rentalStatus): JsonResponse
    {
        // Check if status is being used by any rentals
        $rentalCount = $rentalStatus->rentals()->count();

        if ($rentalCount > 0) {
            return response()->json([
                'message' => "Cannot delete rental status. It is currently assigned to {$rentalCount} rental(s)."
            ], 422);
        }

        $rentalStatus->delete();

        return response()->json([
            'message' => 'Rental status deleted successfully'
        ]);
    }
}
