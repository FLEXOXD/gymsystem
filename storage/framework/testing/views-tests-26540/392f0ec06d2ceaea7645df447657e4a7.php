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
                            openRouteBtn.textContent = sameRoute ? 'Ir al módulo' : 'Ir al paso';
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

<?php /**PATH C:\laragon\www\gymsystem\resources\views/layouts/partials/panel-inline/demo-flow.blade.php ENDPATH**/ ?>