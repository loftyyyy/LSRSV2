{{-- Customer Details Modal --}}
<div id="customerDetailsModal" class="hidden fixed inset-0 z-50 flex items-center justify-center px-2 py-4 bg-black/60 backdrop-blur-sm overflow-y-auto">
    <div class="w-full max-w-4xl bg-white dark:bg-neutral-950 border border-neutral-200 dark:border-neutral-800 rounded-3xl shadow-2xl flex flex-col max-h-[calc(100vh-2rem)] my-auto">
        {{-- Header --}}
        <div class="flex-shrink-0 flex items-center justify-between px-6 py-4 border-b border-neutral-200 dark:border-neutral-800 bg-neutral-50/80 dark:bg-neutral-900/50 rounded-t-3xl">
            <div>
                <p class="text-xs uppercase tracking-[0.2em] text-neutral-500 dark:text-neutral-500">Customer Details</p>
                <h3 id="customerDetailsTitle" class="text-lg font-semibold text-neutral-900 dark:text-white">Loading...</h3>
            </div>
            <button onclick="closeCustomerDetailsModal()" class="text-neutral-500 hover:text-neutral-800 dark:hover:text-neutral-200 text-xl transition-colors duration-200">&times;</button>
        </div>

        {{-- Content (scrollable) --}}
        <div id="customerDetailsContent" class="flex-1 overflow-y-auto">
            {{-- Loading State --}}
            <div id="customerDetailsLoading" class="flex items-center justify-center py-16">
                <div class="flex flex-col items-center gap-3">
                    <div class="h-8 w-8 animate-spin rounded-full border-2 border-violet-600 border-t-transparent"></div>
                    <p class="text-sm text-neutral-500 dark:text-neutral-400">Loading customer details...</p>
                </div>
            </div>

            {{-- Customer Details (hidden initially) --}}
            <div id="customerDetailsData" class="hidden">
                {{-- Main Content --}}
                <div class="p-6 space-y-6">
                    {{-- Status & Stats Row --}}
                    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                        {{-- Status Section --}}
                        <div class="space-y-2">
                            <div class="flex items-center gap-2 text-sm font-medium text-neutral-700 dark:text-neutral-300">
                                <x-icon name="activity" class="h-4 w-4" />
                                <span>Status</span>
                            </div>
                            <div id="detailCustomerStatusCard" class="rounded-xl p-3 border">
                                <div class="flex items-center gap-2">
                                    <span id="detailCustomerStatusDot" class="h-2.5 w-2.5 rounded-full"></span>
                                    <p id="detailCustomerStatus" class="text-sm font-semibold">-</p>
                                </div>
                            </div>
                        </div>

                        {{-- Total Rentals --}}
                        <div class="space-y-2">
                            <div class="flex items-center gap-2 text-sm font-medium text-neutral-700 dark:text-neutral-300">
                                <x-icon name="shopping-bag" class="h-4 w-4" />
                                <span>Total Rentals</span>
                            </div>
                            <div class="bg-gradient-to-br from-violet-500/10 to-purple-500/10 dark:from-violet-500/20 dark:to-purple-500/20 rounded-xl p-3 border border-violet-200 dark:border-violet-800/50">
                                <p id="detailTotalRentals" class="text-xl font-bold text-violet-600 dark:text-violet-400 font-geist-mono">0</p>
                            </div>
                        </div>

                        {{-- Active Rentals --}}
                        <div class="space-y-2">
                            <div class="flex items-center gap-2 text-sm font-medium text-neutral-700 dark:text-neutral-300">
                                <x-icon name="clock" class="h-4 w-4" />
                                <span>Active Rentals</span>
                            </div>
                            <div class="bg-gradient-to-br from-amber-500/10 to-orange-500/10 dark:from-amber-500/20 dark:to-orange-500/20 rounded-xl p-3 border border-amber-200 dark:border-amber-800/50">
                                <p id="detailActiveRentals" class="text-xl font-bold text-amber-600 dark:text-amber-400 font-geist-mono">0</p>
                            </div>
                        </div>

                        {{-- Total Reservations --}}
                        <div class="space-y-2">
                            <div class="flex items-center gap-2 text-sm font-medium text-neutral-700 dark:text-neutral-300">
                                <x-icon name="calendar" class="h-4 w-4" />
                                <span>Reservations</span>
                            </div>
                            <div class="bg-gradient-to-br from-cyan-500/10 to-blue-500/10 dark:from-cyan-500/20 dark:to-blue-500/20 rounded-xl p-3 border border-cyan-200 dark:border-cyan-800/50">
                                <p id="detailTotalReservations" class="text-xl font-bold text-cyan-600 dark:text-cyan-400 font-geist-mono">0</p>
                            </div>
                        </div>
                    </div>

                    {{-- Customer Information Section --}}
                    <div class="space-y-3">
                        <div class="flex items-center gap-2 text-sm font-medium text-neutral-700 dark:text-neutral-300">
                            <x-icon name="user" class="h-4 w-4" />
                            <span>Customer Information</span>
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            {{-- Customer ID --}}
                            <div class="bg-neutral-50 dark:bg-neutral-900/50 rounded-xl p-3 border border-neutral-200 dark:border-neutral-800">
                                <div class="flex items-center gap-1.5 mb-1">
                                    <x-icon name="hash" class="h-3 w-3 text-neutral-400" />
                                    <p class="text-xs text-neutral-500 dark:text-neutral-400">Customer ID</p>
                                </div>
                                <p id="detailCustomerId" class="text-sm font-medium text-neutral-900 dark:text-white font-geist-mono">-</p>
                            </div>

                            {{-- Full Name --}}
                            <div class="bg-neutral-50 dark:bg-neutral-900/50 rounded-xl p-3 border border-neutral-200 dark:border-neutral-800">
                                <div class="flex items-center gap-1.5 mb-1">
                                    <x-icon name="user" class="h-3 w-3 text-neutral-400" />
                                    <p class="text-xs text-neutral-500 dark:text-neutral-400">Full Name</p>
                                </div>
                                <p id="detailCustomerName" class="text-sm font-medium text-neutral-900 dark:text-white">-</p>
                            </div>
                        </div>
                    </div>

                    {{-- Contact Information Section --}}
                    <div class="space-y-3">
                        <div class="flex items-center gap-2 text-sm font-medium text-neutral-700 dark:text-neutral-300">
                            <x-icon name="mail" class="h-4 w-4" />
                            <span>Contact Information</span>
                        </div>

                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-3">
                            {{-- Email --}}
                            <div class="bg-neutral-50 dark:bg-neutral-900/50 rounded-xl p-3 border border-neutral-200 dark:border-neutral-800">
                                <div class="flex items-center gap-1.5 mb-1">
                                    <x-icon name="mail" class="h-3 w-3 text-neutral-400" />
                                    <p class="text-xs text-neutral-500 dark:text-neutral-400">Email Address</p>
                                </div>
                                <p id="detailCustomerEmail" class="text-sm font-medium text-neutral-900 dark:text-white break-all">-</p>
                            </div>

                            {{-- Phone --}}
                            <div class="bg-neutral-50 dark:bg-neutral-900/50 rounded-xl p-3 border border-neutral-200 dark:border-neutral-800">
                                <div class="flex items-center gap-1.5 mb-1">
                                    <x-icon name="phone" class="h-3 w-3 text-neutral-400" />
                                    <p class="text-xs text-neutral-500 dark:text-neutral-400">Contact Number</p>
                                </div>
                                <p id="detailCustomerPhone" class="text-sm font-medium text-neutral-900 dark:text-white font-geist-mono">-</p>
                            </div>

                            {{-- Address (full width) --}}
                            <div class="lg:col-span-2 bg-neutral-50 dark:bg-neutral-900/50 rounded-xl p-3 border border-neutral-200 dark:border-neutral-800">
                                <div class="flex items-center gap-1.5 mb-1">
                                    <x-icon name="map-pin" class="h-3 w-3 text-neutral-400" />
                                    <p class="text-xs text-neutral-500 dark:text-neutral-400">Address</p>
                                </div>
                                <p id="detailCustomerAddress" class="text-sm font-medium text-neutral-900 dark:text-white">-</p>
                            </div>
                        </div>
                    </div>

                    {{-- Recent Rentals Section --}}
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2 text-sm font-medium text-neutral-700 dark:text-neutral-300">
                                <x-icon name="shopping-bag" class="h-4 w-4" />
                                <span>Recent Rentals</span>
                            </div>
                            <span id="detailRentalsCount" class="text-xs text-neutral-500 dark:text-neutral-400 font-geist-mono">0 rentals</span>
                        </div>

                        <div id="detailRecentRentals" class="bg-neutral-50 dark:bg-neutral-900/50 rounded-xl border border-neutral-200 dark:border-neutral-800 overflow-hidden">
                            {{-- Rentals will be inserted here --}}
                            <div class="p-4 text-center text-sm text-neutral-500 dark:text-neutral-400">
                                No rentals found
                            </div>
                        </div>
                    </div>

                    {{-- Recent Reservations Section --}}
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2 text-sm font-medium text-neutral-700 dark:text-neutral-300">
                                <x-icon name="calendar" class="h-4 w-4" />
                                <span>Recent Reservations</span>
                            </div>
                            <span id="detailReservationsCount" class="text-xs text-neutral-500 dark:text-neutral-400 font-geist-mono">0 reservations</span>
                        </div>

                        <div id="detailRecentReservations" class="bg-neutral-50 dark:bg-neutral-900/50 rounded-xl border border-neutral-200 dark:border-neutral-800 overflow-hidden">
                            {{-- Reservations will be inserted here --}}
                            <div class="p-4 text-center text-sm text-neutral-500 dark:text-neutral-400">
                                No reservations found
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
                                <div class="h-1.5 w-1.5 rounded-full bg-emerald-500"></div>
                                <span class="text-neutral-500 dark:text-neutral-400">Registered:</span>
                                <span id="detailCustomerCreated" class="text-neutral-700 dark:text-neutral-300 font-medium">-</span>
                            </div>
                            <div class="flex items-center gap-2 text-xs">
                                <div class="h-1.5 w-1.5 rounded-full bg-blue-500"></div>
                                <span class="text-neutral-500 dark:text-neutral-400">Updated:</span>
                                <span id="detailCustomerUpdated" class="text-neutral-700 dark:text-neutral-300 font-medium">-</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Error State --}}
            <div id="customerDetailsError" class="hidden p-6">
                <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl p-4 flex items-center gap-3">
                    <x-icon name="alert-circle" class="h-5 w-5 text-red-500 flex-shrink-0" />
                    <p id="customerDetailsErrorMessage" class="text-sm text-red-600 dark:text-red-400">Failed to load customer details</p>
                </div>
            </div>
        </div>

        {{-- Footer --}}
        <div class="flex-shrink-0 flex items-center justify-between px-6 py-4 border-t border-neutral-200 dark:border-neutral-800 bg-neutral-50/50 dark:bg-neutral-900/30 rounded-b-3xl">
            <button
                type="button"
                id="customerDetailsEditBtn"
                onclick="openEditFromCustomerDetails()"
                class="inline-flex items-center gap-2 rounded-xl px-4 py-2.5 text-[14px] font-medium bg-violet-600 text-white hover:bg-violet-500 transition-colors duration-100 ease-in-out"
            >
                <x-icon name="edit" class="h-4 w-4" />
                <span>Edit Customer</span>
            </button>
            <button
                type="button"
                onclick="closeCustomerDetailsModal()"
                class="inline-flex items-center gap-2 rounded-xl px-4 py-2.5 text-[14px] font-medium border border-neutral-300 bg-white text-neutral-700 hover:bg-neutral-100 dark:border-neutral-700 dark:bg-neutral-900 dark:text-neutral-200 dark:hover:bg-neutral-800 transition-colors duration-100 ease-in-out"
            >
                Close
            </button>
        </div>
    </div>
</div>

<style>
    /* Optimize modal performance */
    #customerDetailsModal {
        will-change: opacity;
        transform: translateZ(0);
        backface-visibility: hidden;
    }

    #customerDetailsModal .max-w-4xl {
        will-change: transform;
        transform: translateZ(0);
    }

    /* Custom scrollbar for customer details content */
    #customerDetailsContent {
        scrollbar-width: thin;
        scrollbar-color: rgba(155, 155, 155, 0.5) transparent;
    }

    #customerDetailsContent::-webkit-scrollbar {
        width: 6px;
    }

    #customerDetailsContent::-webkit-scrollbar-track {
        background: transparent;
    }

    #customerDetailsContent::-webkit-scrollbar-thumb {
        background-color: rgba(155, 155, 155, 0.5);
        border-radius: 3px;
    }

    #customerDetailsContent::-webkit-scrollbar-thumb:hover {
        background-color: rgba(155, 155, 155, 0.7);
    }

    .dark #customerDetailsContent::-webkit-scrollbar-thumb {
        background-color: rgba(100, 100, 100, 0.5);
    }

    .dark #customerDetailsContent::-webkit-scrollbar-thumb:hover {
        background-color: rgba(100, 100, 100, 0.7);
    }
</style>

<script>
    // Use globalThis to avoid redeclaration errors when Turbo navigates between pages
    if (!globalThis.customerDetailsModalState) {
        globalThis.customerDetailsModalState = {
            isOpen: false,
            currentCustomerId: null,
            currentCustomer: null
        };
    }

    // Open customer details modal
    async function openCustomerDetailsModal(customerId) {
        globalThis.customerDetailsModalState.isOpen = true;
        globalThis.customerDetailsModalState.currentCustomerId = customerId;

        var modal = document.getElementById('customerDetailsModal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');

        // Show loading state
        document.getElementById('customerDetailsLoading').classList.remove('hidden');
        document.getElementById('customerDetailsData').classList.add('hidden');
        document.getElementById('customerDetailsError').classList.add('hidden');
        document.getElementById('customerDetailsTitle').textContent = 'Loading...';

        try {
            var response = await axios.get(`/api/customers/${customerId}`);
            var customer = response.data.data;
            var rentalStats = response.data.rental_statistics;
            globalThis.customerDetailsModalState.currentCustomer = customer;
            globalThis.customerDetailsModalState.rentalStats = rentalStats;

            populateCustomerDetails(customer, rentalStats);

            // Hide loading, show data
            document.getElementById('customerDetailsLoading').classList.add('hidden');
            document.getElementById('customerDetailsData').classList.remove('hidden');
        } catch (error) {
            console.error('Error loading customer details:', error);
            document.getElementById('customerDetailsLoading').classList.add('hidden');
            document.getElementById('customerDetailsError').classList.remove('hidden');
            document.getElementById('customerDetailsErrorMessage').textContent =
                error.response?.data?.message || error.message || 'Failed to load customer details';
        }
    }

    // Populate customer details in the modal
    function populateCustomerDetails(customer, rentalStats) {
        // Title
        var fullName = `${customer.first_name} ${customer.last_name}`;
        document.getElementById('customerDetailsTitle').textContent = fullName;

        // Status with dynamic styling
        var statusName = customer.status?.status_name?.toLowerCase() || 'unknown';
        var statusCard = document.getElementById('detailCustomerStatusCard');
        var statusDot = document.getElementById('detailCustomerStatusDot');
        var statusText = document.getElementById('detailCustomerStatus');

        var statusConfig = {
            'active': {
                label: 'Active',
                cardClass: 'bg-emerald-50 dark:bg-emerald-900/20 border-emerald-200 dark:border-emerald-800/50 text-emerald-700 dark:text-emerald-300',
                dotClass: 'bg-emerald-500'
            },
            'inactive': {
                label: 'Inactive',
                cardClass: 'bg-red-50 dark:bg-red-900/20 border-red-200 dark:border-red-800/50 text-red-700 dark:text-red-300',
                dotClass: 'bg-red-500'
            }
        };

        var config = statusConfig[statusName] || statusConfig['inactive'];
        statusCard.className = `rounded-xl p-3 border ${config.cardClass}`;
        statusDot.className = `h-2.5 w-2.5 rounded-full ${config.dotClass}`;
        statusText.textContent = customer.status?.status_name || 'Unknown';

        // Stats
        document.getElementById('detailTotalRentals').textContent = rentalStats?.total_rentals || 0;
        document.getElementById('detailActiveRentals').textContent = rentalStats?.active_rentals || 0;
        document.getElementById('detailTotalReservations').textContent = rentalStats?.total_reservations || 0;

        // Customer Info
        document.getElementById('detailCustomerId').textContent = `#${String(customer.customer_id).padStart(3, '0')}`;
        document.getElementById('detailCustomerName').textContent = fullName;

        // Contact Info
        document.getElementById('detailCustomerEmail').textContent = customer.email || '-';
        document.getElementById('detailCustomerPhone').textContent = customer.contact_number || '-';
        document.getElementById('detailCustomerAddress').textContent = customer.address || '-';

        // Dates
        document.getElementById('detailCustomerCreated').textContent = customer.created_at
            ? formatCustomerDate(customer.created_at)
            : '-';
        document.getElementById('detailCustomerUpdated').textContent = customer.updated_at
            ? formatCustomerDate(customer.updated_at)
            : '-';

        // Render recent rentals
        renderRecentRentals(customer.rentals || []);

        // Render recent reservations
        renderRecentReservations(customer.reservations || []);
    }

    // Render recent rentals
    function renderRecentRentals(rentals) {
        var container = document.getElementById('detailRecentRentals');
        var countEl = document.getElementById('detailRentalsCount');

        countEl.textContent = `${rentals.length} rental${rentals.length !== 1 ? 's' : ''}`;

        if (!rentals || rentals.length === 0) {
            container.innerHTML = `
                <div class="p-4 text-center text-sm text-neutral-500 dark:text-neutral-400">
                    No rentals found
                </div>
            `;
            return;
        }

        // Show only the 5 most recent rentals
        var recentRentals = rentals.slice(0, 5);

        container.innerHTML = `
            <div class="divide-y divide-neutral-200 dark:divide-neutral-800">
                ${recentRentals.map(rental => {
                    var statusName = rental.status?.status_name?.toLowerCase() || 'unknown';
                    var statusColors = {
                        'active': 'bg-emerald-500/15 text-emerald-600 border-emerald-500/40 dark:text-emerald-300',
                        'completed': 'bg-blue-500/15 text-blue-600 border-blue-500/40 dark:text-blue-300',
                        'cancelled': 'bg-red-500/15 text-red-600 border-red-500/40 dark:text-red-300',
                        'overdue': 'bg-amber-500/15 text-amber-600 border-amber-500/40 dark:text-amber-300'
                    };
                    var statusColor = statusColors[statusName] || 'bg-neutral-500/15 text-neutral-600 border-neutral-500/40 dark:text-neutral-300';

                    var rentalDate = rental.rental_date ? formatCustomerDate(rental.rental_date) : '-';
                    var returnDate = rental.return_date ? formatCustomerDate(rental.return_date) : 'Not returned';

                    return `
                        <div class="px-4 py-3 flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="h-8 w-8 rounded-lg bg-violet-100 dark:bg-violet-900/30 flex items-center justify-center">
                                    <span class="text-xs font-bold text-violet-600 dark:text-violet-400 font-geist-mono">#${rental.rental_id}</span>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-neutral-900 dark:text-white">${rentalDate}</p>
                                    <p class="text-xs text-neutral-500 dark:text-neutral-400">Return: ${returnDate}</p>
                                </div>
                            </div>
                            <span class="inline-flex items-center rounded-full ${statusColor} px-2 py-1 text-[10px] font-medium border">
                                ${rental.status?.status_name || 'Unknown'}
                            </span>
                        </div>
                    `;
                }).join('')}
            </div>
            ${rentals.length > 5 ? `
                <div class="px-4 py-2 bg-neutral-100 dark:bg-neutral-800/50 text-center">
                    <span class="text-xs text-neutral-500 dark:text-neutral-400">+ ${rentals.length - 5} more rentals</span>
                </div>
            ` : ''}
        `;
    }

    // Render recent reservations
    function renderRecentReservations(reservations) {
        var container = document.getElementById('detailRecentReservations');
        var countEl = document.getElementById('detailReservationsCount');

        countEl.textContent = `${reservations.length} reservation${reservations.length !== 1 ? 's' : ''}`;

        if (!reservations || reservations.length === 0) {
            container.innerHTML = `
                <div class="p-4 text-center text-sm text-neutral-500 dark:text-neutral-400">
                    No reservations found
                </div>
            `;
            return;
        }

        // Show only the 5 most recent reservations
        var recentReservations = reservations.slice(0, 5);

        container.innerHTML = `
            <div class="divide-y divide-neutral-200 dark:divide-neutral-800">
                ${recentReservations.map(reservation => {
                    var statusName = reservation.status?.status_name?.toLowerCase() || 'unknown';
                    var statusColors = {
                        'pending': 'bg-amber-500/15 text-amber-600 border-amber-500/40 dark:text-amber-300',
                        'confirmed': 'bg-emerald-500/15 text-emerald-600 border-emerald-500/40 dark:text-emerald-300',
                        'cancelled': 'bg-red-500/15 text-red-600 border-red-500/40 dark:text-red-300',
                        'completed': 'bg-blue-500/15 text-blue-600 border-blue-500/40 dark:text-blue-300'
                    };
                    var statusColor = statusColors[statusName] || 'bg-neutral-500/15 text-neutral-600 border-neutral-500/40 dark:text-neutral-300';

                    var reservationDate = reservation.reservation_date ? formatCustomerDate(reservation.reservation_date) : '-';
                    var eventDate = reservation.event_date ? formatCustomerDate(reservation.event_date) : '-';

                    return `
                        <div class="px-4 py-3 flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="h-8 w-8 rounded-lg bg-cyan-100 dark:bg-cyan-900/30 flex items-center justify-center">
                                    <span class="text-xs font-bold text-cyan-600 dark:text-cyan-400 font-geist-mono">#${reservation.reservation_id}</span>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-neutral-900 dark:text-white">Reserved: ${reservationDate}</p>
                                    <p class="text-xs text-neutral-500 dark:text-neutral-400">Event: ${eventDate}</p>
                                </div>
                            </div>
                            <span class="inline-flex items-center rounded-full ${statusColor} px-2 py-1 text-[10px] font-medium border">
                                ${reservation.status?.status_name || 'Unknown'}
                            </span>
                        </div>
                    `;
                }).join('')}
            </div>
            ${reservations.length > 5 ? `
                <div class="px-4 py-2 bg-neutral-100 dark:bg-neutral-800/50 text-center">
                    <span class="text-xs text-neutral-500 dark:text-neutral-400">+ ${reservations.length - 5} more reservations</span>
                </div>
            ` : ''}
        `;
    }

    // Format date helper
    function formatCustomerDate(dateString) {
        var date = new Date(dateString);
        return date.toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'short',
            day: 'numeric'
        });
    }

    // Close customer details modal
    function closeCustomerDetailsModal() {
        globalThis.customerDetailsModalState.isOpen = false;
        globalThis.customerDetailsModalState.currentCustomerId = null;
        globalThis.customerDetailsModalState.currentCustomer = null;

        var modal = document.getElementById('customerDetailsModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    // Open edit modal from details modal
    function openEditFromCustomerDetails() {
        var customerId = globalThis.customerDetailsModalState.currentCustomerId;
        closeCustomerDetailsModal();

        // Small delay to allow close animation
        setTimeout(() => {
            openEditCustomerModal(customerId);
        }, 100);
    }

    // Handle keyboard navigation
    document.addEventListener('keydown', function(e) {
        if (!globalThis.customerDetailsModalState.isOpen) return;

        // Check if other modals are open (edit modal, etc.)
        var editModalOpen = document.getElementById('editCustomerModal')?.classList.contains('flex');
        var addModalOpen = document.getElementById('addCustomerModal')?.classList.contains('flex');

        if (e.key === 'Escape' && !editModalOpen && !addModalOpen) {
            closeCustomerDetailsModal();
        }
    });

    // Close modal on backdrop click
    document.getElementById('customerDetailsModal')?.addEventListener('click', function(e) {
        if (e.target === this && globalThis.customerDetailsModalState.isOpen) {
            closeCustomerDetailsModal();
        }
    });
</script>
