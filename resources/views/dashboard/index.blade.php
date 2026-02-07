<!doctype html>
<html lang="en">
<head>
    {{-- Prevent flash of wrong theme --}}
    @include('components.theme-init')
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard · Love &amp; Styles</title>

    {{-- Favicon --}}
    <link rel="icon" href="{{ asset('favicon.ico') }}" sizes="any">
    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}">
    <link rel="manifest" href="{{ asset('site.webmanifest') }}">

    {{-- Fonts: Geist & Geist Mono --}}
    <link
        rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Geist:wght@400;500;600;700&family=Geist+Mono:wght@400;500&display=swap"
    >

    {{-- Chart.js --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

    {{-- App styles --}}
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif

    <style>
        /* Hide scrollbar while keeping scroll functionality */
        .scrollbar-hide {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
        .scrollbar-hide::-webkit-scrollbar {
            display: none;
        }

        /* Stat Card Hover Effect */
        .stat-card {
            transition: all 0.3s ease;
        }
        .stat-card:hover {
            transform: translateY(-2px);
        }

        /* Chart Container Styles */
        .chart-container {
            position: relative;
            height: 320px;
        }

        /* Responsive Grid */
        @media (max-width: 1536px) {
            .chart-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }
        @media (max-width: 768px) {
            .chart-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body class="min-h-screen flex font-geist bg-neutral-100 text-neutral-900 dark:bg-black dark:text-neutral-50 transition-colors duration-300 ease-in-out">
    <x-sidebar />

    <main class="flex-1 ml-64 flex flex-col px-10 py-8 overflow-x-hidden overflow-y-auto scrollbar-hide bg-gradient-to-b from-neutral-100 via-neutral-100 to-neutral-200 dark:from-black dark:via-black dark:to-neutral-950 transition-colors duration-300 ease-in-out">

        <header class="mb-8">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-semibold tracking-tight text-neutral-900 dark:text-white transition-colors duration-300 ease-in-out">
                        Dashboard
                    </h1>
                    <p class="mt-1 text-sm font-geist-mono text-neutral-500 dark:text-neutral-400 transition-colors duration-300 ease-in-out">
                        Welcome back! Here's your rental business overview.
                    </p>
                </div>
            </div>
        </header>

        <!-- KPI Cards Section -->
        <section class="mb-8">
            <h2 class="text-lg font-semibold text-neutral-900 dark:text-white mb-4">Key Performance Indicators</h2>
            
            <!-- KPI Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
                 <!-- Customers KPI -->
                 <div class="stat-card bg-white dark:bg-neutral-900 rounded-lg border border-neutral-200 dark:border-neutral-800 p-6 shadow-sm">
                     <div class="flex items-center justify-between">
                         <div>
                             <p class="text-sm font-medium text-neutral-500 dark:text-neutral-400">Total Customers</p>
                             <p class="text-2xl font-semibold text-neutral-900 dark:text-white mt-1" id="kpi-total-customers">0</p>
                             <p class="text-xs text-neutral-500 dark:text-neutral-400 mt-2" id="kpi-new-customers">+0 this month</p>
                         </div>
                         <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/30 rounded-full flex items-center justify-center">
                             <x-icon name="users" class="w-6 h-6 text-blue-600 dark:text-blue-400" />
                         </div>
                     </div>
                 </div>

                 <!-- Rentals KPI -->
                 <div class="stat-card bg-white dark:bg-neutral-900 rounded-lg border border-neutral-200 dark:border-neutral-800 p-6 shadow-sm">
                     <div class="flex items-center justify-between">
                         <div>
                             <p class="text-sm font-medium text-neutral-500 dark:text-neutral-400">Active Rentals</p>
                             <p class="text-2xl font-semibold text-neutral-900 dark:text-white mt-1" id="kpi-active-rentals">0</p>
                             <p class="text-xs text-red-600 dark:text-red-400 mt-2" id="kpi-overdue-rentals">0 overdue</p>
                         </div>
                         <div class="w-12 h-12 bg-emerald-100 dark:bg-emerald-900/30 rounded-full flex items-center justify-center">
                             <x-icon name="truck" class="w-6 h-6 text-emerald-600 dark:text-emerald-400" />
                         </div>
                     </div>
                 </div>

                 <!-- Inventory KPI -->
                 <div class="stat-card bg-white dark:bg-neutral-900 rounded-lg border border-neutral-200 dark:border-neutral-800 p-6 shadow-sm">
                     <div class="flex items-center justify-between">
                         <div>
                             <p class="text-sm font-medium text-neutral-500 dark:text-neutral-400">Inventory Items</p>
                             <p class="text-2xl font-semibold text-neutral-900 dark:text-white mt-1" id="kpi-total-items">0</p>
                             <p class="text-xs text-neutral-500 dark:text-neutral-400 mt-2">
                                 <span id="kpi-occupancy-rate">0</span>% occupancy
                             </p>
                         </div>
                         <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900/30 rounded-full flex items-center justify-center">
                             <x-icon name="package" class="w-6 h-6 text-purple-600 dark:text-purple-400" />
                         </div>
                     </div>
                 </div>

                 <!-- Revenue KPI -->
                 <div class="stat-card bg-white dark:bg-neutral-900 rounded-lg border border-neutral-200 dark:border-neutral-800 p-6 shadow-sm">
                     <div class="flex items-center justify-between">
                         <div>
                             <p class="text-sm font-medium text-neutral-500 dark:text-neutral-400">Revenue (30d)</p>
                             <p class="text-2xl font-semibold text-neutral-900 dark:text-white mt-1" id="kpi-revenue">$0.00</p>
                             <p class="text-xs text-neutral-500 dark:text-neutral-400 mt-2" id="kpi-payment-status">0 pending</p>
                         </div>
                         <div class="w-12 h-12 bg-orange-100 dark:bg-orange-900/30 rounded-full flex items-center justify-center">
                             <x-icon name="credit-card" class="w-6 h-6 text-orange-600 dark:text-orange-400" />
                         </div>
                     </div>
                 </div>
            </div>

            <!-- Secondary KPIs -->
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3">
                <!-- Active Customers -->
                <div class="bg-white dark:bg-neutral-900 rounded-lg border border-neutral-200 dark:border-neutral-800 p-4">
                    <p class="text-xs font-medium text-neutral-500 dark:text-neutral-400">Active Customers</p>
                    <p class="text-lg font-semibold text-blue-600 dark:text-blue-400 mt-1" id="secondary-active-customers">0</p>
                </div>

                <!-- Rented Items -->
                <div class="bg-white dark:bg-neutral-900 rounded-lg border border-neutral-200 dark:border-neutral-800 p-4">
                    <p class="text-xs font-medium text-neutral-500 dark:text-neutral-400">Rented Items</p>
                    <p class="text-lg font-semibold text-emerald-600 dark:text-emerald-400 mt-1" id="secondary-rented-items">0</p>
                </div>

                <!-- Available Items -->
                <div class="bg-white dark:bg-neutral-900 rounded-lg border border-neutral-200 dark:border-neutral-800 p-4">
                    <p class="text-xs font-medium text-neutral-500 dark:text-neutral-400">Available Items</p>
                    <p class="text-lg font-semibold text-purple-600 dark:text-purple-400 mt-1" id="secondary-available-items">0</p>
                </div>

                <!-- Pending Reservations -->
                <div class="bg-white dark:bg-neutral-900 rounded-lg border border-neutral-200 dark:border-neutral-800 p-4">
                    <p class="text-xs font-medium text-neutral-500 dark:text-neutral-400">Pending Reservations</p>
                    <p class="text-lg font-semibold text-cyan-600 dark:text-cyan-400 mt-1" id="secondary-pending-reservations">0</p>
                </div>

                <!-- Damaged Items -->
                <div class="bg-white dark:bg-neutral-900 rounded-lg border border-neutral-200 dark:border-neutral-800 p-4">
                    <p class="text-xs font-medium text-neutral-500 dark:text-neutral-400">Damaged Items</p>
                    <p class="text-lg font-semibold text-red-600 dark:text-red-400 mt-1" id="secondary-damaged-items">0</p>
                </div>

                <!-- Total Invoices -->
                <div class="bg-white dark:bg-neutral-900 rounded-lg border border-neutral-200 dark:border-neutral-800 p-4">
                    <p class="text-xs font-medium text-neutral-500 dark:text-neutral-400">Total Invoices</p>
                    <p class="text-lg font-semibold text-orange-600 dark:text-orange-400 mt-1" id="secondary-total-invoices">0</p>
                </div>
            </div>
        </section>

        <!-- Charts Section -->
        <section class="mb-8">
            <h2 class="text-lg font-semibold text-neutral-900 dark:text-white mb-4">Analytics & Performance</h2>
            
            <div class="chart-grid grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                <!-- Daily Revenue Chart -->
                <div class="bg-white dark:bg-neutral-900 rounded-lg border border-neutral-200 dark:border-neutral-800 p-6 shadow-sm">
                    <h3 class="text-sm font-semibold text-neutral-900 dark:text-white mb-4">Daily Revenue (Last 30 Days)</h3>
                    <div class="chart-container">
                        <canvas id="dailyRevenueChart"></canvas>
                    </div>
                </div>

                <!-- Weekly Rentals Chart -->
                <div class="bg-white dark:bg-neutral-900 rounded-lg border border-neutral-200 dark:border-neutral-800 p-6 shadow-sm">
                    <h3 class="text-sm font-semibold text-neutral-900 dark:text-white mb-4">Weekly Rental Activity (Last 12 Weeks)</h3>
                    <div class="chart-container">
                        <canvas id="weeklyRentalsChart"></canvas>
                    </div>
                </div>

                <!-- Item Status Distribution -->
                <div class="bg-white dark:bg-neutral-900 rounded-lg border border-neutral-200 dark:border-neutral-800 p-6 shadow-sm">
                    <h3 class="text-sm font-semibold text-neutral-900 dark:text-white mb-4">Item Status Distribution</h3>
                    <div class="chart-container">
                        <canvas id="itemStatusChart"></canvas>
                    </div>
                </div>

                <!-- Rental Status Distribution -->
                <div class="bg-white dark:bg-neutral-900 rounded-lg border border-neutral-200 dark:border-neutral-800 p-6 shadow-sm">
                    <h3 class="text-sm font-semibold text-neutral-900 dark:text-white mb-4">Rental Status Distribution</h3>
                    <div class="chart-container">
                        <canvas id="rentalStatusChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Two Column Charts -->
            <div class="chart-grid grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Top Items Chart -->
                <div class="bg-white dark:bg-neutral-900 rounded-lg border border-neutral-200 dark:border-neutral-800 p-6 shadow-sm">
                    <h3 class="text-sm font-semibold text-neutral-900 dark:text-white mb-4">Top 5 Most Rented Items</h3>
                    <div class="chart-container">
                        <canvas id="topItemsChart"></canvas>
                    </div>
                </div>

                <!-- Top Customers Chart -->
                <div class="bg-white dark:bg-neutral-900 rounded-lg border border-neutral-200 dark:border-neutral-800 p-6 shadow-sm">
                    <h3 class="text-sm font-semibold text-neutral-900 dark:text-white mb-4">Top 5 Most Active Customers</h3>
                    <div class="chart-container">
                        <canvas id="topCustomersChart"></canvas>
                    </div>
                </div>
            </div>
        </section>

    </main>

    <script>
        // Use window object to store charts and observer to avoid redeclaration errors with Turbo
        if (!window.dashboardState) {
            window.dashboardState = {
                charts: {
                    dailyRevenue: null,
                    weeklyRentals: null,
                    itemStatus: null,
                    rentalStatus: null,
                    topItems: null,
                    topCustomers: null,
                },
                observer: null
            };
        }
        
        // Convenience reference
        const dashboardCharts = window.dashboardState.charts;

        // Cleanup function
        function cleanupCharts() {
            Object.values(dashboardCharts).forEach(chart => {
                if (chart && typeof chart.destroy === 'function') {
                    chart.destroy();
                }
            });
            
            // Reset all charts to null
            dashboardCharts.dailyRevenue = null;
            dashboardCharts.weeklyRentals = null;
            dashboardCharts.itemStatus = null;
            dashboardCharts.rentalStatus = null;
            dashboardCharts.topItems = null;
            dashboardCharts.topCustomers = null;
            
            // Cleanup the observer
            if (window.dashboardState.observer) {
                window.dashboardState.observer.disconnect();
                window.dashboardState.observer = null;
            }
        }

        // Initialize dashboard
        function initializeDashboard() {
            console.log('Initializing dashboard...');

            // Fetch dashboard metrics using native fetch API
            fetch('/api/dashboard/metrics')
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Dashboard metrics loaded:', data);
                    
                    // Verify data structure
                    if (!data.kpis) {
                        console.error('Invalid response: missing kpis', data);
                        return;
                    }
                    
                    updateKPIs(data.kpis);
                    updateCharts(data);
                    console.log('Dashboard loaded successfully');
                })
                .catch(error => {
                    console.error('Failed to load dashboard metrics:', error);
                });
        }

        // Update KPI cards
        function updateKPIs(kpis) {
            // Main KPIs
            document.getElementById('kpi-total-customers').textContent = kpis.total_customers;
            document.getElementById('kpi-new-customers').textContent = `+${kpis.new_customers_this_month} this month`;
            document.getElementById('kpi-active-rentals').textContent = kpis.active_rentals;
            document.getElementById('kpi-overdue-rentals').textContent = `${kpis.overdue_rentals} overdue`;
            document.getElementById('kpi-total-items').textContent = kpis.total_items;
            document.getElementById('kpi-occupancy-rate').textContent = kpis.occupancy_rate;
            document.getElementById('kpi-revenue').textContent = '$' + kpis.revenue_this_month.toFixed(2);
            document.getElementById('kpi-payment-status').textContent = `${kpis.pending_payments} pending`;

            // Secondary KPIs
            document.getElementById('secondary-active-customers').textContent = kpis.active_customers;
            document.getElementById('secondary-rented-items').textContent = kpis.rented_items;
            document.getElementById('secondary-available-items').textContent = kpis.available_items;
            document.getElementById('secondary-pending-reservations').textContent = kpis.pending_reservations;
            document.getElementById('secondary-damaged-items').textContent = kpis.damaged_items;
            document.getElementById('secondary-total-invoices').textContent = kpis.total_invoices;
        }

        // Update charts
        function updateCharts(data) {
            // Validate data structure
            if (!data.daily_revenue || !data.weekly_rentals || !data.item_status_distribution || 
                !data.rental_status_distribution || !data.top_items || !data.top_customers) {
                console.error('Invalid chart data structure:', data);
                return;
            }

            console.log('Updating charts with data:', data);

            // Destroy existing charts before creating new ones
            cleanupCharts();

            // Simply check if the html element has the 'dark' class
            // This is the most reliable way with Tailwind CSS
            const htmlElement = document.documentElement;
            const isDark = htmlElement.classList.contains('dark');

            // Use pure black for light mode for maximum contrast with white backgrounds
            // Use light gray for dark mode for contrast with dark backgrounds
            const textColor = isDark ? '#e5e7eb' : '#000000';
            const gridColor = isDark ? '#27272a' : '#d1d5db';

            // Daily Revenue Chart
            createDailyRevenueChart(data.daily_revenue, textColor, gridColor);

            // Weekly Rentals Chart
            createWeeklyRentalsChart(data.weekly_rentals, textColor, gridColor);

            // Item Status Distribution Chart
            createItemStatusChart(data.item_status_distribution, textColor, gridColor);

            // Rental Status Distribution Chart
            createRentalStatusChart(data.rental_status_distribution, textColor, gridColor);

            // Top Items Chart
            createTopItemsChart(data.top_items, textColor, gridColor);

            // Top Customers Chart
            createTopCustomersChart(data.top_customers, textColor, gridColor);
        }

        // Daily Revenue Chart
        function createDailyRevenueChart(dailyRevenueData, textColor, gridColor) {
            try {
                const dailyRevenueCanvas = document.getElementById('dailyRevenueChart');
                if (!dailyRevenueCanvas) {
                    console.error('dailyRevenueChart canvas not found');
                    return;
                }
                
                const dailyRevenueCtx = dailyRevenueCanvas.getContext('2d');
                dashboardCharts.dailyRevenue = new Chart(dailyRevenueCtx, {
                    type: 'line',
                    data: {
                        labels: dailyRevenueData.map(d => {
                            const date = new Date(d.date);
                            return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
                        }),
                        datasets: [{
                            label: 'Revenue',
                            data: dailyRevenueData.map(d => d.amount),
                            borderColor: '#0ea5e9',
                            backgroundColor: 'rgba(6, 182, 212, 0.1)',
                            borderWidth: 2,
                            fill: true,
                            tension: 0.4,
                            pointBackgroundColor: '#0ea5e9',
                            pointBorderColor: '#0ea5e9',
                            pointRadius: 3,
                            pointHoverRadius: 5,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                labels: {
                                    color: textColor,
                                    font: { size: 12, weight: 500 },
                                    usePointStyle: true,
                                    padding: 15,
                                }
                            },
                            tooltip: {
                                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                                titleColor: '#fff',
                                bodyColor: '#fff',
                                borderColor: textColor,
                                borderWidth: 1,
                                padding: 10,
                                displayColors: true,
                                callbacks: {
                                    label: function(context) {
                                        return '$' + context.parsed.y.toFixed(2);
                                    }
                                }
                            }
                        },
                        scales: {
                            y: {
                                ticks: { color: textColor, font: { size: 11 } },
                                grid: { color: gridColor, drawBorder: false },
                                beginAtZero: true,
                                callback: function(value) {
                                    return '$' + value.toFixed(0);
                                }
                            },
                            x: {
                                ticks: { color: textColor, font: { size: 11 } },
                                grid: { color: gridColor, drawBorder: false }
                            }
                        }
                    }
                });
                console.log('Daily Revenue Chart created successfully');
            } catch (error) {
                console.error('Error creating Daily Revenue Chart:', error);
            }
        }

        // Weekly Rentals Chart
        function createWeeklyRentalsChart(weeklyRentalsData, textColor, gridColor) {
            try {
                const weeklyRentalsCanvas = document.getElementById('weeklyRentalsChart');
                if (!weeklyRentalsCanvas) {
                    console.error('weeklyRentalsChart canvas not found');
                    return;
                }
                
                const weeklyRentalsCtx = weeklyRentalsCanvas.getContext('2d');
                dashboardCharts.weeklyRentals = new Chart(weeklyRentalsCtx, {
                    type: 'bar',
                    data: {
                        labels: weeklyRentalsData.map(w => w.week),
                        datasets: [{
                            label: 'Rentals',
                            data: weeklyRentalsData.map(w => w.count),
                            backgroundColor: '#8b5cf6',
                            borderColor: '#7c3aed',
                            borderWidth: 1,
                            borderRadius: 6,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                labels: {
                                    color: textColor,
                                    font: { size: 12, weight: 500 },
                                    padding: 15,
                                }
                            },
                            tooltip: {
                                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                                titleColor: '#fff',
                                bodyColor: '#fff',
                                borderColor: textColor,
                                borderWidth: 1,
                                padding: 10,
                            }
                        },
                        scales: {
                            y: {
                                ticks: { color: textColor, font: { size: 11 } },
                                grid: { color: gridColor, drawBorder: false },
                                beginAtZero: true,
                            },
                            x: {
                                ticks: { color: textColor, font: { size: 11 } },
                                grid: { color: gridColor, drawBorder: false }
                            }
                        }
                    }
                });
                console.log('Weekly Rentals Chart created successfully');
            } catch (error) {
                console.error('Error creating Weekly Rentals Chart:', error);
            }
        }

        // Item Status Distribution Chart
        function createItemStatusChart(itemStatusData, textColor, gridColor) {
            try {
                const itemStatusCanvas = document.getElementById('itemStatusChart');
                if (!itemStatusCanvas) {
                    console.error('itemStatusChart canvas not found');
                    return;
                }
                
                const itemStatusCtx = itemStatusCanvas.getContext('2d');
                dashboardCharts.itemStatus = new Chart(itemStatusCtx, {
                    type: 'doughnut',
                    data: {
                        labels: itemStatusData.map(d => d.status),
                        datasets: [{
                            data: itemStatusData.map(d => d.count),
                            backgroundColor: ['#10b981', '#f59e0b', '#ef4444'],
                            borderColor: textColor === '#e5e7eb' ? '#1f2937' : '#fff',
                            borderWidth: 2,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                labels: {
                                    color: textColor,
                                    font: { size: 12, weight: 500 },
                                    padding: 15,
                                }
                            },
                            tooltip: {
                                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                                titleColor: '#fff',
                                bodyColor: '#fff',
                                borderColor: textColor,
                                borderWidth: 1,
                                padding: 10,
                            }
                        }
                    }
                });
                console.log('Item Status Chart created successfully');
            } catch (error) {
                console.error('Error creating Item Status Chart:', error);
            }
        }

        // Rental Status Distribution Chart
        function createRentalStatusChart(rentalStatusData, textColor, gridColor) {
            try {
                const rentalStatusCanvas = document.getElementById('rentalStatusChart');
                if (!rentalStatusCanvas) {
                    console.error('rentalStatusChart canvas not found');
                    return;
                }
                
                const rentalStatusCtx = rentalStatusCanvas.getContext('2d');
                dashboardCharts.rentalStatus = new Chart(rentalStatusCtx, {
                    type: 'pie',
                    data: {
                        labels: rentalStatusData.map(d => d.status),
                        datasets: [{
                            data: rentalStatusData.map(d => d.count),
                            backgroundColor: ['#06b6d4', '#14b8a6', '#f59e0b', '#ef4444', '#8b5cf6'],
                            borderColor: textColor === '#e5e7eb' ? '#1f2937' : '#fff',
                            borderWidth: 2,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                labels: {
                                    color: textColor,
                                    font: { size: 12, weight: 500 },
                                    padding: 15,
                                }
                            },
                            tooltip: {
                                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                                titleColor: '#fff',
                                bodyColor: '#fff',
                                borderColor: textColor,
                                borderWidth: 1,
                                padding: 10,
                            }
                        }
                    }
                });
                console.log('Rental Status Chart created successfully');
            } catch (error) {
                console.error('Error creating Rental Status Chart:', error);
            }
        }

        // Top Items Chart
        function createTopItemsChart(topItemsData, textColor, gridColor) {
            try {
                const topItemsCanvas = document.getElementById('topItemsChart');
                if (!topItemsCanvas) {
                    console.error('topItemsChart canvas not found');
                    return;
                }
                
                const topItemsCtx = topItemsCanvas.getContext('2d');
                dashboardCharts.topItems = new Chart(topItemsCtx, {
                    type: 'bar',
                    data: {
                        labels: topItemsData.map(item => item.item_name),
                        datasets: [{
                            label: 'Rentals',
                            data: topItemsData.map(item => item.rental_count),
                            backgroundColor: '#a78bfa',
                            borderColor: '#9333ea',
                            borderWidth: 1,
                            borderRadius: 6,
                        }]
                    },
                    options: {
                        indexAxis: 'y',
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                labels: {
                                    color: textColor,
                                    font: { size: 12, weight: 500 },
                                    padding: 15,
                                }
                            },
                            tooltip: {
                                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                                titleColor: '#fff',
                                bodyColor: '#fff',
                                borderColor: textColor,
                                borderWidth: 1,
                                padding: 10,
                            }
                        },
                        scales: {
                            x: {
                                ticks: { color: textColor, font: { size: 11 } },
                                grid: { color: gridColor, drawBorder: false },
                                beginAtZero: true,
                            },
                            y: {
                                ticks: { color: textColor, font: { size: 11 } },
                                grid: { color: gridColor, drawBorder: false }
                            }
                        }
                    }
                });
                console.log('Top Items Chart created successfully');
            } catch (error) {
                console.error('Error creating Top Items Chart:', error);
            }
        }

        // Top Customers Chart
        function createTopCustomersChart(topCustomersData, textColor, gridColor) {
            try {
                const topCustomersCanvas = document.getElementById('topCustomersChart');
                if (!topCustomersCanvas) {
                    console.error('topCustomersChart canvas not found');
                    return;
                }
                
                const topCustomersCtx = topCustomersCanvas.getContext('2d');
                dashboardCharts.topCustomers = new Chart(topCustomersCtx, {
                    type: 'bar',
                    data: {
                        labels: topCustomersData.map(customer => customer.name),
                        datasets: [{
                            label: 'Rentals',
                            data: topCustomersData.map(customer => customer.rental_count),
                            backgroundColor: '#38bdf8',
                            borderColor: '#0284c7',
                            borderWidth: 1,
                            borderRadius: 6,
                        }]
                    },
                    options: {
                        indexAxis: 'y',
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                labels: {
                                    color: textColor,
                                    font: { size: 12, weight: 500 },
                                    padding: 15,
                                }
                            },
                            tooltip: {
                                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                                titleColor: '#fff',
                                bodyColor: '#fff',
                                borderColor: textColor,
                                borderWidth: 1,
                                padding: 10,
                            }
                        },
                        scales: {
                            x: {
                                ticks: { color: textColor, font: { size: 11 } },
                                grid: { color: gridColor, drawBorder: false },
                                beginAtZero: true,
                            },
                            y: {
                                ticks: { color: textColor, font: { size: 11 } },
                                grid: { color: gridColor, drawBorder: false }
                            }
                        }
                    }
                });
                console.log('Top Customers Chart created successfully');
            } catch (error) {
                console.error('Error creating Top Customers Chart:', error);
            }
        }

         // Set up dark mode observer for theme switching
         if (!window.dashboardState.observer) {
             window.dashboardState.observer = new MutationObserver((mutations) => {
                 mutations.forEach((mutation) => {
                     if (mutation.type === 'attributes' && mutation.attributeName === 'class') {
                         setTimeout(() => {
                             // Only update if we have data to work with
                             if (!window.dashboardMetrics) {
                                 return;
                             }

                             // Detect current theme
                             const htmlElement = document.documentElement;
                             const isDark = htmlElement.classList.contains('dark');
                             const textColor = isDark ? '#e5e7eb' : '#000000';
                             const gridColor = isDark ? '#27272a' : '#d1d5db';
                             
                             // Helper function to update chart colors
                             const updateChartColors = (chart) => {
                                 if (!chart) return;
                                 
                                 const text = isDark ? '#e5e7eb' : '#000000';
                                 const grid = isDark ? '#27272a' : '#d1d5db';
                                 
                                 // Update plugins (legend, tooltip)
                                 if (chart.options.plugins) {
                                     if (chart.options.plugins.legend?.labels) {
                                         chart.options.plugins.legend.labels.color = text;
                                     }
                                     if (chart.options.plugins.tooltip) {
                                         chart.options.plugins.tooltip.titleColor = text;
                                         chart.options.plugins.tooltip.bodyColor = text;
                                         chart.options.plugins.tooltip.borderColor = text;
                                     }
                                 }
                                 
                                 // Update scales (axes, grid)
                                 if (chart.options.scales) {
                                     Object.values(chart.options.scales).forEach(scale => {
                                         if (scale.ticks) {
                                             scale.ticks.color = text;
                                         }
                                         if (scale.grid) {
                                             scale.grid.color = grid;
                                         }
                                     });
                                 }
                             };
                             
                             // Update all charts
                             if (dashboardCharts.dailyRevenue) updateChartColors(dashboardCharts.dailyRevenue);
                             if (dashboardCharts.weeklyRentals) updateChartColors(dashboardCharts.weeklyRentals);
                             if (dashboardCharts.itemStatus) updateChartColors(dashboardCharts.itemStatus);
                             if (dashboardCharts.rentalStatus) updateChartColors(dashboardCharts.rentalStatus);
                             if (dashboardCharts.topItems) updateChartColors(dashboardCharts.topItems);
                             if (dashboardCharts.topCustomers) updateChartColors(dashboardCharts.topCustomers);
                             
                             // Update all charts with animation
                             Object.values(dashboardCharts).forEach(chart => {
                                 if (chart) chart.update('none');
                             });
                         }, 50);
                     }
                 });
             });

             // Start observing immediately
             window.dashboardState.observer.observe(document.documentElement, {
                 attributes: true,
                 attributeFilter: ['class'],
                 subtree: false
             });
         }

         // Initial load
         initializeDashboard();
    </script>

</body>
</html>
