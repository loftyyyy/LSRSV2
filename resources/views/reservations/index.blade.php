<!DOCTYPE html>
<html lang="en">
<head>
    {{-- Prevent flash of wrong theme --}}
    @include('components.theme-init')
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
<title>Reservations · Love &amp; Styles</title>

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
</head>

<body class="min-h-screen flex font-geist bg-neutral-100 text-neutral-900 dark:bg-black dark:text-neutral-50">
<x-sidebar />

<main class="flex-1 ml-64 flex flex-col px-10 py-8 overflow-x-hidden overflow-y-auto bg-gradient-to-b from-neutral-100 via-neutral-100 to-neutral-200 dark:from-black dark:via-black dark:to-neutral-950">
    {{-- Page header --}}
    <header class="mb-8 transition-colors duration-300 ease-in-out">
        <div class="flex items-center justify-between gap-4">
            <div>
                <h1 class="text-3xl font-semibold tracking-tight text-neutral-900 dark:text-white transition-colors duration-300 ease-in-out">
                    Reservations
                </h1>
                <p class="mt-1 text-sm font-geist-mono text-neutral-500 dark:text-neutral-400 transition-colors duration-300 ease-in-out">
                    Manage customer reservations and bookings
                </p>
            </div>

            <div class="flex items-center gap-3 text-xs">
                <button type="button" class="inline-flex items-center gap-2 rounded-xl border border-neutral-300 bg-white px-3.5 py-2 text-neutral-700 hover:bg-violet-600 dark:hover:text-black hover:text-white dark:border-neutral-800 dark:bg-neutral-950/80 dark:text-neutral-200 transition-colors duration-300 ease-in-out">
                    <span class="inline-flex h-5 w-5 items-center justify-center rounded-md">
                        <x-icon name="chart-column" class="h-4 w-4" />
                    </span>
                    <span class="text-[14px] font-medium tracking-wide">Reports</span>
                </button>

                <button type="button" class="inline-flex items-center gap-2 rounded-xl border border-neutral-300 bg-white px-3.5 py-2 text-neutral-700 hover:bg-violet-600 dark:hover:text-black hover:text-white dark:border-neutral-800 dark:bg-neutral-950/80 dark:text-neutral-200 transition-colors duration-300 ease-in-out">
                    <span class="inline-flex h-5 w-5 items-center justify-center rounded-md">
                        <x-icon name="eye" class="h-4 w-4" />
                    </span>
                    <span class="text-[14px] font-medium tracking-wide">Browse Items</span>
                </button>

                <button type="button" onclick="openNewReservationModal()" class="inline-flex items-center gap-2 rounded-xl bg-violet-600 px-4 py-2 text-[14px] font-medium tracking-wide text-white dark:text-black shadow-lg  hover:text-black dark:hover:text-white hover:bg-violet-500 transition-colors duration-300 ease-in-out">
                    <span class="inline-flex h-5 w-5 items-center justify-center rounded-md">
                        <x-icon name="plus" class="h-4 w-4" />
                    </span>
                    <span>New Reservation</span>
                </button>
            </div>
        </div>
    </header>

    {{-- Stats --}}
    <section class="grid grid-cols-4 gap-6 mb-8">
        <div class="rounded-2xl p-6 border border-neutral-200 bg-white dark:border-neutral-900 dark:bg-neutral-950/60 shadow-sm dark:shadow-[0_18px_60px_rgba(0,0,0,0.65)] transition-colors duration-300 ease-in-out">
            <div class="text-sm text-neutral-500 dark:text-neutral-400 mb-2 transition-colors duration-300 ease-in-out">
                Total Reservations
            </div>
            <div id="totalReservationsCount" class="text-3xl font-semibold text-neutral-900 dark:text-white transition-colors duration-300 ease-in-out">
                0
            </div>
        </div>
        <div class="rounded-2xl p-6 border border-neutral-200 bg-white dark:border-neutral-900 dark:bg-neutral-950/60 shadow-sm dark:shadow-[0_18px_60px_rgba(0,0,0,0.65)] transition-colors duration-300 ease-in-out">
            <div class="text-sm text-neutral-500 dark:text-neutral-400 mb-2 transition-colors duration-300 ease-in-out">
                Active Reservations
            </div>
            <div id="activeReservationsCount" class="text-3xl font-semibold text-amber-500 transition-colors duration-300 ease-in-out">
                0
            </div>
        </div>
        <div class="rounded-2xl p-6 border border-neutral-200 bg-white dark:border-neutral-900 dark:bg-neutral-950/60 shadow-sm dark:shadow-[0_18px_60px_rgba(0,0,0,0.65)] transition-colors duration-300 ease-in-out">
            <div class="text-sm text-neutral-500 dark:text-neutral-400 mb-2 transition-colors duration-300 ease-in-out">
                Total Items Reserved
            </div>
            <div id="totalItemsReservedCount" class="text-3xl font-semibold text-green-500 transition-colors duration-300 ease-in-out">
                0
            </div>
        </div>
        <div class="rounded-2xl p-6 border border-neutral-200 bg-white dark:border-neutral-900 dark:bg-neutral-950/60 shadow-sm dark:shadow-[0_18px_60px_rgba(0,0,0,0.65)] transition-colors duration-300 ease-in-out">
            <div class="text-sm text-neutral-500 dark:text-neutral-400 mb-2 transition-colors duration-300 ease-in-out">
                Estimated Revenue
            </div>
            <div id="estimatedRevenueCount" class="text-3xl font-semibold text-violet-600 transition-colors duration-300 ease-in-out">
                ₱0
            </div>
        </div>
    </section>

    {{-- Filters + table --}}
    <section class="flex-1">
        <div class="rounded-2xl border border-neutral-200 bg-white shadow-sm dark:border-neutral-900 dark:bg-neutral-950/60 dark:shadow-[0_18px_60px_rgba(0,0,0,0.65)] transition-colors duration-300 ease-in-out">
            {{-- Search & filters --}}
            <div class="border-b border-neutral-200 px-6 py-4 dark:border-neutral-900 transition-colors duration-300 ease-in-out">
                <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                    <div class="flex-1">
                        <div>
                            <div class="flex items-center gap-3 rounded-2xl bg-white px-4 py-2.5 border border-neutral-300 focus-within:border-neutral-500 dark:border-neutral-800 dark:bg-black/60 transition-colors duration-300 ease-in-out">
                                <x-icon name="search" class="h-4 w-4 text-neutral-500 transition-colors duration-300 ease-in-out" />
                                <input id="searchInput" type="text" placeholder="Search by customer, item, or ID..." class="w-full bg-transparent text-xs text-neutral-700 placeholder:text-neutral-400 dark:text-neutral-100 dark:placeholder:text-neutral-500 focus:outline-none transition-colors duration-300 ease-in-out">
                            </div>
                            <div id="searchIndicators" class="mt-2 flex flex-wrap gap-1.5 px-0"></div>
                        </div>
                    </div>

                    <div class="flex items-center gap-3">
                        <!-- Custom Filter Dropdown -->
                        <div class="relative" id="filter-dropdown">
                            <button id="filter-button" class="flex items-center gap-2 rounded-2xl px-3 py-2 text-xs border border-neutral-300 bg-white text-neutral-700 dark:border-neutral-800 dark:bg-black/60 dark:text-neutral-100 focus:outline-none transition-colors duration-300 ease-in-out">
                                <span id="filter-button-text">Filter Status</span>
                                <span id="icon-down" class="h-3 w-3 transition-transform duration-300 ease-in-out">
                                    <x-icon name="arrow-down" class="h-3 w-3" />
                                </span>
                                <span id="icon-up" class="hidden h-3 w-3 transition-transform duration-300 ease-in-out">
                                    <x-icon name="arrow-up" class="h-3 w-3" />
                                </span>
                            </button>

                            <div id="filter-menu" class="absolute right-0 mt-2 w-48 rounded-xl border border-neutral-300 bg-white dark:border-neutral-800 dark:bg-black/60 shadow-lg z-50 overflow-hidden opacity-0 scale-95 pointer-events-none transition-all duration-200 ease-in-out">
                                <ul class="flex flex-col text-xs">
                                    <li class="px-4 py-2 hover:bg-neutral-100 dark:hover:bg-neutral-900 cursor-pointer transition-colors duration-200">All Status</li>
                                    <li class="px-4 py-2 hover:bg-neutral-100 dark:hover:bg-neutral-900 cursor-pointer transition-colors duration-200">Confirmed</li>
                                    <li class="px-4 py-2 hover:bg-neutral-100 dark:hover:bg-neutral-900 cursor-pointer transition-colors duration-200">Pending</li>
                                    <li class="px-4 py-2 hover:bg-neutral-100 dark:hover:bg-neutral-900 cursor-pointer transition-colors duration-200">Cancelled</li>
                                    <li class="px-4 py-2 hover:bg-neutral-100 dark:hover:bg-neutral-900 cursor-pointer transition-colors duration-200">Completed</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Table --}}
            <div class="px-6 py-4">
                <div class="overflow-x-auto">
                    <table class="min-w-full text-left text-xs text-neutral-600 dark:text-neutral-300 transition-colors duration-300 ease-in-out">
                        <thead class="text-[11px] uppercase tracking-[0.18em] text-neutral-500">
                        <tr class="border-b border-neutral-200 dark:border-neutral-900/80">
                            <th class="py-2.5 pr-4 pl-4 font-medium">ID</th>
                            <th class="py-2.5 pr-4 font-medium">Customer</th>
                            <th class="py-2.5 pr-4 font-medium">Item</th>
                            <th class="py-2.5 pr-4 font-medium">Pickup</th>
                            <th class="py-2.5 pr-4 font-medium">Return</th>
                            <th class="py-2.5 pr-4 font-medium">Status</th>
                            <th class="py-2.5 pr-4 font-medium text-left">Amount</th>
                            <th class="py-2.5 pl-2 font-medium text-left">Actions</th>
                        </tr>
                        </thead>

                        <tbody id="reservationTableBody" class="text-[13px]">
                        <!-- Rows will be dynamically inserted here -->
                        </tbody>
                    </table>
                </div>

                <div id="emptyState" class="text-center py-12">
                    <p class="text-neutral-500 dark:text-neutral-400">No reservations found</p>
                </div>
            </div>

            <div id="paginationControls" class="px-6 py-4 border-t border-neutral-200 dark:border-neutral-900 flex items-center justify-between">
                <div class="text-xs text-neutral-500 dark:text-neutral-400">
                    Showing <span id="pageStart">0</span> to <span id="pageEnd">0</span> of <span id="pageTotal">0</span> results
                </div>
                <div class="flex items-center gap-2">
                    <button id="prevBtn" onclick="previousPage()" class="text-xs rounded-lg px-3 py-1.5 border border-neutral-300 bg-white text-neutral-700 dark:border-neutral-800 dark:bg-black/60 dark:text-neutral-100 hover:bg-neutral-100 dark:hover:bg-neutral-900 transition-colors duration-200 disabled:opacity-50 disabled:cursor-not-allowed font-geist" disabled>
                        Previous
                    </button>
                    <div id="pageInfo" class="text-xs text-neutral-600 dark:text-neutral-300 min-w-[100px] text-center">
                        Page 1
                    </div>
                    <button id="nextBtn" onclick="nextPage()" class="text-xs rounded-lg px-3 py-1.5 border border-neutral-300 bg-white text-neutral-700 dark:border-neutral-800 dark:bg-black/60 dark:text-neutral-100 hover:bg-neutral-100 dark:hover:bg-neutral-900 transition-colors duration-200 disabled:opacity-50 disabled:cursor-not-allowed font-geist" disabled>
                        Next
                    </button>
                </div>
            </div>
        </div>
    </section>
</main>

{{-- Include New Reservation Modal --}}
@include('reservations.partials.new-reservation-modal')

{{-- Axios for API calls --}}
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

<script>
    var reservationState = {
        currentPage: 1,
        perPage: 15,
        searchQuery: '',
        statusFilter: '',
        sortBy: 'created_at',
        sortOrder: 'desc',
        totalPages: 1,
        totalCount: 0,
        isLoading: false,
        abortController: null
    };

    var searchDebounceTimer;

    function initializeReservationPage() {
        var searchInput = document.getElementById('searchInput');
        var filterMenu = document.getElementById('filter-menu');
        var filterButtonText = document.getElementById('filter-button-text');

        reservationState.abortController = new AbortController();

        fetchReservationStats();
        fetchReservations();

        if (searchInput) {
            searchInput.addEventListener('input', function(e) {
                clearTimeout(searchDebounceTimer);
                reservationState.searchQuery = e.target.value;
                reservationState.currentPage = 1;

                updateSearchIndicators();

                searchDebounceTimer = setTimeout(function() {
                    fetchReservations();
                }, 300);
            });
        }

        if (filterMenu) {
            filterMenu.querySelectorAll('li').forEach(function(item) {
                item.addEventListener('click', function() {
                    var statusText = item.textContent.trim();
                    filterButtonText.textContent = statusText;

                    reservationState.statusFilter = statusText === 'All Status'
                        ? ''
                        : statusText.toLowerCase();

                    reservationState.currentPage = 1;
                    fetchReservations();
                });
            });
        }

        initializeFilterDropdown();
    }

    function initializeFilterDropdown() {
        var filterButton = document.getElementById('filter-button');
        var filterMenu = document.getElementById('filter-menu');
        var iconDown = document.getElementById('icon-down');
        var iconUp = document.getElementById('icon-up');

        if (!filterButton || !filterMenu) {
            return;
        }

        var isOpen = false;

        filterButton.addEventListener('click', function(e) {
            e.stopPropagation();
            isOpen = !isOpen;

            filterMenu.classList.toggle('opacity-0', !isOpen);
            filterMenu.classList.toggle('scale-95', !isOpen);
            filterMenu.classList.toggle('pointer-events-none', !isOpen);
            filterMenu.classList.toggle('opacity-100', isOpen);
            filterMenu.classList.toggle('scale-100', isOpen);
            filterMenu.classList.toggle('pointer-events-auto', isOpen);

            iconDown.classList.toggle('hidden', isOpen);
            iconUp.classList.toggle('hidden', !isOpen);
        });

        document.addEventListener('click', function() {
            if (isOpen) {
                isOpen = false;
                filterMenu.classList.add('opacity-0', 'scale-95', 'pointer-events-none');
                filterMenu.classList.remove('opacity-100', 'scale-100', 'pointer-events-auto');
                iconDown.classList.remove('hidden');
                iconUp.classList.add('hidden');
            }
        });
    }

    async function fetchReservationStats() {
        try {
            if (!reservationState.abortController) {
                reservationState.abortController = new AbortController();
            }

            var response = await axios.get('/api/reservations/reports/generate', {
                signal: reservationState.abortController.signal
            });

            var summary = response.data && response.data.summary ? response.data.summary : {};
            var totalReservations = summary.total_reservations || 0;
            var totalItemsReserved = summary.total_items_reserved || 0;
            var estimatedRevenue = summary.total_revenue || 0;
            var byStatus = summary.by_status || {};
            var cancelledCount = 0;

            Object.keys(byStatus).forEach(function(key) {
                if (String(key).toLowerCase() === 'cancelled') {
                    cancelledCount = byStatus[key] || 0;
                }
            });

            var activeReservations = Math.max(totalReservations - cancelledCount, 0);

            var totalReservationsEl = document.getElementById('totalReservationsCount');
            var activeReservationsEl = document.getElementById('activeReservationsCount');
            var totalItemsReservedEl = document.getElementById('totalItemsReservedCount');
            var estimatedRevenueEl = document.getElementById('estimatedRevenueCount');

            if (totalReservationsEl) totalReservationsEl.textContent = totalReservations;
            if (activeReservationsEl) activeReservationsEl.textContent = activeReservations;
            if (totalItemsReservedEl) totalItemsReservedEl.textContent = totalItemsReserved;
            if (estimatedRevenueEl) estimatedRevenueEl.textContent = '₱' + Number(estimatedRevenue).toLocaleString();
        } catch (error) {
            if (error.name === 'AbortError' || error.code === 'ECONNABORTED' || error.code === 'ERR_CANCELED') {
                return;
            }
            console.error('Error fetching reservation stats:', error);
        }
    }

    async function fetchReservations() {
        if (reservationState.isLoading) {
            return;
        }

        reservationState.isLoading = true;
        showLoadingState();

        try {
            if (!reservationState.abortController) {
                reservationState.abortController = new AbortController();
            }

            var params = new URLSearchParams({
                page: reservationState.currentPage,
                per_page: reservationState.perPage,
                sort_by: reservationState.sortBy,
                sort_order: reservationState.sortOrder
            });

            if (reservationState.searchQuery) {
                params.append('search', reservationState.searchQuery);
            }

            if (reservationState.statusFilter) {
                params.append('status', reservationState.statusFilter);
            }

            var response = await axios.get('/api/reservations?' + params.toString(), {
                signal: reservationState.abortController.signal
            });

            var data = response.data;
            if (!data.data || !Array.isArray(data.data)) {
                showEmptyState('No reservations found.');
                hideLoadingState();
                return;
            }

            reservationState.totalPages = data.last_page || 1;
            reservationState.totalCount = data.total || 0;

            renderTable(data.data);
            updatePagination(data);
            hideLoadingState();
        } catch (error) {
            if (error.name === 'AbortError' || error.code === 'ECONNABORTED' || error.code === 'ERR_CANCELED') {
                hideLoadingState();
                return;
            }

            console.error('Error fetching reservations:', error);
            var errorMessage = error.response && error.response.data && error.response.data.message
                ? error.response.data.message
                : (error.message || 'Failed to load reservations. Please try again.');

            showErrorNotification(errorMessage);
            showEmptyState(errorMessage);
            hideLoadingState();
        } finally {
            reservationState.isLoading = false;
        }
    }

    function updateSearchIndicators() {
        var searchIndicatorsDiv = document.getElementById('searchIndicators');
        if (!searchIndicatorsDiv) {
            return;
        }

        var query = reservationState.searchQuery.trim().toLowerCase();
        if (!query) {
            searchIndicatorsDiv.innerHTML = '';
            return;
        }

        var indicators = ['Customer', 'Item'];
        if (!isNaN(query) && query !== '') {
            indicators.unshift('Reservation ID');
        }

        var html = '';
        indicators.forEach(function(indicator) {
            html += '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-medium bg-violet-100 text-violet-700 dark:bg-violet-900/40 dark:text-violet-300 border border-violet-200 dark:border-violet-800/60">' + indicator + '</span>';
        });

        searchIndicatorsDiv.innerHTML = html;
    }

    function renderTable(reservations) {
        var tbody = document.getElementById('reservationTableBody');
        if (!tbody) {
            return;
        }

        tbody.innerHTML = '';

        if (!reservations.length) {
            var message = 'No reservations found';

            if (reservationState.searchQuery) {
                message = 'No matches found for "' + reservationState.searchQuery + '"';
            } else if (reservationState.statusFilter) {
                message = 'No reservations with this status';
            }

            showEmptyState(message);
            return;
        }

        reservations.forEach(function(reservation) {
            var statusName = ((reservation.status && reservation.status.status_name) || 'unknown').toLowerCase();
            var statusLabel = statusName.charAt(0).toUpperCase() + statusName.slice(1);

            var statusColor = statusName === 'confirmed'
                ? 'bg-emerald-500/15 text-emerald-600 border-emerald-500/40 dark:text-emerald-300'
                : statusName === 'pending'
                ? 'bg-amber-500/15 text-amber-600 border-amber-500/40 dark:text-amber-300'
                : statusName === 'cancelled'
                ? 'bg-red-500/15 text-red-600 border-red-500/40 dark:text-red-300'
                : statusName === 'completed'
                ? 'bg-blue-500/15 text-blue-600 border-blue-500/40 dark:text-blue-300'
                : 'bg-gray-500/15 text-gray-600 border-gray-500/40 dark:text-gray-300';

            var statusBgColor = statusName === 'confirmed'
                ? 'bg-emerald-500'
                : statusName === 'pending'
                ? 'bg-amber-500'
                : statusName === 'cancelled'
                ? 'bg-red-500'
                : statusName === 'completed'
                ? 'bg-blue-500'
                : 'bg-gray-500';

            var customerName = [
                reservation.customer && reservation.customer.first_name,
                reservation.customer && reservation.customer.last_name
            ].filter(Boolean).join(' ') || (reservation.customer && reservation.customer.email) || 'N/A';

            var reservationItems = Array.isArray(reservation.items) ? reservation.items : [];
            var firstItemName = reservationItems[0] && reservationItems[0].item ? reservationItems[0].item.name : 'N/A';
            var additionalItemsCount = Math.max(reservationItems.length - 1, 0);
            var itemLabel = additionalItemsCount > 0 ? (firstItemName + ' +' + additionalItemsCount) : firstItemName;

            var totalAmount = reservationItems.reduce(function(total, item) {
                return total + ((Number(item.rental_price) || 0) * (Number(item.quantity) || 1));
            }, 0);

            var row = document.createElement('tr');
            row.className = 'border-b border-neutral-200 hover:bg-neutral-100 dark:border-neutral-900/60 dark:hover:bg-white/5 transition-colors duration-300 ease-in-out';
            row.innerHTML = '' +
                '<td class="py-3.5 pr-4 pl-4 text-neutral-500 font-geist-mono">#' + (reservation.reservation_id || 'N/A') + '</td>' +
                '<td class="py-3.5 pr-4 text-neutral-900 dark:text-neutral-100">' + customerName + '</td>' +
                '<td class="py-3.5 pr-4 text-neutral-600 dark:text-neutral-300">' + itemLabel + '</td>' +
                '<td class="py-3.5 pr-4 text-neutral-600 dark:text-neutral-300 font-geist-mono">' + formatDate(reservation.start_date) + '</td>' +
                '<td class="py-3.5 pr-2 text-neutral-600 dark:text-neutral-300 font-geist-mono">' + formatDate(reservation.end_date) + '</td>' +
                '<td class="py-3.5 pr-2"><span class="inline-flex items-center rounded-full ' + statusColor + ' px-2 py-1 text-[11px] font-medium border transition-colors duration-300 ease-in-out"><span class="mr-1.5 h-1.5 w-1.5 rounded-full ' + statusBgColor + '"></span>' + statusLabel + '</span></td>' +
                '<td class="py-3.5 pr-4 text-left text-neutral-900 dark:text-neutral-100 font-geist-mono">₱' + totalAmount.toLocaleString() + '</td>' +
                '<td class="py-3.5 pl-2 text-left text-neutral-500 dark:text-neutral-400"><div class="inline-flex items-center gap-2"><button class="rounded-lg p-1.5 hover:bg-violet-600 hover:text-white transition-colors duration-300 ease-in-out" aria-label="Edit"><x-icon name="edit" class="h-3.5 w-3.5" /></button><button class="rounded-lg p-1.5 text-red-500 hover:bg-red-500/15 hover:text-red-400 transition-colors duration-300 ease-in-out" aria-label="Delete"><x-icon name="trash" class="h-3.5 w-3.5" /></button></div></td>';

            tbody.appendChild(row);
        });

        hideEmptyState();
    }

    function formatDate(value) {
        if (!value) {
            return 'N/A';
        }

        var date = new Date(value);
        if (isNaN(date.getTime())) {
            return value;
        }

        return date.toISOString().split('T')[0];
    }

    function updatePagination(data) {
        var prevBtn = document.getElementById('prevBtn');
        var nextBtn = document.getElementById('nextBtn');
        var pageInfo = document.getElementById('pageInfo');
        var pageStart = document.getElementById('pageStart');
        var pageEnd = document.getElementById('pageEnd');
        var pageTotal = document.getElementById('pageTotal');
        var paginationControls = document.getElementById('paginationControls');

        if (!prevBtn || !nextBtn || !pageInfo) {
            return;
        }

        prevBtn.disabled = !data.links || !data.links[0] || !data.links[0].url;
        nextBtn.disabled = !data.links || !data.links[data.links.length - 1] || !data.links[data.links.length - 1].url;

        pageInfo.textContent = 'Page ' + (data.current_page || 1) + ' of ' + (data.last_page || 1);
        if (pageStart) pageStart.textContent = data.from || 0;
        if (pageEnd) pageEnd.textContent = data.to || 0;
        if (pageTotal) pageTotal.textContent = data.total || 0;

        if (paginationControls) {
            paginationControls.style.display = 'flex';
        }
    }

    function showEmptyState(message) {
        var tbody = document.getElementById('reservationTableBody');
        var emptyState = document.getElementById('emptyState');
        var paginationControls = document.getElementById('paginationControls');

        if (tbody) {
            tbody.innerHTML = '';
        }
        if (emptyState) {
            emptyState.innerHTML = '<p class="text-neutral-500 dark:text-neutral-400">' + message + '</p>';
            emptyState.style.display = 'block';
        }
        if (paginationControls) {
            paginationControls.style.display = 'none';
        }
    }

    function hideEmptyState() {
        var emptyState = document.getElementById('emptyState');
        if (emptyState) {
            emptyState.style.display = 'none';
        }
    }

    function showLoadingState() {
        var tbody = document.getElementById('reservationTableBody');
        var emptyState = document.getElementById('emptyState');
        var paginationControls = document.getElementById('paginationControls');

        if (!tbody) {
            return;
        }

        var skeletonRows = Array.from({length: 5}, function() {
            return '<tr class="border-b border-neutral-200 dark:border-neutral-800 hover:bg-neutral-50 dark:hover:bg-neutral-900/50 transition-colors animate-pulse"><td class="px-4 py-3"><div class="h-4 bg-neutral-300 dark:bg-neutral-700 rounded w-1/3"></div></td><td class="px-4 py-3"><div class="h-4 bg-neutral-300 dark:bg-neutral-700 rounded w-2/3"></div></td><td class="px-4 py-3"><div class="h-4 bg-neutral-300 dark:bg-neutral-700 rounded w-3/4"></div></td><td class="px-4 py-3"><div class="h-4 bg-neutral-300 dark:bg-neutral-700 rounded w-1/2"></div></td><td class="px-4 py-3"><div class="h-4 bg-neutral-300 dark:bg-neutral-700 rounded w-1/2"></div></td><td class="px-4 py-3"><div class="h-4 bg-neutral-300 dark:bg-neutral-700 rounded w-1/3"></div></td><td class="px-4 py-3"><div class="h-4 bg-neutral-300 dark:bg-neutral-700 rounded w-1/4"></div></td><td class="px-4 py-3"><div class="h-4 bg-neutral-300 dark:bg-neutral-700 rounded w-1/3"></div></td></tr>';
        }).join('');

        tbody.innerHTML = skeletonRows;

        if (emptyState) {
            emptyState.style.display = 'none';
        }
        if (paginationControls) {
            paginationControls.style.display = 'none';
        }
    }

    function hideLoadingState() {
        hideEmptyState();
        var tbody = document.getElementById('reservationTableBody');
        if (tbody) {
            tbody.style.opacity = '1';
        }
    }

    function showErrorNotification(message) {
        var notification = document.createElement('div');
        notification.className = 'fixed top-4 right-4 max-w-sm bg-red-100 border border-red-300 dark:bg-red-900 dark:border-red-700 rounded-lg px-5 py-4 shadow-xl z-[999] flex items-start gap-3 animate-in slide-in-from-top-2';
        notification.innerHTML = '<svg class="h-5 w-5 text-red-700 dark:text-red-200 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" /></svg><div class="flex-1"><p class="text-sm font-semibold text-red-900 dark:text-red-50">' + message + '</p></div><button onclick="this.parentElement.remove()" class="text-red-700 dark:text-red-200 hover:text-red-900 dark:hover:text-red-50 flex-shrink-0 transition-colors"><svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" /></svg></button>';
        document.body.appendChild(notification);

        setTimeout(function() {
            notification.style.opacity = '0';
            notification.style.transition = 'opacity 0.3s ease-out';
            setTimeout(function() {
                notification.remove();
            }, 300);
        }, 5000);
    }

    function previousPage() {
        if (reservationState.currentPage > 1) {
            reservationState.currentPage--;
            fetchReservations();
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
    }

    function nextPage() {
        if (reservationState.currentPage < reservationState.totalPages) {
            reservationState.currentPage++;
            fetchReservations();
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
    }

    document.addEventListener('DOMContentLoaded', initializeReservationPage);
</script>
</body>
</html>
