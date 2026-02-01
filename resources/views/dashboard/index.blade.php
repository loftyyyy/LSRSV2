<!doctype html>
<html lang="en">
<head>
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
                            <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.856-1.487M15 10a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
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
                            <svg class="w-6 h-6 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
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
                            <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4m0 0L4 7m16 0l-8 4m0 0l8 4m-8-4v10l8-4m-8 4L4 11m16 0l-8-4"></path>
                            </svg>
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
                            <svg class="w-6 h-6 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
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
        // Global chart instances storage
        let dashboardCharts = {
            dailyRevenue: null,
            weeklyRentals: null,
            itemStatus: null,
            rentalStatus: null,
            topItems: null,
            topCustomers: null,
        };

        // Cleanup function
        function cleanupCharts() {
            Object.values(dashboardCharts).forEach(chart => {
                if (chart && typeof chart.destroy === 'function') {
                    chart.destroy();
                }
            });
            dashboardCharts = {
                dailyRevenue: null,
                weeklyRentals: null,
                itemStatus: null,
                rentalStatus: null,
                topItems: null,
                topCustomers: null,
            };
        }

        // Initialize dashboard
        function initializeDashboard() {
            // Fetch dashboard metrics
            axios.get('/api/dashboard/metrics')
                .then(response => {
                    const data = response.data;
                    updateKPIs(data.kpis);
                    updateCharts(data);
                    console.log('Dashboard loaded successfully', data);
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
            const isDark = document.documentElement.classList.contains('dark') || 
                          document.body.classList.contains('dark') ||
                          window.matchMedia('(prefers-color-scheme: dark)').matches;

            const textColor = isDark ? '#e5e7eb' : '#1f2937';
            const gridColor = isDark ? '#27272a' : '#d1d5db';
            const backgroundColor = isDark ? '#404040' : '#f3f4f6';

            // Daily Revenue Chart
            const dailyRevenueCtx = document.getElementById('dailyRevenueChart').getContext('2d');
            dashboardCharts.dailyRevenue = new Chart(dailyRevenueCtx, {
                type: 'line',
                data: {
                    labels: data.daily_revenue.map(d => {
                        const date = new Date(d.date);
                        return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
                    }),
                    datasets: [{
                        label: 'Revenue',
                        data: data.daily_revenue.map(d => d.amount),
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

            // Weekly Rentals Chart
            const weeklyRentalsCtx = document.getElementById('weeklyRentalsChart').getContext('2d');
            dashboardCharts.weeklyRentals = new Chart(weeklyRentalsCtx, {
                type: 'bar',
                data: {
                    labels: data.weekly_rentals.map(w => w.week),
                    datasets: [{
                        label: 'Rentals',
                        data: data.weekly_rentals.map(w => w.count),
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

            // Item Status Distribution (Doughnut)
            const itemStatusCtx = document.getElementById('itemStatusChart').getContext('2d');
            dashboardCharts.itemStatus = new Chart(itemStatusCtx, {
                type: 'doughnut',
                data: {
                    labels: data.item_status_distribution.map(d => d.status),
                    datasets: [{
                        data: data.item_status_distribution.map(d => d.count),
                        backgroundColor: ['#10b981', '#f59e0b', '#ef4444'],
                        borderColor: isDark ? '#1f2937' : '#fff',
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

            // Rental Status Distribution (Pie)
            const rentalStatusCtx = document.getElementById('rentalStatusChart').getContext('2d');
            dashboardCharts.rentalStatus = new Chart(rentalStatusCtx, {
                type: 'pie',
                data: {
                    labels: data.rental_status_distribution.map(d => d.status),
                    datasets: [{
                        data: data.rental_status_distribution.map(d => d.count),
                        backgroundColor: ['#06b6d4', '#14b8a6', '#f59e0b', '#ef4444', '#8b5cf6'],
                        borderColor: isDark ? '#1f2937' : '#fff',
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

            // Top Items (Horizontal Bar)
            const topItemsCtx = document.getElementById('topItemsChart').getContext('2d');
            dashboardCharts.topItems = new Chart(topItemsCtx, {
                type: 'bar',
                data: {
                    labels: data.top_items.map(item => item.item_name),
                    datasets: [{
                        label: 'Rentals',
                        data: data.top_items.map(item => item.rental_count),
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

            // Top Customers (Horizontal Bar)
            const topCustomersCtx = document.getElementById('topCustomersChart').getContext('2d');
            dashboardCharts.topCustomers = new Chart(topCustomersCtx, {
                type: 'bar',
                data: {
                    labels: data.top_customers.map(customer => customer.name),
                    datasets: [{
                        label: 'Rentals',
                        data: data.top_customers.map(customer => customer.rental_count),
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
        }

        // Event listeners for Turbo navigation
        document.addEventListener('turbo:load', function() {
            if (window.location.pathname === '/dashboard') {
                cleanupCharts();
                initializeDashboard();
            }
        });

        document.addEventListener('turbo:before-visit', function() {
            cleanupCharts();
        });

        // Initial load
        initializeDashboard();
    </script>

</body>
</html>
