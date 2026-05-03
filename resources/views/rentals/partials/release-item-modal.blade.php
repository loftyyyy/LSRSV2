{{-- Release Item Modal --}}
<div id="releaseItemModal" class="hidden fixed inset-0 z-50 flex items-center justify-center px-2 py-4 bg-black/60 backdrop-blur-sm overflow-y-auto">
    <div class="w-full max-w-2xl bg-white dark:bg-neutral-950 border border-neutral-200 dark:border-neutral-800 rounded-3xl shadow-2xl flex flex-col max-h-[calc(100vh-2rem)] my-auto">
        {{-- Header --}}
        <div class="flex-shrink-0 flex items-center justify-between px-6 py-4 border-b border-neutral-200 dark:border-neutral-800 bg-neutral-50/80 dark:bg-neutral-900/50 rounded-t-3xl">
            <div class="flex items-center gap-4">
                {{-- Release Icon --}}
                <div class="h-12 w-12 rounded-full bg-gradient-to-br from-emerald-500 to-teal-600 flex items-center justify-center text-white shadow-lg">
                    <x-icon name="package-check" class="h-6 w-6" />
                </div>
                <div>
                    <p class="text-xs uppercase tracking-[0.2em] text-neutral-500 dark:text-neutral-500">Release</p>
                    <h3 class="text-lg font-semibold text-neutral-900 dark:text-white">Release Item to Customer</h3>
                </div>
            </div>
            <button onclick="closeReleaseItemModal()" class="text-neutral-500 hover:text-neutral-800 dark:hover:text-neutral-200 text-xl transition-colors duration-200">&times;</button>
        </div>

        {{-- Content --}}
        <div class="flex-1 overflow-y-auto p-6">
            {{-- Loading State --}}
            <div id="releaseItemLoading" class="hidden items-center justify-center py-12">
                <div class="flex flex-col items-center gap-3">
                    <div class="h-8 w-8 animate-spin rounded-full border-2 border-emerald-600 border-t-transparent"></div>
                    <p class="text-sm text-neutral-500 dark:text-neutral-400">Loading...</p>
                </div>
            </div>

            {{-- Step 1: Select Reservation --}}
            <div id="releaseStep1" class="space-y-5">
                <div class="text-sm text-neutral-600 dark:text-neutral-400 mb-4">
                    Select a confirmed reservation to release items from, or release directly without a reservation.
                </div>

                {{-- Search Reservations --}}
                <div>
                    <label class="block">
                        <span class="text-sm font-medium text-neutral-700 dark:text-neutral-300 flex items-center gap-2">
                            <x-icon name="search" class="h-4 w-4" />
                            Search Reservation
                        </span>
                        <input type="text" id="releaseSearchReservation" placeholder="Search by customer name, reservation ID..."
                               class="mt-2 w-full rounded-xl border border-neutral-300 bg-white px-4 py-3 text-sm text-neutral-900 placeholder-neutral-400 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 dark:border-neutral-700 dark:bg-neutral-900 dark:text-white dark:placeholder-neutral-500 transition-colors duration-200" />
                    </label>
                </div>

                {{-- Reservation List --}}
                <div id="releaseReservationList" class="space-y-2 max-h-60 overflow-y-auto">
                    {{-- Reservations will be loaded here --}}
                </div>

                <div class="pt-4 border-t border-neutral-200 dark:border-neutral-800">
                    <button type="button" onclick="proceedWithoutReservation()" class="w-full px-4 py-3 text-sm font-medium text-neutral-700 dark:text-neutral-300 border border-dashed border-neutral-300 dark:border-neutral-700 rounded-xl hover:bg-neutral-50 dark:hover:bg-neutral-900 transition-colors duration-200 flex items-center justify-center gap-2">
                        <x-icon name="plus" class="h-4 w-4" />
                        Release Without Reservation
                    </button>
                </div>
            </div>

            {{-- Step 2: Release Form --}}
            <form id="releaseItemForm" class="hidden space-y-5">
                {{-- Selected Reservation Info --}}
                <div id="releaseSelectedReservation" class="hidden rounded-xl border border-neutral-200 dark:border-neutral-800 bg-neutral-50/50 dark:bg-neutral-900/30 p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs text-neutral-500 dark:text-neutral-500">Selected Reservation</p>
                            <p id="releaseReservationInfo" class="text-sm font-semibold text-neutral-900 dark:text-white"></p>
                        </div>
                        <button type="button" onclick="backToReservationSelect()" class="text-xs text-violet-600 hover:text-violet-700 dark:text-violet-400">
                            Change
                        </button>
                    </div>
                </div>

                <input type="hidden" id="releaseReservationId" name="reservation_id" value="" />
                <input type="hidden" id="releaseReservationItemId" name="reservation_item_id" value="" />

                {{-- Customer Selection (for walk-in) --}}
                <div id="releaseCustomerSelect" class="hidden">
                    <label class="block">
                        <span class="text-sm font-medium text-neutral-700 dark:text-neutral-300 flex items-center gap-2">
                            <x-icon name="user" class="h-4 w-4" />
                            Customer <span class="text-rose-500">*</span>
                        </span>
                        <select name="customer_id" id="releaseCustomerId" required
                                class="mt-2 w-full rounded-xl border border-neutral-300 bg-white px-4 py-3 text-sm text-neutral-900 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 dark:border-neutral-700 dark:bg-neutral-900 dark:text-white transition-colors duration-200">
                            <option value="">Select a customer...</option>
                        </select>
                    </label>
                </div>

                {{-- Customer Info (when from reservation) --}}
                <div id="releaseCustomerInfo" class="hidden">
                    <p class="text-xs text-neutral-500 dark:text-neutral-500 mb-1">Customer</p>
                    <p id="releaseCustomerName" class="text-sm font-medium text-neutral-900 dark:text-white"></p>
                    <input type="hidden" id="releaseCustomerIdHidden" name="customer_id" value="" />
                </div>

                {{-- Item Selection --}}
                <div>
                    <label class="block">
                        <span class="text-sm font-medium text-neutral-700 dark:text-neutral-300 flex items-center gap-2">
                            <x-icon name="shirt" class="h-4 w-4" />
                            Item to Release <span class="text-rose-500">*</span>
                        </span>
                        <select name="item_id" id="releaseItemId" required
                                class="mt-2 w-full rounded-xl border border-neutral-300 bg-white px-4 py-3 text-sm text-neutral-900 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 dark:border-neutral-700 dark:bg-neutral-900 dark:text-white transition-colors duration-200">
                            <option value="">Select an item...</option>
                        </select>
                    </label>
                </div>

                {{-- Dates --}}
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block">
                            <span class="text-sm font-medium text-neutral-700 dark:text-neutral-300 flex items-center gap-2">
                                <x-icon name="calendar" class="h-4 w-4" />
                                Release Date <span class="text-rose-500">*</span>
                            </span>
                            <input type="date" name="released_date" id="releaseReleasedDate" required
                                   class="mt-2 w-full rounded-xl border border-neutral-300 bg-white px-4 py-3 text-sm text-neutral-900 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 dark:border-neutral-700 dark:bg-neutral-900 dark:text-white transition-colors duration-200" />
                        </label>
                    </div>
                    <div>
                        <label class="block">
                            <span class="text-sm font-medium text-neutral-700 dark:text-neutral-300 flex items-center gap-2">
                                <x-icon name="calendar-clock" class="h-4 w-4" />
                                Due Date <span class="text-rose-500">*</span>
                            </span>
                            <input type="date" name="due_date" id="releaseDueDate" required
                                   class="mt-2 w-full rounded-xl border border-neutral-300 bg-white px-4 py-3 text-sm text-neutral-900 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 dark:border-neutral-700 dark:bg-neutral-900 dark:text-white transition-colors duration-200" />
                        </label>
                    </div>
                </div>

                {{-- Release Notes --}}
                <div>
                    <label class="block">
                        <span class="text-sm font-medium text-neutral-700 dark:text-neutral-300 flex items-center gap-2">
                            <x-icon name="file-text" class="h-4 w-4" />
                            Release Notes
                        </span>
                        <textarea name="release_notes" id="releaseNotes" rows="2" placeholder="Any notes about this release..."
                                  class="mt-2 w-full rounded-xl border border-neutral-300 bg-white px-4 py-3 text-sm text-neutral-900 placeholder-neutral-400 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 dark:border-neutral-700 dark:bg-neutral-900 dark:text-white dark:placeholder-neutral-500 resize-none transition-colors duration-200"></textarea>
                    </label>
                </div>

                {{-- Error Display --}}
                <div id="releaseItemError" class="hidden p-4 rounded-xl bg-rose-50 dark:bg-rose-900/20 border border-rose-200 dark:border-rose-800/50">
                    <div class="flex items-center gap-2 text-rose-700 dark:text-rose-400">
                        <x-icon name="alert-circle" class="h-5 w-5 flex-shrink-0" />
                        <p id="releaseItemErrorText" class="text-sm font-medium"></p>
                    </div>
                </div>
            </form>
        </div>

        {{-- Footer --}}
        <div class="flex-shrink-0 flex items-center justify-end gap-3 px-6 py-4 border-t border-neutral-200 dark:border-neutral-800 bg-neutral-50/80 dark:bg-neutral-900/50 rounded-b-3xl">
            <button type="button" onclick="closeReleaseItemModal()" class="px-5 py-2.5 text-sm font-medium text-neutral-700 dark:text-neutral-300 hover:bg-neutral-100 dark:hover:bg-neutral-800 rounded-xl transition-colors duration-200">
                Cancel
            </button>
            <button type="button" id="releaseItemSubmitBtn" onclick="submitReleaseItem()" disabled
                    class="hidden px-5 py-2.5 text-sm font-medium text-white dark:text-black bg-emerald-600 hover:bg-emerald-700 disabled:bg-neutral-300 disabled:text-neutral-500 dark:disabled:bg-neutral-700 dark:disabled:text-neutral-500 rounded-xl transition-colors duration-200 flex items-center gap-2">
                <x-icon name="package-check" class="h-4 w-4" />
                <span>Release Item</span>
            </button>
        </div>
    </div>
</div>

<script>
    // Release Item Modal State
    globalThis.releaseItemModalState = {
        isOpen: false,
        step: 1,
        selectedReservation: null,
        reservations: [],
        customers: [],
        items: [],
        isSubmitting: false
    };

    // Open release item modal
    globalThis.openReleaseItemModal = function openReleaseItemModal() {
        globalThis.releaseItemModalState.isOpen = true;
        globalThis.releaseItemModalState.step = 1;
        globalThis.releaseItemModalState.selectedReservation = null;

        var releaseStep1 = document.getElementById('releaseStep1');
        if (releaseStep1) releaseStep1.classList.remove('hidden');

        var releaseItemForm = document.getElementById('releaseItemForm');
        if (releaseItemForm) releaseItemForm.classList.add('hidden');

        var releaseItemSubmitBtn = document.getElementById('releaseItemSubmitBtn');
        if (releaseItemSubmitBtn) releaseItemSubmitBtn.classList.add('hidden');

        var releaseItemError = document.getElementById('releaseItemError');
        if (releaseItemError) releaseItemError.classList.add('hidden');

        var releaseSearchReservation = document.getElementById('releaseSearchReservation');
        if (releaseSearchReservation) releaseSearchReservation.value = '';

        var modal = document.getElementById('releaseItemModal');
        if (modal) {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        loadConfirmedReservations();
    };

    // Close release item modal
    globalThis.closeReleaseItemModal = function closeReleaseItemModal() {
        globalThis.releaseItemModalState.isOpen = false;

        var modal = document.getElementById('releaseItemModal');
        if (modal) {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }
    };

    // ── Attach event listeners safely after DOM is ready ──────────────────────
    var releaseSearchTimer;

    document.addEventListener('DOMContentLoaded', function() {
        var releaseSearchInput = document.getElementById('releaseSearchReservation');
        if (releaseSearchInput) {
            releaseSearchInput.addEventListener('input', function() {
                clearTimeout(releaseSearchTimer);
                var query = this.value;
                releaseSearchTimer = setTimeout(function() {
                    loadConfirmedReservations(query);
                }, 300);
            });
        }
    });

    // Load confirmed reservations that can be released
    function loadConfirmedReservations(searchQuery) {
        var listContainer = document.getElementById('releaseReservationList');
        listContainer.innerHTML = '<div class="flex items-center justify-center py-8"><div class="h-6 w-6 animate-spin rounded-full border-2 border-emerald-600 border-t-transparent"></div></div>';

        var url = '/api/reservations?status=confirmed&per_page=20';
        if (searchQuery) {
            url += '&search=' + encodeURIComponent(searchQuery);
        }

        axios.get(url)
            .then(function(response) {
                var reservations = response.data.data || [];
                globalThis.releaseItemModalState.reservations = reservations;
                renderReservationList(reservations);
            })
            .catch(function(error) {
                console.error('Error loading reservations:', error);
                listContainer.innerHTML = '<p class="text-center text-sm text-neutral-500 dark:text-neutral-400 py-4">Failed to load reservations</p>';
            });
    }

    // Render reservation list
    function renderReservationList(reservations) {
        var listContainer = document.getElementById('releaseReservationList');

        if (reservations.length === 0) {
            listContainer.innerHTML = '<p class="text-center text-sm text-neutral-500 dark:text-neutral-400 py-4">No confirmed reservations found</p>';
            return;
        }

        var html = '';
        reservations.forEach(function(res) {
            var customerName = res.customer ? (res.customer.first_name + ' ' + res.customer.last_name) : 'Unknown';
            var eventDate = res.event_date ? new Date(res.event_date).toLocaleDateString() : 'N/A';
            var itemCount = res.items ? res.items.length : 0;

            html += '<div class="p-3 rounded-xl border border-neutral-200 dark:border-neutral-800 hover:border-emerald-500 dark:hover:border-emerald-500 cursor-pointer transition-colors duration-200" onclick="selectReservation(' + res.reservation_id + ')">';
            html += '  <div class="flex items-center justify-between">';
            html += '    <div>';
            html += '      <p class="text-sm font-medium text-neutral-900 dark:text-white">' + customerName + '</p>';
            html += '      <p class="text-xs text-neutral-500 dark:text-neutral-400">Res #' + res.reservation_id + ' • Event: ' + eventDate + '</p>';
            html += '    </div>';
            html += '    <span class="text-xs px-2 py-1 rounded-full bg-violet-100 text-violet-700 dark:bg-violet-900/30 dark:text-violet-400">' + itemCount + ' items</span>';
            html += '  </div>';
            html += '</div>';
        });

        listContainer.innerHTML = html;
    }

    // Select a reservation
    function selectReservation(reservationId) {
        var reservation = globalThis.releaseItemModalState.reservations.find(function(r) {
            return r.reservation_id === reservationId;
        });

        if (!reservation) return;

        globalThis.releaseItemModalState.selectedReservation = reservation;
        globalThis.releaseItemModalState.step = 2;

        document.getElementById('releaseStep1').classList.add('hidden');
        document.getElementById('releaseItemForm').classList.remove('hidden');
        document.getElementById('releaseItemSubmitBtn').classList.remove('hidden');
        document.getElementById('releaseItemSubmitBtn').classList.add('flex');

        document.getElementById('releaseReservationId').value = reservation.reservation_id;
        document.getElementById('releaseSelectedReservation').classList.remove('hidden');
        document.getElementById('releaseReservationInfo').textContent = '#' + reservation.reservation_id + ' - ' + (reservation.customer ? reservation.customer.first_name + ' ' + reservation.customer.last_name : 'Unknown');

        document.getElementById('releaseCustomerSelect').classList.add('hidden');
        document.getElementById('releaseCustomerInfo').classList.remove('hidden');
        document.getElementById('releaseCustomerName').textContent = reservation.customer ? (reservation.customer.first_name + ' ' + reservation.customer.last_name) : 'Unknown';
        document.getElementById('releaseCustomerIdHidden').value = reservation.customer_id;

        loadReservationItems(reservation);

        if (reservation.start_date) {
            var startDate = new Date(reservation.start_date);
            document.getElementById('releaseReleasedDate').value = startDate.getFullYear() + '-' + String(startDate.getMonth() + 1).padStart(2, '0') + '-' + String(startDate.getDate()).padStart(2, '0');
        } else {
            var today = new Date();
            document.getElementById('releaseReleasedDate').value = today.getFullYear() + '-' + String(today.getMonth() + 1).padStart(2, '0') + '-' + String(today.getDate()).padStart(2, '0');
        }

        if (reservation.end_date) {
            var endDate = new Date(reservation.end_date);
            document.getElementById('releaseDueDate').value = endDate.getFullYear() + '-' + String(endDate.getMonth() + 1).padStart(2, '0') + '-' + String(endDate.getDate()).padStart(2, '0');
        } else {
            var dueDate = new Date();
            dueDate.setDate(dueDate.getDate() + 3);
            document.getElementById('releaseDueDate').value = dueDate.getFullYear() + '-' + String(dueDate.getMonth() + 1).padStart(2, '0') + '-' + String(dueDate.getDate()).padStart(2, '0');
        }

        document.getElementById('releaseItemSubmitBtn').disabled = false;
    }

    // Proceed without reservation (walk-in)
    function proceedWithoutReservation() {
        globalThis.releaseItemModalState.selectedReservation = null;
        globalThis.releaseItemModalState.step = 2;

        document.getElementById('releaseStep1').classList.add('hidden');
        document.getElementById('releaseItemForm').classList.remove('hidden');
        document.getElementById('releaseItemSubmitBtn').classList.remove('hidden');
        document.getElementById('releaseItemSubmitBtn').classList.add('flex');

        document.getElementById('releaseSelectedReservation').classList.add('hidden');
        document.getElementById('releaseReservationId').value = '';

        document.getElementById('releaseCustomerSelect').classList.remove('hidden');
        document.getElementById('releaseCustomerInfo').classList.add('hidden');

        loadCustomersForRelease();
        loadAvailableItems();

        var today = new Date();
        document.getElementById('releaseReleasedDate').value = today.getFullYear() + '-' + String(today.getMonth() + 1).padStart(2, '0') + '-' + String(today.getDate()).padStart(2, '0');

        var dueDate = new Date();
        dueDate.setDate(dueDate.getDate() + 3);
        document.getElementById('releaseDueDate').value = dueDate.getFullYear() + '-' + String(dueDate.getMonth() + 1).padStart(2, '0') + '-' + String(dueDate.getDate()).padStart(2, '0');

        document.getElementById('releaseItemSubmitBtn').disabled = false;
    }

    // Go back to reservation selection
    function backToReservationSelect() {
        globalThis.releaseItemModalState.step = 1;

        document.getElementById('releaseStep1').classList.remove('hidden');
        document.getElementById('releaseItemForm').classList.add('hidden');
        document.getElementById('releaseItemSubmitBtn').classList.add('hidden');
    }

    // Load reservation items
    function loadReservationItems(reservation) {
        var itemSelect = document.getElementById('releaseItemId');
        itemSelect.innerHTML = '<option value="">Loading items...</option>';

        if (reservation.items && reservation.items.length > 0) {
            var html = '<option value="">Select an item to release...</option>';
            reservation.items.forEach(function(item) {
                if (item.fulfillment_status !== 'fulfilled') {
                    var itemName = item.variant ? item.variant.name : 'Unknown Item';
                    html += '<option value="0" data-reservation-item-id="' + item.reservation_item_id + '" data-variant-id="' + (item.variant_id || '') + '">' + itemName + '</option>';
                }
            });
            itemSelect.innerHTML = html;

            if (html === '<option value="">Select an item to release...</option>') {
                itemSelect.innerHTML = '<option value="">No unreleased items available</option>';
            }
        } else {
            itemSelect.innerHTML = '<option value="">No items available</option>';
        }

        itemSelect.onchange = function() {
            var selected = itemSelect.options[itemSelect.selectedIndex];
            document.getElementById('releaseReservationItemId').value = selected.dataset.reservationItemId || '';
        };
    }

    // Load customers for walk-in release
    function loadCustomersForRelease() {
        var customerSelect = document.getElementById('releaseCustomerId');
        customerSelect.innerHTML = '<option value="">Loading customers...</option>';

        axios.get('/api/customers?status=active&per_page=100')
            .then(function(response) {
                var customers = response.data.data || [];
                var html = '<option value="">Select a customer...</option>';
                customers.forEach(function(customer) {
                    html += '<option value="' + customer.customer_id + '">' + customer.first_name + ' ' + customer.last_name + ' (' + (customer.email || customer.contact_number || 'N/A') + ')</option>';
                });
                customerSelect.innerHTML = html;
            })
            .catch(function(error) {
                console.error('Error loading customers:', error);
                customerSelect.innerHTML = '<option value="">Failed to load customers</option>';
            });
    }

    // Load available items for walk-in release
    function loadAvailableItems() {
        var itemSelect = document.getElementById('releaseItemId');
        itemSelect.innerHTML = '<option value="">Loading available items...</option>';

        axios.get('/api/inventories/available')
            .then(function(response) {
                var items = response.data.data || response.data || [];
                var html = '<option value="">Select an item to release...</option>';

                if (Array.isArray(items)) {
                    items.forEach(function(item) {
                        var itemName = item.name || item.sku || 'Item #' + item.item_id;
                        html += '<option value="' + item.item_id + '">' + itemName + '</option>';
                    });
                }

                itemSelect.innerHTML = html;

                itemSelect.onchange = function() {
                    // Just update the form when item is selected
                };
            })
            .catch(function(error) {
                console.error('Error loading available items:', error);
                itemSelect.innerHTML = '<option value="">Failed to load items</option>';
            });
    }

    // Submit release item
    function submitReleaseItem() {
        if (globalThis.releaseItemModalState.isSubmitting) return;

        document.getElementById('releaseItemError').classList.add('hidden');

        var customerId = document.getElementById('releaseCustomerIdHidden').value || document.getElementById('releaseCustomerId').value;
        var itemId = document.getElementById('releaseItemId').value;
        var reservationItemId = document.getElementById('releaseReservationItemId').value;
        var releasedDate = document.getElementById('releaseReleasedDate').value;
        var dueDate = document.getElementById('releaseDueDate').value;

        if (!customerId) {
            showReleaseError('Please select a customer');
            return;
        }
        if (!itemId && !reservationItemId) {
            showReleaseError('Please select an item to release');
            return;
        }
        if (!releasedDate || !dueDate) {
            showReleaseError('Please provide release and due dates');
            return;
        }

        globalThis.releaseItemModalState.isSubmitting = true;

        var submitBtn = document.getElementById('releaseItemSubmitBtn');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="h-4 w-4 animate-spin rounded-full border-2 border-white border-t-transparent"></span><span>Releasing...</span>';

        var payload = {
            customer_id: customerId,
            released_date: releasedDate,
            due_date: dueDate,
            release_notes: document.getElementById('releaseNotes').value.trim() || null,
            collect_rental_payment: false
        };

        if (document.getElementById('releaseReservationId').value) {
            payload.reservation_id = document.getElementById('releaseReservationId').value;
        }
        if (reservationItemId) {
            payload.reservation_item_id = reservationItemId;
        } else if (itemId && itemId !== '0') {
            payload.item_id = parseInt(itemId);
        }

        axios.post('/api/rentals/release', payload)
            .then(function(response) {
                if (response.data.data || response.data.message) {
                    closeReleaseItemModal();
                    if (typeof fetchRentals === 'function') {
                        fetchRentals();
                    }
                    if (typeof fetchRentalStats === 'function') {
                        fetchRentalStats();
                    }
                } else {
                    showReleaseError(response.data.message || 'Failed to release item');
                }
            })
            .catch(function(error) {
                console.error('Error releasing item:', error);
                showReleaseError(error.response?.data?.message || 'Failed to release item');
            })
            .finally(function() {
                globalThis.releaseItemModalState.isSubmitting = false;
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m7.5 4.27 9 5.15"/><path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"/><path d="m3.3 7 8.7 5 8.7-5"/><path d="M12 22V12"/><path d="m16 13 4-4"/><path d="m20 13-4-4"/></svg><span>Release Item</span>';
            });
    }

    // Show release error
    function showReleaseError(message) {
        document.getElementById('releaseItemErrorText').textContent = message;
        document.getElementById('releaseItemError').classList.remove('hidden');
    }
</script>
