{{-- Extend Rental Modal --}}
<div id="extendRentalModal" class="hidden fixed inset-0 z-50 flex items-center justify-center px-2 py-4 bg-black/60 backdrop-blur-sm overflow-y-auto">
    <div class="w-full max-w-lg bg-white dark:bg-neutral-950 border border-neutral-200 dark:border-neutral-800 rounded-3xl shadow-2xl flex flex-col max-h-[calc(100vh-2rem)] my-auto">
        {{-- Header --}}
        <div class="flex-shrink-0 flex items-center justify-between px-6 py-4 border-b border-neutral-200 dark:border-neutral-800 bg-neutral-50/80 dark:bg-neutral-900/50 rounded-t-3xl">
            <div class="flex items-center gap-4">
                {{-- Extend Icon --}}
                <div class="h-12 w-12 rounded-full bg-gradient-to-br from-violet-500 to-purple-600 flex items-center justify-center text-white shadow-lg">
                    <x-icon name="calendar-plus" class="h-6 w-6" />
                </div>
                <div>
                    <p class="text-xs uppercase tracking-[0.2em] text-neutral-500 dark:text-neutral-500">Extend Rental</p>
                    <h3 id="extendRentalTitle" class="text-lg font-semibold text-neutral-900 dark:text-white">Extend Due Date</h3>
                </div>
            </div>
            <button onclick="closeExtendRentalModal()" class="text-neutral-500 hover:text-neutral-800 dark:hover:text-neutral-200 text-xl transition-colors duration-200">&times;</button>
        </div>

        {{-- Content --}}
        <div class="flex-1 overflow-y-auto p-6">
            {{-- Loading State --}}
            <div id="extendRentalLoading" class="hidden flex items-center justify-center py-12">
                <div class="flex flex-col items-center gap-3">
                    <div class="h-8 w-8 animate-spin rounded-full border-2 border-violet-600 border-t-transparent"></div>
                    <p class="text-sm text-neutral-500 dark:text-neutral-400">Loading rental data...</p>
                </div>
            </div>

            {{-- Form --}}
            <form id="extendRentalForm" class="space-y-5">
                {{-- Current Rental Info --}}
                <div id="extendRentalInfo" class="hidden rounded-xl border border-neutral-200 dark:border-neutral-800 bg-neutral-50/50 dark:bg-neutral-900/30 p-4 space-y-3">
                    <div class="flex items-center justify-between">
                        <div>
                            <p id="extendRentalCustomer" class="text-sm font-semibold text-neutral-900 dark:text-white"></p>
                            <p id="extendRentalItem" class="text-xs text-neutral-500 dark:text-neutral-400 mt-0.5"></p>
                        </div>
                        <div id="extendRentalExtensionBadge" class="hidden">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-violet-100 text-violet-700 dark:bg-violet-900/30 dark:text-violet-400">
                                <span id="extendRentalExtensionCount">0</span> extensions
                            </span>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4 pt-2 border-t border-neutral-200 dark:border-neutral-700">
                        <div>
                            <p class="text-xs text-neutral-500 dark:text-neutral-500">Release Date</p>
                            <p id="extendRentalReleaseDate" class="text-sm font-medium text-neutral-900 dark:text-white">-</p>
                        </div>
                        <div>
                            <p class="text-xs text-neutral-500 dark:text-neutral-500">Current Due Date</p>
                            <p id="extendRentalCurrentDueDate" class="text-sm font-medium text-neutral-900 dark:text-white">-</p>
                        </div>
                    </div>
                </div>

                {{-- Overdue Warning --}}
                <div id="extendRentalOverdueWarning" class="hidden p-4 rounded-xl bg-rose-50 dark:bg-rose-900/20 border border-rose-200 dark:border-rose-800/50">
                    <div class="flex items-center gap-2 text-rose-700 dark:text-rose-400">
                        <x-icon name="alert-triangle" class="h-5 w-5 flex-shrink-0" />
                        <p class="text-sm font-medium">This rental is overdue. Penalties must be settled before extending.</p>
                    </div>
                </div>

                <input type="hidden" id="extendRentalId" name="rental_id" value="" />

                {{-- New Due Date --}}
                <div>
                    <label class="block">
                        <span class="text-sm font-medium text-neutral-700 dark:text-neutral-300 flex items-center gap-2">
                            <x-icon name="calendar" class="h-4 w-4" />
                            New Due Date <span class="text-rose-500">*</span>
                        </span>
                        <input type="date" name="new_due_date" id="extendNewDueDate" required
                            class="mt-2 w-full rounded-xl border border-neutral-300 bg-white px-4 py-3 text-sm text-neutral-900 focus:border-violet-500 focus:outline-none focus:ring-2 focus:ring-violet-500/20 dark:border-neutral-700 dark:bg-neutral-900 dark:text-white transition-colors duration-200" />
                    </label>
                    <p id="extendDueDateHelp" class="mt-1.5 text-xs text-neutral-500 dark:text-neutral-500">Must be after the current due date</p>
                </div>

                {{-- Extension Reason --}}
                <div>
                    <label class="block">
                        <span class="text-sm font-medium text-neutral-700 dark:text-neutral-300 flex items-center gap-2">
                            <x-icon name="file-text" class="h-4 w-4" />
                            Reason for Extension
                        </span>
                        <textarea name="extension_reason" id="extendReason" rows="3" placeholder="Why is this rental being extended?"
                            class="mt-2 w-full rounded-xl border border-neutral-300 bg-white px-4 py-3 text-sm text-neutral-900 placeholder-neutral-400 focus:border-violet-500 focus:outline-none focus:ring-2 focus:ring-violet-500/20 dark:border-neutral-700 dark:bg-neutral-900 dark:text-white dark:placeholder-neutral-500 resize-none transition-colors duration-200"></textarea>
                    </label>
                </div>

                {{-- Error Display --}}
                <div id="extendRentalError" class="hidden p-4 rounded-xl bg-rose-50 dark:bg-rose-900/20 border border-rose-200 dark:border-rose-800/50">
                    <div class="flex items-center gap-2 text-rose-700 dark:text-rose-400">
                        <x-icon name="alert-circle" class="h-5 w-5 flex-shrink-0" />
                        <p id="extendRentalErrorText" class="text-sm font-medium"></p>
                    </div>
                </div>
            </form>
        </div>

        {{-- Footer --}}
        <div class="flex-shrink-0 flex items-center justify-end gap-3 px-6 py-4 border-t border-neutral-200 dark:border-neutral-800 bg-neutral-50/80 dark:bg-neutral-900/50 rounded-b-3xl">
            <button type="button" onclick="closeExtendRentalModal()" class="px-5 py-2.5 text-sm font-medium text-neutral-700 dark:text-neutral-300 hover:bg-neutral-100 dark:hover:bg-neutral-800 rounded-xl transition-colors duration-200">
                Cancel
            </button>
            <button type="button" id="extendRentalSubmitBtn" onclick="submitExtendRental()" disabled
                class="px-5 py-2.5 text-sm font-medium text-white dark:text-black bg-violet-600 hover:bg-violet-700 disabled:bg-neutral-300 disabled:text-neutral-500 dark:disabled:bg-neutral-700 dark:disabled:text-neutral-500 rounded-xl transition-colors duration-200 flex items-center gap-2">
                <x-icon name="calendar-plus" class="h-4 w-4" />
                <span>Extend Rental</span>
            </button>
        </div>
    </div>
</div>

<script>
    // Extend Rental Modal State
    globalThis.extendRentalModalState = {
        isOpen: false,
        rentalId: null,
        rental: null,
        currentDueDate: null
    };

    // Open extend rental modal for a specific rental
    function openExtendRentalModal(rentalId) {
        globalThis.extendRentalModalState.isOpen = true;
        globalThis.extendRentalModalState.rentalId = rentalId;

        // Reset form
        resetExtendRentalForm();

        // Show loading
        document.getElementById('extendRentalLoading').classList.remove('hidden');
        document.getElementById('extendRentalLoading').classList.add('flex');
        document.getElementById('extendRentalInfo').classList.add('hidden');

        // Show modal
        var modal = document.getElementById('extendRentalModal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');

        // Fetch rental details
        fetchRentalForExtend(rentalId);
    }

    // Close extend rental modal
    function closeExtendRentalModal() {
        globalThis.extendRentalModalState.isOpen = false;
        globalThis.extendRentalModalState.rentalId = null;
        globalThis.extendRentalModalState.rental = null;
        globalThis.extendRentalModalState.currentDueDate = null;

        var modal = document.getElementById('extendRentalModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    // Reset form
    function resetExtendRentalForm() {
        var form = document.getElementById('extendRentalForm');
        form.reset();

        document.getElementById('extendRentalId').value = '';
        document.getElementById('extendRentalError').classList.add('hidden');
        document.getElementById('extendRentalOverdueWarning').classList.add('hidden');
        document.getElementById('extendRentalSubmitBtn').disabled = true;
    }

    // Fetch rental details
    function fetchRentalForExtend(rentalId) {
        axios.get('/api/rentals/' + rentalId)
            .then(function(response) {
                var rental = response.data.data;
                var isOverdue = response.data.is_overdue || false;

                globalThis.extendRentalModalState.rental = rental;
                globalThis.extendRentalModalState.currentDueDate = rental.due_date;

                populateExtendForm(rental, isOverdue);
            })
            .catch(function(error) {
                console.error('Error fetching rental:', error);
                showExtendRentalError('Failed to load rental details. Please try again.');
            })
            .finally(function() {
                document.getElementById('extendRentalLoading').classList.add('hidden');
                document.getElementById('extendRentalLoading').classList.remove('flex');
            });
    }

    // Populate form with rental data
    function populateExtendForm(rental, isOverdue) {
        var customerName = rental.customer ? (rental.customer.first_name + ' ' + rental.customer.last_name) : 'Unknown';
        var itemCode = rental.item ? rental.item.item_code : 'N/A';
        var itemName = rental.item ? rental.item.name : 'Unknown Item';
        var releaseDate = rental.release_date ? formatExtendDate(rental.release_date) : 'N/A';
        var dueDate = rental.due_date ? formatExtendDate(rental.due_date) : 'N/A';
        var extensionCount = rental.extension_count || 0;

        // Update title
        document.getElementById('extendRentalTitle').textContent = 'Extend: ' + itemCode;

        // Update rental info
        document.getElementById('extendRentalCustomer').textContent = customerName;
        document.getElementById('extendRentalItem').textContent = itemCode + ' - ' + itemName;
        document.getElementById('extendRentalReleaseDate').textContent = releaseDate;
        document.getElementById('extendRentalCurrentDueDate').textContent = dueDate;

        // Show extension count badge if any
        if (extensionCount > 0) {
            document.getElementById('extendRentalExtensionCount').textContent = extensionCount;
            document.getElementById('extendRentalExtensionBadge').classList.remove('hidden');
        } else {
            document.getElementById('extendRentalExtensionBadge').classList.add('hidden');
        }

        document.getElementById('extendRentalInfo').classList.remove('hidden');

        // Set rental ID
        document.getElementById('extendRentalId').value = rental.rental_id;

        // Set minimum date for new due date (must be after current due date)
        var dueDateInput = document.getElementById('extendNewDueDate');
        if (rental.due_date) {
            var minDate = new Date(rental.due_date);
            minDate.setDate(minDate.getDate() + 1);
            dueDateInput.min = minDate.toISOString().split('T')[0];

            // Set default to 7 days after current due date
            var defaultDate = new Date(rental.due_date);
            defaultDate.setDate(defaultDate.getDate() + 7);
            dueDateInput.value = defaultDate.toISOString().split('T')[0];
        }

        // Show overdue warning and disable submit if overdue
        if (isOverdue) {
            document.getElementById('extendRentalOverdueWarning').classList.remove('hidden');
            document.getElementById('extendRentalSubmitBtn').disabled = true;
        } else {
            document.getElementById('extendRentalOverdueWarning').classList.add('hidden');
            document.getElementById('extendRentalSubmitBtn').disabled = false;
        }
    }

    // Show error message
    function showExtendRentalError(message) {
        document.getElementById('extendRentalErrorText').textContent = message;
        document.getElementById('extendRentalError').classList.remove('hidden');
    }

    // Submit extension
    function submitExtendRental() {
        var rentalId = globalThis.extendRentalModalState.rentalId;
        if (!rentalId) {
            showExtendRentalError('No rental selected.');
            return;
        }

        var newDueDate = document.getElementById('extendNewDueDate').value;
        var extensionReason = document.getElementById('extendReason').value;

        if (!newDueDate) {
            showExtendRentalError('New due date is required.');
            return;
        }

        // Validate new due date is after current due date
        var currentDueDate = globalThis.extendRentalModalState.currentDueDate;
        if (currentDueDate && new Date(newDueDate) <= new Date(currentDueDate)) {
            showExtendRentalError('New due date must be after the current due date.');
            return;
        }

        var data = {
            new_due_date: newDueDate,
            extension_reason: extensionReason || null
        };

        // Disable submit button and show loading
        var submitBtn = document.getElementById('extendRentalSubmitBtn');
        var originalHtml = submitBtn.innerHTML;
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<div class="h-4 w-4 animate-spin rounded-full border-2 border-white border-t-transparent dark:border-black dark:border-t-transparent"></div><span>Extending...</span>';

        // Hide previous errors
        document.getElementById('extendRentalError').classList.add('hidden');

        // Submit to API
        axios.post('/api/rentals/' + rentalId + '/extend', data)
            .then(function(response) {
                // Success - close modal and refresh
                closeExtendRentalModal();

                // Show success notification
                if (typeof showNotification === 'function') {
                    showNotification('Rental extended successfully', 'success');
                }

                // Refresh the rental list
                if (typeof fetchRentals === 'function') {
                    fetchRentals();
                }

                // If rental details modal is open, refresh it
                if (globalThis.rentalDetailsModalState && globalThis.rentalDetailsModalState.isOpen) {
                    openRentalDetailsModal(rentalId);
                }
            })
            .catch(function(error) {
                var message = 'Failed to extend rental. Please try again.';
                if (error.response && error.response.data && error.response.data.message) {
                    message = error.response.data.message;
                }
                showExtendRentalError(message);
            })
            .finally(function() {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalHtml;
            });
    }

    // Helper function
    function formatExtendDate(dateString) {
        var date = new Date(dateString);
        return date.toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'short',
            day: 'numeric'
        });
    }

    // Keyboard navigation
    document.addEventListener('keydown', function(e) {
        if (!globalThis.extendRentalModalState.isOpen) return;

        if (e.key === 'Escape') {
            closeExtendRentalModal();
        }
    });

    // Close on backdrop click
    document.getElementById('extendRentalModal')?.addEventListener('click', function(e) {
        if (e.target === this && globalThis.extendRentalModalState.isOpen) {
            closeExtendRentalModal();
        }
    });
</script>
