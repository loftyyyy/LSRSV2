{{-- Rental Details Modal --}}
<div id="rentalDetailsModal" class="hidden fixed inset-0 z-50 flex items-center justify-center px-2 py-4 bg-black/60 backdrop-blur-sm overflow-y-auto">
    <div class="w-full max-w-5xl bg-white dark:bg-neutral-950 border border-neutral-200 dark:border-neutral-800 rounded-3xl shadow-2xl flex flex-col max-h-[calc(100vh-2rem)] my-auto">
        {{-- Header --}}
        <div class="flex-shrink-0 flex items-center justify-between px-6 py-4 border-b border-neutral-200 dark:border-neutral-800 bg-neutral-50/80 dark:bg-neutral-900/50 rounded-t-3xl">
            <div class="flex items-center gap-4">
                {{-- Rental Icon --}}
                <div id="rentalDetailsIcon" class="h-12 w-12 rounded-full bg-gradient-to-br from-violet-500 to-purple-600 flex items-center justify-center text-white shadow-lg">
                    <x-icon name="shopping-bag" class="h-6 w-6" />
                </div>
                <div>
                    <p class="text-xs uppercase tracking-[0.2em] text-neutral-500 dark:text-neutral-500">Rental Details</p>
                    <h3 id="rentalDetailsTitle" class="text-lg font-semibold text-neutral-900 dark:text-white">Loading...</h3>
                </div>
            </div>
            <button onclick="closeRentalDetailsModal()" class="text-neutral-500 hover:text-neutral-800 dark:hover:text-neutral-200 text-xl transition-colors duration-200">&times;</button>
        </div>

        {{-- Content (scrollable) --}}
        <div id="rentalDetailsContent" class="flex-1 overflow-y-auto">
            {{-- Loading State --}}
            <div id="rentalDetailsLoading" class="flex items-center justify-center py-16">
                <div class="flex flex-col items-center gap-3">
                    <div class="h-8 w-8 animate-spin rounded-full border-2 border-violet-600 border-t-transparent"></div>
                    <p class="text-sm text-neutral-500 dark:text-neutral-400">Loading rental details...</p>
                </div>
            </div>

            {{-- Rental Details (hidden initially) --}}
            <div id="rentalDetailsData" class="hidden">
                {{-- Main Content: Two Column Layout --}}
                <div class="flex flex-col lg:flex-row">
                    {{-- Left Column: Rental Info --}}
                    <div class="lg:w-1/2 p-6 border-b lg:border-b-0 lg:border-r border-neutral-200 dark:border-neutral-800 space-y-5">
                        {{-- Status & ID Row --}}
                        <div class="grid grid-cols-2 gap-4">
                            {{-- Status Section --}}
                            <div class="space-y-2">
                                <div class="flex items-center gap-2 text-sm font-medium text-neutral-700 dark:text-neutral-300">
                                    <x-icon name="activity" class="h-4 w-4" />
                                    <span>Status</span>
                                </div>
                                <div id="detailRentalStatusCard" class="rounded-xl p-3 border">
                                    <div class="flex items-center gap-2">
                                        <span id="detailRentalStatusDot" class="h-2.5 w-2.5 rounded-full"></span>
                                        <p id="detailRentalStatus" class="text-sm font-semibold">-</p>
                                    </div>
                                </div>
                            </div>

                            {{-- Rental ID --}}
                            <div class="space-y-2">
                                <div class="flex items-center gap-2 text-sm font-medium text-neutral-700 dark:text-neutral-300">
                                    <x-icon name="hash" class="h-4 w-4" />
                                    <span>Rental ID</span>
                                </div>
                                <div class="bg-neutral-50 dark:bg-neutral-900/50 rounded-xl p-3 border border-neutral-200 dark:border-neutral-800">
                                    <p id="detailRentalId" class="text-sm font-semibold text-neutral-900 dark:text-white font-geist-mono">-</p>
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
                                    <p id="detailRentalCustomerName" class="text-sm font-medium text-neutral-900 dark:text-white">-</p>
                                </div>

                                {{-- Customer Contact --}}
                                <div class="grid grid-cols-2 gap-3">
                                    <div class="bg-neutral-50 dark:bg-neutral-900/50 rounded-xl p-3 border border-neutral-200 dark:border-neutral-800">
                                        <div class="flex items-center gap-1.5 mb-1">
                                            <x-icon name="mail" class="h-3 w-3 text-neutral-400" />
                                            <p class="text-xs text-neutral-500 dark:text-neutral-400">Email</p>
                                        </div>
                                        <p id="detailRentalCustomerEmail" class="text-sm font-medium text-neutral-900 dark:text-white break-all">-</p>
                                    </div>
                                    <div class="bg-neutral-50 dark:bg-neutral-900/50 rounded-xl p-3 border border-neutral-200 dark:border-neutral-800">
                                        <div class="flex items-center gap-1.5 mb-1">
                                            <x-icon name="phone" class="h-3 w-3 text-neutral-400" />
                                            <p class="text-xs text-neutral-500 dark:text-neutral-400">Contact Number</p>
                                        </div>
                                        <p id="detailRentalCustomerPhone" class="text-sm font-medium text-neutral-900 dark:text-white font-geist-mono">-</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Item Information Section --}}
                        <div class="space-y-3">
                            <div class="flex items-center gap-2 text-sm font-medium text-neutral-700 dark:text-neutral-300">
                                <x-icon name="package" class="h-4 w-4" />
                                <span>Item Information</span>
                            </div>

                            <div class="bg-neutral-50 dark:bg-neutral-900/50 rounded-xl p-4 border border-neutral-200 dark:border-neutral-800">
                                <div class="flex items-start gap-4">
                                    <div class="h-16 w-16 rounded-xl bg-neutral-200 dark:bg-neutral-800 flex items-center justify-center flex-shrink-0">
                                        <x-icon name="package" class="h-8 w-8 text-neutral-400" />
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p id="detailRentalItemName" class="text-base font-semibold text-neutral-900 dark:text-white truncate">-</p>
                                        <p id="detailRentalItemSku" class="text-xs text-neutral-500 dark:text-neutral-400 font-geist-mono mt-0.5">-</p>
                                        <p id="detailRentalItemDetails" class="text-sm text-neutral-600 dark:text-neutral-300 mt-1">-</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Dates Section --}}
                        <div class="space-y-3">
                            <div class="flex items-center gap-2 text-sm font-medium text-neutral-700 dark:text-neutral-300">
                                <x-icon name="calendar" class="h-4 w-4" />
                                <span>Rental Dates</span>
                            </div>

                            <div class="grid grid-cols-3 gap-3">
                                {{-- Released Date --}}
                                <div class="bg-neutral-50 dark:bg-neutral-900/50 rounded-xl p-3 border border-neutral-200 dark:border-neutral-800">
                                    <div class="flex items-center gap-1.5 mb-1">
                                        <x-icon name="calendar-check" class="h-3 w-3 text-neutral-400" />
                                        <p class="text-xs text-neutral-500 dark:text-neutral-400">Released</p>
                                    </div>
                                    <p id="detailRentalReleasedDate" class="text-sm font-medium text-neutral-900 dark:text-white">-</p>
                                </div>

                                {{-- Due Date --}}
                                <div id="detailRentalDueDateCard" class="bg-neutral-50 dark:bg-neutral-900/50 rounded-xl p-3 border border-neutral-200 dark:border-neutral-800">
                                    <div class="flex items-center gap-1.5 mb-1">
                                        <x-icon name="calendar-x" class="h-3 w-3 text-neutral-400" />
                                        <p class="text-xs text-neutral-500 dark:text-neutral-400">Due Date</p>
                                    </div>
                                    <p id="detailRentalDueDate" class="text-sm font-medium text-neutral-900 dark:text-white">-</p>
                                </div>

                                {{-- Return Date --}}
                                <div class="bg-neutral-50 dark:bg-neutral-900/50 rounded-xl p-3 border border-neutral-200 dark:border-neutral-800">
                                    <div class="flex items-center gap-1.5 mb-1">
                                        <x-icon name="arrow-left-circle" class="h-3 w-3 text-neutral-400" />
                                        <p class="text-xs text-neutral-500 dark:text-neutral-400">Returned</p>
                                    </div>
                                    <p id="detailRentalReturnDate" class="text-sm font-medium text-neutral-900 dark:text-white">-</p>
                                </div>
                            </div>
                        </div>

                        {{-- Overdue Warning (conditionally shown) --}}
                        <div id="detailRentalOverdueWarning" class="hidden">
                            <div class="bg-rose-50 dark:bg-rose-900/20 border border-rose-200 dark:border-rose-800/50 rounded-xl p-4">
                                <div class="flex items-start gap-3">
                                    <x-icon name="alert-triangle" class="h-5 w-5 text-rose-500 flex-shrink-0 mt-0.5" />
                                    <div>
                                        <p class="text-sm font-semibold text-rose-700 dark:text-rose-300">Overdue Rental</p>
                                        <p id="detailRentalOverdueDays" class="text-xs text-rose-600 dark:text-rose-400 mt-1">-</p>
                                        <p id="detailRentalPenaltyAmount" class="text-sm font-semibold text-rose-700 dark:text-rose-300 mt-2 font-geist-mono">-</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Right Column: Staff & Financial --}}
                    <div class="lg:w-1/2 p-6 space-y-5">
                        {{-- Financial Summary --}}
                        <div class="space-y-3">
                            <div class="flex items-center gap-2 text-sm font-medium text-neutral-700 dark:text-neutral-300">
                                <x-icon name="credit-card" class="h-4 w-4" />
                                <span>Financial Summary</span>
                            </div>

                            <div class="grid grid-cols-2 gap-3">
                                {{-- Deposit Collected --}}
                                <div class="bg-neutral-50 dark:bg-neutral-900/50 rounded-xl p-4 border border-neutral-200 dark:border-neutral-800">
                                    <p class="text-xs text-neutral-500 dark:text-neutral-400 mb-1">Deposit Collected</p>
                                    <p id="detailRentalDeposit" class="text-xl font-bold text-neutral-900 dark:text-white font-geist-mono">₱0</p>
                                    <p id="detailRentalDepositStatus" class="text-xs text-neutral-500 dark:text-neutral-400 mt-1">-</p>
                                </div>

                                {{-- Penalty Amount --}}
                                <div class="bg-neutral-50 dark:bg-neutral-900/50 rounded-xl p-4 border border-neutral-200 dark:border-neutral-800">
                                    <p class="text-xs text-neutral-500 dark:text-neutral-400 mb-1">Late Penalty</p>
                                    <p id="detailRentalPenalty" class="text-xl font-bold text-neutral-900 dark:text-white font-geist-mono">₱0</p>
                                </div>
                            </div>
                        </div>

                        {{-- Released By Section --}}
                        <div class="space-y-3">
                            <div class="flex items-center gap-2 text-sm font-medium text-neutral-700 dark:text-neutral-300">
                                <x-icon name="user-check" class="h-4 w-4" />
                                <span>Released By</span>
                            </div>

                            <div class="bg-neutral-50 dark:bg-neutral-900/50 rounded-xl p-3 border border-neutral-200 dark:border-neutral-800">
                                <div class="flex items-center gap-3">
                                    <div id="detailReleasedByAvatar" class="h-8 w-8 rounded-full bg-gradient-to-br from-violet-500 to-purple-600 flex items-center justify-center text-white text-xs font-semibold">
                                        --
                                    </div>
                                    <div>
                                        <p id="detailReleasedByName" class="text-sm font-medium text-neutral-900 dark:text-white">-</p>
                                        <p id="detailReleasedByEmail" class="text-xs text-neutral-500 dark:text-neutral-400">-</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Returned To Section (conditional) --}}
                        <div id="detailReturnedToSection" class="hidden space-y-3">
                            <div class="flex items-center gap-2 text-sm font-medium text-neutral-700 dark:text-neutral-300">
                                <x-icon name="user-check" class="h-4 w-4" />
                                <span>Returned To</span>
                            </div>

                            <div class="bg-neutral-50 dark:bg-neutral-900/50 rounded-xl p-3 border border-neutral-200 dark:border-neutral-800">
                                <div class="flex items-center gap-3">
                                    <div id="detailReturnedToAvatar" class="h-8 w-8 rounded-full bg-gradient-to-br from-emerald-500 to-green-600 flex items-center justify-center text-white text-xs font-semibold">
                                        --
                                    </div>
                                    <div>
                                        <p id="detailReturnedToName" class="text-sm font-medium text-neutral-900 dark:text-white">-</p>
                                        <p id="detailReturnedToEmail" class="text-xs text-neutral-500 dark:text-neutral-400">-</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Linked Reservation Section --}}
                        <div id="detailLinkedReservationSection" class="space-y-3">
                            <div class="flex items-center gap-2 text-sm font-medium text-neutral-700 dark:text-neutral-300">
                                <x-icon name="link" class="h-4 w-4" />
                                <span>Linked Reservation</span>
                            </div>

                            <div id="detailLinkedReservation" class="bg-neutral-50 dark:bg-neutral-900/50 rounded-xl p-3 border border-neutral-200 dark:border-neutral-800">
                                <p class="text-sm text-neutral-500 dark:text-neutral-400">No linked reservation</p>
                            </div>
                        </div>

                        {{-- Related Invoices Section --}}
                        <div class="space-y-3">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2 text-sm font-medium text-neutral-700 dark:text-neutral-300">
                                    <x-icon name="file-text" class="h-4 w-4" />
                                    <span>Invoices</span>
                                </div>
                                <span id="detailRentalInvoicesCount" class="text-xs text-neutral-500 dark:text-neutral-400 font-geist-mono">0 invoices</span>
                            </div>

                            <div id="detailRentalInvoices" class="bg-neutral-50 dark:bg-neutral-900/50 rounded-xl border border-neutral-200 dark:border-neutral-800 overflow-hidden max-h-48 overflow-y-auto">
                                {{-- Invoices will be inserted here --}}
                                <div class="p-4 text-center text-sm text-neutral-500 dark:text-neutral-400">
                                    No invoices found
                                </div>
                            </div>
                        </div>

                        {{-- Rental History Timeline --}}
                        <div class="space-y-3">
                            <div class="flex items-center gap-2 text-sm font-medium text-neutral-700 dark:text-neutral-300">
                                <x-icon name="clock" class="h-4 w-4" />
                                <span>Rental Timeline</span>
                            </div>

                            <div id="detailRentalTimeline" class="relative pl-6 space-y-4 max-h-64 overflow-y-auto">
                                {{-- Timeline line --}}
                                <div class="absolute left-[9px] top-2 bottom-2 w-0.5 bg-neutral-200 dark:bg-neutral-700"></div>

                                {{-- Created Event --}}
                                <div class="relative flex items-start gap-3">
                                    <div class="absolute left-[-15px] top-1 h-4 w-4 rounded-full bg-violet-500 border-2 border-white dark:border-neutral-900 z-10 flex items-center justify-center">
                                        <div class="h-1.5 w-1.5 rounded-full bg-white"></div>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-xs font-medium text-neutral-900 dark:text-white">Rental Created</p>
                                        <p id="timelineCreatedAt" class="text-xs text-neutral-500 dark:text-neutral-400">-</p>
                                    </div>
                                </div>

                                {{-- Released Event --}}
                                <div id="timelineReleasedEvent" class="relative flex items-start gap-3">
                                    <div class="absolute left-[-15px] top-1 h-4 w-4 rounded-full bg-emerald-500 border-2 border-white dark:border-neutral-900 z-10 flex items-center justify-center">
                                        <div class="h-1.5 w-1.5 rounded-full bg-white"></div>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-xs font-medium text-neutral-900 dark:text-white">Item Released</p>
                                        <p id="timelineReleasedAt" class="text-xs text-neutral-500 dark:text-neutral-400">-</p>
                                        <p id="timelineReleasedBy" class="text-xs text-neutral-400 dark:text-neutral-500 mt-0.5">-</p>
                                    </div>
                                </div>

                                {{-- Extension Events (dynamic) --}}
                                <div id="timelineExtensionEvents">
                                    {{-- Extensions will be inserted here dynamically --}}
                                </div>

                                {{-- Due Date Event --}}
                                <div id="timelineDueEvent" class="relative flex items-start gap-3">
                                    <div id="timelineDueIcon" class="absolute left-[-15px] top-1 h-4 w-4 rounded-full bg-amber-500 border-2 border-white dark:border-neutral-900 z-10 flex items-center justify-center">
                                        <div class="h-1.5 w-1.5 rounded-full bg-white"></div>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p id="timelineDueLabel" class="text-xs font-medium text-neutral-900 dark:text-white">Due Date</p>
                                        <p id="timelineDueAt" class="text-xs text-neutral-500 dark:text-neutral-400">-</p>
                                    </div>
                                </div>

                                {{-- Overdue Event (conditional) --}}
                                <div id="timelineOverdueEvent" class="hidden relative flex items-start gap-3">
                                    <div class="absolute left-[-15px] top-1 h-4 w-4 rounded-full bg-rose-500 border-2 border-white dark:border-neutral-900 z-10 flex items-center justify-center">
                                        <div class="h-1.5 w-1.5 rounded-full bg-white"></div>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-xs font-medium text-rose-600 dark:text-rose-400">Overdue</p>
                                        <p id="timelineOverdueDays" class="text-xs text-rose-500 dark:text-rose-400">-</p>
                                    </div>
                                </div>

                                {{-- Returned Event (conditional) --}}
                                <div id="timelineReturnedEvent" class="hidden relative flex items-start gap-3">
                                    <div class="absolute left-[-15px] top-1 h-4 w-4 rounded-full bg-sky-500 border-2 border-white dark:border-neutral-900 z-10 flex items-center justify-center">
                                        <div class="h-1.5 w-1.5 rounded-full bg-white"></div>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-xs font-medium text-neutral-900 dark:text-white">Item Returned</p>
                                        <p id="timelineReturnedAt" class="text-xs text-neutral-500 dark:text-neutral-400">-</p>
                                        <p id="timelineReturnedBy" class="text-xs text-neutral-400 dark:text-neutral-500 mt-0.5">-</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Error State --}}
            <div id="rentalDetailsError" class="hidden p-6">
                <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl p-4 flex items-center gap-3">
                    <x-icon name="alert-circle" class="h-5 w-5 text-red-500 flex-shrink-0" />
                    <p id="rentalDetailsErrorMessage" class="text-sm text-red-600 dark:text-red-400">Failed to load rental details</p>
                </div>
            </div>
        </div>

        {{-- Footer --}}
        <div class="flex-shrink-0 flex items-center justify-between px-6 py-4 border-t border-neutral-200 dark:border-neutral-800 bg-neutral-50/50 dark:bg-neutral-900/30 rounded-b-3xl">
            <div class="flex items-center gap-2">
                <button
                    type="button"
                    id="rentalDetailsProcessReturnBtn"
                    onclick="processReturnFromDetails()"
                    class="inline-flex items-center gap-2 rounded-xl px-4 py-2.5 text-[14px] font-medium bg-violet-600 text-white hover:bg-violet-500 transition-colors duration-100 ease-in-out"
                >
                    <x-icon name="arrow-left-circle" class="h-4 w-4" />
                    <span>Process Return</span>
                </button>
                <button
                    type="button"
                    id="rentalDetailsExtendBtn"
                    onclick="extendRentalFromDetails()"
                    class="inline-flex items-center gap-2 rounded-xl px-4 py-2.5 text-[14px] font-medium border border-neutral-300 bg-white text-neutral-700 hover:bg-neutral-100 dark:border-neutral-700 dark:bg-neutral-900 dark:text-neutral-200 dark:hover:bg-neutral-800 transition-colors duration-100 ease-in-out"
                >
                    <x-icon name="calendar" class="h-4 w-4" />
                    <span>Extend</span>
                </button>
            </div>
            <button
                type="button"
                onclick="closeRentalDetailsModal()"
                class="inline-flex items-center gap-2 rounded-xl px-4 py-2.5 text-[14px] font-medium border border-neutral-300 bg-white text-neutral-700 hover:bg-neutral-100 dark:border-neutral-700 dark:bg-neutral-900 dark:text-neutral-200 dark:hover:bg-neutral-800 transition-colors duration-100 ease-in-out"
            >
                Close
            </button>
        </div>
    </div>
</div>

<style>
    /* Optimize modal performance */
    #rentalDetailsModal {
        will-change: opacity;
        transform: translateZ(0);
        backface-visibility: hidden;
    }

    #rentalDetailsModal .max-w-5xl {
        will-change: transform;
        transform: translateZ(0);
    }

    /* Custom scrollbar for rental details content */
    #rentalDetailsContent {
        scrollbar-width: thin;
        scrollbar-color: rgba(155, 155, 155, 0.5) transparent;
    }

    #rentalDetailsContent::-webkit-scrollbar {
        width: 6px;
    }

    #rentalDetailsContent::-webkit-scrollbar-track {
        background: transparent;
    }

    #rentalDetailsContent::-webkit-scrollbar-thumb {
        background-color: rgba(155, 155, 155, 0.5);
        border-radius: 3px;
    }

    #rentalDetailsContent::-webkit-scrollbar-thumb:hover {
        background-color: rgba(155, 155, 155, 0.7);
    }

    .dark #rentalDetailsContent::-webkit-scrollbar-thumb {
        background-color: rgba(100, 100, 100, 0.5);
    }

    .dark #rentalDetailsContent::-webkit-scrollbar-thumb:hover {
        background-color: rgba(100, 100, 100, 0.7);
    }

    /* Custom scrollbar for invoices list */
    #detailRentalInvoices {
        scrollbar-width: thin;
        scrollbar-color: rgba(155, 155, 155, 0.3) transparent;
    }

    #detailRentalInvoices::-webkit-scrollbar {
        width: 4px;
    }

    #detailRentalInvoices::-webkit-scrollbar-track {
        background: transparent;
    }

    #detailRentalInvoices::-webkit-scrollbar-thumb {
        background-color: rgba(155, 155, 155, 0.3);
        border-radius: 2px;
    }
</style>

<script>
    // Use globalThis to avoid redeclaration errors when Turbo navigates between pages
    if (!globalThis.rentalDetailsModalState) {
        globalThis.rentalDetailsModalState = {
            isOpen: false,
            currentRentalId: null,
            currentRental: null,
            calculatedPenalty: 0,
            isOverdue: false
        };
    }

    // Open rental details modal
    async function openRentalDetailsModal(rentalId) {
        globalThis.rentalDetailsModalState.isOpen = true;
        globalThis.rentalDetailsModalState.currentRentalId = rentalId;

        var modal = document.getElementById('rentalDetailsModal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');

        // Show loading state
        document.getElementById('rentalDetailsLoading').classList.remove('hidden');
        document.getElementById('rentalDetailsData').classList.add('hidden');
        document.getElementById('rentalDetailsError').classList.add('hidden');
        document.getElementById('rentalDetailsTitle').textContent = 'Loading...';

        try {
            var response = await axios.get('/api/rentals/' + rentalId);
            var rental = response.data.data;
            globalThis.rentalDetailsModalState.currentRental = rental;
            globalThis.rentalDetailsModalState.calculatedPenalty = response.data.calculated_penalty || 0;
            globalThis.rentalDetailsModalState.isOverdue = response.data.is_overdue || false;

            populateRentalDetails(rental, response.data);

            // Hide loading, show data
            document.getElementById('rentalDetailsLoading').classList.add('hidden');
            document.getElementById('rentalDetailsData').classList.remove('hidden');
        } catch (error) {
            console.error('Error loading rental details:', error);
            document.getElementById('rentalDetailsLoading').classList.add('hidden');
            document.getElementById('rentalDetailsError').classList.remove('hidden');
            document.getElementById('rentalDetailsErrorMessage').textContent =
                (error.response && error.response.data && error.response.data.message) || error.message || 'Failed to load rental details';
        }
    }

    // Populate rental details in the modal
    function populateRentalDetails(rental, responseData) {
        // Title
        document.getElementById('rentalDetailsTitle').textContent = 'Rental #' + String(rental.rental_id).padStart(3, '0');

        // Status with dynamic styling
        var statusName = (rental.status && rental.status.status_name) ? rental.status.status_name.toLowerCase() : 'unknown';
        var isOverdue = responseData.is_overdue;

        // Override status display if overdue
        if (isOverdue && statusName !== 'returned') {
            statusName = 'overdue';
        }

        var statusCard = document.getElementById('detailRentalStatusCard');
        var statusDot = document.getElementById('detailRentalStatusDot');
        var statusText = document.getElementById('detailRentalStatus');

        var statusConfig = {
            'rented': {
                label: 'Rented',
                cardClass: 'bg-sky-50 dark:bg-sky-900/20 border-sky-200 dark:border-sky-800/50 text-sky-700 dark:text-sky-300',
                dotClass: 'bg-sky-500'
            },
            'active': {
                label: 'Rented',
                cardClass: 'bg-sky-50 dark:bg-sky-900/20 border-sky-200 dark:border-sky-800/50 text-sky-700 dark:text-sky-300',
                dotClass: 'bg-sky-500'
            },
            'returned': {
                label: 'Returned',
                cardClass: 'bg-emerald-50 dark:bg-emerald-900/20 border-emerald-200 dark:border-emerald-800/50 text-emerald-700 dark:text-emerald-300',
                dotClass: 'bg-emerald-500'
            },
            'overdue': {
                label: 'Overdue',
                cardClass: 'bg-rose-50 dark:bg-rose-900/20 border-rose-200 dark:border-rose-800/50 text-rose-700 dark:text-rose-300',
                dotClass: 'bg-rose-500'
            }
        };

        var config = statusConfig[statusName] || {
            label: (rental.status && rental.status.status_name) || 'Unknown',
            cardClass: 'bg-neutral-50 dark:bg-neutral-900/20 border-neutral-200 dark:border-neutral-800/50 text-neutral-700 dark:text-neutral-300',
            dotClass: 'bg-neutral-500'
        };
        statusCard.className = 'rounded-xl p-3 border ' + config.cardClass;
        statusDot.className = 'h-2.5 w-2.5 rounded-full ' + config.dotClass;
        statusText.textContent = config.label;

        // Rental ID
        document.getElementById('detailRentalId').textContent = '#' + String(rental.rental_id).padStart(3, '0');

        // Customer Info
        var customerName = rental.customer
            ? (rental.customer.first_name + ' ' + rental.customer.last_name)
            : '-';
        document.getElementById('detailRentalCustomerName').textContent = customerName;
        document.getElementById('detailRentalCustomerEmail').textContent = (rental.customer && rental.customer.email) || '-';
        document.getElementById('detailRentalCustomerPhone').textContent = (rental.customer && rental.customer.contact_number) || '-';

        // Item Info
        var item = rental.item || {};
        document.getElementById('detailRentalItemName').textContent = item.name || '-';
        document.getElementById('detailRentalItemSku').textContent = item.sku || '-';
        var itemDetails = [item.size, item.color, item.design].filter(Boolean).join(' · ');
        document.getElementById('detailRentalItemDetails').textContent = itemDetails || 'No additional details';

        // Dates
        document.getElementById('detailRentalReleasedDate').textContent = rental.released_date
            ? formatRentalDate(rental.released_date)
            : '-';
        document.getElementById('detailRentalDueDate').textContent = rental.due_date
            ? formatRentalDate(rental.due_date)
            : '-';
        document.getElementById('detailRentalReturnDate').textContent = rental.return_date
            ? formatRentalDate(rental.return_date)
            : 'Not returned';

        // Highlight due date if overdue
        var dueDateCard = document.getElementById('detailRentalDueDateCard');
        if (isOverdue) {
            dueDateCard.className = 'bg-rose-50 dark:bg-rose-900/20 rounded-xl p-3 border border-rose-200 dark:border-rose-800/50';
        } else {
            dueDateCard.className = 'bg-neutral-50 dark:bg-neutral-900/50 rounded-xl p-3 border border-neutral-200 dark:border-neutral-800';
        }

        // Overdue warning
        var overdueWarning = document.getElementById('detailRentalOverdueWarning');
        if (isOverdue) {
            overdueWarning.classList.remove('hidden');
            var dueDate = new Date(rental.due_date);
            var today = new Date();
            var diffTime = Math.abs(today - dueDate);
            var diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
            document.getElementById('detailRentalOverdueDays').textContent = diffDays + ' day' + (diffDays !== 1 ? 's' : '') + ' overdue';
            document.getElementById('detailRentalPenaltyAmount').textContent = 'Estimated Penalty: ₱' + Number(responseData.calculated_penalty || 0).toLocaleString();
        } else {
            overdueWarning.classList.add('hidden');
        }

        // Financial
        var depositAmount = rental.deposit_collected || 0;
        document.getElementById('detailRentalDeposit').textContent = '₱' + Number(depositAmount).toLocaleString();

        var depositStatus = rental.deposit_returned
            ? 'Returned'
            : rental.deposit_forfeited
            ? 'Forfeited'
            : depositAmount > 0
            ? 'Held'
            : 'None';
        document.getElementById('detailRentalDepositStatus').textContent = depositStatus;

        document.getElementById('detailRentalPenalty').textContent = '₱' + Number(responseData.calculated_penalty || 0).toLocaleString();

        // Released By
        if (rental.released_by_user || rental.releasedBy) {
            var releasedBy = rental.released_by_user || rental.releasedBy;
            var initials = (releasedBy.name ? releasedBy.name.charAt(0) : (releasedBy.first_name ? releasedBy.first_name.charAt(0) : 'U')).toUpperCase();
            document.getElementById('detailReleasedByAvatar').textContent = initials;
            document.getElementById('detailReleasedByName').textContent = releasedBy.name || ((releasedBy.first_name || '') + ' ' + (releasedBy.last_name || '')).trim() || '-';
            document.getElementById('detailReleasedByEmail').textContent = releasedBy.email || '-';
        } else {
            document.getElementById('detailReleasedByAvatar').textContent = '--';
            document.getElementById('detailReleasedByName').textContent = '-';
            document.getElementById('detailReleasedByEmail').textContent = '-';
        }

        // Returned To
        var returnedToSection = document.getElementById('detailReturnedToSection');
        if (rental.return_date && (rental.returned_to_user || rental.returnedTo)) {
            returnedToSection.classList.remove('hidden');
            var returnedTo = rental.returned_to_user || rental.returnedTo;
            var initials = (returnedTo.name ? returnedTo.name.charAt(0) : (returnedTo.first_name ? returnedTo.first_name.charAt(0) : 'U')).toUpperCase();
            document.getElementById('detailReturnedToAvatar').textContent = initials;
            document.getElementById('detailReturnedToName').textContent = returnedTo.name || ((returnedTo.first_name || '') + ' ' + (returnedTo.last_name || '')).trim() || '-';
            document.getElementById('detailReturnedToEmail').textContent = returnedTo.email || '-';
        } else {
            returnedToSection.classList.add('hidden');
        }

        // Linked Reservation
        var linkedReservationEl = document.getElementById('detailLinkedReservation');
        if (rental.reservation_id && rental.reservation) {
            linkedReservationEl.innerHTML = '<div class="flex items-center justify-between">' +
                '<div class="flex items-center gap-3">' +
                    '<div class="h-8 w-8 rounded-lg bg-violet-100 dark:bg-violet-900/30 flex items-center justify-center flex-shrink-0">' +
                        '<span class="text-[10px] font-bold text-violet-600 dark:text-violet-400 font-geist-mono">#' + rental.reservation_id + '</span>' +
                    '</div>' +
                    '<p class="text-sm font-medium text-neutral-900 dark:text-white">Reservation #' + String(rental.reservation_id).padStart(3, '0') + '</p>' +
                '</div>' +
                '<span class="text-xs text-violet-600 dark:text-violet-400 font-medium">Linked</span>' +
            '</div>';
        } else {
            linkedReservationEl.innerHTML = '<p class="text-sm text-neutral-500 dark:text-neutral-400">No linked reservation</p>';
        }

        // Invoices
        renderRentalInvoices(rental.invoices || []);

        // Populate Timeline
        populateRentalTimeline(rental, responseData);

        // Update action buttons visibility based on status
        var processReturnBtn = document.getElementById('rentalDetailsProcessReturnBtn');
        var extendBtn = document.getElementById('rentalDetailsExtendBtn');

        if (rental.return_date) {
            // Already returned - hide action buttons
            processReturnBtn.classList.add('hidden');
            extendBtn.classList.add('hidden');
        } else {
            processReturnBtn.classList.remove('hidden');
            // Hide extend if overdue
            if (isOverdue) {
                extendBtn.classList.add('hidden');
            } else {
                extendBtn.classList.remove('hidden');
            }
        }
    }

    // Render rental invoices
    function renderRentalInvoices(invoices) {
        var container = document.getElementById('detailRentalInvoices');
        var countEl = document.getElementById('detailRentalInvoicesCount');

        countEl.textContent = invoices.length + ' invoice' + (invoices.length !== 1 ? 's' : '');

        if (!invoices || invoices.length === 0) {
            container.innerHTML = '<div class="p-4 text-center text-sm text-neutral-500 dark:text-neutral-400">' +
                '<svg class="h-6 w-6 text-neutral-300 dark:text-neutral-600 mx-auto mb-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>' +
                'No invoices found' +
            '</div>';
            return;
        }

        var html = '<div class="divide-y divide-neutral-200 dark:divide-neutral-800">';
        invoices.forEach(function(invoice) {
            var statusName = (invoice.payment_status || 'unknown').toLowerCase();
            var statusColors = {
                'paid': 'bg-emerald-500/15 text-emerald-600 border-emerald-500/40 dark:text-emerald-300',
                'unpaid': 'bg-amber-500/15 text-amber-600 border-amber-500/40 dark:text-amber-300',
                'partial': 'bg-neutral-500/15 text-neutral-600 border-neutral-500/40 dark:text-neutral-300',
                'cancelled': 'bg-red-500/15 text-red-600 border-red-500/40 dark:text-red-300'
            };
            var statusColor = statusColors[statusName] || 'bg-neutral-500/15 text-neutral-600 border-neutral-500/40 dark:text-neutral-300';

            var invoiceDate = invoice.invoice_date ? formatRentalDate(invoice.invoice_date) : '-';
            var totalAmount = invoice.total_amount || 0;

            html += '<div class="px-4 py-3 flex items-center justify-between hover:bg-neutral-100 dark:hover:bg-neutral-800/50 transition-colors">' +
                '<div class="flex items-center gap-3">' +
                    '<div class="h-8 w-8 rounded-lg bg-neutral-200 dark:bg-neutral-800 flex items-center justify-center flex-shrink-0">' +
                        '<span class="text-[10px] font-bold text-neutral-600 dark:text-neutral-400 font-geist-mono">#' + invoice.invoice_id + '</span>' +
                    '</div>' +
                    '<div class="min-w-0">' +
                        '<p class="text-sm font-medium text-neutral-900 dark:text-white truncate">' + invoiceDate + '</p>' +
                        '<p class="text-xs text-neutral-500 dark:text-neutral-400 font-geist-mono">₱' + Number(totalAmount).toLocaleString() + '</p>' +
                    '</div>' +
                '</div>' +
                '<span class="inline-flex items-center rounded-full ' + statusColor + ' px-2 py-1 text-[10px] font-medium border flex-shrink-0 ml-2">' +
                    (invoice.payment_status || 'Unknown') +
                '</span>' +
            '</div>';
        });
        html += '</div>';

        container.innerHTML = html;
    }

    // Format date helper
    function formatRentalDate(dateString) {
        var date = new Date(dateString);
        return date.toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'short',
            day: 'numeric'
        });
    }

    // Close rental details modal
    function closeRentalDetailsModal() {
        globalThis.rentalDetailsModalState.isOpen = false;
        globalThis.rentalDetailsModalState.currentRentalId = null;
        globalThis.rentalDetailsModalState.currentRental = null;

        var modal = document.getElementById('rentalDetailsModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    // Populate the rental timeline
    function populateRentalTimeline(rental, responseData) {
        var isOverdue = responseData.is_overdue;

        // Created at
        var createdEl = document.getElementById('timelineCreatedAt');
        if (createdEl) {
            createdEl.textContent = rental.created_at ? formatRentalDateTime(rental.created_at) : '-';
        }

        // Released at
        var releasedAtEl = document.getElementById('timelineReleasedAt');
        var releasedByEl = document.getElementById('timelineReleasedBy');
        if (releasedAtEl) {
            releasedAtEl.textContent = rental.released_date ? formatRentalDateTime(rental.released_date) : '-';
        }
        if (releasedByEl) {
            var releasedBy = rental.released_by_user || rental.releasedBy;
            if (releasedBy) {
                releasedByEl.textContent = 'by ' + (releasedBy.name || ((releasedBy.first_name || '') + ' ' + (releasedBy.last_name || '')).trim());
            } else {
                releasedByEl.textContent = '';
            }
        }

        // Extension events
        var extensionContainer = document.getElementById('timelineExtensionEvents');
        if (extensionContainer) {
            extensionContainer.innerHTML = '';

            // Check for extensions
            if (rental.extension_count > 0) {
                var extensionHtml = '';

                // Show extension info if available
                if (rental.last_extended_at) {
                    var extendedBy = rental.extended_by_user || rental.extendedBy;
                    var extenderName = extendedBy ? (extendedBy.name || ((extendedBy.first_name || '') + ' ' + (extendedBy.last_name || '')).trim()) : '';

                    for (var i = 0; i < rental.extension_count; i++) {
                        var isLast = (i === rental.extension_count - 1);
                        extensionHtml += '<div class="relative flex items-start gap-3">';
                        extensionHtml += '<div class="absolute left-[-15px] top-1 h-4 w-4 rounded-full bg-violet-500 border-2 border-white dark:border-neutral-900 z-10 flex items-center justify-center">';
                        extensionHtml += '<div class="h-1.5 w-1.5 rounded-full bg-white"></div>';
                        extensionHtml += '</div>';
                        extensionHtml += '<div class="flex-1 min-w-0">';
                        extensionHtml += '<p class="text-xs font-medium text-neutral-900 dark:text-white">Extension #' + (i + 1) + '</p>';

                        if (isLast) {
                            extensionHtml += '<p class="text-xs text-neutral-500 dark:text-neutral-400">' + formatRentalDateTime(rental.last_extended_at) + '</p>';
                            if (extenderName) {
                                extensionHtml += '<p class="text-xs text-neutral-400 dark:text-neutral-500 mt-0.5">by ' + extenderName + '</p>';
                            }
                            if (rental.extension_reason) {
                                extensionHtml += '<p class="text-xs text-neutral-400 dark:text-neutral-500 mt-0.5 italic">"' + rental.extension_reason + '"</p>';
                            }
                        } else {
                            extensionHtml += '<p class="text-xs text-neutral-500 dark:text-neutral-400">Extended</p>';
                        }

                        extensionHtml += '</div></div>';
                    }
                }

                extensionContainer.innerHTML = extensionHtml;
            }
        }

        // Due date
        var dueLabelEl = document.getElementById('timelineDueLabel');
        var dueAtEl = document.getElementById('timelineDueAt');
        var dueIconEl = document.getElementById('timelineDueIcon');

        if (dueAtEl) {
            var originalDue = rental.original_due_date || rental.due_date;
            var currentDue = rental.due_date;

            if (originalDue !== currentDue && rental.extension_count > 0) {
                dueAtEl.innerHTML = formatRentalDate(currentDue) + '<br><span class="line-through text-neutral-400">' + formatRentalDate(originalDue) + '</span>';
            } else {
                dueAtEl.textContent = currentDue ? formatRentalDate(currentDue) : '-';
            }
        }

        if (dueLabelEl && dueIconEl) {
            if (isOverdue && !rental.return_date) {
                dueLabelEl.textContent = 'Due Date (Passed)';
                dueIconEl.className = 'absolute left-[-15px] top-1 h-4 w-4 rounded-full bg-rose-500 border-2 border-white dark:border-neutral-900 z-10 flex items-center justify-center';
            } else {
                dueLabelEl.textContent = 'Due Date';
                dueIconEl.className = 'absolute left-[-15px] top-1 h-4 w-4 rounded-full bg-amber-500 border-2 border-white dark:border-neutral-900 z-10 flex items-center justify-center';
            }
        }

        // Overdue event
        var overdueEvent = document.getElementById('timelineOverdueEvent');
        var overdueDaysEl = document.getElementById('timelineOverdueDays');

        if (overdueEvent && overdueDaysEl) {
            if (isOverdue && !rental.return_date) {
                overdueEvent.classList.remove('hidden');
                var dueDate = new Date(rental.due_date);
                var today = new Date();
                var diffTime = Math.abs(today - dueDate);
                var diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
                overdueDaysEl.textContent = diffDays + ' day' + (diffDays !== 1 ? 's' : '') + ' overdue • ₱' + Number(responseData.calculated_penalty || 0).toLocaleString() + ' penalty';
            } else {
                overdueEvent.classList.add('hidden');
            }
        }

        // Returned event
        var returnedEvent = document.getElementById('timelineReturnedEvent');
        var returnedAtEl = document.getElementById('timelineReturnedAt');
        var returnedByEl = document.getElementById('timelineReturnedBy');

        if (returnedEvent) {
            if (rental.return_date) {
                returnedEvent.classList.remove('hidden');

                if (returnedAtEl) {
                    returnedAtEl.textContent = formatRentalDateTime(rental.return_date);
                }

                if (returnedByEl) {
                    var returnedTo = rental.returned_to_user || rental.returnedTo;
                    if (returnedTo) {
                        returnedByEl.textContent = 'received by ' + (returnedTo.name || ((returnedTo.first_name || '') + ' ' + (returnedTo.last_name || '')).trim());
                    } else {
                        returnedByEl.textContent = '';
                    }
                }
            } else {
                returnedEvent.classList.add('hidden');
            }
        }
    }

    // Format date with time helper
    function formatRentalDateTime(dateString) {
        var date = new Date(dateString);
        return date.toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'short',
            day: 'numeric',
            hour: 'numeric',
            minute: '2-digit'
        });
    }

    // Process return from details modal
    function processReturnFromDetails() {
        var rentalId = globalThis.rentalDetailsModalState.currentRentalId;
        closeRentalDetailsModal();

        // Small delay to allow close animation
        setTimeout(function() {
            if (typeof openProcessReturnModalForRental === 'function') {
                openProcessReturnModalForRental(rentalId);
            } else {
                console.log('Process return modal not implemented yet for rental:', rentalId);
            }
        }, 100);
    }

    // Extend rental from details modal
    function extendRentalFromDetails() {
        var rentalId = globalThis.rentalDetailsModalState.currentRentalId;
        closeRentalDetailsModal();

        // Small delay to allow close animation
        setTimeout(function() {
            if (typeof openExtendRentalModal === 'function') {
                openExtendRentalModal(rentalId);
            } else {
                console.log('Extend rental modal not implemented yet for rental:', rentalId);
            }
        }, 100);
    }

    // Handle keyboard navigation
    document.addEventListener('keydown', function(e) {
        if (!globalThis.rentalDetailsModalState.isOpen) return;

        // Check if other modals are open
        var processReturnModalOpen = document.getElementById('processReturnModal') && !document.getElementById('processReturnModal').classList.contains('hidden');
        var extendRentalModalOpen = document.getElementById('extendRentalModal') && !document.getElementById('extendRentalModal').classList.contains('hidden');

        if (e.key === 'Escape' && !processReturnModalOpen && !extendRentalModalOpen) {
            closeRentalDetailsModal();
        }
    });

    // Close modal on backdrop click
    document.getElementById('rentalDetailsModal')?.addEventListener('click', function(e) {
        if (e.target === this && globalThis.rentalDetailsModalState.isOpen) {
            closeRentalDetailsModal();
        }
    });
</script>
