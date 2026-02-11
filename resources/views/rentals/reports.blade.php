<!doctype html>
<html lang="en">
<head>
    {{-- Prevent flash of wrong theme --}}
    @include('components.theme-init')
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rental Reports · Love &amp; Styles</title>

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
        .scrollbar-hide {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
        .scrollbar-hide::-webkit-scrollbar {
            display: none;
        }

        .stat-card {
            transition: all 0.3s ease;
        }
        .stat-card:hover {
            transform: translateY(-2px);
        }

        .chart-container {
            position: relative;
            height: 320px;
        }

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
                        Rental Reports
                    </h1>
                    <p class="mt-1 text-sm font-geist-mono text-neutral-500 dark:text-neutral-400 transition-colors duration-300 ease-in-out">
                        Track rental activity, revenue, and performance metrics.
                    </p>
                </div>
            </div>
        </header>

        <!-- KPI Cards Section -->
        <section class="mb-8">
            <h2 class="text-lg font-semibold text-neutral-900 dark:text-white mb-4">Key Metrics</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
                <!-- Total Rentals -->
                <div class="stat-card bg-white dark:bg-neutral-900 rounded-lg border border-neutral-200 dark:border-neutral-800 p-6 shadow-sm">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-neutral-500 dark:text-neutral-400">Total Rentals</p>
                            <p class="text-2xl font-semibold text-indigo-600 dark:text-indigo-400 mt-1" id="kpi-total-rentals">0</p>
                            <p class="text-xs text-neutral-500 dark:text-neutral-400 mt-2" id="kpi-active-rentals">0 active</p>
                        </div>
                        <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/30 rounded-full flex items-center justify-center">
                            <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Revenue -->
                <div class="stat-card bg-white dark:bg-neutral-900 rounded-lg border border-neutral-200 dark:border-neutral-800 p-6 shadow-sm">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-neutral-500 dark:text-neutral-400">Total Revenue</p>
                            <p class="text-2xl font-semibold text-emerald-600 dark:text-emerald-400 mt-1" id="kpi-total-revenue">$0.00</p>
                            <p class="text-xs text-neutral-500 dark:text-neutral-400 mt-2" id="kpi-monthly-revenue">$0 this month</p>
                        </div>
                        <div class="w-12 h-12 bg-emerald-100 dark:bg-emerald-900/30 rounded-full flex items-center justify-center">
                            <svg class="w-6 h-6 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Overdue Rentals -->
                <div class="stat-card bg-white dark:bg-neutral-900 rounded-lg border border-neutral-200 dark:border-neutral-800 p-6 shadow-sm">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-neutral-500 dark:text-neutral-400">Overdue Rentals</p>
                            <p class="text-2xl font-semibold text-rose-600 dark:text-rose-400 mt-1" id="kpi-overdue">0</p>
                            <p class="text-xs text-neutral-500 dark:text-neutral-400 mt-2" id="kpi-completed">0 completed</p>
                        </div>
                        <div class="w-12 h-12 bg-rose-100 dark:bg-rose-900/30 rounded-full flex items-center justify-center">
                            <svg class="w-6 h-6 text-rose-600 dark:text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4v2m0 4v2m0-14H8m4 0H4m20 0h-4m0 0V4m0 0h-4"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Duration -->
                <div class="stat-card bg-white dark:bg-neutral-900 rounded-lg border border-neutral-200 dark:border-neutral-800 p-6 shadow-sm">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-neutral-500 dark:text-neutral-400">Avg Duration</p>
                            <p class="text-2xl font-semibold text-sky-600 dark:text-sky-400 mt-1" id="kpi-avg-duration">0</p>
                            <p class="text-xs text-neutral-500 dark:text-neutral-400 mt-2">days per rental</p>
                        </div>
                        <div class="w-12 h-12 bg-sky-100 dark:bg-sky-900/30 rounded-full flex items-center justify-center">
                            <svg class="w-6 h-6 text-sky-600 dark:text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Secondary KPIs -->
            <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                <div class="bg-white dark:bg-neutral-900 rounded-lg border border-neutral-200 dark:border-neutral-800 p-4">
                    <p class="text-xs font-medium text-neutral-500 dark:text-neutral-400">Late Returns</p>
                    <p class="text-lg font-semibold text-amber-600 dark:text-amber-400 mt-1" id="secondary-late">0</p>
                </div>

                <div class="bg-white dark:bg-neutral-900 rounded-lg border border-neutral-200 dark:border-neutral-800 p-4">
                    <p class="text-xs font-medium text-neutral-500 dark:text-neutral-400">Cancelled</p>
                    <p class="text-lg font-semibold text-rose-600 dark:text-rose-400 mt-1" id="secondary-cancelled">0</p>
                </div>

                <div class="bg-white dark:bg-neutral-900 rounded-lg border border-neutral-200 dark:border-neutral-800 p-4">
                    <p class="text-xs font-medium text-neutral-500 dark:text-neutral-400">Completed</p>
                    <p class="text-lg font-semibold text-emerald-600 dark:text-emerald-400 mt-1" id="secondary-completed">0</p>
                </div>
            </div>
        </section>

        <!-- Charts Section -->
        <section class="mb-8">
            <h2 class="text-lg font-semibold text-neutral-900 dark:text-white mb-4">Analytics & Performance</h2>
            
            <div class="chart-grid grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                <!-- Monthly Rentals -->
                <div class="bg-white dark:bg-neutral-900 rounded-lg border border-neutral-200 dark:border-neutral-800 p-6 shadow-sm">
                    <h3 class="text-sm font-semibold text-neutral-900 dark:text-white mb-4">Monthly Rental Activity (Last 12 Months)</h3>
                    <div class="chart-container">
                        <canvas id="monthlyChart"></canvas>
                    </div>
                </div>

                <!-- Weekly Revenue -->
                <div class="bg-white dark:bg-neutral-900 rounded-lg border border-neutral-200 dark:border-neutral-800 p-6 shadow-sm">
                    <h3 class="text-sm font-semibold text-neutral-900 dark:text-white mb-4">Weekly Revenue (Last 8 Weeks)</h3>
                    <div class="chart-container">
                        <canvas id="revenueChart"></canvas>
                    </div>
                </div>

                <!-- Rental Status Distribution -->
                <div class="bg-white dark:bg-neutral-900 rounded-lg border border-neutral-200 dark:border-neutral-800 p-6 shadow-sm">
                    <h3 class="text-sm font-semibold text-neutral-900 dark:text-white mb-4">Rental Status Distribution</h3>
                    <div class="chart-container">
                        <canvas id="statusChart"></canvas>
                    </div>
                </div>

                <!-- Rental Duration Distribution -->
                <div class="bg-white dark:bg-neutral-900 rounded-lg border border-neutral-200 dark:border-neutral-800 p-6 shadow-sm">
                    <h3 class="text-sm font-semibold text-neutral-900 dark:text-white mb-4">Rental Duration Distribution</h3>
                    <div class="chart-container">
                        <canvas id="durationChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Two Column Charts -->
            <div class="chart-grid grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Top Customers -->
                <div class="bg-white dark:bg-neutral-900 rounded-lg border border-neutral-200 dark:border-neutral-800 p-6 shadow-sm">
                    <h3 class="text-sm font-semibold text-neutral-900 dark:text-white mb-4">Top Customers by Rentals</h3>
                    <div class="chart-container">
                        <canvas id="topCustomersChart"></canvas>
                    </div>
                </div>

                <!-- Top Items by Revenue -->
                <div class="bg-white dark:bg-neutral-900 rounded-lg border border-neutral-200 dark:border-neutral-800 p-6 shadow-sm">
                    <h3 class="text-sm font-semibold text-neutral-900 dark:text-white mb-4">Top Items by Revenue</h3>
                    <div class="chart-container">
                        <canvas id="topItemsChart"></canvas>
                    </div>
                </div>
            </div>
        </section>

    </main>

    <script>
        // Use window object to store charts and observer to avoid redeclaration errors with Turbo
        if (!window.rentalState) {
            window.rentalState = {
                charts: {
                    monthly: null,
                    revenue: null,
                    status: null,
                    duration: null,
                    topCustomers: null,
                    topItems: null,
                },
                observer: null
            };
        }
        
        // Convenience reference
        const rentalCharts = window.rentalState.charts;

        function cleanupCharts() {
            Object.values(rentalCharts).forEach(chart => {
                if (chart && typeof chart.destroy === 'function') {
                    chart.destroy();
                }
            });
            
            // Reset all charts to null
            rentalCharts.monthly = null;
            rentalCharts.revenue = null;
            rentalCharts.status = null;
            rentalCharts.duration = null;
            rentalCharts.topCustomers = null;
            rentalCharts.topItems = null;
            
            // Cleanup the observer
            if (window.rentalState.observer) {
                window.rentalState.observer.disconnect();
                window.rentalState.observer = null;
            }
        }

        function initializeRentalReports() {
            axios.get('/api/rentals/reports/metrics')
                .then(response => {
                    const data = response.data;
                    updateKPIs(data.kpis);
                    updateCharts(data);
                    console.log('Rental reports loaded successfully', data);
                })
                .catch(error => {
                    console.error('Failed to load rental reports:', error);
                });
        }

        function updateKPIs(kpis) {
            document.getElementById('kpi-total-rentals').textContent = kpis.total_rentals;
            document.getElementById('kpi-active-rentals').textContent = kpis.active_rentals + ' active';
            document.getElementById('kpi-total-revenue').textContent = '$' + kpis.total_rental_revenue.toFixed(2);
            document.getElementById('kpi-monthly-revenue').textContent = '$' + kpis.revenue_this_month.toFixed(2) + ' this month';
            document.getElementById('kpi-overdue').textContent = kpis.overdue_rentals;
            document.getElementById('kpi-completed').textContent = kpis.completed_rentals + ' completed';
            document.getElementById('kpi-avg-duration').textContent = kpis.avg_rental_duration;
            document.getElementById('secondary-late').textContent = kpis.late_return_rentals;
            document.getElementById('secondary-cancelled').textContent = kpis.cancelled_rentals;
            document.getElementById('secondary-completed').textContent = kpis.completed_rentals;
        }

        function updateCharts(data) {
            const isDark = document.documentElement.classList.contains('dark') || 
                          document.body.classList.contains('dark') ||
                          window.matchMedia('(prefers-color-scheme: dark)').matches;

            const textColor = isDark ? '#e5e7eb' : '#1f2937';
            const gridColor = isDark ? '#27272a' : '#d1d5db';

            // Monthly Rentals Chart
            const monthlyCtx = document.getElementById('monthlyChart').getContext('2d');
            rentalCharts.monthly = new Chart(monthlyCtx, {
                type: 'line',
                data: {
                    labels: data.monthly_rentals.map(m => m.month),
                    datasets: [{
                        label: 'Rentals',
                        data: data.monthly_rentals.map(m => m.count),
                        borderColor: '#0ea5e9',
                        backgroundColor: 'rgba(6, 182, 212, 0.1)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.4,
                        pointBackgroundColor: '#0ea5e9',
                        pointRadius: 4,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            labels: { color: textColor, font: { size: 12, weight: 500 }, padding: 15 }
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

            // Weekly Revenue Chart
            const revenueCtx = document.getElementById('revenueChart').getContext('2d');
            rentalCharts.revenue = new Chart(revenueCtx, {
                type: 'bar',
                data: {
                    labels: data.weekly_revenue.map(w => w.week),
                    datasets: [{
                        label: 'Revenue',
                        data: data.weekly_revenue.map(w => w.revenue),
                        backgroundColor: '#10b981',
                        borderColor: '#059669',
                        borderWidth: 1,
                        borderRadius: 6,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            labels: { color: textColor, font: { size: 12, weight: 500 }, padding: 15 }
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            titleColor: '#fff',
                            bodyColor: '#fff',
                            borderColor: textColor,
                            borderWidth: 1,
                            padding: 10,
                            callbacks: {
                                label: function(context) {
                                    return '$' + context.parsed.y.toFixed(2);
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            ticks: {
                                color: textColor,
                                font: { size: 11 },
                                callback: function(value) {
                                    return '$' + value.toFixed(0);
                                }
                            },
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

            // Rental Status Distribution
            const statusCtx = document.getElementById('statusChart').getContext('2d');
            rentalCharts.status = new Chart(statusCtx, {
                type: 'doughnut',
                data: {
                    labels: data.rental_status_distribution.map(d => d.status),
                    datasets: [{
                        data: data.rental_status_distribution.map(d => d.count),
                        backgroundColor: ['#8b5cf6', '#10b981', '#f59e0b', '#ef4444', '#06b6d4'],
                        borderColor: isDark ? '#1f2937' : '#fff',
                        borderWidth: 2,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            labels: { color: textColor, font: { size: 12, weight: 500 }, padding: 15 }
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

            // Duration Distribution
            const durationCtx = document.getElementById('durationChart').getContext('2d');
            rentalCharts.duration = new Chart(durationCtx, {
                type: 'bar',
                data: {
                    labels: data.duration_distribution.map(d => d.duration),
                    datasets: [{
                        label: 'Count',
                        data: data.duration_distribution.map(d => d.count),
                        backgroundColor: '#f59e0b',
                        borderColor: '#d97706',
                        borderWidth: 1,
                        borderRadius: 6,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    indexAxis: 'y',
                    plugins: {
                        legend: {
                            labels: { color: textColor, font: { size: 12, weight: 500 }, padding: 15 }
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

            // Top Customers Chart
            const topCustomersCtx = document.getElementById('topCustomersChart').getContext('2d');
            rentalCharts.topCustomers = new Chart(topCustomersCtx, {
                type: 'bar',
                data: {
                    labels: data.top_customers.map(c => c.customer_name.split(' ')[0]),
                    datasets: [{
                        label: 'Rentals',
                        data: data.top_customers.map(c => c.rental_count),
                        backgroundColor: '#a78bfa',
                        borderColor: '#9333ea',
                        borderWidth: 1,
                        borderRadius: 6,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    indexAxis: 'y',
                    plugins: {
                        legend: {
                            labels: { color: textColor, font: { size: 12, weight: 500 }, padding: 15 }
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

            // Top Items by Revenue Chart
            const topItemsCtx = document.getElementById('topItemsChart').getContext('2d');
            rentalCharts.topItems = new Chart(topItemsCtx, {
                type: 'bar',
                data: {
                    labels: data.top_items_by_revenue.map(item => item.item_name.substring(0, 15)),
                    datasets: [{
                        label: 'Revenue',
                        data: data.top_items_by_revenue.map(item => item.total_revenue),
                        backgroundColor: '#38bdf8',
                        borderColor: '#0284c7',
                        borderWidth: 1,
                        borderRadius: 6,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    indexAxis: 'y',
                    plugins: {
                        legend: {
                            labels: { color: textColor, font: { size: 12, weight: 500 }, padding: 15 }
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            titleColor: '#fff',
                            bodyColor: '#fff',
                            borderColor: textColor,
                            borderWidth: 1,
                            padding: 10,
                            callbacks: {
                                label: function(context) {
                                    return '$' + context.parsed.x.toFixed(2);
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            ticks: {
                                color: textColor,
                                font: { size: 11 },
                                callback: function(value) {
                                    return '$' + value.toFixed(0);
                                }
                            },
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

         // Set up dark mode observer for theme switching
         if (!window.rentalState.observer) {
             window.rentalState.observer = new MutationObserver((mutations) => {
                 mutations.forEach((mutation) => {
                     if (mutation.type === 'attributes' && mutation.attributeName === 'class') {
                         setTimeout(() => {
                             // Only update if we have data to work with
                             if (!window.rentalChartsData) {
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
                             if (rentalCharts.monthly) updateChartColors(rentalCharts.monthly);
                             if (rentalCharts.revenue) updateChartColors(rentalCharts.revenue);
                             if (rentalCharts.status) updateChartColors(rentalCharts.status);
                             if (rentalCharts.duration) updateChartColors(rentalCharts.duration);
                             if (rentalCharts.topCustomers) updateChartColors(rentalCharts.topCustomers);
                             if (rentalCharts.topItems) updateChartColors(rentalCharts.topItems);
                             
                             // Update all charts with animation
                             Object.values(rentalCharts).forEach(chart => {
                                 if (chart) chart.update('none');
                             });
                         }, 50);
                     }
                 });
             });

             // Start observing immediately
             window.rentalState.observer.observe(document.documentElement, {
                 attributes: true,
                 attributeFilter: ['class'],
                 subtree: false
             });
         }

         initializeRentalReports();
    </script>

</body>
</html>
