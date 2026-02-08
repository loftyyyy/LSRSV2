{{-- New Reservation Modal --}}
<div id="newReservationModal" class="hidden fixed inset-0 z-50 flex items-center justify-center px-2 py-6 bg-black/60 backdrop-blur-sm overflow-y-auto">
    <div class="w-full max-w-4xl bg-white dark:bg-neutral-950 border border-neutral-200 dark:border-neutral-800 rounded-3xl shadow-2xl flex flex-col max-h-[calc(100vh-3rem)] my-auto">
        {{-- Header --}}
        <div class="flex-shrink-0 flex items-center justify-between px-6 py-4 border-b border-neutral-200 dark:border-neutral-800 bg-neutral-50/80 rounded-t-3xl dark:bg-neutral-900/50">
            <div>
                <p class="text-xs uppercase tracking-[0.2em] text-neutral-500 dark:text-neutral-500">New Booking</p>
                <h3 class="text-lg font-semibold text-neutral-900 dark:text-white">Create Reservation</h3>
            </div>
            <button onclick="closeNewReservationModal()" class="text-neutral-500 hover:text-neutral-800 dark:hover:text-neutral-200 text-xl transition-colors duration-200">&times;</button>
        </div>

        {{-- Form (scrollable) --}}
        <form id="newReservationForm" class="flex-1 overflow-y-auto px-8 py-6 space-y-5">
            @csrf

            <div class="grid grid-cols-2 gap-6">
                {{-- Customer Information Column --}}
                <div class="space-y-4">
                    <div class="flex items-center gap-2 text-sm font-medium text-neutral-700 dark:text-neutral-300">
                        <x-icon name="user" class="h-4 w-4" />
                        <span>Customer Information</span>
                    </div>

                    <div class="space-y-3">
                        {{-- Customer Selection --}}
                        <div class="space-y-2">
                            <label class="text-sm font-medium text-neutral-700 dark:text-neutral-300">Customer *</label>
                            <div class="flex items-center rounded-2xl bg-white px-3 py-2.5 border border-neutral-300 focus-within:border-neutral-500 dark:border-neutral-800 dark:bg-black/60 transition-colors duration-300 ease-in-out">
                                <x-icon name="user" class="h-4 w-4 text-neutral-500 mr-2 transition-colors duration-300 ease-in-out dark:text-neutral-400" />
                                <select
                                    name="customer_id"
                                    id="customerSelect"
                                    required
                                    class="flex-1 min-w-0 bg-transparent text-xs text-neutral-700 placeholder:text-neutral-400 dark:text-neutral-100 dark:placeholder:text-neutral-500 focus:outline-none transition-colors duration-300 ease-in-out"
                                >
                                    <option value="">Select a customer</option>
                                    {{-- Options will be populated via JavaScript --}}
                                </select>
                                <a
                                    href="{{ url('/customers') }}"
                                    title="Go to Customers"
                                    class="ml-2 inline-flex h-7 w-7 flex-shrink-0 items-center justify-center rounded-lg bg-violet-600 text-white hover:bg-violet-500 dark:text-black dark:hover:text-white transition-colors duration-200"
                                >
                                    <x-icon name="plus" class="h-3.5 w-3.5" />
                                </a>
                            </div>
                        </div>

                        {{-- Customer Details Preview --}}
                        <div id="customerPreview" class="hidden bg-neutral-50 dark:bg-neutral-900/50 border border-neutral-200 dark:border-neutral-800 rounded-xl p-3 space-y-2">
                            <div class="flex items-center gap-2">
                                <x-icon name="mail" class="h-3.5 w-3.5 text-neutral-500" />
                                <span id="customerEmail" class="text-xs text-neutral-600 dark:text-neutral-400"></span>
                            </div>
                            <div class="flex items-center gap-2">
                                <x-icon name="phone" class="h-3.5 w-3.5 text-neutral-500" />
                                <span id="customerPhone" class="text-xs text-neutral-600 dark:text-neutral-400"></span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Reservation Dates Column --}}
                <div class="space-y-4">
                    <div class="flex items-center gap-2 text-sm font-medium text-neutral-700 dark:text-neutral-300">
                        <x-icon name="calendar" class="h-4 w-4" />
                        <span>Reservation Dates</span>
                    </div>

                    <div class="space-y-3">
                        {{-- Start Date (Pickup) --}}
                        <div class="space-y-2">
                            <label class="text-sm font-medium text-neutral-700 dark:text-neutral-300">Pickup Date *</label>
                            <div class="flex items-center rounded-2xl bg-white px-3 py-2.5 border border-neutral-300 focus-within:border-neutral-500 dark:border-neutral-800 dark:bg-black/60 transition-colors duration-300 ease-in-out">
                                <x-icon name="calendar" class="h-4 w-4 text-neutral-500 mr-2 transition-colors duration-300 ease-in-out" />
                                <input
                                    type="date"
                                    name="start_date"
                                    id="startDate"
                                    required
                                    class="w-full bg-transparent text-xs text-neutral-700 placeholder:text-neutral-400 dark:text-neutral-100 dark:placeholder:text-neutral-500 focus:outline-none transition-colors duration-300 ease-in-out"
                                />
                            </div>
                        </div>

                        {{-- End Date (Return) --}}
                        <div class="space-y-2">
                            <label class="text-sm font-medium text-neutral-700 dark:text-neutral-300">Return Date *</label>
                            <div class="flex items-center rounded-2xl bg-white px-3 py-2.5 border border-neutral-300 focus-within:border-neutral-500 dark:border-neutral-800 dark:bg-black/60 transition-colors duration-300 ease-in-out">
                                <x-icon name="calendar" class="h-4 w-4 text-neutral-500 mr-2 transition-colors duration-300 ease-in-out" />
                                <input
                                    type="date"
                                    name="end_date"
                                    id="endDate"
                                    required
                                    class="w-full bg-transparent text-xs text-neutral-700 placeholder:text-neutral-400 dark:text-neutral-100 dark:placeholder:text-neutral-500 focus:outline-none transition-colors duration-300 ease-in-out"
                                />
                            </div>
                        </div>

                        {{-- Rental Duration Display --}}
                        <div id="rentalDuration" class="hidden bg-violet-50 dark:bg-violet-900/20 border border-violet-200 dark:border-violet-800 rounded-xl p-3">
                            <div class="flex items-center gap-2">
                                <x-icon name="clock" class="h-3.5 w-3.5 text-violet-500" />
                                <span class="text-xs text-violet-700 dark:text-violet-300">
                                    Rental duration: <strong id="durationDays">0</strong> day(s)
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Items Selection Section --}}
            <div class="space-y-3">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2 text-sm font-medium text-neutral-700 dark:text-neutral-300">
                        <x-icon name="package" class="h-4 w-4" />
                        <span>Items to Reserve</span>
                    </div>
                    <button
                        type="button"
                        onclick="openItemSelector()"
                        class="inline-flex items-center gap-1.5 rounded-xl px-3 py-1.5 text-xs font-medium border border-violet-300 bg-violet-50 text-violet-700 hover:bg-violet-100 dark:border-violet-700 dark:bg-violet-900/30 dark:text-violet-300 dark:hover:bg-violet-900/50 transition-colors duration-200"
                    >
                        <x-icon name="plus" class="h-3.5 w-3.5" />
                        <span>Add Item</span>
                    </button>
                </div>

                {{-- Selected Items List --}}
                <div id="selectedItemsContainer" class="bg-neutral-50 dark:bg-neutral-900/50 border border-neutral-200 dark:border-neutral-800 rounded-xl p-4">
                    {{-- Empty State --}}
                    <div id="noItemsMessage" class="text-center py-6">
                        <x-icon name="package" class="h-8 w-8 text-neutral-400 mx-auto mb-2" />
                        <p class="text-xs text-neutral-500 dark:text-neutral-400">No items added yet</p>
                        <p class="text-xs text-neutral-400 dark:text-neutral-500 mt-1">Click "Add Item" to select items for this reservation</p>
                    </div>

                    {{-- Items Table (hidden when empty) --}}
                    <div id="selectedItemsTable" class="hidden">
                        <table class="w-full text-left text-xs">
                            <thead class="text-[11px] uppercase tracking-[0.15em] text-neutral-500 border-b border-neutral-200 dark:border-neutral-700">
                                <tr>
                                    <th class="py-2 pr-3 font-medium">Item</th>
                                    <th class="py-2 pr-3 font-medium">Size</th>
                                    <th class="py-2 pr-3 font-medium">Color</th>
                                    <th class="py-2 pr-3 font-medium text-right">Price/Day</th>
                                    <th class="py-2 pr-3 font-medium text-center">Qty</th>
                                    <th class="py-2 font-medium text-right">Subtotal</th>
                                    <th class="py-2 pl-2 font-medium"></th>
                                </tr>
                            </thead>
                            <tbody id="selectedItemsBody" class="divide-y divide-neutral-200 dark:divide-neutral-700">
                                {{-- Items will be added here dynamically --}}
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Total Summary --}}
                <div id="totalSummary" class="hidden bg-neutral-100 dark:bg-neutral-900 border border-neutral-200 dark:border-neutral-800 rounded-xl p-4">
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-neutral-700 dark:text-neutral-300">Estimated Total</span>
                        <span id="estimatedTotal" class="text-lg font-semibold text-violet-600 dark:text-violet-400">₱0.00</span>
                    </div>
                    <p class="text-xs text-neutral-500 dark:text-neutral-400 mt-1">
                        Based on <span id="totalDays">0</span> day(s) rental period
                    </p>
                </div>
            </div>

            {{-- Notes Section --}}
            <div class="space-y-3">
                <div class="flex items-center gap-2 text-sm font-medium text-neutral-700 dark:text-neutral-300">
                    <x-icon name="file-text" class="h-4 w-4" />
                    <span>Notes (Optional)</span>
                </div>
                <div class="flex items-start rounded-2xl bg-white px-3 py-2.5 border border-neutral-300 focus-within:border-neutral-500 dark:border-neutral-800 dark:bg-black/60 transition-colors duration-300 ease-in-out">
                    <textarea
                        name="notes"
                        id="reservationNotes"
                        rows="3"
                        placeholder="Any special instructions or notes for this reservation..."
                        class="w-full bg-transparent text-xs text-neutral-700 placeholder:text-neutral-400 dark:text-neutral-100 dark:placeholder:text-neutral-500 focus:outline-none resize-none transition-colors duration-300 ease-in-out"
                    ></textarea>
                </div>
            </div>

            {{-- Error Message --}}
            <div id="reservationError" class="hidden bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl p-3 flex items-center gap-2">
                <x-icon name="alert-circle" class="h-4 w-4 text-red-500" />
                <p class="text-xs text-red-600 dark:text-red-400"></p>
            </div>

            {{-- Success Message --}}
            <div id="reservationSuccess" class="hidden bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800 rounded-xl p-3 flex items-center gap-2">
                <x-icon name="check-circle" class="h-4 w-4 text-emerald-500" />
                <p class="text-xs text-emerald-600 dark:text-emerald-400"></p>
            </div>

            {{-- Action Buttons --}}
            <div class="flex items-center justify-end gap-3 pt-2">
                <button
                    type="submit"
                    id="submitReservationBtn"
                    class="inline-flex items-center gap-2 rounded-xl px-4 py-2 text-[14px] font-medium bg-violet-600 text-white dark:hover:text-white hover:text-black dark:text-black hover:bg-violet-500 shadow-violet-600/40 transition-colors duration-100 ease-in-out disabled:opacity-50 disabled:cursor-not-allowed"
                >
                    <span id="submitBtnText">Create Reservation</span>
                    <span id="submitBtnLoading" class="hidden">Creating...</span>
                </button>
                <button
                    type="button"
                    onclick="closeNewReservationModal()"
                    class="inline-flex items-center gap-2 rounded-xl px-3.5 py-2 text-[14px] font-medium border dark:hover:text-black hover:text-white border-neutral-300 bg-white text-neutral-700 dark:hover:bg-violet-600 hover:bg-violet-600 dark:border-neutral-800 dark:bg-neutral-950/80 dark:text-neutral-200 dark:hover:bg-neutral-900 transition-colors duration-100 ease-in-out"
                >
                    Cancel
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Item Selector Modal (nested modal for selecting items) --}}
<div id="itemSelectorModal" class="hidden fixed inset-0 z-[60] flex items-center justify-center px-2 py-6 bg-black/60 backdrop-blur-sm overflow-y-auto">
    <div class="w-full max-w-2xl bg-white dark:bg-neutral-950 border border-neutral-200 dark:border-neutral-800 rounded-3xl shadow-2xl flex flex-col max-h-[calc(100vh-6rem)] my-auto">
        {{-- Header --}}
        <div class="flex-shrink-0 flex items-center justify-between px-6 py-4 border-b border-neutral-200 dark:border-neutral-800 bg-neutral-50/80 rounded-t-3xl dark:bg-neutral-900/50">
            <div>
                <p class="text-xs uppercase tracking-[0.2em] text-neutral-500 dark:text-neutral-500">Browse</p>
                <h3 class="text-lg font-semibold text-neutral-900 dark:text-white">Select Items</h3>
            </div>
            <button onclick="closeItemSelector()" class="text-neutral-500 hover:text-neutral-800 dark:hover:text-neutral-200 text-xl transition-colors duration-200">&times;</button>
        </div>

        {{-- Search --}}
        <div class="px-6 py-4 border-b border-neutral-200 dark:border-neutral-800">
            <div class="flex items-center gap-3 rounded-2xl bg-white px-4 py-2.5 border border-neutral-300 focus-within:border-neutral-500 dark:border-neutral-800 dark:bg-black/60 transition-colors duration-300 ease-in-out">
                <x-icon name="search" class="h-4 w-4 text-neutral-500 transition-colors duration-300 ease-in-out" />
                <input
                    type="text"
                    id="itemSearchInput"
                    placeholder="Search items by name, SKU, or type..."
                    class="w-full bg-transparent text-xs text-neutral-700 placeholder:text-neutral-400 dark:text-neutral-100 dark:placeholder:text-neutral-500 focus:outline-none transition-colors duration-300 ease-in-out"
                />
            </div>
        </div>

        {{-- Items List --}}
        <div class="flex-1 overflow-y-auto px-6 py-4">
            <div id="availableItemsLoading" class="text-center py-8">
                <div class="animate-spin h-6 w-6 border-2 border-violet-600 border-t-transparent rounded-full mx-auto"></div>
                <p class="text-xs text-neutral-500 mt-2">Loading available items...</p>
            </div>

            <div id="availableItemsEmpty" class="hidden text-center py-8">
                <x-icon name="package" class="h-10 w-10 text-neutral-400 mx-auto mb-2" />
                <p class="text-sm text-neutral-500 dark:text-neutral-400">No available items found</p>
            </div>

            <div id="availableItemsGrid" class="hidden grid grid-cols-1 gap-3">
                {{-- Items will be populated here --}}
            </div>
        </div>

        {{-- Footer --}}
        <div class="flex-shrink-0 px-6 py-4 border-t border-neutral-200 dark:border-neutral-800 bg-neutral-50/80 dark:bg-neutral-900/50 rounded-b-3xl">
            <button
                type="button"
                onclick="closeItemSelector()"
                class="w-full inline-flex items-center justify-center gap-2 rounded-xl px-4 py-2 text-[14px] font-medium border border-neutral-300 bg-white text-neutral-700 hover:bg-neutral-100 dark:border-neutral-800 dark:bg-neutral-950/80 dark:text-neutral-200 dark:hover:bg-neutral-900 transition-colors duration-100 ease-in-out"
            >
                Done
            </button>
        </div>
    </div>
</div>

<style>
    /* Optimize modal performance */
    #newReservationModal,
    #itemSelectorModal {
        will-change: opacity;
        transform: translateZ(0);
        backface-visibility: hidden;
    }

    #newReservationModal .max-w-4xl,
    #itemSelectorModal .max-w-2xl {
        will-change: transform;
        transform: translateZ(0);
    }

    /* Custom scrollbar for form */
    #newReservationForm {
        scrollbar-width: thin;
        scrollbar-color: rgba(155, 155, 155, 0.5) transparent;
    }

    #newReservationForm::-webkit-scrollbar {
        width: 6px;
    }

    #newReservationForm::-webkit-scrollbar-track {
        background: transparent;
    }

    #newReservationForm::-webkit-scrollbar-thumb {
        background-color: rgba(155, 155, 155, 0.5);
        border-radius: 3px;
    }

    #newReservationForm::-webkit-scrollbar-thumb:hover {
        background-color: rgba(155, 155, 155, 0.7);
    }

    .dark #newReservationForm::-webkit-scrollbar-thumb {
        background-color: rgba(100, 100, 100, 0.5);
    }

    .dark #newReservationForm::-webkit-scrollbar-thumb:hover {
        background-color: rgba(100, 100, 100, 0.7);
    }

    /* Fix select dropdown appearance */
    #newReservationForm select,
    #itemSelectorModal select {
        background-color: transparent;
        color: #374151;
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e");
        background-position: right 0.5rem center;
        background-repeat: no-repeat;
        background-size: 1.5em 1.5em;
        padding-right: 2rem;
        -webkit-appearance: none;
        -moz-appearance: none;
        appearance: none;
    }

    .dark #newReservationForm select,
    .dark #itemSelectorModal select {
        color: #f5f5f5;
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%239ca3af' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e");
    }

    /* Style select option text for better visibility */
    #newReservationForm select option,
    #itemSelectorModal select option {
        color: #374151;
        background-color: white;
    }

    .dark #newReservationForm select option,
    .dark #itemSelectorModal select option {
        color: #f5f5f5;
        background-color: rgb(17, 24, 39);
    }

    /* Date input styling */
    #newReservationForm input[type="date"] {
        color-scheme: light;
    }

    .dark #newReservationForm input[type="date"] {
        color-scheme: dark;
    }
</style>

<script>
    // Use globalThis to avoid redeclaration errors when Turbo navigates between pages
    if (!globalThis.reservationModalState) {
        globalThis.reservationModalState = {
            isOpen: false,
            isItemSelectorOpen: false,
            isSubmitting: false,
            selectedItems: [],
            availableItems: [],
            customers: []
        };
    }

    // Short reference for easier access
    var reservationState = globalThis.reservationModalState;

    // ===== Modal Open/Close Functions =====

    function openNewReservationModal() {
        reservationState.isOpen = true;
        var modal = document.getElementById('newReservationModal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');

        // Set minimum date to today
        var today = new Date().toISOString().split('T')[0];
        document.getElementById('startDate').min = today;
        document.getElementById('endDate').min = today;

        // Load customers
        loadCustomers();

        // Reset form
        document.getElementById('newReservationForm').reset();
        reservationState.selectedItems = [];
        renderSelectedItems();
        hideReservationMessages();
    }

    function closeNewReservationModal() {
        reservationState.isOpen = false;
        var modal = document.getElementById('newReservationModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');

        // Close item selector if open
        closeItemSelector();

        // Reset form and state
        document.getElementById('newReservationForm').reset();
        reservationState.selectedItems = [];
        renderSelectedItems();
        hideReservationMessages();
        reservationState.isSubmitting = false;
        updateReservationSubmitButton();
    }

    function openItemSelector() {
        reservationState.isItemSelectorOpen = true;
        var modal = document.getElementById('itemSelectorModal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');

        // Load available items
        loadAvailableItems();
    }

    function closeItemSelector() {
        reservationState.isItemSelectorOpen = false;
        var modal = document.getElementById('itemSelectorModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    // ===== Data Loading Functions =====

    async function loadCustomers() {
        try {
            var response = await axios.get('/api/customers');
            var customers = response.data.data || response.data;

            var select = document.getElementById('customerSelect');
            select.innerHTML = '<option value="">Select a customer</option>';

            customers.forEach(function(customer) {
                var option = document.createElement('option');
                option.value = customer.customer_id;
                option.textContent = customer.first_name + ' ' + customer.last_name;
                option.dataset.email = customer.email || '';
                option.dataset.phone = customer.contact_number || '';
                select.appendChild(option);
            });

            reservationState.customers = customers;
        } catch (error) {
            console.error('Error loading customers:', error);
        }
    }

    async function loadAvailableItems() {
        var loadingEl = document.getElementById('availableItemsLoading');
        var emptyEl = document.getElementById('availableItemsEmpty');
        var gridEl = document.getElementById('availableItemsGrid');

        loadingEl.classList.remove('hidden');
        emptyEl.classList.add('hidden');
        gridEl.classList.add('hidden');

        try {
            var response = await axios.get('/api/reservations/items/browse');
            // Handle paginated response: response.data = { data: { data: [...items], current_page, ... }, message }
            var responseData = response.data.data || response.data;
            // If paginated, items are in responseData.data; otherwise responseData is the items array
            var items = Array.isArray(responseData) ? responseData : (responseData.data || []);

            reservationState.availableItems = items;
            loadingEl.classList.add('hidden');

            if (items.length === 0) {
                emptyEl.classList.remove('hidden');
                return;
            }

            renderAvailableItems(items);
            gridEl.classList.remove('hidden');
        } catch (error) {
            console.error('Error loading items:', error);
            loadingEl.classList.add('hidden');
            emptyEl.classList.remove('hidden');
        }
    }

    function renderAvailableItems(items) {
        var gridEl = document.getElementById('availableItemsGrid');

        gridEl.innerHTML = items.map(function(item) {
            var isSelected = reservationState.selectedItems.some(function(si) {
                return si.item_id === item.item_id;
            });

            return `
                <div class="group flex items-center justify-between p-3 rounded-xl border ${isSelected ? 'border-violet-500 bg-violet-50 dark:bg-violet-900/20' : 'border-neutral-200 dark:border-neutral-800 hover:border-violet-300 hover:bg-violet-50/50 dark:hover:border-violet-800 dark:hover:bg-violet-900/10'} transition-colors duration-200">
                    <div class="flex items-center gap-3">
                        <div class="h-12 w-12 rounded-lg bg-neutral-100 dark:bg-neutral-800 flex items-center justify-center overflow-hidden">
                            ${item.images && item.images.length > 0
                                ? `<img src="/storage/${item.images[0].image_path}" alt="${item.name}" class="h-full w-full object-cover">`
                                : `<svg class="h-6 w-6 text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>`
                            }
                        </div>
                        <div>
                            <p class="text-sm font-medium text-neutral-900 dark:text-white">${item.name}</p>
                            <p class="text-xs text-neutral-500 dark:text-neutral-400">${item.sku} &bull; ${item.size} &bull; ${item.color}</p>
                            <p class="text-xs font-medium text-violet-600 dark:text-violet-400">₱${parseFloat(item.rental_price).toLocaleString('en-PH', {minimumFractionDigits: 2})}/day</p>
                        </div>
                    </div>
                    <button
                        type="button"
                        onclick="${isSelected ? `removeItemFromReservation(${item.item_id})` : `addItemToReservation(${JSON.stringify(item).replace(/"/g, '&quot;')})`}"
                        class="inline-flex items-center gap-1.5 rounded-lg px-3 py-1.5 text-xs font-medium ${isSelected ? 'bg-red-100 text-red-700 hover:bg-red-200 dark:bg-red-900/30 dark:text-red-400 dark:hover:bg-red-900/50' : 'border border-violet-300 bg-white text-violet-700 hover:bg-violet-100 hover:border-violet-400 dark:border-violet-700 dark:bg-violet-900/30 dark:text-violet-300 dark:hover:bg-violet-900/50'} transition-colors duration-200"
                    >
                        ${isSelected ? 'Remove' : 'Add'}
                    </button>
                </div>
            `;
        }).join('');
    }

    // ===== Item Management Functions =====

    function addItemToReservation(item) {
        // Check if item already exists
        var exists = reservationState.selectedItems.some(function(si) {
            return si.item_id === item.item_id;
        });

        if (!exists) {
            reservationState.selectedItems.push({
                item_id: item.item_id,
                name: item.name,
                sku: item.sku,
                size: item.size,
                color: item.color,
                rental_price: parseFloat(item.rental_price),
                quantity: 1,
                notes: ''
            });

            renderSelectedItems();
            renderAvailableItems(reservationState.availableItems);
        }
    }

    function removeItemFromReservation(itemId) {
        reservationState.selectedItems = reservationState.selectedItems.filter(function(item) {
            return item.item_id !== itemId;
        });

        renderSelectedItems();
        renderAvailableItems(reservationState.availableItems);
    }

    function updateItemQuantity(itemId, quantity) {
        var item = reservationState.selectedItems.find(function(si) {
            return si.item_id === itemId;
        });

        if (item) {
            item.quantity = Math.max(1, parseInt(quantity) || 1);
            renderSelectedItems();
        }
    }

    function renderSelectedItems() {
        var noItemsEl = document.getElementById('noItemsMessage');
        var tableEl = document.getElementById('selectedItemsTable');
        var bodyEl = document.getElementById('selectedItemsBody');
        var summaryEl = document.getElementById('totalSummary');

        if (reservationState.selectedItems.length === 0) {
            noItemsEl.classList.remove('hidden');
            tableEl.classList.add('hidden');
            summaryEl.classList.add('hidden');
            return;
        }

        noItemsEl.classList.add('hidden');
        tableEl.classList.remove('hidden');
        summaryEl.classList.remove('hidden');

        var rentalDays = calculateRentalDays();

        bodyEl.innerHTML = reservationState.selectedItems.map(function(item) {
            var subtotal = item.rental_price * item.quantity * rentalDays;
            return `
                <tr class="text-[13px]">
                    <td class="py-2.5 pr-3 text-neutral-900 dark:text-neutral-100">${item.name}</td>
                    <td class="py-2.5 pr-3 text-neutral-600 dark:text-neutral-400">${item.size}</td>
                    <td class="py-2.5 pr-3 text-neutral-600 dark:text-neutral-400">${item.color}</td>
                    <td class="py-2.5 pr-3 text-right text-neutral-700 dark:text-neutral-300 font-geist-mono">₱${item.rental_price.toLocaleString('en-PH', {minimumFractionDigits: 2})}</td>
                    <td class="py-2.5 pr-3 text-center">
                        <input
                            type="number"
                            min="1"
                            value="${item.quantity}"
                            onchange="updateItemQuantity(${item.item_id}, this.value)"
                            class="w-14 text-center text-xs rounded-lg border border-neutral-300 dark:border-neutral-700 bg-white dark:bg-black/60 text-neutral-700 dark:text-neutral-100 px-2 py-1 focus:outline-none focus:border-violet-500"
                        />
                    </td>
                    <td class="py-2.5 text-right text-neutral-900 dark:text-neutral-100 font-geist-mono font-medium">₱${subtotal.toLocaleString('en-PH', {minimumFractionDigits: 2})}</td>
                    <td class="py-2.5 pl-2 text-right">
                        <button
                            type="button"
                            onclick="removeItemFromReservation(${item.item_id})"
                            class="text-red-500 hover:text-red-700 dark:hover:text-red-400 transition-colors duration-200"
                        >
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </td>
                </tr>
            `;
        }).join('');

        updateTotalSummary();
    }

    // ===== Calculation Functions =====

    function calculateRentalDays() {
        var startDate = document.getElementById('startDate').value;
        var endDate = document.getElementById('endDate').value;

        if (!startDate || !endDate) return 1;

        var start = new Date(startDate);
        var end = new Date(endDate);
        var diffTime = Math.abs(end - start);
        var diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

        return Math.max(1, diffDays);
    }

    function updateTotalSummary() {
        var rentalDays = calculateRentalDays();
        var total = 0;

        reservationState.selectedItems.forEach(function(item) {
            total += item.rental_price * item.quantity * rentalDays;
        });

        document.getElementById('estimatedTotal').textContent = '₱' + total.toLocaleString('en-PH', {minimumFractionDigits: 2});
        document.getElementById('totalDays').textContent = rentalDays;
    }

    function updateRentalDuration() {
        var startDate = document.getElementById('startDate').value;
        var endDate = document.getElementById('endDate').value;
        var durationEl = document.getElementById('rentalDuration');
        var daysEl = document.getElementById('durationDays');

        if (startDate && endDate) {
            var days = calculateRentalDays();
            daysEl.textContent = days;
            durationEl.classList.remove('hidden');
        } else {
            durationEl.classList.add('hidden');
        }

        renderSelectedItems();
    }

    // ===== Message Functions =====

    function hideReservationMessages() {
        document.getElementById('reservationError').classList.add('hidden');
        document.getElementById('reservationSuccess').classList.add('hidden');
    }

    function showReservationError(message) {
        var errorDiv = document.getElementById('reservationError');
        errorDiv.querySelector('p').textContent = message;
        errorDiv.classList.remove('hidden');
        document.getElementById('reservationSuccess').classList.add('hidden');
    }

    function showReservationSuccess(message) {
        var successDiv = document.getElementById('reservationSuccess');
        successDiv.querySelector('p').textContent = message;
        successDiv.classList.remove('hidden');
        document.getElementById('reservationError').classList.add('hidden');
    }

    function updateReservationSubmitButton() {
        var btn = document.getElementById('submitReservationBtn');
        var btnText = document.getElementById('submitBtnText');
        var btnLoading = document.getElementById('submitBtnLoading');

        if (reservationState.isSubmitting) {
            btn.disabled = true;
            btnText.classList.add('hidden');
            btnLoading.classList.remove('hidden');
        } else {
            btn.disabled = false;
            btnText.classList.remove('hidden');
            btnLoading.classList.add('hidden');
        }
    }

    // ===== Customer Preview =====

    function updateCustomerPreview() {
        var select = document.getElementById('customerSelect');
        var previewEl = document.getElementById('customerPreview');
        var emailEl = document.getElementById('customerEmail');
        var phoneEl = document.getElementById('customerPhone');

        var selectedOption = select.options[select.selectedIndex];

        if (select.value && selectedOption) {
            emailEl.textContent = selectedOption.dataset.email || 'No email';
            phoneEl.textContent = selectedOption.dataset.phone || 'No phone';
            previewEl.classList.remove('hidden');
        } else {
            previewEl.classList.add('hidden');
        }
    }

    // ===== Form Validation =====

    function validateReservationForm() {
        var errors = [];

        var customerId = document.getElementById('customerSelect').value;
        var startDate = document.getElementById('startDate').value;
        var endDate = document.getElementById('endDate').value;

        if (!customerId) {
            errors.push('Please select a customer');
        }

        if (!startDate) {
            errors.push('Please select a pickup date');
        }

        if (!endDate) {
            errors.push('Please select a return date');
        }

        if (startDate && endDate && new Date(endDate) <= new Date(startDate)) {
            errors.push('Return date must be after pickup date');
        }

        if (reservationState.selectedItems.length === 0) {
            errors.push('Please add at least one item to the reservation');
        }

        return errors;
    }

    // ===== Event Listeners =====

    document.addEventListener('DOMContentLoaded', function() {
        // Customer selection change
        var customerSelect = document.getElementById('customerSelect');
        if (customerSelect) {
            customerSelect.addEventListener('change', updateCustomerPreview);
        }

        // Date change listeners
        var startDate = document.getElementById('startDate');
        var endDate = document.getElementById('endDate');

        if (startDate) {
            startDate.addEventListener('change', function() {
                // Set minimum end date to start date
                if (this.value) {
                    endDate.min = this.value;
                }
                updateRentalDuration();
            });
        }

        if (endDate) {
            endDate.addEventListener('change', updateRentalDuration);
        }

        // Item search
        var searchInput = document.getElementById('itemSearchInput');
        if (searchInput) {
            searchInput.addEventListener('input', function() {
                var query = this.value.toLowerCase();
                var filtered = reservationState.availableItems.filter(function(item) {
                    return item.name.toLowerCase().includes(query) ||
                           item.sku.toLowerCase().includes(query) ||
                           item.item_type.toLowerCase().includes(query);
                });
                renderAvailableItems(filtered);
            });
        }

        // Form submission
        var form = document.getElementById('newReservationForm');
        if (form) {
            form.addEventListener('submit', async function(e) {
                e.preventDefault();

                if (reservationState.isSubmitting) return;

                hideReservationMessages();

                var errors = validateReservationForm();
                if (errors.length > 0) {
                    showReservationError(errors[0]);
                    return;
                }

                reservationState.isSubmitting = true;
                updateReservationSubmitButton();

                try {
                    var payload = {
                        customer_id: document.getElementById('customerSelect').value,
                        start_date: document.getElementById('startDate').value,
                        end_date: document.getElementById('endDate').value,
                        notes: document.getElementById('reservationNotes').value,
                        items: reservationState.selectedItems.map(function(item) {
                            return {
                                item_id: item.item_id,
                                quantity: item.quantity,
                                rental_price: item.rental_price,
                                notes: item.notes || ''
                            };
                        })
                    };

                    var response = await axios.post('/api/reservations', payload);
                    var data = response.data;

                    if (data.success !== false) {
                        showReservationSuccess('Reservation created successfully!');

                        setTimeout(function() {
                            closeNewReservationModal();
                            // Refresh the reservations list
                            if (typeof fetchReservations === 'function') {
                                fetchReservations();
                            }
                            // Reload page to show new reservation
                            window.location.reload();
                        }, 1500);
                    } else {
                        showReservationError(data.message || 'Failed to create reservation. Please try again.');
                    }
                } catch (error) {
                    console.error('Error creating reservation:', error);
                    var errorMessage = error.response?.data?.message || error.message || 'Network error. Please check your connection and try again.';
                    showReservationError(errorMessage);
                } finally {
                    reservationState.isSubmitting = false;
                    updateReservationSubmitButton();
                }
            });
        }
    });

    // Close modals on escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            if (reservationState.isItemSelectorOpen) {
                closeItemSelector();
            } else if (reservationState.isOpen) {
                closeNewReservationModal();
            }
        }
    });

    // Close modals on backdrop click
    document.getElementById('newReservationModal')?.addEventListener('click', function(e) {
        if (e.target === this && reservationState.isOpen) {
            closeNewReservationModal();
        }
    });

    document.getElementById('itemSelectorModal')?.addEventListener('click', function(e) {
        if (e.target === this && reservationState.isItemSelectorOpen) {
            closeItemSelector();
        }
    });
</script>
