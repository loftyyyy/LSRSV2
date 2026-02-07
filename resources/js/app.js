import './bootstrap';

function getPreferredDarkMode() {
    const savedPreference = localStorage.getItem('themePreference');

    if (savedPreference === 'dark') {
        return true;
    }

    if (savedPreference === 'light') {
        return false;
    }

    return window.matchMedia('(prefers-color-scheme: dark)').matches;
}

function applyThemePreference() {
    if (globalThis.themeController && typeof globalThis.themeController.getIsDark === 'function') {
        const isDarkMode = globalThis.themeController.getIsDark();
        const root = document.documentElement;

        root.classList.toggle('dark', isDarkMode);
        root.style.colorScheme = isDarkMode ? 'dark' : 'light';
        root.style.backgroundColor = isDarkMode ? '#000000' : '#f5f5f5';
        return;
    }

    const isDarkMode = getPreferredDarkMode();
    const root = document.documentElement;

    root.classList.toggle('dark', isDarkMode);
    root.style.colorScheme = isDarkMode ? 'dark' : 'light';
    root.style.backgroundColor = isDarkMode ? '#000000' : '#f5f5f5';
}

applyThemePreference();

document.addEventListener('turbo:before-render', applyThemePreference);
document.addEventListener('turbo:load', applyThemePreference);
document.addEventListener('turbo:before-cache', applyThemePreference);
