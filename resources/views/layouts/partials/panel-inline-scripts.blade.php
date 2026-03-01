<script>
    (function () {
        const sidebarToggle = document.getElementById('sidebar-toggle');
        const sidebar = document.getElementById('panel-sidebar');
        const sidebarToggleLabel = sidebarToggle?.querySelector('.panel-menu-trigger-label');

        function syncSidebarToggleUi(collapsed) {
            if (!sidebarToggle) return;
            const label = collapsed ? 'Abrir menu' : 'Ocultar menu';
            const ariaLabel = collapsed ? 'Abrir menu' : 'Ocultar menu';
            if (sidebarToggleLabel) {
                sidebarToggleLabel.textContent = label;
            }
            sidebarToggle.setAttribute('aria-label', ariaLabel);
            sidebarToggle.setAttribute('title', ariaLabel);
        }

        sidebarToggle?.addEventListener('click', function () {
            if (!sidebar) return;
            const collapsed = sidebar.classList.toggle('sidebar-collapsed');
            sidebar.classList.toggle('lg:w-64', !collapsed);
            sidebar.classList.toggle('lg:w-20', collapsed);
            sidebar.querySelectorAll('.sidebar-label').forEach(function (element) {
                element.classList.toggle('hidden', collapsed);
            });
            localStorage.setItem('panel.sidebar_collapsed', collapsed ? '1' : '0');
            syncSidebarToggleUi(collapsed);
        });

        if (sidebar && localStorage.getItem('panel.sidebar_collapsed') === '1') {
            sidebarToggle?.click();
        }
        syncSidebarToggleUi(sidebar?.classList.contains('sidebar-collapsed') ?? false);

        const userMenuRoot = document.getElementById('user-menu-root');
        const userMenuButton = document.getElementById('user-menu-button');
        const userMenuDropdown = document.getElementById('user-menu-dropdown');
        const headerBellRoot = document.getElementById('header-bell-root');
        const headerBellButton = document.getElementById('header-bell-button');
        const headerBellDropdown = document.getElementById('header-bell-dropdown');
        const pwaInstallButton = document.getElementById('pwa-install-button');
        const pushNotificationsButton = document.getElementById('push-notifications-button');
        const pushNotificationsStateBadge = document.getElementById('push-notifications-state');
        const pwaAccessAlert = document.getElementById('pwa-access-alert');
        const pushAccessAlert = document.getElementById('push-access-alert');
        const pwaInstallEnabled = pwaInstallButton?.getAttribute('data-pwa-enabled') === '1';
        const pwaUpgradeMessage = @json($pwaUpgradeMessage);
        const currentUserId = Number(@json((int) ($user?->id ?? 0)));
        const pushWebEnabled = document.querySelector('meta[name="push-web-enabled"]')?.getAttribute('content') === '1';
        const pushVapidPublicKey = (document.querySelector('meta[name="push-vapid-public-key"]')?.getAttribute('content') || '').trim();
        const pushSubscribeUrl = document.querySelector('meta[name="push-subscribe-url"]')?.getAttribute('content') || '';
        const pushUnsubscribeUrl = document.querySelector('meta[name="push-unsubscribe-url"]')?.getAttribute('content') || '';
        const pushStatusUrl = document.querySelector('meta[name="push-status-url"]')?.getAttribute('content') || '';
        const pushTestUrl = document.querySelector('meta[name="push-test-url"]')?.getAttribute('content') || '';
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
        const panelToastStack = document.querySelector('.panel-toast-stack');
        const uiLoadingOverlay = document.getElementById('ui-loading-overlay');
        const uiLoadingMessage = document.getElementById('ui-loading-message');
        let pwaInstallPromptEvent = null;
        let pwaAlertTimeoutId = null;
        let pushAlertTimeoutId = null;
        let pushUnsupportedReason = '';
        let uiLoadingTimeoutId = null;
        let uiLoadingReleaseId = null;
        let uiNavigationStarted = false;

        function showUiLoading(message, withFailsafe = false) {
            if (!uiLoadingOverlay) return;
            if (uiLoadingTimeoutId) {
                window.clearTimeout(uiLoadingTimeoutId);
                uiLoadingTimeoutId = null;
            }
            uiLoadingOverlay.setAttribute('data-open', '1');
            uiLoadingOverlay.setAttribute('aria-hidden', 'false');
            if (uiLoadingMessage && typeof message === 'string' && message.trim() !== '') {
                uiLoadingMessage.textContent = message.trim();
            }
            if (withFailsafe) {
                uiLoadingTimeoutId = window.setTimeout(function () {
                    hideUiLoading();
                    showPushAccessAlert('La accion esta tardando mas de lo esperado. Intenta de nuevo.', 'warning');
                }, 15000);
            }
        }

        function hideUiLoading() {
            if (!uiLoadingOverlay) return;
            if (uiLoadingTimeoutId) {
                window.clearTimeout(uiLoadingTimeoutId);
                uiLoadingTimeoutId = null;
            }
            uiLoadingOverlay.setAttribute('data-open', '0');
            uiLoadingOverlay.setAttribute('aria-hidden', 'true');
            if (uiLoadingMessage) {
                uiLoadingMessage.textContent = 'Procesando solicitud...';
            }
        }

        function clearUiLoadingRelease() {
            if (uiLoadingReleaseId) {
                window.clearTimeout(uiLoadingReleaseId);
                uiLoadingReleaseId = null;
            }
        }

        function markUiNavigationStarted() {
            uiNavigationStarted = true;
            clearUiLoadingRelease();
        }

        function showUiLoadingForNavigation(message) {
            uiNavigationStarted = false;
            clearUiLoadingRelease();
            showUiLoading(message, false);
            uiLoadingReleaseId = window.setTimeout(function () {
                if (!uiNavigationStarted && document.visibilityState === 'visible') {
                    hideUiLoading();
                }
            }, 1800);
        }

        function isStandaloneMode() {
            const mediaMatch = window.matchMedia && window.matchMedia('(display-mode: standalone)').matches;
            const iosStandalone = window.navigator && window.navigator.standalone === true;

            return Boolean(mediaMatch || iosStandalone);
        }

        function hidePwaInstallButton() {
            if (!pwaInstallButton) return;
            pwaInstallButton.classList.add('hidden');
            pwaInstallButton.setAttribute('aria-hidden', 'true');
            pwaInstallButton.setAttribute('tabindex', '-1');
        }

        function showPwaInstallButton() {
            if (!pwaInstallButton) return;
            pwaInstallButton.classList.remove('hidden');
            pwaInstallButton.classList.add('lg:inline-flex');
            pwaInstallButton.removeAttribute('aria-hidden');
            pwaInstallButton.removeAttribute('tabindex');
        }

        function getPwaInstalledStorageKey() {
            return ['pwa-installed', window.location.host].join(':');
        }

        function markPwaInstalled() {
            try {
                window.localStorage.setItem(getPwaInstalledStorageKey(), '1');
            } catch (_error) {
                // Keep silent.
            }
        }

        function wasPwaInstalledBefore() {
            try {
                return window.localStorage.getItem(getPwaInstalledStorageKey()) === '1';
            } catch (_error) {
                return false;
            }
        }

        function showPwaAccessAlert(message, variant = 'warning') {
            if (!pwaAccessAlert) return;
            pwaAccessAlert.textContent = String(message || '').trim();
            pwaAccessAlert.classList.remove('hidden', 'ui-alert-warning', 'ui-alert-danger', 'ui-alert-success');
            pwaAccessAlert.classList.add(variant === 'danger' ? 'ui-alert-danger' : 'ui-alert-warning');
            if (pwaAlertTimeoutId) {
                window.clearTimeout(pwaAlertTimeoutId);
            }
            pwaAlertTimeoutId = window.setTimeout(function () {
                pwaAccessAlert.classList.add('hidden');
            }, 6500);
        }
        function showPushAccessAlert(message, variant = 'info') {
            if (!pushAccessAlert) return;
            pushAccessAlert.textContent = String(message || '').trim();
            pushAccessAlert.classList.remove('hidden', 'ui-alert-info', 'ui-alert-warning', 'ui-alert-danger', 'ui-alert-success');
            const resolvedVariant = ['warning', 'danger', 'success'].includes(variant) ? variant : 'info';
            pushAccessAlert.classList.add('ui-alert-' + resolvedVariant);
            if (pushAlertTimeoutId) {
                window.clearTimeout(pushAlertTimeoutId);
            }
            pushAlertTimeoutId = window.setTimeout(function () {
                pushAccessAlert.classList.add('hidden');
            }, 6500);
        }

        function isDesktopPushToastContext() {
            if (!window.matchMedia) return true;
            return window.matchMedia('(min-width: 1024px)').matches;
        }

        function scheduleRuntimeToastRemoval(toast, delayMs) {
            const safeDelay = Number.isFinite(delayMs) ? Math.max(1200, Number(delayMs)) : 6500;
            window.setTimeout(function () {
                toast.classList.add('opacity-0', 'translate-y-1', 'transition');
                window.setTimeout(function () {
                    toast.remove();
                }, 260);
            }, safeDelay);
        }

        function showPushDetailModal(title, shortMessage, detailText) {
            const text = typeof detailText === 'string' ? detailText.trim() : '';
            if (text === '') return;

            const existing = document.getElementById('push-detail-overlay');
            if (existing) {
                existing.remove();
            }

            const overlay = document.createElement('div');
            overlay.id = 'push-detail-overlay';
            overlay.className = 'fixed inset-0 z-[120] flex items-center justify-center bg-slate-950/70 p-4';

            const dialog = document.createElement('div');
            dialog.className = 'w-full max-w-lg rounded-2xl border border-cyan-300/40 bg-slate-950 p-4 text-slate-100 shadow-2xl';

            const heading = document.createElement('h3');
            heading.className = 'text-base font-black';
            heading.textContent = typeof title === 'string' && title.trim() !== '' ? title.trim() : 'Notificacion';
            dialog.appendChild(heading);

            if (typeof shortMessage === 'string' && shortMessage.trim() !== '') {
                const subtitle = document.createElement('p');
                subtitle.className = 'mt-1 text-xs text-cyan-200';
                subtitle.textContent = shortMessage.trim();
                dialog.appendChild(subtitle);
            }

            const body = document.createElement('p');
            body.className = 'mt-3 whitespace-pre-wrap rounded-xl border border-slate-700/70 bg-slate-900/80 p-3 text-sm leading-relaxed';
            body.textContent = text;
            dialog.appendChild(body);

            const actions = document.createElement('div');
            actions.className = 'mt-4 flex justify-end';

            const closeBtn = document.createElement('button');
            closeBtn.type = 'button';
            closeBtn.className = 'inline-flex items-center rounded-lg border border-cyan-300/60 px-3 py-1.5 text-xs font-bold uppercase tracking-[0.06em] text-cyan-200 transition hover:bg-cyan-900/30';
            closeBtn.textContent = 'Cerrar';
            closeBtn.addEventListener('click', function () {
                overlay.remove();
            });
            actions.appendChild(closeBtn);

            dialog.appendChild(actions);
            overlay.appendChild(dialog);
            overlay.addEventListener('click', function (event) {
                if (event.target === overlay) {
                    overlay.remove();
                }
            });

            document.body.appendChild(overlay);
        }

        function showDesktopPushToast(pushPayload) {
            const payload = (pushPayload && typeof pushPayload === 'object') ? pushPayload : {};

            if (!isDesktopPushToastContext()) {
                const compactTitle = typeof payload.title === 'string' && payload.title.trim() !== '' ? payload.title.trim() : 'GymSystem';
                const compactBody = typeof payload.body === 'string' ? payload.body.trim() : '';
                showPushAccessAlert(compactBody !== '' ? compactTitle + ': ' + compactBody : compactTitle, 'info');
                return;
            }
            if (document.visibilityState !== 'visible') return;
            const rawData = (payload.data && typeof payload.data === 'object') ? payload.data : {};
            const title = typeof payload.title === 'string' && payload.title.trim() !== ''
                ? payload.title.trim()
                : 'GymSystem';
            const body = typeof payload.body === 'string' ? payload.body.trim() : '';
            const detailText = typeof rawData.detail_text === 'string' ? rawData.detail_text.trim() : '';

            const toast = document.createElement('div');
            toast.setAttribute('data-toast', '1');
            toast.setAttribute('data-autohide', '1');
            toast.setAttribute('data-delay', '7000');
            toast.className = 'ui-alert ui-alert-info';

            const titleEl = document.createElement('p');
            titleEl.className = 'text-sm font-black';
            titleEl.textContent = title;
            toast.appendChild(titleEl);

            if (body !== '') {
                const bodyEl = document.createElement('p');
                bodyEl.className = 'mt-1 text-xs';
                bodyEl.textContent = body;
                toast.appendChild(bodyEl);
            }

            const actions = document.createElement('div');
            actions.className = 'mt-2 flex items-center justify-between gap-2';

            const hint = document.createElement('span');
            hint.className = 'text-[11px] font-semibold uppercase tracking-[0.06em] text-slate-500 dark:text-slate-300';
            hint.textContent = 'Notificacion recibida';
            actions.appendChild(hint);

            if (detailText !== '') {
                const openButton = document.createElement('button');
                openButton.type = 'button';
                openButton.className = 'inline-flex items-center rounded-md border border-cyan-300/60 px-2.5 py-1 text-[11px] font-bold uppercase tracking-[0.06em] text-cyan-800 transition hover:bg-cyan-100/60 dark:border-cyan-500/55 dark:text-cyan-200 dark:hover:bg-cyan-900/30';
                openButton.textContent = 'Abrir';
                openButton.addEventListener('click', function () {
                    showPushDetailModal(title, body, detailText);
                });
                actions.appendChild(openButton);
            }

            toast.appendChild(actions);
            const toastContainer = panelToastStack || (function () {
                const fallback = document.createElement('div');
                fallback.className = 'panel-toast-stack';
                fallback.setAttribute('aria-live', 'polite');
                fallback.setAttribute('aria-atomic', 'true');
                document.body.appendChild(fallback);
                return fallback;
            })();
            toastContainer.prepend(toast);

            const visibleToasts = toastContainer.querySelectorAll('[data-toast]');
            if (visibleToasts.length > 4) {
                for (let i = 4; i < visibleToasts.length; i += 1) {
                    visibleToasts[i].remove();
                }
            }

            scheduleRuntimeToastRemoval(toast, 7000);
        }

        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.addEventListener('message', function (event) {
                const message = event && event.data ? event.data : null;
                if (!message || message.type !== 'GYMSYSTEM_PUSH_EVENT') return;
                showDesktopPushToast(message.payload);
            });
        }

        if (pwaInstallButton) {
            if (isStandaloneMode()) {
                markPwaInstalled();
                hidePwaInstallButton();
            } else {
                showPwaInstallButton();
            }

            if (!pwaInstallEnabled) {
                pwaInstallButton.classList.add('border-amber-300/70', 'text-amber-200');
            }

            window.addEventListener('beforeinstallprompt', function (event) {
                event.preventDefault();
                if (!pwaInstallEnabled) {
                    showPwaAccessAlert(pwaUpgradeMessage, 'warning');
                    return;
                }
                pwaInstallPromptEvent = event;
                pwaInstallButton.textContent = 'Instalar app';
            });

            window.addEventListener('appinstalled', function () {
                pwaInstallPromptEvent = null;
                markPwaInstalled();
                hidePwaInstallButton();
                showPwaAccessAlert('Aplicacion instalada correctamente.', 'warning');
            });

            pwaInstallButton.addEventListener('click', async function () {
                if (!pwaInstallEnabled) {
                    showPwaAccessAlert(pwaUpgradeMessage, 'warning');
                    return;
                }

                if (!pwaInstallPromptEvent) {
                    if (wasPwaInstalledBefore()) {
                        showPwaAccessAlert('Ya instalaste la app. Revisa tu pantalla de inicio o lista de aplicaciones instaladas.', 'warning');
                        return;
                    }
                    showPwaAccessAlert('Tu navegador no habilito el instalador automatico. Usa "Agregar a pantalla de inicio".', 'warning');
                    return;
                }

                pwaInstallPromptEvent.prompt();
                try {
                    await pwaInstallPromptEvent.userChoice;
                } catch (_error) {
                    // Keep silent.
                }
                pwaInstallPromptEvent = null;
            });

            if (!pwaInstallEnabled && isStandaloneMode()) {
                showPwaAccessAlert(pwaUpgradeMessage, 'danger');
            }
        }
        function urlBase64ToUint8Array(base64String) {
            const normalized = String(base64String || '').trim().replace(/\s+/g, '');
            if (normalized === '') {
                throw new Error('Falta WEBPUSH_VAPID_PUBLIC_KEY en .env.');
            }
            if (!/^[A-Za-z0-9\-_]+$/.test(normalized)) {
                throw new Error('WEBPUSH_VAPID_PUBLIC_KEY invalida. Regenera llaves VAPID.');
            }

            const padding = '='.repeat((4 - (normalized.length % 4)) % 4);
            const base64 = (normalized + padding).replace(/-/g, '+').replace(/_/g, '/');
            let rawData = '';
            try {
                rawData = window.atob(base64);
            } catch (_error) {
                throw new Error('WEBPUSH_VAPID_PUBLIC_KEY invalida. Ejecuta notifications:webpush-keys y actualiza .env.');
            }
            const outputArray = new Uint8Array(rawData.length);
            for (let i = 0; i < rawData.length; i += 1) {
                outputArray[i] = rawData.charCodeAt(i);
            }

            return outputArray;
        }

        async function postJson(url, payload) {
            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                },
                credentials: 'same-origin',
                body: JSON.stringify(payload || {}),
            });
            const data = await response.json().catch(function () {
                return { ok: false, message: 'Respuesta invalida del servidor.' };
            });
            if (!response.ok || data.ok === false) {
                const errorMessage = (data && data.message) ? String(data.message) : 'No se pudo completar la operacion.';
                throw new Error(errorMessage);
            }

            return data;
        }

        function updatePushButtonState(state) {
            if (!pushNotificationsButton) return;

            function setBadge(label, tone) {
                if (!pushNotificationsStateBadge) return;
                pushNotificationsStateBadge.textContent = label;
                pushNotificationsStateBadge.classList.remove(
                    'border-slate-300/80', 'text-slate-600', 'dark:border-slate-600', 'dark:text-slate-300',
                    'border-emerald-300/80', 'text-emerald-700', 'dark:border-emerald-500/70', 'dark:text-emerald-300',
                    'border-rose-300/80', 'text-rose-700', 'dark:border-rose-500/70', 'dark:text-rose-300',
                    'border-amber-300/80', 'text-amber-700', 'dark:border-amber-500/70', 'dark:text-amber-300'
                );
                if (tone === 'success') {
                    pushNotificationsStateBadge.classList.add('border-emerald-300/80', 'text-emerald-700', 'dark:border-emerald-500/70', 'dark:text-emerald-300');
                    return;
                }
                if (tone === 'danger') {
                    pushNotificationsStateBadge.classList.add('border-rose-300/80', 'text-rose-700', 'dark:border-rose-500/70', 'dark:text-rose-300');
                    return;
                }
                if (tone === 'warning') {
                    pushNotificationsStateBadge.classList.add('border-amber-300/80', 'text-amber-700', 'dark:border-amber-500/70', 'dark:text-amber-300');
                    return;
                }
                pushNotificationsStateBadge.classList.add('border-slate-300/80', 'text-slate-600', 'dark:border-slate-600', 'dark:text-slate-300');
            }

            if (state === 'unsupported') {
                pushNotificationsButton.classList.add('opacity-60');
                pushNotificationsButton.classList.remove('cursor-not-allowed', 'border-emerald-300/70', 'text-emerald-200', 'border-rose-300/80', 'text-rose-300');
                pushNotificationsButton.setAttribute('data-push-unsupported', '1');
                pushNotificationsButton.removeAttribute('disabled');
                setBadge('No disp.', 'warning');
                return;
            }

            if (state === 'denied') {
                pushNotificationsButton.classList.add('border-rose-300/80', 'text-rose-300');
                pushNotificationsButton.removeAttribute('data-push-unsupported');
                pushNotificationsButton.removeAttribute('disabled');
                setBadge('Bloqueadas', 'danger');
                return;
            }

            if (state === 'active') {
                pushNotificationsButton.classList.add('border-emerald-300/70', 'text-emerald-200');
                pushNotificationsButton.classList.remove('border-rose-300/80', 'text-rose-300', 'opacity-60', 'cursor-not-allowed');
                pushNotificationsButton.removeAttribute('data-push-unsupported');
                pushNotificationsButton.removeAttribute('disabled');
                setBadge('Activas', 'success');
                return;
            }

            pushNotificationsButton.classList.remove('border-emerald-300/70', 'text-emerald-200', 'border-rose-300/80', 'text-rose-300', 'opacity-60', 'cursor-not-allowed');
            pushNotificationsButton.removeAttribute('data-push-unsupported');
            pushNotificationsButton.removeAttribute('disabled');
            setBadge('Apagadas', 'neutral');
        }

        function getPushOnboardingStorageKey() {
            const userKey = Number.isFinite(currentUserId) && currentUserId > 0 ? String(currentUserId) : 'guest';
            return ['push-onboarding-dismissed', window.location.host, userKey].join(':');
        }

        function markPushOnboardingDismissed() {
            try {
                window.localStorage.setItem(getPushOnboardingStorageKey(), '1');
            } catch (_error) {
                // Keep silent.
            }
        }

        function isPushOnboardingDismissed() {
            try {
                return window.localStorage.getItem(getPushOnboardingStorageKey()) === '1';
            } catch (_error) {
                return false;
            }
        }

        function closePushOnboardingModal() {
            const overlay = document.getElementById('push-onboarding-overlay');
            if (overlay) {
                overlay.remove();
            }
        }

        function showPushOnboardingModal(onAccept) {
            if (document.getElementById('push-onboarding-overlay')) return;

            const overlay = document.createElement('div');
            overlay.id = 'push-onboarding-overlay';
            overlay.className = 'fixed inset-0 z-[130] flex items-center justify-center bg-slate-950/70 p-4';

            const dialog = document.createElement('div');
            dialog.className = 'w-full max-w-xl rounded-2xl border border-cyan-300/40 bg-slate-950 p-5 text-slate-100 shadow-2xl';

            const title = document.createElement('h3');
            title.className = 'text-lg font-black';
            title.textContent = 'Activa notificaciones de GymSystem';
            dialog.appendChild(title);

            const subtitle = document.createElement('p');
            subtitle.className = 'mt-2 text-sm text-cyan-100';
            subtitle.textContent = 'Te avisaremos en tiempo real para que no pierdas eventos importantes.';
            dialog.appendChild(subtitle);

            const benefits = document.createElement('ul');
            benefits.className = 'mt-3 space-y-1.5 text-sm text-slate-200';
            [
                'Recordatorios de renovaciones y vencimientos.',
                'Alertas operativas importantes de tu gimnasio.',
                'Mensajes inmediatos sin tener que recargar la pagina.',
            ].forEach(function (item) {
                const li = document.createElement('li');
                li.className = 'flex items-start gap-2';
                li.innerHTML = '<span class="mt-1 inline-block h-1.5 w-1.5 rounded-full bg-cyan-300"></span><span>' + item + '</span>';
                benefits.appendChild(li);
            });
            dialog.appendChild(benefits);

            const actions = document.createElement('div');
            actions.className = 'mt-5 flex flex-wrap justify-end gap-2';

            const skipBtn = document.createElement('button');
            skipBtn.type = 'button';
            skipBtn.className = 'inline-flex items-center rounded-lg border border-slate-500/70 px-3 py-2 text-xs font-bold uppercase tracking-[0.06em] text-slate-200 transition hover:bg-slate-800';
            skipBtn.textContent = 'Ahora no';
            skipBtn.addEventListener('click', function () {
                markPushOnboardingDismissed();
                closePushOnboardingModal();
            });
            actions.appendChild(skipBtn);

            const acceptBtn = document.createElement('button');
            acceptBtn.type = 'button';
            acceptBtn.className = 'inline-flex items-center rounded-lg border border-cyan-300/80 bg-cyan-500/15 px-3 py-2 text-xs font-bold uppercase tracking-[0.06em] text-cyan-100 transition hover:bg-cyan-500/25';
            acceptBtn.textContent = 'Permitir notificaciones';
            acceptBtn.addEventListener('click', function () {
                markPushOnboardingDismissed();
                closePushOnboardingModal();
                if (typeof onAccept === 'function') {
                    onAccept();
                }
            });
            actions.appendChild(acceptBtn);

            dialog.appendChild(actions);
            overlay.appendChild(dialog);
            overlay.addEventListener('click', function (event) {
                if (event.target === overlay) {
                    markPushOnboardingDismissed();
                    closePushOnboardingModal();
                }
            });
            document.body.appendChild(overlay);
        }

        async function bootstrapPushNotifications() {
            if (!pushNotificationsButton) {
                return;
            }

            async function resolvePushServiceWorkerRegistration() {
                const swMetaUrl = document.querySelector('meta[name="sw-url"]')?.getAttribute('content') || '';
                const swUrl = swMetaUrl.trim() !== '' ? swMetaUrl : '/sw.js';

                let registration = await navigator.serviceWorker.getRegistration(swUrl).catch(function () {
                    return null;
                });
                if (!registration) {
                    registration = await navigator.serviceWorker.getRegistration().catch(function () {
                        return null;
                    });
                }
                if (!registration) {
                    registration = await navigator.serviceWorker.register(swUrl).catch(function () {
                        return null;
                    });
                }

                return registration;
            }

            pushNotificationsButton.addEventListener('click', function () {
                if (pushNotificationsButton.getAttribute('data-push-unsupported') === '1') {
                    showPushAccessAlert(
                        pushUnsupportedReason !== ''
                            ? pushUnsupportedReason
                            : 'Push no disponible en este entorno.',
                        'warning'
                    );
                }
            });

            const hasPushSupport = ('serviceWorker' in navigator) && ('PushManager' in window) && ('Notification' in window);
            if (!hasPushSupport) {
                pushUnsupportedReason = 'Tu navegador no soporta notificaciones push.';
                updatePushButtonState('unsupported');
                return;
            }

            if (!window.isSecureContext) {
                pushUnsupportedReason = 'Push requiere HTTPS (o localhost) para funcionar.';
                updatePushButtonState('unsupported');
                showPushAccessAlert(pushUnsupportedReason, 'warning');
                return;
            }

            if (!pushWebEnabled || pushVapidPublicKey === '') {
                pushUnsupportedReason = 'Falta configurar WEBPUSH_ENABLED y llaves VAPID en .env.';
                updatePushButtonState('unsupported');
                showPushAccessAlert(pushUnsupportedReason, 'warning');
                return;
            }

            try {
                urlBase64ToUint8Array(pushVapidPublicKey);
            } catch (error) {
                pushUnsupportedReason = error instanceof Error ? error.message : 'WEBPUSH_VAPID_PUBLIC_KEY invalida.';
                updatePushButtonState('unsupported');
                showPushAccessAlert(pushUnsupportedReason, 'warning');
                return;
            }

            const statusOnServer = pushStatusUrl !== ''
                ? await fetch(pushStatusUrl, {
                    headers: { 'Accept': 'application/json' },
                    credentials: 'same-origin',
                }).then(function (response) {
                    return response.ok ? response.json() : null;
                }).catch(function () {
                    return null;
                })
                : null;

            let registration = await resolvePushServiceWorkerRegistration();
            let currentSubscription = registration
                ? await registration.pushManager.getSubscription().catch(function () {
                    return null;
                })
                : null;

            if (Notification.permission === 'denied') {
                updatePushButtonState('denied');
            } else if (currentSubscription) {
                updatePushButtonState('active');
            } else {
                updatePushButtonState('idle');
            }

            if (statusOnServer && statusOnServer.webpush_ready === false) {
                showPushAccessAlert('El servidor aun no tiene llaves VAPID activas.', 'warning');
            }

            pushNotificationsButton.addEventListener('click', async function () {
                if (pushNotificationsButton.getAttribute('data-push-unsupported') === '1') {
                    return;
                }

                showUiLoading('Configurando notificaciones...', true);
                pushNotificationsButton.setAttribute('disabled', 'disabled');

                try {
                    registration = await resolvePushServiceWorkerRegistration();
                    if (!registration || !registration.pushManager) {
                        throw new Error('No se pudo inicializar Service Worker para push. Recarga la pagina.');
                    }

                    currentSubscription = await registration.pushManager.getSubscription();

                    if (currentSubscription) {
                        if (pushUnsubscribeUrl !== '') {
                            await postJson(pushUnsubscribeUrl, {
                                endpoint: currentSubscription.endpoint,
                            });
                        }
                        await currentSubscription.unsubscribe().catch(function () {
                            // Keep backend cleanup as source of truth.
                        });
                        updatePushButtonState('idle');
                        showPushAccessAlert('Notificaciones push desactivadas para este dispositivo.', 'success');

                        return;
                    }

                    const permissionResult = await Notification.requestPermission();
                    if (permissionResult !== 'granted') {
                        updatePushButtonState(permissionResult === 'denied' ? 'denied' : 'idle');
                        showPushAccessAlert('No se concedio permiso de notificaciones.', 'warning');

                        return;
                    }

                    const applicationServerKey = urlBase64ToUint8Array(pushVapidPublicKey);
                    currentSubscription = await registration.pushManager.subscribe({
                        userVisibleOnly: true,
                        applicationServerKey: applicationServerKey,
                    });

                    if (pushSubscribeUrl !== '') {
                        const supportedEncodings = Array.isArray(PushManager.supportedContentEncodings)
                            ? PushManager.supportedContentEncodings
                            : [];
                        const resolvedEncoding = supportedEncodings.includes('aes128gcm') ? 'aes128gcm' : 'aesgcm';
                        await postJson(pushSubscribeUrl, {
                            subscription: currentSubscription.toJSON(),
                            encoding: resolvedEncoding,
                            device_name: window.navigator.platform || null,
                        });
                    }

                    updatePushButtonState('active');
                    showPushAccessAlert('Notificaciones push activadas correctamente.', 'success');

                    if (pushTestUrl !== '') {
                        await postJson(pushTestUrl, {});
                        showPushAccessAlert('Se envio una notificacion de prueba a este dispositivo.', 'success');
                    }
                } catch (error) {
                    const message = error instanceof Error ? error.message : 'No se pudo activar notificaciones push.';
                    showPushAccessAlert(message, 'danger');
                    updatePushButtonState(Notification.permission === 'denied' ? 'denied' : 'idle');
                } finally {
                    if (Notification.permission !== 'denied') {
                        pushNotificationsButton.removeAttribute('disabled');
                    }
                    hideUiLoading();
                }
            });

            const shouldShowOnboarding = Notification.permission === 'default'
                && !currentSubscription
                && !isPushOnboardingDismissed();

            if (shouldShowOnboarding) {
                window.setTimeout(function () {
                    showPushOnboardingModal(function () {
                        if (pushNotificationsButton) {
                            pushNotificationsButton.click();
                        }
                    });
                }, 850);
            }
        }

        bootstrapPushNotifications().catch(function () {
            updatePushButtonState('unsupported');
        });

        function closeUserMenu() {
            if (!userMenuDropdown || !userMenuButton) return;
            userMenuDropdown.classList.add('hidden');
            userMenuButton.setAttribute('aria-expanded', 'false');
        }

        function openUserMenu() {
            if (!userMenuDropdown || !userMenuButton) return;
            userMenuDropdown.classList.remove('hidden');
            userMenuButton.setAttribute('aria-expanded', 'true');
        }
        function closeBellMenu() {
            if (!headerBellDropdown || !headerBellButton) return;
            headerBellDropdown.classList.add('hidden');
            headerBellButton.setAttribute('aria-expanded', 'false');
        }

        function openBellMenu() {
            if (!headerBellDropdown || !headerBellButton) return;
            headerBellDropdown.classList.remove('hidden');
            headerBellButton.setAttribute('aria-expanded', 'true');
        }

        userMenuButton?.addEventListener('click', function (event) {
            event.preventDefault();
            event.stopPropagation();
            closeBellMenu();
            if (userMenuDropdown?.classList.contains('hidden')) {
                openUserMenu();
            } else {
                closeUserMenu();
            }
        });
        headerBellButton?.addEventListener('click', function (event) {
            event.preventDefault();
            event.stopPropagation();
            closeUserMenu();
            if (headerBellDropdown?.classList.contains('hidden')) {
                openBellMenu();
            } else {
                closeBellMenu();
            }
        });

        document.addEventListener('click', function (event) {
            const target = event.target;
            if (! (target instanceof Node)) {
                closeUserMenu();
                closeBellMenu();
                return;
            }

            if (userMenuRoot && userMenuDropdown && userMenuRoot.contains(target)) {
                return;
            }
            if (headerBellRoot && headerBellDropdown && headerBellRoot.contains(target)) {
                return;
            }
            closeUserMenu();
            closeBellMenu();
        });

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape') {
                closeUserMenu();
                closeBellMenu();
            }
        });

        function shouldIgnoreLinkForLoading(anchor, clickEvent) {
            if (!(anchor instanceof HTMLAnchorElement)) return true;
            if (anchor.hasAttribute('download')) return true;
            if (anchor.getAttribute('data-ui-loading-ignore') === '1') return true;
            if (anchor.getAttribute('role') === 'button') return true;
            if (anchor.hasAttribute('aria-controls')) return true;
            const linkTarget = (anchor.getAttribute('target') || '').trim().toLowerCase();
            if (linkTarget !== '' && linkTarget !== '_self') return true;
            if (clickEvent.defaultPrevented) return true;
            if (clickEvent.button !== 0) return true;
            if (clickEvent.metaKey || clickEvent.ctrlKey || clickEvent.shiftKey || clickEvent.altKey) return true;

            const href = (anchor.getAttribute('href') || '').trim();
            if (href === '' || href.startsWith('#') || href.startsWith('javascript:')) return true;

            let parsed;
            try {
                parsed = new URL(anchor.href, window.location.href);
            } catch (_error) {
                return true;
            }

            if (parsed.origin !== window.location.origin) return true;
            if (parsed.href === window.location.href) return true;
            if (parsed.pathname === window.location.pathname && parsed.search === window.location.search && parsed.hash !== '') return true;

            return false;
        }

        document.addEventListener('click', function (event) {
            const target = event.target;
            if (!(target instanceof Element)) return;
            const anchor = target.closest('a[href]');
            if (!anchor) return;
            if (shouldIgnoreLinkForLoading(anchor, event)) return;
            showUiLoadingForNavigation('Cargando pagina...');
        });

        document.addEventListener('submit', function (event) {
            const form = event.target;
            if (!(form instanceof HTMLFormElement)) return;
            if (event.defaultPrevented) return;
            if (form.getAttribute('data-ui-loading-ignore') === '1') return;
            const formTarget = (form.getAttribute('target') || '').trim().toLowerCase();
            if (formTarget !== '' && formTarget !== '_self') return;
            if ((form.getAttribute('method') || '').toLowerCase() === 'dialog') return;

            const customEnabled = form.getAttribute('data-ui-loading-overlay') === '1';
            const message = customEnabled
                ? (form.getAttribute('data-ui-loading-message') || 'Cargando...')
                : 'Procesando...';
            showUiLoadingForNavigation(message);
        });

        window.addEventListener('popstate', function () {
            showUiLoadingForNavigation('Cargando pagina...');
        });
        window.addEventListener('beforeunload', function () {
            markUiNavigationStarted();
        });
        window.addEventListener('pagehide', function (event) {
            if (!event.persisted) {
                markUiNavigationStarted();
            }
        });

        // Safety net: avoid a stuck overlay if some script crashes during navigation.
        window.addEventListener('error', function () {
            hideUiLoading();
        });
        window.addEventListener('unhandledrejection', function () {
            hideUiLoading();
        });

        window.addEventListener('pageshow', function () {
            uiNavigationStarted = false;
            clearUiLoadingRelease();
            hideUiLoading();
        });

        document.querySelectorAll('[data-toast]').forEach(function (toast) {
            const shouldHide = toast.getAttribute('data-autohide') === '1';
            if (!shouldHide) return;

            const delay = Number(toast.getAttribute('data-delay') || 4200);
            setTimeout(function () {
                toast.classList.add('opacity-0', 'translate-y-1', 'transition');
                setTimeout(function () {
                    toast.remove();
                }, 250);
            }, delay);
        });

        function normalizeText(value) {
            return (value || '')
                .toString()
                .toLowerCase()
                .normalize('NFD')
                .replace(/[\u0300-\u036f]/g, '');
        }

        function getBodyRows(table) {
            const rows = table.querySelectorAll('tbody tr');
            return Array.from(rows).filter(function (row) {
                return row.querySelector('td') !== null;
            });
        }

        function enhanceSmartList(table, index) {
            if (table.hasAttribute('data-smart-list-manual')) return;
            if (table.dataset.smartListReady === '1') return;

            const rows = getBodyRows(table);
            if (rows.length <= 10) return;

            const wrapper = table.closest('.overflow-x-auto') || table.parentElement;
            if (!wrapper) return;

            table.dataset.smartListReady = '1';
            wrapper.classList.add('smart-list-wrap');

            const toolbar = document.createElement('div');
            toolbar.className = 'smart-list-toolbar';
            const smartListSearchLabel = @js(__('ui.smart_list.search_label'));
            const smartListSearchPlaceholder = @js(__('ui.smart_list.search_placeholder'));
            const smartListShowing = @js(__('ui.smart_list.showing'));
            const smartListOf = @js(__('ui.smart_list.of'));
            const smartListNoResults = @js(__('ui.smart_list.no_results'));
            toolbar.innerHTML =
                '<label class="space-y-1 text-sm font-semibold ui-muted">' +
                    '<span>' + smartListSearchLabel + '</span>' +
                    '<input type="text" class="ui-input js-smart-list-search" placeholder="' + smartListSearchPlaceholder + '" autocomplete="off">' +
                '</label>' +
                '<p class="smart-list-counter">' + smartListShowing + ' <strong class="js-smart-list-visible">' + rows.length + '</strong> ' + smartListOf + ' <strong>' + rows.length + '</strong></p>';

            wrapper.parentNode.insertBefore(toolbar, wrapper);

            const searchInput = toolbar.querySelector('.js-smart-list-search');
            const visibleEl = toolbar.querySelector('.js-smart-list-visible');

            const emptyRow = document.createElement('tr');
            emptyRow.className = 'hidden';
            emptyRow.innerHTML = '<td colspan="' + (table.querySelectorAll('thead th').length || 1) + '" class="px-3 py-6 text-center text-sm text-slate-500 dark:text-slate-300">' + smartListNoResults + '</td>';
            table.querySelector('tbody')?.appendChild(emptyRow);

            rows.forEach(function (row) {
                row.dataset.smartSearch = normalizeText(row.textContent || '');
                if (index % 2 === 0) {
                    row.classList.add('odd:bg-slate-50/45', 'dark:odd:bg-slate-900/30');
                }
                row.classList.add('hover:bg-cyan-50/70', 'dark:hover:bg-cyan-500/10');
            });

            function applyFilter() {
                const term = normalizeText(searchInput?.value || '');
                let visible = 0;

                rows.forEach(function (row) {
                    const matches = term === '' || (row.dataset.smartSearch || '').includes(term);
                    row.classList.toggle('hidden', !matches);
                    if (matches) visible += 1;
                });

                if (visibleEl) visibleEl.textContent = String(visible);
                emptyRow.classList.toggle('hidden', visible !== 0);
            }

            searchInput?.addEventListener('input', applyFilter);
            applyFilter();
        }

        document.querySelectorAll('table.ui-table').forEach(function (table, index) {
            enhanceSmartList(table, index);
        });

        const brandHomeLink = document.getElementById('brand-home-link');
        brandHomeLink?.addEventListener('click', function (event) {
            const targetUrl = brandHomeLink.getAttribute('data-home-url');
            if (!targetUrl) return;
            event.preventDefault();
            window.location.href = targetUrl;
        });
        let demoFinalizeTriggered = false;
        let demoLeaveCleanupInitialized = false;

        const finalizeDemoSession = function (endUrl, options = {}) {
            const url = String(endUrl || '').trim();
            if (url === '') {
                return Promise.resolve(false);
            }

            const useBeacon = options && options.useBeacon === true;
            if (useBeacon && !demoFinalizeTriggered) {
                demoFinalizeTriggered = true;
                const form = new FormData();
                form.append('_token', csrfToken);
                try {
                    if (typeof navigator.sendBeacon === 'function') {
                        const sent = navigator.sendBeacon(url, form);
                        if (sent) {
                            return Promise.resolve(true);
                        }
                    }
                } catch (_error) {
                    // Keep fallback below.
                }
            }

            if (demoFinalizeTriggered && !useBeacon) {
                return Promise.resolve(true);
            }
            demoFinalizeTriggered = true;

            if (typeof window.fetch !== 'function') {
                return Promise.resolve(false);
            }

            return window.fetch(url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                },
                credentials: 'same-origin',
                keepalive: true,
            }).then(function () {
                return true;
            }).catch(function () {
                return false;
            });
        };

        const initializeDemoLeaveCleanup = function (endUrl) {
            const url = String(endUrl || '').trim();
            if (demoLeaveCleanupInitialized || url === '') {
                return;
            }
            demoLeaveCleanupInitialized = true;

            let internalNavigation = false;
            const markInternalNavigation = function () {
                internalNavigation = true;
            };

            document.addEventListener('click', function (event) {
                if (internalNavigation) return;
                if (!(event.target instanceof Element)) return;
                const anchor = event.target.closest('a[href]');
                if (!anchor) return;
                if (anchor.hasAttribute('download')) return;
                if ((anchor.getAttribute('target') || '').toLowerCase() === '_blank') return;
                if (event.ctrlKey || event.metaKey || event.shiftKey || event.altKey || event.button !== 0) return;
                const href = (anchor.getAttribute('href') || '').trim();
                if (href === '' || href.startsWith('#')) return;
                markInternalNavigation();
            }, true);

            document.addEventListener('submit', function () {
                markInternalNavigation();
            }, true);

            const tryFinalizeOnLeave = function () {
                if (internalNavigation || demoFinalizeTriggered) return;
                finalizeDemoSession(url, { useBeacon: true });
            };

            window.addEventListener('pagehide', function (event) {
                if (event.persisted) return;
                tryFinalizeOnLeave();
            });

            window.addEventListener('beforeunload', function () {
                tryFinalizeOnLeave();
            });
        };

        const demoCountdownSource = document.getElementById('demo-countdown-source');
        let demoCountdownManagedExternally = false;
        if (demoCountdownSource) {
            const expiresAtRaw = (demoCountdownSource.getAttribute('data-demo-expires-at') || '').trim();
            const serverNowRaw = (demoCountdownSource.getAttribute('data-demo-server-now') || '').trim();
            const demoEndUrl = (demoCountdownSource.getAttribute('data-demo-end-url') || '').trim();
            initializeDemoLeaveCleanup(demoEndUrl);
            const expiredRedirectUrl = (demoCountdownSource.getAttribute('data-demo-expired-url') || '').trim();
            const expiryFallback = (demoCountdownSource.getAttribute('data-demo-expiry-fallback') || '').trim();
            const countdownTargets = Array.from(document.querySelectorAll('[data-demo-countdown-target]'));
            const expiresAtMs = Date.parse(expiresAtRaw);
            const serverNowMs = Date.parse(serverNowRaw);
            const serverOffsetMs = Number.isFinite(serverNowMs) ? (serverNowMs - Date.now()) : 0;
            let demoCountdownTimer = null;
            let expiredHandled = false;

            demoCountdownManagedExternally = countdownTargets.length > 0;
            countdownTargets.forEach(function (target) {
                target.setAttribute('data-demo-countdown-managed', '1');
            });

            const formatRemaining = function (remainingMs) {
                const totalSec = Math.max(0, Math.floor(remainingMs / 1000));
                const hours = Math.floor(totalSec / 3600);
                const minutes = Math.floor((totalSec % 3600) / 60);
                const seconds = totalSec % 60;
                const pad = function (value) {
                    return String(value).padStart(2, '0');
                };
                if (hours > 0) {
                    return hours + 'h ' + pad(minutes) + 'm ' + pad(seconds) + 's';
                }
                return minutes + 'm ' + pad(seconds) + 's';
            };

            const setCountdownText = function (value) {
                countdownTargets.forEach(function (target) {
                    target.textContent = value;
                });
            };
            const redirectAfterDemoEnd = function () {
                window.setTimeout(function () {
                    if (expiredRedirectUrl !== '') {
                        window.location.href = expiredRedirectUrl;
                        return;
                    }
                    window.location.reload();
                }, 350);
            };

            const handleExpiredDemo = function () {
                if (expiredHandled) return;
                expiredHandled = true;
                setCountdownText('Demo finalizada');
                finalizeDemoSession(demoEndUrl).then(redirectAfterDemoEnd).catch(redirectAfterDemoEnd);
            };

            const updateDemoCountdown = function () {
                if (!Number.isFinite(expiresAtMs)) {
                    setCountdownText(expiryFallback !== '' ? expiryFallback : 'Sin hora de expiracion');
                    return;
                }

                const nowAdjusted = Date.now() + serverOffsetMs;
                const remainingMs = expiresAtMs - nowAdjusted;
                if (remainingMs <= 0) {
                    handleExpiredDemo();
                    return;
                }

                setCountdownText(formatRemaining(remainingMs));
            };

            updateDemoCountdown();
            demoCountdownTimer = window.setInterval(updateDemoCountdown, 1000);
            window.addEventListener('beforeunload', function () {
                if (demoCountdownTimer) {
                    window.clearInterval(demoCountdownTimer);
                }
            });
        }

        const demoTourPopover = document.getElementById('demo-tour-popover');
        const demoTourOverlay = document.getElementById('demo-tour-overlay');
        if (demoTourPopover && demoTourOverlay) {
            const titleEl = demoTourPopover.querySelector('[data-demo-tour-title]');
            const textEl = demoTourPopover.querySelector('[data-demo-tour-text]');
            const progressEl = demoTourPopover.querySelector('[data-demo-tour-progress]');
            const countdownEl = demoTourPopover.querySelector('[data-demo-tour-countdown]');
            const prevBtn = demoTourPopover.querySelector('[data-demo-tour-prev]');
            const nextBtn = demoTourPopover.querySelector('[data-demo-tour-next]');
            const openRouteBtn = demoTourPopover.querySelector('[data-demo-tour-open-route]');
            const closeBtn = demoTourPopover.querySelector('[data-demo-tour-close]');
            const countdownManagedExternally = demoCountdownManagedExternally || countdownEl?.getAttribute('data-demo-countdown-managed') === '1';
            const token = (demoTourPopover.getAttribute('data-demo-token') || 'default').trim();
            const storageKey = 'panel.demo_tour.' + token;
            const expiryFallback = (demoTourPopover.getAttribute('data-demo-expiry-fallback') || '').trim();
            const demoEndUrl = (demoTourPopover.getAttribute('data-demo-end-url') || '').trim();
            initializeDemoLeaveCleanup(demoEndUrl);
            const expiredRedirectUrl = (demoTourPopover.getAttribute('data-demo-expired-url') || '').trim();

            let steps = [];
            try {
                steps = JSON.parse(demoTourPopover.getAttribute('data-demo-steps') || '[]');
            } catch (_error) {
                steps = [];
            }
            steps = Array.isArray(steps) ? steps.filter(function (step) {
                return step && typeof step === 'object';
            }) : [];

            if (steps.length > 0) {
                const state = {
                    step: 0,
                    dismissed: false,
                    completed: false,
                };

                let highlightedEl = null;
                let positionRaf = 0;
                let positionTimer = 0;
                let countdownTimer = null;
                let expiredHandled = false;
                let lastScrolledSignature = '';

                const expiresAtRaw = (demoTourPopover.getAttribute('data-demo-expires-at') || '').trim();
                const serverNowRaw = (demoTourPopover.getAttribute('data-demo-server-now') || '').trim();
                const expiresAtMs = Date.parse(expiresAtRaw);
                const serverNowMs = Date.parse(serverNowRaw);
                const serverOffsetMs = Number.isFinite(serverNowMs) ? (serverNowMs - Date.now()) : 0;

                try {
                    const persisted = JSON.parse(localStorage.getItem(storageKey) || '{}');
                    if (persisted && typeof persisted === 'object') {
                        state.step = Number.isFinite(Number(persisted.step)) ? Number(persisted.step) : 0;
                        state.dismissed = Boolean(persisted.dismissed);
                        state.completed = Boolean(persisted.completed);
                    }
                } catch (_error) {
                    // no-op
                }

                function normalizePath(raw) {
                    if (!raw) return '';
                    try {
                        const url = new URL(raw, window.location.origin);
                        return url.pathname.replace(/\/+$/, '');
                    } catch (_error) {
                        return String(raw || '').replace(/\/+$/, '');
                    }
                }

                function currentPath() {
                    return normalizePath(window.location.href);
                }

                function stepPath(step) {
                    return normalizePath(step && step.route ? step.route : '');
                }

                function routeMatches(step) {
                    const stepRoutePath = stepPath(step);
                    if (stepRoutePath === '') return true;
                    return stepRoutePath === currentPath();
                }

                function clampStep() {
                    if (state.step < 0) state.step = 0;
                    if (state.step > steps.length - 1) state.step = steps.length - 1;
                }

                function persist() {
                    try {
                        localStorage.setItem(storageKey, JSON.stringify(state));
                    } catch (_error) {
                        // no-op
                    }
                }

                function clearHighlight() {
                    if (!highlightedEl) return;
                    highlightedEl.removeAttribute('data-demo-tour-highlight');
                    highlightedEl = null;
                }

                function elementIsVisible(el) {
                    if (!(el instanceof HTMLElement)) return false;
                    const style = window.getComputedStyle(el);
                    if (style.display === 'none' || style.visibility === 'hidden' || style.opacity === '0') {
                        return false;
                    }
                    const rect = el.getBoundingClientRect();
                    return rect.width > 0 && rect.height > 0;
                }

                function findTarget(step) {
                    if (!step || !step.selector || !routeMatches(step)) return null;
                    const candidates = Array.from(document.querySelectorAll(step.selector));
                    if (candidates.length === 0) return null;
                    for (const candidate of candidates) {
                        if (elementIsVisible(candidate)) {
                            return candidate;
                        }
                    }
                    return null;
                }

                function formatRemaining(remainingMs) {
                    const totalSec = Math.max(0, Math.floor(remainingMs / 1000));
                    const hours = Math.floor(totalSec / 3600);
                    const minutes = Math.floor((totalSec % 3600) / 60);
                    const seconds = totalSec % 60;
                    const pad = function (value) {
                        return String(value).padStart(2, '0');
                    };
                    if (hours > 0) {
                        return hours + 'h ' + pad(minutes) + 'm ' + pad(seconds) + 's';
                    }
                    return minutes + 'm ' + pad(seconds) + 's';
                }

                function hideTour() {
                    demoTourOverlay.setAttribute('data-open', '0');
                    demoTourOverlay.setAttribute('aria-hidden', 'true');
                    demoTourPopover.setAttribute('data-open', '0');
                    clearHighlight();
                }

                function showTour() {
                    demoTourOverlay.setAttribute('data-open', '1');
                    demoTourOverlay.setAttribute('aria-hidden', 'false');
                    demoTourPopover.setAttribute('data-open', '1');
                }

                function handleExpiredDemo() {
                    if (expiredHandled) return;
                    expiredHandled = true;
                    state.completed = true;
                    persist();
                    hideTour();
                    const redirectAfterDemoEnd = function () {
                        window.setTimeout(function () {
                            if (expiredRedirectUrl !== '') {
                                window.location.href = expiredRedirectUrl;
                                return;
                            }
                            window.location.reload();
                        }, 350);
                    };
                    finalizeDemoSession(demoEndUrl).then(redirectAfterDemoEnd).catch(redirectAfterDemoEnd);
                }

                function updateCountdown() {
                    if (countdownManagedExternally) {
                        return;
                    }
                    if (!countdownEl) return;
                    if (!Number.isFinite(expiresAtMs)) {
                        countdownEl.textContent = expiryFallback !== '' ? expiryFallback : 'Sin hora de expiracion';
                        return;
                    }

                    const nowAdjusted = Date.now() + serverOffsetMs;
                    const remainingMs = expiresAtMs - nowAdjusted;
                    if (remainingMs <= 0) {
                        countdownEl.textContent = 'Demo finalizada';
                        handleExpiredDemo();
                        return;
                    }

                    countdownEl.textContent = formatRemaining(remainingMs);
                }

                function placementCandidates(preferred) {
                    const normalized = String(preferred || 'bottom').toLowerCase();
                    if (normalized === 'top') {
                        return ['top', 'top-start', 'top-end', 'bottom', 'right', 'left', 'bottom-start', 'bottom-end'];
                    }
                    if (normalized === 'left') {
                        return ['left', 'left-start', 'left-end', 'right', 'top', 'bottom', 'top-start', 'bottom-start'];
                    }
                    if (normalized === 'right') {
                        return ['right', 'right-start', 'right-end', 'left', 'top', 'bottom', 'top-end', 'bottom-end'];
                    }
                    return ['bottom', 'bottom-start', 'bottom-end', 'top', 'right', 'left', 'top-start', 'top-end'];
                }

                function positionForPlacement(rect, popRect, placement, gap) {
                    const horizontalCenter = rect.left + (rect.width / 2) - (popRect.width / 2);
                    const verticalCenter = rect.top + (rect.height / 2) - (popRect.height / 2);
                    switch (placement) {
                        case 'top':
                            return { top: rect.top - popRect.height - gap, left: horizontalCenter };
                        case 'top-start':
                            return { top: rect.top - popRect.height - gap, left: rect.left };
                        case 'top-end':
                            return { top: rect.top - popRect.height - gap, left: rect.right - popRect.width };
                        case 'left':
                            return { top: verticalCenter, left: rect.left - popRect.width - gap };
                        case 'left-start':
                            return { top: rect.top, left: rect.left - popRect.width - gap };
                        case 'left-end':
                            return { top: rect.bottom - popRect.height, left: rect.left - popRect.width - gap };
                        case 'right':
                            return { top: verticalCenter, left: rect.right + gap };
                        case 'right-start':
                            return { top: rect.top, left: rect.right + gap };
                        case 'right-end':
                            return { top: rect.bottom - popRect.height, left: rect.right + gap };
                        case 'bottom-start':
                            return { top: rect.bottom + gap, left: rect.left };
                        case 'bottom-end':
                            return { top: rect.bottom + gap, left: rect.right - popRect.width };
                        case 'bottom':
                        default:
                            return { top: rect.bottom + gap, left: horizontalCenter };
                    }
                }

                function overflowScore(position, popRect, viewportWidth, viewportHeight, margin) {
                    const leftOverflow = Math.max(0, margin - position.left);
                    const topOverflow = Math.max(0, margin - position.top);
                    const rightOverflow = Math.max(0, (position.left + popRect.width + margin) - viewportWidth);
                    const bottomOverflow = Math.max(0, (position.top + popRect.height + margin) - viewportHeight);
                    const total = leftOverflow + rightOverflow + topOverflow + bottomOverflow;
                    const fits = total <= 0.1;

                    return {
                        fits: fits,
                        total: total,
                    };
                }

                function clampPosition(position, popRect, viewportWidth, viewportHeight, margin) {
                    return {
                        top: Math.max(margin, Math.min(position.top, viewportHeight - popRect.height - margin)),
                        left: Math.max(margin, Math.min(position.left, viewportWidth - popRect.width - margin)),
                    };
                }

                function pickBestPosition(rect, popRect, preferred, viewportWidth, viewportHeight, margin, gap) {
                    const candidates = placementCandidates(preferred);
                    let best = null;
                    let bestScore = Number.POSITIVE_INFINITY;

                    for (const candidate of candidates) {
                        const rawPos = positionForPlacement(rect, popRect, candidate, gap);
                        const score = overflowScore(rawPos, popRect, viewportWidth, viewportHeight, margin);
                        if (score.fits) {
                            return rawPos;
                        }
                        if (score.total < bestScore) {
                            bestScore = score.total;
                            best = rawPos;
                        }
                    }

                    if (!best) {
                        best = positionForPlacement(rect, popRect, 'bottom', gap);
                    }

                    return best;
                }

                function placePopover(target, placement) {
                    const margin = 14;
                    const gap = 16;
                    const popRect = demoTourPopover.getBoundingClientRect();
                    const viewportWidth = window.innerWidth;
                    const viewportHeight = window.innerHeight;

                    let position = {
                        top: viewportHeight - popRect.height - margin,
                        left: viewportWidth - popRect.width - margin,
                    };

                    if (target) {
                        const rect = target.getBoundingClientRect();
                        position = pickBestPosition(
                            rect,
                            popRect,
                            placement || 'bottom',
                            viewportWidth,
                            viewportHeight,
                            margin,
                            gap
                        );
                    }

                    const clamped = clampPosition(position, popRect, viewportWidth, viewportHeight, margin);

                    demoTourPopover.style.top = Math.round(clamped.top) + 'px';
                    demoTourPopover.style.left = Math.round(clamped.left) + 'px';
                }

                function schedulePlacement() {
                    if (state.dismissed || state.completed) return;
                    if (positionRaf) {
                        window.cancelAnimationFrame(positionRaf);
                    }
                    if (positionTimer) {
                        window.clearTimeout(positionTimer);
                    }

                    const applyPlacement = function () {
                        const currentStep = steps[state.step] || steps[0];
                        const target = findTarget(currentStep);
                        placePopover(target, currentStep && currentStep.placement ? currentStep.placement : 'bottom');
                    };

                    positionRaf = window.requestAnimationFrame(applyPlacement);
                    positionTimer = window.setTimeout(applyPlacement, 220);
                }

                function renderTour() {
                    clampStep();
                    if (state.dismissed || state.completed) {
                        hideTour();
                        return;
                    }

                    const currentStep = steps[state.step] || steps[0];
                    const stepNumber = state.step + 1;
                    const stepTotal = steps.length;
                    const target = findTarget(currentStep);
                    const targetMissing = !target;
                    const sameRoute = routeMatches(currentStep);
                    const hasRoute = typeof currentStep.route === 'string' && currentStep.route.trim() !== '';
                    const canAdvance = (sameRoute && !targetMissing) || !hasRoute;

                    showTour();
                    clearHighlight();

                    if (target) {
                        target.setAttribute('data-demo-tour-highlight', '1');
                        highlightedEl = target;
                    }

                    if (titleEl) {
                        titleEl.textContent = currentStep.title || ('Paso ' + stepNumber);
                    }
                    if (textEl) {
                        textEl.textContent = currentStep.text || 'Sigue los pasos para recorrer el sistema.';
                    }
                    if (progressEl) {
                        progressEl.textContent = 'Paso ' + stepNumber + ' de ' + stepTotal;
                    }

                    if (prevBtn) {
                        prevBtn.disabled = state.step <= 0;
                    }
                    if (nextBtn) {
                        nextBtn.textContent = stepNumber >= stepTotal ? 'Finalizar' : 'Siguiente';
                        nextBtn.disabled = !canAdvance;
                        nextBtn.classList.toggle('hidden', !canAdvance);
                    }

                    if (openRouteBtn) {
                        openRouteBtn.disabled = !hasRoute;
                        if (!hasRoute) {
                            openRouteBtn.classList.add('hidden');
                        } else {
                            openRouteBtn.classList.toggle('hidden', canAdvance);
                            openRouteBtn.textContent = sameRoute ? 'Ir al modulo' : 'Ir al paso';
                        }
                    }

                    if (target && sameRoute) {
                        const rect = target.getBoundingClientRect();
                        const signature = stepNumber + '|' + currentPath();
                        const outOfView = rect.top < 84 || rect.bottom > (window.innerHeight - 84);
                        if (signature !== lastScrolledSignature && outOfView) {
                            target.scrollIntoView({ behavior: 'smooth', block: 'center', inline: 'nearest' });
                        }
                        lastScrolledSignature = signature;
                    } else {
                        lastScrolledSignature = '';
                    }

                    schedulePlacement();
                    if (!countdownManagedExternally) {
                        updateCountdown();
                    }
                }

                prevBtn?.addEventListener('click', function () {
                    if (state.step <= 0) return;
                    state.step -= 1;
                    persist();
                    renderTour();
                });

                nextBtn?.addEventListener('click', function () {
                    if (state.step >= steps.length - 1) {
                        state.completed = true;
                    } else {
                        state.step += 1;
                    }
                    persist();
                    renderTour();
                });

                openRouteBtn?.addEventListener('click', function () {
                    const currentStep = steps[state.step] || steps[0];
                    const route = String(currentStep.route || '').trim();
                    if (route === '') return;
                    window.location.href = route;
                });

                closeBtn?.addEventListener('click', function () {
                    state.dismissed = true;
                    persist();
                    renderTour();
                });

                demoTourOverlay.addEventListener('click', function () {
                    state.dismissed = true;
                    persist();
                    renderTour();
                });

                document.addEventListener('keydown', function (event) {
                    if (event.key !== 'Escape') return;
                    if (state.dismissed || state.completed) return;
                    state.dismissed = true;
                    persist();
                    renderTour();
                });

                window.addEventListener('resize', schedulePlacement);
                window.addEventListener('scroll', schedulePlacement, true);
                if (!countdownManagedExternally) {
                    countdownTimer = window.setInterval(updateCountdown, 1000);
                }
                window.addEventListener('beforeunload', function () {
                    if (countdownTimer) {
                        window.clearInterval(countdownTimer);
                    }
                    if (positionTimer) {
                        window.clearTimeout(positionTimer);
                    }
                    if (positionRaf) {
                        window.cancelAnimationFrame(positionRaf);
                    }
                });

                renderTour();
            }
        }

        // SuperAdmin-only visual sync:
        // if top-right avatar exists, mirror it into the sidebar brand badge.
        const isSuperAdminViewer = @json($isSuperAdmin);
        if (isSuperAdminViewer) {
            const brandLogoBadge = document.getElementById('brand-logo-badge');
            const topAvatarImage = document.getElementById('user-avatar-image') || document.querySelector('#user-menu-button img');
            const topAvatarSrc = topAvatarImage?.getAttribute('src') || '';
            if (brandLogoBadge && topAvatarSrc !== '') {
                brandLogoBadge.innerHTML = '';
                const img = document.createElement('img');
                img.src = topAvatarSrc;
                img.alt = topAvatarImage?.getAttribute('alt') || 'SuperAdmin';
                img.className = 'brand-logo-media brand-logo-media-cover';
                brandLogoBadge.appendChild(img);
            }
        }

        const legalOverlay = document.getElementById('legal-acceptance-overlay');
        const legalForm = document.getElementById('legal-accept-form');
        const legalCheckbox = document.getElementById('legal-accept-checkbox');
        const legalSubmit = document.getElementById('legal-accept-submit');
        if (legalOverlay && legalForm && legalCheckbox && legalSubmit) {
            document.body.style.overflow = 'hidden';

            const permissionField = document.getElementById('legal-location-permission');
            const latitudeField = document.getElementById('legal-location-latitude');
            const longitudeField = document.getElementById('legal-location-longitude');
            const accuracyField = document.getElementById('legal-location-accuracy');
            let submitting = false;

            const setPermission = function (value) {
                if (!permissionField) return;
                permissionField.value = String(value || 'skipped');
            };

            const setCoordinates = function (latitude, longitude, accuracy) {
                if (latitudeField) latitudeField.value = latitude;
                if (longitudeField) longitudeField.value = longitude;
                if (accuracyField) accuracyField.value = accuracy;
            };

            const applyButtonState = function () {
                legalSubmit.disabled = !legalCheckbox.checked || submitting;
            };

            const finalizeSubmit = function () {
                if (submitting) return;
                submitting = true;
                applyButtonState();
                legalForm.submit();
            };

            legalCheckbox.addEventListener('change', applyButtonState);
            applyButtonState();

            legalForm.addEventListener('submit', function (event) {
                event.preventDefault();
                if (!legalCheckbox.checked || submitting) {
                    applyButtonState();
                    return;
                }

                if (!('geolocation' in navigator)) {
                    setPermission('unavailable');
                    setCoordinates('', '', '');
                    finalizeSubmit();
                    return;
                }

                let settled = false;
                const resolve = function (permission, position) {
                    if (settled) return;
                    settled = true;
                    setPermission(permission);
                    if (position && position.coords) {
                        setCoordinates(
                            String(position.coords.latitude ?? ''),
                            String(position.coords.longitude ?? ''),
                            String(position.coords.accuracy ?? '')
                        );
                    } else {
                        setCoordinates('', '', '');
                    }
                    finalizeSubmit();
                };

                try {
                    navigator.geolocation.getCurrentPosition(
                        function (position) {
                            resolve('granted', position);
                        },
                        function (error) {
                            if (error && error.code === 1) {
                                resolve('denied', null);
                                return;
                            }
                            if (error && error.code === 2) {
                                resolve('unavailable', null);
                                return;
                            }
                            resolve('error', null);
                        },
                        {
                            enableHighAccuracy: true,
                            timeout: 5500,
                            maximumAge: 0
                        }
                    );
                } catch (_error) {
                    resolve('error', null);
                }

                window.setTimeout(function () {
                    if (!settled) {
                        resolve('error', null);
                    }
                }, 6500);
            });
        }
    })();
</script>
