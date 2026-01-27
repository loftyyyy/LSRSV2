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
                    </div>
                </div>

                {{-- Status & Address Column --}}
                <div class="space-y-4">
                    <div class="flex items-center gap-2 text-sm font-medium text-neutral-700 dark:text-neutral-300">
                        <x-icon name="map-pin" class="h-4 w-4" />
                        <span>Address & Status</span>
                    </div>

                    <div class="space-y-3">
                        <div class="space-y-2">
                            <label class="text-sm font-medium text-neutral-700 dark:text-neutral-300">Address</label>
                            <div class="flex items-center rounded-2xl bg-white px-3 py-2.5 border border-neutral-300 focus-within:border-neutral-500 dark:border-neutral-800 dark:bg-black/60 transition-colors duration-300 ease-in-out">
                                <x-icon name="map-pin" class="h-4 w-4 text-neutral-500 mr-2 transition-colors duration-300 ease-in-out" />
                                <input
                                    type="text"
                                    id="editAddress"
                                    name="address"
                                    required
                                    placeholder="123 Main St"
                                    class="w-full bg-transparent text-xs text-neutral-700 placeholder:text-neutral-400 dark:text-neutral-100 dark:placeholder:text-neutral-500 focus:outline-none transition-colors duration-300 ease-in-out"
                                />
                            </div>
                        </div>

                        <div class="space-y-2">
                            <label class="text-sm font-medium text-neutral-700 dark:text-neutral-300">Status</label>
                            <div class="flex items-center rounded-2xl bg-white px-3 py-2.5 border border-neutral-300 focus-within:border-neutral-500 dark:border-neutral-800 dark:bg-black/60 transition-colors duration-300 ease-in-out">
                                <select id="editStatus" name="status_id" class="w-full bg-transparent text-xs text-neutral-700 dark:text-neutral-100 focus:outline-none transition-colors duration-300 ease-in-out">
                                    <option value="1">Active</option>
                                    <option value="2">Inactive</option>
                                </select>
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
            <div class="flex items-center justify-end gap-3 pt-2">
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
</style>

<script>
    // Modal state
    let editCustomerModalState = {
        isOpen: false,
        isSubmitting: false,
        currentCustomerId: null
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

            // Populate form
            document.getElementById('editCustomerId').value = customer.customer_id;
            document.getElementById('editFirstName').value = customer.first_name;
            document.getElementById('editLastName').value = customer.last_name;
            document.getElementById('editEmail').value = customer.email;
            document.getElementById('editContactNumber').value = customer.contact_number;
            document.getElementById('editAddress').value = customer.address;
            document.getElementById('editStatus').value = customer.status_id;

            // Focus first input
            setTimeout(() => {
                document.getElementById('editFirstName').focus();
            }, 100);
        } catch (error) {
            console.error('Error loading customer:', error);
            showEditError('Failed to load customer data');
        }
    }

    // Close modal
    function closeEditCustomerModal() {
        editCustomerModalState.isOpen = false;
        editCustomerModalState.currentCustomerId = null;
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
            // Prepare API payload
            const payload = {
                first_name: formData.get('first_name'),
                last_name: formData.get('last_name'),
                email: formData.get('email'),
                contact_number: formData.get('contact_number'),
                address: formData.get('address'),
                status_id: parseInt(formData.get('status_id'))
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
</script>
