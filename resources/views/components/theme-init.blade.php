<script>
    (function () {
        var root = document.documentElement;
        var savedMode = localStorage.getItem('darkMode');
        var isDarkMode = savedMode !== null ? savedMode === 'true' : true;

        root.setAttribute('data-theme-ready', '0');

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
        style.textContent = [
            'html{background-color:#f5f5f5;}',
            'html.dark{background-color:#000000;}',
            'html[data-theme-ready="0"] body{visibility:hidden;}',
            'html.theme-preload *,html.theme-preload *::before,html.theme-preload *::after{transition:none !important;}'
        ].join('');
        document.head.appendChild(style);

        function cleanupThemePreload() {
            root.setAttribute('data-theme-ready', '1');
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
