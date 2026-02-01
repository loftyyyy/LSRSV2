<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory Reports · Love &amp; Styles</title>

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

        .table-container {
            overflow-x: auto;
        }

        .table-container table {
            min-width: 100%;
            border-collapse: collapse;
        }

        .table-container tbody tr {
            border-bottom: 1px solid #e5e7eb;
        }

        .dark .table-container tbody tr {
            border-bottom-color: #27272a;
        }

        .table-container td, .table-container th {
            padding: 12px;
            text-align: left;
        }

        .table-container th {
            background-color: #f9fafb;
            font-weight: 600;
            font-size: 0.875rem;
            color: #374151;
        }

        .dark .table-container th {
            background-color: #27272a;
            color: #d1d5db;
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
                        Inventory Reports
                    </h1>
                    <p class="mt-1 text-sm font-geist-mono text-neutral-500 dark:text-neutral-400 transition-colors duration-300 ease-in-out">
                        Track stock levels, item performance, and inventory health metrics.
                    </p>
                </div>
            </div>
        </header>

        <!-- KPI Cards Section -->
        <section class="mb-8">
            <h2 class="text-lg font-semibold text-neutral-900 dark:text-white mb-4">Key Metrics</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
                <!-- Total Items -->
                <div class="stat-card bg-white dark:bg-neutral-900 rounded-lg border border-neutral-200 dark:border-neutral-800 p-6 shadow-sm">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-neutral-500 dark:text-neutral-400">Total Items</p>
                            <p class="text-2xl font-semibold text-neutral-900 dark:text-white mt-1" id="kpi-total-items">0</p>
                            <p class="text-xs text-neutral-500 dark:text-neutral-400 mt-2" id="kpi-occupancy">0% rented</p>
                        </div>
                        <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/30 rounded-full flex items-center justify-center">
                            <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4m0 0L4 7m16 0l-8 4m0 0l8 4m-8-4v10l8-4m-8 4L4 11m16 0l-8-4"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Total Value -->
                <div class="stat-card bg-white dark:bg-neutral-900 rounded-lg border border-neutral-200 dark:border-neutral-800 p-6 shadow-sm">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-neutral-500 dark:text-neutral-400">Total Value</p>
                            <p class="text-2xl font-semibold text-neutral-900 dark:text-white mt-1" id="kpi-total-value">$0.00</p>
                            <p class="text-xs text-neutral-500 dark:text-neutral-400 mt-2" id="kpi-value-at-risk">$0 at risk</p>
                        </div>
                        <div class="w-12 h-12 bg-emerald-100 dark:bg-emerald-900/30 rounded-full flex items-center justify-center">
                            <svg class="w-6 h-6 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Status Distribution -->
                <div class="stat-card bg-white dark:bg-neutral-900 rounded-lg border border-neutral-200 dark:border-neutral-800 p-6 shadow-sm">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-neutral-500 dark:text-neutral-400">Available Items</p>
                            <p class="text-2xl font-semibold text-green-600 dark:text-green-400 mt-1" id="kpi-available">0</p>
                            <p class="text-xs text-neutral-500 dark:text-neutral-400 mt-2" id="secondary-rented">0 rented</p>
                        </div>
                        <div class="w-12 h-12 bg-green-100 dark:bg-green-900/30 rounded-full flex items-center justify-center">
                            <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Condition Health -->
                <div class="stat-card bg-white dark:bg-neutral-900 rounded-lg border border-neutral-200 dark:border-neutral-800 p-6 shadow-sm">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-neutral-500 dark:text-neutral-400">Excellent Condition</p>
                            <p class="text-2xl font-semibold text-purple-600 dark:text-purple-400 mt-1" id="kpi-excellent">0</p>
                            <p class="text-xs text-neutral-500 dark:text-neutral-400 mt-2" id="secondary-poor">0 poor condition</p>
                        </div>
                        <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900/30 rounded-full flex items-center justify-center">
                            <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Secondary KPIs -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                <div class="bg-white dark:bg-neutral-900 rounded-lg border border-neutral-200 dark:border-neutral-800 p-4">
                    <p class="text-xs font-medium text-neutral-500 dark:text-neutral-400">Maintenance</p>
                    <p class="text-lg font-semibold text-yellow-600 dark:text-yellow-400 mt-1" id="secondary-maintenance">0</p>
                </div>

                <div class="bg-white dark:bg-neutral-900 rounded-lg border border-neutral-200 dark:border-neutral-800 p-4">
                    <p class="text-xs font-medium text-neutral-500 dark:text-neutral-400">Damaged</p>
                    <p class="text-lg font-semibold text-red-600 dark:text-red-400 mt-1" id="secondary-damaged">0</p>
                </div>

                <div class="bg-white dark:bg-neutral-900 rounded-lg border border-neutral-200 dark:border-neutral-800 p-4">
                    <p class="text-xs font-medium text-neutral-500 dark:text-neutral-400">Good Condition</p>
                    <p class="text-lg font-semibold text-blue-600 dark:text-blue-400 mt-1" id="secondary-good">0</p>
                </div>

                <div class="bg-white dark:bg-neutral-900 rounded-lg border border-neutral-200 dark:border-neutral-800 p-4">
                    <p class="text-xs font-medium text-neutral-500 dark:text-neutral-400">Fair Condition</p>
                    <p class="text-lg font-semibold text-orange-600 dark:text-orange-400 mt-1" id="secondary-fair">0</p>
                </div>
            </div>
        </section>

        <!-- Charts Section -->
        <section class="mb-8">
            <h2 class="text-lg font-semibold text-neutral-900 dark:text-white mb-4">Analytics & Performance</h2>
            
            <div class="chart-grid grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                <!-- Item Status Distribution -->
                <div class="bg-white dark:bg-neutral-900 rounded-lg border border-neutral-200 dark:border-neutral-800 p-6 shadow-sm">
                    <h3 class="text-sm font-semibold text-neutral-900 dark:text-white mb-4">Item Status Distribution</h3>
                    <div class="chart-container">
                        <canvas id="statusChart"></canvas>
                    </div>
                </div>

                <!-- Condition Distribution -->
                <div class="bg-white dark:bg-neutral-900 rounded-lg border border-neutral-200 dark:border-neutral-800 p-6 shadow-sm">
                    <h3 class="text-sm font-semibold text-neutral-900 dark:text-white mb-4">Condition Distribution</h3>
                    <div class="chart-container">
                        <canvas id="conditionChart"></canvas>
                    </div>
                </div>

                <!-- Monthly Rental Activity -->
                <div class="bg-white dark:bg-neutral-900 rounded-lg border border-neutral-200 dark:border-neutral-800 p-6 shadow-sm">
                    <h3 class="text-sm font-semibold text-neutral-900 dark:text-white mb-4">Monthly Rental Activity (Last 12 Months)</h3>
                    <div class="chart-container">
                        <canvas id="monthlyChart"></canvas>
                    </div>
                </div>

                <!-- Item Type Distribution -->
                <div class="bg-white dark:bg-neutral-900 rounded-lg border border-neutral-200 dark:border-neutral-800 p-6 shadow-sm">
                    <h3 class="text-sm font-semibold text-neutral-900 dark:text-white mb-4">Items by Category</h3>
                    <div class="chart-container">
                        <canvas id="itemTypeChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Two Column Charts -->
            <div class="chart-grid grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Top Items -->
                <div class="bg-white dark:bg-neutral-900 rounded-lg border border-neutral-200 dark:border-neutral-800 p-6 shadow-sm">
                    <h3 class="text-sm font-semibold text-neutral-900 dark:text-white mb-4">Top 8 Most Rented Items</h3>
                    <div class="chart-container">
                        <canvas id="topItemsChart"></canvas>
                    </div>
                </div>

                <!-- Value by Type -->
                <div class="bg-white dark:bg-neutral-900 rounded-lg border border-neutral-200 dark:border-neutral-800 p-6 shadow-sm">
                    <h3 class="text-sm font-semibold text-neutral-900 dark:text-white mb-4">Inventory Value by Category</h3>
                    <div class="chart-container">
                        <canvas id="valueChart"></canvas>
                    </div>
                </div>
            </div>
        </section>

    </main>

    <script>
        let inventoryCharts = {
            status: null,
            condition: null,
            monthly: null,
            itemType: null,
            topItems: null,
            value: null,
        };

        function cleanupCharts() {
            Object.values(inventoryCharts).forEach(chart => {
                if (chart && typeof chart.destroy === 'function') {
                    chart.destroy();
                }
            });
            inventoryCharts = {
                status: null,
                condition: null,
                monthly: null,
                itemType: null,
                topItems: null,
                value: null,
            };
        }

        function initializeInventoryReports() {
            axios.get('/api/inventories/reports/metrics')
                .then(response => {
                    const data = response.data;
                    updateKPIs(data.kpis);
                    updateCharts(data);
                    console.log('Inventory reports loaded successfully', data);
                })
                .catch(error => {
                    console.error('Failed to load inventory reports:', error);
                });
        }

        function updateKPIs(kpis) {
            document.getElementById('kpi-total-items').textContent = kpis.total_items;
            document.getElementById('kpi-occupancy').textContent = kpis.occupancy_rate + '% rented';
            document.getElementById('kpi-total-value').textContent = '$' + kpis.total_value.toFixed(2);
            document.getElementById('kpi-value-at-risk').textContent = '$' + kpis.value_at_risk.toFixed(2) + ' at risk';
            document.getElementById('kpi-available').textContent = kpis.available_items;
            document.getElementById('secondary-rented').textContent = kpis.rented_items + ' rented';
            document.getElementById('kpi-excellent').textContent = kpis.excellent_condition;
            document.getElementById('secondary-poor').textContent = kpis.poor_condition + ' poor condition';
            document.getElementById('secondary-maintenance').textContent = kpis.maintenance_items;
            document.getElementById('secondary-damaged').textContent = kpis.damaged_items;
            document.getElementById('secondary-good').textContent = kpis.good_condition;
            document.getElementById('secondary-fair').textContent = kpis.fair_condition;
        }

        function updateCharts(data) {
            const isDark = document.documentElement.classList.contains('dark') || 
                          document.body.classList.contains('dark') ||
                          window.matchMedia('(prefers-color-scheme: dark)').matches;

            const textColor = isDark ? '#e5e7eb' : '#1f2937';
            const gridColor = isDark ? '#27272a' : '#d1d5db';

            // Status Distribution Chart
            const statusCtx = document.getElementById('statusChart').getContext('2d');
            inventoryCharts.status = new Chart(statusCtx, {
                type: 'doughnut',
                data: {
                    labels: data.status_distribution.map(d => d.status),
                    datasets: [{
                        data: data.status_distribution.map(d => d.count),
                        backgroundColor: ['#10b981', '#8b5cf6', '#f59e0b', '#ef4444'],
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

            // Condition Distribution Chart
            const conditionCtx = document.getElementById('conditionChart').getContext('2d');
            inventoryCharts.condition = new Chart(conditionCtx, {
                type: 'pie',
                data: {
                    labels: data.condition_distribution.map(d => d.condition),
                    datasets: [{
                        data: data.condition_distribution.map(d => d.count),
                        backgroundColor: ['#06b6d4', '#10b981', '#f59e0b', '#ef4444'],
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

            // Monthly Rental Activity
            const monthlyCtx = document.getElementById('monthlyChart').getContext('2d');
            inventoryCharts.monthly = new Chart(monthlyCtx, {
                type: 'line',
                data: {
                    labels: data.monthly_rentals.map(m => m.month),
                    datasets: [{
                        label: 'Items Rented',
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

            // Item Type Distribution
            const itemTypeCtx = document.getElementById('itemTypeChart').getContext('2d');
            inventoryCharts.itemType = new Chart(itemTypeCtx, {
                type: 'bar',
                data: {
                    labels: data.item_type_distribution.map(d => d.type),
                    datasets: [{
                        label: 'Count',
                        data: data.item_type_distribution.map(d => d.count),
                        backgroundColor: '#8b5cf6',
                        borderColor: '#7c3aed',
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

            // Top Items Chart
            const topItemsCtx = document.getElementById('topItemsChart').getContext('2d');
            inventoryCharts.topItems = new Chart(topItemsCtx, {
                type: 'bar',
                data: {
                    labels: data.top_items.map(item => item.name.substring(0, 15)),
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

            // Value by Type Chart
            const valueCtx = document.getElementById('valueChart').getContext('2d');
            inventoryCharts.value = new Chart(valueCtx, {
                type: 'bar',
                data: {
                    labels: data.value_by_type.map(d => d.type),
                    datasets: [{
                        label: 'Total Value ($)',
                        data: data.value_by_type.map(d => d.value),
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

        document.addEventListener('turbo:load', function() {
            if (window.location.pathname === '/inventories/reports') {
                cleanupCharts();
                initializeInventoryReports();
            }
        });

        document.addEventListener('turbo:before-visit', function() {
            cleanupCharts();
        });

        initializeInventoryReports();
    </script>

</body>
</html>
