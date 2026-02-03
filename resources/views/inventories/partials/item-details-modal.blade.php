{{-- Item Details Modal --}}
<div id="itemDetailsModal" class="hidden fixed inset-0 z-50 flex items-center justify-center px-2 py-4 bg-black/60 backdrop-blur-sm overflow-y-auto">
    <div class="w-full max-w-4xl bg-white dark:bg-neutral-950 border border-neutral-200 dark:border-neutral-800 rounded-3xl shadow-2xl flex flex-col max-h-[calc(100vh-2rem)] my-auto">
        {{-- Header --}}
        <div class="flex-shrink-0 flex items-center justify-between px-6 py-4 border-b border-neutral-200 dark:border-neutral-800 bg-neutral-50/80 dark:bg-neutral-900/50 rounded-t-3xl">
            <div>
                <p class="text-xs uppercase tracking-[0.2em] text-neutral-500 dark:text-neutral-500">Item Details</p>
                <h3 id="itemDetailsTitle" class="text-lg font-semibold text-neutral-900 dark:text-white">Loading...</h3>
            </div>
            <button onclick="closeItemDetailsModal()" class="text-neutral-500 hover:text-neutral-800 dark:hover:text-neutral-200 text-xl transition-colors duration-200">&times;</button>
        </div>

        {{-- Content (scrollable) --}}
        <div id="itemDetailsContent" class="flex-1 overflow-y-auto px-6 py-6">
            {{-- Loading State --}}
            <div id="itemDetailsLoading" class="flex items-center justify-center py-12">
                <div class="flex flex-col items-center gap-3">
                    <div class="h-8 w-8 animate-spin rounded-full border-2 border-violet-600 border-t-transparent"></div>
                    <p class="text-sm text-neutral-500 dark:text-neutral-400">Loading item details...</p>
                </div>
            </div>

            {{-- Item Details (hidden initially) --}}
            <div id="itemDetailsData" class="hidden space-y-6">
                {{-- Image Gallery --}}
                <div class="space-y-3">
                    <div class="flex items-center gap-2 text-sm font-medium text-neutral-700 dark:text-neutral-300">
                        <x-icon name="image" class="h-4 w-4" />
                        <span>Photos</span>
                    </div>
                    
                    {{-- Main Image Display --}}
                    <div class="flex gap-4">
                        <div id="itemDetailsMainImage" class="flex-1 aspect-[4/3] bg-neutral-100 dark:bg-neutral-900 rounded-2xl overflow-hidden flex items-center justify-center border border-neutral-200 dark:border-neutral-800">
                            <div class="text-neutral-400 dark:text-neutral-600 flex flex-col items-center gap-2">
                                <x-icon name="image" class="h-12 w-12" />
                                <span class="text-xs">No photos available</span>
                            </div>
                        </div>
                        
                        {{-- Thumbnails --}}
                        <div id="itemDetailsThumbnails" class="flex flex-col gap-2 w-20">
                            {{-- Thumbnails will be inserted here --}}
                        </div>
                    </div>
                </div>

                {{-- Item Information Grid --}}
                <div class="grid grid-cols-2 gap-6">
                    {{-- Left Column --}}
                    <div class="space-y-4">
                        <div class="flex items-center gap-2 text-sm font-medium text-neutral-700 dark:text-neutral-300">
                            <x-icon name="package" class="h-4 w-4" />
                            <span>Item Information</span>
                        </div>

                        <div class="space-y-3">
                            {{-- Item Name --}}
                            <div class="bg-neutral-50 dark:bg-neutral-900/50 rounded-xl p-3 border border-neutral-200 dark:border-neutral-800">
                                <p class="text-xs text-neutral-500 dark:text-neutral-400 mb-1">Item Name</p>
                                <p id="detailItemName" class="text-sm font-medium text-neutral-900 dark:text-white">-</p>
                            </div>

                            {{-- SKU --}}
                            <div class="bg-neutral-50 dark:bg-neutral-900/50 rounded-xl p-3 border border-neutral-200 dark:border-neutral-800">
                                <p class="text-xs text-neutral-500 dark:text-neutral-400 mb-1">SKU</p>
                                <p id="detailItemSku" class="text-sm font-medium text-neutral-900 dark:text-white font-geist-mono">-</p>
                            </div>

                            {{-- Item Type --}}
                            <div class="bg-neutral-50 dark:bg-neutral-900/50 rounded-xl p-3 border border-neutral-200 dark:border-neutral-800">
                                <p class="text-xs text-neutral-500 dark:text-neutral-400 mb-1">Item Type</p>
                                <p id="detailItemType" class="text-sm font-medium text-neutral-900 dark:text-white">-</p>
                            </div>

                            {{-- Status --}}
                            <div class="bg-neutral-50 dark:bg-neutral-900/50 rounded-xl p-3 border border-neutral-200 dark:border-neutral-800">
                                <p class="text-xs text-neutral-500 dark:text-neutral-400 mb-1">Status</p>
                                <span id="detailItemStatus" class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-medium">-</span>
                            </div>
                        </div>
                    </div>

                    {{-- Right Column --}}
                    <div class="space-y-4">
                        <div class="flex items-center gap-2 text-sm font-medium text-neutral-700 dark:text-neutral-300">
                            <x-icon name="palette" class="h-4 w-4" />
                            <span>Details</span>
                        </div>

                        <div class="space-y-3">
                            {{-- Size --}}
                            <div class="bg-neutral-50 dark:bg-neutral-900/50 rounded-xl p-3 border border-neutral-200 dark:border-neutral-800">
                                <p class="text-xs text-neutral-500 dark:text-neutral-400 mb-1">Size</p>
                                <p id="detailItemSize" class="text-sm font-medium text-neutral-900 dark:text-white">-</p>
                            </div>

                            {{-- Color --}}
                            <div class="bg-neutral-50 dark:bg-neutral-900/50 rounded-xl p-3 border border-neutral-200 dark:border-neutral-800">
                                <p class="text-xs text-neutral-500 dark:text-neutral-400 mb-1">Color</p>
                                <p id="detailItemColor" class="text-sm font-medium text-neutral-900 dark:text-white">-</p>
                            </div>

                            {{-- Design --}}
                            <div class="bg-neutral-50 dark:bg-neutral-900/50 rounded-xl p-3 border border-neutral-200 dark:border-neutral-800">
                                <p class="text-xs text-neutral-500 dark:text-neutral-400 mb-1">Design</p>
                                <p id="detailItemDesign" class="text-sm font-medium text-neutral-900 dark:text-white">-</p>
                            </div>

                            {{-- Rental Price --}}
                            <div class="bg-neutral-50 dark:bg-neutral-900/50 rounded-xl p-3 border border-neutral-200 dark:border-neutral-800">
                                <p class="text-xs text-neutral-500 dark:text-neutral-400 mb-1">Rental Price</p>
                                <p id="detailItemPrice" class="text-sm font-medium text-violet-600 dark:text-violet-400 font-geist-mono">-</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Additional Information --}}
                <div class="space-y-3">
                    <div class="flex items-center gap-2 text-sm font-medium text-neutral-700 dark:text-neutral-300">
                        <x-icon name="info" class="h-4 w-4" />
                        <span>Additional Information</span>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-3">
                        {{-- Created At --}}
                        <div class="bg-neutral-50 dark:bg-neutral-900/50 rounded-xl p-3 border border-neutral-200 dark:border-neutral-800">
                            <p class="text-xs text-neutral-500 dark:text-neutral-400 mb-1">Created</p>
                            <p id="detailItemCreated" class="text-sm text-neutral-700 dark:text-neutral-300">-</p>
                        </div>

                        {{-- Updated At --}}
                        <div class="bg-neutral-50 dark:bg-neutral-900/50 rounded-xl p-3 border border-neutral-200 dark:border-neutral-800">
                            <p class="text-xs text-neutral-500 dark:text-neutral-400 mb-1">Last Updated</p>
                            <p id="detailItemUpdated" class="text-sm text-neutral-700 dark:text-neutral-300">-</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Error State --}}
            <div id="itemDetailsError" class="hidden">
                <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl p-4 flex items-center gap-3">
                    <x-icon name="alert-circle" class="h-5 w-5 text-red-500" />
                    <p id="itemDetailsErrorMessage" class="text-sm text-red-600 dark:text-red-400">Failed to load item details</p>
                </div>
            </div>
        </div>

        {{-- Footer --}}
        <div class="flex-shrink-0 flex items-center justify-between px-6 py-4 border-t border-neutral-200 dark:border-neutral-800 bg-neutral-50/50 dark:bg-neutral-900/30 rounded-b-3xl">
            <button
                type="button"
                id="itemDetailsEditBtn"
                onclick="openEditFromDetails()"
                class="inline-flex items-center gap-2 rounded-xl px-4 py-2 text-[14px] font-medium bg-violet-600 text-white dark:hover:text-white hover:text-black dark:text-black hover:bg-violet-500 transition-colors duration-100 ease-in-out"
            >
                <x-icon name="edit" class="h-4 w-4" />
                <span>Edit Item</span>
            </button>
            <button
                type="button"
                onclick="closeItemDetailsModal()"
                class="inline-flex items-center gap-2 rounded-xl px-3.5 py-2 text-[14px] font-medium border border-neutral-300 bg-white text-neutral-700 hover:bg-neutral-100 dark:border-neutral-800 dark:bg-neutral-950/80 dark:text-neutral-200 dark:hover:bg-neutral-900 transition-colors duration-100 ease-in-out"
            >
                Close
            </button>
        </div>
    </div>
</div>

<style>
    /* Optimize modal performance */
    #itemDetailsModal {
        will-change: opacity;
        transform: translateZ(0);
        backface-visibility: hidden;
    }

    #itemDetailsModal .max-w-4xl {
        will-change: transform;
        transform: translateZ(0);
    }

    /* Custom scrollbar for item details content */
    #itemDetailsContent {
        scrollbar-width: thin;
        scrollbar-color: rgba(155, 155, 155, 0.5) transparent;
    }

    #itemDetailsContent::-webkit-scrollbar {
        width: 6px;
    }

    #itemDetailsContent::-webkit-scrollbar-track {
        background: transparent;
    }

    #itemDetailsContent::-webkit-scrollbar-thumb {
        background-color: rgba(155, 155, 155, 0.5);
        border-radius: 3px;
    }

    #itemDetailsContent::-webkit-scrollbar-thumb:hover {
        background-color: rgba(155, 155, 155, 0.7);
    }

    .dark #itemDetailsContent::-webkit-scrollbar-thumb {
        background-color: rgba(100, 100, 100, 0.5);
    }

    .dark #itemDetailsContent::-webkit-scrollbar-thumb:hover {
        background-color: rgba(100, 100, 100, 0.7);
    }

    /* Thumbnail active state */
    .thumbnail-item {
        transition: all 0.2s ease-in-out;
    }

    .thumbnail-item.active {
        ring: 2px;
        ring-color: rgb(139, 92, 246);
    }

    .thumbnail-item:hover:not(.active) {
        opacity: 0.8;
    }
</style>

<script>
    // Use globalThis to avoid redeclaration errors when Turbo navigates between pages
    if (!globalThis.itemDetailsModalState) {
        globalThis.itemDetailsModalState = {
            isOpen: false,
            currentItemId: null,
            currentItem: null,
            selectedImageIndex: 0
        };
    }

    // Open item details modal
    async function openItemDetailsModal(itemId) {
        globalThis.itemDetailsModalState.isOpen = true;
        globalThis.itemDetailsModalState.currentItemId = itemId;
        globalThis.itemDetailsModalState.selectedImageIndex = 0;

        var modal = document.getElementById('itemDetailsModal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');

        // Show loading state
        document.getElementById('itemDetailsLoading').classList.remove('hidden');
        document.getElementById('itemDetailsData').classList.add('hidden');
        document.getElementById('itemDetailsError').classList.add('hidden');
        document.getElementById('itemDetailsTitle').textContent = 'Loading...';

        try {
            var response = await axios.get(`/api/inventories/${itemId}`);
            var item = response.data.data;
            globalThis.itemDetailsModalState.currentItem = item;

            populateItemDetails(item);

            // Hide loading, show data
            document.getElementById('itemDetailsLoading').classList.add('hidden');
            document.getElementById('itemDetailsData').classList.remove('hidden');
        } catch (error) {
            console.error('Error loading item details:', error);
            document.getElementById('itemDetailsLoading').classList.add('hidden');
            document.getElementById('itemDetailsError').classList.remove('hidden');
            document.getElementById('itemDetailsErrorMessage').textContent = 
                error.response?.data?.message || error.message || 'Failed to load item details';
        }
    }

    // Populate item details in the modal
    function populateItemDetails(item) {
        // Title
        document.getElementById('itemDetailsTitle').textContent = item.name || 'Item Details';

        // Basic info
        document.getElementById('detailItemName').textContent = item.name || '-';
        document.getElementById('detailItemSku').textContent = item.sku || '-';
        document.getElementById('detailItemType').textContent = item.item_type 
            ? item.item_type.charAt(0).toUpperCase() + item.item_type.slice(1) 
            : '-';

        // Status with styling
        var statusName = item.status?.status_name || 'unknown';
        var statusEl = document.getElementById('detailItemStatus');
        
        var statusConfig = {
            'available': { label: 'Available', class: 'bg-emerald-500/15 text-emerald-600 border border-emerald-500/40 dark:text-emerald-300' },
            'rented': { label: 'Rented', class: 'bg-blue-500/15 text-blue-600 border border-blue-500/40 dark:text-blue-300' },
            'maintenance': { label: 'Maintenance', class: 'bg-amber-500/15 text-amber-600 border border-amber-500/40 dark:text-amber-300' },
            'retired': { label: 'Retired', class: 'bg-gray-500/15 text-gray-600 border border-gray-500/40 dark:text-gray-300' }
        };
        
        var config = statusConfig[statusName] || statusConfig['retired'];
        statusEl.textContent = config.label;
        statusEl.className = `inline-flex items-center rounded-full px-2.5 py-1 text-xs font-medium ${config.class}`;

        // Details
        document.getElementById('detailItemSize').textContent = item.size || '-';
        document.getElementById('detailItemColor').textContent = item.color || '-';
        document.getElementById('detailItemDesign').textContent = item.design || '-';
        document.getElementById('detailItemPrice').textContent = item.rental_price 
            ? `₱${parseFloat(item.rental_price).toLocaleString()}` 
            : '-';

        // Dates
        document.getElementById('detailItemCreated').textContent = item.created_at 
            ? formatDate(item.created_at) 
            : '-';
        document.getElementById('detailItemUpdated').textContent = item.updated_at 
            ? formatDate(item.updated_at) 
            : '-';

        // Images
        renderItemDetailsImages(item.images || []);
    }

    // Render images in the details modal
    function renderItemDetailsImages(images) {
        var mainImageContainer = document.getElementById('itemDetailsMainImage');
        var thumbnailsContainer = document.getElementById('itemDetailsThumbnails');

        if (!images || images.length === 0) {
            mainImageContainer.innerHTML = `
                <div class="text-neutral-400 dark:text-neutral-600 flex flex-col items-center gap-2">
                    <svg class="h-12 w-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <span class="text-xs">No photos available</span>
                </div>
            `;
            thumbnailsContainer.innerHTML = '';
            return;
        }

        // Sort images - primary first
        var sortedImages = [...images].sort((a, b) => {
            if (a.is_primary && !b.is_primary) return -1;
            if (!a.is_primary && b.is_primary) return 1;
            return (a.display_order || 0) - (b.display_order || 0);
        });

        // Set main image
        var selectedIndex = globalThis.itemDetailsModalState.selectedImageIndex;
        var mainImage = sortedImages[selectedIndex] || sortedImages[0];
        
        mainImageContainer.innerHTML = `
            <img src="${mainImage.image_url}" alt="${mainImage.caption || 'Item photo'}" class="w-full h-full object-contain">
        `;

        // Render thumbnails
        thumbnailsContainer.innerHTML = sortedImages.map((img, index) => `
            <button 
                type="button" 
                onclick="selectDetailsImage(${index})"
                class="thumbnail-item aspect-square rounded-lg overflow-hidden border-2 ${index === selectedIndex ? 'border-violet-500' : 'border-neutral-200 dark:border-neutral-700'} hover:border-violet-400 transition-all duration-200"
            >
                <img src="${img.image_url}" alt="${img.caption || 'Thumbnail'}" class="w-full h-full object-cover">
                ${img.is_primary ? '<span class="absolute bottom-0 left-0 right-0 bg-violet-600 text-white text-[8px] text-center py-0.5">Primary</span>' : ''}
            </button>
        `).join('');
    }

    // Select an image in the details modal
    function selectDetailsImage(index) {
        globalThis.itemDetailsModalState.selectedImageIndex = index;
        var images = globalThis.itemDetailsModalState.currentItem?.images || [];
        renderItemDetailsImages(images);
    }

    // Format date helper
    function formatDate(dateString) {
        var date = new Date(dateString);
        return date.toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'short',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
    }

    // Close item details modal
    function closeItemDetailsModal() {
        globalThis.itemDetailsModalState.isOpen = false;
        globalThis.itemDetailsModalState.currentItemId = null;
        globalThis.itemDetailsModalState.currentItem = null;

        var modal = document.getElementById('itemDetailsModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    // Open edit modal from details modal
    function openEditFromDetails() {
        var itemId = globalThis.itemDetailsModalState.currentItemId;
        closeItemDetailsModal();
        
        // Small delay to allow close animation
        setTimeout(() => {
            openEditItemModal(itemId);
        }, 100);
    }

    // Close modal on escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && globalThis.itemDetailsModalState.isOpen) {
            // Only close if other modals are not open
            if (!globalThis.editItemModalState?.isOpen && 
                !document.getElementById('changeItemStatusModal')?.classList.contains('flex')) {
                closeItemDetailsModal();
            }
        }
    });

    // Close modal on backdrop click
    document.getElementById('itemDetailsModal')?.addEventListener('click', function(e) {
        if (e.target === this && globalThis.itemDetailsModalState.isOpen) {
            closeItemDetailsModal();
        }
    });
</script>
