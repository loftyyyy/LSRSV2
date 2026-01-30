<aside class="flex flex-col h-screen w-64 bg-white dark:bg-[#0b0b0b] border-r border-neutral-200 dark:border-neutral-800 text-neutral-900 dark:text-neutral-100 transition-colors duration-200">
    {{-- Brand --}}
    <div class="flex items-center gap-3 px-6 h-20 border-b border-neutral-200 dark:border-neutral-800">
        <div class="flex items-center justify-center h-10 w-10 rounded-2xl bg-violet-600 text-sm font-semibold tracking-tight text-white">
            LS
        </div>
        <div class="flex flex-col">
            <span class="text-sm font-semibold tracking-tight text-neutral-900 dark:text-white" style="font-family: 'Geist', system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;">
                Love &amp; Styles
            </span>
            <span class="text-xs text-neutral-500 dark:text-neutral-400" style="font-family: 'Geist Mono', ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, 'Liberation Mono', 'Courier New', monospace;">
                Rental System
            </span>
        </div>
    </div>

    {{-- Main navigation (dynamic from config/navigation.php) --}}
    @php
        $navItems = config('navigation.main', []);
    @endphp

    <nav class="flex-1 px-3 py-4 space-y-2 text-sm overflow-y-auto">
        @foreach ($navItems as $item)
            @php
                $isActive = request()->routeIs($item['route'] . '*');
            @endphp

            <a
                href="{{ route($item['route']) }}"
                class="group flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all duration-200
                       {{ $isActive
                            ? 'bg-violet-600 dark:text-neutral-900 text-neutral-100 shadow-[0_0_0_1px_rgba(167,139,250,0.7)]'
                            : 'text-neutral-700 dark:text-neutral-300 hover:bg-violet-200 dark:hover:bg-neutral-900 hover:text-neutral-900 dark:hover:text-neutral-100' }}"
                style="font-family: 'Geist', system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;"
            >
                <span class="flex items-center justify-center h-8 w-8 {{ $isActive ? 'dark:text-neutral-900 text-neutral-100' : 'text-neutral-900 dark:text-neutral-100' }}">
                    <x-icon :name="$item['icon']" class="h-6 w-6" />
                </span>

                <span class="truncate text-[13px] font-semibold">
                    {{ $item['label'] }}
                </span>
            </a>
        @endforeach
    </nav>

    {{-- Footer actions --}}
    <div class="px-4 pb-5 pt-2 border-t border-neutral-200 dark:border-neutral-800 space-y-3 text-xs">
        {{-- Dark/Light Mode Toggle --}}
        <button
            type="button"
            id="darkModeToggle"
            onclick="toggleDarkMode()"
            class="flex w-full items-center justify-between rounded-xl px-3 py-2 text-[11px] font-medium text-neutral-700 dark:text-neutral-200 hover:bg-neutral-200 dark:hover:bg-neutral-800 transition-all duration-200"
            style="font-family: 'Geist', system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;"
        >
            <span class="flex items-center gap-2">
                <span class="inline-flex h-6 w-10 items-center rounded-full bg-amber-500/20 dark:bg-violet-600/20 px-0.5 relative transition-all duration-300" id="toggleTrack">
                    <span class="flex h-5 w-5 items-center justify-center rounded-full bg-amber-400 dark:bg-violet-500 shadow-sm text-black dark:text-white transition-all duration-300 ease-in-out" id="toggleKnob" style="transform: translateX(16px);">
                        <span id="iconMoon" class="block dark:block">
                            <x-icon name="moon" class="h-3 w-3" />
                        </span>

                        <span id="iconSun" class="hidden dark:hidden">
                            <x-icon name="sun" class="h-3 w-3" />
                        </span>
                    </span>
                </span>
                <span id="modeLabel" class="text-neutral-700 dark:text-neutral-200">Dark Mode</span>
            </span>
            <span class="text-[10px] uppercase tracking-[0.16em] text-neutral-500 dark:text-neutral-500" id="modeStatus">
                On
            </span>
        </button>

        {{-- Logout Button --}}
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button
                type="submit"
                class="flex w-full items-center gap-2 rounded-xl px-3 py-2 text-[12px] font-medium text-red-600 dark:text-red-400 hover:bg-red-500/10 hover:text-red-500 dark:hover:text-red-300 transition-all duration-200"
                style="font-family: 'Geist', system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;"
            >
                <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-red-500/10 text-red-600 dark:text-red-400">
                    <x-icon name="logout" class="h-4 w-4" />
                </span>
                <span>Logout</span>
            </button>
        </form>
    </div>

    {{-- Fonts: Geist & Geist Mono from Google Fonts --}}
    <link
        rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Geist:wght@400;500;600;700&family=Geist+Mono:wght@400;500&display=swap"
    >

    <script>
        // Use globalThis to store isDarkMode to prevent redeclaration on page navigation
        if (!globalThis.isDarkMode) {
            globalThis.isDarkMode = null;
        }

        var savedMode = localStorage.getItem('darkMode');

        if (savedMode !== null) {
            // User preference exists
            globalThis.isDarkMode = savedMode === 'true';
        } else {
            // Default to dark mode (as per your requirement)
            globalThis.isDarkMode = true;
            localStorage.setItem('darkMode', 'true');
        }

        if (globalThis.isDarkMode) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }

        window.addEventListener('DOMContentLoaded', function () {
            updateToggleUI(globalThis.isDarkMode);
        });

        function toggleDarkMode() {
            globalThis.isDarkMode = !globalThis.isDarkMode;
            localStorage.setItem('darkMode', globalThis.isDarkMode.toString());

            if (globalThis.isDarkMode) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }

            updateToggleUI(globalThis.isDarkMode);
        }

        function updateToggleUI(dark) {
            const knob = document.getElementById('toggleKnob');
            const track = document.getElementById('toggleTrack');
            const modeLabel = document.getElementById('modeLabel');
            const modeStatus = document.getElementById('modeStatus');
            const moonIcon = document.getElementById('iconMoon');
            const sunIcon = document.getElementById('iconSun');

            if (dark) {
                // Dark Mode Active
                knob.style.transform = 'translateX(16px)';

                modeLabel.textContent = 'Dark Mode';
                modeStatus.textContent = 'On';

                // Show moon icon
                moonIcon.classList.remove('hidden');
                sunIcon.classList.add('hidden');
            } else {
                // Light Mode Active
                knob.style.transform = 'translateX(0)';

                modeLabel.textContent = 'Light Mode';
                modeStatus.textContent = 'Off';

                // Show sun icon
                moonIcon.classList.add('hidden');
                sunIcon.classList.remove('hidden');
            }
        }
    </script>
</aside>
