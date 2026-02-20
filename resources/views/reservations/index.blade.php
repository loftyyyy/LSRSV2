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

    <style>
        .reservations-main-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        .reservations-main-scrollbar::-webkit-scrollbar {
            display: none;
        }
    </style>
</head>

<body class="min-h-screen flex font-geist bg-neutral-100 text-neutral-900 dark:bg-black dark:text-neutral-50">
<x-sidebar />

<main class="reservations-main-scrollbar flex-1 ml-64 flex flex-col px-10 py-8 overflow-x-hidden overflow-y-auto bg-gradient-to-b from-neutral-100 via-neutral-100 to-neutral-200 dark:from-black dark:via-black dark:to-neutral-950">
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
                <a href="/reservations/reports" class="inline-flex items-center gap-2 rounded-xl border border-neutral-300 bg-white px-3.5 py-2 text-neutral-700 hover:bg-violet-600 dark:hover:text-black hover:text-white dark:border-neutral-800 dark:bg-neutral-950/80 dark:text-neutral-200 transition-colors duration-300 ease-in-out">
                    <span class="inline-flex h-5 w-5 items-center justify-center rounded-md">
                        <x-icon name="chart-column" class="h-4 w-4" />
                    </span>
                    <span class="text-[14px] font-medium tracking-wide">Reports</span>
                </a>

                <button type="button" onclick="openBrowseItemsModal()" class="inline-flex items-center gap-2 rounded-xl border border-neutral-300 bg-white px-3.5 py-2 text-neutral-700 hover:bg-violet-600 dark:hover:text-black hover:text-white dark:border-neutral-800 dark:bg-neutral-950/80 dark:text-neutral-200 transition-colors duration-300 ease-in-out">
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
            <div id="activeReservationsCount" class="text-3xl font-semibold text-sky-600 dark:text-sky-400 transition-colors duration-300 ease-in-out">
                0
            </div>
        </div>
        <div class="rounded-2xl p-6 border border-neutral-200 bg-white dark:border-neutral-900 dark:bg-neutral-950/60 shadow-sm dark:shadow-[0_18px_60px_rgba(0,0,0,0.65)] transition-colors duration-300 ease-in-out">
            <div class="text-sm text-neutral-500 dark:text-neutral-400 mb-2 transition-colors duration-300 ease-in-out">
                Total Items Reserved
            </div>
            <div id="totalItemsReservedCount" class="text-3xl font-semibold text-indigo-600 dark:text-indigo-400 transition-colors duration-300 ease-in-out">
                0
            </div>
        </div>
        <div class="rounded-2xl p-6 border border-neutral-200 bg-white dark:border-neutral-900 dark:bg-neutral-950/60 shadow-sm dark:shadow-[0_18px_60px_rgba(0,0,0,0.65)] transition-colors duration-300 ease-in-out">
            <div class="text-sm text-neutral-500 dark:text-neutral-400 mb-2 transition-colors duration-300 ease-in-out">
                Estimated Revenue
            </div>
            <div id="estimatedRevenueCount" class="text-3xl font-semibold text-emerald-600 dark:text-emerald-400 transition-colors duration-300 ease-in-out">
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
                                <button id="clearSearchBtn" type="button" class="hidden inline-flex h-5 w-5 items-center justify-center rounded-md text-neutral-400 hover:text-neutral-700 dark:hover:text-neutral-200 transition-colors duration-200" aria-label="Clear search">
                                    &times;
                                </button>
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

{{-- Browse Items Modal --}}
@include('reservations.partials.browse-items-modal')

{{-- Browse Item Details Modal --}}
@include('reservations.partials.browse-item-details-modal')

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
        listAbortController: null,
        statsAbortController: null
    };

    var searchDebounceTimer;

    function initializeReservationPage() {
        var searchInput = document.getElementById('searchInput');
        var clearSearchBtn = document.getElementById('clearSearchBtn');
        var filterMenu = document.getElementById('filter-menu');
        var filterButtonText = document.getElementById('filter-button-text');

        reservationState.listAbortController = null;
        reservationState.statsAbortController = null;

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

            searchInput.addEventListener('keydown', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    clearTimeout(searchDebounceTimer);
                    reservationState.searchQuery = e.target.value;
                    reservationState.currentPage = 1;
                    updateSearchIndicators();
                    fetchReservations();
                }
            });
        }

        if (clearSearchBtn) {
            clearSearchBtn.addEventListener('click', function() {
                reservationState.searchQuery = '';
                reservationState.currentPage = 1;
                if (searchInput) {
                    searchInput.value = '';
                    searchInput.focus();
                }
                updateSearchIndicators();
                fetchReservations();
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
            if (reservationState.statsAbortController) {
                reservationState.statsAbortController.abort();
            }
            reservationState.statsAbortController = new AbortController();

            var response = await axios.get('/api/reservations/reports/generate', {
                signal: reservationState.statsAbortController.signal
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
        if (reservationState.listAbortController) {
            reservationState.listAbortController.abort();
        }
        reservationState.listAbortController = new AbortController();

        reservationState.isLoading = true;
        showLoadingState();

        try {
            var params = new URLSearchParams({
                page: reservationState.currentPage,
                per_page: reservationState.perPage,
                sort_by: reservationState.sortBy,
                sort_order: reservationState.sortOrder
            });

            var normalizedSearchQuery = reservationState.searchQuery.trim();
            if (normalizedSearchQuery) {
                params.append('search', normalizedSearchQuery);
            }

            if (reservationState.statusFilter) {
                params.append('status', reservationState.statusFilter);
            }

            var response = await axios.get('/api/reservations?' + params.toString(), {
                signal: reservationState.listAbortController.signal
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
        var clearSearchBtn = document.getElementById('clearSearchBtn');

        if (clearSearchBtn) {
            clearSearchBtn.classList.toggle('hidden', !query);
        }

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
            var firstItemName = reservationItems[0]
                ? ((reservationItems[0].variant && reservationItems[0].variant.name) || (reservationItems[0].item && reservationItems[0].item.name) || 'N/A')
                : 'N/A';
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

    if (!globalThis.browseItemsModalState) {
        globalThis.browseItemsModalState = {
            isOpen: false,
            isLoading: false,
            loaded: false,
            items: [],
            filteredItems: []
        };
    }

    if (!globalThis.browseItemDetailsModalState) {
        globalThis.browseItemDetailsModalState = {
            isOpen: false,
            currentItemId: null,
            currentItem: null,
            selectedImageIndex: 0,
            sortedImages: []
        };
    }

    var browseItemsModalState = globalThis.browseItemsModalState;
    var browseItemDetailsModalState = globalThis.browseItemDetailsModalState;

    function openBrowseItemsModal() {
        browseItemsModalState.isOpen = true;

        var modal = document.getElementById('browseItemsModal');
        if (!modal) {
            return;
        }

        modal.classList.remove('hidden');
        modal.classList.add('flex');

        var searchInput = document.getElementById('browseItemsSearchInput');
        if (searchInput) {
            searchInput.value = '';
        }

        if (!browseItemsModalState.loaded) {
            fetchBrowseItems();
        } else {
            browseItemsModalState.filteredItems = browseItemsModalState.items.slice();
            renderBrowseItems();
        }
    }

    function closeBrowseItemsModal() {
        browseItemsModalState.isOpen = false;

        if (browseItemDetailsModalState.isOpen) {
            closeBrowseItemDetailsModal();
        }

        var modal = document.getElementById('browseItemsModal');
        if (!modal) {
            return;
        }

        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    async function fetchBrowseItems() {
        if (browseItemsModalState.isLoading) {
            return;
        }

        browseItemsModalState.isLoading = true;
        toggleBrowseItemsState('loading');

        try {
            var response = await axios.get('/api/reservations/items/browse');
            var responseData = response.data && response.data.data ? response.data.data : response.data;
            var items = Array.isArray(responseData) ? responseData : (responseData && responseData.data ? responseData.data : []);

            browseItemsModalState.items = items;
            browseItemsModalState.filteredItems = items.slice();
            browseItemsModalState.loaded = true;

            renderBrowseItems();
        } catch (error) {
            console.error('Error fetching browse items:', error);
            browseItemsModalState.items = [];
            browseItemsModalState.filteredItems = [];
            browseItemsModalState.loaded = true;
            renderBrowseItems();
        } finally {
            browseItemsModalState.isLoading = false;
        }
    }

    function toggleBrowseItemsState(state) {
        var loadingEl = document.getElementById('browseItemsLoading');
        var emptyEl = document.getElementById('browseItemsEmpty');
        var gridEl = document.getElementById('browseItemsGrid');

        if (!loadingEl || !emptyEl || !gridEl) {
            return;
        }

        loadingEl.classList.toggle('hidden', state !== 'loading');
        emptyEl.classList.toggle('hidden', state !== 'empty');
        gridEl.classList.toggle('hidden', state !== 'grid');
    }

    function renderBrowseItems() {
        var gridEl = document.getElementById('browseItemsGrid');
        var emptyEl = document.getElementById('browseItemsEmpty');

        if (!gridEl || !emptyEl) {
            return;
        }

        var items = browseItemsModalState.filteredItems;
        if (!Array.isArray(items) || items.length === 0) {
            emptyEl.querySelector('p').textContent = browseItemsModalState.items.length
                ? 'No items match your search'
                : 'No items found';
            toggleBrowseItemsState('empty');
            gridEl.innerHTML = '';
            return;
        }

        gridEl.innerHTML = items.map(function(item) {
            var imageSource = item.images && item.images.length > 0
                ? '/storage/' + item.images[0].image_path
                : '';

            var statusName = item.status && item.status.status_name
                ? String(item.status.status_name).toLowerCase()
                : 'available';

            var statusClass = statusName === 'available'
                ? 'bg-emerald-500/15 text-emerald-600 border-emerald-500/40 dark:text-emerald-300'
                : statusName === 'rented'
                ? 'bg-blue-500/15 text-blue-600 border-blue-500/40 dark:text-blue-300'
                : statusName === 'maintenance'
                ? 'bg-amber-500/15 text-amber-600 border-amber-500/40 dark:text-amber-300'
                : 'bg-neutral-500/15 text-neutral-600 border-neutral-500/40 dark:text-neutral-300';

            var statusLabel = statusName.charAt(0).toUpperCase() + statusName.slice(1);
            var itemType = item.item_type ? (item.item_type.charAt(0).toUpperCase() + item.item_type.slice(1)) : 'Item';
            var rentalPrice = Number(item.rental_price || 0).toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

            return '' +
                '<button type="button" onclick="openBrowseItemDetailsModal(' + item.variant_id + ')" class="w-full text-left rounded-2xl border border-neutral-200 bg-white dark:border-neutral-800 dark:bg-neutral-900/30 p-4 transition-colors duration-300 ease-in-out hover:border-violet-300 dark:hover:border-violet-700 focus:outline-none focus:ring-2 focus:ring-violet-500/40">' +
                    '<div class="flex items-start gap-4">' +
                        '<div class="h-16 w-16 flex-shrink-0 rounded-xl border border-neutral-200 bg-neutral-100 dark:border-neutral-800 dark:bg-neutral-900 overflow-hidden flex items-center justify-center">' +
                            (imageSource
                                ? '<img src="' + imageSource + '" alt="' + (item.name || 'Item image') + '" class="h-full w-full object-cover" loading="lazy">'
                                : '<svg class="h-6 w-6 text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" /></svg>') +
                        '</div>' +
                        '<div class="min-w-0 flex-1">' +
                            '<div class="flex items-center justify-between gap-3">' +
                                '<p class="text-sm font-semibold text-neutral-900 dark:text-white truncate">' + (item.name || 'Unnamed Item') + '</p>' +
                                '<span class="inline-flex items-center rounded-full border px-2 py-0.5 text-[10px] font-medium ' + statusClass + '">' + statusLabel + '</span>' +
                            '</div>' +
                            '<p class="mt-0.5 text-xs text-neutral-500 dark:text-neutral-400 truncate">' + (item.representative_sku || 'No SKU') + '</p>' +
                            '<p class="mt-1 text-xs text-neutral-600 dark:text-neutral-300">' + itemType + ' · Size ' + (item.size || '-') + ' · ' + (item.color || '-') + '</p>' +
                            '<p class="mt-1 text-xs text-neutral-500 dark:text-neutral-400">Available: ' + (item.available_quantity ?? 0) + '</p>' +
                            '<p class="mt-2 text-sm font-semibold text-violet-600 dark:text-violet-400 font-geist-mono">₱' + rentalPrice + '<span class="text-xs font-normal text-neutral-500 dark:text-neutral-400"> / day</span></p>' +
                        '</div>' +
                    '</div>' +
                '</button>';
        }).join('');

        toggleBrowseItemsState('grid');
    }

    async function openBrowseItemDetailsModal(itemId) {
        browseItemDetailsModalState.isOpen = true;
        browseItemDetailsModalState.currentItemId = itemId;
        browseItemDetailsModalState.selectedImageIndex = 0;
        browseItemDetailsModalState.sortedImages = [];

        var modal = document.getElementById('browseItemDetailsModal');
        if (!modal) {
            return;
        }

        modal.classList.remove('hidden');
        modal.classList.add('flex');

        document.getElementById('browseItemDetailsLoading').classList.remove('hidden');
        document.getElementById('browseItemDetailsError').classList.add('hidden');
        document.getElementById('browseItemDetailsData').classList.add('hidden');
        document.getElementById('browseItemDetailsTitle').textContent = 'Loading...';
        document.getElementById('browseItemDetailsSubtitle').textContent = 'Loading...';

        try {
            var response = await axios.get('/api/reservations/items/' + itemId + '/details');
            var item = response.data && response.data.data ? response.data.data : null;

            if (!item) {
                throw new Error('Unable to load item details.');
            }

            browseItemDetailsModalState.currentItem = item;
            populateBrowseItemDetails(item);

            document.getElementById('browseItemDetailsLoading').classList.add('hidden');
            document.getElementById('browseItemDetailsData').classList.remove('hidden');
        } catch (error) {
            console.error('Error loading browse item details:', error);

            document.getElementById('browseItemDetailsLoading').classList.add('hidden');
            document.getElementById('browseItemDetailsError').classList.remove('hidden');
            document.getElementById('browseItemDetailsErrorMessage').textContent =
                (error.response && error.response.data && error.response.data.message)
                    ? error.response.data.message
                    : (error.message || 'Failed to load item details');
        }
    }

    function closeBrowseItemDetailsModal() {
        browseItemDetailsModalState.isOpen = false;
        browseItemDetailsModalState.currentItemId = null;
        browseItemDetailsModalState.currentItem = null;
        browseItemDetailsModalState.selectedImageIndex = 0;
        browseItemDetailsModalState.sortedImages = [];

        var modal = document.getElementById('browseItemDetailsModal');
        if (!modal) {
            return;
        }

        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    function populateBrowseItemDetails(item) {
        var titleEl = document.getElementById('browseItemDetailsTitle');
        var subtitleEl = document.getElementById('browseItemDetailsSubtitle');

        titleEl.textContent = item.name || 'Item Details';
        subtitleEl.textContent = [item.representative_sku, item.item_type, item.color, item.size ? ('Size ' + item.size) : null].filter(Boolean).join(' • ') || 'Item Details';

        var statusName = item.status && item.status.status_name ? String(item.status.status_name).toLowerCase() : 'unknown';
        var statusMap = {
            available: {
                label: 'Available',
                subtitle: 'Ready for rent',
                cardClass: 'bg-emerald-500/10 dark:bg-emerald-500/20 border-emerald-500/30 dark:border-emerald-500/30',
                dotClass: 'bg-emerald-500',
                textClass: 'text-emerald-700 dark:text-emerald-400'
            },
            rented: {
                label: 'Rented',
                subtitle: 'Currently rented',
                cardClass: 'bg-blue-500/10 dark:bg-blue-500/20 border-blue-500/30 dark:border-blue-500/30',
                dotClass: 'bg-blue-500',
                textClass: 'text-blue-700 dark:text-blue-400'
            },
            maintenance: {
                label: 'Maintenance',
                subtitle: 'Under maintenance',
                cardClass: 'bg-amber-500/10 dark:bg-amber-500/20 border-amber-500/30 dark:border-amber-500/30',
                dotClass: 'bg-amber-500',
                textClass: 'text-amber-700 dark:text-amber-400'
            },
            retired: {
                label: 'Retired',
                subtitle: 'No longer available',
                cardClass: 'bg-neutral-500/10 dark:bg-neutral-500/20 border-neutral-500/30 dark:border-neutral-500/30',
                dotClass: 'bg-neutral-500',
                textClass: 'text-neutral-700 dark:text-neutral-400'
            },
            unknown: {
                label: 'Unknown',
                subtitle: 'Status unavailable',
                cardClass: 'bg-neutral-500/10 dark:bg-neutral-500/20 border-neutral-500/30 dark:border-neutral-500/30',
                dotClass: 'bg-neutral-500',
                textClass: 'text-neutral-700 dark:text-neutral-400'
            }
        };

        var statusConfig = statusMap[statusName] || statusMap.unknown;
        document.getElementById('browseItemDetailStatusCard').className = 'rounded-xl p-4 border ' + statusConfig.cardClass;
        document.getElementById('browseItemDetailStatusDot').className = 'h-2 w-2 rounded-full ' + statusConfig.dotClass;
        var statusTextEl = document.getElementById('browseItemDetailStatusText');
        statusTextEl.className = 'text-sm font-semibold ' + statusConfig.textClass;
        statusTextEl.textContent = statusConfig.label;
        document.getElementById('browseItemDetailStatusSubtitle').textContent = statusConfig.subtitle;

        document.getElementById('browseItemDetailSku').textContent = item.representative_sku || '-';
        document.getElementById('browseItemDetailType').textContent = item.item_type ? (item.item_type.charAt(0).toUpperCase() + item.item_type.slice(1)) : '-';
        document.getElementById('browseItemDetailSize').textContent = item.size || '-';
        document.getElementById('browseItemDetailColor').textContent = item.color || '-';
        document.getElementById('browseItemDetailDesign').textContent = item.design || '-';

        document.getElementById('browseItemDetailRentalPrice').textContent = item.rental_price
            ? ('₱' + parseFloat(item.rental_price).toLocaleString())
            : '-';

        var depositAmount = item.deposit_amount ? parseFloat(item.deposit_amount) : 0;
        document.getElementById('browseItemDetailDeposit').textContent = '₱' + depositAmount.toLocaleString();

        var sellable = item.is_sellable === true || item.is_sellable === 1 || item.is_sellable === '1';
        var sellingRow = document.getElementById('browseItemDetailSellingPriceRow');
        if (sellable) {
            sellingRow.classList.remove('hidden');
            if (item.selling_price && parseFloat(item.selling_price) > 0) {
                document.getElementById('browseItemDetailSellingPrice').textContent = '₱' + parseFloat(item.selling_price).toLocaleString();
            } else {
                document.getElementById('browseItemDetailSellingPrice').textContent = '-';
            }
        } else {
            sellingRow.classList.add('hidden');
        }

        document.getElementById('browseItemDetailCreated').textContent = item.created_at
            ? formatBrowseItemDetailsDate(item.created_at)
            : '-';
        document.getElementById('browseItemDetailUpdated').textContent = item.updated_at
            ? formatBrowseItemDetailsDate(item.updated_at)
            : '-';

        var updatedByRow = document.getElementById('browseItemDetailUpdatedBySection');
        if (item.updated_by_user && item.updated_by_user.name) {
            updatedByRow.classList.remove('hidden');
            document.getElementById('browseItemDetailUpdatedBy').textContent = item.updated_by_user.name;
        } else {
            updatedByRow.classList.add('hidden');
        }

        renderBrowseItemDetailsImages(Array.isArray(item.images) ? item.images : []);
    }

    function renderBrowseItemDetailsImages(images) {
        var mainImageEl = document.getElementById('browseItemDetailsMainImage');
        var thumbnailsEl = document.getElementById('browseItemDetailsThumbnails');
        var prevBtn = document.getElementById('browseItemDetailsPrevBtn');
        var nextBtn = document.getElementById('browseItemDetailsNextBtn');
        var imageCounter = document.getElementById('browseItemDetailsImageCounter');

        if (!mainImageEl || !thumbnailsEl || !prevBtn || !nextBtn || !imageCounter) {
            return;
        }

        if (!images.length) {
            mainImageEl.innerHTML = '<div class="text-neutral-400 dark:text-neutral-600 flex flex-col items-center gap-3"><svg class="h-16 w-16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg><span class="text-sm">No photos available</span></div>';
            thumbnailsEl.innerHTML = '';
            browseItemDetailsModalState.sortedImages = [];
            browseItemDetailsModalState.selectedImageIndex = 0;
            prevBtn.classList.add('hidden');
            nextBtn.classList.add('hidden');
            imageCounter.classList.add('hidden');
            return;
        }

        var sortedImages = images.slice().sort(function(a, b) {
            if (a.is_primary && !b.is_primary) {
                return -1;
            }
            if (!a.is_primary && b.is_primary) {
                return 1;
            }
            return (a.display_order || 0) - (b.display_order || 0);
        });

        browseItemDetailsModalState.sortedImages = sortedImages;

        if (browseItemDetailsModalState.selectedImageIndex >= sortedImages.length) {
            browseItemDetailsModalState.selectedImageIndex = 0;
        }

        var selectedIndex = browseItemDetailsModalState.selectedImageIndex;
        var currentImage = sortedImages[selectedIndex] || sortedImages[0];

        var primaryBadge = currentImage.is_primary
            ? '<span class="absolute top-3 left-3 bg-violet-600 text-white text-xs font-medium px-2.5 py-1 rounded-full shadow-lg flex items-center gap-1.5"><svg class="h-3.5 w-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10.868 2.884c-.321-.772-1.415-.772-1.736 0l-1.83 4.401-4.753.381c-.833.067-1.171 1.107-.536 1.651l3.62 3.102-1.106 4.637c-.194.813.691 1.456 1.405 1.02L10 15.591l4.069 2.485c.713.436 1.598-.207 1.404-1.02l-1.106-4.637 3.62-3.102c.635-.544.297-1.584-.536-1.65l-4.752-.382-1.831-4.401z" clip-rule="evenodd" /></svg>Primary</span>'
            : '';

        var viewTypeBadge = currentImage.view_type
            ? '<span class="absolute bottom-3 left-3 bg-black/60 text-white text-xs font-medium px-2.5 py-1 rounded-full backdrop-blur-sm">' +
                currentImage.view_type.charAt(0).toUpperCase() + currentImage.view_type.slice(1) + ' View</span>'
            : '';

        mainImageEl.innerHTML = '<div class="relative w-full h-full"><img src="' + currentImage.image_url + '" alt="' + (currentImage.caption || 'Item photo') + '" class="w-full h-full object-contain">' + primaryBadge + viewTypeBadge + '</div>';

        if (sortedImages.length > 1) {
            prevBtn.classList.remove('hidden');
            prevBtn.classList.add('flex');
            nextBtn.classList.remove('hidden');
            nextBtn.classList.add('flex');
            imageCounter.classList.remove('hidden');

            imageCounter.textContent = (selectedIndex + 1) + ' / ' + sortedImages.length;

            if (selectedIndex === 0) {
                prevBtn.classList.add('opacity-30', 'cursor-not-allowed');
                prevBtn.classList.remove('hover:bg-white', 'dark:hover:bg-neutral-700');
            } else {
                prevBtn.classList.remove('opacity-30', 'cursor-not-allowed');
                prevBtn.classList.add('hover:bg-white', 'dark:hover:bg-neutral-700');
            }

            if (selectedIndex === sortedImages.length - 1) {
                nextBtn.classList.add('opacity-30', 'cursor-not-allowed');
                nextBtn.classList.remove('hover:bg-white', 'dark:hover:bg-neutral-700');
            } else {
                nextBtn.classList.remove('opacity-30', 'cursor-not-allowed');
                nextBtn.classList.add('hover:bg-white', 'dark:hover:bg-neutral-700');
            }
        } else {
            prevBtn.classList.add('hidden');
            prevBtn.classList.remove('flex');
            nextBtn.classList.add('hidden');
            nextBtn.classList.remove('flex');
            imageCounter.classList.add('hidden');
        }

        thumbnailsEl.innerHTML = sortedImages.map(function(image, index) {
            var selectedClass = index === browseItemDetailsModalState.selectedImageIndex
                ? 'border-violet-500 ring-2 ring-violet-500/30'
                : 'border-neutral-200 dark:border-neutral-700 hover:border-violet-400';

            var primaryDot = image.is_primary
                ? '<span class="absolute top-0.5 right-0.5 w-4 h-4 bg-violet-600 rounded-full flex items-center justify-center shadow"><svg class="h-2.5 w-2.5 text-white" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10.868 2.884c-.321-.772-1.415-.772-1.736 0l-1.83 4.401-4.753.381c-.833.067-1.171 1.107-.536 1.651l3.62 3.102-1.106 4.637c-.194.813.691 1.456 1.405 1.02L10 15.591l4.069 2.485c.713.436 1.598-.207 1.404-1.02l-1.106-4.637 3.62-3.102c.635-.544.297-1.584-.536-1.65l-4.752-.382-1.831-4.401z" clip-rule="evenodd" /></svg></span>'
                : '';

            var title = image.view_type
                ? image.view_type.charAt(0).toUpperCase() + image.view_type.slice(1) + ' View'
                : 'Photo';

            return '' +
                '<button type="button" onclick="selectBrowseItemDetailsImage(' + index + ')" class="relative flex-shrink-0 w-16 h-16 rounded-xl overflow-hidden border-2 transition-all duration-200 ' + selectedClass + '" title="' + title + '">' +
                    '<img src="' + image.image_url + '" alt="' + (image.caption || 'Thumbnail') + '" class="h-full w-full object-cover">' +
                    primaryDot +
                '</button>';
        }).join('');
    }

    function navigateBrowseItemDetailsImage(direction) {
        var sortedImages = browseItemDetailsModalState.sortedImages || [];
        if (sortedImages.length <= 1) {
            return;
        }

        var currentIndex = browseItemDetailsModalState.selectedImageIndex;
        var newIndex = currentIndex + direction;

        if (newIndex < 0 || newIndex >= sortedImages.length) {
            return;
        }

        browseItemDetailsModalState.selectedImageIndex = newIndex;
        var currentItemImages = browseItemDetailsModalState.currentItem && browseItemDetailsModalState.currentItem.images
            ? browseItemDetailsModalState.currentItem.images
            : [];
        renderBrowseItemDetailsImages(currentItemImages);
    }

    function selectBrowseItemDetailsImage(index) {
        if (!Array.isArray(browseItemDetailsModalState.sortedImages) || !browseItemDetailsModalState.sortedImages.length) {
            return;
        }

        browseItemDetailsModalState.selectedImageIndex = index;
        var currentItemImages = browseItemDetailsModalState.currentItem && browseItemDetailsModalState.currentItem.images
            ? browseItemDetailsModalState.currentItem.images
            : [];
        renderBrowseItemDetailsImages(currentItemImages);
    }

    function formatBrowseItemDetailsDate(dateString) {
        var date = new Date(dateString);
        if (isNaN(date.getTime())) {
            return '-';
        }
        return date.toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'short',
            day: 'numeric'
        });
    }

    function filterBrowseItems(query) {
        var normalizedQuery = String(query || '').trim().toLowerCase();

        if (!normalizedQuery) {
            browseItemsModalState.filteredItems = browseItemsModalState.items.slice();
            renderBrowseItems();
            return;
        }

        browseItemsModalState.filteredItems = browseItemsModalState.items.filter(function(item) {
            return [
                item.name,
                item.sku,
                item.item_type,
                item.color,
                item.size
            ].some(function(value) {
                return String(value || '').toLowerCase().indexOf(normalizedQuery) !== -1;
            });
        });

        renderBrowseItems();
    }

    function initializeBrowseItemsModal() {
        var searchInput = document.getElementById('browseItemsSearchInput');
        var browseModal = document.getElementById('browseItemsModal');
        var detailsModal = document.getElementById('browseItemDetailsModal');

        if (searchInput) {
            searchInput.addEventListener('input', function(e) {
                filterBrowseItems(e.target.value);
            });
        }

        if (browseModal) {
            browseModal.addEventListener('click', function(e) {
                if (e.target === browseModal && browseItemsModalState.isOpen) {
                    closeBrowseItemsModal();
                }
            });
        }

        if (detailsModal) {
            detailsModal.addEventListener('click', function(e) {
                if (e.target === detailsModal && browseItemDetailsModalState.isOpen) {
                    closeBrowseItemDetailsModal();
                }
            });
        }

        document.addEventListener('keydown', function(e) {
            if (browseItemDetailsModalState.isOpen && e.key === 'ArrowLeft') {
                e.preventDefault();
                navigateBrowseItemDetailsImage(-1);
                return;
            }

            if (browseItemDetailsModalState.isOpen && e.key === 'ArrowRight') {
                e.preventDefault();
                navigateBrowseItemDetailsImage(1);
                return;
            }

            if (e.key === 'Escape' && browseItemDetailsModalState.isOpen) {
                closeBrowseItemDetailsModal();
                return;
            }

            if (e.key === 'Escape' && browseItemsModalState.isOpen) {
                closeBrowseItemsModal();
            }
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        initializeReservationPage();
        initializeBrowseItemsModal();
    });
</script>
</body>
</html>
