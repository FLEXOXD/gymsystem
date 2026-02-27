import './bootstrap';

const canRegisterSw = import.meta.env.PROD || ['localhost', '127.0.0.1'].includes(window.location.hostname);
const pwaMeta = document.querySelector('meta[name="pwa-install-enabled"]');
const hasPwaAccessMeta = pwaMeta !== null;
const pwaInstallEnabled = pwaMeta ? pwaMeta.getAttribute('content') === '1' : false;

const detectStandaloneMode = () => {
    const mediaMatch = window.matchMedia && window.matchMedia('(display-mode: standalone)').matches;
    const iosStandalone = window.navigator && window.navigator.standalone === true;

    return Boolean(mediaMatch || iosStandalone);
};

const syncPwaModeCookie = () => {
    const pwaMode = detectStandaloneMode() ? 'standalone' : 'browser';
    document.cookie = `gym_pwa_mode=${pwaMode}; path=/; max-age=2592000; SameSite=Lax`;

    if (window.axios?.defaults?.headers?.common) {
        window.axios.defaults.headers.common['X-PWA-Mode'] = pwaMode;
    }
};

syncPwaModeCookie();
window.addEventListener('pageshow', syncPwaModeCookie);
if (window.matchMedia) {
    const mediaQuery = window.matchMedia('(display-mode: standalone)');
    if (typeof mediaQuery.addEventListener === 'function') {
        mediaQuery.addEventListener('change', syncPwaModeCookie);
    } else if (typeof mediaQuery.addListener === 'function') {
        mediaQuery.addListener(syncPwaModeCookie);
    }
}

if (hasPwaAccessMeta && !pwaInstallEnabled && 'serviceWorker' in navigator) {
    navigator.serviceWorker.getRegistrations().then((registrations) => {
        registrations.forEach((registration) => {
            registration.unregister().catch(() => {
                // Keep silent: unregister should not block app usage.
            });
        });
    }).catch(() => {
        // Keep silent.
    });
}

if (hasPwaAccessMeta && pwaInstallEnabled && canRegisterSw && 'serviceWorker' in navigator) {
    window.addEventListener('load', () => {
        const swMetaUrl = document.querySelector('meta[name="sw-url"]')?.getAttribute('content');
        const swUrl = (swMetaUrl && swMetaUrl.trim() !== '') ? swMetaUrl : '/sw.js';

        navigator.serviceWorker.register(swUrl).catch(() => {
            // Keep silent: service worker registration should not block app usage.
        });
    });
}
