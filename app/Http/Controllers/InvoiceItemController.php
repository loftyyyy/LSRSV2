<?php

namespace App\Http\Controllers;

use App\Models\InvoiceItem;
use App\Http\Requests\StoreInvoiceItemRequest;
use App\Http\Requests\UpdateInvoiceItemRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InvoiceItemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $query = InvoiceItem::with(['invoice', 'item']);

        // Filter by invoice_id (most common use case)
        if ($request->has('invoice_id')) {
            $query->where('invoice_id', $request->get('invoice_id'));
        }

        // Filter by item_type
        if ($request->has('item_type')) {
            $query->where('item_type', $request->get('item_type'));
        }

        // Search functionality
        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where('description', 'like', "%{$search}%");
        }

        // Pagination
        $perPage = $request->get('per_page', 15);
        $invoiceItems = $query->paginate($perPage);

        return response()->json($invoiceItems);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreInvoiceItemRequest $request): JsonResponse
    {
        $invoiceItem = InvoiceItem::create($request->validated());

        $invoiceItem->load(['invoice', 'item']);

        return response()->json([
            'message' => 'Invoice item created successfully',
            'data' => $invoiceItem
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(InvoiceItem $invoiceItem): JsonResponse
    {
        $invoiceItem->load(['invoice', 'item']);

        return response()->json([
            'data' => $invoiceItem
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateInvoiceItemRequest $request, InvoiceItem $invoiceItem): JsonResponse
    {
        $invoiceItem->update($request->validated());

        $invoiceItem->load(['invoice', 'item']);

        return response()->json([
            'message' => 'Invoice item updated successfully',
            'data' => $invoiceItem
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(InvoiceItem $invoiceItem): JsonResponse
    {
        $invoiceItem->delete();

        return response()->json([
            'message' => 'Invoice item deleted successfully'
        ]);
    }
}
