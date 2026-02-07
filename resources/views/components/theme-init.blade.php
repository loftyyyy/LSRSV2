<script>
    (function () {
        var root = document.documentElement;
        var savedMode = localStorage.getItem('darkMode');
        var isDarkMode = savedMode !== null ? savedMode === 'true' : true;

        root.classList.add('theme-preload');
        root.classList.toggle('dark', isDarkMode);
        root.style.colorScheme = isDarkMode ? 'dark' : 'light';
        root.style.backgroundColor = isDarkMode ? '#000000' : '#f5f5f5';

        var existingStyle = document.getElementById('theme-preload-style');
        if (existingStyle) {
            existingStyle.remove();
        }

        var style = document.createElement('style');
        style.id = 'theme-preload-style';
        style.textContent = 'html.theme-preload *,html.theme-preload *::before,html.theme-preload *::after{transition:none !important;}';
        document.head.appendChild(style);

        function cleanupThemePreload() {
            root.classList.remove('theme-preload');
            var preloadStyle = document.getElementById('theme-preload-style');
            if (preloadStyle) {
                preloadStyle.remove();
            }
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', cleanupThemePreload, { once: true });
        } else {
            cleanupThemePreload();
        }
    })();
</script>
