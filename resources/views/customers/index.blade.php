<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Customer · Love &amp; Styles</title>

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
</head>
<body class="min-h-screen flex font-geist bg-neutral-100 text-neutral-900 dark:bg-black dark:text-neutral-50 transition-colors duration-300 ease-in-out">
    <x-sidebar />

    <main class="flex-1 flex flex-col px-10 py-8 overflow-x-hidden overflow-y-auto bg-gradient-to-b from-neutral-100 via-neutral-100 to-neutral-200 dark:from-black dark:via-black dark:to-neutral-950 transition-colors duration-300 ease-in-out">

        <header class="mb-8 transition-colors duration-300 ease-in-out">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-semibold tracking-tight text-neutral-900 dark:text-white transition-colors duration-300 ease-in-out">
                        Customers
                    </h1>
                    <p class="mt-1 text-sm font-geist-mono text-neutral-500 dark:text-neutral-400 transition-colors duration-300 ease-in-out">
                        View and manage customer profiles
                    </p>
                </div>

                <div class="flex items-center gap-3 text-xs">
                    <button class="inline-flex items-center gap-2 rounded-xl px-3.5 py-2 text-[14px] font-medium border dark:hover:text-black hover:text-white border-neutral-300 bg-white text-neutral-700 dark:hover:bg-violet-600 hover:bg-violet-600  dark:border-neutral-800 dark:bg-neutral-950/80 dark:text-neutral-200 dark:hover:bg-neutral-900  transition-colors duration-300 ease-in-out">
                        <x-icon name="chart-column" class="h-4 w-4" />
                        <span>Reports</span>
                    </button>

                    <button onclick="openAddCustomerModal()" class="inline-flex items-center gap-2 rounded-xl px-4 py-2 text-[14px] font-medium bg-violet-600 text-white dark:hover:text-white hover:text-black dark:text-black hover:bg-violet-500 shadow-violet-600/40 transition-colors duration-300 ease-in-out">
                        <x-icon name="plus" class="h-4 w-4" />
                        <span>Add Customer</span>
                    </button>
                </div>
            </div>
        </header>


         {{-- Stats --}}
         <section class="grid grid-cols-4 gap-6 mb-8">
             <div class="rounded-2xl p-6 border border-neutral-200 bg-white dark:border-neutral-900 dark:bg-neutral-950/60 shadow-sm dark:shadow-[0_18px_60px_rgba(0,0,0,0.65)] transition-colors duration-300 ease-in-out">
                 <div class="text-sm text-neutral-500 dark:text-neutral-400 mb-2 transition-colors duration-300 ease-in-out">
                     Total Customers
                 </div>
                 <div id="totalCustomersCount" class="text-3xl font-semibold text-neutral-900 dark:text-white transition-colors duration-300 ease-in-out">
                     0
                 </div>
             </div>
             <div class="rounded-2xl p-6 border border-neutral-200 bg-white dark:border-neutral-900 dark:bg-neutral-950/60 shadow-sm dark:shadow-[0_18px_60px_rgba(0,0,0,0.65)] transition-colors duration-300 ease-in-out">
                 <div class="text-sm text-neutral-500 dark:text-neutral-400 mb-2 transition-colors duration-300 ease-in-out">
                     Active Customers
                 </div>
                 <div id="activeCustomersCount" class="text-3xl font-semibold text-amber-500 transition-colors duration-300 ease-in-out">
                     0
                 </div>
             </div>
             <div class="rounded-2xl p-6 border border-neutral-200 bg-white dark:border-neutral-900 dark:bg-neutral-950/60 shadow-sm dark:shadow-[0_18px_60px_rgba(0,0,0,0.65)] transition-colors duration-300 ease-in-out">
                 <div class="text-sm text-neutral-500 dark:text-neutral-400 mb-2 transition-colors duration-300 ease-in-out">
                     Inactive Customers
                 </div>
                 <div id="inactiveCustomersCount" class="text-3xl font-semibold text-green-500 transition-colors duration-300 ease-in-out">
                     0
                 </div>
             </div>
             <div class="rounded-2xl p-6 border border-neutral-200 bg-white dark:border-neutral-900 dark:bg-neutral-950/60 shadow-sm dark:shadow-[0_18px_60px_rgba(0,0,0,0.65)] transition-colors duration-300 ease-in-out">
                 <div class="text-sm text-neutral-500 dark:text-neutral-400 mb-2 transition-colors duration-300 ease-in-out">
                     Customers with Rentals
                 </div>
                 <div id="customersWithRentalsCount" class="text-3xl font-semibold text-violet-600 transition-colors duration-300 ease-in-out">
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
                            <div class="flex items-center gap-3 rounded-2xl bg-white px-4 py-2.5 border border-neutral-300 focus-within:border-neutral-500 dark:border-neutral-800 dark:bg-black/60 transition-colors duration-300 ease-in-out">
                                <x-icon name="search" class="h-4 w-4 text-neutral-500 transition-colors duration-300 ease-in-out" />
                                <input id="searchInput" type="text" placeholder="Search by customer, item, or ID..." class="w-full bg-transparent text-xs text-neutral-700 placeholder:text-neutral-400 dark:text-neutral-100 dark:placeholder:text-neutral-500 focus:outline-none transition-colors duration-300 ease-in-out">
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
                                        <li class="px-4 py-2 hover:bg-neutral-100 dark:hover:bg-neutral-900 cursor-pointer transition-colors duration-200">Active</li>
                                        <li class="px-4 py-2 hover:bg-neutral-100 dark:hover:bg-neutral-900 cursor-pointer transition-colors duration-200">Inactive</li>
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
                                <th class="py-2.5 pr-4 font-medium cursor-pointer hover:text-neutral-700 dark:hover:text-neutral-300 transition-colors" onclick="toggleSort('first_name')">
                                    <div class="flex items-center gap-1.5">
                                        <span>Name</span>
                                        <span class="sort-indicator text-[10px]"></span>
                                    </div>
                                </th>
                                <th class="py-2.5 pr-4 font-medium cursor-pointer hover:text-neutral-700 dark:hover:text-neutral-300 transition-colors" onclick="toggleSort('email')">
                                    <div class="flex items-center gap-1.5">
                                        <span>Email</span>
                                        <span class="sort-indicator text-[10px]"></span>
                                    </div>
                                </th>
                                <th class="py-2.5 pr-4 font-medium cursor-pointer hover:text-neutral-700 dark:hover:text-neutral-300 transition-colors" onclick="toggleSort('contact_number')">
                                    <div class="flex items-center gap-1.5">
                                        <span>Phone</span>
                                        <span class="sort-indicator text-[10px]"></span>
                                    </div>
                                </th>
                                <th class="py-2.5 pr-4 font-medium">Address</th>
                                <th class="py-2.5 pr-4 font-medium cursor-pointer hover:text-neutral-700 dark:hover:text-neutral-300 transition-colors" onclick="toggleSort('created_at')">
                                    <div class="flex items-center gap-1.5">
                                        <span>Created Date</span>
                                        <span class="sort-indicator text-[10px]"></span>
                                    </div>
                                </th>
                                <th class="py-2.5 pr-4 font-medium cursor-pointer hover:text-neutral-700 dark:hover:text-neutral-300 transition-colors" onclick="toggleSort('rentals_count')">
                                    <div class="flex items-center gap-1.5">
                                        <span>Total Rentals</span>
                                        <span class="sort-indicator text-[10px]"></span>
                                    </div>
                                </th>
                                <th class="py-2.5 pr-4 font-medium text-left">Status</th>
                                <th class="py-2.5 pl-2 font-medium text-left">Actions</th>
                            </tr>
                            </thead>

                            <tbody id="customersTableBody" class="text-[13px]">
                            <!-- Rows will be dynamically inserted here -->
                            </tbody>
                        </table>
                    </div>

                    <!-- Empty state -->
                    <div id="emptyState" class="text-center py-12">
                        <p class="text-neutral-500 dark:text-neutral-400">No customers found</p>
                    </div>
                </div>

                <!-- Pagination Controls -->
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


<script>
    // Global state for customer data
    let customerState = {
        currentPage: 1,
        perPage: 15,
        searchQuery: '',
        statusFilter: '',
        sortBy: 'created_at',
        sortOrder: 'desc',
        totalPages: 1,
        totalCount: 0,
        isLoading: false,
        allCustomers: [],
        // Stats tracking (always shows total, not filtered)
        totalCustomersCount: 0,
        activeCustomersCount: 0,
        inactiveCustomersCount: 0,
        customersWithRentalsCount: 0
    };

    // Debounce timer for search
    let searchDebounceTimer;

    // Initialize customer page
    function initializeCustomerPage() {
        const searchInput = document.getElementById('searchInput');
        const filterMenu = document.getElementById('filter-menu');
        const filterButtonText = document.getElementById('filter-button-text');

        // Guard against missing elements
        if (!searchInput || !filterMenu) {
            return;
        }

        // Reset state when page is loaded
        customerState.currentPage = 1;
        customerState.searchQuery = '';
        customerState.statusFilter = '';

        // Load stats and customers
        fetchStats();
        fetchCustomers();

        // Search with debounce
        searchInput.addEventListener('input', function(e) {
            clearTimeout(searchDebounceTimer);
            customerState.searchQuery = e.target.value;
            customerState.currentPage = 1;

            searchDebounceTimer = setTimeout(() => {
                fetchCustomers();
            }, 300);
        });

        // Filter by status
        filterMenu.querySelectorAll('li').forEach(item => {
            item.addEventListener('click', function(e) {
                const statusText = item.textContent.trim();
                filterButtonText.textContent = statusText;

                if (statusText === 'All Status') {
                    customerState.statusFilter = '';
                } else if (statusText === 'Active') {
                    customerState.statusFilter = '1'; // Active status ID
                } else if (statusText === 'Inactive') {
                    customerState.statusFilter = '2'; // Inactive status ID
                }

                customerState.currentPage = 1;
                fetchCustomers();
            });
        });

        // Initialize filter dropdown
        initializeFilterDropdown();
    }

    // Initialize filter dropdown
    function initializeFilterDropdown() {
        const filterButton = document.getElementById('filter-button');
        const filterMenu = document.getElementById('filter-menu');
        const iconDown = document.getElementById('icon-down');
        const iconUp = document.getElementById('icon-up');

        if (!filterButton || !filterMenu) {
            return;
        }

        let isOpen = false;

        filterButton.addEventListener('click', (e) => {
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

        document.addEventListener('click', () => {
            if (isOpen) {
                isOpen = false;
                filterMenu.classList.add('opacity-0', 'scale-95', 'pointer-events-none');
                filterMenu.classList.remove('opacity-100', 'scale-100', 'pointer-events-auto');
                iconDown.classList.remove('hidden');
                iconUp.classList.add('hidden');
            }
        });
    }

    // Listen to both DOMContentLoaded and Turbo events
    document.addEventListener('DOMContentLoaded', initializeCustomerPage);
    document.addEventListener('turbo:load', initializeCustomerPage);
    document.addEventListener('turbo:render', initializeCustomerPage);

    // Fetch total stats (always gets unfiltered counts)
    async function fetchStats() {
        try {
            const response = await axios.get('/api/customers/stats');
            const data = response.data;

            // Store total counts regardless of current filters
            customerState.totalCustomersCount = data.total_customers || 0;
            customerState.activeCustomersCount = data.active_customers || 0;
            customerState.inactiveCustomersCount = data.inactive_customers || 0;
            customerState.customersWithRentalsCount = data.customers_with_rentals || 0;

            // Update KPI displays
            document.getElementById('totalCustomersCount').textContent = customerState.totalCustomersCount;
            document.getElementById('activeCustomersCount').textContent = customerState.activeCustomersCount;
            document.getElementById('inactiveCustomersCount').textContent = customerState.inactiveCustomersCount;
            document.getElementById('customersWithRentalsCount').textContent = customerState.customersWithRentalsCount;

        } catch (error) {
            console.error('Error fetching stats:', error);
        }
    }

    // Fetch customers from API
    async function fetchCustomers() {
        if (customerState.isLoading) return;

        customerState.isLoading = true;
        showLoadingState();

        try {
            const params = new URLSearchParams({
                page: customerState.currentPage,
                per_page: customerState.perPage,
                include_history: 'true',
                sort_by: customerState.sortBy,
                sort_order: customerState.sortOrder
            });

            if (customerState.searchQuery) {
                params.append('search', customerState.searchQuery);
            }

            if (customerState.statusFilter) {
                params.append('status_id', customerState.statusFilter);
            }

            const url = `/api/customers?${params.toString()}`;
            console.log('Fetching customers from:', url);

            const response = await axios.get(url);
            const data = response.data;

            console.log('API Response:', data);

            if (!data.data || !Array.isArray(data.data)) {
                console.warn('Unexpected response format:', data);
                showEmptyState('No customers found.');
                hideLoadingState();
                customerState.isLoading = false;
                return;
            }

            customerState.totalPages = data.last_page;
            customerState.totalCount = data.total;
            customerState.allCustomers = data.data;

            renderTable(data.data);
            updatePagination(data);
            updateStats();
            updateSortIndicators();
            hideLoadingState();

        } catch (error) {
            console.error('Error fetching customers:', error);
            console.error('Error status:', error.response?.status);
            console.error('Error data:', error.response?.data);

            const errorMessage = error.response?.data?.message || error.message || 'Failed to load customers. Please try again.';
            showEmptyState(errorMessage);
            hideLoadingState();
        } finally {
            customerState.isLoading = false;
        }
    }

    // Render table rows
    function renderTable(customers) {
        try {
            const tbody = document.getElementById('customersTableBody');
            tbody.innerHTML = '';

            if (customers.length === 0) {
                // Show custom message based on search or filter
                let emptyMessage = 'No customers found';
                
                if (customerState.searchQuery) {
                    emptyMessage = `No matches found for "${customerState.searchQuery}"`;
                } else if (customerState.statusFilter) {
                    emptyMessage = 'No customers with this status';
                }

                showEmptyState(emptyMessage);
                return;
            }

             customers.forEach(customer => {
                const statusColor = customer.status?.status_name === 'active'
                    ? 'bg-emerald-500/15 text-emerald-600 border-emerald-500/40 dark:text-emerald-300'
                    : 'bg-red-500/15 text-red-600 border-red-500/40 dark:text-red-300';

                const statusBgColor = customer.status?.status_name === 'active'
                    ? 'bg-emerald-500'
                    : 'bg-red-500';

                // Format created date
                const createdDate = new Date(customer.created_at);
                const formattedDate = createdDate.toLocaleDateString('en-US', {
                    year: 'numeric',
                    month: 'short',
                    day: 'numeric'
                });

                const row = document.createElement('tr');
                row.className = 'border-b border-neutral-200 hover:bg-neutral-100 dark:border-neutral-900/60 dark:hover:bg-white/5 transition-colors duration-300 ease-in-out';
                row.innerHTML = `
                    <td class="py-3.5 pr-4 pl-4 text-neutral-500 font-geist-mono">#${String(customer.customer_id).padStart(3, '0')}</td>
                    <td class="py-3.5 pr-4 text-neutral-900 dark:text-neutral-100">${customer.first_name} ${customer.last_name}</td>
                    <td class="py-3.5 pr-4 text-neutral-600 dark:text-neutral-300">${customer.email}</td>
                    <td class="py-3.5 pr-4 text-neutral-600 dark:text-neutral-300 font-geist-mono">${customer.contact_number}</td>
                    <td class="py-3.5 pr-2 text-neutral-600 dark:text-neutral-300 font-geist-mono">${customer.address}</td>
                    <td class="py-3.5 pr-4 text-neutral-600 dark:text-neutral-300 font-geist-mono text-xs">${formattedDate}</td>
                    <td class="py-3.5 pr-4 text-left text-neutral-900 dark:text-neutral-100 font-geist-mono">${customer.rentals_count || 0}</td>
                    <td class="py-3.5 pr-2">
                        <span class="inline-flex items-center rounded-full ${statusColor} px-2 py-1 text-[11px] font-medium border transition-colors duration-300 ease-in-out">
                            <span class="mr-1.5 h-1.5 w-1.5 rounded-full ${statusBgColor}"></span>
                            ${customer.status?.status_name || 'Unknown'}
                        </span>
                    </td>
                    <td class="py-3.5 pl-2 text-left text-neutral-500 dark:text-neutral-400">
                        <div class="inline-flex items-center gap-2">
                            <button class="edit-customer-btn rounded-lg p-1.5 hover:bg-violet-600 hover:text-white transition-colors duration-300 ease-in-out" aria-label="Edit" title="Edit customer" data-customer-id="${customer.customer_id}">
                                <x-icon name="edit" class="h-3.5 w-3.5" />
                            </button>
                            <button class="change-status-btn rounded-lg p-1.5 text-amber-600 hover:bg-amber-500/15 hover:text-amber-500 transition-colors duration-300 ease-in-out dark:text-amber-500 dark:hover:bg-amber-900/25" aria-label="Change Status" title="Change customer status" data-customer-id="${customer.customer_id}">
                                <x-icon name="archive" class="h-3.5 w-3.5" />
                            </button>
                        </div>
                    </td>
                `;
                tbody.appendChild(row);
            });

            // Attach event listeners to edit and change status buttons
            document.querySelectorAll('.edit-customer-btn').forEach(btn => {
                btn.addEventListener('click', (e) => {
                    e.preventDefault();
                    const customerId = btn.getAttribute('data-customer-id');
                    openEditCustomerModal(customerId);
                });
            });

            document.querySelectorAll('.change-status-btn').forEach(btn => {
                btn.addEventListener('click', (e) => {
                    e.preventDefault();
                    const customerId = btn.getAttribute('data-customer-id');
                    openChangeStatusModal(customerId);
                });
            });

            document.getElementById('customersTableBody').style.opacity = '1';
        } catch (error) {
            console.error('Error rendering table:', error);
            showEmptyState('Error rendering customers table');
        }
    }

    // Update pagination controls
    function updatePagination(data) {
        const prevBtn = document.getElementById('prevBtn');
        const nextBtn = document.getElementById('nextBtn');
        const pageInfo = document.getElementById('pageInfo');
        const pageStart = document.getElementById('pageStart');
        const pageEnd = document.getElementById('pageEnd');
        const pageTotal = document.getElementById('pageTotal');

        prevBtn.disabled = !data.links[0].url;
        nextBtn.disabled = !data.links[data.links.length - 1].url;

        pageInfo.textContent = `Page ${data.current_page} of ${data.last_page}`;
        pageStart.textContent = (data.from || 0);
        pageEnd.textContent = (data.to || 0);
        pageTotal.textContent = data.total;

        document.getElementById('paginationControls').style.display = 'flex';
    }

    // Update stats
    function updateStats() {
        // Stats are now fetched separately in fetchStats()
        // This keeps the KPI always showing total counts regardless of search/filter
        // Do nothing here - stats are already updated by fetchStats()
    }

    // Show empty state
    function showEmptyState(message) {
        document.getElementById('customersTableBody').innerHTML = '';
        document.getElementById('emptyState').textContent = message;
        document.getElementById('emptyState').style.display = 'block';
        document.getElementById('paginationControls').style.display = 'none';
    }

    // Hide empty state
    function hideEmptyState() {
        document.getElementById('emptyState').style.display = 'none';
    }

    // Show loading state
    function showLoadingState() {
        const tbody = document.getElementById('customersTableBody');
        tbody.style.opacity = '0.6';
    }

    // Hide loading state
    function hideLoadingState() {
        hideEmptyState();
    }

    // Pagination handlers
    function previousPage() {
        if (customerState.currentPage > 1) {
            customerState.currentPage--;
            fetchCustomers();
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
    }

    function nextPage() {
        if (customerState.currentPage < customerState.totalPages) {
            customerState.currentPage++;
            fetchCustomers();
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
    }

    // Open change status modal
    async function openChangeStatusModal(customerId) {
        try {
            // Fetch customer data to get current status
            const response = await axios.get(`/api/customers/${customerId}`);
            const customer = response.data.data;
            const newStatus = customer.status_id === 1 ? 2 : 1;
            const statusName = newStatus === 1 ? 'Active' : 'Inactive';
            
             // Store customer info for later use
             window.pendingStatusChange = {
                 customerId: customerId,
                 currentStatus: customer.status_id,
                 newStatus: newStatus,
                 customerName: `${customer.first_name} ${customer.last_name}`
             };

             // Require password confirmation for both deactivate and reactivate
             showPasswordConfirmationModal(statusName, newStatus);
        } catch (error) {
            console.error('Error fetching customer for status change:', error);
            alert('Failed to load customer data');
        }
    }

    // Show password confirmation modal (defined in edit modal include)
    // This function is called from openChangeStatusModal
    // The modal is already defined in edit-customer-modal.blade.php

    // Change customer status (from edit modal)
    // This function is called after password verification

    async function deleteCustomer(customerId) {
        if (confirm('Are you sure you want to delete this customer? This action cannot be undone.')) {
            try {
                await axios.delete(`/api/customers/${customerId}`);
                fetchCustomers();
                fetchStats();
            } catch (error) {
                console.error('Error deleting customer:', error);
                alert('Failed to delete customer. ' + (error.response?.data?.message || error.message));
            }
        }
    }

    // Toggle sort on column header click
    function toggleSort(column) {
        customerState.currentPage = 1;
        
        if (customerState.sortBy === column) {
            // Toggle sort order if same column clicked
            customerState.sortOrder = customerState.sortOrder === 'asc' ? 'desc' : 'asc';
        } else {
            // Set new column and default to ascending
            customerState.sortBy = column;
            customerState.sortOrder = 'asc';
        }
        
        fetchCustomers();
    }

    // Update sort indicators on headers
    function updateSortIndicators() {
        // Clear all indicators
        document.querySelectorAll('.sort-indicator').forEach(indicator => {
            indicator.textContent = '';
        });

        // Find the column header that matches current sort
        const headers = document.querySelectorAll('thead th');
        headers.forEach(header => {
            const btn = header.querySelector('[onclick]');
            if (!btn) return;

            const onclickAttr = btn.getAttribute('onclick');
            if (!onclickAttr) return;

            const columnMatch = onclickAttr.match(/toggleSort\('(\w+)'\)/);
            if (!columnMatch) return;

            const column = columnMatch[1];
            if (column === customerState.sortBy) {
                const indicator = header.querySelector('.sort-indicator');
                if (indicator) {
                    indicator.textContent = customerState.sortOrder === 'asc' ? '↑' : '↓';
                    indicator.style.fontWeight = '600';
                }
            }
        });
    }
</script>

{{-- Include Add Customer Modal --}}
@include('customers.partials.add-customer-modal')

{{-- Include Edit Customer Modal --}}
@include('customers.partials.edit-customer-modal')

</body>
</html>
