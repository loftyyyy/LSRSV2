<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Http\Requests\StoreCustomerRequest;
use App\Http\Requests\UpdateCustomerRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CustomerController extends Controller
{
    /**
     * Display Reports for Customer
     */
    public function report(Request $request):JsonResponse
    {
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        $statusId = $request->get('status_id');

        $query = Customer::with(['status', 'rentals', 'reservations']);

        // Filter by date range
        if ($startDate && $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }

        // Filter by status
        if ($statusId) {
            $query->where('status_id', $statusId);
        }

        $customers = $query->get();

        // Generate report statistics
        $statistics = [
            'total_customers' => $customers->count(),
            'active_customers' => $customers->where('status.status_name', 'active')->count(),
            'inactive_customers' => $customers->where('status.status_name', 'inactive')->count(),
            'total_rentals' => $customers->sum(fn($c) => $c->rentals->count()),
            'customers_with_rentals' => $customers->filter(fn($c) => $c->rentals->count() > 0)->count(),
        ];

        // Customer rental history summary
        $customerData = $customers->map(function ($customer) {
            return [
                'customer_id' => $customer->customer_id,
                'name' => $customer->first_name . ' ' . $customer->last_name,
                'email' => $customer->email,
                'contact_number' => $customer->contact_number,
                'status' => $customer->status->status_name ?? 'N/A',
                'total_rentals' => $customer->rentals->count(),
                'total_reservations' => $customer->reservations->count(),
                'registration_date' => $customer->created_at->format('Y-m-d'),
                'last_rental_date' => $customer->rentals->max('rental_date'),
            ];
        });

        return response()->json([
            'statistics' => $statistics,
            'customers' => $customerData,
            'generated_at' => now()->format('Y-m-d H:i:s'),
        ]);

    }

    /**
     * Download PDF
     */
    public function generatePDF(Request $request):JsonResponse
    {
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        $statusId = $request->get('status_id');

        $query = Customer::with(['status', 'rentals', 'reservations']);

        if ($startDate && $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }

        if ($statusId) {
            $query->where('status_id', $statusId);
        }

        $customers = $query->get();

        $statistics = [
            'total_customers' => $customers->count(),
            'active_customers' => $customers->where('status.status_name', 'active')->count(),
            'inactive_customers' => $customers->where('status.status_name', 'inactive')->count(),
            'total_rentals' => $customers->sum(fn($c) => $c->rentals->count()),
        ];

        $customerData = $customers->map(function ($customer) {
            return [
                'name' => $customer->first_name . ' ' . $customer->last_name,
                'email' => $customer->email,
                'contact_number' => $customer->contact_number,
                'status' => $customer->status->status_name ?? 'N/A',
                'total_rentals' => $customer->rentals->count(),
                'registration_date' => $customer->created_at->format('Y-m-d'),
            ];
        });

        $pdf = PDF::loadView('customers.report-pdf', [
            'customers' => $customerData,
            'statistics' => $statistics,
            'date_range' => [
                'start' => $startDate,
                'end' => $endDate
            ],
            'generated_at' => now()->format('Y-m-d H:i:s')
        ]);

        return $pdf->download('customer-report-' . now()->format('Y-m-d') . '.pdf');
    }
    /**
     * Display the Customer Page
     */
    public function showCustomerPage(): View
    {
        return view('customers.index');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Customer::with(['status']);

        // Search functionality
        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('contact_number', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->has('status_id')) {
            $query->where('status_id', $request->get('status_id'));
        }

        // Pagination
        $perPage = $request->get('per_page', 15);
        $customers = $query->paginate($perPage);

        return response()->json($customers);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCustomerRequest $request): JsonResponse
    {
        $customer = Customer::create($request->validated());

        $customer->load('status');

        return response()->json([
            'message' => 'Customer created successfully',
            'data' => $customer
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Customer $customer): JsonResponse
    {
        $customer->load(['status', 'reservations', 'rentals', 'invoices']);

        return response()->json([
            'data' => $customer
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCustomerRequest $request, Customer $customer): JsonResponse
    {
        $customer->update($request->validated());

        $customer->load('status');

        return response()->json([
            'message' => 'Customer updated successfully',
            'data' => $customer
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Customer $customer): JsonResponse
    {
        // Check if customer has active rentals or reservations
        $hasActiveRentals = $customer->rentals()
            ->whereNull('return_date')
            ->exists();

        $hasActiveReservations = $customer->reservations()
            ->whereHas('status', function ($query) {
                $query->where('status_name', '!=', 'cancelled');
            })
            ->exists();

        if ($hasActiveRentals || $hasActiveReservations) {
            return response()->json([
                'message' => 'Cannot delete customer with active rentals or reservations'
            ], 422);
        }

        $customer->delete();

        return response()->json([
            'message' => 'Customer deleted successfully'
        ]);
    }
}
