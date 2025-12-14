<?php

namespace App\Http\Controllers;

use App\Models\PaymentStatus;
use App\Http\Requests\StorePaymentStatusRequest;
use App\Http\Requests\UpdatePaymentStatusRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PaymentStatusController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $query = PaymentStatus::query();

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
    public function store(StorePaymentStatusRequest $request): JsonResponse
    {
        $paymentStatus = PaymentStatus::create($request->validated());

        return response()->json([
            'message' => 'Payment status created successfully',
            'data' => $paymentStatus
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(PaymentStatus $paymentStatus): JsonResponse
    {
        $paymentStatus->load('payments');

        return response()->json([
            'data' => $paymentStatus
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePaymentStatusRequest $request, PaymentStatus $paymentStatus): JsonResponse
    {
        $paymentStatus->update($request->validated());

        return response()->json([
            'message' => 'Payment status updated successfully',
            'data' => $paymentStatus
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PaymentStatus $paymentStatus): JsonResponse
    {
        // Check if status is being used by any payments
        $paymentCount = $paymentStatus->payments()->count();

        if ($paymentCount > 0) {
            return response()->json([
                'message' => "Cannot delete payment status. It is currently assigned to {$paymentCount} payment(s)."
            ], 422);
        }

        $paymentStatus->delete();

        return response()->json([
            'message' => 'Payment status deleted successfully'
        ]);
    }
}
