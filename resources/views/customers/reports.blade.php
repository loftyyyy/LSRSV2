<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Customer Reports · Love &amp; Styles</title>

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
        /* Hide scrollbar while keeping functionality */
        main::-webkit-scrollbar {
            display: none;
        }
    </style>
</head>
<body class="min-h-screen flex font-geist bg-neutral-100 text-neutral-900 dark:bg-black dark:text-neutral-50 transition-colors duration-300 ease-in-out">
    <x-sidebar />

    <main class="flex-1 ml-64 flex flex-col px-10 py-8 overflow-x-hidden overflow-y-auto bg-gradient-to-b from-neutral-100 via-neutral-100 to-neutral-200 dark:from-black dark:via-black dark:to-neutral-950 transition-colors duration-300 ease-in-out scrollbar-hide" style="scrollbar-width: none; -ms-overflow-style: none;">

        <header class="mb-8 transition-colors duration-300 ease-in-out">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-semibold tracking-tight text-neutral-900 dark:text-white transition-colors duration-300 ease-in-out">
                        Customer Reports
                    </h1>
                    <p class="mt-1 text-sm font-geist-mono text-neutral-500 dark:text-neutral-400 transition-colors duration-300 ease-in-out">
                        Analyze customer data and generate detailed reports
                    </p>
                </div>

                <div class="flex items-center gap-3 text-xs">
                    <a href="/customers" class="inline-flex items-center gap-2 rounded-xl px-3.5 py-2 text-[14px] font-medium border dark:hover:text-black hover:text-white border-neutral-300 bg-white text-neutral-700 dark:hover:bg-violet-600 hover:bg-violet-600  dark:border-neutral-800 dark:bg-neutral-950/80 dark:text-neutral-200 dark:hover:bg-neutral-900  transition-colors duration-300 ease-in-out">
                        <x-icon name="arrow-left" class="h-4 w-4" />
                        <span>Back to Customers</span>
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
            <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                <!-- Date Range -->
                <div>
                    <label class="block text-xs font-medium text-neutral-700 dark:text-neutral-300 mb-2">Start Date</label>
                    <input id="startDate" type="date" class="w-full rounded-lg border border-neutral-300 bg-white px-3 py-2 text-xs text-neutral-900 dark:border-neutral-700 dark:bg-neutral-900 dark:text-white focus:border-violet-500 focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs font-medium text-neutral-700 dark:text-neutral-300 mb-2">End Date</label>
                    <input id="endDate" type="date" class="w-full rounded-lg border border-neutral-300 bg-white px-3 py-2 text-xs text-neutral-900 dark:border-neutral-700 dark:bg-neutral-900 dark:text-white focus:border-violet-500 focus:outline-none">
                </div>
                <!-- Status Filter -->
                <div>
                    <label class="block text-xs font-medium text-neutral-700 dark:text-neutral-300 mb-2">Status</label>
                    <select id="statusFilter" class="w-full rounded-lg border border-neutral-300 bg-white px-3 py-2 text-xs text-neutral-900 dark:border-neutral-700 dark:bg-neutral-900 dark:text-white focus:border-violet-500 focus:outline-none">
                        <option value="">All Statuses</option>
                        <option value="1">Active</option>
                        <option value="2">Inactive</option>
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
                     Total Customers
                 </div>
                 <div id="statTotalCustomers" class="text-3xl font-semibold text-green-600 dark:text-green-400 transition-colors duration-300 ease-in-out">
                     0
                 </div>
             </div>

             <div class="rounded-2xl p-6 border border-neutral-200 bg-white dark:border-neutral-900 dark:bg-neutral-950/60 shadow-sm dark:shadow-[0_18px_60px_rgba(0,0,0,0.65)] transition-colors duration-300 ease-in-out">
                 <div class="text-sm text-neutral-500 dark:text-neutral-400 mb-2 transition-colors duration-300 ease-in-out">
                     Active Customers
                 </div>
                 <div id="statActiveCustomers" class="text-3xl font-semibold text-blue-600 dark:text-blue-400 transition-colors duration-300 ease-in-out">
                     0
                 </div>
             </div>

             <div class="rounded-2xl p-6 border border-neutral-200 bg-white dark:border-neutral-900 dark:bg-neutral-950/60 shadow-sm dark:shadow-[0_18px_60px_rgba(0,0,0,0.65)] transition-colors duration-300 ease-in-out">
                 <div class="text-sm text-neutral-500 dark:text-neutral-400 mb-2 transition-colors duration-300 ease-in-out">
                     Inactive Customers
                 </div>
                 <div id="statInactiveCustomers" class="text-3xl font-semibold text-red-600 dark:text-red-400 transition-colors duration-300 ease-in-out">
                     0
                 </div>
             </div>

             <div class="rounded-2xl p-6 border border-neutral-200 bg-white dark:border-neutral-900 dark:bg-neutral-950/60 shadow-sm dark:shadow-[0_18px_60px_rgba(0,0,0,0.65)] transition-colors duration-300 ease-in-out">
                 <div class="text-sm text-neutral-500 dark:text-neutral-400 mb-2 transition-colors duration-300 ease-in-out">
                     Customers with Rentals
                 </div>
                 <div id="statCustomersWithRentals" class="text-3xl font-semibold text-emerald-600 dark:text-emerald-400 transition-colors duration-300 ease-in-out">
                     0
                 </div>
             </div>

             <div class="rounded-2xl p-6 border border-neutral-200 bg-white dark:border-neutral-900 dark:bg-neutral-950/60 shadow-sm dark:shadow-[0_18px_60px_rgba(0,0,0,0.65)] transition-colors duration-300 ease-in-out">
                 <div class="text-sm text-neutral-500 dark:text-neutral-400 mb-2 transition-colors duration-300 ease-in-out">
                     Total Rentals
                 </div>
                 <div id="statTotalRentals" class="text-3xl font-semibold text-amber-600 dark:text-amber-400 transition-colors duration-300 ease-in-out">
                     0
                 </div>
             </div>

             <div class="rounded-2xl p-6 border border-neutral-200 bg-white dark:border-neutral-900 dark:bg-neutral-950/60 shadow-sm dark:shadow-[0_18px_60px_rgba(0,0,0,0.65)] transition-colors duration-300 ease-in-out">
                 <div class="text-sm text-neutral-500 dark:text-neutral-400 mb-2 transition-colors duration-300 ease-in-out">
                     Avg Rentals per Customer
                 </div>
                 <div id="statAvgRentals" class="text-3xl font-semibold text-purple-600 dark:text-purple-400 transition-colors duration-300 ease-in-out">
                     0.0
                 </div>
             </div>

             <div class="rounded-2xl p-6 border border-neutral-200 bg-white dark:border-neutral-900 dark:bg-neutral-950/60 shadow-sm dark:shadow-[0_18px_60px_rgba(0,0,0,0.65)] transition-colors duration-300 ease-in-out">
                 <div class="text-sm text-neutral-500 dark:text-neutral-400 mb-2 transition-colors duration-300 ease-in-out">
                     Total Reservations
                 </div>
                 <div id="statTotalReservations" class="text-3xl font-semibold text-cyan-600 dark:text-cyan-400 transition-colors duration-300 ease-in-out">
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
            {{-- Customer Status Distribution Chart --}}
            <div class="rounded-2xl border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-900 dark:bg-neutral-950/60 dark:shadow-[0_18px_60px_rgba(0,0,0,0.65)] transition-colors duration-300 ease-in-out">
                <h3 class="text-lg font-semibold text-neutral-900 dark:text-white mb-6">Customer Status Distribution</h3>
                <div style="height: 300px; position: relative;">
                    <canvas id="statusChart"></canvas>
                </div>
            </div>

            {{-- Rentals Overview Chart --}}
            <div class="rounded-2xl border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-900 dark:bg-neutral-950/60 dark:shadow-[0_18px_60px_rgba(0,0,0,0.65)] transition-colors duration-300 ease-in-out">
                <h3 class="text-lg font-semibold text-neutral-900 dark:text-white mb-6">Top Customers by Rentals</h3>
                <div style="height: 300px; position: relative;">
                    <canvas id="rentalsChart"></canvas>
                </div>
            </div>

            {{-- Customer Acquisition Chart --}}
            <div class="rounded-2xl border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-900 dark:bg-neutral-950/60 dark:shadow-[0_18px_60px_rgba(0,0,0,0.65)] transition-colors duration-300 ease-in-out">
                <h3 class="text-lg font-semibold text-neutral-900 dark:text-white mb-6">Registration Trend</h3>
                <div style="height: 300px; position: relative;">
                    <canvas id="acquisitionChart"></canvas>
                </div>
            </div>

            {{-- Reservation vs Rental Chart --}}
            <div class="rounded-2xl border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-900 dark:bg-neutral-950/60 dark:shadow-[0_18px_60px_rgba(0,0,0,0.65)] transition-colors duration-300 ease-in-out">
                <h3 class="text-lg font-semibold text-neutral-900 dark:text-white mb-6">Reservations vs Rentals</h3>
                <div style="height: 300px; position: relative;">
                    <canvas id="comparisonChart"></canvas>
                </div>
            </div>
        </section>

        
        <section class="rounded-2xl border border-neutral-200 bg-white shadow-sm dark:border-neutral-900 dark:bg-neutral-950/60 dark:shadow-[0_18px_60px_rgba(0,0,0,0.65)] transition-colors duration-300 ease-in-out">
            <div class="px-6 py-4 border-b border-neutral-200 dark:border-neutral-900">
                <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">Customer Details</h2>
            </div>

            <div class="px-6 py-4">
                <div class="overflow-x-auto">
                    <table class="min-w-full text-left text-xs text-neutral-600 dark:text-neutral-300 transition-colors duration-300 ease-in-out">
                        <thead class="text-[11px] uppercase tracking-[0.18em] text-neutral-500">
                        <tr class="border-b border-neutral-200 dark:border-neutral-900/80">
                            <th class="py-2.5 pr-4 pl-4 font-medium">ID</th>
                            <th class="py-2.5 pr-4 font-medium">Name</th>
                            <th class="py-2.5 pr-4 font-medium">Email</th>
                            <th class="py-2.5 pr-4 font-medium">Phone</th>
                            <th class="py-2.5 pr-4 font-medium">Status</th>
                            <th class="py-2.5 pr-4 font-medium">Total Rentals</th>
                            <th class="py-2.5 pr-4 font-medium">Reservations</th>
                            <th class="py-2.5 pr-4 font-medium">Registered</th>
                            <th class="py-2.5 pr-4 font-medium">Last Rental</th>
                        </tr>
                        </thead>
                        <tbody id="reportTableBody" class="text-[13px]">
                            <tr class="border-b border-neutral-200 dark:border-neutral-900/60">
                                <td colspan="9" class="py-8 text-center text-neutral-500 dark:text-neutral-400">
                                    Click "Generate Report" to view customer details
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

    </main>

<script>
    // Initialize report page - use a guard flag to prevent multiple initializations
    let reportsPageInitialized = false;
    
    document.addEventListener('DOMContentLoaded', initializeReportsPage);
    document.addEventListener('turbo:load', initializeReportsPage);

    function initializeReportsPage() {
        console.log('initializeReportsPage called');
        console.log('Current pathname:', window.location.pathname);
        
        // Check if we're on the reports page
        if (!window.location.pathname.includes('/customers/reports')) {
            console.log('Not on reports page, skipping initialization');
            return;
        }

        // Guard against multiple initializations
        if (reportsPageInitialized) {
            console.log('Reports page already initialized, skipping');
            return;
        }

        console.log('Initializing reports page');
        reportsPageInitialized = true;

        // Clean up any previous listeners
        if (globalThis.reportsCleanup) {
            console.log('Cleaning up previous listeners');
            globalThis.reportsCleanup();
        }

        // Load initial report on page load
        console.log('Calling generateReport()');
        generateReport();

        // Add event listeners
        const startDateInput = document.getElementById('startDate');
        const endDateInput = document.getElementById('endDate');
        const statusFilterSelect = document.getElementById('statusFilter');

        console.log('Found input elements:', { startDateInput, endDateInput, statusFilterSelect });

        const handleDateChange = () => generateReport();
        const handleStatusChange = () => generateReport();

        startDateInput?.addEventListener('change', handleDateChange);
        endDateInput?.addEventListener('change', handleDateChange);
        statusFilterSelect?.addEventListener('change', handleStatusChange);

        console.log('Event listeners attached');

        // Store cleanup function for next page leave
        globalThis.reportsCleanup = () => {
            startDateInput?.removeEventListener('change', handleDateChange);
            endDateInput?.removeEventListener('change', handleDateChange);
            statusFilterSelect?.removeEventListener('change', handleStatusChange);
            reportsPageInitialized = false; // Reset flag when leaving page
        };
    }
    
    // Reset flag when navigating away from the page
    document.addEventListener('turbo:before-visit', () => {
        if (!event.detail.url.includes('/customers/reports')) {
            reportsPageInitialized = false;
        }
    });

    async function generateReport() {
        try {
            const startDate = document.getElementById('startDate')?.value || '';
            const endDate = document.getElementById('endDate')?.value || '';
            const statusId = document.getElementById('statusFilter')?.value || '';

            const params = new URLSearchParams();
            if (startDate) params.append('start_date', startDate);
            if (endDate) params.append('end_date', endDate);
            if (statusId) params.append('status_id', statusId);

            const url = `/api/customers/reports/generate${params.toString() ? '?' + params.toString() : ''}`;
            console.log('Fetching report from:', url);
            const response = await axios.get(url);
            const data = response.data;

            console.log('Report data received:', data);

            // Update statistics
            updateStatistics(data.statistics);

            // Render table
            renderReportTable(data.customers);

            // Update generated time
            document.getElementById('statGeneratedAt').textContent = new Date(data.generated_at).toLocaleString();
        } catch (error) {
            console.error('Error generating report:', error);
            console.error('Error response:', error.response?.data);
            console.error('Error status:', error.response?.status);
            showErrorNotification('Failed to generate report. Please try again.');
        }
    }

    function updateStatistics(stats) {
        document.getElementById('statTotalCustomers').textContent = stats.total_customers || 0;
        document.getElementById('statActiveCustomers').textContent = stats.active_customers || 0;
        document.getElementById('statInactiveCustomers').textContent = stats.inactive_customers || 0;
        document.getElementById('statCustomersWithRentals').textContent = stats.customers_with_rentals || 0;
        document.getElementById('statTotalRentals').textContent = stats.total_rentals || 0;

        const avgRentals = stats.total_customers > 0 ? (stats.total_rentals / stats.total_customers).toFixed(1) : 0;
        document.getElementById('statAvgRentals').textContent = avgRentals;

        // Calculate total reservations (will be in the customers data)
        document.getElementById('statTotalReservations').textContent = stats.total_reservations || 0;

        // Update charts
        updateCharts(stats);
    }

    function updateCharts(stats) {
        // Determine if dark mode is enabled - check multiple conditions
        const htmlElement = document.documentElement;
        const isDark = htmlElement.classList.contains('dark') || 
                       document.body.classList.contains('dark') ||
                       window.matchMedia('(prefers-color-scheme: dark)').matches;

        console.log('Chart color mode - isDark:', isDark, 'classList:', htmlElement.className);

        // Use pure black for light mode for maximum contrast with white backgrounds
        // Use light gray for dark mode for contrast with dark backgrounds
        const textColor = isDark ? '#e5e7eb' : '#000000';
        const gridColor = isDark ? '#27272a' : '#d1d5db';
        const bgColor = isDark ? '#18181b' : '#ffffff';

        console.log('Chart colors - textColor:', textColor, 'gridColor:', gridColor);

        // Status Distribution Chart
        updateStatusChart(stats, textColor, gridColor);

        // Rentals Chart (will be updated with customer data)
        if (globalThis.currentCustomersData) {
            updateRentalsChart(globalThis.currentCustomersData, textColor, gridColor);
        }

        // Acquisition Chart - only update if not already in progress
        if (!globalThis.acquisitionChartUpdating) {
            updateAcquisitionChart(textColor, gridColor);
        }

        // Comparison Chart
        updateComparisonChart(stats, textColor, gridColor);
    }

    function updateStatusChart(stats, textColor, gridColor) {
        const ctx = document.getElementById('statusChart')?.getContext('2d');
        if (!ctx) return;

        // Destroy existing chart if it exists
        if (globalThis.statusChartInstance) {
            globalThis.statusChartInstance.destroy();
        }

        globalThis.statusChartInstance = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Active', 'Inactive'],
                datasets: [{
                    data: [stats.active_customers, stats.inactive_customers],
                    backgroundColor: [
                        '#10b981', // emerald for active
                        '#ef4444'  // red for inactive
                    ],
                    borderColor: [
                        '#059669',
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
                        callbacks: {
                            label: function(context) {
                                return context.label + ': ' + context.parsed;
                            }
                        }
                    }
                }
            }
        });
    }

    function updateRentalsChart(customers, textColor, gridColor) {
        const ctx = document.getElementById('rentalsChart')?.getContext('2d');
        if (!ctx || !customers || customers.length === 0) return;

        // Get top 8 customers by rentals
        const topCustomers = customers
            .sort((a, b) => b.total_rentals - a.total_rentals)
            .slice(0, 8);

        const names = topCustomers.map(c => c.name.split(' ')[0]); // First name only
        const rentals = topCustomers.map(c => c.total_rentals);

        // Destroy existing chart if it exists
        if (globalThis.rentalsChartInstance) {
            globalThis.rentalsChartInstance.destroy();
        }

        globalThis.rentalsChartInstance = new Chart(ctx, {
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

      function updateAcquisitionChart(textColor, gridColor) {
          const ctx = document.getElementById('acquisitionChart')?.getContext('2d');
          if (!ctx) return;

          // Set flag to prevent concurrent updates
          globalThis.acquisitionChartUpdating = true;

          // Fetch real registration trend data
          axios.get('/api/customers/reports/registration-trend')
              .then(response => {
                  const trendData = response.data;
                  
                  // Destroy existing chart if it exists
                  if (globalThis.acquisitionChartInstance) {
                      globalThis.acquisitionChartInstance.destroy();
                  }

                  globalThis.acquisitionChartInstance = new Chart(ctx, {
                      type: 'line',
                      data: {
                          labels: trendData.months,
                          datasets: [{
                              label: 'New Registrations',
                              data: trendData.data,
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
                  
                  // Clear flag after successful update
                  globalThis.acquisitionChartUpdating = false;
              })
              .catch(error => {
                  console.error('Failed to load registration trend data:', error);
                  // Fallback to sample data if API fails
                  createFallbackAcquisitionChart(ctx, textColor, gridColor);
                  // Clear flag after fallback
                  globalThis.acquisitionChartUpdating = false;
              });
      }

     function createFallbackAcquisitionChart(ctx, textColor, gridColor) {
         // Fallback with sample data if real data fails to load
         const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'];
         const data = [12, 19, 15, 25, 22, 30];

         if (globalThis.acquisitionChartInstance) {
             globalThis.acquisitionChartInstance.destroy();
         }

         globalThis.acquisitionChartInstance = new Chart(ctx, {
             type: 'line',
             data: {
                 labels: months,
                 datasets: [{
                     label: 'New Registrations',
                     data: data,
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

    function updateComparisonChart(stats, textColor, gridColor) {
        const ctx = document.getElementById('comparisonChart')?.getContext('2d');
        if (!ctx) return;

        // Destroy existing chart if it exists
        if (globalThis.comparisonChartInstance) {
            globalThis.comparisonChartInstance.destroy();
        }

        globalThis.comparisonChartInstance = new Chart(ctx, {
            type: 'radar',
            data: {
                labels: [
                    'Total Customers',
                    'Active Customers',
                    'With Rentals',
                    'Total Rentals',
                    'Total Reservations'
                ],
                datasets: [{
                    label: 'Customer Metrics',
                    data: [
                        Math.min(stats.total_customers / 2, 100),
                        Math.min(stats.active_customers, 100),
                        Math.min(stats.customers_with_rentals, 100),
                        Math.min(stats.total_rentals / 5, 100),
                        Math.min(stats.total_reservations / 5, 100)
                    ],
                    borderColor: '#f43f5e', // rose
                    backgroundColor: 'rgba(244, 63, 94, 0.2)',
                    borderWidth: 2,
                    pointBackgroundColor: '#f43f5e',
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 2,
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
                    r: {
                        beginAtZero: true,
                        max: 100,
                        ticks: {
                            color: textColor,
                            stepSize: 20,
                            font: { size: 11, weight: '500' }
                        },
                        grid: {
                            color: gridColor,
                        },
                        pointLabels: {
                            color: textColor,
                            font: { size: 11, weight: '500' }
                        }
                    }
                }
            }
        });
    }

    function renderReportTable(customers) {
        const tbody = document.getElementById('reportTableBody');
        if (!tbody) return;

        // Store customers data globally for chart use
        globalThis.currentCustomersData = customers;

        if (!customers || customers.length === 0) {
            tbody.innerHTML = `
                <tr class="border-b border-neutral-200 dark:border-neutral-900/60">
                    <td colspan="9" class="py-8 text-center text-neutral-500 dark:text-neutral-400">
                        No customers found for the selected filters
                    </td>
                </tr>
            `;
            return;
        }

        tbody.innerHTML = customers.map(customer => {
            const lastRentalDate = customer.last_rental_date 
                ? new Date(customer.last_rental_date).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' })
                : 'Never';

            const regDate = new Date(customer.registration_date).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });

            const statusColor = customer.status === 'active'
                ? 'bg-emerald-500/15 text-emerald-600 border-emerald-500/40 dark:text-emerald-300'
                : 'bg-red-500/15 text-red-600 border-red-500/40 dark:text-red-300';

            const statusBgColor = customer.status === 'active' ? 'bg-emerald-500' : 'bg-red-500';

            return `
                <tr class="border-b border-neutral-200 hover:bg-neutral-100 dark:border-neutral-900/60 dark:hover:bg-white/5 transition-colors">
                    <td class="py-3.5 pr-4 pl-4 text-neutral-500 font-geist-mono">#${String(customer.customer_id).padStart(3, '0')}</td>
                    <td class="py-3.5 pr-4 text-neutral-900 dark:text-neutral-100">${customer.name}</td>
                    <td class="py-3.5 pr-4 text-neutral-600 dark:text-neutral-300 text-[11px]">${customer.email}</td>
                    <td class="py-3.5 pr-4 text-neutral-600 dark:text-neutral-300 font-geist-mono text-[11px]">${customer.contact_number}</td>
                    <td class="py-3.5 pr-4">
                        <span class="inline-flex items-center rounded-full ${statusColor} px-2 py-1 text-[10px] font-medium border">
                            <span class="mr-1 h-1.5 w-1.5 rounded-full ${statusBgColor}"></span>
                            ${customer.status}
                        </span>
                    </td>
                    <td class="py-3.5 pr-4 text-center font-geist-mono">${customer.total_rentals}</td>
                    <td class="py-3.5 pr-4 text-center font-geist-mono">${customer.total_reservations}</td>
                    <td class="py-3.5 pr-4 font-geist-mono text-[11px]">${regDate}</td>
                    <td class="py-3.5 pr-4 font-geist-mono text-[11px]">${lastRentalDate}</td>
                </tr>
            `;
        }).join('');
    }

    function resetFilters() {
        document.getElementById('startDate').value = '';
        document.getElementById('endDate').value = '';
        document.getElementById('statusFilter').value = '';
        generateReport();
    }

    async function generatePDF() {
        try {
            const startDate = document.getElementById('startDate')?.value || '';
            const endDate = document.getElementById('endDate')?.value || '';
            const statusId = document.getElementById('statusFilter')?.value || '';

            const params = new URLSearchParams();
            if (startDate) params.append('start_date', startDate);
            if (endDate) params.append('end_date', endDate);
            if (statusId) params.append('status_id', statusId);

            const url = `/api/customers/reports/pdf${params.toString() ? '?' + params.toString() : ''}`;
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
</script>

</body>
</html>
