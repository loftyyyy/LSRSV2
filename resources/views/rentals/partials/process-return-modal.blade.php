{{-- Process Return Modal --}}
<div id="processReturnModal" class="hidden fixed inset-0 z-50 flex items-center justify-center px-2 py-4 bg-black/60 backdrop-blur-sm overflow-y-auto">
    <div class="w-full max-w-3xl bg-white dark:bg-neutral-950 border border-neutral-200 dark:border-neutral-800 rounded-3xl shadow-2xl flex flex-col max-h-[calc(100vh-2rem)] my-auto">
        {{-- Header --}}
        <div class="flex-shrink-0 flex items-center justify-between px-6 py-4 border-b border-neutral-200 dark:border-neutral-800 bg-neutral-50/80 dark:bg-neutral-900/50 rounded-t-3xl">
            <div class="flex items-center gap-4">
                {{-- Return Icon --}}
                <div class="h-12 w-12 rounded-full bg-gradient-to-br from-violet-500 to-purple-600 flex items-center justify-center text-white shadow-lg">
                    <x-icon name="arrow-left-circle" class="h-6 w-6" />
                </div>
                <div>
                    <p class="text-xs uppercase tracking-[0.2em] text-neutral-500 dark:text-neutral-500">Process Return</p>
                    <h3 id="processReturnTitle" class="text-lg font-semibold text-neutral-900 dark:text-white">Return Item</h3>
                </div>
            </div>
            <button onclick="closeProcessReturnModal()" class="text-neutral-500 hover:text-neutral-800 dark:hover:text-neutral-200 text-xl transition-colors duration-200">&times;</button>
        </div>

        {{-- Content (scrollable) --}}
        <div class="flex-1 overflow-y-auto">
            {{-- Loading State --}}
            <div id="processReturnLoading" class="hidden flex items-center justify-center py-16">
                <div class="flex flex-col items-center gap-3">
                    <div class="h-8 w-8 animate-spin rounded-full border-2 border-violet-600 border-t-transparent"></div>
                    <p class="text-sm text-neutral-500 dark:text-neutral-400">Loading rental data...</p>
                </div>
            </div>

            {{-- Form --}}
            <form id="processReturnForm" class="p-6 space-y-6">
                {{-- Rental Selection (shown when no rental pre-selected) --}}
                <div id="rentalSelectionSection" class="space-y-4">
                    <label class="block">
                        <span class="text-sm font-medium text-neutral-700 dark:text-neutral-300 flex items-center gap-2">
                            <x-icon name="search" class="h-4 w-4" />
                            Select Rental
                        </span>
                        <div class="relative mt-2">
                            <input type="text" id="rentalSearchInput" placeholder="Search by customer name or item code..."
                                   class="w-full rounded-xl border border-neutral-300 bg-white px-4 py-3 text-sm text-neutral-900 placeholder-neutral-400 focus:border-violet-500 focus:outline-none focus:ring-2 focus:ring-violet-500/20 dark:border-neutral-700 dark:bg-neutral-900 dark:text-white dark:placeholder-neutral-500 transition-colors duration-200" />
                        </div>
                        {{-- Search Results --}}
                        <div id="rentalSearchResults" class="hidden mt-2 max-h-48 overflow-y-auto rounded-xl border border-neutral-200 dark:border-neutral-800 bg-white dark:bg-neutral-900">
                        </div>
                    </label>

                    {{-- Selected Rental Display --}}
                    <div id="selectedRentalDisplay" class="hidden rounded-xl border border-violet-200 dark:border-violet-800/50 bg-violet-50/50 dark:bg-violet-950/20 p-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <p id="selectedRentalCustomer" class="text-sm font-semibold text-neutral-900 dark:text-white"></p>
                                <p id="selectedRentalItem" class="text-xs text-neutral-500 dark:text-neutral-400 mt-0.5"></p>
                                <p id="selectedRentalDates" class="text-xs text-neutral-500 dark:text-neutral-400 mt-0.5"></p>
                            </div>
                            <button type="button" onclick="clearSelectedRental()" class="text-neutral-400 hover:text-neutral-600 dark:hover:text-neutral-300 transition-colors duration-200">
                                <x-icon name="x" class="h-5 w-5" />
                            </button>
                        </div>
                        {{-- Overdue Warning --}}
                        <div id="selectedRentalOverdue" class="hidden mt-3 p-3 rounded-lg bg-rose-100 dark:bg-rose-900/30 border border-rose-200 dark:border-rose-800/50">
                            <div class="flex items-center gap-2 text-rose-700 dark:text-rose-400">
                                <x-icon name="alert-triangle" class="h-4 w-4" />
                                <span id="selectedRentalOverdueText" class="text-sm font-medium"></span>
                            </div>
                        </div>
                    </div>
                </div>

                <input type="hidden" id="selectedRentalId" name="rental_id" value="" />

                {{-- Return Details Section --}}
                <div id="returnDetailsSection" class="hidden space-y-5">
                    {{-- Return Date --}}
                    <div>
                        <label class="block">
                            <span class="text-sm font-medium text-neutral-700 dark:text-neutral-300 flex items-center gap-2">
                                <x-icon name="calendar" class="h-4 w-4" />
                                Return Date <span class="text-rose-500">*</span>
                            </span>
                            <input type="date" name="return_date" id="returnDate" required
                                   class="mt-2 w-full rounded-xl border border-neutral-300 bg-white px-4 py-3 text-sm text-neutral-900 focus:border-violet-500 focus:outline-none focus:ring-2 focus:ring-violet-500/20 dark:border-neutral-700 dark:bg-neutral-900 dark:text-white transition-colors duration-200" />
                        </label>
                    </div>

                    {{-- Notes Grid --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        {{-- Return Notes --}}
                        <div>
                            <label class="block">
                                <span class="text-sm font-medium text-neutral-700 dark:text-neutral-300 flex items-center gap-2">
                                    <x-icon name="file-text" class="h-4 w-4" />
                                    Return Notes
                                </span>
                                <textarea name="return_notes" id="returnNotes" rows="3" placeholder="Any notes about the return..."
                                          class="mt-2 w-full rounded-xl border border-neutral-300 bg-white px-4 py-3 text-sm text-neutral-900 placeholder-neutral-400 focus:border-violet-500 focus:outline-none focus:ring-2 focus:ring-violet-500/20 dark:border-neutral-700 dark:bg-neutral-900 dark:text-white dark:placeholder-neutral-500 resize-none transition-colors duration-200"></textarea>
                            </label>
                        </div>

                        {{-- Condition Notes --}}
                        <div>
                            <label class="block">
                                <span class="text-sm font-medium text-neutral-700 dark:text-neutral-300 flex items-center gap-2">
                                    <x-icon name="clipboard-check" class="h-4 w-4" />
                                    Condition Notes
                                </span>
                                <textarea name="condition_notes" id="conditionNotes" rows="3" placeholder="Notes about item condition..."
                                          class="mt-2 w-full rounded-xl border border-neutral-300 bg-white px-4 py-3 text-sm text-neutral-900 placeholder-neutral-400 focus:border-violet-500 focus:outline-none focus:ring-2 focus:ring-violet-500/20 dark:border-neutral-700 dark:bg-neutral-900 dark:text-white dark:placeholder-neutral-500 resize-none transition-colors duration-200"></textarea>
                            </label>
                        </div>
                    </div>
                </div> {{-- END returnDetailsSection --}}

                {{-- Error Display --}}
                <div id="processReturnError" class="hidden p-4 rounded-xl bg-rose-50 dark:bg-rose-900/20 border border-rose-200 dark:border-rose-800/50">
                    <div class="flex items-center gap-2 text-rose-700 dark:text-rose-400">
                        <x-icon name="alert-circle" class="h-5 w-5 flex-shrink-0" />
                        <p id="processReturnErrorText" class="text-sm font-medium"></p>
                    </div>
                </div>
            </form>
        </div>

        {{-- Footer --}}
        <div class="flex-shrink-0 flex items-center justify-end gap-3 px-6 py-4 border-t border-neutral-200 dark:border-neutral-800 bg-neutral-50/80 dark:bg-neutral-900/50 rounded-b-3xl">
            <button type="button" onclick="closeProcessReturnModal()" class="px-5 py-2.5 text-sm font-medium text-neutral-700 dark:text-neutral-300 hover:bg-neutral-100 dark:hover:bg-neutral-800 rounded-xl transition-colors duration-200">
                Cancel
            </button>
            <button type="button" id="processReturnSubmitBtn" onclick="submitProcessReturn()" disabled
                    class="px-5 py-2.5 text-sm font-medium text-white dark:text-black bg-violet-600 hover:bg-violet-700 disabled:bg-neutral-300 disabled:text-neutral-500 dark:disabled:bg-neutral-700 dark:disabled:text-neutral-500 rounded-xl transition-colors duration-200 flex items-center gap-2">
                <x-icon name="check" class="h-4 w-4" />
                <span>Process Return</span>
            </button>
        </div>
    </div>
</div>

<script>
    // Process Return Modal State
    globalThis.processReturnModalState = {
        isOpen: false,
        selectedRentalId: null,
        selectedRental: null,
        searchAbortController: null,
        deductionCount: 0
    };

    // Open process return modal (general, no rental pre-selected)
    globalThis.openProcessReturnModal = function openProcessReturnModal() {
        globalThis.processReturnModalState.isOpen = true;
        globalThis.processReturnModalState.selectedRentalId = null;
        globalThis.processReturnModalState.selectedRental = null;
        globalThis.processReturnModalState.deductionCount = 0;

        resetProcessReturnForm();

        var rentalSelectionSection = document.getElementById('rentalSelectionSection');
        if (rentalSelectionSection) rentalSelectionSection.classList.remove('hidden');

        var returnDetailsSection = document.getElementById('returnDetailsSection');
        if (returnDetailsSection) returnDetailsSection.classList.add('hidden');

        var modal = document.getElementById('processReturnModal');
        if (modal) {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        setTimeout(function() {
            var searchInput = document.getElementById('rentalSearchInput');
            if (searchInput) searchInput.focus();
        }, 100);
    };

    // Open process return modal for a specific rental (called from details modal)
    globalThis.openProcessReturnModalForRental = function openProcessReturnModalForRental(rentalId) {
        globalThis.processReturnModalState.isOpen = true;
        globalThis.processReturnModalState.selectedRentalId = rentalId;
        globalThis.processReturnModalState.deductionCount = 0;

        resetProcessReturnForm();

        var rentalSelectionSection = document.getElementById('rentalSelectionSection');
        if (rentalSelectionSection) rentalSelectionSection.classList.add('hidden');

        var processReturnLoading = document.getElementById('processReturnLoading');
        if (processReturnLoading) {
            processReturnLoading.classList.remove('hidden');
            processReturnLoading.classList.add('flex');
        }

        var returnDetailsSection = document.getElementById('returnDetailsSection');
        if (returnDetailsSection) returnDetailsSection.classList.add('hidden');

        var modal = document.getElementById('processReturnModal');
        if (modal) {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        fetchRentalForReturn(rentalId);
    };

    // Close process return modal
    globalThis.closeProcessReturnModal = function closeProcessReturnModal() {
        globalThis.processReturnModalState.isOpen = false;
        globalThis.processReturnModalState.selectedRentalId = null;
        globalThis.processReturnModalState.selectedRental = null;

        if (globalThis.processReturnModalState.searchAbortController) {
            globalThis.processReturnModalState.searchAbortController.abort();
            globalThis.processReturnModalState.searchAbortController = null;
        }

        var modal = document.getElementById('processReturnModal');
        if (modal) {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }
    };

    // Reset form to initial state
    function resetProcessReturnForm() {
        var form = document.getElementById('processReturnForm');
        if (form) form.reset();

        var selectedRentalId = document.getElementById('selectedRentalId');
        if (selectedRentalId) selectedRentalId.value = '';

        var selectedRentalDisplay = document.getElementById('selectedRentalDisplay');
        if (selectedRentalDisplay) selectedRentalDisplay.classList.add('hidden');

        var selectedRentalOverdue = document.getElementById('selectedRentalOverdue');
        if (selectedRentalOverdue) selectedRentalOverdue.classList.add('hidden');

        var rentalSearchResults = document.getElementById('rentalSearchResults');
        if (rentalSearchResults) {
            rentalSearchResults.classList.add('hidden');
            rentalSearchResults.innerHTML = '';
        }

        var depositReturnFields = document.getElementById('depositReturnFields');
        if (depositReturnFields) depositReturnFields.classList.add('hidden');

        var deductionsSection = document.getElementById('deductionsSection');
        if (deductionsSection) deductionsSection.classList.add('hidden');

        var forfeitNotesSection = document.getElementById('forfeitNotesSection');
        if (forfeitNotesSection) forfeitNotesSection.classList.add('hidden');

        var deductionsList = document.getElementById('deductionsList');
        if (deductionsList) deductionsList.innerHTML = '';

        var deductionSummary = document.getElementById('deductionSummary');
        if (deductionSummary) deductionSummary.classList.add('hidden');

        var processReturnError = document.getElementById('processReturnError');
        if (processReturnError) processReturnError.classList.add('hidden');

        var processReturnSubmitBtn = document.getElementById('processReturnSubmitBtn');
        if (processReturnSubmitBtn) processReturnSubmitBtn.disabled = true;

        var returnDate = document.getElementById('returnDate');
        if (returnDate) {
            returnDate.value = new Date().toISOString().split('T')[0];
        }
    }

    // ── Attach event listeners safely after DOM is ready ──────────────────────
    var rentalSearchTimer;

    document.addEventListener('DOMContentLoaded', function() {
        var rentalSearchInput = document.getElementById('rentalSearchInput');
        if (rentalSearchInput) {
            rentalSearchInput.addEventListener('input', function(e) {
                clearTimeout(rentalSearchTimer);
                var query = e.target.value.trim();

                if (query.length < 2) {
                    document.getElementById('rentalSearchResults').classList.add('hidden');
                    document.getElementById('rentalSearchResults').innerHTML = '';
                    return;
                }

                rentalSearchTimer = setTimeout(function() {
                    searchActiveRentals(query);
                }, 300);
            });
        }

        var depositReturnAction = document.getElementById('depositReturnAction');
        if (depositReturnAction) {
            depositReturnAction.addEventListener('change', function(e) {
                var action = e.target.value;
                var depositReturnFields = document.getElementById('depositReturnFields');
                var deductionsSection = document.getElementById('deductionsSection');
                var forfeitNotesSection = document.getElementById('forfeitNotesSection');

                depositReturnFields.classList.add('hidden');
                deductionsSection.classList.add('hidden');
                forfeitNotesSection.classList.add('hidden');

                if (action === 'full') {
                    depositReturnFields.classList.remove('hidden');
                } else if (action === 'partial') {
                    depositReturnFields.classList.remove('hidden');
                    deductionsSection.classList.remove('hidden');
                    if (globalThis.processReturnModalState.deductionCount === 0) {
                        addDeductionRow();
                    }
                } else if (action === 'forfeit') {
                    forfeitNotesSection.classList.remove('hidden');
                }
            });
        }

        var processReturnModalEl = document.getElementById('processReturnModal');
        if (processReturnModalEl) {
            processReturnModalEl.addEventListener('click', function(e) {
                if (e.target === this && globalThis.processReturnModalState.isOpen) {
                    closeProcessReturnModal();
                }
            });
        }
    });

    // Keyboard navigation
    document.addEventListener('keydown', function(e) {
        if (!globalThis.processReturnModalState.isOpen) return;
        if (e.key === 'Escape') {
            closeProcessReturnModal();
        }
    });

    // Search active rentals API call
    function searchActiveRentals(query) {
        if (globalThis.processReturnModalState.searchAbortController) {
            globalThis.processReturnModalState.searchAbortController.abort();
        }

        globalThis.processReturnModalState.searchAbortController = new AbortController();

        axios.get('/api/rentals', {
            params: {
                search: query,
                status: 'rented',
                per_page: 10
            },
            signal: globalThis.processReturnModalState.searchAbortController.signal
        })
            .then(function(response) {
                var rentals = response.data.data || [];
                displayRentalSearchResults(rentals);
            })
            .catch(function(error) {
                if (error.name !== 'CanceledError') {
                    console.error('Error searching rentals:', error);
                }
            });
    }

    // Display search results
    function displayRentalSearchResults(rentals) {
        var container = document.getElementById('rentalSearchResults');

        if (rentals.length === 0) {
            container.innerHTML = '<div class="px-4 py-3 text-sm text-neutral-500 dark:text-neutral-400">No active rentals found</div>';
            container.classList.remove('hidden');
            return;
        }

        var html = rentals.map(function(rental) {
            var customerName = rental.customer ? (rental.customer.first_name + ' ' + rental.customer.last_name) : 'Unknown Customer';
            var itemCode = rental.item ? (rental.item.sku || rental.item.item_code || 'Item #' + rental.item.item_id) : 'N/A';
            var itemName = rental.item ? (rental.item.name || 'Unknown Item') : 'Unknown Item';
            var dueDate = rental.due_date ? formatReturnDate(rental.due_date) : 'N/A';
            var isOverdue = rental.is_overdue || false;

            return '<div onclick="selectRental(' + rental.rental_id + ')" class="px-4 py-3 hover:bg-neutral-50 dark:hover:bg-neutral-800 cursor-pointer border-b border-neutral-100 dark:border-neutral-800 last:border-b-0 transition-colors duration-150">' +
                '<div class="flex items-center justify-between">' +
                '<div>' +
                '<p class="text-sm font-medium text-neutral-900 dark:text-white">' + customerName + '</p>' +
                '<p class="text-xs text-neutral-500 dark:text-neutral-400">' + itemCode + ' - ' + itemName + '</p>' +
                '</div>' +
                '<div class="text-right">' +
                '<p class="text-xs text-neutral-500 dark:text-neutral-400">Due: ' + dueDate + '</p>' +
                (isOverdue ? '<span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-rose-100 text-rose-700 dark:bg-rose-900/30 dark:text-rose-400">Overdue</span>' : '') +
                '</div>' +
                '</div>' +
                '</div>';
        }).join('');

        container.innerHTML = html;
        container.classList.remove('hidden');
    }

    // Select a rental from search results
    function selectRental(rentalId) {
        document.getElementById('rentalSearchResults').classList.add('hidden');
        document.getElementById('rentalSearchInput').value = '';

        globalThis.processReturnModalState.selectedRentalId = rentalId;
        document.getElementById('selectedRentalId').value = rentalId;

        document.getElementById('processReturnLoading').classList.remove('hidden');
        document.getElementById('processReturnLoading').classList.add('flex');

        fetchRentalForReturn(rentalId);
    }

    // Clear selected rental
    function clearSelectedRental() {
        globalThis.processReturnModalState.selectedRentalId = null;
        globalThis.processReturnModalState.selectedRental = null;

        document.getElementById('selectedRentalId').value = '';
        document.getElementById('selectedRentalDisplay').classList.add('hidden');
        document.getElementById('returnDetailsSection').classList.add('hidden');
        document.getElementById('processReturnSubmitBtn').disabled = true;
    }

    // Fetch rental details for return processing
    function fetchRentalForReturn(rentalId) {
        axios.get('/api/rentals/' + rentalId)
            .then(function(response) {
                var rental = response.data.data;
                var calculatedPenalty = response.data.calculated_penalty || 0;
                var isOverdue = response.data.is_overdue || false;

                globalThis.processReturnModalState.selectedRental = rental;
                globalThis.processReturnModalState.selectedRental.calculated_penalty = calculatedPenalty;
                globalThis.processReturnModalState.selectedRental.is_overdue = isOverdue;

                populateReturnForm(rental, calculatedPenalty, isOverdue);
            })
            .catch(function(error) {
                console.error('Error fetching rental:', error);
                showProcessReturnError('Failed to load rental details. Please try again.');
            })
            .finally(function() {
                document.getElementById('processReturnLoading').classList.add('hidden');
                document.getElementById('processReturnLoading').classList.remove('flex');
            });
    }

    // Populate return form with rental data
    function populateReturnForm(rental, calculatedPenalty, isOverdue) {
        var customerName = rental.customer ? (rental.customer.first_name + ' ' + rental.customer.last_name) : 'Unknown';
        var itemCode = rental.item ? (rental.item.sku || rental.item.item_code || 'Item #' + rental.item.item_id) : 'N/A';
        var itemName = rental.item ? (rental.item.name || 'Unknown Item') : 'Unknown Item';
        var releaseDate = rental.release_date ? formatReturnDate(rental.release_date) : 'N/A';
        var dueDate = rental.due_date ? formatReturnDate(rental.due_date) : 'N/A';

        document.getElementById('processReturnTitle').textContent = 'Return: ' + itemCode;

        document.getElementById('selectedRentalCustomer').textContent = customerName;
        document.getElementById('selectedRentalItem').textContent = itemCode + ' - ' + itemName;
        document.getElementById('selectedRentalDates').textContent = 'Released: ' + releaseDate + ' | Due: ' + dueDate;
        document.getElementById('selectedRentalDisplay').classList.remove('hidden');

        if (isOverdue && calculatedPenalty > 0) {
            document.getElementById('selectedRentalOverdueText').textContent = 'Overdue - Penalty: ₱' + formatNumber(calculatedPenalty);
            document.getElementById('selectedRentalOverdue').classList.remove('hidden');
        } else {
            document.getElementById('selectedRentalOverdue').classList.add('hidden');
        }

        document.getElementById('returnDetailsSection').classList.remove('hidden');
        document.getElementById('processReturnSubmitBtn').disabled = false;
    }

    // Add deduction row
    function addDeductionRow() {
        var index = globalThis.processReturnModalState.deductionCount++;
        var container = document.getElementById('deductionsList');

        var html = '<div id="deductionRow' + index + '" class="flex gap-3 items-start">' +
            '<div class="flex-1">' +
            '<input type="text" name="deductions[' + index + '][type]" placeholder="Type (e.g., Damage, Cleaning)" ' +
            'class="w-full rounded-xl border border-neutral-300 bg-white px-4 py-2.5 text-sm text-neutral-900 placeholder-neutral-400 focus:border-violet-500 focus:outline-none focus:ring-2 focus:ring-violet-500/20 dark:border-neutral-700 dark:bg-neutral-900 dark:text-white dark:placeholder-neutral-500 transition-colors duration-200" />' +
            '</div>' +
            '<div class="w-32">' +
            '<div class="relative">' +
            '<span class="absolute left-3 top-1/2 -translate-y-1/2 text-neutral-500 dark:text-neutral-400 text-sm">₱</span>' +
            '<input type="number" name="deductions[' + index + '][amount]" step="0.01" min="0" placeholder="0.00" onchange="updateDeductionSummary()" ' +
            'class="w-full rounded-xl border border-neutral-300 bg-white pl-7 pr-3 py-2.5 text-sm text-neutral-900 placeholder-neutral-400 focus:border-violet-500 focus:outline-none focus:ring-2 focus:ring-violet-500/20 dark:border-neutral-700 dark:bg-neutral-900 dark:text-white dark:placeholder-neutral-500 transition-colors duration-200" />' +
            '</div>' +
            '</div>' +
            '<div class="flex-1">' +
            '<input type="text" name="deductions[' + index + '][reason]" placeholder="Reason (optional)" ' +
            'class="w-full rounded-xl border border-neutral-300 bg-white px-4 py-2.5 text-sm text-neutral-900 placeholder-neutral-400 focus:border-violet-500 focus:outline-none focus:ring-2 focus:ring-violet-500/20 dark:border-neutral-700 dark:bg-neutral-900 dark:text-white dark:placeholder-neutral-500 transition-colors duration-200" />' +
            '</div>' +
            '<button type="button" onclick="removeDeductionRow(' + index + ')" class="p-2.5 text-neutral-400 hover:text-rose-500 transition-colors duration-200">' +
            '<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>' +
            '</button>' +
            '</div>';

        container.insertAdjacentHTML('beforeend', html);
        updateDeductionSummary();
    }

    // Remove deduction row
    function removeDeductionRow(index) {
        var row = document.getElementById('deductionRow' + index);
        if (row) {
            row.remove();
            updateDeductionSummary();
        }
    }

    // Update deduction summary
    function updateDeductionSummary() {
        var deductionInputs = document.querySelectorAll('#deductionsList input[name*="amount"]');
        var total = 0;

        deductionInputs.forEach(function(input) {
            total += parseFloat(input.value || 0);
        });

        var depositAmount = parseFloat(globalThis.processReturnModalState.selectedRental?.deposit_amount || 0);
        var amountToReturn = Math.max(0, depositAmount - total);

        document.getElementById('totalDeductions').textContent = '₱' + formatNumber(total);
        document.getElementById('amountToReturn').textContent = '₱' + formatNumber(amountToReturn);

        if (total > 0 || deductionInputs.length > 0) {
            document.getElementById('deductionSummary').classList.remove('hidden');
        } else {
            document.getElementById('deductionSummary').classList.add('hidden');
        }
    }

    // Show error message
    function showProcessReturnError(message) {
        document.getElementById('processReturnErrorText').textContent = message;
        document.getElementById('processReturnError').classList.remove('hidden');
    }

    // Submit return processing
    function submitProcessReturn() {
        var rentalId = globalThis.processReturnModalState.selectedRentalId;
        if (!rentalId) {
            showProcessReturnError('Please select a rental to process.');
            return;
        }

        var form = document.getElementById('processReturnForm');
        var formData = new FormData(form);

        var data = {
            return_date: formData.get('return_date'),
            return_notes: formData.get('return_notes') || null,
            condition_notes: formData.get('condition_notes') || null
        };

        var depositAction = formData.get('deposit_return_action');
        if (depositAction && depositAction !== 'hold') {
            data.deposit_return_action = depositAction;

            if (depositAction === 'full' || depositAction === 'partial') {
                data.deposit_return_method = formData.get('deposit_return_method');
                data.deposit_return_reference = formData.get('deposit_return_reference') || null;
                data.deposit_return_notes = formData.get('deposit_return_notes') || null;
            }

            if (depositAction === 'partial') {
                var deductions = [];
                var deductionRows = document.querySelectorAll('#deductionsList > div');
                deductionRows.forEach(function(row) {
                    var typeInput = row.querySelector('input[name*="type"]');
                    var amountInput = row.querySelector('input[name*="amount"]');
                    var reasonInput = row.querySelector('input[name*="reason"]');

                    if (typeInput && amountInput && typeInput.value && amountInput.value) {
                        deductions.push({
                            type: typeInput.value,
                            amount: parseFloat(amountInput.value),
                            reason: reasonInput ? reasonInput.value : null
                        });
                    }
                });
                data.deductions = deductions;
            }

            if (depositAction === 'forfeit') {
                data.deposit_return_notes = formData.get('deposit_return_notes') || document.getElementById('forfeitNotes').value || null;
            }
        }

        if (!data.return_date) {
            showProcessReturnError('Return date is required.');
            return;
        }

        if ((depositAction === 'full' || depositAction === 'partial') && !data.deposit_return_method) {
            showProcessReturnError('Deposit return method is required.');
            return;
        }

        var submitBtn = document.getElementById('processReturnSubmitBtn');
        var originalHtml = submitBtn.innerHTML;
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<div class="h-4 w-4 animate-spin rounded-full border-2 border-white border-t-transparent dark:border-black dark:border-t-transparent"></div><span>Processing...</span>';

        document.getElementById('processReturnError').classList.add('hidden');

        axios.post('/api/rentals/' + rentalId + '/return', data)
            .then(function(response) {
                closeProcessReturnModal();

                if (typeof showNotification === 'function') {
                    showNotification('Return processed successfully', 'success');
                }

                if (typeof fetchRentals === 'function') {
                    fetchRentals();
                }
                if (typeof fetchRentalStats === 'function') {
                    fetchRentalStats();
                }
            })
            .catch(function(error) {
                var message = 'Failed to process return. Please try again.';
                if (error.response && error.response.data && error.response.data.message) {
                    message = error.response.data.message;
                }
                showProcessReturnError(message);
            })
            .finally(function() {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalHtml;
            });
    }

    // Helper functions
    function formatReturnDate(dateString) {
        var date = new Date(dateString);
        return date.toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'short',
            day: 'numeric'
        });
    }

    function formatNumber(num) {
        return parseFloat(num).toLocaleString('en-PH', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
    }
</script>
