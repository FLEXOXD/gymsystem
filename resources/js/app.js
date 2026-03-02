import './bootstrap';
import { initCashIndexModule } from './modules/cash-index';
import { initPasswordVisibilityModule } from './modules/password-visibility';

const canRegisterSw = import.meta.env.PROD || ['localhost', '127.0.0.1'].includes(window.location.hostname);
const pwaMeta = document.querySelector('meta[name="pwa-install-enabled"]');
const hasPwaAccessMeta = pwaMeta !== null;
const pwaInstallEnabled = pwaMeta ? pwaMeta.getAttribute('content') === '1' : false;
const pwaEventsUrl = (document.querySelector('meta[name="pwa-events-url"]')?.getAttribute('content') || '').trim();
const csrfToken = (document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '').trim();
let swUpdateReadyBroadcasted = false;
let swControllerChangeHandled = false;
let swUpdateCheckTimerId = null;

const detectStandaloneMode = () => {
    const mediaMatch = window.matchMedia && window.matchMedia('(display-mode: standalone)').matches;
    const iosStandalone = window.navigator && window.navigator.standalone === true;

    return Boolean(mediaMatch || iosStandalone);
};

const reportPwaEvent = (eventName, payload = {}) => {
    const normalizedEvent = String(eventName || '').trim();
    if (normalizedEvent === '' || pwaEventsUrl === '' || !hasPwaAccessMeta) {
        return;
    }

    const body = JSON.stringify({
        event_name: normalizedEvent,
        event_source: 'web',
        mode: detectStandaloneMode() ? 'standalone' : 'browser',
        payload: (payload && typeof payload === 'object') ? payload : {},
    });

    try {
        if (typeof navigator.sendBeacon === 'function') {
            const blob = new Blob([body], { type: 'application/json' });
            if (navigator.sendBeacon(pwaEventsUrl, blob)) {
                return;
            }
        }
    } catch (_error) {
        // Keep silent and fallback to fetch.
    }

    fetch(pwaEventsUrl, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            ...(csrfToken !== '' ? { 'X-CSRF-TOKEN': csrfToken } : {}),
        },
        credentials: 'same-origin',
        keepalive: true,
        body,
    }).catch(() => {
        // Keep silent to avoid impacting UX.
    });
};

window.reportGymPwaEvent = reportPwaEvent;

const syncPwaModeCookie = () => {
    const pwaMode = detectStandaloneMode() ? 'standalone' : 'browser';
    document.cookie = `gym_pwa_mode=${pwaMode}; path=/; max-age=2592000; SameSite=Lax`;

    if (window.axios?.defaults?.headers?.common) {
        window.axios.defaults.headers.common['X-PWA-Mode'] = pwaMode;
    }

    const sessionKey = `gymsystem:pwa-launch:${pwaMode}`;
    try {
        if (window.sessionStorage.getItem(sessionKey) !== '1') {
            window.sessionStorage.setItem(sessionKey, '1');
            reportPwaEvent(pwaMode === 'standalone' ? 'standalone_launch' : 'browser_launch');
        }
    } catch (_error) {
        // Keep silent.
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

const announceSwUpdateReady = () => {
    if (swUpdateReadyBroadcasted) {
        return;
    }
    swUpdateReadyBroadcasted = true;
    window.dispatchEvent(new CustomEvent('GYMSYSTEM_SW_UPDATE_READY'));
    reportPwaEvent('sw_update_ready');
};

const setupSwUpdateHooks = (registration) => {
    if (!registration) {
        return;
    }

    if (registration.waiting) {
        announceSwUpdateReady();
    }

    registration.addEventListener('updatefound', () => {
        const installingWorker = registration.installing;
        if (!installingWorker) {
            return;
        }

        installingWorker.addEventListener('statechange', () => {
            if (installingWorker.state === 'installed' && navigator.serviceWorker.controller) {
                announceSwUpdateReady();
            }
        });
    });
};

const startSwUpdatePolling = (registration) => {
    if (!registration) {
        return;
    }

    if (swUpdateCheckTimerId) {
        window.clearInterval(swUpdateCheckTimerId);
    }

    swUpdateCheckTimerId = window.setInterval(() => {
        registration.update().catch(() => {
            // Keep silent.
        });
    }, 15 * 60 * 1000);
};

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
    window.addEventListener('load', async () => {
        const swMetaUrl = document.querySelector('meta[name="sw-url"]')?.getAttribute('content');
        const swUrl = (swMetaUrl && swMetaUrl.trim() !== '') ? swMetaUrl : '/sw.js';

        try {
            const registration = await navigator.serviceWorker.register(swUrl);
            reportPwaEvent('sw_registered', { scope: registration.scope });
            setupSwUpdateHooks(registration);
            startSwUpdatePolling(registration);
        } catch (_error) {
            // Keep silent: service worker registration should not block app usage.
        }
    });

    navigator.serviceWorker.addEventListener('controllerchange', () => {
        if (swControllerChangeHandled) {
            return;
        }
        swControllerChangeHandled = true;
        window.dispatchEvent(new CustomEvent('GYMSYSTEM_SW_UPDATE_APPLIED'));
        reportPwaEvent('sw_update_applied');
    });
}

window.applyGymSwUpdate = async () => {
    if (!('serviceWorker' in navigator)) {
        window.location.reload();
        return false;
    }

    reportPwaEvent('sw_update_apply_clicked');

    const swMetaUrl = document.querySelector('meta[name="sw-url"]')?.getAttribute('content');
    const swUrl = (swMetaUrl && swMetaUrl.trim() !== '') ? swMetaUrl : '/sw.js';
    let registration = await navigator.serviceWorker.getRegistration(swUrl).catch(() => null);
    if (!registration) {
        registration = await navigator.serviceWorker.getRegistration().catch(() => null);
    }
    if (!registration) {
        window.location.reload();
        return false;
    }

    if (registration.waiting) {
        registration.waiting.postMessage({ type: 'SKIP_WAITING' });
        return true;
    }

    await registration.update().catch(() => {
        // Keep silent.
    });

    if (registration.waiting) {
        registration.waiting.postMessage({ type: 'SKIP_WAITING' });
        return true;
    }

    window.location.reload();
    return false;
};

initCashIndexModule();
initPasswordVisibilityModule();
