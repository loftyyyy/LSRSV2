{{-- Browse Items Modal --}}
<div id="browseItemsModal" class="hidden fixed inset-0 z-50 flex items-center justify-center px-2 py-6 bg-black/60 backdrop-blur-sm overflow-y-auto">
    <div class="w-full max-w-5xl bg-white dark:bg-neutral-950 border border-neutral-200 dark:border-neutral-800 rounded-3xl shadow-2xl flex flex-col max-h-[calc(100vh-3rem)] my-auto">
        <div class="flex-shrink-0 flex items-center justify-between px-6 py-4 border-b border-neutral-200 dark:border-neutral-800 bg-neutral-50/80 rounded-t-3xl dark:bg-neutral-900/50">
            <div>
                <p class="text-xs uppercase tracking-[0.2em] text-neutral-500 dark:text-neutral-500">Browse</p>
                <h3 class="text-lg font-semibold text-neutral-900 dark:text-white">Available Items</h3>
            </div>
            <div class="flex items-center gap-2">
                <a
                    href="{{ url('/inventories') }}"
                    class="inline-flex items-center gap-2 rounded-xl px-3.5 py-2 text-xs font-medium bg-violet-600 text-white hover:bg-violet-500 dark:text-black dark:hover:text-white shadow-sm shadow-violet-600/30 transition-colors duration-200"
                >
                    <x-icon name="plus" class="h-3.5 w-3.5" />
                    <span>Add Item</span>
                </a>
                <button
                    type="button"
                    onclick="closeBrowseItemsModal()"
                    class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-neutral-200 text-neutral-500 hover:text-neutral-800 hover:bg-white dark:border-neutral-700 dark:text-neutral-300 dark:hover:text-neutral-100 dark:hover:bg-neutral-800 transition-colors duration-200"
                >
                    &times;
                </button>
            </div>
        </div>

        <div class="flex-shrink-0 px-6 py-4 border-b border-neutral-200 dark:border-neutral-800">
            <div class="flex items-center gap-3 rounded-2xl bg-white px-4 py-2.5 border border-neutral-300 focus-within:border-neutral-500 dark:border-neutral-800 dark:bg-black/60 transition-colors duration-300 ease-in-out">
                <x-icon name="search" class="h-4 w-4 text-neutral-500 transition-colors duration-300 ease-in-out" />
                <input
                    type="text"
                    id="browseItemsSearchInput"
                    placeholder="Search items by name, SKU, type, color, or size..."
                    class="w-full bg-transparent text-xs text-neutral-700 placeholder:text-neutral-400 dark:text-neutral-100 dark:placeholder:text-neutral-500 focus:outline-none transition-colors duration-300 ease-in-out"
                />
            </div>
        </div>

        <div class="flex-1 overflow-y-auto px-6 py-4">
            <div id="browseItemsLoading" class="text-center py-12">
                <div class="animate-spin h-7 w-7 border-2 border-violet-600 border-t-transparent rounded-full mx-auto"></div>
                <p class="text-xs text-neutral-500 dark:text-neutral-400 mt-3">Loading items...</p>
            </div>

            <div id="browseItemsEmpty" class="hidden text-center py-12">
                <x-icon name="package" class="h-10 w-10 text-neutral-400 mx-auto mb-2" />
                <p class="text-sm text-neutral-500 dark:text-neutral-400">No items found</p>
            </div>

            <div id="browseItemsGrid" class="hidden grid grid-cols-1 md:grid-cols-2 gap-4">
                {{-- Browse items are populated dynamically --}}
            </div>
        </div>

        <div class="flex-shrink-0 px-6 py-4 border-t border-neutral-200 dark:border-neutral-800 bg-neutral-50/80 dark:bg-neutral-900/50 rounded-b-3xl">
            <button
                type="button"
                onclick="closeBrowseItemsModal()"
                class="w-full inline-flex items-center justify-center gap-2 rounded-xl px-4 py-2 text-[14px] font-medium border border-neutral-300 bg-white text-neutral-700 hover:bg-neutral-100 dark:border-neutral-800 dark:bg-neutral-950/80 dark:text-neutral-200 dark:hover:bg-neutral-900 transition-colors duration-100 ease-in-out"
            >
                Close
            </button>
        </div>
    </div>
</div>
