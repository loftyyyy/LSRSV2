{{-- Bulk Extend Modal --}}
<div id="bulkExtendModal" class="hidden fixed inset-0 z-50 items-center justify-center bg-black/60 backdrop-blur-sm">
    <div class="relative w-full max-w-md mx-4 bg-white dark:bg-neutral-950 rounded-2xl shadow-2xl border border-neutral-200 dark:border-neutral-800">
        {{-- Header --}}
        <div class="flex items-center justify-between px-6 py-4 border-b border-neutral-200 dark:border-neutral-800">
            <div class="flex items-center gap-3">
                <div class="flex items-center justify-center w-10 h-10 rounded-xl bg-amber-100 dark:bg-amber-900/30">
                    <x-icon name="calendar-plus" class="w-5 h-5 text-amber-600 dark:text-amber-400" />
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-neutral-900 dark:text-white">Bulk Extend Rentals</h3>
                    <p class="text-xs text-neutral-500 dark:text-neutral-400">
                        Extending <span id="bulkExtendCount" class="font-semibold text-amber-600 dark:text-amber-400">0</span> rental(s)
                    </p>
                </div>
            </div>
            <button type="button" onclick="closeBulkExtendModal()" class="p-2 rounded-lg text-neutral-400 hover:text-neutral-600 dark:hover:text-neutral-200 hover:bg-neutral-100 dark:hover:bg-neutral-800 transition-colors">
                <x-icon name="x" class="w-5 h-5" />
            </button>
        </div>

        {{-- Body --}}
        <div class="px-6 py-5 space-y-4">
            <div>
                <label for="bulkExtendDays" class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-1.5">
                    Extension Days <span class="text-rose-500">*</span>
                </label>
                <input type="number" id="bulkExtendDays" min="1" max="30" value="3" placeholder="Number of days"
                    class="w-full rounded-xl border border-neutral-300 bg-white px-4 py-2.5 text-sm text-neutral-900 focus:border-amber-500 focus:ring-1 focus:ring-amber-500 focus:outline-none dark:border-neutral-700 dark:bg-neutral-900 dark:text-white transition-colors" />
                <p class="mt-1 text-xs text-neutral-500 dark:text-neutral-400">Maximum 30 days per extension</p>
            </div>

            <div>
                <label for="bulkExtendReason" class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-1.5">
                    Reason <span class="text-rose-500">*</span>
                </label>
                <textarea id="bulkExtendReason" rows="3" placeholder="Enter reason for extension..."
                    class="w-full rounded-xl border border-neutral-300 bg-white px-4 py-2.5 text-sm text-neutral-900 focus:border-amber-500 focus:ring-1 focus:ring-amber-500 focus:outline-none dark:border-neutral-700 dark:bg-neutral-900 dark:text-white transition-colors resize-none"></textarea>
            </div>

            <div class="p-3 rounded-xl bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800/50">
                <div class="flex items-start gap-2">
                    <x-icon name="info" class="w-4 h-4 text-amber-600 dark:text-amber-400 mt-0.5 flex-shrink-0" />
                    <p class="text-xs text-amber-700 dark:text-amber-300">
                        All selected rentals will have their due date extended by the specified number of days. Extensions are tracked in the rental history.
                    </p>
                </div>
            </div>
        </div>

        {{-- Footer --}}
        <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-neutral-200 dark:border-neutral-800 bg-neutral-50 dark:bg-neutral-900/50 rounded-b-2xl">
            <button type="button" onclick="closeBulkExtendModal()"
                class="px-4 py-2 text-sm font-medium text-neutral-700 dark:text-neutral-300 hover:bg-neutral-100 dark:hover:bg-neutral-800 rounded-lg transition-colors">
                Cancel
            </button>
            <button type="button" id="bulkExtendSubmitBtn" onclick="processBulkExtend()"
                class="px-4 py-2 text-sm font-medium text-white bg-amber-600 hover:bg-amber-700 rounded-lg transition-colors">
                Extend Rentals
            </button>
        </div>
    </div>
</div>

{{-- Bulk Return Modal --}}
<div id="bulkReturnModal" class="hidden fixed inset-0 z-50 items-center justify-center bg-black/60 backdrop-blur-sm">
    <div class="relative w-full max-w-md mx-4 bg-white dark:bg-neutral-950 rounded-2xl shadow-2xl border border-neutral-200 dark:border-neutral-800">
        {{-- Header --}}
        <div class="flex items-center justify-between px-6 py-4 border-b border-neutral-200 dark:border-neutral-800">
            <div class="flex items-center gap-3">
                <div class="flex items-center justify-center w-10 h-10 rounded-xl bg-emerald-100 dark:bg-emerald-900/30">
                    <x-icon name="arrow-left-circle" class="w-5 h-5 text-emerald-600 dark:text-emerald-400" />
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-neutral-900 dark:text-white">Bulk Process Returns</h3>
                    <p class="text-xs text-neutral-500 dark:text-neutral-400">
                        Processing <span id="bulkReturnCount" class="font-semibold text-emerald-600 dark:text-emerald-400">0</span> rental(s)
                    </p>
                </div>
            </div>
            <button type="button" onclick="closeBulkReturnModal()" class="p-2 rounded-lg text-neutral-400 hover:text-neutral-600 dark:hover:text-neutral-200 hover:bg-neutral-100 dark:hover:bg-neutral-800 transition-colors">
                <x-icon name="x" class="w-5 h-5" />
            </button>
        </div>

        {{-- Body --}}
        <div class="px-6 py-5 space-y-4">
            <div>
                <label for="bulkReturnDate" class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-1.5">
                    Return Date <span class="text-rose-500">*</span>
                </label>
                <input type="date" id="bulkReturnDate"
                    class="w-full rounded-xl border border-neutral-300 bg-white px-4 py-2.5 text-sm text-neutral-900 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 focus:outline-none dark:border-neutral-700 dark:bg-neutral-900 dark:text-white transition-colors" />
            </div>

            <div>
                <label for="bulkReturnNotes" class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-1.5">
                    Notes <span class="text-neutral-400">(optional)</span>
                </label>
                <textarea id="bulkReturnNotes" rows="3" placeholder="Enter any notes about the returns..."
                    class="w-full rounded-xl border border-neutral-300 bg-white px-4 py-2.5 text-sm text-neutral-900 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 focus:outline-none dark:border-neutral-700 dark:bg-neutral-900 dark:text-white transition-colors resize-none"></textarea>
            </div>

            <div class="p-3 rounded-xl bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800/50">
                <div class="flex items-start gap-2">
                    <x-icon name="alert-triangle" class="w-4 h-4 text-amber-600 dark:text-amber-400 mt-0.5 flex-shrink-0" />
                    <div class="text-xs text-amber-700 dark:text-amber-300">
                        <p class="font-medium mb-1">Important Notes:</p>
                        <ul class="list-disc list-inside space-y-0.5">
                            <li>Penalties will be calculated for overdue items</li>
                            <li>Deposit handling must be done individually</li>
                            <li>Inventory status will be updated automatically</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        {{-- Footer --}}
        <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-neutral-200 dark:border-neutral-800 bg-neutral-50 dark:bg-neutral-900/50 rounded-b-2xl">
            <button type="button" onclick="closeBulkReturnModal()"
                class="px-4 py-2 text-sm font-medium text-neutral-700 dark:text-neutral-300 hover:bg-neutral-100 dark:hover:bg-neutral-800 rounded-lg transition-colors">
                Cancel
            </button>
            <button type="button" id="bulkReturnSubmitBtn" onclick="processBulkReturn()"
                class="px-4 py-2 text-sm font-medium text-white bg-emerald-600 hover:bg-emerald-700 rounded-lg transition-colors">
                Process Returns
            </button>
        </div>
    </div>
</div>
