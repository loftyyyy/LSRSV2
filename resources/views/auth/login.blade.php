<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login · Love &amp; Styles</title>

    {{-- Favicon --}}
    <link rel="icon" href="{{ asset('favicon.ico') }}" sizes="any">
    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}">
    <link rel="manifest" href="{{ asset('site.webmanifest') }}">

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
<body class="min-h-screen flex justify-center items-center font-geist bg-neutral-100 text-neutral-900 dark:bg-black dark:text-neutral-50 transition-colors duration-300 ease-in-out">
    <div class="w-full max-w-md bg-gradient-to-b from-zinc-900 to-black border border-zinc-800 rounded-2xl p-8 shadow-xl">

        <h2 class="text-xl font-semibold mb-1 text-white">Welcome Back</h2>
        <p class="text-sm text-gray-400 mb-6">
            Sign in to access your rental management dashboard
        </p>

        <!-- Email -->
        <div class="mb-4">
            <label class="text-sm text-gray-300 block mb-2">Email Address</label>
            <div class="flex items-center bg-black border border-zinc-800 rounded-lg px-3">
                <div class="text-neutral-100/0.2 pr-2">
                    <x-icon name="mail"/>
                </div>
                <input
                    type="email"
                    placeholder="your@business.com"
                    class="w-full bg-transparent py-3 text-sm text-white placeholder-gray-500 focus:outline-none"
                />
            </div>
        </div>

        <!-- Password -->
        <div class="mb-2">
            <label class="text-sm text-gray-300 block mb-2">Password</label>
            <div class="flex items-center bg-black border border-zinc-800 rounded-lg px-3">
                <div class="text-neutral-100/0.2 pr-2">
                    <x-icon name="lock"/>
                </div>
                <input
                    type="password"
                    placeholder="••••••••"
                    class="w-full bg-transparent py-3 text-sm text-white placeholder-gray-500 focus:outline-none"
                />
            </div>
        </div>

        <!-- Forgot password -->
        <div class="text-right mb-6">
            <a href="#" class="text-sm text-purple-400 hover:underline">
                Forgot password?
            </a>
        </div>

        <!-- Button -->
        <button
            class="w-full bg-purple-600 hover:bg-purple-700 transition rounded-lg py-3 font-medium text-white flex items-center justify-center gap-2"
        >
            Sign In →
        </button>

        <hr class="border-zinc-800 my-6">

        <p class="text-center text-sm text-gray-400">
            Don’t have access?
            <span class="text-gray-300">Contact your system administrator</span>
        </p>

    </div>

</body>
</html>
