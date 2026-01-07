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
<body class="min-h-screen bg-black text-neutral-50 flex" style="font-family: 'Geist', system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;">
    <x-sidebar />

    <main class="flex-1 flex flex-col px-10 py-8 overflow-x-hidden overflow-y-auto bg-gradient-to-b from-black via-black to-neutral-950">

        <header class="mb-8">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-semibold tracking-tight text-white">
                        Rental Tracking
                    </h1>
                    <p class="mt-1 text-sm text-neutral-400" style="font-family: 'Geist Mono', ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, 'Liberation Mono', 'Courier New', monospace;">
                        Monitor active rentals and returns dates
                    </p>
                </div>
            </div>
        </header>
    </main>

</body>
</html>
