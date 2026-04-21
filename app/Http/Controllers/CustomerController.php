<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCustomerRequest;
use App\Http\Requests\UpdateCustomerRequest;
use App\Models\Customer;
use App\Models\CustomerStatus;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Throwable;

/**
 * Controller handling customer management operations.
 * 
 * This controller manages all customer-related functionality including:
 * - Customer CRUD operations (Create, Read, Update, Delete)
 * - Customer status management (activation/deactivation)
 * - Customer reports and analytics
 * - Customer rental and reservation history
 * - PDF and CSV report generation
 */
class CustomerController extends Controller
{
    /**
     * Generate customer reports based on filters.
     * 
     * Creates comprehensive reports showing customer statistics and data.
     * Can be filtered by date range and customer status.
     * 
     * @param \Illuminate\Http\Request $request The HTTP request containing filter parameters
     * @return \Illuminate\Http\JsonResponse JSON response with report data
     */
    public function report(Request $request): JsonResponse
    {
        // Get filter parameters from request
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        $statusId = $request->get('status_id');

        // Base query for customer data
        $baseQuery = Customer::query();

        // Apply date range filter if provided
        if ($startDate && $endDate) {
            $baseQuery->whereBetween('created_at', [$startDate, $endDate]);
        }

        // Apply status filter if provided
        if ($statusId) {
            $baseQuery->where('status_id', $statusId);
        }

        // Generate report statistics using efficient DB queries
        // Clone query to avoid modifying original query object
        $statistics = [
            'total_customers' => (clone $baseQuery)->count(),
            'active_customers' => (clone $baseQuery)->whereHas('status', fn ($q) => $q->whereRaw('LOWER(status_name) = ?', ['active']))->count(),
            'inactive_customers' => (clone $baseQuery)->whereHas('status', fn ($q) => $q->whereRaw('LOWER(status_name) = ?', ['inactive']))->count(),
            'total_rentals' => \App\Models\Rental::whereIn('customer_id', (clone $baseQuery)->select('customer_id'))->count(),
            'customers_with_rentals' => (clone $baseQuery)->has('rentals')->count(),
            'total_reservations' => \App\Models\Reservation::whereIn('customer_id', (clone $baseQuery)->select('customer_id'))->count(),
        ];

        // Fetch customers with related data and aggregates
        // Using clone to preserve original query for statistics
        $customers = (clone $baseQuery)
            ->with('status')                    // Load customer status relationship
            ->withCount(['rentals', 'reservations']) // Count rentals and reservations per customer
            ->withMax('rentals', 'released_date')    // Get most recent rental date
            ->get();

        // Transform customer data for API response
        $customerData = $customers->map(function ($customer) {
            return [
                'customer_id' => $customer->customer_id,
                'name' => $customer->first_name.' '.$customer->last_name,
                'email' => $customer->email,
                'contact_number' => $customer->contact_number,
                'status' => $customer->status->status_name ?? 'N/A',
                'total_rentals' => $customer->rentals_count,
                'total_reservations' => $customer->reservations_count,
                'registration_date' => $customer->created_at->format('Y-m-d'),
                'last_rental_date' => $customer->rentals_max_released_date,
            ];
        });

        // Return JSON response with statistics and customer data
        return response()->json([
            'statistics' => $statistics,
            'customers' => $customerData,
            'generated_at' => now()->format('Y-m-d H:i:s'),
        ]);
    }

        // Filter by status
        if ($statusId) {
            $baseQuery->where('status_id', $statusId);
        }

        // Generate report statistics using DB queries instead of memory
        $statistics = [
            'total_customers' => (clone $baseQuery)->count(),
            'active_customers' => (clone $baseQuery)->whereHas('status', fn ($q) => $q->whereRaw('LOWER(status_name) = ?', ['active']))->count(),
            'inactive_customers' => (clone $baseQuery)->whereHas('status', fn ($q) => $q->whereRaw('LOWER(status_name) = ?', ['inactive']))->count(),
            'total_rentals' => \App\Models\Rental::whereIn('customer_id', (clone $baseQuery)->select('customer_id'))->count(),
            'customers_with_rentals' => (clone $baseQuery)->has('rentals')->count(),
            'total_reservations' => \App\Models\Reservation::whereIn('customer_id', (clone $baseQuery)->select('customer_id'))->count(),
        ];

        // Fetch customers with aggregated data to avoid N+1 and loading huge relations
        $customers = (clone $baseQuery)
            ->with('status')
            ->withCount(['rentals', 'reservations'])
            ->withMax('rentals', 'released_date')
            ->get();

        // Customer rental history summary
        $customerData = $customers->map(function ($customer) {
            return [
                'customer_id' => $customer->customer_id,
                'name' => $customer->first_name.' '.$customer->last_name,
                'email' => $customer->email,
                'contact_number' => $customer->contact_number,
                'status' => $customer->status->status_name ?? 'N/A',
                'total_rentals' => $customer->rentals_count,
                'total_reservations' => $customer->reservations_count,
                'registration_date' => $customer->created_at->format('Y-m-d'),
                'last_rental_date' => $customer->rentals_max_released_date,
            ];
        });

        return response()->json([
            'statistics' => $statistics,
            'customers' => $customerData,
            'generated_at' => now()->format('Y-m-d H:i:s'),
        ]);

    }

    /**
     * Generate PDF report for customers.
     * 
     * Creates a downloadable PDF report containing customer data and statistics.
     * Can be filtered by date range and customer status.
     * 
     * @param \Illuminate\Http\Request $request The HTTP request containing filter parameters
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse PDF download response
     */
    public function generatePDF(Request $request)
    {
        // Get filter parameters from request
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        $statusId = $request->get('status_id');

        // Base query for customer data
        $baseQuery = Customer::query();

        // Apply date range filter if provided
        if ($startDate && $endDate) {
            $baseQuery->whereBetween('created_at', [$startDate, $endDate]);
        }

        // Apply status filter if provided
        if ($statusId) {
            $baseQuery->where('status_id', $statusId);
        }

        // Generate report statistics
        $statistics = [
            'total_customers' => (clone $baseQuery)->count(),
            'active_customers' => (clone $baseQuery)->whereHas('status', fn ($q) => $q->whereRaw('LOWER(status_name) = ?', ['active']))->count(),
            'inactive_customers' => (clone $baseQuery)->whereHas('status', fn ($q) => $q->whereRaw('LOWER(status_name) = ?', ['inactive']))->count(),
            'total_rentals' => \App\Models\Rental::whereIn('customer_id', (clone $baseQuery)->select('customer_id'))->count(),
        ];

        // Fetch customers with related data
        $customers = (clone $baseQuery)
            ->with('status')                    // Load customer status relationship
            ->withCount('rentals')              // Count rentals per customer
            ->get();

        // Transform customer data for PDF view
        $customerData = $customers->map(function ($customer) {
            return [
                'name' => $customer->first_name.' '.$customer->last_name,
                'email' => $customer->email,
                'contact_number' => $customer->contact_number,
                'status' => $customer->status->status_name ?? 'N/A',
                'total_rentals' => $customer->rentals_count,
                'registration_date' => $customer->created_at->format('Y-m-d'),
            ];
        });

        // Load PDF view with data
        $pdf = PDF::loadView('customers.report-pdf', [
            'customers' => $customerData,
            'statistics' => $statistics,
            'date_range' => [
                'start' => $startDate,
                'end' => $endDate,
            ],
            'generated_at' => now()->format('Y-m-d H:i:s'),
        ]);

        // Return PDF download with timestamped filename
        return $pdf->download('customer-report-'.now()->format('Y-m-d').'.pdf');
    }

    /**
     * Generate CSV report for customers.
     * 
     * Creates a downloadable CSV report containing customer data and statistics.
     * Can be filtered by date range and customer status.
     * 
     * @param \Illuminate\Http\Request $request The HTTP request containing filter parameters
     * @return \Symfony\Component\HttpFoundation\StreamedResponse CSV download response
     */
    public function generateCSV(Request $request)
    {
        // Get filter parameters from request
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        $statusId = $request->get('status_id');

        // Base query for customer data
        $baseQuery = Customer::query();

        // Apply date range filter if provided
        if ($startDate && $endDate) {
            $baseQuery->whereBetween('created_at', [$startDate, $endDate]);
        }

        // Apply status filter if provided
        if ($statusId) {
            $baseQuery->where('status_id', $statusId);
        }

        // Generate report statistics
        $statistics = [
            'total_customers' => (clone $baseQuery)->count(),
            'active_customers' => (clone $baseQuery)->whereHas('status', fn ($q) => $q->whereRaw('LOWER(status_name) = ?', ['active']))->count(),
            'inactive_customers' => (clone $baseQuery)->whereHas('status', fn ($q) => $q->whereRaw('LOWER(status_name) = ?', ['inactive']))->count(),
            'total_rentals' => \App\Models\Rental::whereIn('customer_id', (clone $baseQuery)->select('customer_id'))->count(),
            'customers_with_rentals' => (clone $baseQuery)->has('rentals')->count(),
            'total_reservations' => \App\Models\Reservation::whereIn('customer_id', (clone $baseQuery)->select('customer_id'))->count(),
        ];

        // Fetch customers with related data and aggregates
        $customers = (clone $baseQuery)
            ->with('status')                    // Load customer status relationship
            ->withCount(['rentals', 'reservations']) // Count rentals and reservations per customer
            ->withMax('rentals', 'released_date')    // Get most recent rental date
            ->get();

        // Transform customer data for CSV export
        $customerData = $customers->map(function ($customer) {
            return [
                'customer_id' => $customer->customer_id,
                'name' => $customer->first_name.' '.$customer->last_name,
                'email' => $customer->email,
                'contact_number' => $customer->contact_number,
                'status' => $customer->status->status_name ?? 'N/A',
                'total_rentals' => $customer->rentals_count,
                'total_reservations' => $customer->reservations_count,
                'registration_date' => $customer->created_at->format('Y-m-d'),
                'last_rental_date' => $customer->rentals_max_released_date ? \Carbon\Carbon::parse($customer->rentals_max_released_date)->format('Y-m-d') : '',
            ];
        });

        // Set headers for CSV download
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="customer-report-'.now()->format('Y-m-d').'.csv"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        // Create CSV output callback
        $callback = function () use ($customerData, $statistics, $startDate, $endDate) {
            $output = fopen('php://output', 'w');
            // Add BOM for UTF-8 encoding support
            fputs($output, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // Add report header information
            fputcsv($output, ['Customer Report']);
            fputcsv($output, ['Generated at', now()->format('Y-m-d H:i:s')]);
            fputcsv($output, ['Date Range', $startDate.' to '.$endDate]);
            fputcsv($output, []); // Empty row for spacing

            // Add report statistics section
            fputcsv($output, ['Report Statistics']);
            fputcsv($output, ['Total Customers', $statistics['total_customers']]);
            fputcsv($output, ['Active Customers', $statistics['active_customers']]);
            fputcsv($output, ['Inactive Customers', $statistics['inactive_customers']]);
            fputcsv($output, ['Total Rentals', $statistics['total_rentals']]);
            fputcsv($output, ['Customers with Rentals', $statistics['customers_with_rentals']]);
            fputcsv($output, ['Total Reservations', $statistics['total_reservations']]);
            fputcsv($output, []); // Empty row for spacing

            // Add customer data header
            fputcsv($output, ['Customer Details']);
            fputcsv($output, [
                'Customer ID',
                'Name',
                'Email',
                'Contact Number',
                'Status',
                'Total Rentals',
                'Total Reservations',
                'Registration Date',
                'Last Rental Date',
            ]);

            // Add customer data rows
            foreach ($customerData as $customer) {
                fputcsv($output, [
                    $customer['customer_id'],
                    $customer['name'],
                    $customer['email'],
                    $customer['contact_number'],
                    $customer['status'],
                    $customer['total_rentals'],
                    $customer['total_reservations'],
                    $customer['registration_date'],
                    $customer['last_rental_date'],
                ]);
            }

            // Close output handle
            fclose($output);
        };

        // Return streaming response for CSV download
        return response()->stream($callback, 200, $headers);
    }

    /**
     * Display the customer management page.
     * 
     * @return \Illuminate\View\View The customer index view
     */
    public function showCustomerPage(): View
    {
        // Return the main customer management view
        return view('customers.index');
    }

    /**
     * Display the customer reports page.
     * 
     * @return \Illuminate\View\View The customer reports view
     */
    public function showReportsPage(): View
    {
        // Return the customer reports view
        return view('customers.reports');
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
                $q->whereRaw('LOWER(status_name) = ?', ['active']);
            })->count(),
            'inactive_customers' => Customer::whereHas('status', function ($q) {
                $q->whereRaw('LOWER(status_name) = ?', ['inactive']);
            })->count(),
            'customers_with_rentals' => Customer::whereHas('rentals')->count(),
        ]);
    }

    /**
     * Get customer registration trend data (monthly breakdown)
     */
    public function getRegistrationTrend(): JsonResponse
    {
        // Get the last 6 months of data (or all data if less than 6 months)
        $sixMonthsAgo = now()->subMonths(6)->startOfMonth();

        // Group customers by month of registration (MySQL compatible)
        $registrationData = Customer::where('created_at', '>=', $sixMonthsAgo)
            ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as month, COUNT(*) as count")
            ->groupBy('month')
            ->orderBy('month', 'asc')
            ->get();

        // Create array of all months in range for consistent display
        $months = [];
        $data = [];
        $currentDate = $sixMonthsAgo->copy();

        while ($currentDate <= now()) {
            $monthKey = $currentDate->format('Y-m');
            $months[] = $currentDate->format('M');

            // Find data for this month
            $monthData = $registrationData->firstWhere('month', $monthKey);

            $data[] = $monthData ? (int) $monthData->count : 0;
            $currentDate->addMonth();
        }

        return response()->json([
            'months' => $months,
            'data' => $data,
            'total_registered' => Customer::count(),
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

                // Search by customer_id if search is numeric
                if (is_numeric($search)) {
                    $q->orWhere('customer_id', '=', (int) $search);
                }
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
        if (! in_array($sortBy, $allowedSortColumns)) {
            $sortBy = 'created_at';
        }
        if (! in_array($sortOrder, ['asc', 'desc'])) {
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

            $activeStatusId = CustomerStatus::whereRaw('LOWER(status_name) = ?', ['active'])
                ->value('status_id');

            if (! $activeStatusId) {
                return response()->json([
                    'message' => 'Active customer status not found.',
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
                'data' => $customer,
            ], 201);

        } catch (Throwable $e) {
            DB::rollBack();

            report($e); // logs to laravel.log

            return response()->json([
                'success' => false,
                'message' => 'Failed to create customer.',
                'error' => $e->getMessage(),
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
            'invoices.status',
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
            'rental_statistics' => $rentalStats,
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
            'data' => $customer,
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
                'message' => 'Cannot delete customer with active rentals or reservations',
            ], 422);
        }

        $customer->delete();

        return response()->json([
            'message' => 'Customer deleted successfully',
        ]);
    }

    /**
     * @return JsonResponse
     *                      Deactivate customer
     */
    public function deactivate(Customer $customer): JsonResponse
    {
        // Find inactive status ID (case-insensitive)
        $inactiveStatus = \App\Models\CustomerStatus::whereRaw('LOWER(status_name) = ?', ['inactive'])->first();

        if (! $inactiveStatus) {
            return response()->json([
                'message' => 'Inactive status not found in system',
            ], 404);
        }

        $customer->update(['status_id' => $inactiveStatus->status_id]);
        $customer->load('status');

        return response()->json([
            'message' => 'Customer deactivated successfully',
            'data' => $customer,
        ]);

    }

    /**
     * @return JsonResponse
     *                      Reactivate a customer
     */
    public function reactivate(Customer $customer): JsonResponse
    {
        // Find active status ID (case-insensitive)
        $activeStatus = \App\Models\CustomerStatus::whereRaw('LOWER(status_name) = ?', ['active'])->first();

        if (! $activeStatus) {
            return response()->json([
                'message' => 'Active status not found in system',
            ], 404);
        }

        $customer->update(['status_id' => $activeStatus->status_id]);
        $customer->load('status');

        return response()->json([
            'message' => 'Customer reactivated successfully',
            'data' => $customer,
        ]);

    }

    /**
     * @return JsonResponse
     *                      Get customer rental history
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
                'name' => $customer->first_name.' '.$customer->last_name,
                'email' => $customer->email,
            ],
            'rentals' => $rentals,
            'reservations' => $reservations,
        ]);

    }
}
