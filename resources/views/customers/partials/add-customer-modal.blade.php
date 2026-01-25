{{-- Add Customer Modal --}}
<div id="addCustomerModal" class="hidden fixed inset-0 z-50 flex items-center justify-center px-4 py-6 bg-black/60 backdrop-blur-sm">
    <div class="w-full max-w-4xl bg-white dark:bg-neutral-950 border border-neutral-200 dark:border-neutral-800 rounded-3xl shadow-2xl overflow-hidden max-h-[90vh] flex flex-col">
        {{-- Header --}}
        <div class="flex items-center justify-between px-6 py-4 border-b border-neutral-200 dark:border-neutral-800 bg-neutral-50/80 dark:bg-neutral-900/50">
            <div>
                <p class="text-xs uppercase tracking-[0.2em] text-neutral-500 dark:text-neutral-500">New Customer</p>
                <h3 class="text-lg font-semibold text-neutral-900 dark:text-white">Add Customer Details</h3>
            </div>
            <button onclick="closeAddCustomerModal()" class="text-neutral-500 hover:text-neutral-800 dark:hover:text-neutral-200 text-xl transition-colors duration-200">×</button>
        </div>

        {{-- Form --}}
        <form id="addCustomerForm" class="flex-1 px-6 py-5 space-y-4 overflow-y-auto">
            @csrf

            {{-- Personal Information Section --}}
            <div class="space-y-4">
                <div class="flex items-center gap-2 text-sm font-medium text-neutral-700 dark:text-neutral-300">
                    <x-icon name="user" class="h-4 w-4" />
                    <span>Personal Information</span>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <label class="text-sm font-medium text-neutral-700 dark:text-neutral-300">First Name</label>
                        <div class="flex items-center bg-white dark:bg-black border border-neutral-200 dark:border-neutral-800 rounded-xl px-3 shadow-sm focus-within:border-violet-500 focus-within:ring-2 focus-within:ring-violet-100 dark:focus-within:ring-violet-500/30 transition">
                            <x-icon name="user" class="text-neutral-400 mr-2 h-4 w-4" />
                            <input
                                type="text"
                                name="first_name"
                                required
                                placeholder="John"
                                class="w-full bg-transparent py-3 text-sm text-neutral-900 dark:text-neutral-100 placeholder-neutral-400 dark:placeholder-neutral-500 focus:outline-none"
                            />
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="text-sm font-medium text-neutral-700 dark:text-neutral-300">Last Name</label>
                        <div class="flex items-center bg-white dark:bg-black border border-neutral-200 dark:border-neutral-800 rounded-xl px-3 shadow-sm focus-within:border-violet-500 focus-within:ring-2 focus-within:ring-violet-100 dark:focus-within:ring-violet-500/30 transition">
                            <x-icon name="user" class="text-neutral-400 mr-2 h-4 w-4" />
                            <input
                                type="text"
                                name="last_name"
                                required
                                placeholder="Doe"
                                class="w-full bg-transparent py-3 text-sm text-neutral-900 dark:text-neutral-100 placeholder-neutral-400 dark:placeholder-neutral-500 focus:outline-none"
                            />
                        </div>
                    </div>
                </div>
            </div>

            {{-- Contact Information Section --}}
            <div class="space-y-4">
                <div class="flex items-center gap-2 text-sm font-medium text-neutral-700 dark:text-neutral-300">
                    <x-icon name="phone" class="h-4 w-4" />
                    <span>Contact Information</span>
                </div>

                <div class="space-y-4">
                    <div class="space-y-2">
                        <label class="text-sm font-medium text-neutral-700 dark:text-neutral-300">Email Address</label>
                        <div class="flex items-center bg-white dark:bg-black border border-neutral-200 dark:border-neutral-800 rounded-xl px-3 shadow-sm focus-within:border-violet-500 focus-within:ring-2 focus-within:ring-violet-100 dark:focus-within:ring-violet-500/30 transition">
                            <x-icon name="mail" class="text-neutral-400 mr-2 h-4 w-4" />
                            <input
                                type="email"
                                name="email"
                                required
                                placeholder="john.doe@example.com"
                                class="w-full bg-transparent py-3 text-sm text-neutral-900 dark:text-neutral-100 placeholder-neutral-400 dark:placeholder-neutral-500 focus:outline-none"
                            />
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="text-sm font-medium text-neutral-700 dark:text-neutral-300">Contact Number</label>
                        <div class="flex items-center bg-white dark:bg-black border border-neutral-200 dark:border-neutral-800 rounded-xl px-3 shadow-sm focus-within:border-violet-500 focus-within:ring-2 focus-within:ring-violet-100 dark:focus-within:ring-violet-500/30 transition">
                            <x-icon name="phone" class="text-neutral-400 mr-2 h-4 w-4" />
                            <input
                                type="tel"
                                name="contact_number"
                                required
                                placeholder="09123456789"
                                class="w-full bg-transparent py-3 text-sm text-neutral-900 dark:text-neutral-100 placeholder-neutral-400 dark:placeholder-neutral-500 focus:outline-none"
                            />
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="text-sm font-medium text-neutral-700 dark:text-neutral-300">Address</label>
                        <div class="flex items-center bg-white dark:bg-black border border-neutral-200 dark:border-neutral-800 rounded-xl px-3 shadow-sm focus-within:border-violet-500 focus-within:ring-2 focus-within:ring-violet-100 dark:focus-within:ring-violet-500/30 transition">
                            <x-icon name="map-pin" class="text-neutral-400 mr-2 h-4 w-4" />
                            <input
                                type="text"
                                name="address"
                                required
                                placeholder="123 Main St, City, Country"
                                class="w-full bg-transparent py-3 text-sm text-neutral-900 dark:text-neutral-100 placeholder-neutral-400 dark:placeholder-neutral-500 focus:outline-none"
                            />
                        </div>
                    </div>
                </div>
            </div>

            {{-- Status Section --}}
            <div class="space-y-4">
                <div class="flex items-center gap-2 text-sm font-medium text-neutral-700 dark:text-neutral-300">
                    <x-icon name="shield-check" class="h-4 w-4" />
                    <span>Status</span>
                </div>

                <div class="space-y-2">
                    <label class="text-sm font-medium text-neutral-700 dark:text-neutral-300">Customer Status</label>
                    <div class="flex items-center bg-white dark:bg-black border border-neutral-200 dark:border-neutral-800 rounded-xl px-3 shadow-sm focus-within:border-violet-500 focus-within:ring-2 focus-within:ring-violet-100 dark:focus-within:ring-violet-500/30 transition">
                        <x-icon name="flag" class="text-neutral-400 mr-2 h-4 w-4" />
                        <select
                            name="status_id"
                            required
                            class="w-full bg-transparent py-3 text-sm text-neutral-900 dark:text-neutral-100 focus:outline-none"
                        >
                            <option value="">Select Status</option>
                            <option value="1">Active</option>
                            <option value="2">Inactive</option>
                        </select>
                    </div>
                </div>
            </div>

            {{-- Measurements Section --}}
            <div class="space-y-4">
                <div class="flex items-center gap-2 text-sm font-medium text-neutral-700 dark:text-neutral-300">
                    <x-icon name="ruler" class="h-4 w-4" />
                    <span>Measurements (Optional)</span>
                </div>

                <div class="bg-neutral-50 dark:bg-neutral-900/50 border border-neutral-200 dark:border-neutral-800 rounded-2xl p-4">
                    <p class="text-xs text-neutral-600 dark:text-neutral-400 mb-3">
                        Add customer measurements for better fitting recommendations
                    </p>
                    <div class="grid grid-cols-2 gap-3">
                        <div class="space-y-2">
                            <label class="text-xs font-medium text-neutral-600 dark:text-neutral-400">Chest (inches)</label>
                            <input
                                type="number"
                                name="measurement[chest]"
                                step="0.1"
                                placeholder="38.0"
                                class="w-full bg-white dark:bg-black border border-neutral-200 dark:border-neutral-800 rounded-lg px-3 py-2 text-xs text-neutral-900 dark:text-neutral-100 placeholder-neutral-400 dark:placeholder-neutral-500 focus:outline-none focus:border-violet-500"
                            />
                        </div>
                        <div class="space-y-2">
                            <label class="text-xs font-medium text-neutral-600 dark:text-neutral-400">Waist (inches)</label>
                            <input
                                type="number"
                                name="measurement[waist]"
                                step="0.1"
                                placeholder="32.0"
                                class="w-full bg-white dark:bg-black border border-neutral-200 dark:border-neutral-800 rounded-lg px-3 py-2 text-xs text-neutral-900 dark:text-neutral-100 placeholder-neutral-400 dark:placeholder-neutral-500 focus:outline-none focus:border-violet-500"
                            />
                        </div>
                        <div class="space-y-2">
                            <label class="text-xs font-medium text-neutral-600 dark:text-neutral-400">Hips (inches)</label>
                            <input
                                type="number"
                                name="measurement[hips]"
                                step="0.1"
                                placeholder="40.0"
                                class="w-full bg-white dark:bg-black border border-neutral-200 dark:border-neutral-800 rounded-lg px-3 py-2 text-xs text-neutral-900 dark:text-neutral-100 placeholder-neutral-400 dark:placeholder-neutral-500 focus:outline-none focus:border-violet-500"
                            />
                        </div>
                        <div class="space-y-2">
                            <label class="text-xs font-medium text-neutral-600 dark:text-neutral-400">Height (inches)</label>
                            <input
                                type="number"
                                name="measurement[height]"
                                step="0.1"
                                placeholder="70.0"
                                class="w-full bg-white dark:bg-black border border-neutral-200 dark:border-neutral-800 rounded-lg px-3 py-2 text-xs text-neutral-900 dark:text-neutral-100 placeholder-neutral-400 dark:placeholder-neutral-500 focus:outline-none focus:border-violet-500"
                            />
                        </div>
                    </div>
                </div>
            </div>

            {{-- Error Message --}}
            <div id="addCustomerError" class="hidden bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl p-3 flex items-center gap-2">
                <x-icon name="alert-circle" class="h-4 w-4 text-red-500" />
                <p class="text-xs text-red-600 dark:text-red-400"></p>
            </div>

            {{-- Success Message --}}
            <div id="addCustomerSuccess" class="hidden bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800 rounded-xl p-3 flex items-center gap-2">
                <x-icon name="check-circle" class="h-4 w-4 text-emerald-500" />
                <p class="text-xs text-emerald-600 dark:text-emerald-400"></p>
            </div>

            {{-- Action Buttons --}}
            <div class="flex items-center gap-3 pt-2">
                <button
                    type="submit"
                    id="addCustomerSubmitBtn"
                    class="flex-1 bg-gradient-to-r from-violet-600 via-indigo-600 to-blue-600 hover:from-violet-700 hover:via-indigo-700 hover:to-blue-700 rounded-xl py-3 text-white font-semibold shadow-lg shadow-violet-600/20 transition-all duration-200 hover:translate-y-[-1px] disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none"
                >
                    <span id="addCustomerBtnText">Add Customer</span>
                    <span id="addCustomerBtnLoading" class="hidden">Adding Customer...</span>
                </button>
                <button
                    type="button"
                    onclick="closeAddCustomerModal()"
                    class="px-4 py-3 rounded-xl border border-neutral-200 dark:border-neutral-800 text-neutral-700 dark:text-neutral-200 hover:bg-neutral-50 dark:hover:bg-neutral-900 transition-colors duration-200"
                >
                    Cancel
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    // Modal state
    let addCustomerModalState = {
        isOpen: false,
        isSubmitting: false
    };

    // Open modal
    function openAddCustomerModal() {
        addCustomerModalState.isOpen = true;
        const modal = document.getElementById('addCustomerModal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');

        // Focus first input
        setTimeout(() => {
            modal.querySelector('input[name="first_name"]').focus();
        }, 100);

        // Reset form
        document.getElementById('addCustomerForm').reset();
        hideMessages();
    }

    // Close modal
    function closeAddCustomerModal() {
        addCustomerModalState.isOpen = false;
        const modal = document.getElementById('addCustomerModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');

        // Reset form and state
        document.getElementById('addCustomerForm').reset();
        hideMessages();
        addCustomerModalState.isSubmitting = false;
        updateSubmitButton();
    }

    // Hide messages
    function hideMessages() {
        document.getElementById('addCustomerError').classList.add('hidden');
        document.getElementById('addCustomerSuccess').classList.add('hidden');
    }

    // Show error message
    function showError(message) {
        const errorDiv = document.getElementById('addCustomerError');
        errorDiv.querySelector('p').textContent = message;
        errorDiv.classList.remove('hidden');
        document.getElementById('addCustomerSuccess').classList.add('hidden');
    }

    // Show success message
    function showSuccess(message) {
        const successDiv = document.getElementById('addCustomerSuccess');
        successDiv.querySelector('p').textContent = message;
        successDiv.classList.remove('hidden');
        document.getElementById('addCustomerError').classList.add('hidden');
    }

    // Update submit button state
    function updateSubmitButton() {
        const btn = document.getElementById('addCustomerSubmitBtn');
        const btnText = document.getElementById('addCustomerBtnText');
        const btnLoading = document.getElementById('addCustomerBtnLoading');

        if (addCustomerModalState.isSubmitting) {
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
    function validateAddCustomerForm(formData) {
        const errors = [];

        // Required fields validation
        if (!formData.get('first_name')?.trim()) {
            errors.push('First name is required');
        }
        if (!formData.get('last_name')?.trim()) {
            errors.push('Last name is required');
        }
        if (!formData.get('email')?.trim()) {
            errors.push('Email is required');
        } else {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(formData.get('email'))) {
                errors.push('Please enter a valid email address');
            }
        }
        if (!formData.get('contact_number')?.trim()) {
            errors.push('Contact number is required');
        }
        if (!formData.get('address')?.trim()) {
            errors.push('Address is required');
        }
        if (!formData.get('status_id')) {
            errors.push('Status is required');
        }

        return errors;
    }

    // Handle form submission
    document.getElementById('addCustomerForm').addEventListener('submit', async function(e) {
        e.preventDefault();

        if (addCustomerModalState.isSubmitting) {
            return;
        }

        hideMessages();

        const formData = new FormData(this);
        const errors = validateAddCustomerForm(formData);

        if (errors.length > 0) {
            showError(errors[0]);
            return;
        }

        addCustomerModalState.isSubmitting = true;
        updateSubmitButton();

        try {
            // Prepare measurements data
            const measurements = {};
            const measurementInputs = this.querySelectorAll('input[name^="measurement["]');
            measurementInputs.forEach(input => {
                if (input.value) {
                    const key = input.name.match(/measurement\[(.+)\]/)[1];
                    measurements[key] = parseFloat(input.value);
                }
            });

            // Prepare API payload
            const payload = {
                first_name: formData.get('first_name'),
                last_name: formData.get('last_name'),
                email: formData.get('email'),
                contact_number: formData.get('contact_number'),
                address: formData.get('address'),
                status_id: parseInt(formData.get('status_id')),
                measurement: Object.keys(measurements).length > 0 ? measurements : null
            };

            const response = await fetch('/api/customers', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(payload)
            });

            const data = await response.json();

            if (response.ok && data.success) {
                showSuccess('Customer added successfully!');

                // Close modal after success
                setTimeout(() => {
                    closeAddCustomerModal();
                    // Refresh the page to show the new customer
                    window.location.reload();
                }, 1500);
            } else {
                showError(data.message || 'Failed to add customer. Please try again.');
            }
        } catch (error) {
            console.error('Error adding customer:', error);
            showError('Network error. Please check your connection and try again.');
        } finally {
            addCustomerModalState.isSubmitting = false;
            updateSubmitButton();
        }
    });

    // Close modal on escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && addCustomerModalState.isOpen) {
            closeAddCustomerModal();
        }
    });

    // Close modal on backdrop click
    document.getElementById('addCustomerModal').addEventListener('click', function(e) {
        if (e.target === this && addCustomerModalState.isOpen) {
            closeAddCustomerModal();
        }
    });
</script>
