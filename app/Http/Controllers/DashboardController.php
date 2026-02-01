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
        $thirtyDaysAgo = now()->subDays(30);
        $today = now();

        // ============================================
        // KEY PERFORMANCE INDICATORS (KPIs)
        // ============================================

        // Customer Metrics
        $totalCustomers = Customer::count();
        $activeCustomers = Customer::whereHas('status', fn($q) => $q->where('status_name', 'active'))->count();
        $newCustomersThisMonth = Customer::where('created_at', '>=', $thirtyDaysAgo)->count();

        // Rental Metrics
        $totalRentals = Rental::count();
        $activeRentals = Rental::whereHas('status', fn($q) => $q->where('status_name', 'active'))->count();
        $overdueRentals = Rental::where('return_date', '<', now())
            ->whereHas('status', fn($q) => $q->where('status_name', 'active'))
            ->count();
        $rentalsThisMonth = Rental::where('created_at', '>=', $thirtyDaysAgo)->count();

        // Inventory Metrics
        $totalItems = Item::count();
        $availableItems = Item::whereHas('status', fn($q) => $q->where('status_name', 'available'))->count();
        $rentedItems = Item::whereHas('status', fn($q) => $q->where('status_name', 'rented'))->count();
        $damagedItems = Item::whereHas('status', fn($q) => $q->where('status_name', 'damaged'))->count();

        // Reservation Metrics
        $totalReservations = Reservation::count();
        $pendingReservations = Reservation::whereHas('status', fn($q) => $q->where('status_name', 'pending'))->count();

        // Financial Metrics
        $totalInvoices = Invoice::count();
        $totalInvoiceAmount = Invoice::sum('total_amount') ?? 0;
        $paidAmount = Payment::whereHas('status', fn($q) => $q->where('status_name', 'completed'))->sum('amount') ?? 0;
        $pendingPayments = Payment::whereHas('status', fn($q) => $q->where('status_name', 'pending'))->count();
        $pendingPaymentAmount = Payment::whereHas('status', fn($q) => $q->where('status_name', 'pending'))->sum('amount') ?? 0;

        // Revenue (Last 30 days)
        $revenueThisMonth = Payment::where('created_at', '>=', $thirtyDaysAgo)
            ->whereHas('status', fn($q) => $q->where('status_name', 'completed'))
            ->sum('amount') ?? 0;

        // Occupancy Rate
        $occupancyRate = $totalItems > 0 ? round(($rentedItems / $totalItems) * 100, 2) : 0;

        // ============================================
        // TOP PERFORMERS
        // ============================================

        // Top 5 Most Rented Items
        $topItems = Item::with('status')
            ->withCount('rentals')
            ->orderBy('rentals_count', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($item) {
                return [
                    'item_id' => $item->item_id,
                    'item_name' => $item->item_name,
                    'category' => $item->category,
                    'rental_count' => $item->rentals_count,
                    'status' => $item->status->status_name ?? 'unknown',
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
                    'rental_count' => $customer->rentals_count,
                    'status' => $customer->status->status_name ?? 'active',
                ];
            });

        // ============================================
        // CHARTS DATA
        // ============================================

        // Daily Revenue (Last 30 days)
        $dailyRevenue = collect();
        for ($i = 29; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $amount = Payment::where('created_at', '>=', $date)
                ->where('created_at', '<', $date . ' 23:59:59')
                ->whereHas('status', fn($q) => $q->where('status_name', 'completed'))
                ->sum('amount') ?? 0;
            $dailyRevenue->push([
                'date' => $date,
                'amount' => $amount,
            ]);
        }

        // Weekly Rentals (Last 12 weeks)
        $weeklyRentals = collect();
        for ($i = 11; $i >= 0; $i--) {
            $startOfWeek = now()->subWeeks($i)->startOfWeek();
            $endOfWeek = now()->subWeeks($i)->endOfWeek();
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
            ->groupBy('status.status_name')
            ->map(fn($rentals, $status) => [
                'status' => ucfirst($status),
                'count' => $rentals->count(),
            ])
            ->values();

        // Payment Status Distribution
        $paymentStatusDistribution = Payment::with('status')
            ->get()
            ->groupBy('status.status_name')
            ->map(fn($payments, $status) => [
                'status' => ucfirst($status),
                'count' => $payments->count(),
            ])
            ->values();

        return response()->json([
            // KPIs
            'kpis' => [
                'total_customers' => $totalCustomers,
                'active_customers' => $activeCustomers,
                'new_customers_this_month' => $newCustomersThisMonth,
                'total_rentals' => $totalRentals,
                'active_rentals' => $activeRentals,
                'overdue_rentals' => $overdueRentals,
                'rentals_this_month' => $rentalsThisMonth,
                'total_items' => $totalItems,
                'available_items' => $availableItems,
                'rented_items' => $rentedItems,
                'damaged_items' => $damagedItems,
                'occupancy_rate' => $occupancyRate,
                'total_reservations' => $totalReservations,
                'pending_reservations' => $pendingReservations,
                'total_invoices' => $totalInvoices,
                'total_invoice_amount' => round($totalInvoiceAmount, 2),
                'paid_amount' => round($paidAmount, 2),
                'pending_payments' => $pendingPayments,
                'pending_payment_amount' => round($pendingPaymentAmount, 2),
                'revenue_this_month' => round($revenueThisMonth, 2),
            ],
            // Top performers
            'top_items' => $topItems,
            'top_customers' => $topCustomers,
            // Charts
            'daily_revenue' => $dailyRevenue,
            'weekly_rentals' => $weeklyRentals,
            'item_status_distribution' => $itemStatusDistribution,
            'rental_status_distribution' => $rentalStatusDistribution,
            'payment_status_distribution' => $paymentStatusDistribution,
            'generated_at' => now()->format('Y-m-d H:i:s'),
        ]);
    }
}
