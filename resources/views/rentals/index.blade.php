<!DOCTYPE html>
<html lang="en">
<head>
    {{-- Prevent flash of wrong theme --}}
    @include('components.theme-init')
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Rentals · Love &amp; Styles</title>

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
        .rentals-main-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        .rentals-main-scrollbar::-webkit-scrollbar {
            display: none;
        }
    </style>
</head>

<body class="min-h-screen flex font-geist bg-neutral-100 text-neutral-900 dark:bg-black dark:text-neutral-50">
<x-sidebar />

<main class="rentals-main-scrollbar flex-1 ml-64 flex flex-col px-10 py-8 overflow-x-hidden overflow-y-auto bg-gradient-to-b from-neutral-100 via-neutral-100 to-neutral-200 dark:from-black dark:via-black dark:to-neutral-950">
    {{-- Page header --}}
    <header class="mb-8 transition-colors duration-300 ease-in-out">
        <div class="flex items-center justify-between gap-4">
            <div>
                <h1 class="text-3xl font-semibold tracking-tight text-neutral-900 dark:text-white transition-colors duration-300 ease-in-out">
                    Rental Tracking
                </h1>
                <p class="mt-1 text-sm font-geist-mono text-neutral-500 dark:text-neutral-400 transition-colors duration-300 ease-in-out">
                    Monitor active rentals and return dates
                </p>
            </div>

            <div class="flex items-center gap-3 text-xs">
                <a href="/rentals/reports" class="inline-flex items-center gap-2 rounded-xl border border-neutral-300 bg-white px-3.5 py-2 text-neutral-700 hover:bg-violet-600 dark:hover:text-black hover:text-white dark:border-neutral-800 dark:bg-neutral-950/80 dark:text-neutral-200 transition-colors duration-300 ease-in-out">
                    <span class="inline-flex h-5 w-5 items-center justify-center rounded-md">
                        <x-icon name="chart-column" class="h-4 w-4" />
                    </span>
                    <span class="text-[14px] font-medium tracking-wide">Reports</span>
                </a>

                <button type="button" onclick="openProcessReturnModal()" class="inline-flex items-center gap-2 rounded-xl bg-violet-600 px-4 py-2 text-[14px] font-medium tracking-wide text-white dark:text-black shadow-lg hover:text-black dark:hover:text-white hover:bg-violet-500 transition-colors duration-300 ease-in-out">
                    <span class="inline-flex h-5 w-5 items-center justify-center rounded-md">
                        <x-icon name="arrow-left-circle" class="h-4 w-4" />
                    </span>
                    <span>Process Return</span>
                </button>
            </div>
        </div>
    </header>

    {{-- Stats --}}
    <section class="grid grid-cols-4 gap-6 mb-8">
        <div class="rounded-2xl p-6 border border-neutral-200 bg-white dark:border-neutral-900 dark:bg-neutral-950/60 shadow-sm dark:shadow-[0_18px_60px_rgba(0,0,0,0.65)] transition-colors duration-300 ease-in-out">
            <div class="text-sm text-neutral-500 dark:text-neutral-400 mb-2 transition-colors duration-300 ease-in-out">
                Total Rentals
            </div>
            <div id="totalRentalsCount" class="text-3xl font-semibold text-neutral-900 dark:text-white transition-colors duration-300 ease-in-out">
                0
            </div>
        </div>
        <div class="rounded-2xl p-6 border border-neutral-200 bg-white dark:border-neutral-900 dark:bg-neutral-950/60 shadow-sm dark:shadow-[0_18px_60px_rgba(0,0,0,0.65)] transition-colors duration-300 ease-in-out">
            <div class="text-sm text-neutral-500 dark:text-neutral-400 mb-2 transition-colors duration-300 ease-in-out">
                Active Rentals
            </div>
            <div id="activeRentalsCount" class="text-3xl font-semibold text-sky-600 dark:text-sky-400 transition-colors duration-300 ease-in-out">
                0
            </div>
        </div>
        <div class="rounded-2xl p-6 border border-neutral-200 bg-white dark:border-neutral-900 dark:bg-neutral-950/60 shadow-sm dark:shadow-[0_18px_60px_rgba(0,0,0,0.65)] transition-colors duration-300 ease-in-out">
            <div class="text-sm text-neutral-500 dark:text-neutral-400 mb-2 transition-colors duration-300 ease-in-out">
                Overdue
            </div>
            <div id="overdueRentalsCount" class="text-3xl font-semibold text-rose-600 dark:text-rose-400 transition-colors duration-300 ease-in-out">
                0
            </div>
        </div>
        <div class="rounded-2xl p-6 border border-neutral-200 bg-white dark:border-neutral-900 dark:bg-neutral-950/60 shadow-sm dark:shadow-[0_18px_60px_rgba(0,0,0,0.65)] transition-colors duration-300 ease-in-out">
            <div class="text-sm text-neutral-500 dark:text-neutral-400 mb-2 transition-colors duration-300 ease-in-out">
                Returned
            </div>
            <div id="returnedRentalsCount" class="text-3xl font-semibold text-emerald-600 dark:text-emerald-400 transition-colors duration-300 ease-in-out">
                0
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
                                    <li data-status="" class="px-4 py-2 hover:bg-neutral-100 dark:hover:bg-neutral-900 cursor-pointer transition-colors duration-200">All Status</li>
                                    <li data-status="active" class="px-4 py-2 hover:bg-neutral-100 dark:hover:bg-neutral-900 cursor-pointer transition-colors duration-200">Rented</li>
                                    <li data-status="overdue" class="px-4 py-2 hover:bg-neutral-100 dark:hover:bg-neutral-900 cursor-pointer transition-colors duration-200">Overdue</li>
                                    <li data-status="returned" class="px-4 py-2 hover:bg-neutral-100 dark:hover:bg-neutral-900 cursor-pointer transition-colors duration-200">Returned</li>
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
                            <th class="py-2.5 pr-4 font-medium">Released</th>
                            <th class="py-2.5 pr-4 font-medium">Due Date</th>
                            <th class="py-2.5 pr-4 font-medium">Status</th>
                            <th class="py-2.5 pl-2 font-medium text-left">Actions</th>
                        </tr>
                        </thead>

                        <tbody id="rentalTableBody" class="text-[13px]">
                        <!-- Rows will be dynamically inserted here -->
                        </tbody>
                    </table>
                </div>

                <div id="emptyState" class="text-center py-12">
                    <p class="text-neutral-500 dark:text-neutral-400">No rentals found</p>
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

{{-- Axios for API calls --}}
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

<script>
    var rentalState = {
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

    function initializeRentalPage() {
        var searchInput = document.getElementById('searchInput');
        var clearSearchBtn = document.getElementById('clearSearchBtn');
        var filterMenu = document.getElementById('filter-menu');
        var filterButtonText = document.getElementById('filter-button-text');

        rentalState.listAbortController = null;
        rentalState.statsAbortController = null;

        fetchRentalStats();
        fetchRentals();

        if (searchInput) {
            searchInput.addEventListener('input', function(e) {
                clearTimeout(searchDebounceTimer);
                rentalState.searchQuery = e.target.value;
                rentalState.currentPage = 1;

                updateSearchIndicators();

                searchDebounceTimer = setTimeout(function() {
                    fetchRentals();
                }, 300);
            });

            searchInput.addEventListener('keydown', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    clearTimeout(searchDebounceTimer);
                    rentalState.searchQuery = e.target.value;
                    rentalState.currentPage = 1;
                    updateSearchIndicators();
                    fetchRentals();
                }
            });
        }

        if (clearSearchBtn) {
            clearSearchBtn.addEventListener('click', function() {
                rentalState.searchQuery = '';
                rentalState.currentPage = 1;
                if (searchInput) {
                    searchInput.value = '';
                    searchInput.focus();
                }
                updateSearchIndicators();
                fetchRentals();
            });
        }

        if (filterMenu) {
            filterMenu.querySelectorAll('li').forEach(function(item) {
                item.addEventListener('click', function() {
                    var statusText = item.textContent.trim();
                    var statusValue = item.getAttribute('data-status') || '';
                    filterButtonText.textContent = statusText;

                    rentalState.statusFilter = statusValue;
                    rentalState.currentPage = 1;
                    fetchRentals();
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

    async function fetchRentalStats() {
        try {
            if (rentalState.statsAbortController) {
                rentalState.statsAbortController.abort();
            }
            rentalState.statsAbortController = new AbortController();

            var response = await axios.get('/api/rentals/reports/metrics', {
                signal: rentalState.statsAbortController.signal
            });

            var kpis = response.data && response.data.kpis ? response.data.kpis : {};

            var totalRentalsEl = document.getElementById('totalRentalsCount');
            var activeRentalsEl = document.getElementById('activeRentalsCount');
            var overdueRentalsEl = document.getElementById('overdueRentalsCount');
            var returnedRentalsEl = document.getElementById('returnedRentalsCount');

            if (totalRentalsEl) totalRentalsEl.textContent = kpis.total_rentals || 0;
            if (activeRentalsEl) activeRentalsEl.textContent = kpis.active_rentals || 0;
            if (overdueRentalsEl) overdueRentalsEl.textContent = kpis.overdue_rentals || 0;
            if (returnedRentalsEl) returnedRentalsEl.textContent = kpis.completed_rentals || 0;
        } catch (error) {
            if (error.name === 'AbortError' || error.code === 'ECONNABORTED' || error.code === 'ERR_CANCELED') {
                return;
            }
            console.error('Error fetching rental stats:', error);
        }
    }

    async function fetchRentals() {
        if (rentalState.listAbortController) {
            rentalState.listAbortController.abort();
        }
        rentalState.listAbortController = new AbortController();

        rentalState.isLoading = true;
        showLoadingState();

        try {
            var params = new URLSearchParams({
                page: rentalState.currentPage,
                per_page: rentalState.perPage
            });

            var normalizedSearchQuery = rentalState.searchQuery.trim();
            if (normalizedSearchQuery) {
                params.append('search', normalizedSearchQuery);
            }

            if (rentalState.statusFilter) {
                params.append('rental_status', rentalState.statusFilter);
            }

            var response = await axios.get('/api/rentals?' + params.toString(), {
                signal: rentalState.listAbortController.signal
            });

            var data = response.data;
            if (!data.data || !Array.isArray(data.data)) {
                showEmptyState('No rentals found.');
                hideLoadingState();
                return;
            }

            rentalState.totalPages = data.last_page || 1;
            rentalState.totalCount = data.total || 0;

            renderTable(data.data);
            updatePagination(data);
            hideLoadingState();
        } catch (error) {
            if (error.name === 'AbortError' || error.code === 'ECONNABORTED' || error.code === 'ERR_CANCELED') {
                hideLoadingState();
                return;
            }

            console.error('Error fetching rentals:', error);
            var errorMessage = error.response && error.response.data && error.response.data.message
                ? error.response.data.message
                : (error.message || 'Failed to load rentals. Please try again.');

            showErrorNotification(errorMessage);
            showEmptyState(errorMessage);
            hideLoadingState();
        } finally {
            rentalState.isLoading = false;
        }
    }

    function updateSearchIndicators() {
        var searchIndicatorsDiv = document.getElementById('searchIndicators');
        if (!searchIndicatorsDiv) {
            return;
        }

        var query = rentalState.searchQuery.trim().toLowerCase();
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
            indicators.unshift('Rental ID');
        }

        var html = '';
        indicators.forEach(function(indicator) {
            html += '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-medium bg-violet-100 text-violet-700 dark:bg-violet-900/40 dark:text-violet-300 border border-violet-200 dark:border-violet-800/60">' + indicator + '</span>';
        });

        searchIndicatorsDiv.innerHTML = html;
    }

    function renderTable(rentals) {
        var tbody = document.getElementById('rentalTableBody');
        if (!tbody) {
            return;
        }

        tbody.innerHTML = '';

        if (!rentals.length) {
            var message = 'No rentals found';

            if (rentalState.searchQuery) {
                message = 'No matches found for "' + rentalState.searchQuery + '"';
            } else if (rentalState.statusFilter) {
                message = 'No rentals with this status';
            }

            showEmptyState(message);
            return;
        }

        rentals.forEach(function(rental) {
            var statusName = ((rental.status && rental.status.status_name) || 'unknown').toLowerCase();

            // Check if overdue based on due_date and return_date
            var isOverdue = !rental.return_date && rental.due_date && new Date(rental.due_date) < new Date();
            if (isOverdue) {
                statusName = 'overdue';
            }

            var statusLabel = statusName.charAt(0).toUpperCase() + statusName.slice(1);

            var statusColor = statusName === 'returned'
                ? 'bg-emerald-500/15 text-emerald-600 border-emerald-500/40 dark:text-emerald-300'
                : statusName === 'rented' || statusName === 'active'
                ? 'bg-sky-500/15 text-sky-600 border-sky-500/40 dark:text-sky-300'
                : statusName === 'overdue'
                ? 'bg-rose-500/15 text-rose-600 border-rose-500/40 dark:text-rose-300'
                : 'bg-neutral-500/15 text-neutral-600 border-neutral-500/40 dark:text-neutral-300';

            var statusBgColor = statusName === 'returned'
                ? 'bg-emerald-500'
                : statusName === 'rented' || statusName === 'active'
                ? 'bg-sky-500'
                : statusName === 'overdue'
                ? 'bg-rose-500'
                : 'bg-neutral-500';

            // Normalize status label for display
            if (statusName === 'active') {
                statusLabel = 'Rented';
            }

            var customerName = [
                rental.customer && rental.customer.first_name,
                rental.customer && rental.customer.last_name
            ].filter(Boolean).join(' ') || (rental.customer && rental.customer.email) || 'N/A';

            var itemName = (rental.item && rental.item.name) || 'N/A';
            var itemSku = (rental.item && rental.item.sku) || '';

            var row = document.createElement('tr');
            row.className = 'border-b border-neutral-200 hover:bg-neutral-100 dark:border-neutral-900/60 dark:hover:bg-white/5 transition-colors duration-300 ease-in-out cursor-pointer';
            row.setAttribute('data-rental-id', rental.rental_id);
            row.addEventListener('click', function(e) {
                // Don't open details if clicking on action buttons
                if (e.target.closest('button')) {
                    return;
                }
                openRentalDetailsModal(rental.rental_id);
            });

            row.innerHTML = '' +
                '<td class="py-3.5 pr-4 pl-4 text-neutral-500 font-geist-mono">#' + (rental.rental_id || 'N/A') + '</td>' +
                '<td class="py-3.5 pr-4 text-neutral-900 dark:text-neutral-100">' + customerName + '</td>' +
                '<td class="py-3.5 pr-4">' +
                    '<div class="text-neutral-900 dark:text-neutral-100">' + itemName + '</div>' +
                    (itemSku ? '<div class="text-[11px] text-neutral-500 font-geist-mono">' + itemSku + '</div>' : '') +
                '</td>' +
                '<td class="py-3.5 pr-4 text-neutral-600 dark:text-neutral-300 font-geist-mono">' + formatDate(rental.released_date) + '</td>' +
                '<td class="py-3.5 pr-4 text-neutral-600 dark:text-neutral-300 font-geist-mono">' + formatDate(rental.due_date) + '</td>' +
                '<td class="py-3.5 pr-4"><span class="inline-flex items-center rounded-full ' + statusColor + ' px-2 py-1 text-[11px] font-medium border transition-colors duration-300 ease-in-out"><span class="mr-1.5 h-1.5 w-1.5 rounded-full ' + statusBgColor + '"></span>' + statusLabel + '</span></td>' +
                '<td class="py-3.5 pl-2 text-left text-neutral-500 dark:text-neutral-400">' +
                    '<div class="inline-flex items-center gap-2">' +
                        '<button type="button" onclick="openRentalDetailsModal(' + rental.rental_id + ')" class="rounded-lg p-1.5 hover:bg-violet-600 hover:text-white transition-colors duration-300 ease-in-out" aria-label="View rental details" title="View details">' +
                            '<x-icon name="eye" class="h-3.5 w-3.5" />' +
                        '</button>' +
                    '</div>' +
                '</td>';

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
        var tbody = document.getElementById('rentalTableBody');
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
        var tbody = document.getElementById('rentalTableBody');
        var emptyState = document.getElementById('emptyState');
        var paginationControls = document.getElementById('paginationControls');

        if (!tbody) {
            return;
        }

        var skeletonRows = Array.from({length: 5}, function() {
            return '<tr class="border-b border-neutral-200 dark:border-neutral-800 hover:bg-neutral-50 dark:hover:bg-neutral-900/50 transition-colors animate-pulse">' +
                '<td class="px-4 py-3"><div class="h-4 bg-neutral-300 dark:bg-neutral-700 rounded w-12"></div></td>' +
                '<td class="px-4 py-3"><div class="h-4 bg-neutral-300 dark:bg-neutral-700 rounded w-32"></div></td>' +
                '<td class="px-4 py-3"><div class="h-4 bg-neutral-300 dark:bg-neutral-700 rounded w-28"></div></td>' +
                '<td class="px-4 py-3"><div class="h-4 bg-neutral-300 dark:bg-neutral-700 rounded w-20"></div></td>' +
                '<td class="px-4 py-3"><div class="h-4 bg-neutral-300 dark:bg-neutral-700 rounded w-20"></div></td>' +
                '<td class="px-4 py-3"><div class="h-4 bg-neutral-300 dark:bg-neutral-700 rounded w-16"></div></td>' +
                '<td class="px-4 py-3"><div class="h-4 bg-neutral-300 dark:bg-neutral-700 rounded w-8"></div></td>' +
                '</tr>';
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
        var tbody = document.getElementById('rentalTableBody');
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
        if (rentalState.currentPage > 1) {
            rentalState.currentPage--;
            fetchRentals();
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
    }

    function nextPage() {
        if (rentalState.currentPage < rentalState.totalPages) {
            rentalState.currentPage++;
            fetchRentals();
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
    }

    // Placeholder functions for future modals
    function openRentalDetailsModal(rentalId) {
        console.log('Opening rental details modal for rental:', rentalId);
        // TODO: Implement rental details modal
    }

    function openProcessReturnModal() {
        console.log('Opening process return modal');
        // TODO: Implement process return modal
    }

    document.addEventListener('DOMContentLoaded', function() {
        initializeRentalPage();
    });
</script>
</body>
</html>
