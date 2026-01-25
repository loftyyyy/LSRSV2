{{-- Add Customer Modal --}}
<div id="addCustomerModal" class="hidden fixed inset-0 z-50 flex items-center justify-center px-2 py-6 bg-black/60 backdrop-blur-sm">
    <div class="w-full max-w-6xl bg-white dark:bg-neutral-950 border border-neutral-200 dark:border-neutral-800 rounded-3xl shadow-2xl">
        {{-- Header --}}
        <div class="flex items-center justify-between px-6 py-4 border-b border-neutral-200 dark:border-neutral-800 bg-neutral-50/80 dark:bg-neutral-900/50">
            <div>
                <p class="text-xs uppercase tracking-[0.2em] text-neutral-500 dark:text-neutral-500">New Customer</p>
                <h3 class="text-lg font-semibold text-neutral-900 dark:text-white">Add Customer Details</h3>
            </div>
            <button onclick="closeAddCustomerModal()" class="text-neutral-500 hover:text-neutral-800 dark:hover:text-neutral-200 text-xl transition-colors duration-200">×</button>
        </div>

        {{-- Form --}}
        <form id="addCustomerForm" class="px-8 py-6 space-y-5">
            @csrf

            <div class="grid grid-cols-3 gap-6">
                {{-- Personal Information Column --}}
                <div class="space-y-4">
                    <div class="flex items-center gap-2 text-sm font-medium text-neutral-700 dark:text-neutral-300">
                        <x-icon name="user" class="h-4 w-4" />
                        <span>Personal Info</span>
                    </div>

                    <div class="space-y-3">
                        <div class="space-y-2">
                            <label class="text-sm font-medium text-neutral-700 dark:text-neutral-300">First Name</label>
                            <div class="flex items-center rounded-2xl bg-white px-3 py-2.5 border border-neutral-300 focus-within:border-neutral-500 dark:border-neutral-800 dark:bg-black/60 transition-colors duration-300 ease-in-out">
                                <x-icon name="user" class="h-4 w-4 text-neutral-500 mr-2 transition-colors duration-300 ease-in-out" />
                                <input
                                    type="text"
                                    name="first_name"
                                    required
                                    placeholder="John"
                                    class="w-full bg-transparent text-xs text-neutral-700 placeholder:text-neutral-400 dark:text-neutral-100 dark:placeholder:text-neutral-500 focus:outline-none transition-colors duration-300 ease-in-out"
                                />
                            </div>
                        </div>

                        <div class="space-y-2">
                            <label class="text-sm font-medium text-neutral-700 dark:text-neutral-300">Last Name</label>
                            <div class="flex items-center rounded-2xl bg-white px-3 py-2.5 border border-neutral-300 focus-within:border-neutral-500 dark:border-neutral-800 dark:bg-black/60 transition-colors duration-300 ease-in-out">
                                <x-icon name="user" class="h-4 w-4 text-neutral-500 mr-2 transition-colors duration-300 ease-in-out" />
                                <input
                                    type="text"
                                    name="last_name"
                                    required
                                    placeholder="Doe"
                                    class="w-full bg-transparent text-xs text-neutral-700 placeholder:text-neutral-400 dark:text-neutral-100 dark:placeholder:text-neutral-500 focus:outline-none transition-colors duration-300 ease-in-out"
                                />
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Contact Information Column --}}
                <div class="space-y-4">
                    <div class="flex items-center gap-2 text-sm font-medium text-neutral-700 dark:text-neutral-300">
                        <x-icon name="phone" class="h-4 w-4" />
                        <span>Contact Info</span>
                    </div>

                    <div class="space-y-3">
                        <div class="space-y-2">
                            <label class="text-sm font-medium text-neutral-700 dark:text-neutral-300">Email</label>
                            <div class="flex items-center rounded-2xl bg-white px-3 py-2.5 border border-neutral-300 focus-within:border-neutral-500 dark:border-neutral-800 dark:bg-black/60 transition-colors duration-300 ease-in-out">
                                <x-icon name="mail" class="h-4 w-4 text-neutral-500 mr-2 transition-colors duration-300 ease-in-out" />
                                <input
                                    type="email"
                                    name="email"
                                    required
                                    placeholder="john.doe@example.com"
                                    class="w-full bg-transparent text-xs text-neutral-700 placeholder:text-neutral-400 dark:text-neutral-100 dark:placeholder:text-neutral-500 focus:outline-none transition-colors duration-300 ease-in-out"
                                />
                            </div>
                        </div>

                        <div class="space-y-2">
                            <label class="text-sm font-medium text-neutral-700 dark:text-neutral-300">Phone</label>
                            <div class="flex items-center rounded-2xl bg-white px-3 py-2.5 border border-neutral-300 focus-within:border-neutral-500 dark:border-neutral-800 dark:bg-black/60 transition-colors duration-300 ease-in-out">
                                <x-icon name="phone" class="h-4 w-4 text-neutral-500 mr-2 transition-colors duration-300 ease-in-out" />
                                <input
                                    type="tel"
                                    name="contact_number"
                                    required
                                    placeholder="09123456789"
                                    class="w-full bg-transparent text-xs text-neutral-700 placeholder:text-neutral-400 dark:text-neutral-100 dark:placeholder:text-neutral-500 focus:outline-none transition-colors duration-300 ease-in-out"
                                />
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Status & Address Column --}}
                <div class="space-y-4">
                    <div class="flex items-center gap-2 text-sm font-medium text-neutral-700 dark:text-neutral-300">
                        <x-icon name="flag" class="h-4 w-4" />
                        <span>Status & Address</span>
                    </div>

                    <div class="space-y-3">
                        <div class="space-y-2">
                            <label class="text-sm font-medium text-neutral-700 dark:text-neutral-300">Status</label>
                            <div class="flex items-center rounded-2xl bg-white px-3 py-2.5 border border-neutral-300 focus-within:border-neutral-500 dark:border-neutral-800 dark:bg-black/60 transition-colors duration-300 ease-in-out">
                                <x-icon name="flag" class="h-4 w-4 text-neutral-500 mr-2 transition-colors duration-300 ease-in-out" />
                                <select
                                    name="status_id"
                                    required
                                    class="w-full bg-transparent text-xs text-neutral-700 dark:text-neutral-100 focus:outline-none transition-colors duration-300 ease-in-out"
                                >
                                    <option value="" class="text-neutral-700">Select Status</option>
                                    <option value="1" class="text-neutral-700">Active</option>
                                    <option value="2" class="text-neutral-700">Inactive</option>
                                </select>
                            </div>
                        </div>

                        <div class="space-y-2">
                            <label class="text-sm font-medium text-neutral-700 dark:text-neutral-300">Address</label>
                            <div class="flex items-center rounded-2xl bg-white px-3 py-2.5 border border-neutral-300 focus-within:border-neutral-500 dark:border-neutral-800 dark:bg-black/60 transition-colors duration-300 ease-in-out">
                                <x-icon name="map-pin" class="h-4 w-4 text-neutral-500 mr-2 transition-colors duration-300 ease-in-out" />
                                <input
                                    type="text"
                                    name="address"
                                    required
                                    placeholder="123 Main St"
                                    class="w-full bg-transparent text-xs text-neutral-700 placeholder:text-neutral-400 dark:text-neutral-100 dark:placeholder:text-neutral-500 focus:outline-none transition-colors duration-300 ease-in-out"
                                />
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Measurements Section --}}
            <div class="space-y-3">
                <div class="flex items-center gap-2 text-sm font-medium text-neutral-700 dark:text-neutral-300">
                    <x-icon name="ruler" class="h-4 w-4" />
                    <span>Measurements (Optional)</span>
                </div>

                <div class="bg-neutral-50 dark:bg-neutral-900/50 border border-neutral-200 dark:border-neutral-800 rounded-xl p-4">
                    <p class="text-xs text-neutral-600 dark:text-neutral-400 mb-3">
                        Add customer measurements for better fitting recommendations
                    </p>
                    <div class="grid grid-cols-4 gap-3">
                        <div class="space-y-2">
                            <label class="text-xs font-medium text-neutral-600 dark:text-neutral-400">Chest</label>
                            <div class="measurement-spinner rounded-lg border border-neutral-300 dark:border-neutral-800 bg-white dark:bg-black/60 transition-colors duration-300 ease-in-out">
                                <input
                                    type="number"
                                    name="measurement[chest]"
                                    step="0.1"
                                    placeholder="38.0"
                                    class="w-full bg-transparent px-3 py-2 text-xs text-neutral-700 placeholder:text-neutral-400 dark:text-neutral-100 dark:placeholder:text-neutral-500 focus:outline-none"
                                />
                                <div class="spinner-buttons">
                                    <button type="button" class="spinner-btn" data-input="measurement[chest]" data-action="up">▲</button>
                                    <button type="button" class="spinner-btn" data-input="measurement[chest]" data-action="down">▼</button>
                                </div>
                            </div>
                        </div>
                        <div class="space-y-2">
                            <label class="text-xs font-medium text-neutral-600 dark:text-neutral-400">Waist</label>
                            <div class="measurement-spinner rounded-lg border border-neutral-300 dark:border-neutral-800 bg-white dark:bg-black/60 transition-colors duration-300 ease-in-out">
                                <input
                                    type="number"
                                    name="measurement[waist]"
                                    step="0.1"
                                    placeholder="32.0"
                                    class="w-full bg-transparent px-3 py-2 text-xs text-neutral-700 placeholder:text-neutral-400 dark:text-neutral-100 dark:placeholder:text-neutral-500 focus:outline-none"
                                />
                                <div class="spinner-buttons">
                                    <button type="button" class="spinner-btn" data-input="measurement[waist]" data-action="up">▲</button>
                                    <button type="button" class="spinner-btn" data-input="measurement[waist]" data-action="down">▼</button>
                                </div>
                            </div>
                        </div>
                        <div class="space-y-2">
                            <label class="text-xs font-medium text-neutral-600 dark:text-neutral-400">Hips</label>
                            <div class="measurement-spinner rounded-lg border border-neutral-300 dark:border-neutral-800 bg-white dark:bg-black/60 transition-colors duration-300 ease-in-out">
                                <input
                                    type="number"
                                    name="measurement[hips]"
                                    step="0.1"
                                    placeholder="40.0"
                                    class="w-full bg-transparent px-3 py-2 text-xs text-neutral-700 placeholder:text-neutral-400 dark:text-neutral-100 dark:placeholder:text-neutral-500 focus:outline-none"
                                />
                                <div class="spinner-buttons">
                                    <button type="button" class="spinner-btn" data-input="measurement[hips]" data-action="up">▲</button>
                                    <button type="button" class="spinner-btn" data-input="measurement[hips]" data-action="down">▼</button>
                                </div>
                            </div>
                        </div>
                        <div class="space-y-2">
                            <label class="text-xs font-medium text-neutral-600 dark:text-neutral-400">Height</label>
                            <div class="measurement-spinner rounded-lg border border-neutral-300 dark:border-neutral-800 bg-white dark:bg-black/60 transition-colors duration-300 ease-in-out">
                                <input
                                    type="number"
                                    name="measurement[height]"
                                    step="0.1"
                                    placeholder="70.0"
                                    class="w-full bg-transparent px-3 py-2 text-xs text-neutral-700 placeholder:text-neutral-400 dark:text-neutral-100 dark:placeholder:text-neutral-500 focus:outline-none"
                                />
                                <div class="spinner-buttons">
                                    <button type="button" class="spinner-btn" data-input="measurement[height]" data-action="up">▲</button>
                                    <button type="button" class="spinner-btn" data-input="measurement[height]" data-action="down">▼</button>
                                </div>
                            </div>
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
            <div class="flex items-center justify-end gap-3 pt-2">
                <button
                    type="submit"
                    id="addCustomerSubmitBtn"
                    class="inline-flex items-center gap-2 rounded-xl px-4 py-2 text-[14px] font-medium bg-violet-600 text-white dark:hover:text-white hover:text-black dark:text-black hover:bg-violet-500 shadow-violet-600/40 transition-colors duration-100 ease-in-out disabled:opacity-50 disabled:cursor-not-allowed"
                >
                    <span id="addCustomerBtnText">Add Customer</span>
                    <span id="addCustomerBtnLoading" class="hidden">Adding...</span>
                </button>
                <button
                    type="button"
                    onclick="closeAddCustomerModal()"
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
    #addCustomerModal {
        will-change: opacity;
        transform: translateZ(0);
        backface-visibility: hidden;
    }
    
    #addCustomerModal .max-w-6xl {
        will-change: transform;
        transform: translateZ(0);
    }
    
    /* Fix select dropdown appearance */
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
    
    /* Custom number input spinner styling */
    input[type="number"] {
        -webkit-appearance: textfield;
        -moz-appearance: textfield;
        appearance: textfield;
    }
    
    input[type="number"]::-webkit-outer-spin-button,
    input[type="number"]::-webkit-inner-spin-button {
        -webkit-appearance: none;
        appearance: none;
        margin: 0;
    }
    
    /* Custom increment buttons */
    .measurement-spinner {
        position: relative;
        display: inline-flex;
        align-items: center;
        width: 100%;
    }
    
    .measurement-spinner input {
        flex: 1;
    }
    
    .spinner-buttons {
        position: absolute;
        right: 0.25rem;
        display: inline-flex;
        flex-direction: column;
        gap: 0;
        height: 100%;
    }
    
    .spinner-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 1.5rem;
        padding: 0;
        cursor: pointer;
        border: none;
        background: transparent;
        color: #6b7280;
        font-size: 0.6rem;
        line-height: 1;
        transition: color 0.2s;
        flex: 1;
    }
    
    .dark .spinner-btn {
        color: #9ca3af;
    }
    
    .spinner-btn:hover {
        color: #374151;
    }
    
    .dark .spinner-btn:hover {
        color: #d1d5db;
    }
</style>

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

    // Handle measurement spinner buttons
    document.querySelectorAll('.spinner-btn').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const inputName = this.getAttribute('data-input');
            const action = this.getAttribute('data-action');
            const input = document.querySelector(`input[name="${inputName}"]`);
            
            if (input) {
                const currentValue = parseFloat(input.value) || 0;
                const step = parseFloat(input.step) || 1;
                
                if (action === 'up') {
                    input.value = (currentValue + step).toFixed(1);
                } else if (action === 'down') {
                    input.value = Math.max(0, currentValue - step).toFixed(1);
                }
                
                // Trigger input event for form tracking
                input.dispatchEvent(new Event('input', { bubbles: true }));
            }
        });
    });
</script>
