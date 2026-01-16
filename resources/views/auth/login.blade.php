<!doctype html>
<html lang="en" class="transition-colors duration-300">
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
<body class="min-h-screen flex flex-col justify-center items-center font-geist bg-neutral-100 text-neutral-900 dark:bg-black dark:text-neutral-50 transition-colors duration-300 ease-in-out px-4 py-8">

    {{-- Theme Toggle --}}
    <div class="absolute top-6 right-6">
        <button
            type="button"
            id="darkModeToggle"
            onclick="toggleDarkMode()"
            class="flex items-center gap-2 rounded-xl px-3 py-2 text-xs font-medium text-neutral-700 dark:text-neutral-200 bg-white/80 dark:bg-black/80 border border-neutral-200 dark:border-neutral-800 hover:bg-neutral-100 dark:hover:bg-neutral-900 transition-all duration-200 backdrop-blur-sm"
        >
            <span class="inline-flex h-5 w-8 items-center rounded-full bg-amber-500/20 dark:bg-violet-600/20 px-0.5 relative transition-all duration-300" id="toggleTrack">
                <span class="flex h-4 w-4 items-center justify-center rounded-full bg-amber-400 dark:bg-violet-500 shadow-sm text-black dark:text-white transition-all duration-300 ease-in-out" id="toggleKnob" style="transform: translateX(12px);">
                    <span id="iconMoon" class="block dark:block">
                        <x-icon name="moon" class="h-2.5 w-2.5" />
                    </span>
                    <span id="iconSun" class="hidden dark:hidden">
                        <x-icon name="sun" class="h-2.5 w-2.5" />
                    </span>
                </span>
            </span>
            <span id="modeLabel" class="text-neutral-700 dark:text-neutral-200 text-[11px]">Dark</span>
        </button>
    </div>

    <div class="w-full max-w-md bg-white dark:bg-gradient-to-b dark:from-zinc-900 dark:to-black border border-neutral-200 dark:border-zinc-800 rounded-2xl p-6 sm:p-8 shadow-xl">

<h2 class="text-xl font-semibold mb-1 text-neutral-900 dark:text-white">Welcome Back</h2>
        <p class="text-sm text-neutral-600 dark:text-gray-400 mb-6">
            Sign in to access your rental management dashboard
        </p>

        <!-- Email -->
        <div class="mb-4">
            <label class="text-sm text-neutral-700 dark:text-gray-300 block mb-2">Email Address</label>
            <div class="flex items-center bg-neutral-50 dark:bg-black border border-neutral-200 dark:border-zinc-800 rounded-lg px-3 transition-colors">
                <div class="text-neutral-400 dark:text-neutral-600 pr-2">
                    <x-icon name="mail"/>
                </div>
                <input
                    type="email"
                    placeholder="your@business.com"
                    class="w-full bg-transparent py-3 text-sm text-neutral-900 dark:text-white placeholder-neutral-500 dark:placeholder-gray-500 focus:outline-none"
                />
            </div>
        </div>

        <!-- Password -->
        <div class="mb-2">
            <label class="text-sm text-neutral-700 dark:text-gray-300 block mb-2">Password</label>
            <div class="flex items-center bg-neutral-50 dark:bg-black border border-neutral-200 dark:border-zinc-800 rounded-lg px-3 transition-colors">
                <div class="text-neutral-400 dark:text-neutral-600 pr-2">
                    <x-icon name="lock"/>
                </div>
                <input
                    type="password"
                    id="passwordInput"
                    placeholder="••••••••"
                    class="w-full bg-transparent py-3 text-sm text-neutral-900 dark:text-white placeholder-neutral-500 dark:placeholder-gray-500 focus:outline-none"
                />
                <button
                    type="button"
                    id="passwordToggle"
                    onclick="togglePassword()"
                    class="text-neutral-400 dark:text-neutral-600 hover:text-neutral-600 dark:hover:text-neutral-400 ml-2 p-1 transition-colors"
                >
                    <x-icon name="eye" id="eyeOpen" class="h-4 w-4" />
                    <x-icon name="eye-off" id="eyeClosed" class="h-4 w-4 hidden" />
                </button>
            </div>
        </div>

        <!-- Forgot password -->
        <div class="text-right mb-6">
            <a href="#" class="text-sm text-violet-600 dark:text-purple-400 hover:underline transition-colors">
                Forgot password?
            </a>
        </div>

        <!-- Button -->
        <button
            class="w-full bg-violet-600 hover:bg-violet-700 dark:bg-purple-600 dark:hover:bg-purple-700 transition rounded-lg py-3 font-medium text-white flex items-center justify-center gap-2"
        >
            Sign In →
        </button>

        <hr class="border-neutral-200 dark:border-zinc-800 my-6">

        <p class="text-center text-sm text-neutral-600 dark:text-gray-400">
            Don't have access?
            <span class="text-neutral-800 dark:text-gray-300">Contact your system administrator</span>
        </p>

    </div>

    {{-- Theme and Password Scripts --}}
    <script>
        let isDarkMode;

        // Initialize theme on page load
        const savedMode = localStorage.getItem('darkMode');

        if (savedMode !== null) {
            isDarkMode = savedMode === 'true';
        } else {
            // Default to dark mode
            isDarkMode = true;
            localStorage.setItem('darkMode', 'true');
        }

        // Apply theme immediately
        if (isDarkMode) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }

        // Update UI after DOM is ready
        window.addEventListener('DOMContentLoaded', function () {
            updateToggleUI(isDarkMode);
        });

        function toggleDarkMode() {
            isDarkMode = !isDarkMode;
            localStorage.setItem('darkMode', isDarkMode.toString());

            if (isDarkMode) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }

            updateToggleUI(isDarkMode);
        }

        function updateToggleUI(dark) {
            const knob = document.getElementById('toggleKnob');
            const modeLabel = document.getElementById('modeLabel');
            const moonIcon = document.getElementById('iconMoon');
            const sunIcon = document.getElementById('iconSun');

            if (dark) {
                // Dark Mode Active
                knob.style.transform = 'translateX(12px)';
                modeLabel.textContent = 'Dark';
                moonIcon.classList.remove('hidden');
                sunIcon.classList.add('hidden');
            } else {
                // Light Mode Active
                knob.style.transform = 'translateX(0)';
                modeLabel.textContent = 'Light';
                moonIcon.classList.add('hidden');
                sunIcon.classList.remove('hidden');
            }
        }

        // Password visibility toggle
        function togglePassword() {
            const passwordInput = document.getElementById('passwordInput');
            const eyeOpen = document.getElementById('eyeOpen');
            const eyeClosed = document.getElementById('eyeClosed');

            const isPassword = passwordInput.type === 'password';

            passwordInput.type = isPassword ? 'text' : 'password';

            eyeOpen.classList.toggle('hidden', !isPassword);
            eyeClosed.classList.toggle('hidden', isPassword);
        }
    </script>

</body>
</html>
