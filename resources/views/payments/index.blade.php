<!doctype html>
<html lang="en">
<head>
    {{-- Prevent flash of wrong theme --}}
    @include('components.theme-init')
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Payments · Love &amp; Styles</title>

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
<body class="min-h-screen flex font-geist bg-neutral-100 text-neutral-900 dark:bg-black dark:text-neutral-50">
<x-sidebar />

<main class="flex-1 ml-64 flex flex-col px-10 py-8 overflow-x-hidden overflow-y-auto bg-gradient-to-b from-neutral-100 via-neutral-100 to-neutral-200 dark:from-black dark:via-black dark:to-neutral-950">

    <header class="mb-8 transition-colors duration-300 ease-in-out">
        <div class="flex items-center justify-between gap-4">
            <div>
                <h1 class="text-3xl font-semibold tracking-tight text-neutral-900 dark:text-white transition-colors duration-300 ease-in-out">
                    Payments and Billing
                </h1>
                <p class="mt-1 text-sm font-geist-mono text-neutral-500 dark:text-neutral-400 transition-colors duration-300 ease-in-out">
                    Track payments and billing
                </p>
            </div>

            <div class="flex items-center gap-3 text-xs">
                <a href="/payments/reports" class="inline-flex items-center gap-2 rounded-xl px-3.5 py-2 text-[14px] font-medium border dark:hover:text-black hover:text-white border-neutral-300 bg-white text-neutral-700 dark:hover:bg-violet-600 hover:bg-violet-600 dark:border-neutral-800 dark:bg-neutral-950/80 dark:text-neutral-200 dark:hover:bg-neutral-900 transition-colors duration-300 ease-in-out">
                    <x-icon name="chart-column" class="h-4 w-4" />
                    <span>Reports</span>
                </a>

                <button onclick="openRecordPaymentModal()" class="inline-flex items-center gap-2 rounded-xl px-4 py-2 text-[14px] font-medium bg-violet-600 text-white hover:bg-violet-500 transition-colors duration-100 ease-in-out">
                    <x-icon name="plus" class="h-4 w-4" />
                    <span>Record Payment</span>
                </button>
            </div>
        </div>
    </header>


     {{-- Stats --}}
     <section class="grid grid-cols-4 gap-6 mb-8">
         <div class="rounded-2xl p-6 border border-neutral-200 bg-white dark:border-neutral-900 dark:bg-neutral-950/60 shadow-sm dark:shadow-[0_18px_60px_rgba(0,0,0,0.65)] transition-colors duration-300 ease-in-out">
             <div class="text-sm text-neutral-500 dark:text-neutral-400 mb-2 transition-colors duration-300 ease-in-out">
                 Total Revenue
             </div>
             <div id="totalRevenueCount" class="text-3xl font-semibold text-emerald-600 dark:text-emerald-400 transition-colors duration-300 ease-in-out">
                 ₱0
             </div>
         </div>
         <div class="rounded-2xl p-6 border border-neutral-200 bg-white dark:border-neutral-900 dark:bg-neutral-950/60 shadow-sm dark:shadow-[0_18px_60px_rgba(0,0,0,0.65)] transition-colors duration-300 ease-in-out">
             <div class="text-sm text-neutral-500 dark:text-neutral-400 mb-2 transition-colors duration-300 ease-in-out">
                 Pending Payments
             </div>
             <div id="pendingPaymentsCount" class="text-3xl font-semibold text-amber-600 dark:text-amber-400 transition-colors duration-300 ease-in-out">
                 ₱0
             </div>
         </div>
         <div class="rounded-2xl p-6 border border-neutral-200 bg-white dark:border-neutral-900 dark:bg-neutral-950/60 shadow-sm dark:shadow-[0_18px_60px_rgba(0,0,0,0.65)] transition-colors duration-300 ease-in-out">
             <div class="text-sm text-neutral-500 dark:text-neutral-400 mb-2 transition-colors duration-300 ease-in-out">
                 Overdue
             </div>
             <div id="overduePaymentsCount" class="text-3xl font-semibold text-rose-600 dark:text-rose-400 transition-colors duration-300 ease-in-out">
                 ₱0
             </div>
         </div>
         <div class="rounded-2xl p-6 border border-neutral-200 bg-white dark:border-neutral-900 dark:bg-neutral-950/60 shadow-sm dark:shadow-[0_18px_60px_rgba(0,0,0,0.65)] transition-colors duration-300 ease-in-out">
             <div class="text-sm text-neutral-500 dark:text-neutral-400 mb-2 transition-colors duration-300 ease-in-out">
                 Collection Rate
             </div>
             <div id="collectionRateCount" class="text-3xl font-semibold text-sky-600 dark:text-sky-400 transition-colors duration-300 ease-in-out">
                 0%
             </div>
         </div>
     </section>


    {{-- Filters + table --}}
    <section class="rounded-2xl p-6 border border-neutral-200 bg-white dark:border-neutral-900 dark:bg-neutral-950/60 transition-colors duration-300 ease-in-out">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-semibold tracking-tight text-neutral-900 dark:text-white transition-colors duration-300 ease-in-out">
                Payments and Invoices
            </h2>

            <div class="relative flex items-center gap-3">
                <!-- Search -->
                <div class="flex items-center gap-3 rounded-2xl px-4 py-2.5 border border-neutral-300 bg-white focus-within:border-neutral-500 dark:border-neutral-800 dark:bg-black/60 transition-colors duration-300 ease-in-out">
                    <x-icon name="search" class="h-4 w-4 text-neutral-500 transition-colors duration-300 ease-in-out" />
                    <input 
                        id="searchInput"
                        type="text" 
                        placeholder="Search by customer, item, or ID..." 
                        class="w-72 bg-transparent text-xs text-neutral-700 placeholder:text-neutral-400 dark:text-neutral-100 dark:placeholder:text-neutral-500 focus:outline-none transition-colors duration-300 ease-in-out">
                </div>

                <!-- Filter with toggle icons -->
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
                            <li class="px-4 py-2 hover:bg-neutral-100 dark:hover:bg-neutral-900 cursor-pointer transition-colors duration-200">Paid</li>
                            <li class="px-4 py-2 hover:bg-neutral-100 dark:hover:bg-neutral-900 cursor-pointer transition-colors duration-200">Pending</li>
                            <li class="px-4 py-2 hover:bg-neutral-100 dark:hover:bg-neutral-900 cursor-pointer transition-colors duration-200">Overdue</li>
                            <li class="px-4 py-2 hover:bg-neutral-100 dark:hover:bg-neutral-900 cursor-pointer transition-colors duration-200">Cancelled</li>
                        </ul>
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
                            <th class="py-2.5 pr-4 pl-4 font-medium">Invoice</th>
                            <th class="py-2.5 pr-4 font-medium">Customer</th>
                            <th class="py-2.5 pr-4 font-medium">Amount</th>
                            <th class="py-2.5 pr-4 font-medium">Paid</th>
                            <th class="py-2.5 pr-4 font-medium">Balance Due</th>
                            <th class="py-2.5 pr-4 font-medium">Date</th>
                            <th class="py-2.5 pr-4 font-medium">Type</th>
                            <th class="py-2.5 pr-4 font-medium text-left">Status</th>
                            <th class="py-2.5 pl-2 font-medium text-left">Action</th>
                        </tr>
                        </thead>

                        <tbody id="invoicesTableBody" class="text-[13px]">
                        <!-- Invoices will be loaded here -->
                        </tbody>
                    </table>
                </div>
                <div id="noInvoices" class="text-center py-8 text-neutral-500 dark:text-neutral-400">
                    <p>No invoices found</p>
                </div>
            </div>
    </section>
</main>


<script>
     const filterButton = document.getElementById('filter-button');
     const filterButtonText = document.getElementById('filter-button-text');
     const filterMenu = document.getElementById('filter-menu');
     const iconDown = document.getElementById('icon-down');
     const iconUp = document.getElementById('icon-up');
     const invoicesTableBody = document.getElementById('invoicesTableBody');
     const noInvoices = document.getElementById('noInvoices');

     let isOpen = false;
     let currentFilter = 'all';
     let highlightedInvoiceId = null;

     // Initialize filter button text to "All Status"
     function initializeFilterText() {
         const filterButtonText = document.getElementById('filter-button-text');
         if (filterButtonText) {
             filterButtonText.textContent = 'All Status';
         }
     }

     // Check for invoice_id in URL query parameters
     function checkForInvoiceIdInUrl() {
         const urlParams = new URLSearchParams(window.location.search);
         const invoiceId = urlParams.get('invoice_id');
         if (invoiceId) {
             highlightedInvoiceId = parseInt(invoiceId);
             // Remove the query parameter from the URL to keep it clean
             window.history.replaceState({}, document.title, window.location.pathname);
             // Immediately open the payment modal with this invoice
             setTimeout(() => {
                 openRecordPaymentModalWithInvoice(highlightedInvoiceId);
             }, 500);
         }
     }

     // Toggle dropdown
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

     // Update filter button text when a status is clicked
     filterMenu.querySelectorAll('li').forEach(item => {
         item.addEventListener('click', (e) => {
             e.stopPropagation();
             filterButtonText.textContent = item.textContent;
             isOpen = false;

            filterMenu.classList.add('opacity-0', 'scale-95', 'pointer-events-none');
            filterMenu.classList.remove('opacity-100', 'scale-100', 'pointer-events-auto');

            iconDown.classList.remove('hidden');
            iconUp.classList.add('hidden');

            // Map filter text to status value
            const filterMap = {
                'All Status': 'all',
                'Paid': 'completed',
                'Pending': 'pending',
                'Overdue': 'overdue',
                'Cancelled': 'cancelled'
            };
            currentFilter = filterMap[item.textContent] || 'all';
            loadInvoices();
        });
    });

     // Close dropdown when clicking outside
     document.addEventListener('click', () => {
         if (isOpen) {
             isOpen = false;
             filterMenu.classList.add('opacity-0', 'scale-95', 'pointer-events-none');
             filterMenu.classList.remove('opacity-100', 'scale-100', 'pointer-events-auto');

             iconDown.classList.remove('hidden');
             iconUp.classList.add('hidden');
         }
     });

     // Search input event listener
     let searchTimeout;
     const searchInput = document.getElementById('searchInput');
     if (searchInput) {
         searchInput.addEventListener('input', function(e) {
             // Clear previous timeout
             clearTimeout(searchTimeout);
             
             // Set a new timeout to avoid making API calls on every keystroke
             searchTimeout = setTimeout(() => {
                 const searchQuery = e.target.value;
                 loadInvoices(searchQuery);
             }, 300); // 300ms debounce
         });
     }

      // Load invoices from API
      function loadInvoices(searchQuery = '') {
          if (!window.axios) {
              console.warn('Axios not available, retrying in 100ms');
              setTimeout(() => loadInvoices(searchQuery), 100);
              return;
          }

          // Use 'all' as default if currentFilter is not set
          const filterToUse = currentFilter || 'all';
          let url = filterToUse === 'all' 
              ? '/api/invoices/monitor?status=all'
              : `/api/invoices/monitor?status=${filterToUse}`;
          
          // Add search parameter if provided
          if (searchQuery && searchQuery.trim() !== '') {
              url += `&search=${encodeURIComponent(searchQuery.trim())}`;
          }

          window.axios.get(url)
              .then(function(resp) {
                  const invoices = resp.data?.invoices?.data || [];
                  console.log('Loaded invoices:', invoices.length, resp.data);
                  renderInvoices(invoices);
              })
              .catch(function(err) {
                  console.error('Error loading invoices:', err);
                  invoicesTableBody.innerHTML = '';
                  noInvoices.classList.remove('hidden');
              });
      }

     // Render invoices in table
     function renderInvoices(invoices) {
         invoicesTableBody.innerHTML = '';

         if (!invoices || invoices.length === 0) {
             noInvoices.classList.remove('hidden');
             return;
         }

         noInvoices.classList.add('hidden');

         invoices.forEach(function(inv) {
             const row = document.createElement('tr');
             row.className = 'border-b border-neutral-200 hover:bg-neutral-100 dark:border-neutral-900/60 dark:hover:bg-white/5 transition-colors duration-300 ease-in-out cursor-pointer';
             
             // Highlight the invoice if it matches the URL parameter
             if (highlightedInvoiceId === inv.invoice_id) {
                 row.classList.add('bg-violet-50', 'dark:bg-violet-900/20');
             }
             
             row.addEventListener('click', function(e) {
                 // Don't trigger if clicking on action buttons
                 if (e.target.closest('button')) {
                     return;
                 }
                 
                 const statusName = inv.status?.status_name?.toLowerCase() || 'unknown';
                 
                 // If the invoice is fully paid, open invoice details instead of record payment modal
                 if (statusName === 'paid') {
                     openInvoiceDetailsModal(inv.invoice_id);
                 } else {
                     openRecordPaymentModalWithInvoice(inv.invoice_id);
                 }
             });

            const customer = inv.customer 
                ? `${inv.customer.first_name} ${inv.customer.last_name}` 
                : 'Unknown Customer';

            const invoiceDate = inv.invoice_date 
                ? new Date(inv.invoice_date).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' })
                : '-';

            const statusName = inv.status?.status_name?.toLowerCase() || 'unknown';
            let statusClass = 'bg-neutral-500/15 text-neutral-600 border-neutral-500/40 dark:text-neutral-300';
            
            if (statusName === 'paid') {
                statusClass = 'bg-emerald-500/15 text-emerald-600 border-emerald-500/40 dark:text-emerald-300';
            } else if (statusName === 'unpaid') {
                statusClass = 'bg-amber-500/15 text-amber-600 border-amber-500/40 dark:text-amber-300';
            }

            const invoiceType = inv.invoice_type 
                ? `<span class="capitalize">${inv.invoice_type === 'reservation' ? 'Deposit' : inv.invoice_type}</span>` 
                : '-';

            row.innerHTML = `
                <td class="py-3.5 pr-4 pl-4 text-neutral-500 font-geist-mono">${inv.invoice_number || 'N/A'}</td>
                <td class="py-3.5 pr-4 text-neutral-900 dark:text-neutral-100">${customer}</td>
                <td class="py-3.5 pr-4 text-neutral-600 dark:text-neutral-300 font-geist-mono">₱${parseFloat(inv.total_amount || 0).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</td>
                <td class="py-3.5 pr-4 text-neutral-600 dark:text-neutral-300 font-geist-mono">₱${parseFloat(inv.amount_paid || 0).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</td>
                <td class="py-3.5 pr-4 text-neutral-600 dark:text-neutral-300 font-geist-mono">₱${parseFloat(inv.balance_due || 0).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</td>
                <td class="py-3.5 pr-4 text-neutral-600 dark:text-neutral-300 font-geist-mono">${invoiceDate}</td>
                <td class="py-3.5 pr-4 text-neutral-600 dark:text-neutral-300 capitalize">${invoiceType}</td>
                <td class="py-3.5 pr-2">
                    <span class="inline-flex items-center rounded-full ${statusClass} px-2 py-1 text-[11px] font-medium border transition-colors duration-300 ease-in-out">
                        <span class="mr-1.5 h-1.5 w-1.5 rounded-full ${statusName === 'paid' ? 'bg-emerald-500' : statusName === 'unpaid' ? 'bg-amber-500' : 'bg-neutral-500'}"></span>
                        ${inv.status?.status_name || 'Unknown'}
                    </span>
                </td>
                <td class="py-3.5 pl-2 text-left text-neutral-500 dark:text-neutral-400">
                    <div class="inline-flex items-center gap-2">
                        <button class="rounded-lg p-1.5 hover:bg-violet-600 hover:text-white transition-colors duration-300 ease-in-out" aria-label="Download" onclick="downloadInvoice(${inv.invoice_id})">
                            <svg class="h-3.5 w-3.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
                        </button>
                    </div>
                </td>
            `;
            invoicesTableBody.appendChild(row);
        });
    }

    // Download invoice (placeholder)
    function downloadInvoice(invoiceId) {
        window.open(`/api/invoices/reports/invoice/${invoiceId}`, '_blank');
    }

     // Open record payment modal with selected invoice
     function openRecordPaymentModalWithInvoice(invoiceId) {
         // Set the pending invoice in the modal state
         if (window.recordPaymentModalState) {
             window.recordPaymentModalState.pendingInvoiceId = invoiceId;
         }
         openRecordPaymentModal();
         // Focus on amount field after modal is fully loaded
         setTimeout(function() {
             const amountInput = document.getElementById('paymentAmount');
             if (amountInput) {
                 amountInput.focus();
             }
         }, 700);
     }

     // Load payment metrics
     function loadPaymentMetrics() {
         if (!window.axios) return;

         window.axios.get('/api/invoices/monitor?status=all')
             .then(function(resp) {
                 const invoices = resp.data?.invoices?.data || [];
                 
                 // Calculate metrics
                 let totalRevenue = 0;
                 let pendingAmount = 0;
                 let overdueAmount = 0;
                 let paidAmount = 0;

                 invoices.forEach(function(inv) {
                     // Only calculate invoices of type "Rental" for Total Revenue
                     if (inv.invoice_type === 'rental') {
                         totalRevenue += parseFloat(inv.total_amount || 0);
                         paidAmount += parseFloat(inv.amount_paid || 0);
                     }
                     
                     const status = inv.status?.status_name?.toLowerCase() || '';
                     if (status === 'unpaid') {
                         pendingAmount += parseFloat(inv.balance_due || 0);
                     } else if (status === 'overdue') {
                         overdueAmount += parseFloat(inv.balance_due || 0);
                     }
                 });

                 // Calculate collection rate
                 const collectionRate = totalRevenue > 0 ? ((paidAmount / totalRevenue) * 100).toFixed(1) : 0;

                 // Update KPI displays
                 const totalRevenueEl = document.getElementById('totalRevenueCount');
                 const pendingPaymentsEl = document.getElementById('pendingPaymentsCount');
                 const overduePaymentsEl = document.getElementById('overduePaymentsCount');
                 const collectionRateEl = document.getElementById('collectionRateCount');

                 if (totalRevenueEl) totalRevenueEl.textContent = '₱' + totalRevenue.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
                 if (pendingPaymentsEl) pendingPaymentsEl.textContent = '₱' + pendingAmount.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
                 if (overduePaymentsEl) overduePaymentsEl.textContent = '₱' + overdueAmount.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
                 if (collectionRateEl) collectionRateEl.textContent = collectionRate + '%';
             })
             .catch(function(err) {
                 console.error('Error loading payment metrics:', err);
             });
     }

      // Load invoices on page load
      // Wait for axios to be available
      function initializePaymentsPage() {
          if (!window.axios) {
              setTimeout(initializePaymentsPage, 50);
              return;
          }
          
          initializeFilterText();
          checkForInvoiceIdInUrl();
          loadPaymentMetrics();
          loadInvoices();
      }
      
      initializePaymentsPage();
  </script>

{{-- Include Record Payment Modal --}}
@include('payments.partials.record-payment-modal')

{{-- Include Invoice Details Modal --}}
@include('payments.partials.invoice-details-modal')
</body>
</html>
