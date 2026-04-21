<style id="theme-preload-style">

    html,
    body {
        /* No hardcoded bg color to avoid flash when custom gradient/bg used */
        color-scheme: light;
    }

    html.dark,
    html.dark body {
        background-color: #000000 !important;
        color-scheme: dark;
    }

    html:not(.dark),
    html:not(.dark) body {
        /* No enforced background color here, letting Tailwind defaults apply */
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
                // Ignore storage failures; fallback to system preference.
            }
        }

        function resolveIsDark() {
            return false;
        }

        function getState() {
            var preference = getStoredPreference();
            return {
                preference: preference,
                isDark: resolveIsDark()
            };
        }

        function applyResolvedTheme() {
            var isDarkMode = false;
            root.classList.remove('dark');
            root.style.colorScheme = 'light';
            // Do not force background style via JS to prevent overriding tailwind classes
            root.style.backgroundColor = '';

            if (document.body) {
                document.body.style.backgroundColor = '';
            }

            return false;
        }

        root.classList.add('theme-preload');
        root.classList.add('page-enter');
        try {
            localStorage.removeItem('darkMode');
        } catch (_e) {
            // Ignore storage failures.
        }
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
            if (getStoredPreference() !== null) {
                return;
            }

            var isDarkMode = applyResolvedTheme();
            window.dispatchEvent(new CustomEvent('theme:changed', {
                detail: {
                    isDark: isDarkMode,
                    preference: null
                }
            }));
        }

        if (typeof prefersDarkQuery.addEventListener === 'function') {
            // prefersDarkQuery.addEventListener('change', handleSystemThemeChange);
        } else if (typeof prefersDarkQuery.addListener === 'function') {
            // prefersDarkQuery.addListener(handleSystemThemeChange);
        }

        function cleanupAfterFirstPaint() {
            requestAnimationFrame(function () {
                root.classList.remove('theme-preload');
                requestAnimationFrame(function () {
                    root.classList.remove('page-enter');
                });
            });
        }

        var preloadTimeout = window.setTimeout(cleanupAfterFirstPaint, 250);

        function onWindowLoad() {
            window.clearTimeout(preloadTimeout);
            cleanupAfterFirstPaint();
        }

        if (document.readyState === 'complete') {
            onWindowLoad();
        } else {
            window.addEventListener('load', onWindowLoad, { once: true });
        }
    })();
</script>
