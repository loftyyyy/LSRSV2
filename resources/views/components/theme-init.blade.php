<style id="theme-preload-style">

    html,
    body {
        background-color: #f5f5f5;
        color-scheme: light;
    }

    html.dark,
    html.dark body {
        background-color: #000000 !important;
        color-scheme: dark;
    }

    html:not(.dark),
    html:not(.dark) body {
        background-color: #f5f5f5 !important;
        color-scheme: light;
    }

    html.theme-preload,
    html.theme-preload body,
    html.theme-preload *,
    html.theme-preload *::before,
    html.theme-preload *::after {
        transition: none !important;
        animation: none !important;
    }

</style>
<script>
    (function () {
        var root = document.documentElement;
        var STORAGE_KEY = 'themePreference';
        var prefersDarkQuery = window.matchMedia('(prefers-color-scheme: dark)');

        function getStoredPreference() {
            try {
                var value = localStorage.getItem(STORAGE_KEY);
                return value === 'dark' || value === 'light' ? value : null;
            } catch (_e) {
                return null;
            }
        }

        function setStoredPreference(value) {
            try {
                if (value === 'dark' || value === 'light') {
                    localStorage.setItem(STORAGE_KEY, value);
                } else {
                    localStorage.removeItem(STORAGE_KEY);
                }
                localStorage.removeItem('darkMode');
            } catch (_e) {
                // Ignore storage failures.
            }
        }

        function resolveIsDark() {
            var storedPreference = getStoredPreference();
            if (storedPreference === 'dark') return true;
            if (storedPreference === 'light') return false;
            return prefersDarkQuery.matches;
        }

        function applyResolvedTheme() {
            var isDarkMode = resolveIsDark();
            root.classList.toggle('dark', isDarkMode);
            root.style.colorScheme = isDarkMode ? 'dark' : 'light';
            root.style.backgroundColor = isDarkMode ? '#000000' : '#f5f5f5';
            return isDarkMode;
        }

        function getState() {
            return {
                preference: getStoredPreference(),
                isDark: resolveIsDark()
            };
        }

        // Apply theme as early as possible to avoid wrong first paint.
        applyResolvedTheme();

        globalThis.themeController = {
            getPreference: function () {
                return getStoredPreference();
            },
            setPreference: function (value) {
                setStoredPreference(value);
                var isDarkMode = applyResolvedTheme();
                window.dispatchEvent(new CustomEvent('theme:changed', {
                    detail: {
                        isDark: isDarkMode,
                        preference: getStoredPreference()
                    }
                }));
                return isDarkMode;
            },
            togglePreference: function () {
                var currentPreference = getStoredPreference();
                var nextPreference = currentPreference === 'dark'
                    ? 'light'
                    : (currentPreference === 'light' ? null : 'dark');
                return this.setPreference(nextPreference);
            },
            getIsDark: function () {
                return resolveIsDark();
            },
            getState: function () {
                return getState();
            }
        };

        function handleSystemThemeChange() {
            if (getStoredPreference() !== null) return;
            var isDarkMode = applyResolvedTheme();
            window.dispatchEvent(new CustomEvent('theme:changed', {
                detail: { isDark: isDarkMode, preference: null }
            }));
        }

        if (typeof prefersDarkQuery.addEventListener === 'function') {
            prefersDarkQuery.addEventListener('change', handleSystemThemeChange);
        } else if (typeof prefersDarkQuery.addListener === 'function') {
            prefersDarkQuery.addListener(handleSystemThemeChange);
        }
    })();
</script>
