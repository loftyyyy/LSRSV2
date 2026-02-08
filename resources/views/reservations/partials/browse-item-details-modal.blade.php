{{-- Browse Item Details Modal --}}
<div id="browseItemDetailsModal" class="hidden fixed inset-0 z-[60] flex items-center justify-center px-2 py-4 bg-black/60 backdrop-blur-sm overflow-y-auto">
    <div class="w-full max-w-5xl bg-white dark:bg-neutral-950 border border-neutral-200 dark:border-neutral-800 rounded-3xl shadow-2xl flex flex-col max-h-[calc(100vh-2rem)] my-auto">
        <div class="flex-shrink-0 flex items-center justify-between px-6 py-4 border-b border-neutral-200 dark:border-neutral-800 bg-neutral-50/80 dark:bg-neutral-900/50 rounded-t-3xl">
            <div>
                <h3 id="browseItemDetailsTitle" class="text-lg font-semibold text-neutral-900 dark:text-white">Loading...</h3>
                <p id="browseItemDetailsSubtitle" class="text-xs text-neutral-500 dark:text-neutral-400 mt-0.5">Loading...</p>
            </div>
            <button type="button" onclick="closeBrowseItemDetailsModal()" class="text-neutral-500 hover:text-neutral-800 dark:hover:text-neutral-200 text-xl transition-colors duration-200">&times;</button>
        </div>

        <div id="browseItemDetailsContent" class="flex-1 overflow-y-auto">
            <div id="browseItemDetailsLoading" class="flex items-center justify-center py-16">
                <div class="flex flex-col items-center gap-3">
                    <div class="h-8 w-8 animate-spin rounded-full border-2 border-violet-600 border-t-transparent"></div>
                    <p class="text-sm text-neutral-500 dark:text-neutral-400">Loading item details...</p>
                </div>
            </div>

            <div id="browseItemDetailsData" class="hidden">
                <div class="flex flex-col lg:flex-row">
                    <div class="lg:w-1/2 p-6 border-b lg:border-b-0 lg:border-r border-neutral-200 dark:border-neutral-800">
                        <div class="relative group">
                            <div id="browseItemDetailsMainImage" class="aspect-square bg-neutral-100 dark:bg-neutral-900 rounded-2xl overflow-hidden flex items-center justify-center border border-neutral-200 dark:border-neutral-800">
                                <div class="text-neutral-400 dark:text-neutral-600 flex flex-col items-center gap-2">
                                    <x-icon name="image" class="h-16 w-16" />
                                    <span class="text-sm">No photos available</span>
                                </div>
                            </div>

                            <button
                                type="button"
                                id="browseItemDetailsPrevBtn"
                                onclick="navigateBrowseItemDetailsImage(-1)"
                                class="hidden absolute left-2 top-1/2 -translate-y-1/2 w-10 h-10 rounded-full bg-white/90 dark:bg-neutral-800/90 border border-neutral-200 dark:border-neutral-700 shadow-lg flex items-center justify-center text-neutral-700 dark:text-neutral-200 hover:bg-white dark:hover:bg-neutral-700 transition-all duration-200 opacity-0 group-hover:opacity-100 focus:opacity-100"
                                title="Previous image"
                            >
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                                </svg>
                            </button>

                            <button
                                type="button"
                                id="browseItemDetailsNextBtn"
                                onclick="navigateBrowseItemDetailsImage(1)"
                                class="hidden absolute right-2 top-1/2 -translate-y-1/2 w-10 h-10 rounded-full bg-white/90 dark:bg-neutral-800/90 border border-neutral-200 dark:border-neutral-700 shadow-lg flex items-center justify-center text-neutral-700 dark:text-neutral-200 hover:bg-white dark:hover:bg-neutral-700 transition-all duration-200 opacity-0 group-hover:opacity-100 focus:opacity-100"
                                title="Next image"
                            >
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </button>

                            <div id="browseItemDetailsImageCounter" class="hidden absolute bottom-3 right-3 bg-black/60 text-white text-xs font-medium px-2.5 py-1 rounded-full backdrop-blur-sm">
                                1 / 1
                            </div>
                        </div>

                        <div id="browseItemDetailsThumbnails" class="flex gap-2 overflow-x-auto pb-2 mt-4"></div>
                    </div>

                    <div class="lg:w-1/2 p-6 space-y-5">
                        <div id="browseItemDetailStatusCard" class="rounded-xl p-4 border">
                            <div class="flex items-center gap-2">
                                <span id="browseItemDetailStatusDot" class="h-2 w-2 rounded-full"></span>
                                <span id="browseItemDetailStatusText" class="text-sm font-semibold">-</span>
                            </div>
                            <p id="browseItemDetailStatusSubtitle" class="text-xs text-neutral-500 dark:text-neutral-400 mt-1">-</p>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div class="rounded-xl p-4 border border-neutral-200 dark:border-neutral-800">
                                <div class="flex items-center gap-2 text-xs font-medium text-neutral-500 dark:text-neutral-400 uppercase tracking-wider mb-3">
                                    <x-icon name="info" class="h-3.5 w-3.5" />
                                    <span>Overview</span>
                                </div>
                                <div class="space-y-3">
                                    <div>
                                        <p class="text-xs text-neutral-500 dark:text-neutral-400 mb-1">SKU</p>
                                        <p id="browseItemDetailSku" class="text-sm font-medium text-neutral-900 dark:text-white">-</p>
                                    </div>
                                    <div>
                                        <div class="flex items-center gap-1.5 mb-1">
                                            <x-icon name="tag" class="h-3 w-3 text-neutral-400" />
                                            <p class="text-xs text-neutral-500 dark:text-neutral-400">Type</p>
                                        </div>
                                        <p id="browseItemDetailType" class="text-sm font-medium text-neutral-900 dark:text-white">-</p>
                                    </div>
                                </div>
                            </div>

                            <div class="rounded-xl p-4 border border-neutral-200 dark:border-neutral-800">
                                <div class="flex items-center gap-2 text-xs font-medium text-neutral-500 dark:text-neutral-400 uppercase tracking-wider mb-3">
                                    <x-icon name="currency-peso" class="h-3.5 w-3.5" />
                                    <span>Pricing</span>
                                </div>
                                <div class="space-y-3">
                                    <div>
                                        <p id="browseItemDetailRentalPrice" class="text-xl font-bold text-violet-600 dark:text-violet-400 font-geist-mono">-</p>
                                        <p class="text-xs text-neutral-500 dark:text-neutral-400">Rental price</p>
                                    </div>
                                    <hr class="border-neutral-200 dark:border-neutral-800">
                                    <div id="browseItemDetailSellingPriceRow" class="hidden flex items-center justify-between">
                                        <span class="text-xs text-neutral-500 dark:text-neutral-400">Selling price</span>
                                        <span id="browseItemDetailSellingPrice" class="text-sm font-medium text-neutral-900 dark:text-white font-geist-mono">-</span>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <span class="text-xs text-neutral-500 dark:text-neutral-400">Deposit</span>
                                        <span id="browseItemDetailDeposit" class="text-sm font-medium text-neutral-900 dark:text-white font-geist-mono">-</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="space-y-3">
                            <div class="flex items-center gap-2 text-xs font-medium text-neutral-500 dark:text-neutral-400 uppercase tracking-wider">
                                <x-icon name="list" class="h-3.5 w-3.5" />
                                <span>Specifications</span>
                            </div>
                            <div class="grid grid-cols-2 gap-3">
                                <div class="rounded-xl p-3 border border-neutral-200 dark:border-neutral-800">
                                    <div class="flex items-center gap-1.5 mb-1">
                                        <x-icon name="ruler" class="h-3 w-3 text-neutral-400" />
                                        <p class="text-xs text-neutral-500 dark:text-neutral-400">Size</p>
                                    </div>
                                    <p id="browseItemDetailSize" class="text-sm font-medium text-neutral-900 dark:text-white">-</p>
                                </div>
                                <div class="rounded-xl p-3 border border-neutral-200 dark:border-neutral-800">
                                    <div class="flex items-center gap-1.5 mb-1">
                                        <x-icon name="palette" class="h-3 w-3 text-neutral-400" />
                                        <p class="text-xs text-neutral-500 dark:text-neutral-400">Color</p>
                                    </div>
                                    <p id="browseItemDetailColor" class="text-sm font-medium text-neutral-900 dark:text-white">-</p>
                                </div>
                            </div>
                            <div class="rounded-xl p-3 border border-neutral-200 dark:border-neutral-800">
                                <div class="flex items-center gap-1.5 mb-1">
                                    <x-icon name="sparkles" class="h-3 w-3 text-neutral-400" />
                                    <p class="text-xs text-neutral-500 dark:text-neutral-400">Design</p>
                                </div>
                                <p id="browseItemDetailDesign" class="text-sm font-medium text-neutral-900 dark:text-white">-</p>
                            </div>
                        </div>

                        <div class="pt-4 border-t border-neutral-200 dark:border-neutral-800">
                            <div class="flex items-center gap-2 text-xs font-medium text-neutral-500 dark:text-neutral-400 uppercase tracking-wider mb-3">
                                <x-icon name="clock" class="h-3.5 w-3.5" />
                                <span>Timeline</span>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div class="flex items-center gap-2">
                                    <div class="h-1.5 w-1.5 rounded-full bg-emerald-500"></div>
                                    <span class="text-xs text-neutral-500 dark:text-neutral-400">Added:</span>
                                    <span id="browseItemDetailCreated" class="text-xs text-neutral-700 dark:text-neutral-300">-</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <div class="h-1.5 w-1.5 rounded-full bg-blue-500"></div>
                                    <span class="text-xs text-neutral-500 dark:text-neutral-400">Updated:</span>
                                    <span id="browseItemDetailUpdated" class="text-xs text-neutral-700 dark:text-neutral-300">-</span>
                                </div>
                            </div>
                            <div id="browseItemDetailUpdatedBySection" class="hidden mt-3 pt-3 border-t border-neutral-200 dark:border-neutral-800">
                                <div class="flex items-center gap-2">
                                    <div class="h-1.5 w-1.5 rounded-full bg-violet-500"></div>
                                    <span class="text-xs text-neutral-500 dark:text-neutral-400">Last updated by:</span>
                                    <span id="browseItemDetailUpdatedBy" class="text-xs text-neutral-700 dark:text-neutral-300">-</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div id="browseItemDetailsError" class="hidden p-6">
                <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl p-4 flex items-center gap-3">
                    <x-icon name="alert-circle" class="h-5 w-5 text-red-500 flex-shrink-0" />
                    <p id="browseItemDetailsErrorMessage" class="text-sm text-red-600 dark:text-red-400">Failed to load item details</p>
                </div>
            </div>
        </div>

        <div class="flex-shrink-0 flex items-center justify-end px-6 py-4 border-t border-neutral-200 dark:border-neutral-800 bg-neutral-50/50 dark:bg-neutral-900/30 rounded-b-3xl">
            <button
                type="button"
                onclick="closeBrowseItemDetailsModal()"
                class="inline-flex items-center gap-2 rounded-xl px-4 py-2.5 text-[14px] font-medium border border-neutral-300 bg-white text-neutral-700 hover:bg-neutral-100 dark:border-neutral-700 dark:bg-neutral-900 dark:text-neutral-200 dark:hover:bg-neutral-800 transition-colors duration-100 ease-in-out"
            >
                Close
            </button>
        </div>
    </div>
</div>

<style>
    #browseItemDetailsModal {
        will-change: opacity;
        transform: translateZ(0);
        backface-visibility: hidden;
    }

    #browseItemDetailsModal .max-w-5xl {
        will-change: transform;
        transform: translateZ(0);
    }

    #browseItemDetailsContent {
        scrollbar-width: thin;
        scrollbar-color: rgba(155, 155, 155, 0.5) transparent;
    }

    #browseItemDetailsContent::-webkit-scrollbar {
        width: 6px;
    }

    #browseItemDetailsContent::-webkit-scrollbar-track {
        background: transparent;
    }

    #browseItemDetailsContent::-webkit-scrollbar-thumb {
        background-color: rgba(155, 155, 155, 0.5);
        border-radius: 3px;
    }

    #browseItemDetailsContent::-webkit-scrollbar-thumb:hover {
        background-color: rgba(155, 155, 155, 0.7);
    }

    .dark #browseItemDetailsContent::-webkit-scrollbar-thumb {
        background-color: rgba(100, 100, 100, 0.5);
    }

    .dark #browseItemDetailsContent::-webkit-scrollbar-thumb:hover {
        background-color: rgba(100, 100, 100, 0.7);
    }

    #browseItemDetailsThumbnails {
        scrollbar-width: thin;
        scrollbar-color: rgba(155, 155, 155, 0.3) transparent;
    }

    #browseItemDetailsThumbnails::-webkit-scrollbar {
        height: 4px;
    }

    #browseItemDetailsThumbnails::-webkit-scrollbar-track {
        background: transparent;
    }

    #browseItemDetailsThumbnails::-webkit-scrollbar-thumb {
        background-color: rgba(155, 155, 155, 0.3);
        border-radius: 2px;
    }
</style>
