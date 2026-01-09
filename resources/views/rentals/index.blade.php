<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rentals · Love &amp; Styles</title>

    {{-- Fonts: Geist & Geist Mono --}}
    <link
        rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Geist:wght@400;500;600;700&family=Geist+Mono:wght@400;500&display=swap"
    >

    {{-- App styles --}}
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
</head>

<body class="min-h-screen bg-black text-neutral-50 flex font-geist">
<x-sidebar />

<main class="flex-1 flex flex-col px-10 py-8 overflow-x-hidden overflow-y-auto bg-gradient-to-b from-black via-black to-neutral-950 transition-colors">
    <header class="mb-8">
        <div class="flex items-center justify-between gap-4">
            <div>
                <h1 class="text-3xl font-semibold tracking-tight">
                    Rental Tracking
                </h1>
                <p class="mt-1 text-sm text-neutral-400 font-geist-mono">
                    Monitor active rentals and return dates
                </p>
            </div>

            <div class="flex items-center gap-3">
                <button class="px-4 py-2 text-sm font-medium rounded-lg hover:bg-neutral-800 transition-colors flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                    Reports
                </button>

                <button class="px-4 py-2 text-sm font-medium rounded-lg bg-purple-600 hover:bg-purple-700 transition-colors flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 4v16m8-8H4"/>
                    </svg>
                    Log Return
                </button>
            </div>
        </div>
    </header>

    <!-- Stats Grid -->
    <div class="grid grid-cols-4 gap-6 mb-8">
        <div class="bg-neutral-900 rounded-xl p-6 border border-neutral-800">
            <div class="text-sm text-neutral-400 mb-2">Active Rentals</div>
            <div class="text-3xl font-semibold">3</div>
        </div>

        <div class="bg-neutral-900 rounded-xl p-6 border border-neutral-800">
            <div class="text-sm text-neutral-400 mb-2">Due This Week</div>
            <div class="text-3xl font-semibold text-orange-500">3</div>
        </div>

        <div class="bg-neutral-900 rounded-xl p-6 border border-neutral-800">
            <div class="text-sm text-neutral-400 mb-2">Overdue</div>
            <div class="text-3xl font-semibold text-red-500">1</div>
        </div>

        <div class="bg-neutral-900 rounded-xl p-6 border border-neutral-800">
            <div class="text-sm text-neutral-400 mb-2">Late Penalties</div>
            <div class="text-3xl font-semibold text-purple-500">$NaN</div>
        </div>
    </div>

    <!-- Active Rentals -->
    <div class="bg-neutral-900 rounded-xl border border-neutral-800 p-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-semibold">Active Rentals</h2>

            <div class="relative">
                <input
                    type="text"
                    placeholder="Search by customer, item, or ID..."
                    class="w-80 px-4 py-2 pl-10 rounded-lg bg-neutral-800 border border-neutral-700 text-sm focus:outline-none focus:ring-2 focus:ring-purple-500"
                >
                <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-neutral-400"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>
        </div>

        <div class="space-y-3">
            <!-- Rental Card -->
            <div class="bg-neutral-800/50 rounded-lg p-4 border border-neutral-700 hover:border-neutral-600 transition cursor-pointer">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="font-semibold text-base">Sarah Johnson</h3>
                        <div class="text-sm text-neutral-400">Wedding Gown</div>
                        <div class="text-xs text-neutral-500 font-geist-mono">
                            Pickup: 2025-11-10 | Due: 2025-11-14 | Fee: $450
                        </div>
                    </div>

                    <span class="px-3 py-1 rounded-md text-xs font-medium bg-blue-500/20 text-blue-400 border border-blue-500/30">
                            Active
                        </span>
                </div>
            </div>
        </div>
    </div>
</main>
</body>
</html>
