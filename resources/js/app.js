import './bootstrap';

function getPreferredDarkMode() {
    const savedMode = localStorage.getItem('darkMode');
    return savedMode !== null ? savedMode === 'true' : true;
}

function applyThemePreference() {
    const isDarkMode = getPreferredDarkMode();
    const root = document.documentElement;

    root.classList.toggle('dark', isDarkMode);
    root.style.colorScheme = isDarkMode ? 'dark' : 'light';
    root.style.backgroundColor = isDarkMode ? '#000000' : '#f5f5f5';
    globalThis.isDarkMode = isDarkMode;
}

applyThemePreference();

document.addEventListener('turbo:before-render', applyThemePreference);
document.addEventListener('turbo:load', applyThemePreference);
document.addEventListener('turbo:before-cache', applyThemePreference);
