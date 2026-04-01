{{-- Record Payment Modal --}}
<div id="recordPaymentModal" class="hidden fixed inset-0 z-50 flex items-center justify-center px-2 py-6 bg-black/60 backdrop-blur-sm">
    <div class="w-full max-w-2xl bg-white dark:bg-neutral-950 border border-neutral-200 dark:border-neutral-800 rounded-3xl shadow-2xl">
        {{-- Header --}}
        <div class="flex items-center justify-between px-6 py-4 border-b border-neutral-200 dark:border-neutral-800 bg-neutral-50/80 dark:bg-neutral-900/50 rounded-t-3xl">
            <div>
                <p class="text-xs uppercase tracking-[0.2em] text-neutral-500 dark:text-neutral-500">New Payment</p>
                <h3 class="text-lg font-semibold text-neutral-900 dark:text-white">Record Payment</h3>
            </div>
            <button onclick="closeRecordPaymentModal()" class="text-neutral-500 hover:text-neutral-800 dark:hover:text-neutral-200 text-xl transition-colors duration-200">×</button>
        </div>

        {{-- Form --}}
        <form id="recordPaymentForm" class="px-8 py-6 space-y-5">
            @csrf

            <div class="grid grid-cols-2 gap-6">
                {{-- Invoice Selection --}}
                <div class="space-y-2">
                    <label class="text-sm font-medium text-neutral-700 dark:text-neutral-300">Invoice</label>
                    <div class="flex items-center rounded-2xl bg-white px-3 py-2.5 border border-neutral-300 focus-within:border-neutral-500 dark:border-neutral-800 dark:bg-black/60 transition-colors duration-300 ease-in-out">
                        <x-icon name="file-text" class="h-4 w-4 text-neutral-500 mr-2 transition-colors duration-300 ease-in-out" />
                        <select
                            name="invoice_id"
                            required
                            id="invoiceSelect"
                            class="w-full bg-transparent text-xs text-neutral-700 dark:text-neutral-100 focus:outline-none appearance-none cursor-pointer"
                        >
                            <option value="">Select Invoice</option>
                        </select>
                    </div>
                </div>

                {{-- Payment Method --}}
                <div class="space-y-2">
                    <label class="text-sm font-medium text-neutral-700 dark:text-neutral-300">Payment Method</label>
                    <div class="flex items-center rounded-2xl bg-white px-3 py-2.5 border border-neutral-300 focus-within:border-neutral-500 dark:border-neutral-800 dark:bg-black/60 transition-colors duration-300 ease-in-out">
                        <x-icon name="credit-card" class="h-4 w-4 text-neutral-500 mr-2 transition-colors duration-300 ease-in-out" />
                        <select
                            name="payment_method"
                            required
                            class="w-full bg-transparent text-xs text-neutral-700 dark:text-neutral-100 focus:outline-none appearance-none cursor-pointer"
                        >
                            <option value="cash">Cash</option>
                            <option value="card">Card</option>
                            <option value="gcash">GCash</option>
                            <option value="bank_transfer">Bank Transfer</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-6">
                {{-- Amount --}}
                <div class="space-y-2">
                    <label class="text-sm font-medium text-neutral-700 dark:text-neutral-300">Amount</label>
                    <div class="flex items-center rounded-2xl bg-white px-3 py-2.5 border border-neutral-300 focus-within:border-neutral-500 dark:border-neutral-800 dark:bg-black/60 transition-colors duration-300 ease-in-out">
                        <span class="text-neutral-500 mr-2">₱</span>
                        <input
                            type="number"
                            name="amount"
                            id="paymentAmount"
                            required
                            step="0.01"
                            min="0"
                            placeholder="0.00"
                            class="w-full bg-transparent text-xs text-neutral-700 placeholder:text-neutral-400 dark:text-neutral-100 dark:placeholder:text-neutral-500 focus:outline-none"
                        />
                    </div>
                    <p id="balanceInfo" class="text-xs text-neutral-500 dark:text-neutral-400 hidden">
                        Balance: <span id="balanceAmount" class="font-medium text-amber-600 dark:text-amber-400"></span>
                    </p>
                </div>

                {{-- Status --}}
                <div class="space-y-2">
                    <label class="text-sm font-medium text-neutral-700 dark:text-neutral-300">Status</label>
                    <div class="flex items-center rounded-2xl bg-white px-3 py-2.5 border border-neutral-300 focus-within:border-neutral-500 dark:border-neutral-800 dark:bg-black/60 transition-colors duration-300 ease-in-out">
                        <select
                            name="status_id"
                            required
                            class="w-full bg-transparent text-xs text-neutral-700 dark:text-neutral-100 focus:outline-none appearance-none cursor-pointer"
                        >
                            <option value="1">Pending</option>
                            <option value="2">Completed</option>
                            <option value="3">Failed</option>
                            <option value="4">Refunded</option>
                        </select>
                    </div>
                </div>
            </div>

            {{-- Notes --}}
            <div class="space-y-2">
                <label class="text-sm font-medium text-neutral-700 dark:text-neutral-300">Notes (Optional)</label>
                <div class="rounded-2xl bg-white px-3 py-2.5 border border-neutral-300 focus-within:border-neutral-500 dark:border-neutral-800 dark:bg-black/60 transition-colors duration-300 ease-in-out">
                    <textarea
                        name="notes"
                        rows="2"
                        placeholder="Add any additional notes..."
                        class="w-full bg-transparent text-xs text-neutral-700 placeholder:text-neutral-400 dark:text-neutral-100 dark:placeholder:text-neutral-500 focus:outline-none resize-none"
                    ></textarea>
                </div>
            </div>

            {{-- Error Message --}}
            <div id="recordPaymentError" class="hidden bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl p-3 flex items-center gap-2">
                <x-icon name="alert-circle" class="h-4 w-4 text-red-500" />
                <p class="text-xs text-red-600 dark:text-red-400"></p>
            </div>

            {{-- Success Message --}}
            <div id="recordPaymentSuccess" class="hidden bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800 rounded-xl p-3 flex items-center gap-2">
                <x-icon name="check-circle" class="h-4 w-4 text-emerald-500" />
                <p class="text-xs text-emerald-600 dark:text-emerald-400"></p>
            </div>

            {{-- Action Buttons --}}
            <div class="flex items-center justify-end gap-3 pt-2">
                <button
                    type="submit"
                    id="recordPaymentSubmitBtn"
                    class="inline-flex items-center gap-2 rounded-xl px-4 py-2 text-[14px] font-medium bg-violet-600 text-white hover:bg-violet-500 transition-colors duration-100 ease-in-out disabled:opacity-50 disabled:cursor-not-allowed"
                >
                    <span id="recordPaymentBtnText">Record Payment</span>
                    <span id="recordPaymentBtnLoading" class="hidden">Processing...</span>
                </button>
                <button
                    type="button"
                    onclick="closeRecordPaymentModal()"
                    class="inline-flex items-center gap-2 rounded-xl px-3.5 py-2 text-[14px] font-medium border border-neutral-300 bg-white text-neutral-700 hover:bg-neutral-100 dark:border-neutral-800 dark:bg-neutral-950/80 dark:text-neutral-200 dark:hover:bg-neutral-900 transition-colors duration-100 ease-in-out"
                >
                    Cancel
                </button>
            </div>
        </form>
    </div>
</div>

<style>
    #recordPaymentModal {
        will-change: opacity;
        transform: translateZ(0);
        backface-visibility: hidden;
    }

    #recordPaymentModal .max-w-2xl {
        will-change: transform;
        transform: translateZ(0);
    }

    select {
        background-color: transparent;
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e");
        background-position: right 0.5rem center;
        background-repeat: no-repeat;
        background-size: 1.5em 1.5em;
        padding-right: 2rem;
        -webkit-appearance: none;
        -moz-appearance: none;
        appearance: none;
    }

    .dark select {
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%239ca3af' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e");
    }
</style>

<script>
(function() {
    var state = {
        isOpen: false,
        isSubmitting: false
    };
    
    window.recordPaymentModalState = state;
    
    window.openRecordPaymentModal = function() {
        state.isOpen = true;
        var modal = document.getElementById('recordPaymentModal');
        if (!modal) return;
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        loadInvoices();
        setTimeout(function() {
            var sel = modal.querySelector('select[name="invoice_id"]');
            if (sel) sel.focus();
        }, 100);
        document.getElementById('recordPaymentForm').reset();
        hideMessages();
    };
    
    window.closeRecordPaymentModal = function() {
        state.isOpen = false;
        state.isSubmitting = false;
        var modal = document.getElementById('recordPaymentModal');
        if (!modal) return;
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.getElementById('recordPaymentForm').reset();
        hideMessages();
        updateSubmitButton();
        var bal = document.getElementById('balanceInfo');
        if (bal) bal.classList.add('hidden');
    };
    
    function hideMessages() {
        var err = document.getElementById('recordPaymentError');
        var suc = document.getElementById('recordPaymentSuccess');
        if (err) err.classList.add('hidden');
        if (suc) suc.classList.add('hidden');
    }
    
    function showError(msg) {
        var el = document.getElementById('recordPaymentError');
        if (el) {
            el.querySelector('p').textContent = msg;
            el.classList.remove('hidden');
        }
        var suc = document.getElementById('recordPaymentSuccess');
        if (suc) suc.classList.add('hidden');
    }
    
    function showSuccess(msg) {
        var el = document.getElementById('recordPaymentSuccess');
        if (el) {
            el.querySelector('p').textContent = msg;
            el.classList.remove('hidden');
        }
        var err = document.getElementById('recordPaymentError');
        if (err) err.classList.add('hidden');
    }
    
    function updateSubmitButton() {
        var btn = document.getElementById('recordPaymentSubmitBtn');
        var txt = document.getElementById('recordPaymentBtnText');
        var ldg = document.getElementById('recordPaymentBtnLoading');
        if (!btn || !txt || !ldg) return;
        btn.disabled = state.isSubmitting;
        txt.classList.toggle('hidden', state.isSubmitting);
        ldg.classList.toggle('hidden', !state.isSubmitting);
    }
    
    function loadInvoices() {
        if (!window.axios) return;
        window.axios.get('/api/invoices/monitor?status=pending')
            .then(function(resp) {
                var sel = document.getElementById('invoiceSelect');
                if (!sel) return;
                sel.innerHTML = '<option value="">Select Invoice</option>';
                var data = resp.data && resp.data.invoices && resp.data.invoices.data;
                if (data && data.length) {
                    data.forEach(function(inv) {
                        var opt = document.createElement('option');
                        opt.value = inv.invoice_id;
                        var name = inv.customer ? inv.customer.first_name + ' ' + inv.customer.last_name : 'Unknown';
                        opt.textContent = inv.invoice_number + ' - ' + name + ' (₱' + parseFloat(inv.balance_due).toFixed(2) + ')';
                        opt.dataset.balance = inv.balance_due;
                        sel.appendChild(opt);
                    });
                } else {
                    var opt = document.createElement('option');
                    opt.value = '';
                    opt.textContent = 'No pending invoices';
                    opt.disabled = true;
                    sel.appendChild(opt);
                }
            })
            .catch(function(err) {
                console.error('Error loading invoices:', err);
                var sel = document.getElementById('invoiceSelect');
                if (sel) sel.innerHTML = '<option value="">Error loading</option>';
            });
    }
    
    document.getElementById('invoiceSelect').addEventListener('change', function() {
        var opt = this.options[this.selectedIndex];
        var balInfo = document.getElementById('balanceInfo');
        var balAmt = document.getElementById('balanceAmount');
        var amtInp = document.getElementById('paymentAmount');
        if (opt && opt.value) {
            if (balInfo) balInfo.classList.remove('hidden');
            if (balAmt) balAmt.textContent = '₱' + parseFloat(opt.dataset.balance).toFixed(2);
            if (amtInp) {
                amtInp.max = opt.dataset.balance;
                amtInp.placeholder = 'Max: ₱' + parseFloat(opt.dataset.balance).toFixed(2);
            }
        } else {
            if (balInfo) balInfo.classList.add('hidden');
            if (amtInp) {
                amtInp.max = '';
                amtInp.placeholder = '0.00';
            }
        }
    });
    
    document.getElementById('recordPaymentForm').addEventListener('submit', function(e) {
        e.preventDefault();
        if (state.isSubmitting) return;
        
        hideMessages();
        var form = e.target;
        var data = new FormData(form);
        
        var errors = [];
        if (!data.get('invoice_id')) errors.push('Please select an invoice');
        if (!data.get('amount') || parseFloat(data.get('amount')) <= 0) errors.push('Please enter a valid amount');
        
        if (errors.length) { showError(errors[0]); return; }
        
        state.isSubmitting = true;
        updateSubmitButton();
        
        var payload = {
            invoice_id: data.get('invoice_id'),
            amount: parseFloat(data.get('amount')),
            payment_method: data.get('payment_method'),
            status_id: parseInt(data.get('status_id')),
            notes: data.get('notes') || null
        };
        
        window.axios.post('/api/payments', payload)
            .then(function(resp) {
                if (resp.data.message) {
                    showSuccess('Payment recorded successfully!');
                    setTimeout(function() {
                        window.closeRecordPaymentModal();
                        window.location.reload();
                    }, 1500);
                }
            })
            .catch(function(err) {
                console.error('Error:', err);
                showError(err.response?.data?.message || 'Network error. Please try again.');
            })
            .finally(function() {
                state.isSubmitting = false;
                updateSubmitButton();
            });
    });
    
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && state.isOpen) window.closeRecordPaymentModal();
    });
    
    var modal = document.getElementById('recordPaymentModal');
    if (modal) {
        modal.addEventListener('click', function(e) {
            if (e.target === modal && state.isOpen) window.closeRecordPaymentModal();
        });
    }
})();
</script>
