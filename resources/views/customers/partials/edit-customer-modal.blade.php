{{-- Edit Customer Modal --}}
<div id="editCustomerModal" class="hidden fixed inset-0 z-50 flex items-center justify-center px-2 py-6 bg-black/60 backdrop-blur-sm">
    <div class="w-full max-w-6xl bg-white dark:bg-neutral-950 border border-neutral-200 dark:border-neutral-800 rounded-3xl shadow-2xl">
        {{-- Header --}}
        <div class="flex items-center justify-between px-6 py-4 border-b border-neutral-200 dark:border-neutral-800 bg-neutral-50/80 dark:bg-neutral-900/50">
            <div>
                <p class="text-xs uppercase tracking-[0.2em] text-neutral-500 dark:text-neutral-500">Edit Customer</p>
                <h3 class="text-lg font-semibold text-neutral-900 dark:text-white">Update Customer Details</h3>
            </div>
            <button onclick="closeEditCustomerModal()" class="text-neutral-500 hover:text-neutral-800 dark:hover:text-neutral-200 text-xl transition-colors duration-200">×</button>
        </div>

        {{-- Form --}}
        <form id="editCustomerForm" class="px-8 py-6 space-y-5">
            @csrf
            <input type="hidden" id="editCustomerId" name="customer_id" value="">

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
                                    id="editFirstName"
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
                                    id="editLastName"
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
                                    id="editEmail"
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
                                    id="editContactNumber"
                                    name="contact_number"
                                    required
                                    placeholder="09123456789"
                                    class="w-full bg-transparent text-xs text-neutral-700 placeholder:text-neutral-400 dark:text-neutral-100 dark:placeholder:text-neutral-500 focus:outline-none transition-colors duration-300 ease-in-out"
                                />
                            </div>
                        </div>

                        <div class="space-y-2">
                            <label class="text-sm font-medium text-neutral-700 dark:text-neutral-300">Address</label>
                            <div class="flex items-center rounded-2xl bg-white px-3 py-2.5 border border-neutral-300 focus-within:border-neutral-500 dark:border-neutral-800 dark:bg-black/60 transition-colors duration-300 ease-in-out">
                                <x-icon name="map-pin" class="h-4 w-4 text-neutral-500 mr-2 transition-colors duration-300 ease-in-out" />
                                <input
                                    type="text"
                                    id="editAddress"
                                    name="address"
                                    required
                                    placeholder="123 Main St, City"
                                    class="w-full bg-transparent text-xs text-neutral-700 placeholder:text-neutral-400 dark:text-neutral-100 dark:placeholder:text-neutral-500 focus:outline-none transition-colors duration-300 ease-in-out"
                                />
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Measurements Column --}}
                <div class="space-y-4">
                    <div class="flex items-center gap-2 text-sm font-medium text-neutral-700 dark:text-neutral-300">
                        <x-icon name="ruler" class="h-4 w-4" />
                        <span>Measurements (Optional)</span>
                    </div>

                    <div class="bg-neutral-50 dark:bg-neutral-900/50 border border-neutral-200 dark:border-neutral-800 rounded-xl p-3">
                        <p class="text-xs text-neutral-600 dark:text-neutral-400 mb-3">
                            Add customer measurements for better fitting recommendations
                        </p>
                        <div class="grid grid-cols-2 gap-2">
                            <div class="space-y-2">
                                <label class="text-xs font-medium text-neutral-600 dark:text-neutral-400">Chest</label>
                                <div class="measurement-spinner rounded-lg border border-neutral-300 dark:border-neutral-800 bg-white dark:bg-black/60 transition-colors duration-300 ease-in-out">
                                    <input
                                        type="number"
                                        id="editMeasurementChest"
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
                                        id="editMeasurementWaist"
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
                                        id="editMeasurementHips"
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
                                        id="editMeasurementHeight"
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
            </div>

            {{-- Error Message --}}
            <div id="editCustomerError" class="hidden bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl p-3 flex items-center gap-2">
                <x-icon name="alert-circle" class="h-4 w-4 text-red-500" />
                <p class="text-xs text-red-600 dark:text-red-400"></p>
            </div>

            {{-- Success Message --}}
            <div id="editCustomerSuccess" class="hidden bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800 rounded-xl p-3 flex items-center gap-2">
                <x-icon name="check-circle" class="h-4 w-4 text-emerald-500" />
                <p class="text-xs text-emerald-600 dark:text-emerald-400"></p>
            </div>

            {{-- Action Buttons --}}
            <div class="flex items-center justify-between pt-2">
                <button
                    type="button"
                    id="changeStatusBtn"
                    class="inline-flex items-center gap-2 rounded-xl px-4 py-2 text-[14px] font-medium border border-orange-300 bg-orange-50 text-orange-700 dark:border-orange-800 dark:bg-orange-900/20 dark:text-orange-300 hover:bg-orange-100 dark:hover:bg-orange-900/40 transition-colors duration-100 ease-in-out"
                >
                    <span id="changeStatusBtnText">Change Status</span>
                </button>
                <div class="flex items-center gap-3">
                    <button
                        type="submit"
                        id="editCustomerSubmitBtn"
                        class="inline-flex items-center gap-2 rounded-xl px-4 py-2 text-[14px] font-medium bg-violet-600 text-white dark:hover:text-white hover:text-black dark:text-black hover:bg-violet-500 shadow-violet-600/40 transition-colors duration-100 ease-in-out disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        <span id="editCustomerBtnText">Save Changes</span>
                        <span id="editCustomerBtnLoading" class="hidden">Saving...</span>
                    </button>
                    <button
                        type="button"
                        onclick="closeEditCustomerModal()"
                        class="inline-flex items-center gap-2 rounded-xl px-3.5 py-2 text-[14px] font-medium border dark:hover:text-black hover:text-white border-neutral-300 bg-white text-neutral-700 dark:hover:bg-violet-600 hover:bg-violet-600 dark:border-neutral-800 dark:bg-neutral-950/80 dark:text-neutral-200 dark:hover:bg-neutral-900 transition-colors duration-100 ease-in-out"
                    >
                        Cancel
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<style>
    /* Optimize modal performance */
    #editCustomerModal {
        will-change: opacity;
        transform: translateZ(0);
        backface-visibility: hidden;
    }

    #editCustomerModal .max-w-6xl {
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
    let editCustomerModalState = {
        isOpen: false,
        isSubmitting: false,
        currentCustomerId: null,
        currentCustomerStatus: null
    };

    // Open modal and load customer data
    async function openEditCustomerModal(customerId) {
        editCustomerModalState.isOpen = true;
        editCustomerModalState.currentCustomerId = customerId;
        const modal = document.getElementById('editCustomerModal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');

        hideMessages();

        try {
            // Fetch customer data
            const response = await axios.get(`/api/customers/${customerId}`);
            const customer = response.data.data;

            editCustomerModalState.currentCustomerStatus = customer.status_id;

            // Populate form
            document.getElementById('editCustomerId').value = customer.customer_id;
            document.getElementById('editFirstName').value = customer.first_name;
            document.getElementById('editLastName').value = customer.last_name;
            document.getElementById('editEmail').value = customer.email;
            document.getElementById('editContactNumber').value = customer.contact_number;
            document.getElementById('editAddress').value = customer.address;

            // Populate measurements if they exist
            if (customer.measurement) {
                document.getElementById('editMeasurementChest').value = customer.measurement.chest || '';
                document.getElementById('editMeasurementWaist').value = customer.measurement.waist || '';
                document.getElementById('editMeasurementHips').value = customer.measurement.hips || '';
                document.getElementById('editMeasurementHeight').value = customer.measurement.height || '';
            }

            // Focus first input
            setTimeout(() => {
                document.getElementById('editFirstName').focus();
            }, 100);
        } catch (error) {
            const errorMsg = error.response?.data?.message || error.message || 'Failed to load customer data';
            showEditError(errorMsg);
        }
    }

    // Close modal
    function closeEditCustomerModal() {
        editCustomerModalState.isOpen = false;
        editCustomerModalState.currentCustomerId = null;
        editCustomerModalState.currentCustomerStatus = null;
        const modal = document.getElementById('editCustomerModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');

        // Reset form and state
        document.getElementById('editCustomerForm').reset();
        hideMessages();
        editCustomerModalState.isSubmitting = false;
        updateEditSubmitButton();
    }

    // Hide messages
    function hideMessages() {
        document.getElementById('editCustomerError').classList.add('hidden');
        document.getElementById('editCustomerSuccess').classList.add('hidden');
    }

    // Show error message
    function showEditError(message) {
        const errorDiv = document.getElementById('editCustomerError');
        errorDiv.querySelector('p').textContent = message;
        errorDiv.classList.remove('hidden');
        document.getElementById('editCustomerSuccess').classList.add('hidden');
    }

    // Show success message
    function showEditSuccess(message) {
        const successDiv = document.getElementById('editCustomerSuccess');
        successDiv.querySelector('p').textContent = message;
        successDiv.classList.remove('hidden');
        document.getElementById('editCustomerError').classList.add('hidden');
    }

    // Update submit button state
    function updateEditSubmitButton() {
        const btn = document.getElementById('editCustomerSubmitBtn');
        const btnText = document.getElementById('editCustomerBtnText');
        const btnLoading = document.getElementById('editCustomerBtnLoading');

        if (editCustomerModalState.isSubmitting) {
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
    function validateEditCustomerForm(formData) {
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

        return errors;
    }

    // Handle form submission
    document.getElementById('editCustomerForm').addEventListener('submit', async function(e) {
        e.preventDefault();

        if (editCustomerModalState.isSubmitting || !editCustomerModalState.currentCustomerId) {
            return;
        }

        hideMessages();

        const formData = new FormData(this);
        const errors = validateEditCustomerForm(formData);

        if (errors.length > 0) {
            showEditError(errors[0]);
            return;
        }

        editCustomerModalState.isSubmitting = true;
        updateEditSubmitButton();

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
                measurement: Object.keys(measurements).length > 0 ? measurements : null
            };

            const response = await axios.put(`/api/customers/${editCustomerModalState.currentCustomerId}`, payload);
            const data = response.data;

            showEditSuccess('Customer updated successfully!');

            // Close modal after success and refresh table
            setTimeout(() => {
                closeEditCustomerModal();
                fetchCustomers();
                fetchStats();
            }, 1500);
        } catch (error) {
            console.error('Error updating customer:', error);
            console.error('Error response:', error.response);
            const errorMessage = error.response?.data?.message || error.message || 'Network error. Please check your connection and try again.';
            showEditError(errorMessage);
        } finally {
            editCustomerModalState.isSubmitting = false;
            updateEditSubmitButton();
        }
    });

     // Handle change status button
     document.getElementById('changeStatusBtn').addEventListener('click', function(e) {
         e.preventDefault();
         
         const newStatus = editCustomerModalState.currentCustomerStatus === 1 ? 2 : 1;
         const statusName = newStatus === 1 ? 'Active' : 'Inactive';

         // Require password confirmation for both deactivate and reactivate
         showPasswordConfirmationModal(statusName, newStatus);
     });

     // Show password confirmation modal
     function showPasswordConfirmationModal(newStatusName, newStatus) {
         const modal = document.createElement('div');
         modal.id = 'passwordConfirmationModal';
         modal.className = 'fixed inset-0 z-[51] flex items-center justify-center px-2 py-6 bg-black/60 backdrop-blur-sm';
        modal.innerHTML = `
            <div class="w-full max-w-md bg-white dark:bg-neutral-950 border border-neutral-200 dark:border-neutral-800 rounded-3xl shadow-2xl">
                <div class="flex items-center justify-between px-6 py-4 border-b border-neutral-200 dark:border-neutral-800 bg-neutral-50/80 dark:bg-neutral-900/50">
                    <div>
                        <p class="text-xs uppercase tracking-[0.2em] text-neutral-500 dark:text-neutral-500">Security Verification</p>
                        <h3 class="text-lg font-semibold text-neutral-900 dark:text-white">Confirm Status Change</h3>
                    </div>
                    <button onclick="closePasswordConfirmationModal()" class="text-neutral-500 hover:text-neutral-800 dark:hover:text-neutral-200 text-xl transition-colors duration-200">×</button>
                </div>

                <div class="px-6 py-4 space-y-4">
                    <p class="text-sm text-neutral-600 dark:text-neutral-400">
                        You're about to change this customer's status to <strong>${newStatusName}</strong>. For security purposes, please enter your password to confirm this action.
                    </p>

                    <div class="space-y-2">
                        <label class="text-sm font-medium text-neutral-700 dark:text-neutral-300">Your Password</label>
                        <div class="flex items-center rounded-2xl bg-white px-3 py-2.5 border border-neutral-300 focus-within:border-neutral-500 dark:border-neutral-800 dark:bg-black/60 transition-colors duration-300 ease-in-out">
                            <svg class="h-4 w-4 text-neutral-500 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                            <input
                                type="password"
                                id="passwordConfirmationInput"
                                placeholder="Enter your password"
                                class="w-full bg-transparent text-xs text-neutral-700 placeholder:text-neutral-400 dark:text-neutral-100 dark:placeholder:text-neutral-500 focus:outline-none transition-colors duration-300 ease-in-out"
                            />
                        </div>
                    </div>

                    <div id="passwordConfirmationError" class="hidden bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl p-3 flex items-center gap-2">
                        <svg class="h-4 w-4 text-red-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4v.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <p class="text-xs text-red-600 dark:text-red-400"></p>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-neutral-200 dark:border-neutral-800">
                    <button
                        type="button"
                        onclick="closePasswordConfirmationModal()"
                        class="inline-flex items-center gap-2 rounded-xl px-3.5 py-2 text-[14px] font-medium border dark:hover:text-black hover:text-white border-neutral-300 bg-white text-neutral-700 dark:hover:bg-neutral-700 hover:bg-neutral-100 dark:border-neutral-800 dark:bg-neutral-950/80 dark:text-neutral-200 transition-colors duration-100 ease-in-out"
                    >
                        Cancel
                    </button>
                    <button
                        type="button"
                        id="confirmPasswordBtn"
                        class="inline-flex items-center gap-2 rounded-xl px-4 py-2 text-[14px] font-medium bg-orange-600 text-white hover:bg-orange-500 transition-colors duration-100 ease-in-out disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        <span id="confirmPasswordBtnText">Confirm Change</span>
                        <span id="confirmPasswordBtnLoading" class="hidden">Verifying...</span>
                    </button>
                </div>
            </div>
         `;

         document.body.appendChild(modal);
         console.log('Password confirmation modal appended to DOM');

         // Handle confirm button
         document.getElementById('confirmPasswordBtn').addEventListener('click', async function() {
             const password = document.getElementById('passwordConfirmationInput').value;
             
             console.log('Password confirmation clicked');
             console.log('Current customer ID:', editCustomerModalState.currentCustomerId);
             console.log('Current customer status:', editCustomerModalState.currentCustomerStatus);
             
             if (!password.trim()) {
                 const errorDiv = document.getElementById('passwordConfirmationError');
                 errorDiv.querySelector('p').textContent = 'Please enter your password';
                 errorDiv.classList.remove('hidden');
                 return;
             }

             this.disabled = true;
             document.getElementById('confirmPasswordBtnText').classList.add('hidden');
             document.getElementById('confirmPasswordBtnLoading').classList.remove('hidden');

              try {
                  // Verify password with backend
                  const response = await axios.post('/api/verify-password', { password });
                  
                   if (response.data.valid) {
                       closePasswordConfirmationModal();
                       await changeCustomerStatus(newStatus);
                  } else {
                      const errorDiv = document.getElementById('passwordConfirmationError');
                      errorDiv.querySelector('p').textContent = 'Invalid password. Please try again.';
                      errorDiv.classList.remove('hidden');
                  }
              } catch (error) {
                  const errorDiv = document.getElementById('passwordConfirmationError');
                  errorDiv.querySelector('p').textContent = error.response?.data?.message || 'Error verifying password. Please try again.';
                  errorDiv.classList.remove('hidden');
              } finally {
                 this.disabled = false;
                 document.getElementById('confirmPasswordBtnText').classList.remove('hidden');
                 document.getElementById('confirmPasswordBtnLoading').classList.add('hidden');
             }
         });

        // Close on escape key
        document.addEventListener('keydown', function closeOnEscape(e) {
            if (e.key === 'Escape') {
                closePasswordConfirmationModal();
                document.removeEventListener('keydown', closeOnEscape);
            }
        });

        // Close on backdrop click
        modal.addEventListener('click', function(e) {
            if (e.target === this) {
                closePasswordConfirmationModal();
            }
        });

        // Focus password input
        setTimeout(() => {
            document.getElementById('passwordConfirmationInput').focus();
        }, 100);
    }

    // Close password confirmation modal
    function closePasswordConfirmationModal() {
        const modal = document.getElementById('passwordConfirmationModal');
        if (modal) {
            modal.remove();
        }
    }

      // Change customer status
      async function changeCustomerStatus(newStatus) {
          try {
              // Handle both cases: called from edit modal or from table button
              const customerId = editCustomerModalState.currentCustomerId || window.pendingStatusChange?.customerId;
              
              if (!customerId) {
                  throw new Error('Customer ID not found');
              }

               const endpoint = newStatus === 2 ? 'deactivate' : 'reactivate';
              const url = `/api/customers/${customerId}/${endpoint}`;
              
              const response = await axios.post(url);

              // Update modal state if called from edit modal
              if (editCustomerModalState.currentCustomerId) {
                  editCustomerModalState.currentCustomerStatus = newStatus;
                  showEditSuccess(`Customer ${newStatus === 1 ? 'activated' : 'deactivated'} successfully!`);

                  // Close modal after success and refresh table
                  setTimeout(() => {
                      closeEditCustomerModal();
                      fetchCustomers();
                      fetchStats();
                  }, 1500);
              } else {
                  // Called from table button - just refresh table without modal
                  window.pendingStatusChange = null;
                  closePasswordConfirmationModal();
                  fetchCustomers();
                  fetchStats();
              }
          } catch (error) {
              const errorMessage = error.response?.data?.message || error.message || 'Failed to change customer status';
              showEditError(errorMessage);
          }
      }

    // Close modal on escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && editCustomerModalState.isOpen) {
            closeEditCustomerModal();
        }
    });

    // Close modal on backdrop click
    document.getElementById('editCustomerModal')?.addEventListener('click', function(e) {
        if (e.target === this && editCustomerModalState.isOpen) {
            closeEditCustomerModal();
        }
    });

    // Handle measurement spinner buttons
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('spinner-btn')) {
            e.preventDefault();
            const inputName = e.target.getAttribute('data-input');
            const action = e.target.getAttribute('data-action');
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
        }
    });
</script>
