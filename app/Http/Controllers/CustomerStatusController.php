<?php

namespace App\Http\Controllers;

use App\Models\CustomerStatus;
use App\Http\Requests\StoreCustomerStatusRequest;
use App\Http\Requests\UpdateCustomerStatusRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CustomerStatusController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $query = CustomerStatus::query();

        // Search functionality
        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('status_name', 'like', "%{$search}%")
                    ->orWhere('reason', 'like', "%{$search}%");
            });
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
    public function store(StoreCustomerStatusRequest $request): JsonResponse
    {
        $customerStatus = CustomerStatus::create($request->validated());

        return response()->json([
            'message' => 'Customer status created successfully',
            'data' => $customerStatus
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(CustomerStatus $customerStatus): JsonResponse
    {
        $customerStatus->load('customers');

        return response()->json([
            'data' => $customerStatus
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCustomerStatusRequest $request, CustomerStatus $customerStatus): JsonResponse
    {
        $customerStatus->update($request->validated());

        return response()->json([
            'message' => 'Customer status updated successfully',
            'data' => $customerStatus
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CustomerStatus $customerStatus): JsonResponse
    {
        // Check if status is being used by any customers
        $customerCount = $customerStatus->customers()->count();

        if ($customerCount > 0) {
            return response()->json([
                'message' => "Cannot delete customer status. It is currently assigned to {$customerCount} customer(s)."
            ], 422);
        }

        $customerStatus->delete();

        return response()->json([
            'message' => 'Customer status deleted successfully'
        ]);
    }
}
