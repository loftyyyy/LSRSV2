<!doctype html>
<html lang="en">
<head>
    {{-- Prevent flash of wrong theme --}}
    <script>
        (function() {
            var savedMode = localStorage.getItem('darkMode');
            var isDarkMode = savedMode !== null ? savedMode === 'true' : true;
            document.documentElement.classList.toggle('dark', isDarkMode);
            document.documentElement.style.colorScheme = isDarkMode ? 'dark' : 'light';
            document.documentElement.style.backgroundColor = isDarkMode ? '#000000' : '#f5f5f5';
        })();
    </script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
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

    {{-- App styles --}}
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif

    {{-- Chart.js Library --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.js"></script>

    <style>
        /* Hide scrollbar while keeping scroll functionality */
        html {
            scrollbar-width: none; /* Firefox - hide on document root */
        }
        
        html::-webkit-scrollbar {
            display: none; /* Chrome, Safari and Opera */
        }
        
        body {
            -ms-overflow-style: none; /* IE and Edge */
            scrollbar-width: none; /* Firefox */
        }
        
        body::-webkit-scrollbar {
            display: none; /* Chrome, Safari and Opera */
        }
        
        main {
            -ms-overflow-style: none; /* IE and Edge */
            scrollbar-width: none; /* Firefox */
            overflow-y: scroll; /* Ensure scroll behavior is enabled */
        }
        
        main::-webkit-scrollbar {
            display: none; /* Chrome, Safari and Opera */
        }

        /* Style date input calendar icon for dark mode */
        input[type="date"]::-webkit-calendar-picker-indicator {
            filter: invert(0.7);
            cursor: pointer;
        }

        /* Dark mode - make calendar icon bright white */
        .dark input[type="date"]::-webkit-calendar-picker-indicator {
            filter: brightness(0) invert(1);
            cursor: pointer;
        }

        /* Ensure dark mode text color for date inputs */
        input[type="date"].dark {
            color-scheme: dark;
        }

        /* Fix select dropdown arrow spacing */
        select {
            padding-right: 2rem; /* Ensure space for dropdown arrow */
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3E%3Cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='m6 8 4 4 4-4'/%3E%3C/svg%3E");
            background-position: right 0.5rem center;
            background-repeat: no-repeat;
            background-size: 1.5em 1.5em;
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
        }

        select.dark {
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3E%3Cpath stroke='%23d1d5db' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='m6 8 4 4 4-4'/%3E%3C/svg%3E");
        }
    </style>
</head>
<body class="min-h-screen flex font-geist bg-neutral-100 text-neutral-900 dark:bg-black dark:text-neutral-50 transition-colors duration-300 ease-in-out">
    <x-sidebar />

    <main class="flex-1 ml-64 flex flex-col px-10 py-8 overflow-x-hidden overflow-y-auto bg-gradient-to-b from-neutral-100 via-neutral-100 to-neutral-200 dark:from-black dark:via-black dark:to-neutral-950 transition-colors duration-300 ease-in-out">

        <header class="mb-8 transition-colors duration-300 ease-in-out">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-semibold tracking-tight text-neutral-900 dark:text-white transition-colors duration-300 ease-in-out">
                        Inventory Reports
                    </h1>
                    <p class="mt-1 text-sm font-geist-mono text-neutral-500 dark:text-neutral-400 transition-colors duration-300 ease-in-out">
                        Analyze inventory data and generate detailed reports
                    </p>
                </div>

                <div class="flex items-center gap-3 text-xs">
                    <a href="/inventories" class="inline-flex items-center gap-2 rounded-xl px-3.5 py-2 text-[14px] font-medium border dark:hover:text-black hover:text-white border-neutral-300 bg-white text-neutral-700 dark:hover:bg-violet-600 hover:bg-violet-600  dark:border-neutral-800 dark:bg-neutral-950/80 dark:text-neutral-200 dark:hover:bg-neutral-900  transition-colors duration-300 ease-in-out">
                        <x-icon name="arrow-left" class="h-4 w-4" />
                        <span>Back to Inventory</span>
                    </a>

                    <button onclick="generatePDF()" class="inline-flex items-center gap-2 rounded-xl px-4 py-2 text-[14px] font-medium bg-violet-600 text-white dark:hover:text-white hover:text-black dark:text-black hover:bg-violet-500 shadow-violet-600/40 transition-colors duration-300 ease-in-out">
                        <x-icon name="download" class="h-4 w-4" />
                        <span>Download PDF</span>
                    </button>
                </div>
            </div>
        </header>

        {{-- Report Controls --}}
        <section class="mb-8 rounded-2xl border border-neutral-200 bg-white p-6 dark:border-neutral-900 dark:bg-neutral-950/60 transition-colors duration-300 ease-in-out">
            <h2 class="mb-4 text-lg font-semibold text-neutral-900 dark:text-white">Report Filters</h2>
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <!-- Item Type Filter -->
                <div>
                    <label class="block text-xs font-medium text-neutral-700 dark:text-neutral-300 mb-2">Item Type</label>
                    <select id="itemTypeFilter" class="w-full rounded-lg border border-neutral-300 bg-white px-3 py-2 text-xs text-neutral-900 dark:border-neutral-700 dark:bg-neutral-900 dark:text-white focus:border-violet-500 focus:outline-none pr-8">
                        <option value="">All Types</option>
                        <option value="gown">Gown</option>
                        <option value="suit">Suit</option>
                    </select>
                </div>
                <!-- Status Filter -->
                <div>
                    <label class="block text-xs font-medium text-neutral-700 dark:text-neutral-300 mb-2">Status</label>
                    <select id="statusFilter" class="w-full rounded-lg border border-neutral-300 bg-white px-3 py-2 text-xs text-neutral-900 dark:border-neutral-700 dark:bg-neutral-900 dark:text-white focus:border-violet-500 focus:outline-none pr-8">
                        <option value="">All Statuses</option>
                        <option value="available">Available</option>
                        <option value="rented">Rented</option>
                        <option value="maintenance">Maintenance</option>
                        <option value="damaged">Damaged</option>
                    </select>
                </div>
            </div>
            <div class="mt-4 flex gap-2">
                <button onclick="generateReport()" class="inline-flex items-center gap-2 rounded-lg bg-violet-600 px-4 py-2 text-xs font-medium text-white hover:bg-violet-500 transition-colors">
                    <x-icon name="search" class="h-4 w-4" />
                    Generate Report
                </button>
                <button onclick="resetFilters()" class="inline-flex items-center gap-2 rounded-lg border border-neutral-300 bg-white px-4 py-2 text-xs font-medium text-neutral-700 hover:bg-neutral-50 dark:border-neutral-700 dark:bg-neutral-900 dark:text-neutral-300 dark:hover:bg-neutral-800 transition-colors">
                    <x-icon name="refresh-cw" class="h-4 w-4" />
                    Reset
                </button>
            </div>
        </section>

        {{-- Report Statistics --}}
        <section class="grid grid-cols-1 gap-6 mb-8 md:grid-cols-2 lg:grid-cols-4">
             <div class="rounded-2xl p-6 border border-neutral-200 bg-white dark:border-neutral-900 dark:bg-neutral-950/60 shadow-sm dark:shadow-[0_18px_60px_rgba(0,0,0,0.65)] transition-colors duration-300 ease-in-out">
                 <div class="text-sm text-neutral-500 dark:text-neutral-400 mb-2 transition-colors duration-300 ease-in-out">
                     Total Items
                 </div>
                 <div id="statTotalItems" class="text-3xl font-semibold text-neutral-900 dark:text-white transition-colors duration-300 ease-in-out">
                     0
                 </div>
             </div>

             <div class="rounded-2xl p-6 border border-neutral-200 bg-white dark:border-neutral-900 dark:bg-neutral-950/60 shadow-sm dark:shadow-[0_18px_60px_rgba(0,0,0,0.65)] transition-colors duration-300 ease-in-out">
                 <div class="text-sm text-neutral-500 dark:text-neutral-400 mb-2 transition-colors duration-300 ease-in-out">
                     Available Items
                 </div>
                 <div id="statAvailableItems" class="text-3xl font-semibold text-green-600 dark:text-green-400 transition-colors duration-300 ease-in-out">
                     0
                 </div>
             </div>

             <div class="rounded-2xl p-6 border border-neutral-200 bg-white dark:border-neutral-900 dark:bg-neutral-950/60 shadow-sm dark:shadow-[0_18px_60px_rgba(0,0,0,0.65)] transition-colors duration-300 ease-in-out">
                 <div class="text-sm text-neutral-500 dark:text-neutral-400 mb-2 transition-colors duration-300 ease-in-out">
                     Rented Items
                 </div>
                 <div id="statRentedItems" class="text-3xl font-semibold text-blue-600 dark:text-blue-400 transition-colors duration-300 ease-in-out">
                     0
                 </div>
             </div>

             <div class="rounded-2xl p-6 border border-neutral-200 bg-white dark:border-neutral-900 dark:bg-neutral-950/60 shadow-sm dark:shadow-[0_18px_60px_rgba(0,0,0,0.65)] transition-colors duration-300 ease-in-out">
                 <div class="text-sm text-neutral-500 dark:text-neutral-400 mb-2 transition-colors duration-300 ease-in-out">
                     Maintenance
                 </div>
                 <div id="statMaintenanceItems" class="text-3xl font-semibold text-amber-600 dark:text-amber-400 transition-colors duration-300 ease-in-out">
                     0
                 </div>
             </div>

             <div class="rounded-2xl p-6 border border-neutral-200 bg-white dark:border-neutral-900 dark:bg-neutral-950/60 shadow-sm dark:shadow-[0_18px_60px_rgba(0,0,0,0.65)] transition-colors duration-300 ease-in-out">
                 <div class="text-sm text-neutral-500 dark:text-neutral-400 mb-2 transition-colors duration-300 ease-in-out">
                     Total Inventory Value
                 </div>
                 <div id="statTotalValue" class="text-3xl font-semibold text-violet-600 dark:text-violet-400 transition-colors duration-300 ease-in-out">
                     0
                 </div>
             </div>

             <div class="rounded-2xl p-6 border border-neutral-200 bg-white dark:border-neutral-900 dark:bg-neutral-950/60 shadow-sm dark:shadow-[0_18px_60px_rgba(0,0,0,0.65)] transition-colors duration-300 ease-in-out">
                 <div class="text-sm text-neutral-500 dark:text-neutral-400 mb-2 transition-colors duration-300 ease-in-out">
                     Occupancy Rate
                 </div>
                 <div id="statOccupancyRate" class="text-3xl font-semibold text-purple-600 dark:text-purple-400 transition-colors duration-300 ease-in-out">
                     0%
                 </div>
             </div>

             <div class="rounded-2xl p-6 border border-neutral-200 bg-white dark:border-neutral-900 dark:bg-neutral-950/60 shadow-sm dark:shadow-[0_18px_60px_rgba(0,0,0,0.65)] transition-colors duration-300 ease-in-out">
                 <div class="text-sm text-neutral-500 dark:text-neutral-400 mb-2 transition-colors duration-300 ease-in-out">
                     Damaged Items
                 </div>
                 <div id="statDamagedItems" class="text-3xl font-semibold text-red-600 dark:text-red-400 transition-colors duration-300 ease-in-out">
                     0
                 </div>
             </div>

            <div class="rounded-2xl p-6 border border-neutral-200 bg-white dark:border-neutral-900 dark:bg-neutral-950/60 shadow-sm dark:shadow-[0_18px_60px_rgba(0,0,0,0.65)] transition-colors duration-300 ease-in-out">
                <div class="text-sm text-neutral-500 dark:text-neutral-400 mb-2 transition-colors duration-300 ease-in-out">
                    Report Generated
                </div>
                <div id="statGeneratedAt" class="text-sm font-semibold text-neutral-600 dark:text-neutral-300 transition-colors duration-300 ease-in-out">
                    Not yet
                </div>
            </div>
        </section>

        {{-- Charts Section --}}
        <section class="grid grid-cols-1 gap-6 mb-8 lg:grid-cols-2">
            {{-- Item Status Distribution Chart --}}
            <div class="rounded-2xl border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-900 dark:bg-neutral-950/60 dark:shadow-[0_18px_60px_rgba(0,0,0,0.65)] transition-colors duration-300 ease-in-out">
                <h3 class="text-lg font-semibold text-neutral-900 dark:text-white mb-6">Status Distribution</h3>
                <div style="height: 300px; position: relative;">
                    <canvas id="statusChart"></canvas>
                </div>
            </div>

            {{-- Item Type Distribution Chart --}}
            <div class="rounded-2xl border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-900 dark:bg-neutral-950/60 dark:shadow-[0_18px_60px_rgba(0,0,0,0.65)] transition-colors duration-300 ease-in-out">
                <h3 class="text-lg font-semibold text-neutral-900 dark:text-white mb-6">Item Type Distribution</h3>
                <div style="height: 300px; position: relative;">
                    <canvas id="itemTypeChart"></canvas>
                </div>
            </div>

            {{-- Monthly Rental Activity Chart --}}
            <div class="rounded-2xl border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-900 dark:bg-neutral-950/60 dark:shadow-[0_18px_60px_rgba(0,0,0,0.65)] transition-colors duration-300 ease-in-out">
                <h3 class="text-lg font-semibold text-neutral-900 dark:text-white mb-6">Monthly Rental Activity</h3>
                <div style="height: 300px; position: relative;">
                    <canvas id="monthlyChart"></canvas>
                </div>
            </div>

            {{-- Top Items by Rentals Chart --}}
            <div class="rounded-2xl border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-900 dark:bg-neutral-950/60 dark:shadow-[0_18px_60px_rgba(0,0,0,0.65)] transition-colors duration-300 ease-in-out">
                <h3 class="text-lg font-semibold text-neutral-900 dark:text-white mb-6">Top Items by Rentals</h3>
                <div style="height: 300px; position: relative;">
                    <canvas id="topItemsChart"></canvas>
                </div>
            </div>
        </section>

        {{-- Inventory Details Table --}}
        <section class="rounded-2xl border border-neutral-200 bg-white shadow-sm dark:border-neutral-900 dark:bg-neutral-950/60 dark:shadow-[0_18px_60px_rgba(0,0,0,0.65)] transition-colors duration-300 ease-in-out">
            <div class="px-6 py-4 border-b border-neutral-200 dark:border-neutral-900">
                <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">Inventory Details</h2>
            </div>

            <div class="px-6 py-4">
                <div class="overflow-x-auto">
                    <table class="min-w-full text-left text-xs text-neutral-600 dark:text-neutral-300 transition-colors duration-300 ease-in-out">
                        <thead class="text-[11px] uppercase tracking-[0.18em] text-neutral-500">
                        <tr class="border-b border-neutral-200 dark:border-neutral-900/80">
                            <th class="py-2.5 pr-4 pl-4 font-medium">SKU</th>
                            <th class="py-2.5 pr-4 font-medium">Name</th>
                            <th class="py-2.5 pr-4 font-medium">Type</th>
                            <th class="py-2.5 pr-4 font-medium">Size</th>
                            <th class="py-2.5 pr-4 font-medium">Color</th>
                            <th class="py-2.5 pr-4 font-medium">Status</th>
                            <th class="py-2.5 pr-4 font-medium">Rental Price</th>
                        </tr>
                        </thead>
                        <tbody id="reportTableBody" class="text-[13px]">
                            <tr class="border-b border-neutral-200 dark:border-neutral-900/60">
                                <td colspan="7" class="py-8 text-center text-neutral-500 dark:text-neutral-400">
                                    Click "Generate Report" to view inventory details
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

    </main>

<script>
    // Page state
    var reportsState = {
        observer: null,
        currentData: null,
        currentItems: []
    };
    
    document.addEventListener('DOMContentLoaded', initializeReportsPage);

    function initializeReportsPage() {
        // Load initial report on page load
        generateReport();

        // Add event listeners
        const itemTypeFilterSelect = document.getElementById('itemTypeFilter');
        const statusFilterSelect = document.getElementById('statusFilter');

        itemTypeFilterSelect?.addEventListener('change', generateReport);
        statusFilterSelect?.addEventListener('change', generateReport);
    }

      async function generateReport() {
          try {
              const itemType = document.getElementById('itemTypeFilter')?.value || '';
              const status = document.getElementById('statusFilter')?.value || '';

              // Fetch metrics data
              const metricsResponse = await axios.get('/api/inventories/reports/metrics');
              const metricsData = metricsResponse.data;

              // Fetch items with filters
              const params = new URLSearchParams();
              if (itemType) params.append('item_type', itemType);
              if (status) params.append('status', status);
              params.append('per_page', '100');

              const itemsResponse = await axios.get(`/api/inventories?${params.toString()}`);
              const itemsData = itemsResponse.data;

              // Store data globally
              reportsState.currentData = metricsData;
              reportsState.currentItems = itemsData.data || [];
              
              // Update statistics
              updateStatistics(metricsData.kpis);

              // Update charts
              updateCharts(metricsData);

              // Render table
              renderReportTable(itemsData.data || []);

              // Update generated time
              document.getElementById('statGeneratedAt').textContent = new Date(metricsData.generated_at).toLocaleString();
          } catch (error) {
              console.error('Error generating report:', error);
              showErrorNotification('Failed to generate report. Please try again.');
          }
      }

     function updateStatistics(kpis) {
         document.getElementById('statTotalItems').textContent = kpis.total_items || 0;
         document.getElementById('statAvailableItems').textContent = kpis.available_items || 0;
         document.getElementById('statRentedItems').textContent = kpis.rented_items || 0;
         document.getElementById('statMaintenanceItems').textContent = kpis.maintenance_items || 0;
         document.getElementById('statTotalValue').textContent = '₱' + (kpis.total_value || 0).toLocaleString();
         document.getElementById('statOccupancyRate').textContent = (kpis.occupancy_rate || 0) + '%';
         document.getElementById('statDamagedItems').textContent = kpis.damaged_items || 0;
     }

      function updateCharts(data) {
          // Simply check if the html element has the 'dark' class
          const htmlElement = document.documentElement;
          const isDark = htmlElement.classList.contains('dark');

          const textColor = isDark ? '#e5e7eb' : '#000000';
          const gridColor = isDark ? '#27272a' : '#d1d5db';

          // Status Distribution Chart
          updateStatusChart(data.status_distribution, textColor, gridColor);

          // Item Type Distribution Chart
          updateItemTypeChart(data.item_type_distribution, textColor, gridColor);

          // Monthly Rental Activity Chart
          updateMonthlyChart(data.monthly_rentals, textColor, gridColor);

          // Top Items Chart
          updateTopItemsChart(data.top_items, textColor, gridColor);
      }

    function updateStatusChart(statusData, textColor, gridColor) {
        const ctx = document.getElementById('statusChart')?.getContext('2d');
        if (!ctx) return;

        // Destroy existing chart if it exists
        if (globalThis.statusChartInstance) {
            globalThis.statusChartInstance.destroy();
        }

        globalThis.statusChartInstance = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: statusData.map(d => d.status),
                datasets: [{
                    data: statusData.map(d => d.count),
                    backgroundColor: [
                        '#10b981', // Available - emerald
                        '#3b82f6', // Rented - blue
                        '#f59e0b', // Maintenance - amber
                        '#ef4444'  // Damaged - red
                    ],
                    borderColor: [
                        '#059669',
                        '#2563eb',
                        '#d97706',
                        '#dc2626'
                    ],
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
                            font: { size: 12, weight: '600' },
                            padding: 15,
                        }
                    },
                    tooltip: {
                        titleColor: textColor,
                        bodyColor: textColor,
                        backgroundColor: 'rgba(0,0,0,0.8)',
                        padding: 12,
                        titleFont: { size: 12, weight: 'bold' },
                        bodyFont: { size: 12 },
                    }
                }
            }
        });
    }

    function updateItemTypeChart(itemTypeData, textColor, gridColor) {
        const ctx = document.getElementById('itemTypeChart')?.getContext('2d');
        if (!ctx || !itemTypeData || itemTypeData.length === 0) return;

        // Destroy existing chart if it exists
        if (globalThis.itemTypeChartInstance) {
            globalThis.itemTypeChartInstance.destroy();
        }

        globalThis.itemTypeChartInstance = new Chart(ctx, {
            type: 'pie',
            data: {
                labels: itemTypeData.map(d => d.type),
                datasets: [{
                    data: itemTypeData.map(d => d.count),
                    backgroundColor: [
                        '#8b5cf6', // Gown - violet
                        '#06b6d4', // Suit - cyan
                        '#10b981', // emerald
                        '#f59e0b'  // amber
                    ],
                    borderColor: [
                        '#7c3aed',
                        '#0891b2',
                        '#059669',
                        '#d97706'
                    ],
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
                            font: { size: 12, weight: '600' },
                            padding: 15,
                        }
                    },
                    tooltip: {
                        titleColor: textColor,
                        bodyColor: textColor,
                        backgroundColor: 'rgba(0,0,0,0.8)',
                        padding: 12,
                        titleFont: { size: 12, weight: 'bold' },
                        bodyFont: { size: 12 },
                    }
                }
            }
        });
    }

      function updateMonthlyChart(monthlyData, textColor, gridColor) {
          const ctx = document.getElementById('monthlyChart')?.getContext('2d');
          if (!ctx) return;

          // Destroy existing chart if it exists
          if (globalThis.monthlyChartInstance) {
              globalThis.monthlyChartInstance.destroy();
          }

          globalThis.monthlyChartInstance = new Chart(ctx, {
              type: 'line',
              data: {
                  labels: monthlyData.map(m => m.month),
                  datasets: [{
                      label: 'Items Rented',
                      data: monthlyData.map(m => m.count),
                      borderColor: '#06b6d4', // cyan
                      backgroundColor: 'rgba(6, 182, 212, 0.1)',
                      borderWidth: 3,
                      fill: true,
                      tension: 0.4,
                      pointBackgroundColor: '#06b6d4',
                      pointBorderColor: '#ffffff',
                      pointBorderWidth: 2,
                      pointRadius: 6,
                  }]
              },
              options: {
                  responsive: true,
                  maintainAspectRatio: false,
                  plugins: {
                      legend: {
                          labels: {
                              color: textColor,
                              font: { size: 12, weight: '600' },
                              padding: 15,
                          }
                      },
                      tooltip: {
                          titleColor: textColor,
                          bodyColor: textColor,
                          backgroundColor: 'rgba(0,0,0,0.8)',
                          padding: 12,
                          titleFont: { size: 12, weight: 'bold' },
                          bodyFont: { size: 12 },
                      }
                  },
                  scales: {
                      y: {
                          beginAtZero: true,
                          ticks: { 
                              color: textColor,
                              font: { size: 11, weight: '500' }
                          },
                          grid: { color: gridColor },
                      },
                      x: {
                          ticks: { 
                              color: textColor,
                              font: { size: 11, weight: '500' }
                          },
                          grid: { color: gridColor },
                      }
                  }
              }
          });
      }

    function updateTopItemsChart(topItems, textColor, gridColor) {
        const ctx = document.getElementById('topItemsChart')?.getContext('2d');
        if (!ctx || !topItems || topItems.length === 0) return;

        const names = topItems.map(i => i.name.substring(0, 15));
        const rentals = topItems.map(i => i.rental_count);

        // Destroy existing chart if it exists
        if (globalThis.topItemsChartInstance) {
            globalThis.topItemsChartInstance.destroy();
        }

        globalThis.topItemsChartInstance = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: names,
                datasets: [{
                    label: 'Total Rentals',
                    data: rentals,
                    backgroundColor: '#8b5cf6', // violet
                    borderColor: '#7c3aed',
                    borderWidth: 2,
                    borderRadius: 8,
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
                            font: { size: 12, weight: '600' },
                            padding: 15,
                        }
                    },
                    tooltip: {
                        titleColor: textColor,
                        bodyColor: textColor,
                        backgroundColor: 'rgba(0,0,0,0.8)',
                        padding: 12,
                        titleFont: { size: 12, weight: 'bold' },
                        bodyFont: { size: 12 },
                    }
                },
                scales: {
                    x: {
                        ticks: { 
                            color: textColor,
                            font: { size: 11, weight: '500' }
                        },
                        grid: { color: gridColor },
                    },
                    y: {
                        ticks: { 
                            color: textColor,
                            font: { size: 11, weight: '500' }
                        },
                        grid: { color: gridColor },
                    }
                }
            }
        });
    }

    function renderReportTable(items) {
        const tbody = document.getElementById('reportTableBody');
        if (!tbody) return;

        if (!items || items.length === 0) {
            tbody.innerHTML = `
                <tr class="border-b border-neutral-200 dark:border-neutral-900/60">
                    <td colspan="7" class="py-8 text-center text-neutral-500 dark:text-neutral-400">
                        No items found for the selected filters
                    </td>
                </tr>
            `;
            return;
        }

        tbody.innerHTML = items.map(item => {
            const statusName = item.status?.status_name || 'unknown';
            const statusColor = statusName === 'available'
                ? 'bg-emerald-500/15 text-emerald-600 border-emerald-500/40 dark:text-emerald-300'
                : statusName === 'rented'
                ? 'bg-blue-500/15 text-blue-600 border-blue-500/40 dark:text-blue-300'
                : statusName === 'maintenance'
                ? 'bg-amber-500/15 text-amber-600 border-amber-500/40 dark:text-amber-300'
                : 'bg-red-500/15 text-red-600 border-red-500/40 dark:text-red-300';

            const statusBgColor = statusName === 'available' ? 'bg-emerald-500' 
                : statusName === 'rented' ? 'bg-blue-500'
                : statusName === 'maintenance' ? 'bg-amber-500'
                : 'bg-red-500';

            const itemType = item.item_type ? item.item_type.charAt(0).toUpperCase() + item.item_type.slice(1) : 'N/A';

            return `
                <tr class="border-b border-neutral-200 hover:bg-neutral-100 dark:border-neutral-900/60 dark:hover:bg-white/5 transition-colors">
                    <td class="py-3.5 pr-4 pl-4 text-neutral-500 font-geist-mono">${item.sku || 'N/A'}</td>
                    <td class="py-3.5 pr-4 text-neutral-900 dark:text-neutral-100">${item.name || 'N/A'}</td>
                    <td class="py-3.5 pr-4 text-neutral-600 dark:text-neutral-300">${itemType}</td>
                    <td class="py-3.5 pr-4 text-neutral-600 dark:text-neutral-300">${item.size || 'N/A'}</td>
                    <td class="py-3.5 pr-4 text-neutral-600 dark:text-neutral-300">${item.color || 'N/A'}</td>
                    <td class="py-3.5 pr-4">
                        <span class="inline-flex items-center rounded-full ${statusColor} px-2 py-1 text-[10px] font-medium border">
                            <span class="mr-1 h-1.5 w-1.5 rounded-full ${statusBgColor}"></span>
                            ${statusName.charAt(0).toUpperCase() + statusName.slice(1)}
                        </span>
                    </td>
                    <td class="py-3.5 pr-4 font-geist-mono">₱${(item.rental_price || 0).toLocaleString()}</td>
                </tr>
            `;
        }).join('');
    }

    function resetFilters() {
        document.getElementById('itemTypeFilter').value = '';
        document.getElementById('statusFilter').value = '';
        generateReport();
    }

    async function generatePDF() {
        try {
            const itemType = document.getElementById('itemTypeFilter')?.value || '';
            const status = document.getElementById('statusFilter')?.value || '';

            const params = new URLSearchParams();
            params.append('report_type', 'summary');
            if (itemType) params.append('item_type', itemType);
            if (status) params.append('status', status);

            const url = `/api/inventories/reports/pdf${params.toString() ? '?' + params.toString() : ''}`;
            window.open(url, '_blank');
        } catch (error) {
            console.error('Error generating PDF:', error);
            showErrorNotification('Failed to generate PDF. Please try again.');
        }
    }

     function showErrorNotification(message) {
         const notification = document.createElement('div');
         notification.className = 'fixed top-4 right-4 max-w-sm bg-red-100 border border-red-300 dark:bg-red-900 dark:border-red-700 rounded-lg px-5 py-4 shadow-xl z-[999] flex items-start gap-3 animate-in slide-in-from-top-2';
         notification.innerHTML = `
             <svg class="h-5 w-5 text-red-700 dark:text-red-200 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                 <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
             </svg>
             <div class="flex-1">
                 <p class="text-sm font-semibold text-red-900 dark:text-red-50">${message}</p>
             </div>
             <button onclick="this.parentElement.remove()" class="text-red-700 dark:text-red-200 hover:text-red-900 dark:hover:text-red-50 flex-shrink-0 transition-colors">
                 <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                     <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                 </svg>
             </button>
         `;
         document.body.appendChild(notification);

         setTimeout(() => {
             notification.style.opacity = '0';
             notification.style.transition = 'opacity 0.3s ease-out';
             setTimeout(() => {
                 notification.remove();
             }, 300);
         }, 5000);
     }

        // Set up dark mode observer immediately
        if (!reportsState.observer) {
            reportsState.observer = new MutationObserver((mutations) => {
                mutations.forEach((mutation) => {
                    if (mutation.type === 'attributes' && mutation.attributeName === 'class') {
                        // Small delay to ensure CSS transitions have completed
                        setTimeout(() => {
                            // Only update if we have data to work with
                            if (!reportsState.currentData) {
                                return;
                            }

                            // Detect current theme
                            const htmlElement = document.documentElement;
                            const isDark = htmlElement.classList.contains('dark');
                            const textColor = isDark ? '#e5e7eb' : '#000000';
                            const gridColor = isDark ? '#27272a' : '#d1d5db';
                            
                            // Helper function to update chart colors
                            const updateChartColors = (chart, isDark) => {
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
                                        if (scale.pointLabels) {
                                            scale.pointLabels.color = text;
                                        }
                                    });
                                }
                            };
                            
                            // Update all existing charts with new colors
                            if (globalThis.statusChartInstance) {
                                updateChartColors(globalThis.statusChartInstance, isDark);
                                globalThis.statusChartInstance.update('none');
                            }
                            
                            if (globalThis.itemTypeChartInstance) {
                                updateChartColors(globalThis.itemTypeChartInstance, isDark);
                                globalThis.itemTypeChartInstance.update('none');
                            }
                            
                            if (globalThis.monthlyChartInstance) {
                                updateChartColors(globalThis.monthlyChartInstance, isDark);
                                globalThis.monthlyChartInstance.update('none');
                            }
                            
                            if (globalThis.topItemsChartInstance) {
                                updateChartColors(globalThis.topItemsChartInstance, isDark);
                                globalThis.topItemsChartInstance.update('none');
                            }
                        }, 50);
                    }
                });
            });

            // Start observing immediately (not waiting for DOMContentLoaded)
            reportsState.observer.observe(document.documentElement, {
                attributes: true,
                attributeFilter: ['class'],
                subtree: false
            });
        }
</script>

</body>
</html>
