<!doctype html>
<html lang="en">
<head>
    {{-- Prevent flash of wrong theme --}}
    @include('components.theme-init')
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Reservation Reports · Love &amp; Styles</title>

    {{-- Favicon --}}
    <link rel="icon" href="{{ asset('favicon.ico') }}" sizes="any">
    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}">
    <link rel="manifest" href="{{ asset('site.webmanifest') }}">

    {{-- Fonts: Geist & Geist Mono --}}
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Geist:wght@400;500;600;700&family=Geist+Mono:wght@400;500&display=swap">

    {{-- App styles --}}
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.js"></script>
</head>
<body class="min-h-screen flex font-geist bg-neutral-100 text-neutral-900 dark:bg-black dark:text-neutral-50 transition-colors duration-300 ease-in-out">
<x-sidebar />

<main class="flex-1 ml-64 flex flex-col px-10 py-8 overflow-x-hidden overflow-y-auto bg-gradient-to-b from-neutral-100 via-neutral-100 to-neutral-200 dark:from-black dark:via-black dark:to-neutral-950 transition-colors duration-300 ease-in-out">
    <header class="mb-8 transition-colors duration-300 ease-in-out">
        <div class="flex items-center justify-between gap-4">
            <div>
                <h1 class="text-3xl font-semibold tracking-tight text-neutral-900 dark:text-white transition-colors duration-300 ease-in-out">Reservation Reports</h1>
                <p class="mt-1 text-sm font-geist-mono text-neutral-500 dark:text-neutral-400 transition-colors duration-300 ease-in-out">Analyze reservation performance and export report summaries</p>
            </div>

            <div class="flex items-center gap-3 text-xs">
                <a href="/reservations" class="inline-flex items-center gap-2 rounded-xl px-3.5 py-2 text-[14px] font-medium border border-neutral-300 bg-white text-neutral-700 hover:bg-violet-600 hover:text-white dark:border-neutral-800 dark:bg-neutral-950/80 dark:text-neutral-200 dark:hover:bg-neutral-900 dark:hover:text-black transition-colors duration-300 ease-in-out">
                    <x-icon name="arrow-left" class="h-4 w-4" />
                    <span>Back to Reservations</span>
                </a>

                <button onclick="generatePDF()" class="inline-flex items-center gap-2 rounded-xl px-4 py-2 text-[14px] font-medium bg-violet-600 text-white hover:text-black hover:bg-violet-500 dark:text-black dark:hover:text-white transition-colors duration-300 ease-in-out">
                    <x-icon name="download" class="h-4 w-4" />
                    <span>Download PDF</span>
                </button>
            </div>
        </div>
    </header>

    <section class="mb-8 rounded-2xl border border-neutral-200 bg-white p-6 dark:border-neutral-900 dark:bg-neutral-950/60 transition-colors duration-300 ease-in-out">
        <h2 class="mb-4 text-lg font-semibold text-neutral-900 dark:text-white">Report Filters</h2>
        <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
            <div>
                <label class="block text-xs font-medium text-neutral-700 dark:text-neutral-300 mb-2">Start Date</label>
                <input id="startDate" type="date" class="w-full rounded-lg border border-neutral-300 bg-white px-3 py-2 text-xs text-neutral-900 dark:border-neutral-700 dark:bg-neutral-900 dark:text-white focus:border-violet-500 focus:outline-none">
            </div>
            <div>
                <label class="block text-xs font-medium text-neutral-700 dark:text-neutral-300 mb-2">End Date</label>
                <input id="endDate" type="date" class="w-full rounded-lg border border-neutral-300 bg-white px-3 py-2 text-xs text-neutral-900 dark:border-neutral-700 dark:bg-neutral-900 dark:text-white focus:border-violet-500 focus:outline-none">
            </div>
            <div>
                <label class="block text-xs font-medium text-neutral-700 dark:text-neutral-300 mb-2">Status</label>
                <select id="statusFilter" class="w-full rounded-lg border border-neutral-300 bg-white px-3 py-2 text-xs text-neutral-900 dark:border-neutral-700 dark:bg-neutral-900 dark:text-white focus:border-violet-500 focus:outline-none">
                    <option value="">All Statuses</option>
                    <option value="confirmed">Confirmed</option>
                    <option value="pending">Pending</option>
                    <option value="cancelled">Cancelled</option>
                    <option value="completed">Completed</option>
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

    <section class="grid grid-cols-1 gap-6 mb-8 md:grid-cols-2 lg:grid-cols-4">
        <div class="rounded-2xl p-6 border border-neutral-200 bg-white dark:border-neutral-900 dark:bg-neutral-950/60 shadow-sm dark:shadow-[0_18px_60px_rgba(0,0,0,0.65)] transition-colors duration-300 ease-in-out">
            <div class="text-sm text-neutral-500 dark:text-neutral-400 mb-2">Total Reservations</div>
            <div id="statTotalReservations" class="text-3xl font-semibold text-neutral-900 dark:text-white">0</div>
        </div>
        <div class="rounded-2xl p-6 border border-neutral-200 bg-white dark:border-neutral-900 dark:bg-neutral-950/60 shadow-sm dark:shadow-[0_18px_60px_rgba(0,0,0,0.65)] transition-colors duration-300 ease-in-out">
            <div class="text-sm text-neutral-500 dark:text-neutral-400 mb-2">Total Items Reserved</div>
            <div id="statTotalItems" class="text-3xl font-semibold text-emerald-600 dark:text-emerald-400">0</div>
        </div>
        <div class="rounded-2xl p-6 border border-neutral-200 bg-white dark:border-neutral-900 dark:bg-neutral-950/60 shadow-sm dark:shadow-[0_18px_60px_rgba(0,0,0,0.65)] transition-colors duration-300 ease-in-out">
            <div class="text-sm text-neutral-500 dark:text-neutral-400 mb-2">Total Revenue</div>
            <div id="statRevenue" class="text-3xl font-semibold text-violet-600 dark:text-violet-400 font-geist-mono">₱0</div>
        </div>
        <div class="rounded-2xl p-6 border border-neutral-200 bg-white dark:border-neutral-900 dark:bg-neutral-950/60 shadow-sm dark:shadow-[0_18px_60px_rgba(0,0,0,0.65)] transition-colors duration-300 ease-in-out">
            <div class="text-sm text-neutral-500 dark:text-neutral-400 mb-2">Avg Items / Reservation</div>
            <div id="statAvgItems" class="text-3xl font-semibold text-amber-600 dark:text-amber-400">0</div>
        </div>
    </section>

    <section class="grid grid-cols-1 gap-6 mb-8 lg:grid-cols-2">
        <div class="rounded-2xl border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-900 dark:bg-neutral-950/60 dark:shadow-[0_18px_60px_rgba(0,0,0,0.65)] transition-colors duration-300 ease-in-out">
            <h3 class="text-lg font-semibold text-neutral-900 dark:text-white mb-6">Reservation Status Distribution</h3>
            <div style="height: 300px; position: relative;">
                <canvas id="statusChart"></canvas>
            </div>
        </div>
        <div class="rounded-2xl border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-900 dark:bg-neutral-950/60 dark:shadow-[0_18px_60px_rgba(0,0,0,0.65)] transition-colors duration-300 ease-in-out">
            <h3 class="text-lg font-semibold text-neutral-900 dark:text-white mb-6">Reservations by Month</h3>
            <div style="height: 300px; position: relative;">
                <canvas id="monthlyChart"></canvas>
            </div>
        </div>
    </section>

    <section class="rounded-2xl border border-neutral-200 bg-white shadow-sm dark:border-neutral-900 dark:bg-neutral-950/60 dark:shadow-[0_18px_60px_rgba(0,0,0,0.65)] transition-colors duration-300 ease-in-out">
        <div class="px-6 py-4 border-b border-neutral-200 dark:border-neutral-900 flex items-center justify-between">
            <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">Reservation Details</h2>
            <span id="generatedAt" class="text-xs text-neutral-500 dark:text-neutral-400">Not yet generated</span>
        </div>

        <div class="px-6 py-4">
            <div class="overflow-x-auto">
                <table class="min-w-full text-left text-xs text-neutral-600 dark:text-neutral-300 transition-colors duration-300 ease-in-out">
                    <thead class="text-[11px] uppercase tracking-[0.18em] text-neutral-500">
                    <tr class="border-b border-neutral-200 dark:border-neutral-900/80">
                        <th class="py-2.5 pr-4 pl-4 font-medium">ID</th>
                        <th class="py-2.5 pr-4 font-medium">Customer</th>
                        <th class="py-2.5 pr-4 font-medium">Date</th>
                        <th class="py-2.5 pr-4 font-medium">Pickup</th>
                        <th class="py-2.5 pr-4 font-medium">Return</th>
                        <th class="py-2.5 pr-4 font-medium">Items</th>
                        <th class="py-2.5 pr-4 font-medium">Status</th>
                        <th class="py-2.5 pr-4 font-medium text-right">Revenue</th>
                    </tr>
                    </thead>
                    <tbody id="reportTableBody" class="text-[13px]">
                    <tr class="border-b border-neutral-200 dark:border-neutral-900/60">
                        <td colspan="8" class="py-8 text-center text-neutral-500 dark:text-neutral-400">Loading reservation report...</td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </section>
</main>

<script>
    var reservationReportsState = {
        summary: null,
        reservations: [],
        statusChart: null,
        monthlyChart: null
    };

    document.addEventListener('DOMContentLoaded', function() {
        generateReport();

        var startDateInput = document.getElementById('startDate');
        var endDateInput = document.getElementById('endDate');
        var statusFilterSelect = document.getElementById('statusFilter');

        if (startDateInput) startDateInput.addEventListener('change', generateReport);
        if (endDateInput) endDateInput.addEventListener('change', generateReport);
        if (statusFilterSelect) statusFilterSelect.addEventListener('change', generateReport);

        var observer = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                if (mutation.type === 'attributes' && mutation.attributeName === 'class') {
                    updateCharts();
                }
            });
        });

        observer.observe(document.documentElement, {
            attributes: true,
            attributeFilter: ['class']
        });
    });

    async function generateReport() {
        try {
            var params = buildReportParams();
            var url = '/api/reservations/reports/generate' + (params.toString() ? ('?' + params.toString()) : '');
            var response = await axios.get(url);
            var data = response.data || {};

            reservationReportsState.summary = data.summary || {};
            reservationReportsState.reservations = Array.isArray(data.reservations) ? data.reservations : [];

            updateStatistics(reservationReportsState.summary);
            renderReportTable(reservationReportsState.reservations);
            updateCharts();

            var generatedAtEl = document.getElementById('generatedAt');
            if (generatedAtEl) {
                generatedAtEl.textContent = 'Generated: ' + new Date().toLocaleString();
            }
        } catch (error) {
            console.error('Error generating reservation report:', error);
            showErrorNotification('Failed to generate report. Please try again.');
        }
    }

    function buildReportParams() {
        var params = new URLSearchParams();
        var startDate = document.getElementById('startDate');
        var endDate = document.getElementById('endDate');
        var status = document.getElementById('statusFilter');

        if (startDate && startDate.value) params.append('start_date', startDate.value);
        if (endDate && endDate.value) params.append('end_date', endDate.value);
        if (status && status.value) params.append('status', status.value);

        return params;
    }

    function updateStatistics(summary) {
        var totalReservations = summary.total_reservations || 0;
        var totalItems = summary.total_items_reserved || 0;
        var revenue = summary.total_revenue || 0;
        var avgItems = summary.average_items_per_reservation || 0;

        document.getElementById('statTotalReservations').textContent = Number(totalReservations).toLocaleString();
        document.getElementById('statTotalItems').textContent = Number(totalItems).toLocaleString();
        document.getElementById('statRevenue').textContent = '₱' + Number(revenue).toLocaleString();
        document.getElementById('statAvgItems').textContent = Number(avgItems).toLocaleString();
    }

    function updateCharts() {
        if (!reservationReportsState.summary) {
            return;
        }

        var isDark = document.documentElement.classList.contains('dark');
        var textColor = isDark ? '#e5e7eb' : '#111827';
        var gridColor = isDark ? '#27272a' : '#e5e7eb';

        updateStatusChart(reservationReportsState.summary.by_status || {}, textColor);
        updateMonthlyChart(reservationReportsState.summary.by_month || {}, textColor, gridColor);
    }

    function updateStatusChart(byStatus, textColor) {
        var canvas = document.getElementById('statusChart');
        if (!canvas) return;

        var labels = Object.keys(byStatus || {}).map(function(label) {
            var normalized = String(label || 'unknown');
            return normalized.charAt(0).toUpperCase() + normalized.slice(1);
        });
        var values = Object.values(byStatus || {}).map(function(value) {
            return Number(value || 0);
        });

        if (!labels.length) {
            labels = ['No Data'];
            values = [1];
        }

        if (reservationReportsState.statusChart) {
            reservationReportsState.statusChart.destroy();
        }

        reservationReportsState.statusChart = new Chart(canvas.getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    data: values,
                    backgroundColor: ['#10b981', '#f59e0b', '#ef4444', '#3b82f6', '#8b5cf6', '#6b7280'],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        labels: {
                            color: textColor,
                            font: { size: 12, weight: '600' }
                        }
                    }
                }
            }
        });
    }

    function updateMonthlyChart(byMonth, textColor, gridColor) {
        var canvas = document.getElementById('monthlyChart');
        if (!canvas) return;

        var monthKeys = Object.keys(byMonth || {}).sort();
        var labels = monthKeys.map(function(key) {
            var parts = String(key).split('-');
            if (parts.length !== 2) return key;
            var date = new Date(Number(parts[0]), Number(parts[1]) - 1, 1);
            return isNaN(date.getTime()) ? key : date.toLocaleDateString('en-US', { month: 'short', year: 'numeric' });
        });
        var values = monthKeys.map(function(key) {
            return Number(byMonth[key] || 0);
        });

        if (!labels.length) {
            labels = ['No Data'];
            values = [0];
        }

        if (reservationReportsState.monthlyChart) {
            reservationReportsState.monthlyChart.destroy();
        }

        reservationReportsState.monthlyChart = new Chart(canvas.getContext('2d'), {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Reservations',
                    data: values,
                    backgroundColor: '#8b5cf6',
                    borderRadius: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        labels: {
                            color: textColor,
                            font: { size: 12, weight: '600' }
                        }
                    }
                },
                scales: {
                    x: {
                        ticks: { color: textColor },
                        grid: { color: gridColor }
                    },
                    y: {
                        ticks: { color: textColor },
                        grid: { color: gridColor },
                        beginAtZero: true
                    }
                }
            }
        });
    }

    function renderReportTable(reservations) {
        var tbody = document.getElementById('reportTableBody');
        if (!tbody) {
            return;
        }

        if (!reservations.length) {
            tbody.innerHTML = '<tr class="border-b border-neutral-200 dark:border-neutral-900/60"><td colspan="8" class="py-8 text-center text-neutral-500 dark:text-neutral-400">No reservations found for selected filters</td></tr>';
            return;
        }

        tbody.innerHTML = reservations.map(function(reservation) {
            var customerName = [reservation.customer && reservation.customer.first_name, reservation.customer && reservation.customer.last_name]
                .filter(Boolean)
                .join(' ') || ((reservation.customer && reservation.customer.email) || 'N/A');

            var statusName = ((reservation.status && reservation.status.status_name) || 'unknown').toLowerCase();
            var statusLabel = statusName.charAt(0).toUpperCase() + statusName.slice(1);

            var statusClass = statusName === 'confirmed'
                ? 'bg-emerald-500/15 text-emerald-600 border-emerald-500/40 dark:text-emerald-300'
                : statusName === 'pending'
                ? 'bg-amber-500/15 text-amber-600 border-amber-500/40 dark:text-amber-300'
                : statusName === 'cancelled'
                ? 'bg-red-500/15 text-red-600 border-red-500/40 dark:text-red-300'
                : statusName === 'completed'
                ? 'bg-blue-500/15 text-blue-600 border-blue-500/40 dark:text-blue-300'
                : 'bg-gray-500/15 text-gray-600 border-gray-500/40 dark:text-gray-300';

            var reservationItems = Array.isArray(reservation.items) ? reservation.items : [];
            var totalAmount = reservationItems.reduce(function(total, item) {
                return total + ((Number(item.rental_price) || 0) * (Number(item.quantity) || 1));
            }, 0);

            return '' +
                '<tr class="border-b border-neutral-200 hover:bg-neutral-100 dark:border-neutral-900/60 dark:hover:bg-white/5 transition-colors">' +
                    '<td class="py-3.5 pr-4 pl-4 text-neutral-500 font-geist-mono">#' + (reservation.reservation_id || 'N/A') + '</td>' +
                    '<td class="py-3.5 pr-4 text-neutral-900 dark:text-neutral-100">' + customerName + '</td>' +
                    '<td class="py-3.5 pr-4 text-neutral-600 dark:text-neutral-300 font-geist-mono">' + formatDateSafe(reservation.reservation_date || reservation.created_at) + '</td>' +
                    '<td class="py-3.5 pr-4 text-neutral-600 dark:text-neutral-300 font-geist-mono">' + formatDateSafe(reservation.start_date) + '</td>' +
                    '<td class="py-3.5 pr-4 text-neutral-600 dark:text-neutral-300 font-geist-mono">' + formatDateSafe(reservation.end_date) + '</td>' +
                    '<td class="py-3.5 pr-4 text-neutral-600 dark:text-neutral-300">' + reservationItems.length + '</td>' +
                    '<td class="py-3.5 pr-4"><span class="inline-flex items-center rounded-full ' + statusClass + ' px-2 py-1 text-[11px] font-medium border">' + statusLabel + '</span></td>' +
                    '<td class="py-3.5 pr-4 text-right text-neutral-900 dark:text-neutral-100 font-geist-mono">₱' + totalAmount.toLocaleString() + '</td>' +
                '</tr>';
        }).join('');
    }

    function formatDateSafe(value) {
        if (!value) return 'N/A';
        var date = new Date(value);
        if (isNaN(date.getTime())) return 'N/A';
        return date.toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });
    }

    function resetFilters() {
        document.getElementById('startDate').value = '';
        document.getElementById('endDate').value = '';
        document.getElementById('statusFilter').value = '';
        generateReport();
    }

    function generatePDF() {
        var params = buildReportParams();
        var url = '/api/reservations/reports/pdf' + (params.toString() ? ('?' + params.toString()) : '');
        window.open(url, '_blank');
    }

    function showErrorNotification(message) {
        var notification = document.createElement('div');
        notification.className = 'fixed top-4 right-4 max-w-sm bg-red-100 border border-red-300 dark:bg-red-900 dark:border-red-700 rounded-lg px-5 py-4 shadow-xl z-[999] flex items-start gap-3';
        notification.innerHTML = '<svg class="h-5 w-5 text-red-700 dark:text-red-200 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" /></svg><div class="flex-1"><p class="text-sm font-semibold text-red-900 dark:text-red-50">' + message + '</p></div><button onclick="this.parentElement.remove()" class="text-red-700 dark:text-red-200 hover:text-red-900 dark:hover:text-red-50 flex-shrink-0 transition-colors"><svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" /></svg></button>';
        document.body.appendChild(notification);

        setTimeout(function() {
            notification.style.opacity = '0';
            notification.style.transition = 'opacity 0.3s ease-out';
            setTimeout(function() { notification.remove(); }, 300);
        }, 5000);
    }
</script>
</body>
</html>
