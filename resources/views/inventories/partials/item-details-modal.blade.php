{{-- Item Details Modal --}}
<div id="itemDetailsModal" class="hidden fixed inset-0 z-50 flex items-center justify-center px-2 py-4 bg-black/60 backdrop-blur-sm overflow-y-auto">
    <div class="w-full max-w-5xl bg-white dark:bg-neutral-950 border border-neutral-200 dark:border-neutral-800 rounded-3xl shadow-2xl flex flex-col max-h-[calc(100vh-2rem)] my-auto">
        {{-- Header --}}
        <div class="flex-shrink-0 flex items-center justify-between px-6 py-4 border-b border-neutral-200 dark:border-neutral-800 bg-neutral-50/80 dark:bg-neutral-900/50 rounded-t-3xl">
            <div>
                <h3 id="itemDetailsTitle" class="text-lg font-semibold text-neutral-900 dark:text-white">Loading...</h3>
                <p id="itemDetailsSubtitle" class="text-xs text-neutral-500 dark:text-neutral-400 mt-0.5">Loading...</p>
            </div>
            <button onclick="closeItemDetailsModal()" class="text-neutral-500 hover:text-neutral-800 dark:hover:text-neutral-200 text-xl transition-colors duration-200">&times;</button>
        </div>

        {{-- Content (scrollable) --}}
        <div id="itemDetailsContent" class="flex-1 overflow-y-auto">
            {{-- Loading State --}}
            <div id="itemDetailsLoading" class="flex items-center justify-center py-16">
                <div class="flex flex-col items-center gap-3">
                    <div class="h-8 w-8 animate-spin rounded-full border-2 border-violet-600 border-t-transparent"></div>
                    <p class="text-sm text-neutral-500 dark:text-neutral-400">Loading item details...</p>
                </div>
            </div>

            {{-- Item Details (hidden initially) --}}
            <div id="itemDetailsData" class="hidden">
                {{-- Main Content: Image Gallery + Info Side by Side --}}
                <div class="flex flex-col lg:flex-row">
                    {{-- Left: Image Gallery --}}
                    <div class="lg:w-1/2 p-6 border-b lg:border-b-0 lg:border-r border-neutral-200 dark:border-neutral-800">
                        {{-- Main Image with Navigation Arrows --}}
                        <div class="relative group">
                            <div id="itemDetailsMainImage" class="aspect-square bg-neutral-100 dark:bg-neutral-900 rounded-2xl overflow-hidden flex items-center justify-center border border-neutral-200 dark:border-neutral-800">
                                <div class="text-neutral-400 dark:text-neutral-600 flex flex-col items-center gap-2">
                                    <x-icon name="image" class="h-16 w-16" />
                                    <span class="text-sm">No photos available</span>
                                </div>
                            </div>

                            {{-- Left Arrow --}}
                            <button
                                type="button"
                                id="itemDetailsPrevBtn"
                                onclick="navigateDetailsImage(-1)"
                                class="hidden absolute left-2 top-1/2 -translate-y-1/2 w-10 h-10 rounded-full bg-white/90 dark:bg-neutral-800/90 border border-neutral-200 dark:border-neutral-700 shadow-lg flex items-center justify-center text-neutral-700 dark:text-neutral-200 hover:bg-white dark:hover:bg-neutral-700 transition-all duration-200 opacity-0 group-hover:opacity-100 focus:opacity-100"
                                title="Previous image"
                            >
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                                </svg>
                            </button>

                            {{-- Right Arrow --}}
                            <button
                                type="button"
                                id="itemDetailsNextBtn"
                                onclick="navigateDetailsImage(1)"
                                class="hidden absolute right-2 top-1/2 -translate-y-1/2 w-10 h-10 rounded-full bg-white/90 dark:bg-neutral-800/90 border border-neutral-200 dark:border-neutral-700 shadow-lg flex items-center justify-center text-neutral-700 dark:text-neutral-200 hover:bg-white dark:hover:bg-neutral-700 transition-all duration-200 opacity-0 group-hover:opacity-100 focus:opacity-100"
                                title="Next image"
                            >
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </button>

                            {{-- Image Counter --}}
                            <div id="itemDetailsImageCounter" class="hidden absolute bottom-3 right-3 bg-black/60 text-white text-xs font-medium px-2.5 py-1 rounded-full backdrop-blur-sm">
                                1 / 1
                            </div>
                        </div>

                        {{-- Thumbnails Row --}}
                        <div id="itemDetailsThumbnails" class="flex gap-2 overflow-x-auto pb-2 mt-4">
                            {{-- Thumbnails will be inserted here --}}
                        </div>
                    </div>

                    {{-- Right: Item Information --}}
                    <div class="lg:w-1/2 p-6 space-y-5">
                        {{-- Status Section --}}
                        <div id="detailStatusCard" class="rounded-xl p-4 border">
                            <div class="flex items-center gap-2">
                                <span id="detailStatusDot" class="h-2 w-2 rounded-full"></span>
                                <span id="detailItemStatus" class="text-sm font-semibold">-</span>
                            </div>
                            <p id="detailStatusSubtitle" class="text-xs text-neutral-500 dark:text-neutral-400 mt-1">-</p>
                        </div>

                        {{-- Overview & Pricing Row --}}
                        <div class="grid grid-cols-2 gap-4">
                            {{-- Overview Section --}}
                            <div class="rounded-xl p-4 border border-neutral-200 dark:border-neutral-800">
                                <div class="flex items-center gap-2 text-xs font-medium text-neutral-500 dark:text-neutral-400 uppercase tracking-wider mb-3">
                                    <x-icon name="info" class="h-3.5 w-3.5" />
                                    <span>Overview</span>
                                </div>
                                <div class="space-y-3">
                                    {{-- SKU --}}
                                    <div>
                                        <p class="text-xs text-neutral-500 dark:text-neutral-400 mb-1">SKU</p>
                                        <p id="detailItemSku" class="text-sm font-medium text-neutral-900 dark:text-white">-</p>
                                    </div>
                                    {{-- Type --}}
                                    <div>
                                        <div class="flex items-center gap-1.5 mb-1">
                                            <x-icon name="tag" class="h-3 w-3 text-neutral-400" />
                                            <p class="text-xs text-neutral-500 dark:text-neutral-400">Type</p>
                                        </div>
                                        <p id="detailItemType" class="text-sm font-medium text-neutral-900 dark:text-white">-</p>
                                    </div>
                                    <div>
                                        <div class="flex items-center gap-1.5 mb-1">
                                            <x-icon name="list" class="h-3 w-3 text-neutral-400" />
                                            <p class="text-xs text-neutral-500 dark:text-neutral-400">Variant</p>
                                        </div>
                                        <p id="detailItemVariant" class="text-sm font-medium text-neutral-900 dark:text-white">-</p>
                                    </div>
                                </div>
                            </div>

                            {{-- Pricing Section --}}
                            <div class="rounded-xl p-4 border border-neutral-200 dark:border-neutral-800">
                                <div class="flex items-center gap-2 text-xs font-medium text-neutral-500 dark:text-neutral-400 uppercase tracking-wider mb-3">
                                    <x-icon name="currency-peso" class="h-3.5 w-3.5" />
                                    <span>Pricing</span>
                                </div>
                                <div class="space-y-3">
                                    {{-- Rental Price --}}
                                    <div>
                                        <p id="detailItemPrice" class="text-xl font-bold text-violet-600 dark:text-violet-400 font-geist-mono">-</p>
                                        <p class="text-xs text-neutral-500 dark:text-neutral-400">Rental price</p>
                                    </div>
                                    <hr class="border-neutral-200 dark:border-neutral-800">
                                    {{-- Selling Price --}}
                                    <div id="detailSellingPriceRow" class="hidden flex items-center justify-between">
                                        <span class="text-xs text-neutral-500 dark:text-neutral-400">Selling price</span>
                                        <span id="detailItemSellingPrice" class="text-sm font-medium text-neutral-900 dark:text-white font-geist-mono">-</span>
                                    </div>
                                    {{-- Deposit --}}
                                    <div class="flex items-center justify-between">
                                        <span class="text-xs text-neutral-500 dark:text-neutral-400">Deposit</span>
                                        <span id="detailItemDeposit" class="text-sm font-medium text-neutral-900 dark:text-white font-geist-mono">-</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Specifications Section --}}
                        <div class="space-y-3">
                            <div class="flex items-center gap-2 text-xs font-medium text-neutral-500 dark:text-neutral-400 uppercase tracking-wider">
                                <x-icon name="list" class="h-3.5 w-3.5" />
                                <span>Specifications</span>
                            </div>
                            <div class="grid grid-cols-2 gap-3">
                                {{-- Size --}}
                                <div class="rounded-xl p-3 border border-neutral-200 dark:border-neutral-800">
                                    <div class="flex items-center gap-1.5 mb-1">
                                        <x-icon name="ruler" class="h-3 w-3 text-neutral-400" />
                                        <p class="text-xs text-neutral-500 dark:text-neutral-400">Size</p>
                                    </div>
                                    <p id="detailItemSize" class="text-sm font-medium text-neutral-900 dark:text-white">-</p>
                                </div>
                                {{-- Color --}}
                                <div class="rounded-xl p-3 border border-neutral-200 dark:border-neutral-800">
                                    <div class="flex items-center gap-1.5 mb-1">
                                        <x-icon name="palette" class="h-3 w-3 text-neutral-400" />
                                        <p class="text-xs text-neutral-500 dark:text-neutral-400">Color</p>
                                    </div>
                                    <p id="detailItemColor" class="text-sm font-medium text-neutral-900 dark:text-white">-</p>
                                </div>
                            </div>
                            {{-- Design --}}
                            <div class="rounded-xl p-3 border border-neutral-200 dark:border-neutral-800">
                                <div class="flex items-center gap-1.5 mb-1">
                                    <x-icon name="sparkles" class="h-3 w-3 text-neutral-400" />
                                    <p class="text-xs text-neutral-500 dark:text-neutral-400">Design</p>
                                </div>
                                <p id="detailItemDesign" class="text-sm font-medium text-neutral-900 dark:text-white">-</p>
                            </div>
                        </div>

                        {{-- Timeline --}}
                        <div class="pt-4 border-t border-neutral-200 dark:border-neutral-800">
                            <div class="flex items-center gap-2 text-xs font-medium text-neutral-500 dark:text-neutral-400 uppercase tracking-wider mb-3">
                                <x-icon name="clock" class="h-3.5 w-3.5" />
                                <span>Timeline</span>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div class="flex items-center gap-2">
                                    <div class="h-1.5 w-1.5 rounded-full bg-emerald-500"></div>
                                    <span class="text-xs text-neutral-500 dark:text-neutral-400">Added:</span>
                                    <span id="detailItemCreated" class="text-xs text-neutral-700 dark:text-neutral-300">-</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <div class="h-1.5 w-1.5 rounded-full bg-blue-500"></div>
                                    <span class="text-xs text-neutral-500 dark:text-neutral-400">Updated:</span>
                                    <span id="detailItemUpdated" class="text-xs text-neutral-700 dark:text-neutral-300">-</span>
                                </div>
                            </div>
                            {{-- Last Updated By --}}
                            <div id="detailUpdatedBySection" class="hidden mt-3 pt-3 border-t border-neutral-200 dark:border-neutral-800">
                                <div class="flex items-center gap-2">
                                    <div class="h-1.5 w-1.5 rounded-full bg-violet-500"></div>
                                    <span class="text-xs text-neutral-500 dark:text-neutral-400">Last updated by:</span>
                                    <span id="detailUpdatedByName" class="text-xs text-neutral-700 dark:text-neutral-300">-</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Error State --}}
            <div id="itemDetailsError" class="hidden p-6">
                <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl p-4 flex items-center gap-3">
                    <x-icon name="alert-circle" class="h-5 w-5 text-red-500 flex-shrink-0" />
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
                class="inline-flex items-center gap-2 rounded-xl px-4 py-2.5 text-[14px] font-medium bg-violet-600 text-white hover:bg-violet-500 transition-colors duration-100 ease-in-out"
            >
                <x-icon name="edit" class="h-4 w-4" />
                <span>Edit Item</span>
            </button>
            <button
                type="button"
                onclick="closeItemDetailsModal()"
                class="inline-flex items-center gap-2 rounded-xl px-4 py-2.5 text-[14px] font-medium border border-neutral-300 bg-white text-neutral-700 hover:bg-neutral-100 dark:border-neutral-700 dark:bg-neutral-900 dark:text-neutral-200 dark:hover:bg-neutral-800 transition-colors duration-100 ease-in-out"
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

    #itemDetailsModal .max-w-5xl {
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

    /* Thumbnail scrollbar */
    #itemDetailsThumbnails {
        scrollbar-width: thin;
        scrollbar-color: rgba(155, 155, 155, 0.3) transparent;
    }

    #itemDetailsThumbnails::-webkit-scrollbar {
        height: 4px;
    }

    #itemDetailsThumbnails::-webkit-scrollbar-track {
        background: transparent;
    }

    #itemDetailsThumbnails::-webkit-scrollbar-thumb {
        background-color: rgba(155, 155, 155, 0.3);
        border-radius: 2px;
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
        // Title and Subtitle
        document.getElementById('itemDetailsTitle').textContent = item.name || 'Item Details';
        var subtitleParts = [item.sku, item.item_type, item.color, 'Size ' + item.size].filter(Boolean);
        document.getElementById('itemDetailsSubtitle').textContent = subtitleParts.join(' • ') || 'Item Details';

        // Status with dynamic styling
        var statusName = item.status?.status_name || 'unknown';
        var statusCard = document.getElementById('detailStatusCard');
        var statusDot = document.getElementById('detailStatusDot');
        var statusText = document.getElementById('detailItemStatus');
        var statusSubtitle = document.getElementById('detailStatusSubtitle');

        var statusConfig = {
            'available': {
                label: 'Available',
                subtitle: 'Ready for rent',
                cardClass: 'bg-emerald-500/10 dark:bg-emerald-500/20 border-emerald-500/30 dark:border-emerald-500/30',
                dotClass: 'bg-emerald-500',
                textClass: 'text-emerald-700 dark:text-emerald-400'
            },
            'rented': {
                label: 'Rented',
                subtitle: 'Currently rented',
                cardClass: 'bg-blue-500/10 dark:bg-blue-500/20 border-blue-500/30 dark:border-blue-500/30',
                dotClass: 'bg-blue-500',
                textClass: 'text-blue-700 dark:text-blue-400'
            },
            'maintenance': {
                label: 'Maintenance',
                subtitle: 'Under maintenance',
                cardClass: 'bg-amber-500/10 dark:bg-amber-500/20 border-amber-500/30 dark:border-amber-500/30',
                dotClass: 'bg-amber-500',
                textClass: 'text-amber-700 dark:text-amber-400'
            },
            'reserved': {
                label: 'Reserved',
                subtitle: 'Booked for schedule',
                cardClass: 'bg-cyan-500/10 dark:bg-cyan-500/20 border-cyan-500/30 dark:border-cyan-500/30',
                dotClass: 'bg-cyan-500',
                textClass: 'text-cyan-700 dark:text-cyan-400'
            },
            'sold': {
                label: 'Sold',
                subtitle: 'No longer rentable',
                cardClass: 'bg-rose-500/10 dark:bg-rose-500/20 border-rose-500/30 dark:border-rose-500/30',
                dotClass: 'bg-rose-500',
                textClass: 'text-rose-700 dark:text-rose-400'
            },
            'retired': {
                label: 'Retired',
                subtitle: 'No longer available',
                cardClass: 'bg-neutral-500/10 dark:bg-neutral-500/20 border-neutral-500/30 dark:border-neutral-500/30',
                dotClass: 'bg-neutral-500',
                textClass: 'text-neutral-700 dark:text-neutral-400'
            }
        };

        var config = statusConfig[statusName] || statusConfig['retired'];
        statusCard.className = `rounded-xl p-4 border ${config.cardClass}`;
        statusDot.className = `h-2 w-2 rounded-full ${config.dotClass}`;
        statusText.className = `text-sm font-semibold ${config.textClass}`;
        statusText.textContent = config.label;
        statusSubtitle.textContent = config.subtitle;

        // Info fields
        document.getElementById('detailItemSku').textContent = item.sku || '-';
        document.getElementById('detailItemType').textContent = item.item_type
            ? item.item_type.charAt(0).toUpperCase() + item.item_type.slice(1)
            : '-';
        document.getElementById('detailItemVariant').textContent = item.variant?.variant_id
            ? `#${item.variant.variant_id}`
            : 'Unassigned';
        document.getElementById('detailItemSize').textContent = item.size || '-';
        document.getElementById('detailItemColor').textContent = item.color || '-';
        document.getElementById('detailItemDesign').textContent = item.design || '-';
        document.getElementById('detailItemPrice').textContent = item.rental_price
            ? `₱${parseFloat(item.rental_price).toLocaleString()}`
            : '-';

        // Selling Price (only show when item is sellable)
        var sellingPriceRow = document.getElementById('detailSellingPriceRow');
        var sellingPriceElement = document.getElementById('detailItemSellingPrice');
        var isSellable = item.is_sellable === true || item.is_sellable === 1 || item.is_sellable === '1';
        if (isSellable) {
            sellingPriceRow.classList.remove('hidden');
            if (item.selling_price && parseFloat(item.selling_price) > 0) {
                sellingPriceElement.textContent = `₱${parseFloat(item.selling_price).toLocaleString()}`;
            } else {
                sellingPriceElement.textContent = '-';
            }
        } else {
            sellingPriceRow.classList.add('hidden');
        }

        // Deposit Amount
        var depositAmount = item.deposit_amount ? parseFloat(item.deposit_amount) : 0;
        document.getElementById('detailItemDeposit').textContent = `₱${depositAmount.toLocaleString()}`;

        // Dates
        document.getElementById('detailItemCreated').textContent = item.created_at
            ? formatDate(item.created_at)
            : '-';
        document.getElementById('detailItemUpdated').textContent = item.updated_at
            ? formatDate(item.updated_at)
            : '-';

        // Last Updated By (only show if updated by a user)
        var updatedBySection = document.getElementById('detailUpdatedBySection');
        var updatedByName = document.getElementById('detailUpdatedByName');
        if (item.updated_by_user && item.updated_by_user.name) {
            updatedBySection.classList.remove('hidden');
            updatedByName.textContent = item.updated_by_user.name;
        } else {
            updatedBySection.classList.add('hidden');
        }

        // Images
        renderItemDetailsImages(item.images || []);
    }

    // Render images in the details modal
    function renderItemDetailsImages(images) {
        var mainImageContainer = document.getElementById('itemDetailsMainImage');
        var thumbnailsContainer = document.getElementById('itemDetailsThumbnails');
        var prevBtn = document.getElementById('itemDetailsPrevBtn');
        var nextBtn = document.getElementById('itemDetailsNextBtn');
        var imageCounter = document.getElementById('itemDetailsImageCounter');

        if (!images || images.length === 0) {
            mainImageContainer.innerHTML = `
                <div class="text-neutral-400 dark:text-neutral-600 flex flex-col items-center gap-3">
                    <svg class="h-16 w-16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <span class="text-sm">No photos available</span>
                </div>
            `;
            thumbnailsContainer.innerHTML = '';
            // Hide navigation buttons and counter
            prevBtn.classList.add('hidden');
            nextBtn.classList.add('hidden');
            imageCounter.classList.add('hidden');
            return;
        }

        // Sort images - primary first
        var sortedImages = [...images].sort((a, b) => {
            if (a.is_primary && !b.is_primary) return -1;
            if (!a.is_primary && b.is_primary) return 1;
            return (a.display_order || 0) - (b.display_order || 0);
        });

        // Store sorted images for navigation
        globalThis.itemDetailsModalState.sortedImages = sortedImages;

        // Set main image
        var selectedIndex = globalThis.itemDetailsModalState.selectedImageIndex;
        var mainImage = sortedImages[selectedIndex] || sortedImages[0];

        // Main image with primary badge overlay if applicable
        var primaryBadge = mainImage.is_primary
            ? `<span class="absolute top-3 left-3 bg-violet-600 text-white text-xs font-medium px-2.5 py-1 rounded-full shadow-lg flex items-center gap-1.5">
                    <svg class="h-3.5 w-3.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10.868 2.884c-.321-.772-1.415-.772-1.736 0l-1.83 4.401-4.753.381c-.833.067-1.171 1.107-.536 1.651l3.62 3.102-1.106 4.637c-.194.813.691 1.456 1.405 1.02L10 15.591l4.069 2.485c.713.436 1.598-.207 1.404-1.02l-1.106-4.637 3.62-3.102c.635-.544.297-1.584-.536-1.65l-4.752-.382-1.831-4.401z" clip-rule="evenodd" />
                    </svg>
                    Primary
               </span>`
            : '';

        // View type badge
        var viewTypeBadge = mainImage.view_type
            ? `<span class="absolute bottom-3 left-3 bg-black/60 text-white text-xs font-medium px-2.5 py-1 rounded-full backdrop-blur-sm">
                    ${mainImage.view_type.charAt(0).toUpperCase() + mainImage.view_type.slice(1)} View
               </span>`
            : '';

        mainImageContainer.innerHTML = `
            <div class="relative w-full h-full">
                <img src="${mainImage.image_url}" alt="${mainImage.caption || 'Item photo'}" class="w-full h-full object-contain">
                ${primaryBadge}
                ${viewTypeBadge}
            </div>
        `;

        // Show/hide navigation arrows based on image count
        if (sortedImages.length > 1) {
            // Show arrows (they will appear on hover due to CSS)
            prevBtn.classList.remove('hidden');
            prevBtn.classList.add('flex');
            nextBtn.classList.remove('hidden');
            nextBtn.classList.add('flex');
            imageCounter.classList.remove('hidden');

            // Update counter
            imageCounter.textContent = `${selectedIndex + 1} / ${sortedImages.length}`;

            // Disable prev button if at first image
            if (selectedIndex === 0) {
                prevBtn.classList.add('opacity-30', 'cursor-not-allowed');
                prevBtn.classList.remove('hover:bg-white', 'dark:hover:bg-neutral-700');
            } else {
                prevBtn.classList.remove('opacity-30', 'cursor-not-allowed');
                prevBtn.classList.add('hover:bg-white', 'dark:hover:bg-neutral-700');
            }

            // Disable next button if at last image
            if (selectedIndex === sortedImages.length - 1) {
                nextBtn.classList.add('opacity-30', 'cursor-not-allowed');
                nextBtn.classList.remove('hover:bg-white', 'dark:hover:bg-neutral-700');
            } else {
                nextBtn.classList.remove('opacity-30', 'cursor-not-allowed');
                nextBtn.classList.add('hover:bg-white', 'dark:hover:bg-neutral-700');
            }
        } else {
            prevBtn.classList.add('hidden');
            prevBtn.classList.remove('flex');
            nextBtn.classList.add('hidden');
            nextBtn.classList.remove('flex');
            imageCounter.classList.add('hidden');
        }

        // Render thumbnails - horizontal row
        thumbnailsContainer.innerHTML = sortedImages.map((img, index) => {
            var isSelected = index === selectedIndex;
            var isPrimary = img.is_primary;

            return `
                <button
                    type="button"
                    onclick="selectDetailsImage(${index})"
                    class="relative flex-shrink-0 w-16 h-16 rounded-xl overflow-hidden border-2 transition-all duration-200 ${isSelected ? 'border-violet-500 ring-2 ring-violet-500/30' : 'border-neutral-200 dark:border-neutral-700 hover:border-violet-400'}"
                    title="${img.view_type ? img.view_type.charAt(0).toUpperCase() + img.view_type.slice(1) + ' View' : 'Photo'}"
                >
                    <img src="${img.image_url}" alt="${img.caption || 'Thumbnail'}" class="w-full h-full object-cover">
                    ${isPrimary ? `<span class="absolute top-0.5 right-0.5 w-4 h-4 bg-violet-600 rounded-full flex items-center justify-center shadow">
                        <svg class="h-2.5 w-2.5 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10.868 2.884c-.321-.772-1.415-.772-1.736 0l-1.83 4.401-4.753.381c-.833.067-1.171 1.107-.536 1.651l3.62 3.102-1.106 4.637c-.194.813.691 1.456 1.405 1.02L10 15.591l4.069 2.485c.713.436 1.598-.207 1.404-1.02l-1.106-4.637 3.62-3.102c.635-.544.297-1.584-.536-1.65l-4.752-.382-1.831-4.401z" clip-rule="evenodd" />
                        </svg>
                    </span>` : ''}
                </button>
            `;
        }).join('');
    }

    // Navigate to previous/next image
    function navigateDetailsImage(direction) {
        var sortedImages = globalThis.itemDetailsModalState.sortedImages || [];
        if (sortedImages.length <= 1) return;

        var currentIndex = globalThis.itemDetailsModalState.selectedImageIndex;
        var newIndex = currentIndex + direction;

        // Boundary checks
        if (newIndex < 0 || newIndex >= sortedImages.length) return;

        globalThis.itemDetailsModalState.selectedImageIndex = newIndex;
        var images = globalThis.itemDetailsModalState.currentItem?.images || [];
        renderItemDetailsImages(images);
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
            day: 'numeric'
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

    // Handle keyboard navigation
    document.addEventListener('keydown', function(e) {
        if (!globalThis.itemDetailsModalState.isOpen) return;

        // Check if other modals are open (edit modal, status modal)
        var otherModalsOpen = globalThis.editItemModalState?.isOpen ||
            document.getElementById('changeItemStatusModal')?.classList.contains('flex');

        if (e.key === 'Escape' && !otherModalsOpen) {
            closeItemDetailsModal();
        }

        // Arrow key navigation for images (only when no other modals are open)
        if (!otherModalsOpen) {
            if (e.key === 'ArrowLeft') {
                e.preventDefault();
                navigateDetailsImage(-1);
            } else if (e.key === 'ArrowRight') {
                e.preventDefault();
                navigateDetailsImage(1);
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
