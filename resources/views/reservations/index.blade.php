<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reservations · Love &amp; Styles</title>

    {{-- Fonts: Geist & Geist Mono --}}
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Geist:wght@400;500;600;700&family=Geist+Mono:wght@400;500&display=swap">

    {{-- App styles --}}
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
</head>

<body class="min-h-screen flex font-geist bg-neutral-100 text-neutral-900 dark:bg-black dark:text-neutral-50 transition-colors duration-300 ease-in-out">
<x-sidebar />

<main class="flex-1 flex flex-col px-10 py-8 overflow-x-hidden overflow-y-auto bg-gradient-to-b from-neutral-100 via-neutral-100 to-neutral-200 dark:from-black dark:via-black dark:to-neutral-950 transition-colors duration-300 ease-in-out">
    {{-- Page header --}}
    <header class="mb-8 transition-colors duration-300 ease-in-out">
        <div class="flex items-center justify-between gap-4">
            <div>
                <h1 class="text-3xl font-semibold tracking-tight text-neutral-900 dark:text-white transition-colors duration-300 ease-in-out">
                    Reservations
                </h1>
                <p class="mt-1 text-sm font-geist-mono text-neutral-500 dark:text-neutral-400 transition-colors duration-300 ease-in-out">
                    Manage customer reservations and bookings
                </p>
            </div>

            <div class="flex items-center gap-3 text-xs">
                <button type="button" class="inline-flex items-center gap-2 rounded-xl border border-neutral-300 bg-white px-3.5 py-2 text-neutral-700 hover:bg-violet-600 dark:border-neutral-800 dark:bg-neutral-950/80 dark:text-neutral-200  transition-colors duration-300 ease-in-out">
                    <span class="inline-flex h-5 w-5 items-center justify-center rounded-md">
                        <x-icon name="chart-column" class="h-4 w-4" />
                    </span>
                    <span class="text-[14px] font-medium tracking-wide">Reports</span>
                </button>

                <button type="button" class="inline-flex items-center gap-2 rounded-xl border border-neutral-300 bg-white px-3.5 py-2 text-neutral-700 hover:bg-violet-600 dark:border-neutral-800 dark:bg-neutral-950/80 dark:text-neutral-200  transition-colors duration-300 ease-in-out">
                    <span class="inline-flex h-5 w-5 items-center justify-center rounded-md">
                        <x-icon name="eye" class="h-4 w-4" />
                    </span>
                    <span class="text-[14px] font-medium tracking-wide">Browse Items</span>
                </button>

                <button type="button" class="inline-flex items-center gap-2 rounded-xl bg-violet-600 px-4 py-2 text-[14px] font-medium tracking-wide text-white dark:text-black shadow-lg hover:bg-violet-500 transition-colors duration-300 ease-in-out">
                    <span class="inline-flex h-5 w-5 items-center justify-center rounded-md">
                        <x-icon name="plus" class="h-4 w-4" />
                    </span>
                    <span>New Reservation</span>
                </button>
            </div>
        </div>
    </header>

    {{-- Filters + table --}}
    <section class="flex-1">
        <div class="rounded-2xl border border-neutral-200 bg-white shadow-sm dark:border-neutral-900 dark:bg-neutral-950/60 dark:shadow-[0_18px_60px_rgba(0,0,0,0.65)] transition-colors duration-300 ease-in-out">
            {{-- Search & filters --}}
            <div class="border-b border-neutral-200 px-6 py-4 dark:border-neutral-900 transition-colors duration-300 ease-in-out">
                <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                    <div class="flex-1">
                        <div class="flex items-center gap-3 rounded-2xl bg-white px-4 py-2.5 border border-neutral-300 focus-within:border-neutral-500 dark:border-neutral-800 dark:bg-black/60 transition-colors duration-300 ease-in-out">
                            <x-icon name="search" class="h-4 w-4 text-neutral-500 transition-colors duration-300 ease-in-out" />
                            <input type="text" placeholder="Search by customer, item, or ID..." class="w-full bg-transparent text-xs text-neutral-700 placeholder:text-neutral-400 dark:text-neutral-100 dark:placeholder:text-neutral-500 focus:outline-none transition-colors duration-300 ease-in-out">
                        </div>
                    </div>

                    <div class="flex items-center gap-3">
                        <div class="relative">
                            <select class="appearance-none rounded-2xl border border-neutral-300 bg-white px-3 pr-8 py-2 text-[11px] font-medium text-neutral-700 focus:outline-none focus:border-neutral-500 dark:border-neutral-800 dark:bg-black/70 dark:text-neutral-200 transition-colors duration-300 ease-in-out">
                                <option>All Status</option>
                                <option>Confirmed</option>
                                <option>Pending</option>
                                <option>Cancelled</option>
                            </select>
                            <span class="pointer-events-none absolute inset-y-0 right-2 flex items-center text-neutral-500 text-xs">▾</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Table --}}
            <div class="px-6 py-4">
                <div class="overflow-x-auto">
                    <table class="min-w-full text-left text-xs text-neutral-600 dark:text-neutral-300 transition-colors duration-300 ease-in-out">
                        <thead class="text-[11px] uppercase tracking-[0.18em] text-neutral-500">
                        <tr class="border-b border-neutral-200 dark:border-neutral-900/80">
                            <th class="py-2.5 pr-4 pl-4 font-medium">ID</th>
                            <th class="py-2.5 pr-4 font-medium">Customer</th>
                            <th class="py-2.5 pr-4 font-medium">Item</th>
                            <th class="py-2.5 pr-4 font-medium">Pickup</th>
                            <th class="py-2.5 pr-4 font-medium">Return</th>
                            <th class="py-2.5 pr-4 font-medium">Status</th>
                            <th class="py-2.5 pr-4 font-medium text-left">Amount</th>
                            <th class="py-2.5 pl-2 font-medium text-left">Actions</th>
                        </tr>
                        </thead>

                        <tbody class="text-[13px]">
                        <tr class="border-b border-neutral-200 hover:bg-neutral-100 dark:border-neutral-900/60 dark:hover:bg-white/5 transition-colors duration-300 ease-in-out">
                            <td class="py-3.5 pr-4 pl-4 text-neutral-500 font-geist-mono">#001</td>
                            <td class="py-3.5 pr-4 text-neutral-900 dark:text-neutral-100">Sarah Johnson</td>
                            <td class="py-3.5 pr-4 text-neutral-600 dark:text-neutral-300">Wedding Gown</td>
                            <td class="py-3.5 pr-4 text-neutral-600 dark:text-neutral-300 font-geist-mono">2025-11-15</td>
                            <td class="py-3.5 pr-2 text-neutral-600 dark:text-neutral-300 font-geist-mono">2025-11-17</td>
                            <td class="py-3.5 pr-2">
                                    <span class="inline-flex items-center rounded-full bg-emerald-500/15 px-2 py-1 text-[11px] font-medium text-emerald-600 border border-emerald-500/40 dark:text-emerald-300 transition-colors duration-300 ease-in-out">
                                        <span class="mr-1.5 h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                                        Confirmed
                                    </span>
                            </td>
                            <td class="py-3.5 pr-4 text-left text-neutral-900 dark:text-neutral-100 font-geist-mono">$450</td>
                            <td class="py-3.5 pl-2 text-left text-neutral-500 dark:text-neutral-400">
                                <div class="inline-flex items-center gap-2">
                                    <button class="rounded-lg p-1.5 hover:bg-violet-600 hover:text-white transition-colors duration-300 ease-in-out" aria-label="Edit">
                                        <x-icon name="edit" class="h-3.5 w-3.5" />
                                    </button>
                                    <button class="rounded-lg p-1.5 text-red-500 hover:bg-red-500/15 hover:text-red-400 transition-colors duration-300 ease-in-out" aria-label="Delete">
                                        <x-icon name="trash" class="h-3.5 w-3.5" />
                                    </button>
                                </div>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
</main>
</body>
</html>
