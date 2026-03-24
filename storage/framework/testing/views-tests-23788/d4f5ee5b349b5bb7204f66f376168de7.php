        const sidebarToggle = document.getElementById('sidebar-toggle');
        const sidebar = document.getElementById('panel-sidebar');
        const sidebarToggleLabel = sidebarToggle?.querySelector('.panel-menu-trigger-label');
        const sidebarTooltip = document.getElementById('sidebar-collapsed-tooltip');
        let activeSidebarTooltipAnchor = null;

        function isSidebarCollapsed() {
            return Boolean(sidebar?.classList.contains('sidebar-collapsed'));
        }

        function hideSidebarTooltip() {
            if (!sidebarTooltip) return;
            sidebarTooltip.textContent = '';
            sidebarTooltip.setAttribute('data-open', '0');
            sidebarTooltip.setAttribute('aria-hidden', 'true');
        }

        function positionSidebarTooltip(anchor) {
            if (!sidebarTooltip || !(anchor instanceof HTMLElement) || !isSidebarCollapsed()) {
                hideSidebarTooltip();
                return;
            }

            const label = (anchor.getAttribute('data-sidebar-label') || '').trim();
            if (label === '') {
                hideSidebarTooltip();
                return;
            }

            sidebarTooltip.textContent = label;
            sidebarTooltip.setAttribute('data-open', '1');
            sidebarTooltip.setAttribute('aria-hidden', 'false');

            const anchorRect = anchor.getBoundingClientRect();
            let nextLeft = anchorRect.right + 14;
            let nextTop = anchorRect.top + (anchorRect.height / 2);

            sidebarTooltip.style.left = nextLeft + 'px';
            sidebarTooltip.style.top = nextTop + 'px';

            const tooltipRect = sidebarTooltip.getBoundingClientRect();
            const viewportWidth = document.documentElement.clientWidth;
            const viewportHeight = document.documentElement.clientHeight;
            const halfHeight = tooltipRect.height / 2;

            if (tooltipRect.right > viewportWidth - 12) {
                nextLeft = Math.max(12, viewportWidth - tooltipRect.width - 12);
            }
            if ((nextTop - halfHeight) < 12) {
                nextTop = 12 + halfHeight;
            }
            if ((nextTop + halfHeight) > (viewportHeight - 12)) {
                nextTop = viewportHeight - 12 - halfHeight;
            }

            sidebarTooltip.style.left = nextLeft + 'px';
            sidebarTooltip.style.top = nextTop + 'px';
        }

        function showSidebarTooltip(anchor) {
            if (!(anchor instanceof HTMLElement)) return;
            activeSidebarTooltipAnchor = anchor;
            positionSidebarTooltip(anchor);
        }

        function syncSidebarToggleUi(collapsed) {
            if (!sidebarToggle) return;
            const label = collapsed ? 'Abrir menú' : 'Ocultar menú';
            const ariaLabel = collapsed ? 'Abrir menú' : 'Ocultar menú';
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
            hideSidebarTooltip();
        });

        if (sidebar && localStorage.getItem('panel.sidebar_collapsed') === '1') {
            sidebarToggle?.click();
        }
        syncSidebarToggleUi(sidebar?.classList.contains('sidebar-collapsed') ?? false);

        sidebar?.querySelectorAll('.sidebar-nav-item[data-sidebar-label]').forEach(function (anchor) {
            anchor.addEventListener('mouseenter', function () {
                showSidebarTooltip(anchor);
            });
            anchor.addEventListener('mouseleave', function () {
                if (activeSidebarTooltipAnchor === anchor) {
                    activeSidebarTooltipAnchor = null;
                }
                hideSidebarTooltip();
            });
            anchor.addEventListener('focus', function () {
                showSidebarTooltip(anchor);
            });
            anchor.addEventListener('blur', function () {
                if (activeSidebarTooltipAnchor === anchor) {
                    activeSidebarTooltipAnchor = null;
                }
                hideSidebarTooltip();
            });
        });

        window.addEventListener('resize', function () {
            if (activeSidebarTooltipAnchor) {
                positionSidebarTooltip(activeSidebarTooltipAnchor);
            }
        });
        window.addEventListener('scroll', function () {
            if (activeSidebarTooltipAnchor) {
                positionSidebarTooltip(activeSidebarTooltipAnchor);
            }
        }, true);

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
        const pwaUpgradeMessage = <?php echo json_encode($pwaUpgradeMessage, 15, 512) ?>;
        const isDemoMode = Boolean(<?php echo json_encode((bool) ($demo_mode ?? false), 15, 512) ?>);
        const currentUserId = Number(<?php echo json_encode((int) ($user?->id ?? 0), 15, 512) ?>);
        const reportPwaEvent = typeof window.reportGymPwaEvent === 'function'
            ? window.reportGymPwaEvent
            : function () {};
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
        let pwaUpdateReady = false;
        let pwaAlertTimeoutId = null;
        let pushAlertTimeoutId = null;
        let pushUnsupportedReason = '';
        let uiLoadingTimeoutId = null;
        let uiLoadingReleaseId = null;
        let uiLoadingHardReleaseId = null;
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
                    showPushAccessAlert('La acción está tardando más de lo esperado. Intenta de nuevo.', 'warning');
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
            if (uiLoadingHardReleaseId) {
                window.clearTimeout(uiLoadingHardReleaseId);
                uiLoadingHardReleaseId = null;
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
            // Failsafe for download-like navigations (CSV/PDF) where the browser
            // may trigger navigation events without unloading the current page.
            uiLoadingHardReleaseId = window.setTimeout(function () {
                if (document.visibilityState === 'visible') {
                    hideUiLoading();
                }
            }, 9000);
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
                const compactTitle = typeof payload.title === 'string' && payload.title.trim() !== '' ? payload.title.trim() : 'FlexGym';
                const compactBody = typeof payload.body === 'string' ? payload.body.trim() : '';
                showPushAccessAlert(compactBody !== '' ? compactTitle + ': ' + compactBody : compactTitle, 'info');
                return;
            }
            if (document.visibilityState !== 'visible') return;
            const rawData = (payload.data && typeof payload.data === 'object') ? payload.data : {};
            const title = typeof payload.title === 'string' && payload.title.trim() !== ''
                ? payload.title.trim()
                : 'FlexGym';
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

            window.addEventListener('GYMSYSTEM_SW_UPDATE_READY', function () {
                pwaUpdateReady = true;
                pwaInstallButton.classList.remove('hidden');
                pwaInstallButton.classList.add('lg:inline-flex');
                pwaInstallButton.setAttribute('data-pwa-update-ready', '1');
                pwaInstallButton.textContent = 'Actualizar app';
                pwaInstallButton.setAttribute('title', 'Aplicar nueva versión');
                showPwaAccessAlert('Hay una actualización disponible. Pulsa "Actualizar app".', 'warning');
            });

            window.addEventListener('GYMSYSTEM_SW_UPDATE_APPLIED', function () {
                pwaUpdateReady = false;
                pwaInstallButton.removeAttribute('data-pwa-update-ready');
                pwaInstallButton.textContent = 'Instalar app';
                showPwaAccessAlert('Actualización aplicada correctamente.', 'warning');
            });

            window.addEventListener('beforeinstallprompt', function (event) {
                event.preventDefault();
                if (!pwaInstallEnabled) {
                    showPwaAccessAlert(pwaUpgradeMessage, 'warning');
                    return;
                }
                pwaInstallPromptEvent = event;
                pwaInstallButton.textContent = 'Instalar app';
                reportPwaEvent('install_prompt_available');
            });

            window.addEventListener('appinstalled', function () {
                pwaInstallPromptEvent = null;
                markPwaInstalled();
                hidePwaInstallButton();
                showPwaAccessAlert('Aplicación instalada correctamente.', 'warning');
                reportPwaEvent('app_installed');
            });

            pwaInstallButton.addEventListener('click', async function () {
                if (pwaUpdateReady || pwaInstallButton.getAttribute('data-pwa-update-ready') === '1') {
                    showUiLoading('Aplicando actualización...', true);
                    try {
                        if (typeof window.applyGymSwUpdate === 'function') {
                            await window.applyGymSwUpdate();
                        } else {
                            window.location.reload();
                        }
                    } finally {
                        hideUiLoading();
                    }
                    return;
                }

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
                    reportPwaEvent('install_manual_hint_shown');
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
                throw new Error('WEBPUSH_VAPID_PUBLIC_KEY inválida. Regenera llaves VAPID.');
            }

            const padding = '='.repeat((4 - (normalized.length % 4)) % 4);
            const base64 = (normalized + padding).replace(/-/g, '+').replace(/_/g, '/');
            let rawData = '';
            try {
                rawData = window.atob(base64);
            } catch (_error) {
                throw new Error('WEBPUSH_VAPID_PUBLIC_KEY inválida. Ejecuta notifications:webpush-keys y actualiza .env.');
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
                return { ok: false, message: 'Respuesta inválida del servidor.' };
            });
            if (!response.ok || data.ok === false) {
                const errorMessage = (data && data.message) ? String(data.message) : 'No se pudo completar la operación.';
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
            title.textContent = 'Activa notificaciones de FlexGym';
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
                'Mensajes inmediatos sin tener que recargar la página.',
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

            if (isDemoMode) {
                pushUnsupportedReason = 'Las notificaciones push no están disponibles en la cuenta demo.';
                updatePushButtonState('unsupported');
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
                pushUnsupportedReason = error instanceof Error ? error.message : 'WEBPUSH_VAPID_PUBLIC_KEY inválida.';
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
                showPushAccessAlert('El servidor aún no tiene llaves VAPID activas.', 'warning');
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
                        throw new Error('No se pudo inicializar Service Worker para push. Recarga la página.');
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
                        showPushAccessAlert('No se concedió permiso de notificaciones.', 'warning');

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
                && !isDemoMode
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
            resetFloatingMenu(userMenuDropdown);
        }

        function openUserMenu() {
            if (!userMenuDropdown || !userMenuButton) return;
            userMenuDropdown.classList.remove('hidden');
            userMenuButton.setAttribute('aria-expanded', 'true');
            positionFloatingMenu(userMenuDropdown, userMenuButton);
        }
        function closeBellMenu() {
            if (!headerBellDropdown || !headerBellButton) return;
            headerBellDropdown.classList.add('hidden');
            headerBellButton.setAttribute('aria-expanded', 'false');
            resetFloatingMenu(headerBellDropdown);
        }

        function openBellMenu() {
            if (!headerBellDropdown || !headerBellButton) return;
            headerBellDropdown.classList.remove('hidden');
            headerBellButton.setAttribute('aria-expanded', 'true');
            positionFloatingMenu(headerBellDropdown, headerBellButton);
        }

        function resetFloatingMenu(menu) {
            if (!(menu instanceof HTMLElement)) return;
            [
                'position',
                'top',
                'right',
                'left',
                'width',
                'maxWidth',
                'maxHeight',
                'overflowY',
                'zIndex',
            ].forEach(function (property) {
                menu.style.removeProperty(property);
            });
        }

        function positionFloatingMenu(menu, anchor) {
            if (!(menu instanceof HTMLElement) || !(anchor instanceof HTMLElement)) return;

            const isMobile = window.matchMedia('(max-width: 640px)').matches;
            if (!isMobile) {
                resetFloatingMenu(menu);
                return;
            }

            const anchorRect = anchor.getBoundingClientRect();
            const sideInset = 12;
            const viewportHeight = window.innerHeight || document.documentElement.clientHeight || 0;
            const top = Math.max(anchorRect.bottom + 10, sideInset);
            const maxHeight = Math.max(220, viewportHeight - top - sideInset);

            menu.style.position = 'fixed';
            menu.style.top = top + 'px';
            menu.style.left = sideInset + 'px';
            menu.style.right = sideInset + 'px';
            menu.style.width = 'auto';
            menu.style.maxWidth = 'none';
            menu.style.maxHeight = maxHeight + 'px';
            menu.style.overflowY = 'auto';
            menu.style.zIndex = '140';
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
                hideSidebarTooltip();
                closeUserMenu();
                closeBellMenu();
            }
        });

        window.addEventListener('resize', function () {
            if (userMenuDropdown && !userMenuDropdown.classList.contains('hidden') && userMenuButton) {
                positionFloatingMenu(userMenuDropdown, userMenuButton);
            }
            if (headerBellDropdown && !headerBellDropdown.classList.contains('hidden') && headerBellButton) {
                positionFloatingMenu(headerBellDropdown, headerBellButton);
            }
        });

        window.addEventListener('scroll', function () {
            if (userMenuDropdown && !userMenuDropdown.classList.contains('hidden') && userMenuButton) {
                positionFloatingMenu(userMenuDropdown, userMenuButton);
            }
            if (headerBellDropdown && !headerBellDropdown.classList.contains('hidden') && headerBellButton) {
                positionFloatingMenu(headerBellDropdown, headerBellButton);
            }
        }, true);

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
            showUiLoadingForNavigation('Cargando página...');
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
            showUiLoadingForNavigation('Cargando página...');
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

        function prepareMobileTableStack(table) {
            if (!(table instanceof HTMLTableElement)) return;
            if (table.dataset.mobileStackReady === '1') return;
            if (table.getAttribute('data-mobile-stack') === 'off') return;

            const wrapper = table.closest('.smart-list-wrap, .overflow-x-auto') || table.parentElement;
            if (!wrapper) return;

            wrapper.classList.add('table-mobile-stack');

            const headers = Array.from(table.querySelectorAll('thead th')).map(function (header) {
                const text = String(header.textContent || '').replace(/\s+/g, ' ').trim();
                return text === '' ? 'Dato' : text;
            });

            const rows = getBodyRows(table);
            rows.forEach(function (row) {
                const cells = Array.from(row.querySelectorAll('td'));
                cells.forEach(function (cell, index) {
                    const headerLabel = headers[index] || 'Dato';
                    cell.setAttribute('data-label', headerLabel);
                });
            });

            table.dataset.mobileStackReady = '1';
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
            const smartListSearchLabel = <?php echo \Illuminate\Support\Js::from(__('ui.smart_list.search_label'))->toHtml() ?>;
            const smartListSearchPlaceholder = <?php echo \Illuminate\Support\Js::from(__('ui.smart_list.search_placeholder'))->toHtml() ?>;
            const smartListShowing = <?php echo \Illuminate\Support\Js::from(__('ui.smart_list.showing'))->toHtml() ?>;
            const smartListOf = <?php echo \Illuminate\Support\Js::from(__('ui.smart_list.of'))->toHtml() ?>;
            const smartListNoResults = <?php echo \Illuminate\Support\Js::from(__('ui.smart_list.no_results'))->toHtml() ?>;
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
            prepareMobileTableStack(table);
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

<?php /**PATH C:\laragon\www\gymsystem\resources\views/layouts/partials/panel-inline/core-ui-and-push.blade.php ENDPATH**/ ?>