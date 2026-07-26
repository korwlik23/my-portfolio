import axios from 'axios';
window.axios = axios;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

import Alpine from 'alpinejs';
window.Alpine = Alpine;
Alpine.start();

window.toggleDarkMode = function() {
    document.documentElement.classList.toggle('dark');
    localStorage.setItem('darkMode', document.documentElement.classList.contains('dark'));
};

window.toggleLargeText = function() {
    document.documentElement.classList.toggle('large-text');
    const enabled = document.documentElement.classList.contains('large-text');
    localStorage.setItem('largeTextMode', enabled);

    const button = document.getElementById('large-text-toggle');
    if (button) {
        button.setAttribute('aria-pressed', String(enabled));
    }
};

if (localStorage.getItem('darkMode') === 'true' || (!('darkMode' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
    document.documentElement.classList.add('dark');
} else {
    document.documentElement.classList.remove('dark');
}

if (localStorage.getItem('largeTextMode') === 'true') {
    document.documentElement.classList.add('large-text');
}

window.toggleSidebar = function() {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebar-overlay');

    if (sidebar) {
        sidebar.classList.toggle('-translate-x-full');
    }

    if (overlay) {
        overlay.classList.toggle('hidden');
    }
};

document.addEventListener('DOMContentLoaded', () => {
    const largeTextToggle = document.getElementById('large-text-toggle');
    if (largeTextToggle) {
        largeTextToggle.setAttribute('aria-pressed', String(document.documentElement.classList.contains('large-text')));
    }

    const overlay = document.getElementById('sidebar-overlay');
    if (overlay) {
        overlay.addEventListener('click', window.toggleSidebar);
    }
});
