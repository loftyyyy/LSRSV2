<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Http\Requests\StoreCustomerRequest;
use App\Http\Requests\UpdateCustomerRequest;
use App\Models\CustomerStatus;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Throwable;

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
    public function generatePDF(Request $request)
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
        * Get all available customer statuses
        */
       public function statuses(): JsonResponse
       {
           $statuses = CustomerStatus::select('status_id', 'status_name')->get();
           return response()->json(['statuses' => $statuses]);
       }

       /**
      * Get customer statistics
      */
      public function stats(): JsonResponse
      {
          return response()->json([
              'total_customers' => Customer::count(),
              'active_customers' => Customer::whereHas('status', function ($q) {
                  $q->where('status_name', 'active');
              })->count(),
              'inactive_customers' => Customer::whereHas('status', function ($q) {
                  $q->where('status_name', 'inactive');
              })->count(),
              'customers_with_rentals' => Customer::whereHas('rentals')->count(),
          ]);
      }

      /**
       * Display a listing of the resource.
       */
       public function index(Request $request): JsonResponse
       {
           $query = Customer::with(['status'])->withCount(['rentals', 'reservations']);

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

           // Sorting functionality
          $sortBy = $request->get('sort_by', 'created_at');
          $sortOrder = $request->get('sort_order', 'desc');

          // Validate sort parameters
          $allowedSortColumns = ['first_name', 'last_name', 'email', 'contact_number', 'created_at', 'rentals_count'];
          if (!in_array($sortBy, $allowedSortColumns)) {
              $sortBy = 'created_at';
          }
          if (!in_array($sortOrder, ['asc', 'desc'])) {
              $sortOrder = 'desc';
          }

           $query->orderBy($sortBy, $sortOrder);

           // Pagination with validation
           $perPage = min($request->get('per_page', 15), 100);
          $customers = $query->paginate($perPage);

         return response()->json($customers);
     }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCustomerRequest $request): JsonResponse
    {
        try {
            DB::beginTransaction();

            $activeStatusId = CustomerStatus::where('status_name', 'active')
                ->value('status_id');

            if (!$activeStatusId) {
                return response()->json([
                    'message' => 'Active customer status not found.'
                ], 422);
            }

            $customer = Customer::create(
                array_merge(
                    $request->validated(),
                    ['status_id' => $activeStatusId]
                )
            );

            $customer->load('status');

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Customer created successfully',
                'data' => $customer
            ], 201);

        } catch (Throwable $e) {
            DB::rollBack();

            report($e); // logs to laravel.log

            return response()->json([
                'success' => false,
                'message' => 'Failed to create customer.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Customer $customer): JsonResponse
    {
        $customer->load([
            'status',
            'reservations.status',
            'rentals.status',
            'invoices.status'
        ]);

        // Calculate rental statistics
        $rentalStats = [
            'total_rentals' => $customer->rentals->count(),
            'active_rentals' => $customer->rentals->whereNull('return_date')->count(),
            'completed_rentals' => $customer->rentals->whereNotNull('return_date')->count(),
            'total_reservations' => $customer->reservations->count(),
        ];

        return response()->json([
            'data' => $customer,
            'rental_statistics' => $rentalStats
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

    /**
     * @param Customer $customer
     * @return JsonResponse
     * Deactivate customer
     */
    public function deactivate(Customer $customer): JsonResponse
    {
         // Find inactive status ID
        $inactiveStatus = \App\Models\CustomerStatus::where('status_name', 'inactive')->first();

        if (!$inactiveStatus) {
            return response()->json([
                'message' => 'Inactive status not found in system'
            ], 404);
        }

        $customer->update(['status_id' => $inactiveStatus->status_id]);
        $customer->load('status');

        return response()->json([
            'message' => 'Customer deactivated successfully',
            'data' => $customer
        ]);

    }

    /**
     * @param Customer $customer
     * @return JsonResponse
     * Reactivate a customer
     */
    public function reactivate(Customer $customer): JsonResponse
    {
         // Find active status ID
        $activeStatus = \App\Models\CustomerStatus::where('status_name', 'active')->first();

        if (!$activeStatus) {
            return response()->json([
                'message' => 'Active status not found in system'
            ], 404);
        }

        $customer->update(['status_id' => $activeStatus->status_id]);
        $customer->load('status');

        return response()->json([
            'message' => 'Customer reactivated successfully',
            'data' => $customer
        ]);

    }

    /**
     * @param Customer $customer
     * @return JsonResponse
     * Get customer rental history
     */
    public function rentalHistory(Customer $customer): JsonResponse
    {
         $rentals = $customer->rentals()
            ->with(['status', 'items.inventoryItem.item'])
            ->orderBy('rental_date', 'desc')
            ->get();

        $reservations = $customer->reservations()
            ->with(['status', 'items.inventoryItem.item'])
            ->orderBy('reservation_date', 'desc')
            ->get();

        return response()->json([
            'customer' => [
                'customer_id' => $customer->customer_id,
                'name' => $customer->first_name . ' ' . $customer->last_name,
                'email' => $customer->email,
            ],
            'rentals' => $rentals,
            'reservations' => $reservations,
        ]);

    }

}
