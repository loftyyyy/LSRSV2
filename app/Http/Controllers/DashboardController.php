<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Inventory;
use App\Models\Item;
use App\Models\Payment;
use App\Models\Rental;
use App\Models\Reservation;
use Illuminate\Routing\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{

    /**
     * Display Dashboard Page
     */
    public function showDashboardPage(): View
    {
        return view('dashboard.index');
    }

    /**
     * Get comprehensive dashboard metrics and statistics
     */
    public function getMetrics(): JsonResponse
    {
        // Date range for metrics (last 30 days)
        $thirtyDaysAgo = now()->subDays(30)->startOfDay();
        $today = now()->endOfDay();

        // ============================================
        // KEY PERFORMANCE INDICATORS (KPIs)
        // ============================================

        // Customer Metrics
        $totalCustomers = Customer::count();
        $activeCustomers = Customer::whereHas('status', fn($q) => $q->where('status_name', 'active'))->count();
        $newCustomersThisMonth = Customer::where('created_at', '>=', $thirtyDaysAgo)->count();

        // Rental Metrics
        $totalRentals = Rental::count();
        $activeRentals = Rental::whereNull('return_date')->count();
        $overdueRentals = Rental::where('due_date', '<', now()->startOfDay())
            ->whereNull('return_date')
            ->count();

        // Inventory Metrics (using Inventory model)
        $totalItems = Inventory::count();
        // Since inventory status names vary, we'll count by existence if needed
        // For now, we'll return counts directly from inventory items
        $availableItems = 0;
        $rentedItems = 0;
        $damagedItems = 0;
        
        // Count items by status if status relationships exist
        $inventoryByStatus = Inventory::with('status')->get()->groupBy(function ($item) {
            return $item->status ? strtolower($item->status->status_name) : 'unknown';
        });
        
        $availableItems = $inventoryByStatus->get('available', collect())->count();
        $rentedItems = $inventoryByStatus->get('rented', collect())->count();
        $damagedItems = $inventoryByStatus->get('damaged', collect())->count();
        
        // If no status data, use all items as available
        if ($totalItems > 0 && ($availableItems + $rentedItems + $damagedItems) === 0) {
            $availableItems = $totalItems;
        }

        // Reservation Metrics
        $totalReservations = Reservation::count();
        $pendingReservations = Reservation::whereHas('status', fn($q) => $q->where('status_name', 'pending'))->count();

        // Financial Metrics - Use simple aggregations since invoice doesn't have status relationship
        $totalInvoices = Invoice::count();
        $totalInvoiceAmount = Invoice::sum('total_amount') ?? 0;
        $paidAmount = Invoice::sum('amount_paid') ?? 0;
        // Count invoices with pending balance (balance_due > 0)
        $pendingPayments = Invoice::where('balance_due', '>', 0)->count();
        // Get sum of pending amounts
        $pendingPaymentAmount = Invoice::where('balance_due', '>', 0)->sum('balance_due') ?? 0;

        // Revenue (Last 30 days) - from invoices with total_amount
        $revenueThisMonth = Invoice::where('invoice_date', '>=', $thirtyDaysAgo)
            ->where('invoice_date', '<=', $today)
            ->sum('total_amount') ?? 0;

        // Occupancy Rate
        $occupancyRate = $totalItems > 0 ? round(($rentedItems / $totalItems) * 100, 2) : 0;

        // ============================================
        // TOP PERFORMERS
        // ============================================

        // Top 5 Most Rented Items - using Inventory model which tracks actual rentals
        $topItems = Inventory::with('status')
            ->withCount('rentals')
            ->orderBy('rentals_count', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($inventory) {
                return [
                    'item_id' => $inventory->item_id,
                    'item_name' => $inventory->name,
                    'category' => $inventory->item_type ?? 'Uncategorized',
                    'rental_count' => $inventory->rentals_count ?? 0,
                    'status' => $inventory->status->status_name ?? 'Unknown',
                ];
            });

        // Top 5 Most Active Customers
        $topCustomers = Customer::with('status')
            ->withCount('rentals')
            ->orderBy('rentals_count', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($customer) {
                return [
                    'customer_id' => $customer->customer_id,
                    'name' => $customer->first_name . ' ' . $customer->last_name,
                    'rental_count' => $customer->rentals_count ?? 0,
                    'status' => $customer->status->status_name ?? 'active',
                ];
            });

        // ============================================
        // CHARTS DATA
        // ============================================

        // Daily Revenue (Last 30 days)
        $dailyRevenue = collect();
        for ($i = 29; $i >= 0; $i--) {
            $date = now()->subDays($i)->startOfDay();
            $dateString = $date->format('Y-m-d');
            $amount = Invoice::where('invoice_date', '>=', $date)
                ->where('invoice_date', '<', $date->copy()->addDay())
                ->sum('total_amount') ?? 0;
            $dailyRevenue->push([
                'date' => $dateString,
                'amount' => (float) $amount,
            ]);
        }

        // Weekly Rentals (Last 12 weeks)
        $weeklyRentals = collect();
        for ($i = 11; $i >= 0; $i--) {
            $startOfWeek = now()->subWeeks($i)->startOfWeek();
            $endOfWeek = $startOfWeek->copy()->endOfWeek();
            $count = Rental::whereBetween('created_at', [$startOfWeek, $endOfWeek])->count();
            $weeklyRentals->push([
                'week' => 'W' . $startOfWeek->weekOfYear . ' (' . $startOfWeek->format('M d') . ')',
                'count' => $count,
            ]);
        }

        // Item Status Distribution
        $itemStatusDistribution = [
            ['status' => 'Available', 'count' => $availableItems],
            ['status' => 'Rented', 'count' => $rentedItems],
            ['status' => 'Damaged', 'count' => $damagedItems],
        ];

        // Rental Status Distribution
        $rentalStatusDistribution = Rental::with('status')
            ->get()
            ->groupBy(function ($rental) {
                return $rental->status->status_name ?? 'Unknown';
            })
            ->map(function ($rentals, $status) {
                return [
                    'status' => ucfirst($status),
                    'count' => $rentals->count(),
                ];
            })
            ->values();

        return response()->json([
            // KPIs
            'kpis' => [
                'total_customers' => $totalCustomers,
                'active_customers' => $activeCustomers,
                'new_customers_this_month' => $newCustomersThisMonth,
                'active_rentals' => $activeRentals,
                'overdue_rentals' => $overdueRentals,
                'total_items' => $totalItems,
                'available_items' => $availableItems,
                'rented_items' => $rentedItems,
                'damaged_items' => $damagedItems,
                'occupancy_rate' => (int) $occupancyRate,
                'pending_reservations' => $pendingReservations,
                'total_invoices' => $totalInvoices,
                'pending_payments' => $pendingPayments,
                'revenue_this_month' => (float) round($revenueThisMonth, 2),
            ],
            // Top performers
            'top_items' => $topItems,
            'top_customers' => $topCustomers,
            // Charts
            'daily_revenue' => $dailyRevenue,
            'weekly_rentals' => $weeklyRentals,
            'item_status_distribution' => $itemStatusDistribution,
            'rental_status_distribution' => $rentalStatusDistribution,
            'generated_at' => now()->format('Y-m-d H:i:s'),
        ]);
    }
}
