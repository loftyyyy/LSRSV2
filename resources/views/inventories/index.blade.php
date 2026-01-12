<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory · Love &amp; Styles</title>

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
<body class="min-h-screen flex font-geist bg-neutral-100 text-neutral-900 dark:bg-black dark:text-neutral-50 transition-colors duration-300 ease-in-out">
<x-sidebar />

<main class="flex-1 flex flex-col px-10 py-8 overflow-x-hidden overflow-y-auto bg-gradient-to-b from-neutral-100 via-neutral-100 to-neutral-200 dark:from-black dark:via-black dark:to-neutral-950 transition-colors duration-300 ease-in-out">

    <header class="mb-8 transition-colors duration-300 ease-in-out">
        <div class="flex items-center justify-between gap-4">
            <div>
                <h1 class="text-3xl font-semibold tracking-tight text-neutral-900 dark:text-white transition-colors duration-300 ease-in-out">
                    Inventory Management
                </h1>
                <p class="mt-1 text-sm font-geist-mono text-neutral-500 dark:text-neutral-400 transition-colors duration-300 ease-in-out">
                    Manage rental items and stock levels
                </p>
            </div>

            <div class="flex items-center gap-3 text-xs">
                <button class="inline-flex items-center gap-2 rounded-xl px-3.5 py-2 text-[14px] font-medium border dark:hover:text-black hover:text-white border-neutral-300 bg-white text-neutral-700 dark:hover:bg-violet-600 hover:bg-violet-600  dark:border-neutral-800 dark:bg-neutral-950/80 dark:text-neutral-200 dark:hover:bg-neutral-900  transition-colors duration-300 ease-in-out">
                    <x-icon name="chart-column" class="h-4 w-4" />
                    <span>Reports</span>
                </button>

                <button class="inline-flex items-center gap-2 rounded-xl px-4 py-2 text-[14px] font-medium bg-violet-600 text-white dark:hover:text-white hover:text-black dark:text-black hover:bg-violet-500 shadow-violet-600/40 transition-colors duration-300 ease-in-out">
                    <x-icon name="plus" class="h-4 w-4" />
                    <span>Add Customer</span>
                </button>
            </div>
        </div>
    </header>


    {{-- Stats --}}
    <section class="grid grid-cols-4 gap-6 mb-8">
        @foreach ([
            ['label' => 'Total Items', 'value' => '3', 'color' => 'text-neutral-900 dark:text-white'],
            ['label' => 'Available', 'value' => '3', 'color' => 'text-amber-500'],
            ['label' => 'Under Repair', 'value' => '1', 'color' => 'text-green-500'],
            ['label' => 'Inventory Value', 'value' => '₱2', 'color' => 'text-violet-600'],
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
                        <!-- Custom Filter Dropdown -->
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
                                    <li class="px-4 py-2 hover:bg-neutral-100 dark:hover:bg-neutral-900 cursor-pointer transition-colors duration-200">All Status</li>
                                    <li class="px-4 py-2 hover:bg-neutral-100 dark:hover:bg-neutral-900 cursor-pointer transition-colors duration-200">Active</li>
                                    <li class="px-4 py-2 hover:bg-neutral-100 dark:hover:bg-neutral-900 cursor-pointer transition-colors duration-200">Inactive</li>
                                </ul>
                            </div>
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
                            <th class="py-2.5 pr-4 font-medium">Name</th>
                            <th class="py-2.5 pr-4 font-medium">Email</th>
                            <th class="py-2.5 pr-4 font-medium">Phone</th>
                            <th class="py-2.5 pr-4 font-medium">Address</th>
                            <th class="py-2.5 pr-4 font-medium">Total Rentals</th>
                            <th class="py-2.5 pr-4 font-medium text-left">Status</th>
                            <th class="py-2.5 pl-2 font-medium text-left">Actions</th>
                        </tr>
                        </thead>

                        <tbody class="text-[13px]">
                        <tr class="border-b border-neutral-200 hover:bg-neutral-100 dark:border-neutral-900/60 dark:hover:bg-white/5 transition-colors duration-300 ease-in-out">
                            <td class="py-3.5 pr-4 pl-4 text-neutral-500 font-geist-mono">#001</td>
                            <td class="py-3.5 pr-4 text-neutral-900 dark:text-neutral-100">Sarah Johnson</td>
                            <td class="py-3.5 pr-4 text-neutral-600 dark:text-neutral-300">sjohnson@gmail.com</td>
                            <td class="py-3.5 pr-4 text-neutral-600 dark:text-neutral-300 font-geist-mono">09923423</td>
                            <td class="py-3.5 pr-2 text-neutral-600 dark:text-neutral-300 font-geist-mono">115 Boulevard</td>
                            <td class="py-3.5 pr-4 text-left text-neutral-900 dark:text-neutral-100 font-geist-mono">5</td>
                            <td class="py-3.5 pr-2">
                                <span class="inline-flex items-center rounded-full bg-emerald-500/15 px-2 py-1 text-[11px] font-medium text-emerald-600 border border-emerald-500/40 dark:text-emerald-300 transition-colors duration-300 ease-in-out">
                                    <span class="mr-1.5 h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                                    Active
                                </span>
                            </td>
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


<script>
    const filterButton = document.getElementById('filter-button');
    const filterButtonText = document.getElementById('filter-button-text');
    const filterMenu = document.getElementById('filter-menu');
    const iconDown = document.getElementById('icon-down');
    const iconUp = document.getElementById('icon-up');

    let isOpen = false;

    // Toggle dropdown
    filterButton.addEventListener('click', (e) => {
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
    filterMenu.querySelectorAll('li').forEach(item => {
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
    document.addEventListener('click', () => {
        if (isOpen) {
            isOpen = false;
            filterMenu.classList.add('opacity-0', 'scale-95', 'pointer-events-none');
            filterMenu.classList.remove('opacity-100', 'scale-100', 'pointer-events-auto');

            iconDown.classList.remove('hidden');
            iconUp.classList.add('hidden');
        }
    });
</script>
</body>
</html>
