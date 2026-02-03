{{-- Add Item Modal --}}
<div id="addItemModal" class="hidden fixed inset-0 z-50 flex items-center justify-center px-2 py-6 bg-black/60 backdrop-blur-sm">
    <div class="w-full max-w-4xl bg-white dark:bg-neutral-950 border border-neutral-200 dark:border-neutral-800 rounded-3xl shadow-2xl">
        {{-- Header --}}
        <div class="flex items-center justify-between px-6 py-4 border-b border-neutral-200 dark:border-neutral-800 bg-neutral-50/80 dark:bg-neutral-900/50">
            <div>
                <p class="text-xs uppercase tracking-[0.2em] text-neutral-500 dark:text-neutral-500">New Item</p>
                <h3 class="text-lg font-semibold text-neutral-900 dark:text-white">Add Inventory Item</h3>
            </div>
            <button onclick="closeAddItemModal()" class="text-neutral-500 hover:text-neutral-800 dark:hover:text-neutral-200 text-xl transition-colors duration-200">×</button>
        </div>

        {{-- Form --}}
        <form id="addItemForm" class="px-8 py-6 space-y-5">
            @csrf

            <div class="grid grid-cols-2 gap-6">
                {{-- Item Information Column --}}
                <div class="space-y-4">
                    <div class="flex items-center gap-2 text-sm font-medium text-neutral-700 dark:text-neutral-300">
                        <x-icon name="package" class="h-4 w-4" />
                        <span>Item Information</span>
                    </div>

                    <div class="space-y-3">
                        {{-- Item Name --}}
                        <div class="space-y-2">
                            <label class="text-sm font-medium text-neutral-700 dark:text-neutral-300">Item Name *</label>
                            <div class="flex items-center rounded-2xl bg-white px-3 py-2.5 border border-neutral-300 focus-within:border-neutral-500 dark:border-neutral-800 dark:bg-black/60 transition-colors duration-300 ease-in-out">
                                <x-icon name="tag" class="h-4 w-4 text-neutral-500 mr-2 transition-colors duration-300 ease-in-out" />
                                <input
                                    type="text"
                                    name="name"
                                    required
                                    placeholder="e.g., Wedding Gown - Ivory"
                                    class="w-full bg-transparent text-xs text-neutral-700 placeholder:text-neutral-400 dark:text-neutral-100 dark:placeholder:text-neutral-500 focus:outline-none transition-colors duration-300 ease-in-out"
                                />
                            </div>
                        </div>

                        {{-- SKU --}}
                        <div class="space-y-2">
                            <label class="text-sm font-medium text-neutral-700 dark:text-neutral-300">SKU *</label>
                            <div class="flex items-center rounded-2xl bg-white px-3 py-2.5 border border-neutral-300 focus-within:border-neutral-500 dark:border-neutral-800 dark:bg-black/60 transition-colors duration-300 ease-in-out">
                                <x-icon name="barcode" class="h-4 w-4 text-neutral-500 mr-2 transition-colors duration-300 ease-in-out" />
                                <input
                                    type="text"
                                    name="sku"
                                    required
                                    placeholder="WG-001"
                                    class="w-full bg-transparent text-xs text-neutral-700 placeholder:text-neutral-400 dark:text-neutral-100 dark:placeholder:text-neutral-500 focus:outline-none transition-colors duration-300 ease-in-out"
                                />
                            </div>
                        </div>

                        {{-- Item Type --}}
                        <div class="space-y-2">
                            <label class="text-sm font-medium text-neutral-700 dark:text-neutral-300">Item Type *</label>
                            <div class="flex items-center rounded-2xl bg-white px-3 py-2.5 border border-neutral-300 focus-within:border-neutral-500 dark:border-neutral-800 dark:bg-black/60 transition-colors duration-300 ease-in-out">
                                <x-icon name="tag" class="h-4 w-4 text-neutral-500 mr-2 transition-colors duration-300 ease-in-out" />
                                <select
                                    name="item_type"
                                    required
                                    class="w-full bg-transparent text-xs text-neutral-700 dark:text-neutral-100 focus:outline-none transition-colors duration-300 ease-in-out"
                                >
                                    <option value="">Select type</option>
                                    <option value="gown">Gown</option>
                                    <option value="suit">Suit</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Physical Details Column --}}
                <div class="space-y-4">
                    <div class="flex items-center gap-2 text-sm font-medium text-neutral-700 dark:text-neutral-300">
                        <x-icon name="palette" class="h-4 w-4" />
                        <span>Details</span>
                    </div>

                    <div class="space-y-3">
                        {{-- Size --}}
                        <div class="space-y-2">
                            <label class="text-sm font-medium text-neutral-700 dark:text-neutral-300">Size *</label>
                            <div class="flex items-center rounded-2xl bg-white px-3 py-2.5 border border-neutral-300 focus-within:border-neutral-500 dark:border-neutral-800 dark:bg-black/60 transition-colors duration-300 ease-in-out">
                                <x-icon name="ruler" class="h-4 w-4 text-neutral-500 mr-2 transition-colors duration-300 ease-in-out" />
                                <input
                                    type="text"
                                    name="size"
                                    required
                                    placeholder="S, M, L, XL, etc."
                                    class="w-full bg-transparent text-xs text-neutral-700 placeholder:text-neutral-400 dark:text-neutral-100 dark:placeholder:text-neutral-500 focus:outline-none transition-colors duration-300 ease-in-out"
                                />
                            </div>
                        </div>

                        {{-- Color --}}
                        <div class="space-y-2">
                            <label class="text-sm font-medium text-neutral-700 dark:text-neutral-300">Color *</label>
                            <div class="flex items-center rounded-2xl bg-white px-3 py-2.5 border border-neutral-300 focus-within:border-neutral-500 dark:border-neutral-800 dark:bg-black/60 transition-colors duration-300 ease-in-out">
                                <x-icon name="palette" class="h-4 w-4 text-neutral-500 mr-2 transition-colors duration-300 ease-in-out" />
                                <input
                                    type="text"
                                    name="color"
                                    required
                                    placeholder="Ivory, White, Black, etc."
                                    class="w-full bg-transparent text-xs text-neutral-700 placeholder:text-neutral-400 dark:text-neutral-100 dark:placeholder:text-neutral-500 focus:outline-none transition-colors duration-300 ease-in-out"
                                />
                            </div>
                        </div>

                        {{-- Design --}}
                        <div class="space-y-2">
                            <label class="text-sm font-medium text-neutral-700 dark:text-neutral-300">Design *</label>
                            <div class="flex items-center rounded-2xl bg-white px-3 py-2.5 border border-neutral-300 focus-within:border-neutral-500 dark:border-neutral-800 dark:bg-black/60 transition-colors duration-300 ease-in-out">
                                <x-icon name="sparkles" class="h-4 w-4 text-neutral-500 mr-2 transition-colors duration-300 ease-in-out" />
                                <input
                                    type="text"
                                    name="design"
                                    required
                                    placeholder="e.g., Classic, Modern, Embellished"
                                    class="w-full bg-transparent text-xs text-neutral-700 placeholder:text-neutral-400 dark:text-neutral-100 dark:placeholder:text-neutral-500 focus:outline-none transition-colors duration-300 ease-in-out"
                                />
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Rental Price --}}
            <div class="space-y-3">
                <div class="flex items-center gap-2 text-sm font-medium text-neutral-700 dark:text-neutral-300">
                    <x-icon name="currency-peso" class="h-4 w-4" />
                    <span>Pricing</span>
                </div>
                <div class="space-y-2">
                    <label class="text-sm font-medium text-neutral-700 dark:text-neutral-300">Rental Price (PHP) *</label>
                    <div class="flex items-center rounded-2xl bg-white px-3 py-2.5 border border-neutral-300 focus-within:border-neutral-500 dark:border-neutral-800 dark:bg-black/60 transition-colors duration-300 ease-in-out">
                        <span class="text-neutral-500 mr-2">₱</span>
                        <input
                            type="number"
                            name="rental_price"
                            required
                            step="0.01"
                            min="0"
                            placeholder="0.00"
                            class="w-full bg-transparent text-xs text-neutral-700 placeholder:text-neutral-400 dark:text-neutral-100 dark:placeholder:text-neutral-500 focus:outline-none transition-colors duration-300 ease-in-out"
                        />
                    </div>
                </div>
            </div>

            {{-- Error Message --}}
            <div id="addItemError" class="hidden bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl p-3 flex items-center gap-2">
                <x-icon name="alert-circle" class="h-4 w-4 text-red-500" />
                <p class="text-xs text-red-600 dark:text-red-400"></p>
            </div>

            {{-- Success Message --}}
            <div id="addItemSuccess" class="hidden bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800 rounded-xl p-3 flex items-center gap-2">
                <x-icon name="check-circle" class="h-4 w-4 text-emerald-500" />
                <p class="text-xs text-emerald-600 dark:text-emerald-400"></p>
            </div>

            {{-- Action Buttons --}}
            <div class="flex items-center justify-end gap-3 pt-2">
                <button
                    type="submit"
                    id="addItemSubmitBtn"
                    class="inline-flex items-center gap-2 rounded-xl px-4 py-2 text-[14px] font-medium bg-violet-600 text-white dark:hover:text-white hover:text-black dark:text-black hover:bg-violet-500 shadow-violet-600/40 transition-colors duration-100 ease-in-out disabled:opacity-50 disabled:cursor-not-allowed"
                >
                    <span id="addItemBtnText">Add Item</span>
                    <span id="addItemBtnLoading" class="hidden">Adding...</span>
                </button>
                <button
                    type="button"
                    onclick="closeAddItemModal()"
                    class="inline-flex items-center gap-2 rounded-xl px-3.5 py-2 text-[14px] font-medium border dark:hover:text-black hover:text-white border-neutral-300 bg-white text-neutral-700 dark:hover:bg-violet-600 hover:bg-violet-600 dark:border-neutral-800 dark:bg-neutral-950/80 dark:text-neutral-200 dark:hover:bg-neutral-900 transition-colors duration-100 ease-in-out"
                >
                    Cancel
                </button>
            </div>
        </form>
    </div>
</div>

<style>
    /* Optimize modal performance */
    #addItemModal {
        will-change: opacity;
        transform: translateZ(0);
        backface-visibility: hidden;
    }

    #addItemModal .max-w-4xl {
        will-change: transform;
        transform: translateZ(0);
    }

    /* Fix select dropdown appearance */
    select {
        background-color: white;
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

    .dark select {
        background-color: rgb(12, 12, 12);
        color: #f5f5f5;
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%239ca3af' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e");
    }

    /* Style select option text for better visibility */
    select option {
        color: #374151;
        background-color: white;
    }

    .dark select option {
        color: #f5f5f5;
        background-color: rgb(17, 24, 39);
    }
</style>

<script>
    // Use globalThis to avoid redeclaration errors when Turbo navigates between pages
    if (!globalThis.addItemModalState) {
        globalThis.addItemModalState = {
            isOpen: false,
            isSubmitting: false
        };
    }

    // Short reference for easier access
    var addItemModalState = globalThis.addItemModalState;

    // Open modal
    function openAddItemModal() {
        globalThis.addItemModalState.isOpen = true;
        var modal = document.getElementById('addItemModal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');

        // Focus first input
        setTimeout(() => {
            modal.querySelector('input[name="name"]').focus();
        }, 100);

        // Reset form
        document.getElementById('addItemForm').reset();
        hideMessages();
    }

    // Close modal
    function closeAddItemModal() {
        globalThis.addItemModalState.isOpen = false;
        var modal = document.getElementById('addItemModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');

        // Reset form and state
        document.getElementById('addItemForm').reset();
        hideMessages();
        globalThis.addItemModalState.isSubmitting = false;
        updateSubmitButton();
    }

    // Hide messages
    function hideMessages() {
        document.getElementById('addItemError').classList.add('hidden');
        document.getElementById('addItemSuccess').classList.add('hidden');
    }

    // Show error message
    function showError(message) {
        var errorDiv = document.getElementById('addItemError');
        errorDiv.querySelector('p').textContent = message;
        errorDiv.classList.remove('hidden');
        document.getElementById('addItemSuccess').classList.add('hidden');
    }

    // Show success message
    function showSuccess(message) {
        var successDiv = document.getElementById('addItemSuccess');
        successDiv.querySelector('p').textContent = message;
        successDiv.classList.remove('hidden');
        document.getElementById('addItemError').classList.add('hidden');
    }

    // Update submit button state
    function updateSubmitButton() {
        var btn = document.getElementById('addItemSubmitBtn');
        var btnText = document.getElementById('addItemBtnText');
        var btnLoading = document.getElementById('addItemBtnLoading');

        if (globalThis.addItemModalState.isSubmitting) {
            btn.disabled = true;
            btnText.classList.add('hidden');
            btnLoading.classList.remove('hidden');
        } else {
            btn.disabled = false;
            btnText.classList.remove('hidden');
            btnLoading.classList.add('hidden');
        }
    }

    // Validate form
    function validateAddItemForm(formData) {
        var errors = [];

        // Required fields validation
        if (!formData.get('name')?.trim()) {
            errors.push('Item name is required');
        }
        if (!formData.get('sku')?.trim()) {
            errors.push('SKU is required');
        }
        if (!formData.get('item_type')?.trim()) {
            errors.push('Item type is required');
        }
        if (!formData.get('size')?.trim()) {
            errors.push('Size is required');
        }
        if (!formData.get('color')?.trim()) {
            errors.push('Color is required');
        }
        if (!formData.get('design')?.trim()) {
            errors.push('Design is required');
        }
        if (!formData.get('rental_price')?.trim()) {
            errors.push('Rental price is required');
        } else {
            var price = parseFloat(formData.get('rental_price'));
            if (isNaN(price) || price < 0) {
                errors.push('Please enter a valid rental price');
            }
        }

        return errors;
    }

    // Handle form submission
    document.getElementById('addItemForm').addEventListener('submit', async function(e) {
        e.preventDefault();

        if (globalThis.addItemModalState.isSubmitting) {
            return;
        }

        hideMessages();

        var formData = new FormData(this);
        var errors = validateAddItemForm(formData);

        if (errors.length > 0) {
            showError(errors[0]);
            return;
        }

        globalThis.addItemModalState.isSubmitting = true;
        updateSubmitButton();

        try {
            // Prepare API payload
            var payload = {
                name: formData.get('name'),
                sku: formData.get('sku'),
                item_type: formData.get('item_type'),
                size: formData.get('size'),
                color: formData.get('color'),
                design: formData.get('design'),
                rental_price: parseFloat(formData.get('rental_price'))
            };

            var response = await axios.post('/api/inventories', payload);
            var data = response.data;

            if (data.success) {
                showSuccess('Item added successfully!');

                // Close modal after success
                setTimeout(() => {
                    closeAddItemModal();
                    // Refresh the inventory data
                    fetchInventoryItems();
                    fetchStats();
                }, 1500);
            } else {
                showError(data.message || 'Failed to add item. Please try again.');
            }
        } catch (error) {
            console.error('Error adding item:', error);
            console.error('Error response:', error.response);
            var errorMessage = error.response?.data?.message || error.message || 'Network error. Please check your connection and try again.';
            showError(errorMessage);
        } finally {
            globalThis.addItemModalState.isSubmitting = false;
            updateSubmitButton();
        }
    });

    // Close modal on escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && globalThis.addItemModalState.isOpen) {
            closeAddItemModal();
        }
    });

    // Close modal on backdrop click
    document.getElementById('addItemModal').addEventListener('click', function(e) {
        if (e.target === this && globalThis.addItemModalState.isOpen) {
            closeAddItemModal();
        }
    });
</script>
