<!doctype html>
<html lang="en">
<head>
    {{-- Prevent flash of wrong theme --}}
    @include('components.theme-init')
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rentals · Love &amp; Styles</title>

    {{-- Favicon --}}
    <link rel="icon" href="{{ asset('favicon.ico') }}" sizes="any">
    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}">
    <link rel="manifest" href="{{ asset('site.webmanifest') }}">

    {{-- Fonts: Geist & Geist Mono --}}
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Geist:wght@400;500;600;700&family=Geist+Mono:wght@400;500&display=swap">

    {{-- App styles --}}
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
</head>

<body class="min-h-screen flex font-geist bg-neutral-100 text-neutral-900 dark:bg-black dark:text-neutral-50">
<x-sidebar />

<main class="flex-1 ml-64 flex flex-col px-10 py-8 overflow-x-hidden overflow-y-auto bg-gradient-to-b from-neutral-100 via-neutral-100 to-neutral-200 dark:from-black dark:via-black dark:to-neutral-950">
    {{-- Header --}}
    <header class="mb-8 transition-colors duration-300 ease-in-out">
        <div class="flex items-center justify-between gap-4">
            <div>
                <h1 class="text-3xl font-semibold tracking-tight text-neutral-900 dark:text-white transition-colors duration-300 ease-in-out">
                    Rental Tracking
                </h1>
                <p class="mt-1 text-sm font-geist-mono text-neutral-500 dark:text-neutral-400 transition-colors duration-300 ease-in-out">
                    Monitor active rentals and return dates
                </p>
            </div>

            <div class="flex items-center gap-3 text-xs">
                <button class="inline-flex items-center gap-2 rounded-xl px-3.5 py-2 text-[14px] font-medium border dark:hover:text-black hover:text-white border-neutral-300 bg-white text-neutral-700 dark:hover:bg-violet-600 hover:bg-violet-600  dark:border-neutral-800 dark:bg-neutral-950/80 dark:text-neutral-200 dark:hover:bg-neutral-900  transition-colors duration-300 ease-in-out">
                    <x-icon name="chart-column" class="h-4 w-4" />
                    <span>Reports</span>
                </button>

                <button class="inline-flex items-center gap-2 rounded-xl px-4 py-2 text-[14px] font-medium bg-violet-600 text-white dark:hover:text-white hover:text-black dark:text-black hover:bg-violet-500 shadow-violet-600/40 transition-colors duration-300 ease-in-out">
                    <x-icon name="plus" class="h-4 w-4" />
                    <span>Log Return</span>
                </button>
            </div>
        </div>
    </header>

    {{-- Stats --}}
    <section class="grid grid-cols-4 gap-6 mb-8">
        @foreach ([
            ['label' => 'Active Rentals', 'value' => '3', 'color' => 'text-neutral-900 dark:text-white'],
            ['label' => 'Due This Week', 'value' => '3', 'color' => 'text-amber-500'],
            ['label' => 'Overdue', 'value' => '1', 'color' => 'text-red-500'],
            ['label' => 'Late Penalties', 'value' => '$NaN', 'color' => 'text-violet-600'],
        ] as $stat)
            <div class="rounded-2xl p-6 border border-neutral-200 bg-white dark:border-neutral-900 dark:bg-neutral-950/60 shadow-sm dark:shadow-[0_18px_60px_rgba(0,0,0,0.65)] transition-colors duration-300 ease-in-out">
                <div class="text-sm text-neutral-500 dark:text-neutral-400 mb-2 transition-colors duration-300 ease-in-out">
                    {{ $stat['label'] }}
                </div>
                <div class="text-3xl font-semibold {{ $stat['color'] }} transition-colors duration-300 ease-in-out">
                    {{ $stat['value'] }}
                </div>
            </div>
        @endforeach
    </section>

    {{-- Active Rentals --}}
    <section class="rounded-2xl p-6 border border-neutral-200 bg-white dark:border-neutral-900 dark:bg-neutral-950/60 transition-colors duration-300 ease-in-out">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-semibold tracking-tight text-neutral-900 dark:text-white transition-colors duration-300 ease-in-out">
                Active Rentals
            </h2>

            <div class="relative flex items-center gap-3">
                <!-- Search -->
                <div class="flex items-center gap-3 rounded-2xl px-4 py-2.5 border border-neutral-300 bg-white focus-within:border-neutral-500 dark:border-neutral-800 dark:bg-black/60 transition-colors duration-300 ease-in-out">
                    <x-icon name="search" class="h-4 w-4 text-neutral-500 transition-colors duration-300 ease-in-out" />
                    <input type="text" placeholder="Search by customer, item, or ID..." class="w-72 bg-transparent text-xs text-neutral-700 placeholder:text-neutral-400 dark:text-neutral-100 dark:placeholder:text-neutral-500 focus:outline-none transition-colors duration-300 ease-in-out">
                </div>

                <!-- Filter with toggle icons -->
                <div class="relative" id="filter-dropdown">
                    <button id="filter-button" class="flex items-center gap-2 rounded-2xl px-3 py-2 text-xs border border-neutral-300 bg-white text-neutral-700 dark:border-neutral-800 dark:bg-black/60 dark:text-neutral-100 focus:outline-none transition-colors duration-300 ease-in-out">
                        <span id="filter-button-text">Filter Status</span>
                        <span id="icon-down" class="h-3 w-3 transition-transform duration-300 ease-in-out">
                            <x-icon name="arrow-down" class="h-3 w-3" />
                        </span>
                        <span id="icon-up" class="hidden h-3 w-3 transition-transform duration-300 ease-in-out">
                            <x-icon name="arrow-up" class="h-3 w-3" />
                        </span>
                    </button>

                    <div id="filter-menu" class="absolute right-0 mt-2 w-48 rounded-xl border border-neutral-300 bg-white dark:border-neutral-800 dark:bg-black/60 shadow-lg z-50 overflow-hidden opacity-0 scale-95 pointer-events-none transition-all duration-200 ease-in-out">
                        <ul class="flex flex-col text-xs">
                            <li class="px-4 py-2 hover:bg-neutral-100 dark:hover:bg-neutral-900 cursor-pointer transition-colors duration-200">All Statuses</li>
                            <li class="px-4 py-2 hover:bg-neutral-100 dark:hover:bg-neutral-900 cursor-pointer transition-colors duration-200">Active</li>
                            <li class="px-4 py-2 hover:bg-neutral-100 dark:hover:bg-neutral-900 cursor-pointer transition-colors duration-200">Due Soon</li>
                            <li class="px-4 py-2 hover:bg-neutral-100 dark:hover:bg-neutral-900 cursor-pointer transition-colors duration-200">Overdue</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div class="space-y-3">
            <div class="rounded-xl p-4 cursor-pointer border border-neutral-200 bg-white hover:bg-neutral-100 dark:border-neutral-900 dark:bg-neutral-950/60 dark:hover:bg-white/5 transition-colors duration-300 ease-in-out">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="font-semibold text-neutral-900 dark:text-neutral-100 transition-colors duration-300 ease-in-out">
                            Sarah Johnson
                        </h3>
                        <div class="text-sm text-neutral-600 dark:text-neutral-300 transition-colors duration-300 ease-in-out">
                            Wedding Gown
                        </div>
                        <div class="text-xs font-geist-mono text-neutral-500 transition-colors duration-300 ease-in-out">
                            Pickup: 2025-11-10 · Due: 2025-11-14 · Fee: $450
                        </div>
                    </div>

                    <span class="inline-flex items-center rounded-full px-2 py-1 text-[11px] font-medium border bg-emerald-500/15 text-emerald-600 border-emerald-500/40 dark:text-emerald-300 transition-colors duration-300 ease-in-out">
                        <span class="mr-1.5 h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                        Active
                    </span>
                </div>
            </div>
        </div>
    </section>
</main>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var filterButton = document.getElementById('filter-button');
        var filterButtonText = document.getElementById('filter-button-text');
        var filterMenu = document.getElementById('filter-menu');
        var iconDown = document.getElementById('icon-down');
        var iconUp = document.getElementById('icon-up');

        var isOpen = false;

        // Toggle dropdown
        filterButton?.addEventListener('click', (e) => {
            e.stopPropagation();
            isOpen = !isOpen;

            filterMenu.classList.toggle('opacity-0', !isOpen);
            filterMenu.classList.toggle('scale-95', !isOpen);
            filterMenu.classList.toggle('pointer-events-none', !isOpen);
            filterMenu.classList.toggle('opacity-100', isOpen);
            filterMenu.classList.toggle('scale-100', isOpen);
            filterMenu.classList.toggle('pointer-events-auto', isOpen);

            iconDown.classList.toggle('hidden', isOpen);
            iconUp.classList.toggle('hidden', !isOpen);
        });

        // Update filter button text when a status is clicked
        filterMenu?.querySelectorAll('li').forEach(item => {
            item.addEventListener('click', (e) => {
                e.stopPropagation();
                filterButtonText.textContent = item.textContent;
                isOpen = false;

                filterMenu.classList.add('opacity-0', 'scale-95', 'pointer-events-none');
                filterMenu.classList.remove('opacity-100', 'scale-100', 'pointer-events-auto');

                iconDown.classList.remove('hidden');
                iconUp.classList.add('hidden');
            });
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', function() {
            if (isOpen) {
                isOpen = false;
                filterMenu.classList.add('opacity-0', 'scale-95', 'pointer-events-none');
                filterMenu.classList.remove('opacity-100', 'scale-100', 'pointer-events-auto');

                iconDown.classList.remove('hidden');
                iconUp.classList.add('hidden');
            }
        });
    });
</script>
</body>
</html>
