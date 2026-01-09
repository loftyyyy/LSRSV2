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

<main class="flex-1 flex flex-col px-10 py-8 overflow-x-hidden overflow-y-auto bg-gradient-to-b from-black via-black to-neutral-950">
    {{-- Header --}}
    <header class="mb-8">
        <div class="flex items-center justify-between gap-4">
            <div>
                <h1 class="text-3xl font-semibold tracking-tight text-white">
                    Rental Tracking
                </h1>
                <p class="mt-1 text-sm text-neutral-400 font-geist-mono">
                    Monitor active rentals and return dates
                </p>
            </div>

            <div class="flex items-center gap-3 text-xs">
                <button
                    class="inline-flex items-center gap-2 rounded-xl border border-neutral-800 bg-neutral-950/80 px-3.5 py-2 text-neutral-200 hover:bg-neutral-900 transition"
                >
                    <x-icon name="chart-column" class="h-4 w-4 text-white" />
                    <span class="text-[14px] font-medium tracking-wide">Reports</span>
                </button>

                <button
                    class="inline-flex items-center gap-2 rounded-xl bg-violet-600 px-4 py-2 text-[14px] font-medium tracking-wide text-black shadow-lg shadow-violet-600/40 hover:bg-violet-500 transition"
                >
                    <x-icon name="plus" class="h-4 w-4 text-black" />
                    <span>Log Return</span>
                </button>
            </div>
        </div>
    </header>

    {{-- Stats --}}
    <section class="grid grid-cols-4 gap-6 mb-8">
        @foreach ([
            ['label' => 'Active Rentals', 'value' => '3', 'color' => 'text-white'],
            ['label' => 'Due This Week', 'value' => '3', 'color' => 'text-amber-400'],
            ['label' => 'Overdue', 'value' => '1', 'color' => 'text-red-400'],
            ['label' => 'Late Penalties', 'value' => '$NaN', 'color' => 'text-violet-400'],
        ] as $stat)
            <x-card>
                <div class="text-sm text-neutral-400 mb-2">
                    {{ $stat['label'] }}
                </div>
                <div class="text-3xl font-semibold {{ $stat['color'] }}">
                    {{ $stat['value'] }}
                </div>
            </x-card>
        @endforeach
    </section>

    {{-- Active Rentals --}}
    <section class="rounded-2xl border border-neutral-900 bg-neutral-950/60 p-6 shadow-[0_18px_60px_rgba(0,0,0,0.65)]">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-semibold tracking-tight text-white">
                Active Rentals
            </h2>

            <div class="relative">
                <div class="flex items-center gap-3 rounded-2xl bg-black/60 px-4 py-2.5 border border-neutral-800 focus-within:border-neutral-500 transition">
                    <x-icon name="search" class="h-4 w-4 text-neutral-500" />
                    <input
                        type="text"
                        placeholder="Search by customer, item, or ID..."
                        class="w-72 bg-transparent text-xs text-neutral-100 placeholder:text-neutral-500 focus:outline-none"
                    >
                </div>
            </div>
        </div>

        <div class="space-y-3">
            {{-- Rental Card --}}
            <div class="rounded-xl border border-neutral-900 bg-neutral-950/60 p-4 hover:bg-white/5 transition cursor-pointer">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="font-semibold text-neutral-100">
                            Sarah Johnson
                        </h3>
                        <div class="text-sm text-neutral-300">
                            Wedding Gown
                        </div>
                        <div class="text-xs text-neutral-500 font-geist-mono">
                            Pickup: 2025-11-10 · Due: 2025-11-14 · Fee: $450
                        </div>
                    </div>

                    <span class="inline-flex items-center rounded-full bg-emerald-500/15 px-2 py-1 text-[11px] font-medium text-emerald-300 border border-emerald-500/40">
                        <span class="mr-1.5 h-1.5 w-1.5 rounded-full bg-emerald-400"></span>
                        Active
                    </span>
                </div>
            </div>
        </div>
    </section>
</main>
</body>
</html>
