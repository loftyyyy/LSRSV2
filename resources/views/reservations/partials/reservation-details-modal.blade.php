{{-- Reservation Details Modal --}}
<div id="reservationDetailsModal" class="hidden fixed inset-0 z-50 flex items-center justify-center px-2 py-4 bg-black/60 backdrop-blur-sm overflow-y-auto">
    <div class="w-full max-w-5xl bg-white dark:bg-neutral-950 border border-neutral-200 dark:border-neutral-800 rounded-3xl shadow-2xl flex flex-col max-h-[calc(100vh-2rem)] my-auto">
        {{-- Header --}}
        <div class="flex-shrink-0 flex items-center justify-between px-6 py-4 border-b border-neutral-200 dark:border-neutral-800 bg-neutral-50/80 dark:bg-neutral-900/50 rounded-t-3xl">
            <div class="flex items-center gap-4">
                {{-- Reservation Icon --}}
                <div id="reservationDetailsIcon" class="h-12 w-12 rounded-full bg-gradient-to-br from-violet-500 to-purple-600 flex items-center justify-center text-white shadow-lg">
                    <x-icon name="calendar" class="h-6 w-6" />
                </div>
                <div>
                    <p class="text-xs uppercase tracking-[0.2em] text-neutral-500 dark:text-neutral-500">Reservation Details</p>
                    <h3 id="reservationDetailsTitle" class="text-lg font-semibold text-neutral-900 dark:text-white">Loading...</h3>
                </div>
            </div>
            <button onclick="closeReservationDetailsModal()" class="text-neutral-500 hover:text-neutral-800 dark:hover:text-neutral-200 text-xl transition-colors duration-200">&times;</button>
        </div>

        {{-- Content (scrollable) --}}
        <div id="reservationDetailsContent" class="flex-1 overflow-y-auto">
            {{-- Loading State --}}
            <div id="reservationDetailsLoading" class="flex items-center justify-center py-16">
                <div class="flex flex-col items-center gap-3">
                    <div class="h-8 w-8 animate-spin rounded-full border-2 border-violet-600 border-t-transparent"></div>
                    <p class="text-sm text-neutral-500 dark:text-neutral-400">Loading reservation details...</p>
                </div>
            </div>

            {{-- Reservation Details (hidden initially) --}}
            <div id="reservationDetailsData" class="hidden">
                {{-- Main Content: Two Column Layout --}}
                <div class="flex flex-col lg:flex-row">
                    {{-- Left Column: Reservation Info --}}
                    <div class="lg:w-1/2 p-6 border-b lg:border-b-0 lg:border-r border-neutral-200 dark:border-neutral-800 space-y-5">
                        {{-- Status & ID Row --}}
                        <div class="grid grid-cols-2 gap-4">
                            {{-- Status Section --}}
                            <div class="space-y-2">
                                <div class="flex items-center gap-2 text-sm font-medium text-neutral-700 dark:text-neutral-300">
                                    <x-icon name="activity" class="h-4 w-4" />
                                    <span>Status</span>
                                </div>
                                <div id="detailReservationStatusCard" class="rounded-xl p-3 border">
                                    <div class="flex items-center gap-2">
                                        <span id="detailReservationStatusDot" class="h-2.5 w-2.5 rounded-full"></span>
                                        <p id="detailReservationStatus" class="text-sm font-semibold">-</p>
                                    </div>
                                </div>
                            </div>

                            {{-- Reservation ID --}}
                            <div class="space-y-2">
                                <div class="flex items-center gap-2 text-sm font-medium text-neutral-700 dark:text-neutral-300">
                                    <x-icon name="hash" class="h-4 w-4" />
                                    <span>Reservation ID</span>
                                </div>
                                <div class="bg-neutral-50 dark:bg-neutral-900/50 rounded-xl p-3 border border-neutral-200 dark:border-neutral-800">
                                    <p id="detailReservationId" class="text-sm font-semibold text-neutral-900 dark:text-white font-geist-mono">-</p>
                                </div>
                            </div>
                        </div>

                        {{-- Customer Information Section --}}
                        <div class="space-y-3">
                            <div class="flex items-center gap-2 text-sm font-medium text-neutral-700 dark:text-neutral-300">
                                <x-icon name="user" class="h-4 w-4" />
                                <span>Customer Information</span>
                            </div>

                            <div class="grid grid-cols-1 gap-3">
                                {{-- Customer Name --}}
                                <div class="bg-neutral-50 dark:bg-neutral-900/50 rounded-xl p-3 border border-neutral-200 dark:border-neutral-800">
                                    <div class="flex items-center gap-1.5 mb-1">
                                        <x-icon name="user" class="h-3 w-3 text-neutral-400" />
                                        <p class="text-xs text-neutral-500 dark:text-neutral-400">Customer Name</p>
                                    </div>
                                    <p id="detailReservationCustomerName" class="text-sm font-medium text-neutral-900 dark:text-white">-</p>
                                </div>

                                {{-- Customer Contact --}}
                                <div class="grid grid-cols-2 gap-3">
                                    <div class="bg-neutral-50 dark:bg-neutral-900/50 rounded-xl p-3 border border-neutral-200 dark:border-neutral-800">
                                        <div class="flex items-center gap-1.5 mb-1">
                                            <x-icon name="mail" class="h-3 w-3 text-neutral-400" />
                                            <p class="text-xs text-neutral-500 dark:text-neutral-400">Email</p>
                                        </div>
                                        <p id="detailReservationCustomerEmail" class="text-sm font-medium text-neutral-900 dark:text-white break-all">-</p>
                                    </div>
                                    <div class="bg-neutral-50 dark:bg-neutral-900/50 rounded-xl p-3 border border-neutral-200 dark:border-neutral-800">
                                        <div class="flex items-center gap-1.5 mb-1">
                                            <x-icon name="phone" class="h-3 w-3 text-neutral-400" />
                                            <p class="text-xs text-neutral-500 dark:text-neutral-400">Contact Number</p>
                                        </div>
                                        <p id="detailReservationCustomerPhone" class="text-sm font-medium text-neutral-900 dark:text-white font-geist-mono">-</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Dates Section --}}
                        <div class="space-y-3">
                            <div class="flex items-center gap-2 text-sm font-medium text-neutral-700 dark:text-neutral-300">
                                <x-icon name="calendar" class="h-4 w-4" />
                                <span>Reservation Dates</span>
                            </div>

                            <div class="grid grid-cols-3 gap-3">
                                {{-- Reservation Date --}}
                                <div class="bg-neutral-50 dark:bg-neutral-900/50 rounded-xl p-3 border border-neutral-200 dark:border-neutral-800">
                                    <div class="flex items-center gap-1.5 mb-1">
                                        <x-icon name="calendar-plus" class="h-3 w-3 text-neutral-400" />
                                        <p class="text-xs text-neutral-500 dark:text-neutral-400">Reserved On</p>
                                    </div>
                                    <p id="detailReservationDate" class="text-sm font-medium text-neutral-900 dark:text-white">-</p>
                                </div>

                                {{-- Start Date --}}
                                <div class="bg-neutral-50 dark:bg-neutral-900/50 rounded-xl p-3 border border-neutral-200 dark:border-neutral-800">
                                    <div class="flex items-center gap-1.5 mb-1">
                                        <x-icon name="calendar-check" class="h-3 w-3 text-neutral-400" />
                                        <p class="text-xs text-neutral-500 dark:text-neutral-400">Pickup Date</p>
                                    </div>
                                    <p id="detailReservationStartDate" class="text-sm font-medium text-neutral-900 dark:text-white">-</p>
                                </div>

                                {{-- End Date --}}
                                <div class="bg-neutral-50 dark:bg-neutral-900/50 rounded-xl p-3 border border-neutral-200 dark:border-neutral-800">
                                    <div class="flex items-center gap-1.5 mb-1">
                                        <x-icon name="calendar-x" class="h-3 w-3 text-neutral-400" />
                                        <p class="text-xs text-neutral-500 dark:text-neutral-400">Return Date</p>
                                    </div>
                                    <p id="detailReservationEndDate" class="text-sm font-medium text-neutral-900 dark:text-white">-</p>
                                </div>
                            </div>
                        </div>

                        {{-- Reserved By Section --}}
                        <div class="space-y-3">
                            <div class="flex items-center gap-2 text-sm font-medium text-neutral-700 dark:text-neutral-300">
                                <x-icon name="user-check" class="h-4 w-4" />
                                <span>Created By</span>
                            </div>

                            <div class="bg-neutral-50 dark:bg-neutral-900/50 rounded-xl p-3 border border-neutral-200 dark:border-neutral-800">
                                <div class="flex items-center gap-3">
                                    <div id="detailReservedByAvatar" class="h-8 w-8 rounded-full bg-gradient-to-br from-violet-500 to-purple-600 flex items-center justify-center text-white text-xs font-semibold">
                                        --
                                    </div>
                                    <div>
                                        <p id="detailReservedByName" class="text-sm font-medium text-neutral-900 dark:text-white">-</p>
                                        <p id="detailReservedByEmail" class="text-xs text-neutral-500 dark:text-neutral-400">-</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Timestamps --}}
                        <div class="pt-4 border-t border-neutral-200 dark:border-neutral-800">
                            <div class="flex items-center gap-2 text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-3">
                                <x-icon name="clock" class="h-4 w-4" />
                                <span>Timeline</span>
                            </div>
                            <div class="grid grid-cols-2 gap-3">
                                <div class="flex items-center gap-2 text-xs">
                                    <div class="h-1.5 w-1.5 rounded-full bg-violet-500"></div>
                                    <span class="text-neutral-500 dark:text-neutral-400">Created:</span>
                                    <span id="detailReservationCreated" class="text-neutral-700 dark:text-neutral-300 font-medium">-</span>
                                </div>
                                <div class="flex items-center gap-2 text-xs">
                                    <div class="h-1.5 w-1.5 rounded-full bg-violet-400"></div>
                                    <span class="text-neutral-500 dark:text-neutral-400">Updated:</span>
                                    <span id="detailReservationUpdated" class="text-neutral-700 dark:text-neutral-300 font-medium">-</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Right Column: Items & Financial --}}
                    <div class="lg:w-1/2 p-6 space-y-5">
                        {{-- Stats Row --}}
                        <div class="grid grid-cols-3 gap-3">
                            {{-- Total Items --}}
                            <div class="bg-neutral-50 dark:bg-neutral-900/50 rounded-xl p-4 border border-neutral-200 dark:border-neutral-800 text-center">
                                <p id="detailTotalItems" class="text-2xl font-bold text-neutral-900 dark:text-white font-geist-mono">0</p>
                                <p class="text-xs text-neutral-500 dark:text-neutral-400 mt-1">Total Items</p>
                            </div>

                            {{-- Total Quantity --}}
                            <div class="bg-neutral-50 dark:bg-neutral-900/50 rounded-xl p-4 border border-neutral-200 dark:border-neutral-800 text-center">
                                <p id="detailTotalQuantity" class="text-2xl font-bold text-neutral-900 dark:text-white font-geist-mono">0</p>
                                <p class="text-xs text-neutral-500 dark:text-neutral-400 mt-1">Total Qty</p>
                            </div>

                            {{-- Total Amount --}}
                            <div class="bg-gradient-to-br from-violet-500/10 to-purple-500/10 dark:from-violet-500/20 dark:to-purple-500/20 rounded-xl p-4 border border-violet-200 dark:border-violet-800/50 text-center">
                                <p id="detailTotalAmount" class="text-2xl font-bold text-violet-600 dark:text-violet-400 font-geist-mono">₱0</p>
                                <p class="text-xs text-violet-600/70 dark:text-violet-400/70 mt-1">Est. Total</p>
                            </div>
                        </div>

                        {{-- Reserved Items Section --}}
                        <div class="space-y-3">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2 text-sm font-medium text-neutral-700 dark:text-neutral-300">
                                    <x-icon name="package" class="h-4 w-4" />
                                    <span>Reserved Items</span>
                                </div>
                                <span id="detailItemsCount" class="text-xs text-neutral-500 dark:text-neutral-400 font-geist-mono">0 items</span>
                            </div>

                            <div id="detailReservedItems" class="bg-neutral-50 dark:bg-neutral-900/50 rounded-xl border border-neutral-200 dark:border-neutral-800 overflow-hidden max-h-64 overflow-y-auto">
                                {{-- Items will be inserted here --}}
                                <div class="p-4 text-center text-sm text-neutral-500 dark:text-neutral-400">
                                    No items found
                                </div>
                            </div>
                        </div>

                        {{-- Related Rentals Section --}}
                        <div class="space-y-3">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2 text-sm font-medium text-neutral-700 dark:text-neutral-300">
                                    <x-icon name="shopping-bag" class="h-4 w-4" />
                                    <span>Related Rentals</span>
                                </div>
                                <span id="detailRentalsCount" class="text-xs text-neutral-500 dark:text-neutral-400 font-geist-mono">0 rentals</span>
                            </div>

                            <div id="detailRelatedRentals" class="bg-neutral-50 dark:bg-neutral-900/50 rounded-xl border border-neutral-200 dark:border-neutral-800 overflow-hidden max-h-40 overflow-y-auto">
                                {{-- Rentals will be inserted here --}}
                                <div class="p-4 text-center text-sm text-neutral-500 dark:text-neutral-400">
                                    No rentals found
                                </div>
                            </div>
                        </div>

                        {{-- Related Invoices Section --}}
                        <div class="space-y-3">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2 text-sm font-medium text-neutral-700 dark:text-neutral-300">
                                    <x-icon name="file-text" class="h-4 w-4" />
                                    <span>Related Invoices</span>
                                </div>
                                <span id="detailInvoicesCount" class="text-xs text-neutral-500 dark:text-neutral-400 font-geist-mono">0 invoices</span>
                            </div>

                            <div id="detailRelatedInvoices" class="bg-neutral-50 dark:bg-neutral-900/50 rounded-xl border border-neutral-200 dark:border-neutral-800 overflow-hidden max-h-40 overflow-y-auto">
                                {{-- Invoices will be inserted here --}}
                                <div class="p-4 text-center text-sm text-neutral-500 dark:text-neutral-400">
                                    No invoices found
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Error State --}}
            <div id="reservationDetailsError" class="hidden p-6">
                <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl p-4 flex items-center gap-3">
                    <x-icon name="alert-circle" class="h-5 w-5 text-red-500 flex-shrink-0" />
                    <p id="reservationDetailsErrorMessage" class="text-sm text-red-600 dark:text-red-400">Failed to load reservation details</p>
                </div>
            </div>
        </div>

        {{-- Footer --}}
        <div class="flex-shrink-0 flex items-center justify-between px-6 py-4 border-t border-neutral-200 dark:border-neutral-800 bg-neutral-50/50 dark:bg-neutral-900/30 rounded-b-3xl">
            <button
                type="button"
                id="reservationDetailsEditBtn"
                onclick="openEditFromReservationDetails()"
                class="inline-flex items-center gap-2 rounded-xl px-4 py-2.5 text-[14px] font-medium bg-violet-600 text-white hover:bg-violet-500 transition-colors duration-100 ease-in-out"
            >
                <x-icon name="edit" class="h-4 w-4" />
                <span>Edit</span>
            </button>
            <button
                type="button"
                onclick="closeReservationDetailsModal()"
                class="inline-flex items-center gap-2 rounded-xl px-4 py-2.5 text-[14px] font-medium border border-neutral-300 bg-white text-neutral-700 hover:bg-neutral-100 dark:border-neutral-700 dark:bg-neutral-900 dark:text-neutral-200 dark:hover:bg-neutral-800 transition-colors duration-100 ease-in-out"
            >
                Close
            </button>
        </div>
    </div>
</div>

<style>
    /* Optimize modal performance */
    #reservationDetailsModal {
        will-change: opacity;
        transform: translateZ(0);
        backface-visibility: hidden;
    }

    #reservationDetailsModal .max-w-5xl {
        will-change: transform;
        transform: translateZ(0);
    }

    /* Custom scrollbar for reservation details content */
    #reservationDetailsContent {
        scrollbar-width: thin;
        scrollbar-color: rgba(155, 155, 155, 0.5) transparent;
    }

    #reservationDetailsContent::-webkit-scrollbar {
        width: 6px;
    }

    #reservationDetailsContent::-webkit-scrollbar-track {
        background: transparent;
    }

    #reservationDetailsContent::-webkit-scrollbar-thumb {
        background-color: rgba(155, 155, 155, 0.5);
        border-radius: 3px;
    }

    #reservationDetailsContent::-webkit-scrollbar-thumb:hover {
        background-color: rgba(155, 155, 155, 0.7);
    }

    .dark #reservationDetailsContent::-webkit-scrollbar-thumb {
        background-color: rgba(100, 100, 100, 0.5);
    }

    .dark #reservationDetailsContent::-webkit-scrollbar-thumb:hover {
        background-color: rgba(100, 100, 100, 0.7);
    }

    /* Custom scrollbar for items/rentals/invoices lists */
    #detailReservedItems,
    #detailRelatedRentals,
    #detailRelatedInvoices {
        scrollbar-width: thin;
        scrollbar-color: rgba(155, 155, 155, 0.3) transparent;
    }

    #detailReservedItems::-webkit-scrollbar,
    #detailRelatedRentals::-webkit-scrollbar,
    #detailRelatedInvoices::-webkit-scrollbar {
        width: 4px;
    }

    #detailReservedItems::-webkit-scrollbar-track,
    #detailRelatedRentals::-webkit-scrollbar-track,
    #detailRelatedInvoices::-webkit-scrollbar-track {
        background: transparent;
    }

    #detailReservedItems::-webkit-scrollbar-thumb,
    #detailRelatedRentals::-webkit-scrollbar-thumb,
    #detailRelatedInvoices::-webkit-scrollbar-thumb {
        background-color: rgba(155, 155, 155, 0.3);
        border-radius: 2px;
    }
</style>

<script>
    // Use globalThis to avoid redeclaration errors when Turbo navigates between pages
    if (!globalThis.reservationDetailsModalState) {
        globalThis.reservationDetailsModalState = {
            isOpen: false,
            currentReservationId: null,
            currentReservation: null
        };
    }

    // Open reservation details modal
    async function openReservationDetailsModal(reservationId) {
        globalThis.reservationDetailsModalState.isOpen = true;
        globalThis.reservationDetailsModalState.currentReservationId = reservationId;

        var modal = document.getElementById('reservationDetailsModal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');

        // Show loading state
        document.getElementById('reservationDetailsLoading').classList.remove('hidden');
        document.getElementById('reservationDetailsData').classList.add('hidden');
        document.getElementById('reservationDetailsError').classList.add('hidden');
        document.getElementById('reservationDetailsTitle').textContent = 'Loading...';

        try {
            var response = await axios.get(`/api/reservations/${reservationId}`);
            var reservation = response.data.data;
            globalThis.reservationDetailsModalState.currentReservation = reservation;

            populateReservationDetails(reservation);

            // Hide loading, show data
            document.getElementById('reservationDetailsLoading').classList.add('hidden');
            document.getElementById('reservationDetailsData').classList.remove('hidden');
        } catch (error) {
            console.error('Error loading reservation details:', error);
            document.getElementById('reservationDetailsLoading').classList.add('hidden');
            document.getElementById('reservationDetailsError').classList.remove('hidden');
            document.getElementById('reservationDetailsErrorMessage').textContent =
                error.response?.data?.message || error.message || 'Failed to load reservation details';
        }
    }

    // Populate reservation details in the modal
    function populateReservationDetails(reservation) {
        // Title
        document.getElementById('reservationDetailsTitle').textContent = `Reservation #${String(reservation.reservation_id).padStart(3, '0')}`;

        // Status with dynamic styling - using neutral palette with subtle color hints
        var statusName = reservation.status?.status_name?.toLowerCase() || 'unknown';
        var statusCard = document.getElementById('detailReservationStatusCard');
        var statusDot = document.getElementById('detailReservationStatusDot');
        var statusText = document.getElementById('detailReservationStatus');

        var statusConfig = {
            'pending': {
                label: 'Pending',
                cardClass: 'bg-amber-50 dark:bg-amber-900/20 border-amber-200 dark:border-amber-800/50 text-amber-700 dark:text-amber-300',
                dotClass: 'bg-amber-500'
            },
            'confirmed': {
                label: 'Confirmed',
                cardClass: 'bg-emerald-50 dark:bg-emerald-900/20 border-emerald-200 dark:border-emerald-800/50 text-emerald-700 dark:text-emerald-300',
                dotClass: 'bg-emerald-500'
            },
            'completed': {
                label: 'Completed',
                cardClass: 'bg-neutral-100 dark:bg-neutral-800/50 border-neutral-300 dark:border-neutral-700 text-neutral-700 dark:text-neutral-300',
                dotClass: 'bg-neutral-500'
            },
            'cancelled': {
                label: 'Cancelled',
                cardClass: 'bg-red-50 dark:bg-red-900/20 border-red-200 dark:border-red-800/50 text-red-700 dark:text-red-300',
                dotClass: 'bg-red-500'
            }
        };

        var config = statusConfig[statusName] || {
            label: reservation.status?.status_name || 'Unknown',
            cardClass: 'bg-neutral-50 dark:bg-neutral-900/20 border-neutral-200 dark:border-neutral-800/50 text-neutral-700 dark:text-neutral-300',
            dotClass: 'bg-neutral-500'
        };
        statusCard.className = `rounded-xl p-3 border ${config.cardClass}`;
        statusDot.className = `h-2.5 w-2.5 rounded-full ${config.dotClass}`;
        statusText.textContent = reservation.status?.status_name || 'Unknown';

        // Reservation ID
        document.getElementById('detailReservationId').textContent = `#${String(reservation.reservation_id).padStart(3, '0')}`;

        // Customer Info
        var customerName = reservation.customer
            ? `${reservation.customer.first_name} ${reservation.customer.last_name}`
            : '-';
        document.getElementById('detailReservationCustomerName').textContent = customerName;
        document.getElementById('detailReservationCustomerEmail').textContent = reservation.customer?.email || '-';
        document.getElementById('detailReservationCustomerPhone').textContent = reservation.customer?.contact_number || '-';

        // Dates
        document.getElementById('detailReservationDate').textContent = reservation.reservation_date
            ? formatReservationDate(reservation.reservation_date)
            : '-';
        document.getElementById('detailReservationStartDate').textContent = reservation.start_date
            ? formatReservationDate(reservation.start_date)
            : '-';
        document.getElementById('detailReservationEndDate').textContent = reservation.end_date
            ? formatReservationDate(reservation.end_date)
            : '-';

        // Reserved By
        var reservedBy = reservation.reserved_by_user || reservation.reservedBy || reservation.reserved_by;
        if (reservedBy && typeof reservedBy === 'object') {
            var initials = (reservedBy.name?.charAt(0) || reservedBy.first_name?.charAt(0) || 'U').toUpperCase();
            document.getElementById('detailReservedByAvatar').textContent = initials;
            document.getElementById('detailReservedByName').textContent = reservedBy.name || `${reservedBy.first_name || ''} ${reservedBy.last_name || ''}`.trim() || '-';
            document.getElementById('detailReservedByEmail').textContent = reservedBy.email || '-';
        } else {
            document.getElementById('detailReservedByAvatar').textContent = '--';
            document.getElementById('detailReservedByName').textContent = '-';
            document.getElementById('detailReservedByEmail').textContent = '-';
        }

        // Timestamps
        document.getElementById('detailReservationCreated').textContent = reservation.created_at
            ? formatReservationDate(reservation.created_at)
            : '-';
        document.getElementById('detailReservationUpdated').textContent = reservation.updated_at
            ? formatReservationDate(reservation.updated_at)
            : '-';

        // Calculate stats
        var items = reservation.items || [];
        var totalItems = items.length;
        var totalQuantity = items.reduce((sum, item) => sum + (item.quantity || 1), 0);
        var totalAmount = items.reduce((sum, item) => sum + ((item.rental_price || 0) * (item.quantity || 1)), 0);

        document.getElementById('detailTotalItems').textContent = totalItems;
        document.getElementById('detailTotalQuantity').textContent = totalQuantity;
        document.getElementById('detailTotalAmount').textContent = `₱${totalAmount.toLocaleString()}`;

        // Render reserved items
        renderReservedItems(items);

        // Render related rentals
        renderRelatedRentals(reservation.rentals || []);

        // Render related invoices
        renderRelatedInvoices(reservation.invoices || []);

        // Update edit button visibility based on status
        var editBtn = document.getElementById('reservationDetailsEditBtn');
        if (statusName === 'cancelled' || statusName === 'completed') {
            editBtn.classList.add('hidden');
        } else {
            editBtn.classList.remove('hidden');
        }
    }

    // Render reserved items
    function renderReservedItems(items) {
        var container = document.getElementById('detailReservedItems');
        var countEl = document.getElementById('detailItemsCount');

        countEl.textContent = `${items.length} item${items.length !== 1 ? 's' : ''}`;

        if (!items || items.length === 0) {
            container.innerHTML = `
                <div class="p-4 text-center text-sm text-neutral-500 dark:text-neutral-400">
                    <svg class="h-6 w-6 text-neutral-300 dark:text-neutral-600 mx-auto mb-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                    No items reserved
                </div>
            `;
            return;
        }

        container.innerHTML = `
            <div class="divide-y divide-neutral-200 dark:divide-neutral-800">
                ${items.map(item => {
                    var variant = item.variant || {};
                    var itemName = variant.name || item.item?.name || 'Unknown Item';
                    var itemDetails = [variant.size, variant.color, variant.design].filter(Boolean).join(' · ');
                    var rentalPrice = item.rental_price || variant.rental_price || 0;
                    var quantity = item.quantity || 1;
                    var subtotal = rentalPrice * quantity;

                    return `
                        <div class="px-4 py-3 flex items-center justify-between hover:bg-neutral-100 dark:hover:bg-neutral-800/50 transition-colors">
                            <div class="flex items-center gap-3 flex-1 min-w-0">
                                <div class="h-10 w-10 rounded-lg bg-neutral-200 dark:bg-neutral-800 flex items-center justify-center flex-shrink-0">
                                    <svg class="h-5 w-5 text-neutral-500 dark:text-neutral-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="text-sm font-medium text-neutral-900 dark:text-white truncate">${itemName}</p>
                                    <p class="text-xs text-neutral-500 dark:text-neutral-400 truncate">${itemDetails || 'No details'}</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-4 flex-shrink-0 ml-4">
                                <div class="text-right">
                                    <p class="text-sm font-semibold text-neutral-900 dark:text-white font-geist-mono">₱${rentalPrice.toLocaleString()}</p>
                                    <p class="text-xs text-neutral-500 dark:text-neutral-400">× ${quantity}</p>
                                </div>
                                <div class="bg-violet-100 dark:bg-violet-900/30 rounded-lg px-2 py-1">
                                    <p class="text-xs font-semibold text-violet-600 dark:text-violet-400 font-geist-mono">₱${subtotal.toLocaleString()}</p>
                                </div>
                            </div>
                        </div>
                    `;
                }).join('')}
            </div>
        `;
    }

    // Render related rentals
    function renderRelatedRentals(rentals) {
        var container = document.getElementById('detailRelatedRentals');
        var countEl = document.getElementById('detailRentalsCount');

        countEl.textContent = `${rentals.length} rental${rentals.length !== 1 ? 's' : ''}`;

        if (!rentals || rentals.length === 0) {
            container.innerHTML = `
                <div class="p-4 text-center text-sm text-neutral-500 dark:text-neutral-400">
                    <svg class="h-6 w-6 text-neutral-300 dark:text-neutral-600 mx-auto mb-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                    No related rentals
                </div>
            `;
            return;
        }

        container.innerHTML = `
            <div class="divide-y divide-neutral-200 dark:divide-neutral-800">
                ${rentals.slice(0, 5).map(rental => {
                    var statusName = rental.status?.status_name?.toLowerCase() || 'unknown';
                    var statusColors = {
                        'active': 'bg-emerald-500/15 text-emerald-600 border-emerald-500/40 dark:text-emerald-300',
                        'completed': 'bg-neutral-500/15 text-neutral-600 border-neutral-500/40 dark:text-neutral-300',
                        'cancelled': 'bg-red-500/15 text-red-600 border-red-500/40 dark:text-red-300',
                        'overdue': 'bg-amber-500/15 text-amber-600 border-amber-500/40 dark:text-amber-300'
                    };
                    var statusColor = statusColors[statusName] || 'bg-neutral-500/15 text-neutral-600 border-neutral-500/40 dark:text-neutral-300';

                    var rentalDate = rental.released_date ? formatReservationDate(rental.released_date) : (rental.created_at ? formatReservationDate(rental.created_at) : '-');

                    return `
                        <div class="px-4 py-3 flex items-center justify-between hover:bg-neutral-100 dark:hover:bg-neutral-800/50 transition-colors">
                            <div class="flex items-center gap-3">
                                <div class="h-8 w-8 rounded-lg bg-neutral-200 dark:bg-neutral-800 flex items-center justify-center flex-shrink-0">
                                    <span class="text-[10px] font-bold text-neutral-600 dark:text-neutral-400 font-geist-mono">#${rental.rental_id}</span>
                                </div>
                                <div class="min-w-0">
                                    <p class="text-sm font-medium text-neutral-900 dark:text-white truncate">${rentalDate}</p>
                                </div>
                            </div>
                            <span class="inline-flex items-center rounded-full ${statusColor} px-2 py-1 text-[10px] font-medium border flex-shrink-0 ml-2">
                                ${rental.status?.status_name || 'Unknown'}
                            </span>
                        </div>
                    `;
                }).join('')}
            </div>
            ${rentals.length > 5 ? `
                <div class="px-4 py-2 bg-neutral-100 dark:bg-neutral-800/50 text-center border-t border-neutral-200 dark:border-neutral-700">
                    <span class="text-xs text-neutral-500 dark:text-neutral-400">+ ${rentals.length - 5} more rentals</span>
                </div>
            ` : ''}
        `;
    }

    // Render related invoices
    function renderRelatedInvoices(invoices) {
        var container = document.getElementById('detailRelatedInvoices');
        var countEl = document.getElementById('detailInvoicesCount');

        countEl.textContent = `${invoices.length} invoice${invoices.length !== 1 ? 's' : ''}`;

        if (!invoices || invoices.length === 0) {
            container.innerHTML = `
                <div class="p-4 text-center text-sm text-neutral-500 dark:text-neutral-400">
                    <svg class="h-6 w-6 text-neutral-300 dark:text-neutral-600 mx-auto mb-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    No related invoices
                </div>
            `;
            return;
        }

        container.innerHTML = `
            <div class="divide-y divide-neutral-200 dark:divide-neutral-800">
                ${invoices.slice(0, 5).map(invoice => {
                    var statusName = invoice.status?.status_name?.toLowerCase() || invoice.payment_status?.toLowerCase() || 'unknown';
                    var statusColors = {
                        'paid': 'bg-emerald-500/15 text-emerald-600 border-emerald-500/40 dark:text-emerald-300',
                        'unpaid': 'bg-amber-500/15 text-amber-600 border-amber-500/40 dark:text-amber-300',
                        'partial': 'bg-neutral-500/15 text-neutral-600 border-neutral-500/40 dark:text-neutral-300',
                        'cancelled': 'bg-red-500/15 text-red-600 border-red-500/40 dark:text-red-300'
                    };
                    var statusColor = statusColors[statusName] || 'bg-neutral-500/15 text-neutral-600 border-neutral-500/40 dark:text-neutral-300';

                    var invoiceDate = invoice.invoice_date ? formatReservationDate(invoice.invoice_date) : '-';
                    var totalAmount = invoice.total_amount || 0;
                    var isPaid = statusName === 'paid';
                    var paymentButtonClass = isPaid 
                        ? 'bg-neutral-100 dark:bg-neutral-800 text-neutral-500 dark:text-neutral-400 cursor-not-allowed' 
                        : 'bg-violet-600 text-white hover:bg-violet-500 dark:hover:bg-violet-700 cursor-pointer';

                    return `
                        <div class="px-4 py-3 flex items-center justify-between hover:bg-neutral-100 dark:hover:bg-neutral-800/50 transition-colors">
                            <div class="flex items-center gap-3 flex-1 min-w-0">
                                <div class="h-8 w-8 rounded-lg bg-neutral-200 dark:bg-neutral-800 flex items-center justify-center flex-shrink-0">
                                    <span class="text-[10px] font-bold text-neutral-600 dark:text-neutral-400 font-geist-mono">#${invoice.invoice_id}</span>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="text-sm font-medium text-neutral-900 dark:text-white truncate">${invoiceDate}</p>
                                    <p class="text-xs text-neutral-500 dark:text-neutral-400 font-geist-mono">₱${totalAmount.toLocaleString()}</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-2 flex-shrink-0 ml-2">
                                ${invoice.invoice_type ? `
                                    <span class="inline-flex items-center rounded-full bg-blue-500/15 text-blue-600 border-blue-500/40 dark:text-blue-300 px-2 py-1 text-[10px] font-medium border capitalize">
                                        ${invoice.invoice_type === 'reservation' ? 'Deposit' : invoice.invoice_type}
                                    </span>
                                ` : ''}
                                <span class="inline-flex items-center rounded-full ${statusColor} px-2 py-1 text-[10px] font-medium border">
                                    ${invoice.status?.status_name || invoice.payment_status || 'Unknown'}
                                </span>
                                ${!isPaid ? `
                                    <a 
                                        href="/payments?invoice_id=${invoice.invoice_id}" 
                                        class="inline-flex items-center rounded-lg px-2.5 py-1 text-[10px] font-medium ${paymentButtonClass} transition-colors duration-100 ease-in-out"
                                        title="Pay this invoice"
                                    >
                                        Pay
                                    </a>
                                ` : `
                                    <button 
                                        disabled 
                                        class="inline-flex items-center rounded-lg px-2.5 py-1 text-[10px] font-medium ${paymentButtonClass}"
                                        title="Already paid"
                                    >
                                        Paid
                                    </button>
                                `}
                            </div>
                        </div>
                    `;
                }).join('')}
            </div>
            ${invoices.length > 5 ? `
                <div class="px-4 py-2 bg-neutral-100 dark:bg-neutral-800/50 text-center border-t border-neutral-200 dark:border-neutral-700">
                    <span class="text-xs text-neutral-500 dark:text-neutral-400">+ ${invoices.length - 5} more invoices</span>
                </div>
            ` : ''}
        `;
    }

    // Format date helper
    function formatReservationDate(dateString) {
        var date = new Date(dateString);
        return date.toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'short',
            day: 'numeric'
        });
    }

    // Close reservation details modal
    function closeReservationDetailsModal() {
        globalThis.reservationDetailsModalState.isOpen = false;
        globalThis.reservationDetailsModalState.currentReservationId = null;
        globalThis.reservationDetailsModalState.currentReservation = null;

        var modal = document.getElementById('reservationDetailsModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    // Open edit modal from details modal
    function openEditFromReservationDetails() {
        var reservationId = globalThis.reservationDetailsModalState.currentReservationId;
        closeReservationDetailsModal();

        // Small delay to allow close animation
        setTimeout(() => {
            if (typeof openEditReservationModal === 'function') {
                openEditReservationModal(reservationId);
            }
        }, 100);
    }

    // Handle keyboard navigation
    document.addEventListener('keydown', function(e) {
        if (!globalThis.reservationDetailsModalState.isOpen) return;

        // Check if other modals are open
        var editModalOpen = document.getElementById('editReservationModal')?.classList.contains('flex');
        var newModalOpen = document.getElementById('newReservationModal')?.classList.contains('flex');

        if (e.key === 'Escape' && !editModalOpen && !newModalOpen) {
            closeReservationDetailsModal();
        }
    });

    // Close modal on backdrop click
    document.getElementById('reservationDetailsModal')?.addEventListener('click', function(e) {
        if (e.target === this && globalThis.reservationDetailsModalState.isOpen) {
            closeReservationDetailsModal();
        }
    });
</script>
