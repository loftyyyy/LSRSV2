{{-- Edit Item Modal --}}
<div id="editItemModal" class="hidden fixed inset-0 z-50 flex items-center justify-center px-2 py-4 bg-black/60 backdrop-blur-sm overflow-y-auto">
    <div class="w-full max-w-4xl bg-white dark:bg-neutral-950 border border-neutral-200 dark:border-neutral-800 rounded-3xl shadow-2xl flex flex-col max-h-[calc(100vh-2rem)] my-auto">
        {{-- Header (sticky) --}}
        <div class="flex-shrink-0 flex items-center justify-between px-6 py-4 border-b border-neutral-200 dark:border-neutral-800 bg-neutral-50/80 dark:bg-neutral-900/50 rounded-t-3xl">
            <div>
                <p class="text-xs uppercase tracking-[0.2em] text-neutral-500 dark:text-neutral-500">Edit Item</p>
                <h3 class="text-lg font-semibold text-neutral-900 dark:text-white">Update Inventory Item</h3>
            </div>
            <button onclick="closeEditItemModal()" class="text-neutral-500 hover:text-neutral-800 dark:hover:text-neutral-200 text-xl transition-colors duration-200">&times;</button>
        </div>

        {{-- Form (scrollable) --}}
        <form id="editItemForm" class="flex-1 overflow-y-auto px-8 py-6 space-y-5">
            @csrf
            <input type="hidden" id="editItemId" name="item_id" value="">

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
                                    id="editItemName"
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
                                    id="editItemSku"
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
                                    id="editItemType"
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
                                    id="editItemSize"
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
                                    id="editItemColor"
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
                                    id="editItemDesign"
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
                        <span class="text-neutral-500 mr-2">&#8369;</span>
                        <input
                            type="number"
                            id="editItemRentalPrice"
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

            {{-- Image Edit Section --}}
            <div class="space-y-3">
                <div class="flex items-center gap-2 text-sm font-medium text-neutral-700 dark:text-neutral-300">
                    <x-icon name="image" class="h-4 w-4" />
                    <span>Item Photos</span>
                </div>

                <div class="bg-neutral-50 dark:bg-neutral-900/50 border border-neutral-200 dark:border-neutral-800 rounded-xl p-4">
                    <p class="text-xs text-neutral-600 dark:text-neutral-400 mb-4">
                        Manage photos of the item. You can upload up to 5 photos total.
                    </p>

                    {{-- Existing Images Grid --}}
                    <div id="editExistingImagesContainer" class="mb-4">
                        <p class="text-xs font-medium text-neutral-600 dark:text-neutral-400 mb-2">Current Photos</p>
                        <div id="editExistingImagesGrid" class="grid grid-cols-5 gap-3">
                            <!-- Existing images will be inserted here -->
                        </div>
                        <div id="editNoExistingImagesMessage" class="hidden text-center text-xs text-neutral-500 dark:text-neutral-400 py-4">
                            No photos uploaded yet
                        </div>
                    </div>

                    {{-- File Input (Hidden) --}}
                    <input
                        type="file"
                        id="editImageInput"
                        name="images[]"
                        multiple
                        accept="image/*"
                        class="hidden"
                    />

                    {{-- Upload Button --}}
                    <button
                        type="button"
                        id="editUploadBtn"
                        onclick="document.getElementById('editImageInput').click()"
                        class="w-full flex items-center justify-center gap-2 rounded-xl px-4 py-3 border-2 border-dashed border-neutral-300 dark:border-neutral-700 bg-white dark:bg-black/60 text-neutral-600 dark:text-neutral-400 hover:border-violet-600 hover:text-violet-600 dark:hover:border-violet-500 dark:hover:text-violet-400 transition-colors duration-300"
                    >
                        <x-icon name="cloud-upload" class="h-4 w-4" />
                        <span class="text-xs font-medium">Click to upload new photos</span>
                    </button>

                    {{-- New Images Preview Grid --}}
                    <div id="editNewImagesContainer" class="mt-4 hidden">
                        <p class="text-xs font-medium text-neutral-600 dark:text-neutral-400 mb-2">New Photos to Add</p>
                        <div id="editNewImagesGrid" class="grid grid-cols-5 gap-3">
                            <!-- New image previews will be inserted here -->
                        </div>
                    </div>
                </div>
            </div>

            {{-- Error Message --}}
            <div id="editItemError" class="hidden bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl p-3 flex items-center gap-2">
                <x-icon name="alert-circle" class="h-4 w-4 text-red-500" />
                <p class="text-xs text-red-600 dark:text-red-400"></p>
            </div>

            {{-- Success Message --}}
            <div id="editItemSuccess" class="hidden bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800 rounded-xl p-3 flex items-center gap-2">
                <x-icon name="check-circle" class="h-4 w-4 text-emerald-500" />
                <p class="text-xs text-emerald-600 dark:text-emerald-400"></p>
            </div>

            {{-- Action Buttons --}}
            <div class="flex items-center justify-between pt-4">
                <button
                    type="button"
                    id="changeItemStatusBtn"
                    class="inline-flex items-center gap-2 rounded-xl px-4 py-2 text-[14px] font-medium border border-orange-300 bg-orange-50 text-orange-700 dark:border-orange-800 dark:bg-orange-900/20 dark:text-orange-300 hover:bg-orange-100 dark:hover:bg-orange-900/40 transition-colors duration-100 ease-in-out"
                >
                    <span id="changeItemStatusBtnText">Change Status</span>
                </button>
                <div class="flex items-center gap-3">
                    <button
                        type="submit"
                        id="editItemSubmitBtn"
                        class="inline-flex items-center gap-2 rounded-xl px-4 py-2 text-[14px] font-medium bg-violet-600 text-white dark:hover:text-white hover:text-black dark:text-black hover:bg-violet-500 shadow-violet-600/40 transition-colors duration-100 ease-in-out disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        <span id="editItemBtnText">Save Changes</span>
                        <span id="editItemBtnLoading" class="hidden">Saving...</span>
                    </button>
                    <button
                        type="button"
                        onclick="closeEditItemModal()"
                        class="inline-flex items-center gap-2 rounded-xl px-3.5 py-2 text-[14px] font-medium border dark:hover:text-black hover:text-white border-neutral-300 bg-white text-neutral-700 dark:hover:bg-violet-600 hover:bg-violet-600 dark:border-neutral-800 dark:bg-neutral-950/80 dark:text-neutral-200 dark:hover:bg-neutral-900 transition-colors duration-100 ease-in-out"
                    >
                        Cancel
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Change Status Modal --}}
<div id="changeItemStatusModal" class="hidden fixed inset-0 z-[51] flex items-center justify-center px-2 py-6 bg-black/60 backdrop-blur-sm">
    <div class="w-full max-w-md bg-white dark:bg-neutral-950 border border-neutral-200 dark:border-neutral-800 rounded-3xl shadow-2xl">
        <div class="flex items-center justify-between px-6 py-4 border-b border-neutral-200 dark:border-neutral-800 bg-neutral-50/80 dark:bg-neutral-900/50 rounded-t-3xl">
            <div>
                <p class="text-xs uppercase tracking-[0.2em] text-neutral-500 dark:text-neutral-500">Update Status</p>
                <h3 class="text-lg font-semibold text-neutral-900 dark:text-white">Set Item Status</h3>
            </div>
            <button onclick="closeChangeItemStatusModal()" class="text-neutral-500 hover:text-neutral-800 dark:hover:text-neutral-200 text-xl transition-colors duration-200">&times;</button>
        </div>

        <div class="px-6 py-4 space-y-4">
            <p class="text-sm text-neutral-600 dark:text-neutral-400">
                Mark this item for maintenance or retire it from your inventory.
            </p>

            <div class="space-y-2">
                <label class="text-sm font-medium text-neutral-700 dark:text-neutral-300">New Status</label>
                <div class="flex items-center rounded-2xl bg-white px-3 py-2.5 border border-neutral-300 focus-within:border-neutral-500 dark:border-neutral-800 dark:bg-black/60 transition-colors duration-300 ease-in-out">
                    <x-icon name="archive" class="h-4 w-4 text-neutral-500 mr-2 transition-colors duration-300 ease-in-out" />
                    <select
                        id="newItemStatusSelect"
                        class="w-full bg-transparent text-xs text-neutral-700 placeholder:text-neutral-400 dark:text-neutral-100 dark:placeholder:text-neutral-500 focus:outline-none transition-colors duration-300 ease-in-out"
                    >
                        <option value="">Select status</option>
                        <option value="3">Maintenance</option>
                        <option value="4">Retired</option>
                    </select>
                </div>
                <p class="text-xs text-neutral-500 dark:text-neutral-400 mt-1">
                    Note: "Available" and "Rented" statuses are automatically managed by the rental system.
                </p>
            </div>

            <div id="changeItemStatusError" class="hidden bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl p-3 flex items-center gap-2">
                <x-icon name="alert-circle" class="h-4 w-4 text-red-500" />
                <p class="text-xs text-red-600 dark:text-red-400"></p>
            </div>
        </div>

        <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-neutral-200 dark:border-neutral-800">
            <button
                type="button"
                onclick="closeChangeItemStatusModal()"
                class="inline-flex items-center gap-2 rounded-xl px-3.5 py-2 text-[14px] font-medium border dark:hover:text-black hover:text-white border-neutral-300 bg-white text-neutral-700 dark:hover:bg-neutral-700 hover:bg-neutral-100 dark:border-neutral-800 dark:bg-neutral-950/80 dark:text-neutral-200 transition-colors duration-100 ease-in-out"
            >
                Cancel
            </button>
            <button
                type="button"
                id="confirmItemStatusBtn"
                class="inline-flex items-center gap-2 rounded-xl px-4 py-2 text-[14px] font-medium bg-orange-600 text-white hover:bg-orange-500 transition-colors duration-100 ease-in-out disabled:opacity-50 disabled:cursor-not-allowed"
            >
                <span id="confirmItemStatusBtnText">Confirm Change</span>
                <span id="confirmItemStatusBtnLoading" class="hidden">Updating...</span>
            </button>
        </div>
    </div>
</div>

<style>
    /* Optimize modal performance */
    #editItemModal {
        will-change: opacity;
        transform: translateZ(0);
        backface-visibility: hidden;
    }

    #editItemModal .max-w-4xl {
        will-change: transform;
        transform: translateZ(0);
    }

    #changeItemStatusModal {
        will-change: opacity;
        transform: translateZ(0);
        backface-visibility: hidden;
    }

    /* Custom scrollbar for edit item form */
    #editItemForm {
        scrollbar-width: thin;
        scrollbar-color: rgba(155, 155, 155, 0.5) transparent;
    }

    #editItemForm::-webkit-scrollbar {
        width: 6px;
    }

    #editItemForm::-webkit-scrollbar-track {
        background: transparent;
    }

    #editItemForm::-webkit-scrollbar-thumb {
        background-color: rgba(155, 155, 155, 0.5);
        border-radius: 3px;
    }

    #editItemForm::-webkit-scrollbar-thumb:hover {
        background-color: rgba(155, 155, 155, 0.7);
    }

    .dark #editItemForm::-webkit-scrollbar-thumb {
        background-color: rgba(100, 100, 100, 0.5);
    }

    .dark #editItemForm::-webkit-scrollbar-thumb:hover {
        background-color: rgba(100, 100, 100, 0.7);
    }

    /* Fix select dropdown appearance */
    #editItemModal select,
    #changeItemStatusModal select {
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

    .dark #editItemModal select,
    .dark #changeItemStatusModal select {
        color: #f5f5f5;
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%239ca3af' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e");
    }

    /* Style select option text for better visibility */
    #editItemModal select option,
    #changeItemStatusModal select option {
        color: #374151;
        background-color: white;
    }

    .dark #editItemModal select option,
    .dark #changeItemStatusModal select option {
        color: #f5f5f5;
        background-color: rgb(17, 24, 39);
    }
</style>

<script>
    // Use globalThis to avoid redeclaration errors when Turbo navigates between pages
    if (!globalThis.editItemModalState) {
        globalThis.editItemModalState = {
            isOpen: false,
            isSubmitting: false,
            currentItemId: null,
            currentItemStatus: null,
            existingImages: [],      // Images already saved on server
            newImages: [],           // New images to upload
            imagesToDelete: []       // IDs of images to delete
        };
    }

    // Short reference for easier access
    var editItemModalState = globalThis.editItemModalState;

    // Open modal and load item data
    async function openEditItemModal(itemId) {
        globalThis.editItemModalState.isOpen = true;
        globalThis.editItemModalState.currentItemId = itemId;
        globalThis.editItemModalState.existingImages = [];
        globalThis.editItemModalState.newImages = [];
        globalThis.editItemModalState.imagesToDelete = [];
        
        var modal = document.getElementById('editItemModal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');

        hideEditItemMessages();
        clearEditImagePreviews();

        try {
            // Fetch item data
            var response = await axios.get(`/api/inventories/${itemId}`);
            var item = response.data.data;

            globalThis.editItemModalState.currentItemStatus = item.status?.status_name || 'available';

            // Populate form
            document.getElementById('editItemId').value = item.item_id;
            document.getElementById('editItemName').value = item.name || '';
            document.getElementById('editItemSku').value = item.sku || '';
            document.getElementById('editItemType').value = item.item_type || '';
            document.getElementById('editItemSize').value = item.size || '';
            document.getElementById('editItemColor').value = item.color || '';
            document.getElementById('editItemDesign').value = item.design || '';
            document.getElementById('editItemRentalPrice').value = item.rental_price || '';

            // Load existing images
            if (item.images && Array.isArray(item.images)) {
                globalThis.editItemModalState.existingImages = item.images.map(img => ({
                    id: img.image_id,
                    url: img.image_url,
                    view_type: img.view_type || 'front',
                    caption: img.caption || '',
                    is_primary: img.is_primary || false
                }));
                renderEditExistingImages();
            }

            updateEditUploadButtonState();

            // Focus first input
            setTimeout(() => {
                document.getElementById('editItemName').focus();
            }, 100);
        } catch (error) {
            var errorMsg = error.response?.data?.message || error.message || 'Failed to load item data';
            showEditItemError(errorMsg);
        }
    }

    // Close modal
    function closeEditItemModal() {
        globalThis.editItemModalState.isOpen = false;
        globalThis.editItemModalState.currentItemId = null;
        globalThis.editItemModalState.currentItemStatus = null;
        globalThis.editItemModalState.existingImages = [];
        globalThis.editItemModalState.newImages = [];
        globalThis.editItemModalState.imagesToDelete = [];
        
        var modal = document.getElementById('editItemModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');

        // Reset form and state
        document.getElementById('editItemForm').reset();
        clearEditImagePreviews();
        hideEditItemMessages();
        globalThis.editItemModalState.isSubmitting = false;
        updateEditItemSubmitButton();
    }

    // Clear all image previews
    function clearEditImagePreviews() {
        document.getElementById('editExistingImagesGrid').innerHTML = '';
        document.getElementById('editNewImagesGrid').innerHTML = '';
        document.getElementById('editNewImagesContainer').classList.add('hidden');
        document.getElementById('editNoExistingImagesMessage').classList.add('hidden');
    }

    // Render existing images
    function renderEditExistingImages() {
        var container = document.getElementById('editExistingImagesGrid');
        var noImagesMsg = document.getElementById('editNoExistingImagesMessage');
        var visibleImages = globalThis.editItemModalState.existingImages.filter(
            img => !globalThis.editItemModalState.imagesToDelete.includes(img.id)
        );

        if (visibleImages.length === 0) {
            container.innerHTML = '';
            noImagesMsg.classList.remove('hidden');
            return;
        }

        noImagesMsg.classList.add('hidden');
        container.innerHTML = visibleImages.map((img, index) => `
            <div class="relative group bg-white dark:bg-neutral-900 border border-neutral-200 dark:border-neutral-800 rounded-lg overflow-hidden">
                <div class="aspect-square bg-neutral-100 dark:bg-neutral-800 flex items-center justify-center overflow-hidden">
                    <img src="${img.url}" alt="Item photo ${index + 1}" class="w-full h-full object-cover">
                </div>
                <div class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 transition-opacity duration-200 flex items-center justify-center">
                    <button type="button" onclick="markImageForDeletion(${img.id})" class="p-2 bg-red-600 text-white rounded-full hover:bg-red-700 transition-colors">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </button>
                </div>
                ${img.is_primary ? '<span class="absolute top-1 left-1 bg-violet-600 text-white text-[9px] px-1.5 py-0.5 rounded-full">Primary</span>' : ''}
            </div>
        `).join('');

        updateEditUploadButtonState();
    }

    // Render new images to upload
    function renderEditNewImages() {
        var container = document.getElementById('editNewImagesGrid');
        var wrapper = document.getElementById('editNewImagesContainer');

        if (globalThis.editItemModalState.newImages.length === 0) {
            container.innerHTML = '';
            wrapper.classList.add('hidden');
            return;
        }

        // Check if any new image is already marked as primary
        var hasPrimarySelected = globalThis.editItemModalState.newImages.some(img => img.is_primary);

        wrapper.classList.remove('hidden');
        container.innerHTML = globalThis.editItemModalState.newImages.map((img, index) => {
            // Disable checkbox if another image is primary (but not this one)
            var isDisabled = hasPrimarySelected && !img.is_primary;
            var disabledClass = isDisabled ? 'opacity-50 cursor-not-allowed' : 'cursor-pointer';
            var disabledAttr = isDisabled ? 'disabled' : '';
            
            return `
            <div class="bg-white dark:bg-neutral-900 border border-neutral-200 dark:border-neutral-800 rounded-lg overflow-hidden hover:border-violet-400 dark:hover:border-violet-500 transition-colors duration-200">
                <div class="aspect-square bg-neutral-100 dark:bg-neutral-800 flex items-center justify-center overflow-hidden">
                    <img src="${img.preview}" alt="New photo ${index + 1}" class="w-full h-full object-cover">
                </div>
                <div class="p-2 space-y-2">
                    <div class="space-y-1">
                        <label class="text-xs font-medium text-neutral-600 dark:text-neutral-400">View Type</label>
                        <select onchange="updateEditNewImageMetadata(${index}, 'view_type', this.value)" class="w-full text-xs rounded border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-black/60 text-neutral-700 dark:text-neutral-100 px-2 py-1 focus:outline-none focus:border-violet-500">
                            <option value="front" ${img.view_type === 'front' ? 'selected' : ''}>Front</option>
                            <option value="back" ${img.view_type === 'back' ? 'selected' : ''}>Back</option>
                            <option value="side" ${img.view_type === 'side' ? 'selected' : ''}>Side</option>
                            <option value="detail" ${img.view_type === 'detail' ? 'selected' : ''}>Detail</option>
                            <option value="full" ${img.view_type === 'full' ? 'selected' : ''}>Full</option>
                        </select>
                    </div>
                    <div class="space-y-1">
                        <label class="text-xs font-medium text-neutral-600 dark:text-neutral-400">Caption</label>
                        <input type="text" placeholder="Optional caption" value="${img.caption || ''}" onchange="updateEditNewImageMetadata(${index}, 'caption', this.value)" class="w-full text-xs rounded border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-black/60 text-neutral-700 dark:text-neutral-100 px-2 py-1 placeholder:text-neutral-400 dark:placeholder:text-neutral-500 focus:outline-none focus:border-violet-500">
                    </div>
                    <label class="flex items-center gap-2 ${disabledClass}">
                        <input type="checkbox" onchange="updateEditNewImageMetadata(${index}, 'is_primary', this.checked)" ${img.is_primary ? 'checked' : ''} ${disabledAttr} class="rounded border-neutral-300 dark:border-neutral-600 ${isDisabled ? 'opacity-50' : ''}">
                        <span class="text-xs text-neutral-600 dark:text-neutral-400 ${isDisabled ? 'opacity-50' : ''}">Primary</span>
                    </label>
                    <button type="button" onclick="removeEditNewImage(${index})" class="w-full text-xs text-red-600 dark:text-red-400 hover:text-red-700 dark:hover:text-red-300 py-1 transition-colors duration-200">
                        Remove
                    </button>
                </div>
            </div>
        `}).join('');

        updateEditUploadButtonState();
    }

    // Mark existing image for deletion
    function markImageForDeletion(imageId) {
        if (!globalThis.editItemModalState.imagesToDelete.includes(imageId)) {
            globalThis.editItemModalState.imagesToDelete.push(imageId);
        }
        renderEditExistingImages();
    }

    // Update new image metadata
    function updateEditNewImageMetadata(index, field, value) {
        if (globalThis.editItemModalState.newImages[index]) {
            globalThis.editItemModalState.newImages[index][field] = value;
            
            // Re-render when primary changes to update disabled state of other checkboxes
            if (field === 'is_primary') {
                renderEditNewImages();
            }
        }
    }

    // Remove new image from upload list
    function removeEditNewImage(index) {
        globalThis.editItemModalState.newImages.splice(index, 1);
        renderEditNewImages();
    }

    // Get total image count (existing - deleted + new)
    function getEditTotalImageCount() {
        var existingCount = globalThis.editItemModalState.existingImages.length - globalThis.editItemModalState.imagesToDelete.length;
        var newCount = globalThis.editItemModalState.newImages.length;
        return existingCount + newCount;
    }

    // Update upload button state based on image count
    function updateEditUploadButtonState() {
        var btn = document.getElementById('editUploadBtn');
        var totalImages = getEditTotalImageCount();
        var maxImages = 5;

        if (totalImages >= maxImages) {
            btn.disabled = true;
            btn.classList.add('opacity-50', 'cursor-not-allowed');
            btn.classList.remove('hover:border-violet-600', 'hover:text-violet-600', 'dark:hover:border-violet-500', 'dark:hover:text-violet-400');
        } else {
            btn.disabled = false;
            btn.classList.remove('opacity-50', 'cursor-not-allowed');
            btn.classList.add('hover:border-violet-600', 'hover:text-violet-600', 'dark:hover:border-violet-500', 'dark:hover:text-violet-400');
        }
    }

    // Validate image file
    function validateEditImageFile(file) {
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
    document.getElementById('editImageInput').addEventListener('change', function(e) {
        var files = Array.from(e.target.files);
        var maxImages = 5;
        var currentCount = getEditTotalImageCount();

        if (currentCount + files.length > maxImages) {
            showEditItemError(`You can upload a maximum of ${maxImages} images. You currently have ${currentCount}.`);
            this.value = '';
            return;
        }

        files.forEach((file) => {
            var error = validateEditImageFile(file);
            if (error) {
                showEditItemError(error);
                return;
            }

            var reader = new FileReader();
            reader.onload = function(e) {
                globalThis.editItemModalState.newImages.push({
                    file: file,
                    preview: e.target.result,
                    view_type: 'front',
                    caption: '',
                    is_primary: false
                });
                renderEditNewImages();
            };
            reader.readAsDataURL(file);
        });

        // Reset file input
        this.value = '';
    });

    // Hide messages
    function hideEditItemMessages() {
        document.getElementById('editItemError').classList.add('hidden');
        document.getElementById('editItemSuccess').classList.add('hidden');
    }

    // Show error message
    function showEditItemError(message) {
        var errorDiv = document.getElementById('editItemError');
        errorDiv.querySelector('p').textContent = message;
        errorDiv.classList.remove('hidden');
        document.getElementById('editItemSuccess').classList.add('hidden');
    }

    // Show success message
    function showEditItemSuccess(message) {
        var successDiv = document.getElementById('editItemSuccess');
        successDiv.querySelector('p').textContent = message;
        successDiv.classList.remove('hidden');
        document.getElementById('editItemError').classList.add('hidden');
    }

    // Update submit button state
    function updateEditItemSubmitButton() {
        var btn = document.getElementById('editItemSubmitBtn');
        var btnText = document.getElementById('editItemBtnText');
        var btnLoading = document.getElementById('editItemBtnLoading');

        if (globalThis.editItemModalState.isSubmitting) {
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
    function validateEditItemForm(formData) {
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
    document.getElementById('editItemForm').addEventListener('submit', async function(e) {
        e.preventDefault();

        if (globalThis.editItemModalState.isSubmitting || !globalThis.editItemModalState.currentItemId) {
            return;
        }

        hideEditItemMessages();

        var formData = new FormData(this);
        var errors = validateEditItemForm(formData);

        if (errors.length > 0) {
            showEditItemError(errors[0]);
            return;
        }

        globalThis.editItemModalState.isSubmitting = true;
        updateEditItemSubmitButton();

        try {
            // Step 1: Update item basic info
            var payload = {
                name: formData.get('name'),
                sku: formData.get('sku'),
                item_type: formData.get('item_type'),
                size: formData.get('size'),
                color: formData.get('color'),
                design: formData.get('design'),
                rental_price: parseFloat(formData.get('rental_price'))
            };

            await axios.put(`/api/inventories/${globalThis.editItemModalState.currentItemId}`, payload);

            // Step 2: Delete images marked for deletion
            var deletePromises = globalThis.editItemModalState.imagesToDelete.map(imageId => 
                axios.delete(`/api/inventories/${globalThis.editItemModalState.currentItemId}/images/${imageId}`)
            );
            
            if (deletePromises.length > 0) {
                await Promise.all(deletePromises);
            }

            // Step 3: Upload new images
            if (globalThis.editItemModalState.newImages.length > 0) {
                var imagePayload = new FormData();
                
                // Find if any new image is marked as primary
                var primaryNewImageIndex = globalThis.editItemModalState.newImages.findIndex(img => img.is_primary);
                
                globalThis.editItemModalState.newImages.forEach((img, index) => {
                    imagePayload.append('images[]', img.file);
                    imagePayload.append('view_types[]', img.view_type);
                    imagePayload.append('captions[]', img.caption || '');
                    imagePayload.append('is_primary[]', img.is_primary ? '1' : '0');
                });

                await axios.post(`/api/inventories/${globalThis.editItemModalState.currentItemId}/images`, imagePayload, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                });
                
                // If a new image was marked as primary, we need to call setPrimary endpoint
                // The store endpoint will return the uploaded images, but we need to handle this
            }

            showEditItemSuccess('Item updated successfully!');

            // Close modal after success and refresh table
            setTimeout(() => {
                closeEditItemModal();
                fetchInventoryItems();
                fetchStats();
            }, 1500);
        } catch (error) {
            console.error('Error updating item:', error);
            console.error('Error response:', error.response);
            var errorMessage = error.response?.data?.message || error.message || 'Network error. Please check your connection and try again.';
            showEditItemError(errorMessage);
        } finally {
            globalThis.editItemModalState.isSubmitting = false;
            updateEditItemSubmitButton();
        }
    });

    // Handle change status button in edit modal
    document.getElementById('changeItemStatusBtn').addEventListener('click', function(e) {
        e.preventDefault();
        openChangeItemStatusModalFromEdit();
    });

    // Open change status modal from edit modal
    function openChangeItemStatusModalFromEdit() {
        var modal = document.getElementById('changeItemStatusModal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');

        // Reset to default "Select status" option
        var select = document.getElementById('newItemStatusSelect');
        select.value = '';

        hideChangeItemStatusError();
    }

    // Open change status modal (directly from table action button)
    async function openChangeStatusModal(itemId) {
        try {
            // Fetch item data to get current status
            var response = await axios.get(`/api/inventories/${itemId}`);
            var item = response.data.data;

            // Store item info for later use
            window.pendingItemStatusChange = {
                itemId: itemId,
                currentStatus: item.status?.status_name || 'available',
                itemName: item.name
            };

            var modal = document.getElementById('changeItemStatusModal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');

            // Reset to default "Select status" option
            var select = document.getElementById('newItemStatusSelect');
            select.value = '';

            hideChangeItemStatusError();
        } catch (error) {
            console.error('Error fetching item for status change:', error);
            showErrorNotification('Failed to load item data. Please try again.');
        }
    }

    // Close change status modal
    function closeChangeItemStatusModal() {
        var modal = document.getElementById('changeItemStatusModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        window.pendingItemStatusChange = null;
    }

    // Hide change status error
    function hideChangeItemStatusError() {
        document.getElementById('changeItemStatusError').classList.add('hidden');
    }

    // Show change status error
    function showChangeItemStatusError(message) {
        var errorDiv = document.getElementById('changeItemStatusError');
        errorDiv.querySelector('p').textContent = message;
        errorDiv.classList.remove('hidden');
    }

    // Handle confirm status change button
    document.getElementById('confirmItemStatusBtn').addEventListener('click', async function() {
        var newStatus = document.getElementById('newItemStatusSelect').value;

        if (!newStatus) {
            showChangeItemStatusError('Please select a status');
            return;
        }

        this.disabled = true;
        document.getElementById('confirmItemStatusBtnText').classList.add('hidden');
        document.getElementById('confirmItemStatusBtnLoading').classList.remove('hidden');

        try {
            // Determine which item ID to use
            var itemId = globalThis.editItemModalState.currentItemId || window.pendingItemStatusChange?.itemId;

            if (!itemId) {
                throw new Error('Item ID not found');
            }

            var response = await axios.patch(`/api/inventories/${itemId}/status`, {
                status_id: newStatus
            });

            // Map status ID to name for display
            var statusNames = { '3': 'maintenance', '4': 'retired' };
            var statusName = statusNames[newStatus] || newStatus;

            // Update modal state if called from edit modal
            if (globalThis.editItemModalState.currentItemId) {
                globalThis.editItemModalState.currentItemStatus = statusName;
                showEditItemSuccess(`Item status changed to ${statusName} successfully!`);
            }

            closeChangeItemStatusModal();

            // Refresh table
            setTimeout(() => {
                if (globalThis.editItemModalState.isOpen) {
                    closeEditItemModal();
                }
                fetchInventoryItems();
                fetchStats();
            }, 500);
        } catch (error) {
            var errorMessage = error.response?.data?.message || error.message || 'Failed to change item status';
            showChangeItemStatusError(errorMessage);
        } finally {
            this.disabled = false;
            document.getElementById('confirmItemStatusBtnText').classList.remove('hidden');
            document.getElementById('confirmItemStatusBtnLoading').classList.add('hidden');
        }
    });

    // Close modal on escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            if (document.getElementById('changeItemStatusModal').classList.contains('flex')) {
                closeChangeItemStatusModal();
            } else if (globalThis.editItemModalState.isOpen) {
                closeEditItemModal();
            }
        }
    });

    // Close modal on backdrop click
    document.getElementById('editItemModal')?.addEventListener('click', function(e) {
        if (e.target === this && globalThis.editItemModalState.isOpen) {
            closeEditItemModal();
        }
    });

    document.getElementById('changeItemStatusModal')?.addEventListener('click', function(e) {
        if (e.target === this) {
            closeChangeItemStatusModal();
        }
    });
</script>
