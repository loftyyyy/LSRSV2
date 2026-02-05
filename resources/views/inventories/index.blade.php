<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Inventory · Love &amp; Styles</title>

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

<main class="flex-1 ml-64 flex flex-col px-10 py-8 overflow-x-hidden overflow-y-auto bg-gradient-to-b from-neutral-100 via-neutral-100 to-neutral-200 dark:from-black dark:via-black dark:to-neutral-950 transition-colors duration-300 ease-in-out">

    <header class="mb-8 transition-colors duration-300 ease-in-out">
        <div class="flex items-center justify-between gap-4">
            <div>
                <h1 class="text-3xl font-semibold tracking-tight text-neutral-900 dark:text-white transition-colors duration-300 ease-in-out">
                    Inventory Management
                </h1>
                <p class="mt-1 text-sm font-geist-mono text-neutral-500 dark:text-neutral-400 transition-colors duration-300 ease-in-out">
                    Manage rental items and stock levels
                </p>
            </div>

            <div class="flex items-center gap-3 text-xs">
                <a href="/inventories/reports" class="inline-flex items-center gap-2 rounded-xl px-3.5 py-2 text-[14px] font-medium border dark:hover:text-black hover:text-white border-neutral-300 bg-white text-neutral-700 dark:hover:bg-violet-600 hover:bg-violet-600  dark:border-neutral-800 dark:bg-neutral-950/80 dark:text-neutral-200 dark:hover:bg-neutral-900  transition-colors duration-300 ease-in-out">
                    <x-icon name="chart-column" class="h-4 w-4" />
                    <span>Reports</span>
                </a>

                <button onclick="openAddItemModal()" class="inline-flex items-center gap-2 rounded-xl px-4 py-2 text-[14px] font-medium bg-violet-600 text-white dark:hover:text-white hover:text-black dark:text-black hover:bg-violet-500 shadow-violet-600/40 transition-colors duration-300 ease-in-out">
                    <x-icon name="plus" class="h-4 w-4" />
                    <span>Add Item</span>
                </button>
            </div>
        </div>
    </header>


    {{-- Stats --}}
    <section class="grid grid-cols-4 gap-6 mb-8">
        <div class="rounded-2xl p-6 border border-neutral-200 bg-white dark:border-neutral-900 dark:bg-neutral-950/60 shadow-sm dark:shadow-[0_18px_60px_rgba(0,0,0,0.65)] transition-colors duration-300 ease-in-out">
            <div class="text-sm text-neutral-500 dark:text-neutral-400 mb-2 transition-colors duration-300 ease-in-out">
                Total Items
            </div>
            <div id="totalItemsCount" class="text-3xl font-semibold text-neutral-900 dark:text-white transition-colors duration-300 ease-in-out">
                0
            </div>
        </div>
        <div class="rounded-2xl p-6 border border-neutral-200 bg-white dark:border-neutral-900 dark:bg-neutral-950/60 shadow-sm dark:shadow-[0_18px_60px_rgba(0,0,0,0.65)] transition-colors duration-300 ease-in-out">
            <div class="text-sm text-neutral-500 dark:text-neutral-400 mb-2 transition-colors duration-300 ease-in-out">
                Available
            </div>
            <div id="availableItemsCount" class="text-3xl font-semibold text-amber-500 transition-colors duration-300 ease-in-out">
                0
            </div>
        </div>
        <div class="rounded-2xl p-6 border border-neutral-200 bg-white dark:border-neutral-900 dark:bg-neutral-950/60 shadow-sm dark:shadow-[0_18px_60px_rgba(0,0,0,0.65)] transition-colors duration-300 ease-in-out">
            <div class="text-sm text-neutral-500 dark:text-neutral-400 mb-2 transition-colors duration-300 ease-in-out">
                Maintenance
            </div>
            <div id="underRepairItemsCount" class="text-3xl font-semibold text-green-500 transition-colors duration-300 ease-in-out">
                0
            </div>
        </div>
        <div class="rounded-2xl p-6 border border-neutral-200 bg-white dark:border-neutral-900 dark:bg-neutral-950/60 shadow-sm dark:shadow-[0_18px_60px_rgba(0,0,0,0.65)] transition-colors duration-300 ease-in-out">
            <div class="text-sm text-neutral-500 dark:text-neutral-400 mb-2 transition-colors duration-300 ease-in-out">
                Inventory Value
            </div>
            <div id="inventoryValueCount" class="text-3xl font-semibold text-violet-600 transition-colors duration-300 ease-in-out">
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
                                 <input id="searchInput" type="text" placeholder="Search by item name, SKU, or ID..." class="w-full bg-transparent text-xs text-neutral-700 placeholder:text-neutral-400 dark:text-neutral-100 dark:placeholder:text-neutral-500 focus:outline-none transition-colors duration-300 ease-in-out">
                             </div>
                            <div id="searchIndicators" class="mt-2 flex flex-wrap gap-1.5 px-0"></div>
                        </div>
                    </div>

                    <div class="flex items-center gap-3">
                        <!-- Custom Filter Dropdown -->
                        <div class="relative mb-2" id="filter-dropdown">
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
                                    <li class="px-4 py-2 hover:bg-neutral-100 dark:hover:bg-neutral-900 cursor-pointer transition-colors duration-200">Available</li>
                                    <li class="px-4 py-2 hover:bg-neutral-100 dark:hover:bg-neutral-900 cursor-pointer transition-colors duration-200">Rented</li>
                                    <li class="px-4 py-2 hover:bg-neutral-100 dark:hover:bg-neutral-900 cursor-pointer transition-colors duration-200">Maintenance</li>
                                    <li class="px-4 py-2 hover:bg-neutral-100 dark:hover:bg-neutral-900 cursor-pointer transition-colors duration-200">Retired</li>
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
                            <th class="py-2.5 pr-4 pl-4 font-medium">Item</th>
                            <th class="py-2.5 pr-4 font-medium">Type</th>
                            <th class="py-2.5 pr-4 font-medium">SKU</th>
                            <th class="py-2.5 pr-4 font-medium">Size</th>
                            <th class="py-2.5 pr-4 font-medium">Price</th>
                            <th class="py-2.5 pr-4 font-medium text-left">Status</th>
                            <th class="py-2.5 pl-2 font-medium text-left">Actions</th>
                        </tr>
                        </thead>

                        <tbody id="inventoryTableBody" class="text-[13px]">
                        <!-- Rows will be dynamically inserted here -->
                        </tbody>
                    </table>
                </div>

                <!-- Empty state -->
                <div id="emptyState" class="text-center py-12">
                    <p class="text-neutral-500 dark:text-neutral-400">No items found</p>
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
    // Page state
    var inventoryState = {
        currentPage: 1,
        perPage: 15,
        searchQuery: '',
        statusFilter: '',
        sortBy: 'created_at',
        sortOrder: 'desc',
        totalPages: 1,
        totalCount: 0,
        isLoading: false,
        allItems: [],
        // Stats tracking (always shows total, not filtered)
        totalItemsCount: 0,
        availableItemsCount: 0,
        underRepairItemsCount: 0,
        inventoryValueCount: 0,
        // Dynamic status mappings
        statuses: {},
        // Request cancellation
        abortController: null
    };

    // Debounce timer for search
    var searchDebounceTimer;

    // Initialize inventory page
    function initializeInventoryPage() {
        var searchInput = document.getElementById('searchInput');
        var filterMenu = document.getElementById('filter-menu');
        var filterButtonText = document.getElementById('filter-button-text');

        // Create abort controller for API requests
        inventoryState.abortController = new AbortController();

        // Load statuses first, then stats and items
        fetchStatuses().then(() => {
            fetchStats();
            fetchInventoryItems();
        });

        // Search with debounce
        searchInput.addEventListener('input', function(e) {
            clearTimeout(searchDebounceTimer);
            inventoryState.searchQuery = e.target.value;
            inventoryState.currentPage = 1;

            // Update search indicators immediately
            updateSearchIndicators();

            searchDebounceTimer = setTimeout(() => {
                fetchInventoryItems();
            }, 300);
        });

        // Filter by status
        filterMenu.querySelectorAll('li').forEach(item => {
            item.addEventListener('click', function(e) {
                var statusText = item.textContent.trim();
                filterButtonText.textContent = statusText;

                if (statusText === 'All Status') {
                    inventoryState.statusFilter = '';
                } else {
                    // Convert "Available" -> "available", "Maintenance" -> "maintenance", etc.
                    inventoryState.statusFilter = statusText.toLowerCase().replace(' ', '_');
                }

                inventoryState.currentPage = 1;
                fetchInventoryItems();
            });
        });

        // Initialize filter dropdown
        initializeFilterDropdown();
    }

    // Initialize filter dropdown
    function initializeFilterDropdown() {
        var filterButton = document.getElementById('filter-button');
        var filterMenu = document.getElementById('filter-menu');
        var iconDown = document.getElementById('icon-down');
        var iconUp = document.getElementById('icon-up');

        if (!filterButton || !filterMenu) {
            return;
        }

        var isOpen = false;

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

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', initializeInventoryPage);

    // Fetch inventory statuses (dynamically populate status ID mappings)
    async function fetchStatuses() {
        try {
            var response = await axios.get('/api/inventories/statuses');
            var statuses = response.data.statuses || response.data || [];

            // Build mappings for status names to IDs
            if (Array.isArray(statuses)) {
                statuses.forEach(status => {
                    inventoryState.statuses[status.status_id] = status.status_name;
                });
            }
        } catch (error) {
            console.error('Error fetching statuses:', error);
            // Continue anyway - statuses are not critical for display
        }
    }

    // Fetch total stats (always gets unfiltered counts)
    async function fetchStats() {
        try {
            // Ensure abortController exists
            if (!inventoryState.abortController) {
                inventoryState.abortController = new AbortController();
            }
            
            var response = await axios.get('/api/inventories/reports/statistics', {
                signal: inventoryState.abortController.signal
            });
            var data = response.data;

            // Store total counts regardless of current filters
            // Map the API response fields to frontend state
            inventoryState.totalItemsCount = data.total_items || 0;
            inventoryState.availableItemsCount = data.available_items || 0;
            inventoryState.underRepairItemsCount = data.under_maintenance || 0;
            // Note: inventory_value is not provided by this endpoint, we'll fetch it separately if needed
            inventoryState.inventoryValueCount = data.inventory_value || 0;

             // Update KPI displays
             var totalCountEl = document.getElementById('totalItemsCount');
             var availableCountEl = document.getElementById('availableItemsCount');
             var underRepairCountEl = document.getElementById('underRepairItemsCount');
             var valueCountEl = document.getElementById('inventoryValueCount');

             if (totalCountEl) totalCountEl.textContent = inventoryState.totalItemsCount;
             if (availableCountEl) availableCountEl.textContent = inventoryState.availableItemsCount;
             if (underRepairCountEl) underRepairCountEl.textContent = inventoryState.underRepairItemsCount;
             if (valueCountEl) valueCountEl.textContent = '₱' + (inventoryState.inventoryValueCount || 0).toLocaleString();

         } catch (error) {
             // Don't show error if request was cancelled (user navigated away)
             if (error.name === 'AbortError' || error.code === 'ECONNABORTED' || error.code === 'ERR_CANCELED') {
                 console.log('Stats request cancelled (user navigated away)');
                 return;
             }
             console.error('Error fetching stats:', error);
             // Don't show notification for stats errors - only log them
         }
     }

     // Fetch inventory items from API
     async function fetchInventoryItems() {
         if (inventoryState.isLoading) return;

         inventoryState.isLoading = true;
         showLoadingState();

         try {
             // Ensure abortController exists
             if (!inventoryState.abortController) {
                 inventoryState.abortController = new AbortController();
             }
             
             var params = new URLSearchParams({
                 page: inventoryState.currentPage,
                 per_page: inventoryState.perPage,
                 sort_by: inventoryState.sortBy,
                 sort_order: inventoryState.sortOrder
             });

             if (inventoryState.searchQuery) {
                 params.append('search', inventoryState.searchQuery);
             }

             if (inventoryState.statusFilter) {
                 params.append('status', inventoryState.statusFilter);
             }

             var url = `/api/inventories?${params.toString()}`;

             var response = await axios.get(url, {
                 signal: inventoryState.abortController.signal
             });
             var data = response.data;

             if (!data.data || !Array.isArray(data.data)) {
                 showEmptyState('No items found.');
                 hideLoadingState();
                 inventoryState.isLoading = false;
                 return;
             }

             inventoryState.totalPages = data.last_page;
             inventoryState.totalCount = data.total;
             inventoryState.allItems = data.data;

             renderTable(data.data);
             updatePagination(data);
             hideLoadingState();

          } catch (error) {
             // Don't show error if request was cancelled (user navigated away)
             if (error.name === 'AbortError' || error.code === 'ECONNABORTED' || error.code === 'ERR_CANCELED') {
                 console.log('Items request cancelled (user navigated away)');
                 inventoryState.isLoading = false;
                 hideLoadingState();
                 return;
             }

             console.error('Error fetching items:', error);
             console.error('Error status:', error.response?.status);
             console.error('Error data:', error.response?.data);

             var errorMessage = error.response?.data?.message || error.message || 'Failed to load items. Please try again.';
             showErrorNotification(errorMessage);
             showEmptyState(errorMessage);
             hideLoadingState();
         } finally {
             inventoryState.isLoading = false;
         }
      }

      // Update search indicators
      function updateSearchIndicators() {
          var searchIndicatorsDiv = document.getElementById('searchIndicators');
          
          // Guard against missing element
          if (!searchIndicatorsDiv) {
              return;
          }

          var query = inventoryState.searchQuery.trim().toLowerCase();

          if (!query) {
              searchIndicatorsDiv.innerHTML = '';
              return;
          }

          var indicators = [];

          // Determine what fields match
          if (!isNaN(query) && query !== '') {
              // Numeric search - matches item ID
              indicators.push('Item ID');
          }

          // Text search - could match name or SKU
          indicators.push('Item Name');
          indicators.push('SKU');

         // Build the HTML for indicators
         let html = '';
         indicators.forEach(indicator => {
             html += `<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-medium bg-violet-100 text-violet-700 dark:bg-violet-900/40 dark:text-violet-300 border border-violet-200 dark:border-violet-800/60">${indicator}</span>`;
         });

         searchIndicatorsDiv.innerHTML = html;
     }

       // Render table rows
       function renderTable(items) {
          console.log('[Inventory] renderTable() called with', items?.length || 0, 'items');
          if (items && items.length > 0) {
              console.log('[Inventory] First item:', items[0]?.name, items[0]?.sku);
          }
          
          try {
              var tbody = document.getElementById('inventoryTableBody');
              
              // Guard against missing tbody element (can happen during Turbo navigation)
              if (!tbody) {
                  console.warn('[Inventory] inventoryTableBody element not found');
                  return;
              }

              tbody.innerHTML = '';

              if (items.length === 0) {
                 // Show custom message based on search or filter
                 let emptyMessage = 'No items found';

                 if (inventoryState.searchQuery) {
                     emptyMessage = `No matches found for "${inventoryState.searchQuery}"`;
                 } else if (inventoryState.statusFilter) {
                     emptyMessage = 'No items with this status';
                 }

                 showEmptyState(emptyMessage);
                 return;
             }

              items.forEach(item => {
                 // Get status name from the status relationship
                 var statusName = item.status?.status_name || 'unknown';
                 
                 var statusColor = statusName === 'available'
                     ? 'bg-emerald-500/15 text-emerald-600 border-emerald-500/40 dark:text-emerald-300'
                     : statusName === 'rented'
                     ? 'bg-blue-500/15 text-blue-600 border-blue-500/40 dark:text-blue-300'
                     : statusName === 'maintenance'
                     ? 'bg-amber-500/15 text-amber-600 border-amber-500/40 dark:text-amber-300'
                     : 'bg-gray-500/15 text-gray-600 border-gray-500/40 dark:text-gray-300';

                 var statusBgColor = statusName === 'available'
                     ? 'bg-emerald-500'
                     : statusName === 'rented'
                     ? 'bg-blue-500'
                     : statusName === 'maintenance'
                     ? 'bg-amber-500'
                     : 'bg-gray-500';

                 var statusLabel = statusName === 'available'
                     ? 'Available'
                     : statusName === 'rented'
                     ? 'Rented'
                     : statusName === 'maintenance'
                     ? 'Maintenance'
                     : 'Retired';

                 // Format item type (capitalize first letter)
                 var itemType = item.item_type ? item.item_type.charAt(0).toUpperCase() + item.item_type.slice(1) : 'N/A';

                 var row = document.createElement('tr');
                 row.className = 'border-b border-neutral-200 hover:bg-neutral-100 dark:border-neutral-900/60 dark:hover:bg-white/5 transition-colors duration-300 ease-in-out cursor-pointer';
                 row.setAttribute('data-item-id', item.item_id);
                 row.innerHTML = `
                     <td class="py-3.5 pr-4 pl-4 text-neutral-500 font-geist-mono">${item.name || 'N/A'}</td>
                     <td class="py-3.5 pr-4 text-neutral-900 dark:text-neutral-100">${itemType}</td>
                     <td class="py-3.5 pr-4 text-neutral-900 dark:text-neutral-100">${item.sku || 'N/A'}</td>
                     <td class="py-3.5 pr-4 text-neutral-600 dark:text-neutral-300">${item.size || 'N/A'}</td>
                     <td class="py-3.5 pr-2 text-neutral-600 dark:text-neutral-300 font-geist-mono">₱${(item.rental_price || 0).toLocaleString()}</td>
                     <td class="py-3.5 pr-2">
                         <span class="inline-flex items-center rounded-full ${statusColor} px-2 py-1 text-[11px] font-medium border transition-colors duration-300 ease-in-out">
                             <span class="mr-1.5 h-1.5 w-1.5 rounded-full ${statusBgColor}"></span>
                             ${statusLabel}
                         </span>
                     </td>
                     <td class="py-3.5 pl-2 text-left text-neutral-500 dark:text-neutral-400" onclick="event.stopPropagation()">
                         <div class="inline-flex items-center gap-2">
                             <button class="edit-item-btn rounded-lg p-1.5 hover:bg-violet-600 hover:text-white transition-colors duration-300 ease-in-out" aria-label="Edit" title="Edit item" data-item-id="${item.item_id}">
                                 <x-icon name="edit" class="h-3.5 w-3.5" />
                             </button>
                             <button class="change-status-btn rounded-lg p-1.5 text-amber-600 hover:bg-amber-500/15 hover:text-amber-500 transition-colors duration-300 ease-in-out dark:text-amber-500 dark:hover:bg-amber-900/25" aria-label="Set Status" title="Set maintenance or retire" data-item-id="${item.item_id}">
                                 <x-icon name="archive" class="h-3.5 w-3.5" />
                             </button>
                         </div>
                     </td>
                 `;
                 
                 // Add click handler for row to open details modal
                 row.addEventListener('click', function() {
                     openItemDetailsModal(item.item_id);
                 });
                 
                 tbody.appendChild(row);
             });

            // Attach event listeners to edit and change status buttons
            document.querySelectorAll('.edit-item-btn').forEach(btn => {
                btn.addEventListener('click', (e) => {
                    e.preventDefault();
                    var itemId = btn.getAttribute('data-item-id');
                    openEditItemModal(itemId);
                });
            });

             document.querySelectorAll('.change-status-btn').forEach(btn => {
                 btn.addEventListener('click', (e) => {
                     e.preventDefault();
                     var itemId = btn.getAttribute('data-item-id');
                     openChangeStatusModal(itemId);
                 });
             });

             // Set opacity to 1 for tbody if it exists
             var tbody = document.getElementById('inventoryTableBody');
             if (tbody) {
                 tbody.style.opacity = '1';
             }
             // Hide the empty state when we have data
             hideEmptyState();
        } catch (error) {
            console.error('Error rendering table:', error);
            showEmptyState('Error rendering inventory table');
        }
    }

    // Update pagination controls
    function updatePagination(data) {
        var prevBtn = document.getElementById('prevBtn');
        var nextBtn = document.getElementById('nextBtn');
        var pageInfo = document.getElementById('pageInfo');
        var pageStart = document.getElementById('pageStart');
        var pageEnd = document.getElementById('pageEnd');
        var pageTotal = document.getElementById('pageTotal');
        var paginationControls = document.getElementById('paginationControls');

        // Guard against missing elements (can happen during Turbo navigation)
        if (!prevBtn || !nextBtn || !pageInfo) {
            console.warn('Pagination elements not found');
            return;
        }

        prevBtn.disabled = !data.links[0].url;
        nextBtn.disabled = !data.links[data.links.length - 1].url;

        pageInfo.textContent = `Page ${data.current_page} of ${data.last_page}`;
        if (pageStart) pageStart.textContent = (data.from || 0);
        if (pageEnd) pageEnd.textContent = (data.to || 0);
        if (pageTotal) pageTotal.textContent = data.total;

        if (paginationControls) {
            paginationControls.style.display = 'flex';
        }
    }

    // Show empty state
    function showEmptyState(message) {
        var tbody = document.getElementById('inventoryTableBody');
        var emptyState = document.getElementById('emptyState');
        var paginationControls = document.getElementById('paginationControls');

        // Guard against missing elements (can happen during Turbo navigation)
        if (tbody) {
            tbody.innerHTML = '';
        }
        if (emptyState) {
            emptyState.textContent = message;
            emptyState.style.display = 'block';
        }
        if (paginationControls) {
            paginationControls.style.display = 'none';
        }
    }

    // Hide empty state
    function hideEmptyState() {
        var emptyState = document.getElementById('emptyState');
        // Guard against missing elements (can happen during Turbo navigation)
        if (emptyState) {
            emptyState.style.display = 'none';
        }
    }

    // Show loading state with skeleton
    function showLoadingState() {
        var tbody = document.getElementById('inventoryTableBody');
        var emptyState = document.getElementById('emptyState');
        var paginationControls = document.getElementById('paginationControls');

        // Guard against missing elements (can happen during Turbo navigation)
        if (!tbody) {
            console.warn('inventoryTableBody element not found');
            return;
        }

        var skeletonRows = Array.from({length: 5}, () => `
            <tr class="border-b border-neutral-200 dark:border-neutral-800 hover:bg-neutral-50 dark:hover:bg-neutral-900/50 transition-colors animate-pulse">
                <td class="px-4 py-3"><div class="h-4 bg-neutral-300 dark:bg-neutral-700 rounded w-1/2"></div></td>
                <td class="px-4 py-3"><div class="h-4 bg-neutral-300 dark:bg-neutral-700 rounded w-3/4"></div></td>
                <td class="px-4 py-3"><div class="h-4 bg-neutral-300 dark:bg-neutral-700 rounded w-2/3"></div></td>
                <td class="px-4 py-3"><div class="h-4 bg-neutral-300 dark:bg-neutral-700 rounded w-1/2"></div></td>
                <td class="px-4 py-3"><div class="h-4 bg-neutral-300 dark:bg-neutral-700 rounded w-2/3"></div></td>
                <td class="px-4 py-3"><div class="h-4 bg-neutral-300 dark:bg-neutral-700 rounded w-1/3"></div></td>
                <td class="px-4 py-3"><div class="h-4 bg-neutral-300 dark:bg-neutral-700 rounded w-1/4"></div></td>
                <td class="px-4 py-3"><div class="h-4 bg-neutral-300 dark:bg-neutral-700 rounded w-1/3"></div></td>
            </tr>
        `).join('');
        tbody.innerHTML = skeletonRows;

        if (emptyState) {
            emptyState.style.display = 'none';
        }
        if (paginationControls) {
            paginationControls.style.display = 'none';
        }
    }

    // Hide loading state
    function hideLoadingState() {
        hideEmptyState();
        var tbody = document.getElementById('inventoryTableBody');
        // Guard against missing elements (can happen during Turbo navigation)
        if (tbody) {
            tbody.style.opacity = '1';
        }
    }

    // Show error notification (user-facing)
    function showErrorNotification(message) {
        // Create a temporary notification element
        var notification = document.createElement('div');
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

        // Auto-remove after 5 seconds
        setTimeout(() => {
            notification.style.opacity = '0';
            notification.style.transition = 'opacity 0.3s ease-out';
            setTimeout(() => {
                notification.remove();
            }, 300);
        }, 5000);
    }

    // Pagination handlers
    function previousPage() {
        if (inventoryState.currentPage > 1) {
            inventoryState.currentPage--;
            fetchInventoryItems();
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
    }

    function nextPage() {
        if (inventoryState.currentPage < inventoryState.totalPages) {
            inventoryState.currentPage++;
            fetchInventoryItems();
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
    }

    // openChangeStatusModal and openEditItemModal are defined in the edit-item-modal partial

    // Open add item modal
    function openAddItemModal() {
        var modal = document.getElementById('addItemModal');
        if (modal) {
            globalThis.addItemModalState.isOpen = true;
            modal.classList.remove('hidden');
            modal.classList.add('flex');

            // Focus first input
            setTimeout(() => {
                modal.querySelector('input[name="name"]').focus();
            }, 100);

            // Reset form
            var form = document.getElementById('addItemForm');
            if (form) {
                form.reset();
            }
        }
    }
</script>

{{-- Include Add Item Modal --}}
@include('inventories.partials.add-item-modal')

{{-- Include Edit Item Modal --}}
@include('inventories.partials.edit-item-modal')

{{-- Include Item Details Modal --}}
@include('inventories.partials.item-details-modal')

</body>
</html>
