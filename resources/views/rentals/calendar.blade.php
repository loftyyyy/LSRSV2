@extends('main')

@section('title', 'Rental Calendar · Love &amp; Styles')

@section('head_scripts')
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js"></script>
@endsection

@section('body_classes', 'min-h-screen flex font-geist bg-neutral-100 text-neutral-900 dark:bg-black dark:text-neutral-50 transition-colors duration-300 ease-in-out')

@section('main_classes', 'flex-1 ml-64 flex flex-col px-10 py-8 overflow-x-hidden overflow-y-auto scrollbar-hide bg-gradient-to-b from-neutral-100 via-neutral-100 to-neutral-200 dark:from-black dark:via-black dark:to-neutral-950 transition-colors duration-300 ease-in-out')

@section('styles')
<style>
        .scrollbar-hide {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
        .scrollbar-hide::-webkit-scrollbar {
            display: none;
        }

        /* FullCalendar Theme Overrides - Light Mode */
        .fc {
            --fc-border-color: rgb(229 229 229);
            --fc-button-bg-color: rgb(245 245 245);
            --fc-button-border-color: rgb(212 212 212);
            --fc-button-text-color: rgb(64 64 64);
            --fc-button-hover-bg-color: rgb(229 229 229);
            --fc-button-hover-border-color: rgb(163 163 163);
            --fc-button-active-bg-color: rgb(139 92 246);
            --fc-button-active-border-color: rgb(124 58 237);
            --fc-today-bg-color: rgba(139, 92, 246, 0.08);
            --fc-page-bg-color: transparent;
            --fc-neutral-bg-color: rgb(250 250 250);
            --fc-event-border-color: transparent;
            font-family: 'Geist', system-ui, sans-serif;
        }

        /* Dark Mode Overrides */
        .dark .fc {
            --fc-border-color: rgb(38 38 38);
            --fc-button-bg-color: rgb(23 23 23);
            --fc-button-border-color: rgb(64 64 64);
            --fc-button-text-color: rgb(229 229 229);
            --fc-button-hover-bg-color: rgb(38 38 38);
            --fc-button-hover-border-color: rgb(82 82 82);
            --fc-button-active-bg-color: rgb(139 92 246);
            --fc-button-active-border-color: rgb(124 58 237);
            --fc-today-bg-color: rgba(139, 92, 246, 0.15);
            --fc-page-bg-color: transparent;
            --fc-neutral-bg-color: rgb(23 23 23);
        }

        .fc .fc-toolbar-title {
            font-size: 1.25rem;
            font-weight: 600;
        }

        .dark .fc .fc-toolbar-title {
            color: white;
        }

        .fc .fc-col-header-cell-cushion,
        .fc .fc-daygrid-day-number {
            color: rgb(115 115 115);
            font-size: 0.75rem;
            font-weight: 500;
        }

        .dark .fc .fc-col-header-cell-cushion,
        .dark .fc .fc-daygrid-day-number {
            color: rgb(163 163 163);
        }

        .fc .fc-daygrid-day.fc-day-today .fc-daygrid-day-number {
            color: rgb(139 92 246);
            font-weight: 600;
        }

        .fc .fc-button {
            border-radius: 0.5rem;
            font-size: 0.75rem;
            font-weight: 500;
            padding: 0.5rem 1rem;
            text-transform: capitalize;
        }

        .fc .fc-button-primary:not(:disabled).fc-button-active {
            color: white;
        }

        .dark .fc .fc-button-primary:not(:disabled).fc-button-active {
            color: black;
        }

        /* Event Styling */
        .fc-event {
            border-radius: 0.375rem;
            padding: 2px 6px;
            font-size: 0.7rem;
            font-weight: 500;
            cursor: pointer;
            border: none !important;
        }

        .fc-event-due {
            background-color: rgb(56 189 248) !important;
            color: rgb(7 89 133) !important;
        }

        .fc-event-overdue {
            background-color: rgb(251 113 133) !important;
            color: rgb(136 19 55) !important;
        }

        .fc-event-returned {
            background-color: rgb(74 222 128) !important;
            color: rgb(20 83 45) !important;
        }

        .fc-event-released {
            background-color: rgb(251 191 36) !important;
            color: rgb(120 53 15) !important;
        }

        .dark .fc-event-due {
            background-color: rgba(56, 189, 248, 0.2) !important;
            color: rgb(125 211 252) !important;
        }

        .dark .fc-event-overdue {
            background-color: rgba(251, 113, 133, 0.2) !important;
            color: rgb(253 164 175) !important;
        }

        .dark .fc-event-returned {
            background-color: rgba(74, 222, 128, 0.2) !important;
            color: rgb(134 239 172) !important;
        }

        .dark .fc-event-released {
            background-color: rgba(251, 191, 36, 0.2) !important;
            color: rgb(253 224 71) !important;
        }

        /* Day cell hover */
        .fc .fc-daygrid-day:hover {
            background-color: rgba(139, 92, 246, 0.04);
        }

        .dark .fc .fc-daygrid-day:hover {
            background-color: rgba(139, 92, 246, 0.08);
        }

        /* More events popover */
        .fc .fc-more-popover {
            border-radius: 0.75rem;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
        }

        .dark .fc .fc-more-popover {
            background-color: rgb(23 23 23);
            border-color: rgb(38 38 38);
        }

        .dark .fc .fc-more-popover .fc-popover-header {
            background-color: rgb(38 38 38);
            color: white;
        }
    </style>
@endsection

@section('content')
{{-- Page Header --}}
        <header class="mb-8">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-semibold tracking-tight text-neutral-900 dark:text-white transition-colors duration-300 ease-in-out">
                        Rental Calendar
                    </h1>
                    <p class="mt-1 text-sm font-geist-mono text-neutral-500 dark:text-neutral-400 transition-colors duration-300 ease-in-out">
                        View rental due dates and returns at a glance
                    </p>
                </div>

                <div class="flex items-center gap-3 text-xs">
                    <a href="/rentals" class="inline-flex items-center gap-2 rounded-xl px-3.5 py-2 text-[14px] font-medium border dark:hover:text-black hover:text-white border-neutral-300 bg-white text-neutral-700 hover:bg-violet-600 dark:border-neutral-800 dark:bg-neutral-950/80 dark:text-neutral-200 transition-colors duration-300 ease-in-out">
                        <x-icon name="arrow-left" class="h-4 w-4" />
                        <span>Back to Rentals</span>
                    </a>

                    <a href="/rentals/reports" class="inline-flex items-center gap-2 rounded-xl border border-neutral-300 bg-white px-3.5 py-2 text-neutral-700 hover:bg-violet-600 dark:hover:text-black hover:text-white dark:border-neutral-800 dark:bg-neutral-950/80 dark:text-neutral-200 transition-colors duration-300 ease-in-out">
                        <span class="inline-flex h-5 w-5 items-center justify-center rounded-md">
                            <x-icon name="chart-column" class="h-4 w-4" />
                        </span>
                        <span class="text-[14px] font-medium tracking-wide">Reports</span>
                    </a>
                </div>
            </div>
        </header>

        {{-- Legend --}}
        <section class="mb-6">
            <div class="flex flex-wrap items-center gap-6 text-sm">
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-sky-400"></span>
                    <span class="text-neutral-600 dark:text-neutral-400">Due Today/Upcoming</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-rose-400"></span>
                    <span class="text-neutral-600 dark:text-neutral-400">Overdue</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-emerald-400"></span>
                    <span class="text-neutral-600 dark:text-neutral-400">Returned</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-amber-400"></span>
                    <span class="text-neutral-600 dark:text-neutral-400">Released (Start)</span>
                </div>
            </div>
        </section>

        {{-- Quick Stats --}}
        <section class="grid grid-cols-4 gap-4 mb-8">
            <div class="rounded-xl p-4 border border-neutral-200 bg-white dark:border-neutral-800 dark:bg-neutral-950/60 shadow-sm transition-colors duration-300 ease-in-out">
                <div class="text-xs text-neutral-500 dark:text-neutral-400 mb-1">Due Today</div>
                <div id="dueTodayCount" class="text-2xl font-semibold text-sky-600 dark:text-sky-400">0</div>
            </div>
            <div class="rounded-xl p-4 border border-neutral-200 bg-white dark:border-neutral-800 dark:bg-neutral-950/60 shadow-sm transition-colors duration-300 ease-in-out">
                <div class="text-xs text-neutral-500 dark:text-neutral-400 mb-1">Due This Week</div>
                <div id="dueThisWeekCount" class="text-2xl font-semibold text-violet-600 dark:text-violet-400">0</div>
            </div>
            <div class="rounded-xl p-4 border border-neutral-200 bg-white dark:border-neutral-800 dark:bg-neutral-950/60 shadow-sm transition-colors duration-300 ease-in-out">
                <div class="text-xs text-neutral-500 dark:text-neutral-400 mb-1">Currently Overdue</div>
                <div id="currentlyOverdueCount" class="text-2xl font-semibold text-rose-600 dark:text-rose-400">0</div>
            </div>
            <div class="rounded-xl p-4 border border-neutral-200 bg-white dark:border-neutral-800 dark:bg-neutral-950/60 shadow-sm transition-colors duration-300 ease-in-out">
                <div class="text-xs text-neutral-500 dark:text-neutral-400 mb-1">Active Rentals</div>
                <div id="activeRentalsCount" class="text-2xl font-semibold text-neutral-900 dark:text-white">0</div>
            </div>
        </section>

        {{-- Calendar Container --}}
        <section class="flex-1">
            <div class="rounded-2xl border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-800 dark:bg-neutral-950/60 transition-colors duration-300 ease-in-out">
                <div id="rental-calendar" class="min-h-[600px]"></div>
            </div>
        </section>
@endsection

@section('scripts')
{{-- Rental Event Detail Modal --}}
    <div id="eventDetailModal" class="hidden fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            {{-- Background overlay --}}
            <div class="fixed inset-0 transition-opacity bg-neutral-900/60 dark:bg-black/80" onclick="closeEventModal()"></div>

            {{-- Modal panel --}}
            <div class="inline-block w-full max-w-md my-8 overflow-hidden text-left align-middle transition-all transform bg-white dark:bg-neutral-950 rounded-2xl shadow-xl border border-neutral-200 dark:border-neutral-800">
                {{-- Header --}}
                <div class="flex items-center justify-between px-6 py-4 border-b border-neutral-200 dark:border-neutral-800">
                    <div class="flex items-center gap-3">
                        <div id="modalStatusIcon" class="flex items-center justify-center w-10 h-10 rounded-xl bg-violet-100 dark:bg-violet-900/30">
                            <x-icon name="calendar" class="h-5 w-5 text-violet-600 dark:text-violet-400" />
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-neutral-900 dark:text-white">Rental Details</h3>
                            <p id="modalRentalId" class="text-xs font-geist-mono text-neutral-500 dark:text-neutral-400">#---</p>
                        </div>
                    </div>
                    <button onclick="closeEventModal()" class="p-2 rounded-lg text-neutral-400 hover:text-neutral-600 dark:hover:text-neutral-200 hover:bg-neutral-100 dark:hover:bg-neutral-800 transition-colors">
                        <x-icon name="x" class="h-5 w-5" />
                    </button>
                </div>

                {{-- Content --}}
                <div class="px-6 py-4 space-y-4">
                    {{-- Customer Info --}}
                    <div class="flex items-start gap-3">
                        <div class="flex items-center justify-center w-8 h-8 rounded-lg bg-neutral-100 dark:bg-neutral-800">
                            <x-icon name="user" class="h-4 w-4 text-neutral-500" />
                        </div>
                        <div class="flex-1">
                            <p class="text-xs text-neutral-500 dark:text-neutral-400">Customer</p>
                            <p id="modalCustomerName" class="text-sm font-medium text-neutral-900 dark:text-white">---</p>
                        </div>
                    </div>

                    {{-- Item Info --}}
                    <div class="flex items-start gap-3">
                        <div class="flex items-center justify-center w-8 h-8 rounded-lg bg-neutral-100 dark:bg-neutral-800">
                            <x-icon name="package" class="h-4 w-4 text-neutral-500" />
                        </div>
                        <div class="flex-1">
                            <p class="text-xs text-neutral-500 dark:text-neutral-400">Item</p>
                            <p id="modalItemName" class="text-sm font-medium text-neutral-900 dark:text-white">---</p>
                            <p id="modalItemCode" class="text-xs font-geist-mono text-neutral-400">---</p>
                        </div>
                    </div>

                    {{-- Dates --}}
                    <div class="grid grid-cols-2 gap-4">
                        <div class="flex items-start gap-3">
                            <div class="flex items-center justify-center w-8 h-8 rounded-lg bg-emerald-100 dark:bg-emerald-900/30">
                                <x-icon name="calendar-check" class="h-4 w-4 text-emerald-600 dark:text-emerald-400" />
                            </div>
                            <div class="flex-1">
                                <p class="text-xs text-neutral-500 dark:text-neutral-400">Released</p>
                                <p id="modalReleasedDate" class="text-sm font-medium text-neutral-900 dark:text-white">---</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <div class="flex items-center justify-center w-8 h-8 rounded-lg bg-sky-100 dark:bg-sky-900/30">
                                <x-icon name="calendar-clock" class="h-4 w-4 text-sky-600 dark:text-sky-400" />
                            </div>
                            <div class="flex-1">
                                <p class="text-xs text-neutral-500 dark:text-neutral-400">Due Date</p>
                                <p id="modalDueDate" class="text-sm font-medium text-neutral-900 dark:text-white">---</p>
                            </div>
                        </div>
                    </div>

                    {{-- Status Badge --}}
                    <div class="flex items-center justify-between pt-2 border-t border-neutral-100 dark:border-neutral-800">
                        <span class="text-xs text-neutral-500 dark:text-neutral-400">Status</span>
                        <span id="modalStatusBadge" class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-neutral-100 text-neutral-700 dark:bg-neutral-800 dark:text-neutral-300">
                            ---
                        </span>
                    </div>

                    {{-- Return Info (if returned) --}}
                    <div id="modalReturnInfo" class="hidden pt-2 border-t border-neutral-100 dark:border-neutral-800">
                        <div class="flex items-start gap-3">
                            <div class="flex items-center justify-center w-8 h-8 rounded-lg bg-emerald-100 dark:bg-emerald-900/30">
                                <x-icon name="check-circle" class="h-4 w-4 text-emerald-600 dark:text-emerald-400" />
                            </div>
                            <div class="flex-1">
                                <p class="text-xs text-neutral-500 dark:text-neutral-400">Returned On</p>
                                <p id="modalReturnDate" class="text-sm font-medium text-emerald-600 dark:text-emerald-400">---</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Footer --}}
                <div class="px-6 py-4 border-t border-neutral-200 dark:border-neutral-800 bg-neutral-50 dark:bg-neutral-900/50">
                    <div class="flex justify-end gap-3">
                        <button onclick="closeEventModal()" class="px-4 py-2 text-sm font-medium text-neutral-700 dark:text-neutral-300 hover:bg-neutral-100 dark:hover:bg-neutral-800 rounded-lg transition-colors">
                            Close
                        </button>
                        <a id="modalViewLink" href="#" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-violet-600 hover:bg-violet-500 rounded-lg transition-colors">
                            <x-icon name="external-link" class="h-4 w-4" />
                            View in Rentals
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

<script>
        // State
        let calendar = null;
        let calendarEvents = [];

        // Initialize on DOM ready
        document.addEventListener('DOMContentLoaded', function() {
            initCalendar();
            loadCalendarData();
        });

        function initCalendar() {
            const calendarEl = document.getElementById('rental-calendar');
            
            calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,dayGridWeek,listWeek'
                },
                buttonText: {
                    today: 'Today',
                    month: 'Month',
                    week: 'Week',
                    list: 'List'
                },
                firstDay: 0, // Sunday
                navLinks: true,
                editable: false,
                dayMaxEvents: 3,
                moreLinkClick: 'popover',
                eventClick: function(info) {
                    showEventDetail(info.event);
                },
                eventDidMount: function(info) {
                    // Add tooltip with more info
                    info.el.title = info.event.extendedProps.customerName + ' - ' + info.event.extendedProps.itemName;
                },
                datesSet: function(dateInfo) {
                    // Reload data when view/date changes
                    loadCalendarData(dateInfo.start, dateInfo.end);
                }
            });

            calendar.render();
        }

        function loadCalendarData(startDate, endDate) {
            // Default to current month if not specified
            if (!startDate || !endDate) {
                const now = new Date();
                startDate = new Date(now.getFullYear(), now.getMonth() - 1, 1);
                endDate = new Date(now.getFullYear(), now.getMonth() + 2, 0);
            }

            const params = new URLSearchParams({
                start: startDate.toISOString().split('T')[0],
                end: endDate.toISOString().split('T')[0]
            });

            fetch('/api/rentals/calendar?' + params.toString(), {
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(response => response.json())
            .then(data => {
                // Update stats
                document.getElementById('dueTodayCount').textContent = data.stats.due_today || 0;
                document.getElementById('dueThisWeekCount').textContent = data.stats.due_this_week || 0;
                document.getElementById('currentlyOverdueCount').textContent = data.stats.overdue || 0;
                document.getElementById('activeRentalsCount').textContent = data.stats.active || 0;

                // Clear existing events
                calendar.removeAllEvents();

                // Add new events
                calendarEvents = data.events || [];
                calendarEvents.forEach(event => {
                    calendar.addEvent(event);
                });
            })
            .catch(error => {
                console.error('Failed to load calendar data:', error);
            });
        }

        function showEventDetail(event) {
            const props = event.extendedProps;
            
            // Update modal content
            document.getElementById('modalRentalId').textContent = '#' + props.rentalId;
            document.getElementById('modalCustomerName').textContent = props.customerName || 'Unknown';
            document.getElementById('modalItemName').textContent = props.itemName || 'Unknown';
            document.getElementById('modalItemCode').textContent = props.itemCode || '';
            document.getElementById('modalReleasedDate').textContent = formatDate(props.releasedDate);
            document.getElementById('modalDueDate').textContent = formatDate(props.dueDate);

            // Status badge
            const statusBadge = document.getElementById('modalStatusBadge');
            const statusMap = {
                'overdue': { text: 'Overdue', class: 'bg-rose-100 text-rose-700 dark:bg-rose-900/30 dark:text-rose-400' },
                'due': { text: 'Due', class: 'bg-sky-100 text-sky-700 dark:bg-sky-900/30 dark:text-sky-400' },
                'returned': { text: 'Returned', class: 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400' },
                'released': { text: 'Active', class: 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400' }
            };
            
            const status = statusMap[props.eventType] || statusMap['due'];
            statusBadge.textContent = status.text;
            statusBadge.className = 'inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium ' + status.class;

            // Return info
            const returnInfo = document.getElementById('modalReturnInfo');
            if (props.returnDate) {
                returnInfo.classList.remove('hidden');
                document.getElementById('modalReturnDate').textContent = formatDate(props.returnDate);
            } else {
                returnInfo.classList.add('hidden');
            }

            // View link
            document.getElementById('modalViewLink').href = '/rentals?highlight=' + props.rentalId;

            // Show modal
            document.getElementById('eventDetailModal').classList.remove('hidden');
        }

        function closeEventModal() {
            document.getElementById('eventDetailModal').classList.add('hidden');
        }

        function formatDate(dateStr) {
            if (!dateStr) return '---';
            const date = new Date(dateStr);
            return date.toLocaleDateString('en-US', { 
                weekday: 'short',
                year: 'numeric', 
                month: 'short', 
                day: 'numeric' 
            });
        }

        // Close modal on escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeEventModal();
            }
        });
    </script>
@endsection
