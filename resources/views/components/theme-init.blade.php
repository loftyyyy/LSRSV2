<style>
    html, body {
        color-scheme: light;
        background-color: #f5f5f5;
        color: #171717;
    }
</style>

<script>
    (function () {
        // Purge any stale theme keys left over from when dark mode existed
        localStorage.removeItem('theme');
        localStorage.removeItem('color-scheme');
        localStorage.removeItem('darkMode');
        localStorage.removeItem('appearance');

        var root = document.documentElement;
        root.classList.remove('dark');
        root.style.colorScheme = 'light';

        globalThis.themeController = {
            getPreference: function () { return 'light'; },
            setPreference: function () { return false; },
            togglePreference: function () { return false; },
            getIsDark: function () { return false; },
            getState: function () { return { preference: 'light', isDark: false }; }
        };
    })();
</script>
