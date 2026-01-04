<?php

namespace App\Http\Controllers;

use App\Models\ReservationItem;
use App\Http\Requests\StoreReservationItemRequest;
use App\Http\Requests\UpdateReservationItemRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReservationItemController extends Controller
{

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $query = ReservationItem::with(['reservation', 'item']);

        // Filter by reservation_id (most common use case)
        if ($request->has('reservation_id')) {
            $query->where('reservation_id', $request->get('reservation_id'));
        }

        // Filter by item_id
        if ($request->has('item_id')) {
            $query->where('item_id', $request->get('item_id'));
        }

        // Filter by fulfillment_status
        if ($request->has('fulfillment_status')) {
            $query->where('fulfillment_status', $request->get('fulfillment_status'));
        }

        // Pagination
        $perPage = $request->get('per_page', 15);
        $reservationItems = $query->paginate($perPage);

        return response()->json($reservationItems);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreReservationItemRequest $request): JsonResponse
    {
        $reservationItem = ReservationItem::create($request->validated());

        $reservationItem->load(['reservation', 'item']);

        return response()->json([
            'message' => 'Reservation item created successfully',
            'data' => $reservationItem
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(ReservationItem $reservationItem): JsonResponse
    {
        $reservationItem->load(['reservation', 'item']);

        return response()->json([
            'data' => $reservationItem
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateReservationItemRequest $request, ReservationItem $reservationItem): JsonResponse
    {
        $reservationItem->update($request->validated());

        $reservationItem->load(['reservation', 'item']);

        return response()->json([
            'message' => 'Reservation item updated successfully',
            'data' => $reservationItem
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ReservationItem $reservationItem): JsonResponse
    {
        $reservationItem->delete();

        return response()->json([
            'message' => 'Reservation item deleted successfully'
        ]);
    }

}
