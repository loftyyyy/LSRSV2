<?php

namespace App\Http\Controllers;

use App\Models\ReservationStatus;
use App\Http\Requests\StoreReservationStatusRequest;
use App\Http\Requests\UpdateReservationStatusRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReservationStatusController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $query = ReservationStatus::query();

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
    public function store(StoreReservationStatusRequest $request): JsonResponse
    {
        $reservationStatus = ReservationStatus::create($request->validated());

        return response()->json([
            'message' => 'Reservation status created successfully',
            'data' => $reservationStatus
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(ReservationStatus $reservationStatus): JsonResponse
    {
        $reservationStatus->load('reservations');

        return response()->json([
            'data' => $reservationStatus
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateReservationStatusRequest $request, ReservationStatus $reservationStatus): JsonResponse
    {
        $reservationStatus->update($request->validated());

        return response()->json([
            'message' => 'Reservation status updated successfully',
            'data' => $reservationStatus
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ReservationStatus $reservationStatus): JsonResponse
    {
        // Check if status is being used by any reservations
        $reservationCount = $reservationStatus->reservations()->count();

        if ($reservationCount > 0) {
            return response()->json([
                'message' => "Cannot delete reservation status. It is currently assigned to {$reservationCount} reservation(s)."
            ], 422);
        }

        $reservationStatus->delete();

        return response()->json([
            'message' => 'Reservation status deleted successfully'
        ]);
    }
}
