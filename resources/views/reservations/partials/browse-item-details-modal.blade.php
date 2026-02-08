{{-- Browse Item Details Modal --}}
<div id="browseItemDetailsModal" class="hidden fixed inset-0 z-[60] flex items-center justify-center px-2 py-6 bg-black/60 backdrop-blur-sm overflow-y-auto">
    <div class="w-full max-w-5xl bg-white dark:bg-neutral-950 border border-neutral-200 dark:border-neutral-800 rounded-3xl shadow-2xl flex flex-col max-h-[calc(100vh-3rem)] my-auto">
        <div class="flex-shrink-0 flex items-center justify-between px-6 py-4 border-b border-neutral-200 dark:border-neutral-800 bg-neutral-50/80 rounded-t-3xl dark:bg-neutral-900/50">
            <div>
                <p class="text-xs uppercase tracking-[0.2em] text-neutral-500 dark:text-neutral-500">Item Details</p>
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

            <div id="browseItemDetailsError" class="hidden p-6">
                <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl p-4 flex items-center gap-3">
                    <x-icon name="alert-circle" class="h-5 w-5 text-red-500 flex-shrink-0" />
                    <p id="browseItemDetailsErrorMessage" class="text-sm text-red-600 dark:text-red-400">Failed to load item details</p>
                </div>
            </div>

            <div id="browseItemDetailsData" class="hidden">
                <div class="flex flex-col lg:flex-row">
                    <div class="lg:w-1/2 p-6 border-b lg:border-b-0 lg:border-r border-neutral-200 dark:border-neutral-800">
                        <div id="browseItemDetailsMainImage" class="aspect-square bg-neutral-100 dark:bg-neutral-900 rounded-2xl overflow-hidden flex items-center justify-center border border-neutral-200 dark:border-neutral-800">
                            <div class="text-neutral-400 dark:text-neutral-600 flex flex-col items-center gap-2">
                                <x-icon name="image" class="h-12 w-12" />
                                <span class="text-sm">No photos available</span>
                            </div>
                        </div>
                        <div id="browseItemDetailsThumbnails" class="mt-4 flex gap-2 overflow-x-auto pb-2"></div>
                    </div>

                    <div class="lg:w-1/2 p-6 space-y-5">
                        <div class="rounded-xl p-4 border" id="browseItemDetailStatusCard">
                            <div class="flex items-center gap-2">
                                <span id="browseItemDetailStatusDot" class="h-2 w-2 rounded-full"></span>
                                <span id="browseItemDetailStatusText" class="text-sm font-semibold">-</span>
                            </div>
                            <p id="browseItemDetailStatusSubtitle" class="text-xs text-neutral-500 dark:text-neutral-400 mt-1">-</p>
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div class="rounded-xl p-3 border border-neutral-200 dark:border-neutral-800">
                                <p class="text-xs text-neutral-500 dark:text-neutral-400">SKU</p>
                                <p id="browseItemDetailSku" class="mt-1 text-sm font-medium text-neutral-900 dark:text-white">-</p>
                            </div>
                            <div class="rounded-xl p-3 border border-neutral-200 dark:border-neutral-800">
                                <p class="text-xs text-neutral-500 dark:text-neutral-400">Type</p>
                                <p id="browseItemDetailType" class="mt-1 text-sm font-medium text-neutral-900 dark:text-white">-</p>
                            </div>
                            <div class="rounded-xl p-3 border border-neutral-200 dark:border-neutral-800">
                                <p class="text-xs text-neutral-500 dark:text-neutral-400">Size</p>
                                <p id="browseItemDetailSize" class="mt-1 text-sm font-medium text-neutral-900 dark:text-white">-</p>
                            </div>
                            <div class="rounded-xl p-3 border border-neutral-200 dark:border-neutral-800">
                                <p class="text-xs text-neutral-500 dark:text-neutral-400">Color</p>
                                <p id="browseItemDetailColor" class="mt-1 text-sm font-medium text-neutral-900 dark:text-white">-</p>
                            </div>
                        </div>

                        <div class="rounded-xl p-4 border border-neutral-200 dark:border-neutral-800">
                            <p class="text-xs uppercase tracking-[0.12em] text-neutral-500 dark:text-neutral-400">Pricing</p>
                            <div class="mt-3 space-y-2">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs text-neutral-500 dark:text-neutral-400">Rental price</span>
                                    <span id="browseItemDetailRentalPrice" class="text-sm font-semibold text-violet-600 dark:text-violet-400 font-geist-mono">-</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-xs text-neutral-500 dark:text-neutral-400">Deposit</span>
                                    <span id="browseItemDetailDeposit" class="text-sm font-medium text-neutral-900 dark:text-white font-geist-mono">-</span>
                                </div>
                                <div id="browseItemDetailSellingPriceRow" class="hidden flex items-center justify-between">
                                    <span class="text-xs text-neutral-500 dark:text-neutral-400">Selling price</span>
                                    <span id="browseItemDetailSellingPrice" class="text-sm font-medium text-neutral-900 dark:text-white font-geist-mono">-</span>
                                </div>
                            </div>
                        </div>

                        <div class="rounded-xl p-4 border border-neutral-200 dark:border-neutral-800">
                            <p class="text-xs uppercase tracking-[0.12em] text-neutral-500 dark:text-neutral-400">Additional Details</p>
                            <div class="mt-3 space-y-2 text-xs">
                                <p><span class="text-neutral-500 dark:text-neutral-400">Design:</span> <span id="browseItemDetailDesign" class="text-neutral-700 dark:text-neutral-200">-</span></p>
                                <p><span class="text-neutral-500 dark:text-neutral-400">Added:</span> <span id="browseItemDetailCreated" class="text-neutral-700 dark:text-neutral-200">-</span></p>
                                <p><span class="text-neutral-500 dark:text-neutral-400">Updated:</span> <span id="browseItemDetailUpdated" class="text-neutral-700 dark:text-neutral-200">-</span></p>
                                <p id="browseItemDetailUpdatedByRow" class="hidden"><span class="text-neutral-500 dark:text-neutral-400">Updated by:</span> <span id="browseItemDetailUpdatedBy" class="text-neutral-700 dark:text-neutral-200">-</span></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex-shrink-0 px-6 py-4 border-t border-neutral-200 dark:border-neutral-800 bg-neutral-50/80 dark:bg-neutral-900/50 rounded-b-3xl">
            <button
                type="button"
                onclick="closeBrowseItemDetailsModal()"
                class="w-full inline-flex items-center justify-center gap-2 rounded-xl px-4 py-2 text-[14px] font-medium border border-neutral-300 bg-white text-neutral-700 hover:bg-neutral-100 dark:border-neutral-800 dark:bg-neutral-950/80 dark:text-neutral-200 dark:hover:bg-neutral-900 transition-colors duration-100 ease-in-out"
            >
                Back to Browse
            </button>
        </div>
    </div>
</div>
