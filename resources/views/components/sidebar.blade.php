<aside class="flex flex-col h-screen w-64 bg-black/95 border-r border-neutral-800 text-neutral-100">
    {{-- Brand --}}
    <div class="flex items-center gap-3 px-6 h-20 border-b border-neutral-800">
        <div class="flex items-center justify-center h-10 w-10 rounded-2xl bg-violet-600 text-sm font-semibold tracking-tight">
            LS
        </div>
        <div class="flex flex-col">
            <span class="text-sm font-semibold tracking-tight" style="font-family: 'Geist', system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;">
                Love &amp; Styles
            </span>
            <span class="text-xs text-neutral-400" style="font-family: 'Geist Mono', ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, 'Liberation Mono', 'Courier New', monospace;">
                Rental System
            </span>
        </div>
    </div>

    {{-- Main navigation (dynamic from config/navigation.php) --}}
    @php
        $navItems = config('navigation.main', []);
    @endphp

    <nav class="flex-1 px-3 py-4 space-y-2 text-sm">
        @foreach ($navItems as $item)
            @php
                $isActive = request()->routeIs($item['route'] . '*');
            @endphp

            <a
                href="{{ route($item['route']) }}"
                class="group flex items-center gap-3 px-3 py-2.5 rounded-xl transition
                       {{ $isActive
                            ? 'bg-violet-600 text-white shadow-[0_0_0_1px_rgba(167,139,250,0.7)]'
                            : 'text-neutral-300 hover:bg-neutral-900 hover:text-white' }}"
                style="font-family: 'Geist', system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;"
            >
                <span class="flex items-center justify-center h-8 w-8 text-white">
                    <x-icon :name="$item['icon']" class="h-6 w-6" />
                </span>

                <span class="truncate text-[13px] font-semibold">
                    {{ $item['label'] }}
                </span>
            </a>
        @endforeach
    </nav>

    {{-- Footer actions --}}
    <div class="px-4 pb-5 pt-2 border-t border-neutral-800 space-y-3 text-xs text-neutral-400">
        <button
            type="button"
            id="darkModeToggle"
            onclick="toggleDarkMode()"
            class="flex w-full items-center justify-between rounded-xl px-3 py-2 text-[11px] font-medium text-neutral-200 hover:bg-neutral-800 transition"
            style="font-family: 'Geist', system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;"
        >
            <span class="flex items-center gap-2">
                <span class="inline-flex h-6 w-10 items-center rounded-full bg-black/80 px-0.5 relative transition-all duration-300" id="toggleTrack">
                    <span class="flex h-5 w-5 items-center justify-center rounded-full bg-violet-500 shadow-sm text-black transition-transform duration-300 ease-in-out" id="toggleKnob">
                         <span id="iconMoon" class="block">
                             <x-icon name="moon" class="h-3 w-3" />
                         </span>

                        <span id="iconSun" class="hidden">
                            <x-icon name="sun" class="h-3 w-3" />
                        </span>

                    </span>
                </span>
                <span id="modeLabel">Dark Mode</span>
            </span>
            <span class="text-[10px] uppercase tracking-[0.16em] text-neutral-500" id="modeStatus">
                On
            </span>
        </button>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button
                type="submit"
                class="flex w-full items-center gap-2 rounded-xl px-3 py-2 text-[12px] font-medium text-red-400 hover:bg-red-500/10 hover:text-red-300 transition"
                style="font-family: 'Geist', system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;"
            >
                <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-red-500/10 text-red-400">
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

    {{-- Dark Mode Toggle Script --}}
    <script>
        let isDarkMode;

        // 1️⃣ Determine initial mode
        const savedMode = localStorage.getItem('darkMode');

        if (savedMode !== null) {
            // User preference exists
            isDarkMode = savedMode === 'true';
        } else {
            // No saved preference → follow OS / browser
            isDarkMode = window.matchMedia('(prefers-color-scheme: dark)').matches;
        }

        // 2️⃣ Apply theme immediately (prevents flicker)
        document.documentElement.classList.toggle('dark', isDarkMode);

        // 3️⃣ Apply UI state on load
        window.addEventListener('DOMContentLoaded', function () {
            updateToggleUI(isDarkMode);
        });

        function toggleDarkMode() {
            isDarkMode = !isDarkMode;
            localStorage.setItem('darkMode', isDarkMode);

            updateToggleUI(isDarkMode);
            document.documentElement.classList.toggle('dark', isDarkMode);
        }

        function updateToggleUI(dark) {
            const knob = document.getElementById('toggleKnob');
            const track = document.getElementById('toggleTrack');
            const modeLabel = document.getElementById('modeLabel');
            const modeStatus = document.getElementById('modeStatus');
            const moonIcon = document.getElementById('iconMoon');
            const sunIcon = document.getElementById('iconSun');

            if (dark) {
                // 🌙 Dark mode ON → knob RIGHT
                knob.style.transform = 'translateX(16px)';

                track.classList.add('bg-black/80');
                track.classList.remove('bg-amber-500/20');

                knob.classList.add('bg-violet-500');
                knob.classList.remove('bg-amber-400');

                modeLabel.textContent = 'Dark Mode';
                modeStatus.textContent = 'On';

                moonIcon.classList.remove('hidden');
                sunIcon.classList.add('hidden');
            } else {
                // ☀️ Light mode ON → knob LEFT
                knob.style.transform = 'translateX(0)';

                track.classList.remove('bg-black/80');
                track.classList.add('bg-amber-500/20');

                knob.classList.remove('bg-violet-500');
                knob.classList.add('bg-amber-400');

                modeLabel.textContent = 'Light Mode';
                modeStatus.textContent = 'On';

                moonIcon.classList.add('hidden');
                sunIcon.classList.remove('hidden');
            }
        }
    </script>
</aside>
