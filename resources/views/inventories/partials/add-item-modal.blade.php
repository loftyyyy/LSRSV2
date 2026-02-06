{{-- Add Item Modal --}}
<div id="addItemModal" class="hidden fixed inset-0 z-50 flex items-center justify-center px-2 py-6 bg-black/60 backdrop-blur-sm overflow-y-auto">
    <div class="w-full max-w-4xl bg-white dark:bg-neutral-950 border border-neutral-200 dark:border-neutral-800 rounded-3xl shadow-2xl flex flex-col max-h-[calc(100vh-3rem)] my-auto">
        {{-- Header --}}
        <div class="flex-shrink-0 flex items-center justify-between px-6 py-4 border-b border-neutral-200 dark:border-neutral-800 bg-neutral-50/80 rounded-t-3xl dark:bg-neutral-900/50">
            <div>
                <p class="text-xs uppercase tracking-[0.2em] text-neutral-500 dark:text-neutral-500">New Item</p>
                <h3 class="text-lg font-semibold text-neutral-900 dark:text-white">Add Inventory Item</h3>
            </div>
            <button onclick="closeAddItemModal()" class="text-neutral-500 hover:text-neutral-800 dark:hover:text-neutral-200 text-xl transition-colors duration-200">×</button>
        </div>

        {{-- Form (scrollable) --}}
        <form id="addItemForm" class="flex-1 overflow-y-auto px-8 py-6 space-y-5">
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
                                <x-icon name="tag" class="h-4 w-4 text-neutral-500 mr-2 transition-colors duration-300 ease-in-out dark:text-neutral-400" />
                                <select
                                    name="item_type"
                                    required
                                    class="w-full bg-transparent text-xs text-neutral-700 placeholder:text-neutral-400 dark:text-neutral-100 dark:placeholder:text-neutral-500 focus:outline-none transition-colors duration-300 ease-in-out"
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

            {{-- Pricing Section --}}
            <div class="space-y-3">
                <div class="flex items-center gap-2 text-sm font-medium text-neutral-700 dark:text-neutral-300">
                    <x-icon name="currency-peso" class="h-4 w-4" />
                    <span>Pricing</span>
                </div>

                <div class="grid grid-cols-3 gap-4">
                    {{-- Rental Price --}}
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

                    {{-- Selling Price --}}
                    <div class="space-y-2">
                        <label class="text-sm font-medium text-neutral-700 dark:text-neutral-300">Selling Price (PHP) <span class="text-neutral-400 text-xs">(Optional)</span></label>
                        <div class="flex items-center rounded-2xl bg-white px-3 py-2.5 border border-neutral-300 focus-within:border-neutral-500 dark:border-neutral-800 dark:bg-black/60 transition-colors duration-300 ease-in-out">
                            <span class="text-neutral-500 mr-2">₱</span>
                            <input
                                type="number"
                                name="selling_price"
                                step="0.01"
                                min="0"
                                placeholder="0.00"
                                class="w-full bg-transparent text-xs text-neutral-700 placeholder:text-neutral-400 dark:text-neutral-100 dark:placeholder:text-neutral-500 focus:outline-none transition-colors duration-300 ease-in-out"
                            />
                        </div>
                    </div>

                    {{-- Deposit Amount --}}
                    <div class="space-y-2">
                        <label class="text-sm font-medium text-neutral-700 dark:text-neutral-300">Deposit Amount (PHP)</label>
                        <div class="flex items-center rounded-2xl bg-white px-3 py-2.5 border border-neutral-300 focus-within:border-neutral-500 dark:border-neutral-800 dark:bg-black/60 transition-colors duration-300 ease-in-out">
                            <span class="text-neutral-500 mr-2">₱</span>
                            <input
                                type="number"
                                name="deposit_amount"
                                step="0.01"
                                min="0"
                                placeholder="0.00"
                                value="0"
                                class="w-full bg-transparent text-xs text-neutral-700 placeholder:text-neutral-400 dark:text-neutral-100 dark:placeholder:text-neutral-500 focus:outline-none transition-colors duration-300 ease-in-out"
                            />
                        </div>
                    </div>
                </div>
            </div>

            {{-- Image Upload Section --}}
            <div class="space-y-3">
                <div class="flex items-center gap-2 text-sm font-medium text-neutral-700 dark:text-neutral-300">
                    <x-icon name="image" class="h-4 w-4" />
                    <span>Item Photos (Optional)</span>
                </div>

                <div class="bg-neutral-50 dark:bg-neutral-900/50 border border-neutral-200 dark:border-neutral-800 rounded-xl p-4">
                    <p class="text-xs text-neutral-600 dark:text-neutral-400 mb-4">
                        Upload photos of the item from different angles. You can upload up to 5 photos.
                    </p>

                    {{-- File Input (Hidden) --}}
                    <input
                        type="file"
                        id="imageInput"
                        name="images[]"
                        multiple
                        accept="image/*"
                        class="hidden"
                    />

                    {{-- Upload Button --}}
                    <button
                        type="button"
                        onclick="document.getElementById('imageInput').click()"
                        class="w-full flex items-center justify-center gap-2 rounded-xl px-4 py-3 border-2 border-dashed border-neutral-300 dark:border-neutral-700 bg-white dark:bg-black/60 text-neutral-600 dark:text-neutral-400 hover:border-violet-600 hover:text-violet-600 dark:hover:border-violet-500 dark:hover:text-violet-400 transition-colors duration-300"
                    >
                        <x-icon name="cloud-upload" class="h-4 w-4" />
                        <span class="text-xs font-medium">Click to upload or drag and drop</span>
                    </button>

                    {{-- Image Preview Grid --}}
                    <div id="imagePreviewContainer" class="mt-4 grid grid-cols-5 gap-3 hidden">
                        <!-- Image previews will be inserted here -->
                    </div>

                    {{-- No Images Message --}}
                    <div id="noImagesMessage" class="mt-4 text-center text-xs text-neutral-500 dark:text-neutral-400">
                        No photos selected yet
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

    /* Custom scrollbar for form */
    #addItemForm {
        scrollbar-width: thin;
        scrollbar-color: rgba(155, 155, 155, 0.5) transparent;
    }

    #addItemForm::-webkit-scrollbar {
        width: 6px;
    }

    #addItemForm::-webkit-scrollbar-track {
        background: transparent;
    }

    #addItemForm::-webkit-scrollbar-thumb {
        background-color: rgba(155, 155, 155, 0.5);
        border-radius: 3px;
    }

    #addItemForm::-webkit-scrollbar-thumb:hover {
        background-color: rgba(155, 155, 155, 0.7);
    }

    .dark #addItemForm::-webkit-scrollbar-thumb {
        background-color: rgba(100, 100, 100, 0.5);
    }

    .dark #addItemForm::-webkit-scrollbar-thumb:hover {
        background-color: rgba(100, 100, 100, 0.7);
    }

    /* Fix select dropdown appearance */
    select {
        background-color: transparent;
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
            isSubmitting: false,
            selectedImages: [] // Track selected images
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
        clearImagePreviews();
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
        clearImagePreviews();
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

    // Clear image previews
    function clearImagePreviews() {
        globalThis.addItemModalState.selectedImages = [];
        var container = document.getElementById('imagePreviewContainer');
        container.innerHTML = '';
        container.classList.add('hidden');
        document.getElementById('noImagesMessage').classList.remove('hidden');
    }

    // Remove single image preview
    function removeImagePreview(index) {
        globalThis.addItemModalState.selectedImages.splice(index, 1);
        renderImagePreviews();
    }

    // Update image metadata
    function updateImageMetadata(index, field, value) {
        if (globalThis.addItemModalState.selectedImages[index]) {
            globalThis.addItemModalState.selectedImages[index][field] = value;
        }
    }

    // Render image previews
    function renderImagePreviews() {
        var container = document.getElementById('imagePreviewContainer');
        var noImagesMsg = document.getElementById('noImagesMessage');

        if (globalThis.addItemModalState.selectedImages.length === 0) {
            container.classList.add('hidden');
            noImagesMsg.classList.remove('hidden');
            return;
        }

        container.classList.remove('hidden');
        noImagesMsg.classList.add('hidden');

        container.innerHTML = globalThis.addItemModalState.selectedImages.map((img, index) => `
            <div class="bg-white dark:bg-neutral-900 border border-neutral-200 dark:border-neutral-800 rounded-lg overflow-hidden hover:border-violet-400 dark:hover:border-violet-500 transition-colors duration-200">
                <!-- Image Preview -->
                <div class="aspect-square bg-neutral-100 dark:bg-neutral-800 flex items-center justify-center overflow-hidden">
                    <img src="${img.preview}" alt="Preview ${index + 1}" class="w-full h-full object-cover">
                </div>

                <!-- Metadata Section -->
                <div class="p-2 space-y-2">
                    <!-- View Type Dropdown -->
                    <div class="space-y-1">
                        <label class="text-xs font-medium text-neutral-600 dark:text-neutral-400">View Type</label>
                        <select onchange="updateImageMetadata(${index}, 'view_type', this.value)" class="w-full text-xs rounded border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-black/60 text-neutral-700 dark:text-neutral-100 px-2 py-1 focus:outline-none focus:border-violet-500">
                            <option value="front" ${img.view_type === 'front' ? 'selected' : ''}>Front</option>
                            <option value="back" ${img.view_type === 'back' ? 'selected' : ''}>Back</option>
                            <option value="side" ${img.view_type === 'side' ? 'selected' : ''}>Side</option>
                            <option value="detail" ${img.view_type === 'detail' ? 'selected' : ''}>Detail</option>
                            <option value="full" ${img.view_type === 'full' ? 'selected' : ''}>Full</option>
                        </select>
                    </div>

                    <!-- Caption Input -->
                    <div class="space-y-1">
                        <label class="text-xs font-medium text-neutral-600 dark:text-neutral-400">Caption (optional)</label>
                        <input type="text" placeholder="e.g., Embroidered detail" onchange="updateImageMetadata(${index}, 'caption', this.value)" class="w-full text-xs rounded border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-black/60 text-neutral-700 dark:text-neutral-100 px-2 py-1 placeholder:text-neutral-400 dark:placeholder:text-neutral-500 focus:outline-none focus:border-violet-500">
                    </div>

                    <!-- Primary Checkbox -->
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" onchange="updateImageMetadata(${index}, 'is_primary', this.checked)" ${img.is_primary ? 'checked' : ''} class="rounded border-neutral-300 dark:border-neutral-600">
                        <span class="text-xs text-neutral-600 dark:text-neutral-400">Primary image</span>
                    </label>

                    <!-- Remove Button -->
                    <button type="button" onclick="removeImagePreview(${index})" class="w-full text-xs text-red-600 dark:text-red-400 hover:text-red-700 dark:hover:text-red-300 py-1 transition-colors duration-200">
                        Remove
                    </button>
                </div>
            </div>
        `).join('');
    }

    // Validate image file
    function validateImageFile(file) {
        var maxSize = 5 * 1024 * 1024; // 5MB
        var allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];

        if (!allowedTypes.includes(file.type)) {
            return 'Only JPEG, PNG, GIF, and WebP images are allowed';
        }

        if (file.size > maxSize) {
            return 'Image size must be less than 5MB';
        }

        return null;
    }

    // Handle image input change
    document.getElementById('imageInput').addEventListener('change', function(e) {
        var files = Array.from(e.target.files);
        var maxImages = 5;
        var currentCount = globalThis.addItemModalState.selectedImages.length;

        if (currentCount + files.length > maxImages) {
            showError(`You can upload a maximum of ${maxImages} images. You have ${currentCount} selected.`);
            return;
        }

        files.forEach((file) => {
            var error = validateImageFile(file);
            if (error) {
                showError(error);
                return;
            }

            var reader = new FileReader();
            reader.onload = function(e) {
                globalThis.addItemModalState.selectedImages.push({
                    file: file,
                    preview: e.target.result,
                    view_type: 'front',
                    caption: '',
                    is_primary: globalThis.addItemModalState.selectedImages.length === 0 // First image is primary by default
                });
                renderImagePreviews();
            };
            reader.readAsDataURL(file);
        });

        // Reset file input for next selection
        this.value = '';
    });

    // Validate form
    function validateAddItemForm(formData) {
        var errors = [];

        // Required fields validation
        if (!formData.get('name')?.trim()) {
            errors.push('Item name is required');
        }
        // SKU is optional (auto-generated if not provided)
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
            var payload = new FormData();
            payload.append('name', formData.get('name'));
            payload.append('sku', formData.get('sku'));
            payload.append('item_type', formData.get('item_type'));
            payload.append('size', formData.get('size'));
            payload.append('color', formData.get('color'));
            payload.append('design', formData.get('design'));
            payload.append('rental_price', parseFloat(formData.get('rental_price')));

            // Optional pricing fields
            var sellingPrice = formData.get('selling_price');
            if (sellingPrice && sellingPrice.trim() !== '') {
                payload.append('selling_price', parseFloat(sellingPrice));
            }

            var depositAmount = formData.get('deposit_amount');
            if (depositAmount && depositAmount.trim() !== '') {
                payload.append('deposit_amount', parseFloat(depositAmount));
            }

            // Add images and their metadata
            globalThis.addItemModalState.selectedImages.forEach((img, index) => {
                payload.append(`images[${index}][file]`, img.file);
                payload.append(`images[${index}][view_type]`, img.view_type);
                payload.append(`images[${index}][caption]`, img.caption || '');
                payload.append(`images[${index}][is_primary]`, img.is_primary ? 1 : 0);
            });

            var response = await axios.post('/api/inventories', payload, {
                headers: {
                    'Content-Type': 'multipart/form-data'
                }
            });
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
