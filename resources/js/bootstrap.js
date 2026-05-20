import axios from 'axios';
window.axios = axios;

// Configure axios to send credentials (cookies) with cross-origin requests
// This is essential for Laravel session authentication to work with API calls
window.axios.defaults.withCredentials = true;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// Set CSRF token from meta tag on every request
window.axios.interceptors.request.use(config => {
    const token = document.querySelector('meta[name="csrf-token"]');
    if (token) {
        config.headers['X-CSRF-TOKEN'] = token.getAttribute('content');
    }
    return config;
});
