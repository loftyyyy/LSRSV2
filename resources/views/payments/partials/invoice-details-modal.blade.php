{{-- Invoice Details Modal --}}
<div id="invoiceDetailsModal" class="hidden fixed inset-0 z-50 flex items-center justify-center px-2 py-4 bg-black/60 backdrop-blur-sm overflow-y-auto">
    <div class="w-full max-w-5xl bg-white dark:bg-neutral-950 border border-neutral-200 dark:border-neutral-800 rounded-3xl shadow-2xl flex flex-col max-h-[calc(100vh-2rem)] my-auto">
        {{-- Header --}}
        <div class="flex-shrink-0 flex items-center justify-between px-6 py-4 border-b border-neutral-200 dark:border-neutral-800 bg-neutral-50/80 dark:bg-neutral-900/50 rounded-t-3xl">
            <div class="flex items-center gap-4">
                {{-- Invoice Icon --}}
                <div id="invoiceDetailsIcon" class="h-12 w-12 rounded-full bg-gradient-to-br from-violet-500 to-purple-600 flex items-center justify-center text-white shadow-lg">
                    <x-icon name="file-text" class="h-6 w-6" />
                </div>
                <div>
                    <p class="text-xs uppercase tracking-[0.2em] text-neutral-500 dark:text-neutral-500">Invoice Details</p>
                    <h3 id="invoiceDetailsTitle" class="text-lg font-semibold text-neutral-900 dark:text-white">Loading...</h3>
                </div>
            </div>
            <button onclick="closeInvoiceDetailsModal()" class="text-neutral-500 hover:text-neutral-800 dark:hover:text-neutral-200 text-xl transition-colors duration-200">&times;</button>
        </div>

        {{-- Content (scrollable) --}}
        <div id="invoiceDetailsContent" class="flex-1 overflow-y-auto">
            {{-- Loading State --}}
            <div id="invoiceDetailsLoading" class="flex items-center justify-center py-16">
                <div class="flex flex-col items-center gap-3">
                    <div class="h-8 w-8 animate-spin rounded-full border-2 border-violet-600 border-t-transparent"></div>
                    <p class="text-sm text-neutral-500 dark:text-neutral-400">Loading invoice details...</p>
                </div>
            </div>

            {{-- Invoice Details (hidden initially) --}}
            <div id="invoiceDetailsData" class="hidden">
                {{-- Main Content: Two Column Layout --}}
                <div class="flex flex-col lg:flex-row">
                    {{-- Left Column: Invoice Info --}}
                    <div class="lg:w-1/2 p-6 border-b lg:border-b-0 lg:border-r border-neutral-200 dark:border-neutral-800 space-y-5">
                        {{-- Status & ID Row --}}
                        <div class="grid grid-cols-2 gap-4">
                            {{-- Status Section --}}
                            <div class="space-y-2">
                                <div class="flex items-center gap-2 text-sm font-medium text-neutral-700 dark:text-neutral-300">
                                    <x-icon name="activity" class="h-4 w-4" />
                                    <span>Payment Status</span>
                                </div>
                                <div id="detailInvoiceStatusCard" class="rounded-xl p-3 border">
                                    <div class="flex items-center gap-2">
                                        <span id="detailInvoiceStatusDot" class="h-2.5 w-2.5 rounded-full"></span>
                                        <p id="detailInvoiceStatus" class="text-sm font-semibold">-</p>
                                    </div>
                                </div>
                            </div>

                            {{-- Invoice Type --}}
                            <div class="space-y-2">
                                <div class="flex items-center gap-2 text-sm font-medium text-neutral-700 dark:text-neutral-300">
                                    <x-icon name="tag" class="h-4 w-4" />
                                    <span>Invoice Type</span>
                                </div>
                                <div class="bg-neutral-50 dark:bg-neutral-900/50 rounded-xl p-3 border border-neutral-200 dark:border-neutral-800">
                                    <p id="detailInvoiceType" class="text-sm font-semibold text-neutral-900 dark:text-white capitalize">-</p>
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
                                    <p id="detailInvoiceCustomerName" class="text-sm font-medium text-neutral-900 dark:text-white">-</p>
                                </div>

                                {{-- Customer Contact --}}
                                <div class="grid grid-cols-2 gap-3">
                                    <div class="bg-neutral-50 dark:bg-neutral-900/50 rounded-xl p-3 border border-neutral-200 dark:border-neutral-800">
                                        <div class="flex items-center gap-1.5 mb-1">
                                            <x-icon name="mail" class="h-3 w-3 text-neutral-400" />
                                            <p class="text-xs text-neutral-500 dark:text-neutral-400">Email</p>
                                        </div>
                                        <p id="detailInvoiceCustomerEmail" class="text-sm font-medium text-neutral-900 dark:text-white break-all">-</p>
                                    </div>
                                    <div class="bg-neutral-50 dark:bg-neutral-900/50 rounded-xl p-3 border border-neutral-200 dark:border-neutral-800">
                                        <div class="flex items-center gap-1.5 mb-1">
                                            <x-icon name="phone" class="h-3 w-3 text-neutral-400" />
                                            <p class="text-xs text-neutral-500 dark:text-neutral-400">Contact Number</p>
                                        </div>
                                        <p id="detailInvoiceCustomerPhone" class="text-sm font-medium text-neutral-900 dark:text-white font-geist-mono">-</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Dates Section --}}
                        <div class="space-y-3">
                            <div class="flex items-center gap-2 text-sm font-medium text-neutral-700 dark:text-neutral-300">
                                <x-icon name="calendar" class="h-4 w-4" />
                                <span>Important Dates</span>
                            </div>

                            <div class="grid grid-cols-2 gap-3">
                                {{-- Invoice Date --}}
                                <div class="bg-neutral-50 dark:bg-neutral-900/50 rounded-xl p-3 border border-neutral-200 dark:border-neutral-800">
                                    <div class="flex items-center gap-1.5 mb-1">
                                        <x-icon name="calendar-check" class="h-3 w-3 text-neutral-400" />
                                        <p class="text-xs text-neutral-500 dark:text-neutral-400">Invoice Date</p>
                                    </div>
                                    <p id="detailInvoiceDate" class="text-sm font-medium text-neutral-900 dark:text-white">-</p>
                                </div>

                                {{-- Due Date --}}
                                <div class="bg-neutral-50 dark:bg-neutral-900/50 rounded-xl p-3 border border-neutral-200 dark:border-neutral-800">
                                    <div class="flex items-center gap-1.5 mb-1">
                                        <x-icon name="calendar-x" class="h-3 w-3 text-neutral-400" />
                                        <p class="text-xs text-neutral-500 dark:text-neutral-400">Due Date</p>
                                    </div>
                                    <p id="detailInvoiceDueDate" class="text-sm font-medium text-neutral-900 dark:text-white">-</p>
                                </div>
                            </div>
                        </div>

                        {{-- Linked Records Section --}}
                        <div class="space-y-3">
                            <div class="flex items-center gap-2 text-sm font-medium text-neutral-700 dark:text-neutral-300">
                                <x-icon name="link" class="h-4 w-4" />
                                <span>Linked Records</span>
                            </div>

                            <div class="grid grid-cols-2 gap-3">
                                {{-- Reservation --}}
                                <div id="detailInvoiceLinkedReservation" class="bg-neutral-50 dark:bg-neutral-900/50 rounded-xl p-3 border border-neutral-200 dark:border-neutral-800">
                                    <p class="text-sm text-neutral-500 dark:text-neutral-400">No linked reservation</p>
                                </div>

                                {{-- Rental --}}
                                <div id="detailInvoiceLinkedRental" class="bg-neutral-50 dark:bg-neutral-900/50 rounded-xl p-3 border border-neutral-200 dark:border-neutral-800">
                                    <p class="text-sm text-neutral-500 dark:text-neutral-400">No linked rental</p>
                                </div>
                            </div>
                        </div>

                    </div>

                    {{-- Right Column: Items & Financial --}}
                    <div class="lg:w-1/2 p-6 space-y-5">
                        {{-- Financial Summary --}}
                        <div class="space-y-3">
                            <div class="flex items-center gap-2 text-sm font-medium text-neutral-700 dark:text-neutral-300">
                                <x-icon name="credit-card" class="h-4 w-4" />
                                <span>Financial Summary</span>
                            </div>

                            <div class="grid grid-cols-3 gap-3">
                                {{-- Total Amount --}}
                                <div class="bg-neutral-50 dark:bg-neutral-900/50 rounded-xl p-4 border border-neutral-200 dark:border-neutral-800 text-center">
                                    <p class="text-xs text-neutral-500 dark:text-neutral-400 mb-1">Total Amount</p>
                                    <p id="detailInvoiceTotalAmount" class="text-xl font-bold text-neutral-900 dark:text-white font-geist-mono">₱0</p>
                                </div>

                                {{-- Amount Paid --}}
                                <div class="bg-emerald-50 dark:bg-emerald-900/10 rounded-xl p-4 border border-emerald-200 dark:border-emerald-800/50 text-center">
                                    <p class="text-xs text-emerald-600/70 dark:text-emerald-400/70 mb-1">Amount Paid</p>
                                    <p id="detailInvoiceAmountPaid" class="text-xl font-bold text-emerald-600 dark:text-emerald-400 font-geist-mono">₱0</p>
                                </div>

                                {{-- Balance Due --}}
                                <div id="detailInvoiceBalanceCard" class="bg-amber-50 dark:bg-amber-900/10 rounded-xl p-4 border border-amber-200 dark:border-amber-800/50 text-center">
                                    <p class="text-xs text-amber-600/70 dark:text-amber-400/70 mb-1">Balance Due</p>
                                    <p id="detailInvoiceBalanceDue" class="text-xl font-bold text-amber-600 dark:text-amber-400 font-geist-mono">₱0</p>
                                </div>
                            </div>
                        </div>

                        {{-- Line Items Section --}}
                        <div class="space-y-3">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2 text-sm font-medium text-neutral-700 dark:text-neutral-300">
                                    <x-icon name="list" class="h-4 w-4" />
                                    <span>Line Items</span>
                                </div>
                                <span id="detailInvoiceItemsCount" class="text-xs text-neutral-500 dark:text-neutral-400 font-geist-mono">0 items</span>
                            </div>

                            <div id="detailInvoiceItems" class="bg-neutral-50 dark:bg-neutral-900/50 rounded-xl border border-neutral-200 dark:border-neutral-800 overflow-hidden max-h-48 overflow-y-auto">
                                {{-- Items will be inserted here --}}
                                <div class="p-4 text-center text-sm text-neutral-500 dark:text-neutral-400">
                                    No items found
                                </div>
                            </div>
                            
                            {{-- Subtotal breakdown --}}
                            <div class="bg-neutral-50 dark:bg-neutral-900/50 rounded-xl p-3 border border-neutral-200 dark:border-neutral-800 flex flex-col gap-1.5 text-sm">
                                <div class="flex justify-between text-neutral-500 dark:text-neutral-400">
                                    <span>Subtotal:</span>
                                    <span id="detailInvoiceSubtotal" class="font-geist-mono">₱0</span>
                                </div>
                                <div class="flex justify-between text-rose-500 dark:text-rose-400 hidden" id="detailInvoiceDiscountRow">
                                    <span>Discount:</span>
                                    <span id="detailInvoiceDiscount" class="font-geist-mono">-₱0</span>
                                </div>
                                <div class="flex justify-between text-neutral-500 dark:text-neutral-400 hidden" id="detailInvoiceTaxRow">
                                    <span>Tax:</span>
                                    <span id="detailInvoiceTax" class="font-geist-mono">+₱0</span>
                                </div>
                            </div>
                        </div>

                        {{-- Payments Section --}}
                        <div class="space-y-3">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2 text-sm font-medium text-neutral-700 dark:text-neutral-300">
                                    <x-icon name="dollar-sign" class="h-4 w-4" />
                                    <span>Payment History</span>
                                </div>
                                <span id="detailInvoicePaymentsCount" class="text-xs text-neutral-500 dark:text-neutral-400 font-geist-mono">0 payments</span>
                            </div>

                            <div id="detailInvoicePayments" class="bg-neutral-50 dark:bg-neutral-900/50 rounded-xl border border-neutral-200 dark:border-neutral-800 overflow-hidden max-h-40 overflow-y-auto">
                                {{-- Payments will be inserted here --}}
                                <div class="p-4 text-center text-sm text-neutral-500 dark:text-neutral-400">
                                    No payments recorded
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Error State --}}
            <div id="invoiceDetailsError" class="hidden p-6">
                <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl p-4 flex items-center gap-3">
                    <x-icon name="alert-circle" class="h-5 w-5 text-red-500 flex-shrink-0" />
                    <p id="invoiceDetailsErrorMessage" class="text-sm text-red-600 dark:text-red-400">Failed to load invoice details</p>
                </div>
            </div>
        </div>

        {{-- Footer --}}
        <div class="flex-shrink-0 flex items-center justify-between px-6 py-4 border-t border-neutral-200 dark:border-neutral-800 bg-neutral-50/50 dark:bg-neutral-900/30 rounded-b-3xl">
            <button
                type="button"
                id="invoiceDetailsDownloadBtn"
                onclick="downloadInvoiceFromDetails()"
                class="inline-flex items-center gap-2 rounded-xl px-4 py-2.5 text-[14px] font-medium border border-neutral-300 bg-white text-neutral-700 hover:bg-neutral-100 dark:border-neutral-700 dark:bg-neutral-900 dark:text-neutral-200 dark:hover:bg-neutral-800 transition-colors duration-100 ease-in-out"
            >
                <x-icon name="download" class="h-4 w-4" />
                <span>Download PDF</span>
            </button>
            
            <button
                type="button"
                onclick="closeInvoiceDetailsModal()"
                class="inline-flex items-center gap-2 rounded-xl px-4 py-2.5 text-[14px] font-medium border border-neutral-300 bg-white text-neutral-700 hover:bg-neutral-100 dark:border-neutral-700 dark:bg-neutral-900 dark:text-neutral-200 dark:hover:bg-neutral-800 transition-colors duration-100 ease-in-out"
            >
                Close
            </button>
        </div>
    </div>
</div>

<style>
    /* Optimize modal performance */
    #invoiceDetailsModal {
        will-change: opacity;
        transform: translateZ(0);
        backface-visibility: hidden;
    }

    #invoiceDetailsModal .max-w-5xl {
        will-change: transform;
        transform: translateZ(0);
    }

    /* Custom scrollbars */
    #invoiceDetailsContent, #detailInvoiceItems, #detailInvoicePayments {
        scrollbar-width: thin;
        scrollbar-color: rgba(155, 155, 155, 0.5) transparent;
    }

    #invoiceDetailsContent::-webkit-scrollbar,
    #detailInvoiceItems::-webkit-scrollbar,
    #detailInvoicePayments::-webkit-scrollbar {
        width: 6px;
    }

    #invoiceDetailsContent::-webkit-scrollbar-track,
    #detailInvoiceItems::-webkit-scrollbar-track,
    #detailInvoicePayments::-webkit-scrollbar-track {
        background: transparent;
    }

    #invoiceDetailsContent::-webkit-scrollbar-thumb,
    #detailInvoiceItems::-webkit-scrollbar-thumb,
    #detailInvoicePayments::-webkit-scrollbar-thumb {
        background-color: rgba(155, 155, 155, 0.5);
        border-radius: 3px;
    }
</style>

<script>
    if (!globalThis.invoiceDetailsModalState) {
        globalThis.invoiceDetailsModalState = {
            isOpen: false,
            currentInvoiceId: null,
            currentInvoice: null
        };
    }

    async function openInvoiceDetailsModal(invoiceId) {
        globalThis.invoiceDetailsModalState.isOpen = true;
        globalThis.invoiceDetailsModalState.currentInvoiceId = invoiceId;

        var modal = document.getElementById('invoiceDetailsModal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');

        // Show loading state
        document.getElementById('invoiceDetailsLoading').classList.remove('hidden');
        document.getElementById('invoiceDetailsData').classList.add('hidden');
        document.getElementById('invoiceDetailsError').classList.add('hidden');
        document.getElementById('invoiceDetailsTitle').textContent = 'Loading...';

        try {
            var response = await window.axios.get(`/api/invoices/${invoiceId}`);
            var invoice = response.data.data;
            globalThis.invoiceDetailsModalState.currentInvoice = invoice;

            populateInvoiceDetails(invoice);

            // Hide loading, show data
            document.getElementById('invoiceDetailsLoading').classList.add('hidden');
            document.getElementById('invoiceDetailsData').classList.remove('hidden');
        } catch (error) {
            console.error('Error loading invoice details:', error);
            document.getElementById('invoiceDetailsLoading').classList.add('hidden');
            document.getElementById('invoiceDetailsError').classList.remove('hidden');
            document.getElementById('invoiceDetailsErrorMessage').textContent =
                error.response?.data?.message || error.message || 'Failed to load invoice details';
        }
    }

    function populateInvoiceDetails(invoice) {
        // Title
        document.getElementById('invoiceDetailsTitle').textContent = `Invoice ${invoice.invoice_number || '#' + String(invoice.invoice_id).padStart(3, '0')}`;

        // Status with dynamic styling
        var statusName = invoice.status?.status_name?.toLowerCase() || invoice.payment_status?.toLowerCase() || 'unknown';
        var statusCard = document.getElementById('detailInvoiceStatusCard');
        var statusDot = document.getElementById('detailInvoiceStatusDot');
        var statusText = document.getElementById('detailInvoiceStatus');

        var statusConfig = {
            'paid': {
                label: 'Paid',
                cardClass: 'bg-emerald-50 dark:bg-emerald-900/20 border-emerald-200 dark:border-emerald-800/50 text-emerald-700 dark:text-emerald-300',
                dotClass: 'bg-emerald-500'
            },
            'unpaid': {
                label: 'Unpaid',
                cardClass: 'bg-amber-50 dark:bg-amber-900/20 border-amber-200 dark:border-amber-800/50 text-amber-700 dark:text-amber-300',
                dotClass: 'bg-amber-500'
            },
            'partial': {
                label: 'Partial',
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
            label: invoice.status?.status_name || invoice.payment_status || 'Unknown',
            cardClass: 'bg-neutral-50 dark:bg-neutral-900/20 border-neutral-200 dark:border-neutral-800/50 text-neutral-700 dark:text-neutral-300',
            dotClass: 'bg-neutral-500'
        };
        
        statusCard.className = `rounded-xl p-3 border ${config.cardClass}`;
        statusDot.className = `h-2.5 w-2.5 rounded-full ${config.dotClass}`;
        statusText.textContent = config.label;

        // Invoice Type
        document.getElementById('detailInvoiceType').textContent = invoice.invoice_type === 'reservation' ? 'Deposit' : (invoice.invoice_type || '-');

        // Customer Info
        var customerName = invoice.customer
            ? `${invoice.customer.first_name} ${invoice.customer.last_name}`
            : '-';
        document.getElementById('detailInvoiceCustomerName').textContent = customerName;
        document.getElementById('detailInvoiceCustomerEmail').textContent = invoice.customer?.email || '-';
        document.getElementById('detailInvoiceCustomerPhone').textContent = invoice.customer?.contact_number || '-';

        // Dates
        document.getElementById('detailInvoiceDate').textContent = invoice.invoice_date
            ? formatInvoiceDate(invoice.invoice_date)
            : '-';
        document.getElementById('detailInvoiceDueDate').textContent = invoice.due_date
            ? formatInvoiceDate(invoice.due_date)
            : '-';

        // Linked Reservation
        var resEl = document.getElementById('detailInvoiceLinkedReservation');
        if (invoice.reservation_id) {
            resEl.innerHTML = `
                <div class="flex items-center gap-3">
                    <div class="h-8 w-8 rounded-lg bg-violet-100 dark:bg-violet-900/30 flex items-center justify-center flex-shrink-0">
                        <span class="text-[10px] font-bold text-violet-600 dark:text-violet-400 font-geist-mono">#${invoice.reservation_id}</span>
                    </div>
                    <p class="text-sm font-medium text-neutral-900 dark:text-white">Reservation</p>
                </div>
            `;
        } else {
            resEl.innerHTML = '<p class="text-sm text-neutral-500 dark:text-neutral-400">No linked reservation</p>';
        }

        // Linked Rental
        var rentEl = document.getElementById('detailInvoiceLinkedRental');
        if (invoice.rental_id) {
            rentEl.innerHTML = `
                <div class="flex items-center gap-3">
                    <div class="h-8 w-8 rounded-lg bg-sky-100 dark:bg-sky-900/30 flex items-center justify-center flex-shrink-0">
                        <span class="text-[10px] font-bold text-sky-600 dark:text-sky-400 font-geist-mono">#${invoice.rental_id}</span>
                    </div>
                    <p class="text-sm font-medium text-neutral-900 dark:text-white">Rental</p>
                </div>
            `;
        } else {
            rentEl.innerHTML = '<p class="text-sm text-neutral-500 dark:text-neutral-400">No linked rental</p>';
        }

        // Financials
        document.getElementById('detailInvoiceTotalAmount').textContent = `₱${Number(invoice.total_amount || 0).toLocaleString(undefined, {minimumFractionDigits: 2})}`;
        document.getElementById('detailInvoiceAmountPaid').textContent = `₱${Number(invoice.amount_paid || 0).toLocaleString(undefined, {minimumFractionDigits: 2})}`;
        
        var balanceDue = Number(invoice.balance_due || 0);
        document.getElementById('detailInvoiceBalanceDue').textContent = `₱${balanceDue.toLocaleString(undefined, {minimumFractionDigits: 2})}`;
        
        var balCard = document.getElementById('detailInvoiceBalanceCard');
        if (balanceDue <= 0) {
            balCard.className = 'bg-emerald-50 dark:bg-emerald-900/10 rounded-xl p-4 border border-emerald-200 dark:border-emerald-800/50 text-center';
            balCard.querySelector('p:first-child').className = 'text-xs text-emerald-600/70 dark:text-emerald-400/70 mb-1';
            balCard.querySelector('p:last-child').className = 'text-xl font-bold text-emerald-600 dark:text-emerald-400 font-geist-mono';
        } else {
            balCard.className = 'bg-amber-50 dark:bg-amber-900/10 rounded-xl p-4 border border-amber-200 dark:border-amber-800/50 text-center';
            balCard.querySelector('p:first-child').className = 'text-xs text-amber-600/70 dark:text-amber-400/70 mb-1';
            balCard.querySelector('p:last-child').className = 'text-xl font-bold text-amber-600 dark:text-amber-400 font-geist-mono';
        }

        // Subtotal Breakdown
        document.getElementById('detailInvoiceSubtotal').textContent = `₱${Number(invoice.subtotal || 0).toLocaleString(undefined, {minimumFractionDigits: 2})}`;
        
        var discount = Number(invoice.discount || 0);
        var discountRow = document.getElementById('detailInvoiceDiscountRow');
        if (discount > 0) {
            discountRow.classList.remove('hidden');
            document.getElementById('detailInvoiceDiscount').textContent = `-₱${discount.toLocaleString(undefined, {minimumFractionDigits: 2})}`;
        } else {
            discountRow.classList.add('hidden');
        }

        var tax = Number(invoice.tax || 0);
        var taxRow = document.getElementById('detailInvoiceTaxRow');
        if (tax > 0) {
            taxRow.classList.remove('hidden');
            document.getElementById('detailInvoiceTax').textContent = `+₱${tax.toLocaleString(undefined, {minimumFractionDigits: 2})}`;
        } else {
            taxRow.classList.add('hidden');
        }

        // Render items
        renderInvoiceItems(invoice.invoice_items || invoice.invoiceItems || []);

        // Render payments
        renderInvoicePayments(invoice.payments || []);
    }

    function renderInvoiceItems(items) {
        var container = document.getElementById('detailInvoiceItems');
        document.getElementById('detailInvoiceItemsCount').textContent = `${items.length} item${items.length !== 1 ? 's' : ''}`;

        if (!items || items.length === 0) {
            container.innerHTML = `
                <div class="p-4 text-center text-sm text-neutral-500 dark:text-neutral-400">
                    No items found
                </div>
            `;
            return;
        }

        container.innerHTML = `
            <div class="divide-y divide-neutral-200 dark:divide-neutral-800">
                ${items.map(item => {
                    var itemName = item.description || item.item?.name || 'Unknown Item';
                    var unitPrice = Number(item.unit_price || 0);
                    var quantity = Number(item.quantity || 1);
                    var totalPrice = Number(item.total_price || (unitPrice * quantity));
                    
                    var itemTypeLabel = item.item_type ? item.item_type.replace('_', ' ').toUpperCase() : 'ITEM';

                    return `
                        <div class="px-4 py-3 flex items-center justify-between hover:bg-neutral-100 dark:hover:bg-neutral-800/50 transition-colors">
                            <div class="flex items-center gap-3 flex-1 min-w-0">
                                <div class="min-w-0 flex-1">
                                    <div class="flex items-center gap-2">
                                        <p class="text-sm font-medium text-neutral-900 dark:text-white truncate">${itemName}</p>
                                        <span class="text-[9px] px-1.5 py-0.5 rounded-md bg-neutral-200 dark:bg-neutral-700 text-neutral-600 dark:text-neutral-300 font-medium">${itemTypeLabel}</span>
                                    </div>
                                    <p class="text-xs text-neutral-500 dark:text-neutral-400 truncate mt-0.5">₱${unitPrice.toLocaleString(undefined, {minimumFractionDigits: 2})} × ${quantity}</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-4 flex-shrink-0 ml-4">
                                <div class="bg-neutral-100 dark:bg-neutral-800/80 rounded-lg px-2 py-1 border border-neutral-200 dark:border-neutral-700">
                                    <p class="text-xs font-semibold text-neutral-700 dark:text-neutral-300 font-geist-mono">₱${totalPrice.toLocaleString(undefined, {minimumFractionDigits: 2})}</p>
                                </div>
                            </div>
                        </div>
                    `;
                }).join('')}
            </div>
        `;
    }

    function renderInvoicePayments(payments) {
        var container = document.getElementById('detailInvoicePayments');
        document.getElementById('detailInvoicePaymentsCount').textContent = `${payments.length} payment${payments.length !== 1 ? 's' : ''}`;

        if (!payments || payments.length === 0) {
            container.innerHTML = `
                <div class="p-4 text-center text-sm text-neutral-500 dark:text-neutral-400">
                    No payments recorded yet
                </div>
            `;
            return;
        }

        container.innerHTML = `
            <div class="divide-y divide-neutral-200 dark:divide-neutral-800">
                ${payments.map(payment => {
                    var payDate = payment.payment_date ? formatInvoiceDate(payment.payment_date) : '-';
                    var amount = Number(payment.amount || 0);
                    var method = payment.payment_method ? payment.payment_method.replace('_', ' ').toUpperCase() : 'UNKNOWN';
                    
                    var statusColor = payment.status_id === 2 || payment.status?.status_name === 'Completed'
                        ? 'text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-900/20 border-emerald-200 dark:border-emerald-800/50'
                        : 'text-amber-600 dark:text-amber-400 bg-amber-50 dark:bg-amber-900/20 border-amber-200 dark:border-amber-800/50';

                    return `
                        <div class="px-4 py-3 flex items-center justify-between hover:bg-neutral-100 dark:hover:bg-neutral-800/50 transition-colors">
                            <div class="flex items-center gap-3">
                                <div class="h-8 w-8 rounded-lg bg-emerald-100 dark:bg-emerald-900/30 flex items-center justify-center flex-shrink-0 text-emerald-600 dark:text-emerald-400">
                                    <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                </div>
                                <div class="min-w-0">
                                    <p class="text-sm font-medium text-neutral-900 dark:text-white truncate">${method}</p>
                                    <p class="text-xs text-neutral-500 dark:text-neutral-400">${payDate}</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="inline-flex items-center rounded-full ${statusColor} px-2 py-0.5 text-[10px] font-medium border flex-shrink-0">
                                    ${payment.status?.status_name || (payment.status_id === 2 ? 'Completed' : 'Pending')}
                                </span>
                                <p class="text-sm font-semibold text-neutral-900 dark:text-white font-geist-mono ml-2">₱${amount.toLocaleString(undefined, {minimumFractionDigits: 2})}</p>
                            </div>
                        </div>
                    `;
                }).join('')}
            </div>
        `;
    }

    function formatInvoiceDate(dateString) {
        var date = new Date(dateString);
        return date.toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'short',
            day: 'numeric'
        });
    }

    function closeInvoiceDetailsModal() {
        globalThis.invoiceDetailsModalState.isOpen = false;
        globalThis.invoiceDetailsModalState.currentInvoiceId = null;
        globalThis.invoiceDetailsModalState.currentInvoice = null;

        var modal = document.getElementById('invoiceDetailsModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }
    
    function downloadInvoiceFromDetails() {
        var id = globalThis.invoiceDetailsModalState.currentInvoiceId;
        if (id) {
            window.open(`/api/invoices/reports/invoice/${id}`, '_blank');
        }
    }

    // Handle keyboard navigation
    document.addEventListener('keydown', function(e) {
        if (!globalThis.invoiceDetailsModalState?.isOpen) return;

        if (e.key === 'Escape') {
            closeInvoiceDetailsModal();
        }
    });

    // Close modal on backdrop click
    document.getElementById('invoiceDetailsModal')?.addEventListener('click', function(e) {
        if (e.target === this && globalThis.invoiceDetailsModalState?.isOpen) {
            closeInvoiceDetailsModal();
        }
    });
</script>
