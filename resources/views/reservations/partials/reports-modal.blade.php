{{-- Reservation Reports Modal --}}
<div id="reservationReportsModal" class="hidden fixed inset-0 z-50 flex items-center justify-center px-2 py-6 bg-black/60 backdrop-blur-sm overflow-y-auto">
    <div class="w-full max-w-5xl bg-white dark:bg-neutral-950 border border-neutral-200 dark:border-neutral-800 rounded-3xl shadow-2xl flex flex-col max-h-[calc(100vh-3rem)] my-auto">
        <div class="flex-shrink-0 flex items-center justify-between px-6 py-4 border-b border-neutral-200 dark:border-neutral-800 bg-neutral-50/80 rounded-t-3xl dark:bg-neutral-900/50">
            <div>
                <p class="text-xs uppercase tracking-[0.2em] text-neutral-500 dark:text-neutral-500">Reports</p>
                <h3 class="text-lg font-semibold text-neutral-900 dark:text-white">Reservation Reports</h3>
            </div>
            <button type="button" onclick="closeReservationReportsModal()" class="text-neutral-500 hover:text-neutral-800 dark:hover:text-neutral-200 text-xl transition-colors duration-200">&times;</button>
        </div>

        <div class="flex-shrink-0 px-6 py-4 border-b border-neutral-200 dark:border-neutral-800">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
                <div class="space-y-1">
                    <label for="reportsStartDate" class="text-[11px] uppercase tracking-[0.12em] text-neutral-500 dark:text-neutral-400">Start Date</label>
                    <input id="reportsStartDate" type="date" class="w-full rounded-xl border border-neutral-300 bg-white px-3 py-2 text-xs text-neutral-700 dark:border-neutral-800 dark:bg-black/60 dark:text-neutral-100 focus:outline-none focus:border-neutral-500 transition-colors duration-200" />
                </div>
                <div class="space-y-1">
                    <label for="reportsEndDate" class="text-[11px] uppercase tracking-[0.12em] text-neutral-500 dark:text-neutral-400">End Date</label>
                    <input id="reportsEndDate" type="date" class="w-full rounded-xl border border-neutral-300 bg-white px-3 py-2 text-xs text-neutral-700 dark:border-neutral-800 dark:bg-black/60 dark:text-neutral-100 focus:outline-none focus:border-neutral-500 transition-colors duration-200" />
                </div>
                <button type="button" onclick="applyReservationReportsFilters()" class="inline-flex items-center justify-center gap-2 self-end rounded-xl px-4 py-2 text-[13px] font-medium border border-neutral-300 bg-white text-neutral-700 hover:bg-neutral-100 dark:border-neutral-800 dark:bg-neutral-950/80 dark:text-neutral-200 dark:hover:bg-neutral-900 transition-colors duration-200">
                    <x-icon name="search" class="h-3.5 w-3.5" />
                    <span>Apply Filters</span>
                </button>
                <a id="reservationReportsExportBtn" href="/api/reservations/reports/pdf" target="_blank" class="inline-flex items-center justify-center gap-2 self-end rounded-xl px-4 py-2 text-[13px] font-medium bg-violet-600 text-white hover:bg-violet-500 dark:text-black dark:hover:text-white transition-colors duration-200">
                    <x-icon name="download" class="h-3.5 w-3.5" />
                    <span>Export PDF</span>
                </a>
            </div>
        </div>

        <div id="reservationReportsContent" class="flex-1 overflow-y-auto px-6 py-4">
            <div id="reservationReportsLoading" class="text-center py-14">
                <div class="animate-spin h-7 w-7 border-2 border-violet-600 border-t-transparent rounded-full mx-auto"></div>
                <p class="text-xs text-neutral-500 dark:text-neutral-400 mt-3">Loading report data...</p>
            </div>

            <div id="reservationReportsData" class="hidden space-y-4">
                <section class="grid grid-cols-2 md:grid-cols-4 gap-3">
                    <div class="rounded-2xl p-4 border border-neutral-200 bg-white dark:border-neutral-800 dark:bg-neutral-900/30">
                        <p class="text-[11px] uppercase tracking-[0.12em] text-neutral-500 dark:text-neutral-400">Total</p>
                        <p id="reportsTotalReservations" class="mt-1 text-2xl font-semibold text-neutral-900 dark:text-white">0</p>
                    </div>
                    <div class="rounded-2xl p-4 border border-neutral-200 bg-white dark:border-neutral-800 dark:bg-neutral-900/30">
                        <p class="text-[11px] uppercase tracking-[0.12em] text-neutral-500 dark:text-neutral-400">Items Reserved</p>
                        <p id="reportsTotalItems" class="mt-1 text-2xl font-semibold text-emerald-600 dark:text-emerald-400">0</p>
                    </div>
                    <div class="rounded-2xl p-4 border border-neutral-200 bg-white dark:border-neutral-800 dark:bg-neutral-900/30">
                        <p class="text-[11px] uppercase tracking-[0.12em] text-neutral-500 dark:text-neutral-400">Revenue</p>
                        <p id="reportsTotalRevenue" class="mt-1 text-2xl font-semibold text-violet-600 dark:text-violet-400 font-geist-mono">₱0</p>
                    </div>
                    <div class="rounded-2xl p-4 border border-neutral-200 bg-white dark:border-neutral-800 dark:bg-neutral-900/30">
                        <p class="text-[11px] uppercase tracking-[0.12em] text-neutral-500 dark:text-neutral-400">Avg Items / Reservation</p>
                        <p id="reportsAverageItems" class="mt-1 text-2xl font-semibold text-amber-600 dark:text-amber-400">0</p>
                    </div>
                </section>

                <section class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="rounded-2xl border border-neutral-200 bg-white dark:border-neutral-800 dark:bg-neutral-900/30 p-4">
                        <p class="text-xs font-medium text-neutral-700 dark:text-neutral-200 mb-3">By Status</p>
                        <div id="reportsByStatus" class="space-y-2 text-xs text-neutral-600 dark:text-neutral-300"></div>
                    </div>
                    <div class="rounded-2xl border border-neutral-200 bg-white dark:border-neutral-800 dark:bg-neutral-900/30 p-4">
                        <p class="text-xs font-medium text-neutral-700 dark:text-neutral-200 mb-3">By Month</p>
                        <div id="reportsByMonth" class="space-y-2 text-xs text-neutral-600 dark:text-neutral-300"></div>
                    </div>
                    <div class="rounded-2xl border border-neutral-200 bg-white dark:border-neutral-800 dark:bg-neutral-900/30 p-4">
                        <p class="text-xs font-medium text-neutral-700 dark:text-neutral-200 mb-3">By Clerk</p>
                        <div id="reportsByClerk" class="space-y-2 text-xs text-neutral-600 dark:text-neutral-300"></div>
                    </div>
                </section>
            </div>
        </div>

        <div class="flex-shrink-0 px-6 py-4 border-t border-neutral-200 dark:border-neutral-800 bg-neutral-50/80 dark:bg-neutral-900/50 rounded-b-3xl">
            <button type="button" onclick="closeReservationReportsModal()" class="w-full inline-flex items-center justify-center gap-2 rounded-xl px-4 py-2 text-[14px] font-medium border border-neutral-300 bg-white text-neutral-700 hover:bg-neutral-100 dark:border-neutral-800 dark:bg-neutral-950/80 dark:text-neutral-200 dark:hover:bg-neutral-900 transition-colors duration-100 ease-in-out">
                Close
            </button>
        </div>
    </div>
</div>
