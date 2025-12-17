<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Http\Requests\StorePaymentRequest;
use App\Http\Requests\UpdatePaymentRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PaymentController extends Controller
{

    /**
     * Display Payment Page
     */

    public function showPaymentPage(): View
    {
        return view('payments.index');
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Payment::with(['invoice', 'status', 'processedBy']);

        // Search functionality
        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('payment_reference', 'like', "%{$search}%")
                    ->orWhere('notes', 'like', "%{$search}%")
                    ->orWhereHas('invoice', function ($invoiceQuery) use ($search) {
                        $invoiceQuery->where('invoice_number', 'like', "%{$search}%");
                    });
            });
        }

        // Filter by invoice_id
        if ($request->has('invoice_id')) {
            $query->where('invoice_id', $request->get('invoice_id'));
        }

        // Filter by status
        if ($request->has('status_id')) {
            $query->where('status_id', $request->get('status_id'));
        }

        // Filter by payment_method
        if ($request->has('payment_method')) {
            $query->where('payment_method', $request->get('payment_method'));
        }

        // Filter by date range
        if ($request->has('payment_date_from')) {
            $query->where('payment_date', '>=', $request->get('payment_date_from'));
        }
        if ($request->has('payment_date_to')) {
            $query->where('payment_date', '<=', $request->get('payment_date_to'));
        }

        // Pagination
        $perPage = $request->get('per_page', 15);
        $payments = $query->paginate($perPage);

        return response()->json($payments);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePaymentRequest $request): JsonResponse
    {
        $payment = Payment::create($request->validated());

        $payment->load(['invoice', 'status', 'processedBy']);

        return response()->json([
            'message' => 'Payment created successfully',
            'data' => $payment
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Payment $payment): JsonResponse
    {
        $payment->load(['invoice', 'status', 'processedBy']);

        return response()->json([
            'data' => $payment
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePaymentRequest $request, Payment $payment): JsonResponse
    {
        $payment->update($request->validated());

        $payment->load(['invoice', 'status', 'processedBy']);

        return response()->json([
            'message' => 'Payment updated successfully',
            'data' => $payment
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Payment $payment): JsonResponse
    {
        $payment->delete();

        return response()->json([
            'message' => 'Payment deleted successfully'
        ]);
    }
}
