{{-- Edit Reservation Modal --}}
<div id="editReservationModal" class="hidden fixed inset-0 z-[70] flex items-center justify-center px-2 py-6 bg-black/60 backdrop-blur-sm overflow-y-auto">
    <div class="w-full max-w-2xl bg-white dark:bg-neutral-950 border border-neutral-200 dark:border-neutral-800 rounded-3xl shadow-2xl flex flex-col max-h-[calc(100vh-3rem)] my-auto">
        <div class="flex-shrink-0 flex items-center justify-between px-6 py-4 border-b border-neutral-200 dark:border-neutral-800 bg-neutral-50/80 rounded-t-3xl dark:bg-neutral-900/50">
            <div>
                <p class="text-xs uppercase tracking-[0.2em] text-neutral-500 dark:text-neutral-500">Edit Booking</p>
                <h3 class="text-lg font-semibold text-neutral-900 dark:text-white">Update Reservation</h3>
            </div>
            <button type="button" onclick="closeEditReservationModal()" class="text-neutral-500 hover:text-neutral-800 dark:hover:text-neutral-200 text-xl transition-colors duration-200">&times;</button>
        </div>

        <form id="editReservationForm" class="flex-1 overflow-y-auto px-6 py-5 space-y-4">
            @csrf
            <input type="hidden" id="editReservationId" value="">

            <div class="space-y-2">
                <label for="editReservationCustomer" class="text-sm font-medium text-neutral-700 dark:text-neutral-300">Customer *</label>
                <div class="flex items-center rounded-2xl bg-white px-3 py-2.5 border border-neutral-300 focus-within:border-neutral-500 dark:border-neutral-800 dark:bg-black/60 transition-colors duration-300 ease-in-out">
                    <x-icon name="user" class="h-4 w-4 text-neutral-500 mr-2" />
                    <select id="editReservationCustomer" name="customer_id" required class="w-full bg-transparent text-xs text-neutral-700 dark:text-neutral-100 focus:outline-none">
                        <option value="">Select customer</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="space-y-2">
                    <label for="editReservationStartDate" class="text-sm font-medium text-neutral-700 dark:text-neutral-300">Pickup Date *</label>
                    <div class="flex items-center rounded-2xl bg-white px-3 py-2.5 border border-neutral-300 focus-within:border-neutral-500 dark:border-neutral-800 dark:bg-black/60 transition-colors duration-300 ease-in-out">
                        <x-icon name="calendar" class="h-4 w-4 text-neutral-500 mr-2" />
                        <input type="date" id="editReservationStartDate" name="start_date" required class="w-full bg-transparent text-xs text-neutral-700 dark:text-neutral-100 focus:outline-none" />
                    </div>
                </div>
                <div class="space-y-2">
                    <label for="editReservationEndDate" class="text-sm font-medium text-neutral-700 dark:text-neutral-300">Return Date *</label>
                    <div class="flex items-center rounded-2xl bg-white px-3 py-2.5 border border-neutral-300 focus-within:border-neutral-500 dark:border-neutral-800 dark:bg-black/60 transition-colors duration-300 ease-in-out">
                        <x-icon name="calendar" class="h-4 w-4 text-neutral-500 mr-2" />
                        <input type="date" id="editReservationEndDate" name="end_date" required class="w-full bg-transparent text-xs text-neutral-700 dark:text-neutral-100 focus:outline-none" />
                    </div>
                </div>
            </div>

            <div class="space-y-2">
                <label for="editReservationPassword" class="text-sm font-medium text-neutral-700 dark:text-neutral-300">Password Verification *</label>
                <div class="flex items-center rounded-2xl bg-white px-3 py-2.5 border border-neutral-300 focus-within:border-neutral-500 dark:border-neutral-800 dark:bg-black/60 transition-colors duration-300 ease-in-out">
                    <x-icon name="lock" class="h-4 w-4 text-neutral-500 mr-2" />
                    <input type="password" id="editReservationPassword" autocomplete="current-password" placeholder="Enter your password to confirm changes" required class="w-full bg-transparent text-xs text-neutral-700 placeholder:text-neutral-400 dark:text-neutral-100 dark:placeholder:text-neutral-500 focus:outline-none" />
                </div>
                <p class="text-[11px] text-neutral-500 dark:text-neutral-400">For security, reservation updates require your account password.</p>
            </div>

            <div id="editReservationError" class="hidden bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl p-3 flex items-center gap-2">
                <x-icon name="alert-circle" class="h-4 w-4 text-red-500" />
                <p class="text-xs text-red-600 dark:text-red-400"></p>
            </div>

            <div id="editReservationSuccess" class="hidden bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800 rounded-xl p-3 flex items-center gap-2">
                <x-icon name="check-circle" class="h-4 w-4 text-emerald-500" />
                <p class="text-xs text-emerald-600 dark:text-emerald-400"></p>
            </div>

            <div class="flex items-center justify-end gap-3 pt-1">
                <button type="submit" id="submitEditReservationBtn" class="inline-flex items-center gap-2 rounded-xl px-4 py-2 text-[14px] font-medium bg-violet-600 text-white dark:text-black hover:text-black dark:hover:text-white hover:bg-violet-500 transition-colors duration-100 ease-in-out disabled:opacity-50 disabled:cursor-not-allowed">
                    <span id="submitEditReservationBtnText">Save Changes</span>
                    <span id="submitEditReservationBtnLoading" class="hidden">Saving...</span>
                </button>
                <button type="button" onclick="closeEditReservationModal()" class="inline-flex items-center gap-2 rounded-xl px-3.5 py-2 text-[14px] font-medium border border-neutral-300 bg-white text-neutral-700 dark:border-neutral-800 dark:bg-neutral-950/80 dark:text-neutral-200 hover:bg-neutral-100 dark:hover:bg-neutral-900 transition-colors duration-100 ease-in-out">Cancel</button>
            </div>
        </form>
    </div>
</div>
