@php
    $user = auth()->user();
    $theme = $user?->theme ?? 'iron_dark';
    $darkThemes = ['iron_dark', 'power_red', 'energy_green', 'gold_elite'];
    $themeClass = in_array($theme, $darkThemes, true) ? 'dark theme-dark' : 'theme-light';
    $gymName = trim((string) ($syncGymName ?? ($user?->gym?->name ?? 'Gym')));
@endphp
<!DOCTYPE html>
<html lang="es" class="h-full antialiased {{ $themeClass }}" data-theme="{{ $theme }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Pantalla QR - {{ config('app.name', 'GymSystem') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="theme-body min-h-screen ui-text">
<main class="mx-auto w-full max-w-5xl space-y-4 px-3 py-3 md:px-6 md:py-6">
    <header class="flex flex-wrap items-center justify-between gap-3 rounded-2xl border border-slate-700/40 bg-slate-900/40 px-4 py-3">
        <div>
            <p class="ui-muted text-xs font-bold uppercase tracking-widest">Pantalla QR</p>
            <h1 class="ui-heading text-xl font-black md:text-2xl">QR dinámico - {{ $gymName }}</h1>
        </div>
        <span class="rounded-full border border-cyan-400/40 bg-cyan-500/10 px-3 py-1 text-xs font-bold uppercase tracking-wider text-cyan-200">Live</span>
    </header>

    <x-ui.card title="Check-in móvil" subtitle="Escanea este QR desde la app cliente.">
        <div id="mobile-qr-screen-svg" class="flex min-h-[420px] items-center justify-center rounded-2xl border border-slate-300 bg-white p-6 dark:border-slate-700 dark:bg-slate-900/70">
            <p class="text-sm text-slate-500 dark:text-slate-300">Generando QR...</p>
        </div>

        <div class="mt-3 grid gap-3 md:grid-cols-[minmax(0,1fr)_220px]">
            <div class="rounded-lg border border-slate-300 bg-slate-50 px-3 py-2 dark:border-slate-700 dark:bg-slate-900/60">
                <p class="text-[11px] font-bold uppercase tracking-wide text-slate-500 dark:text-slate-400">Payload</p>
                <p id="mobile-qr-screen-payload" class="mt-1 break-all font-mono text-xs text-slate-700 dark:text-slate-200">-</p>
            </div>
            <p id="mobile-qr-screen-countdown" class="rounded-lg border border-cyan-300 bg-cyan-50 px-3 py-2 text-xs font-bold uppercase tracking-wide text-cyan-800 dark:border-cyan-700/60 dark:bg-cyan-900/20 dark:text-cyan-100">
                Esperando QR...
            </p>
        </div>
    </x-ui.card>
</main>

<script>
    (function () {
        const mobileQrEndpoint = @json(route('reception.mobile-qr'));
        const mobileQrStatusEndpoint = @json(route('reception.mobile-qr.status'));
        const svgContainer = document.getElementById('mobile-qr-screen-svg');
        const payloadEl = document.getElementById('mobile-qr-screen-payload');
        const countdownEl = document.getElementById('mobile-qr-screen-countdown');

        if (!svgContainer || !payloadEl || !countdownEl) {
            return;
        }

        const url = new URL(window.location.href);
        const rotateParam = Number(url.searchParams.get('rotate') || 20);
        const rotateSeconds = Number.isFinite(rotateParam) ? Math.max(10, Math.min(2592000, Math.floor(rotateParam))) : 20;

        let expiresAtTs = 0;
        let countdownTimer = null;
        let refreshTimer = null;
        let loading = false;
        let activeToken = '';
        let lastConsumedAtMs = 0;
        let statusPollTimer = null;
        let statusLoading = false;

        function clearTimers(includeStatusPoll = true) {
            if (countdownTimer) {
                clearInterval(countdownTimer);
                countdownTimer = null;
            }
            if (refreshTimer) {
                clearTimeout(refreshTimer);
                refreshTimer = null;
            }
            if (includeStatusPoll && statusPollTimer) {
                clearInterval(statusPollTimer);
                statusPollTimer = null;
            }
        }

        function resolveQrSvgMarkup(rawValue) {
            if (typeof rawValue === 'string') {
                return rawValue.trim();
            }

            if (rawValue && typeof rawValue === 'object') {
                if (typeof rawValue.html === 'string') {
                    return rawValue.html.trim();
                }
                if (typeof rawValue.svg === 'string') {
                    return rawValue.svg.trim();
                }
            }

            return '';
        }

        function setCountdown(text, tone) {
            countdownEl.textContent = text;

            if (tone === 'error') {
                countdownEl.className = 'rounded-lg border border-rose-300 bg-rose-50 px-3 py-2 text-xs font-bold uppercase tracking-wide text-rose-800 dark:border-rose-700/60 dark:bg-rose-900/20 dark:text-rose-100';
                return;
            }

            if (tone === 'warn') {
                countdownEl.className = 'rounded-lg border border-amber-300 bg-amber-50 px-3 py-2 text-xs font-bold uppercase tracking-wide text-amber-800 dark:border-amber-700/60 dark:bg-amber-900/20 dark:text-amber-100';
                return;
            }

            countdownEl.className = 'rounded-lg border border-cyan-300 bg-cyan-50 px-3 py-2 text-xs font-bold uppercase tracking-wide text-cyan-800 dark:border-cyan-700/60 dark:bg-cyan-900/20 dark:text-cyan-100';
        }

        function startCountdown() {
            if (!expiresAtTs) {
                setCountdown('Esperando QR...', 'warn');
                return;
            }

            if (countdownTimer) {
                clearInterval(countdownTimer);
                countdownTimer = null;
            }

            const tick = function () {
                const nowTs = Math.floor(Date.now() / 1000);
                const remaining = Math.max(0, expiresAtTs - nowTs);
                setCountdown('QR vence en ' + String(remaining) + 's', remaining <= 3 ? 'warn' : 'info');
            };

            tick();
            countdownTimer = setInterval(tick, 1000);
        }

        function scheduleRefresh() {
            if (refreshTimer) {
                clearTimeout(refreshTimer);
                refreshTimer = null;
            }

            if (!expiresAtTs) {
                return;
            }

            const msUntilRefresh = Math.max(1000, (expiresAtTs * 1000) - Date.now() - 500);
            refreshTimer = setTimeout(function () {
                generateQr(false);
            }, msUntilRefresh);
        }

        async function checkConsumedStatus() {
            if (!mobileQrStatusEndpoint || !activeToken || loading || statusLoading) {
                return;
            }

            statusLoading = true;
            try {
                const response = await fetch(mobileQrStatusEndpoint, {
                    method: 'GET',
                    credentials: 'same-origin',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                });

                const payload = await response.json();
                if (!response.ok || !payload.ok || !payload.consumed) {
                    return;
                }

                const consumedToken = String(payload.consumed.token || '').trim().toUpperCase();
                const consumedAtMs = Number(payload.consumed.consumed_at_ms || 0);
                const hasValidConsumedAt = Number.isFinite(consumedAtMs) && consumedAtMs > 0;
                const shouldRefresh = hasValidConsumedAt
                    && consumedToken !== ''
                    && consumedToken === activeToken
                    && consumedAtMs > lastConsumedAtMs;

                if (hasValidConsumedAt && consumedAtMs > lastConsumedAtMs) {
                    lastConsumedAtMs = consumedAtMs;
                }

                if (shouldRefresh) {
                    generateQr(false);
                }
            } catch (error) {
                // Silent: reintenta en el siguiente poll.
            } finally {
                statusLoading = false;
            }
        }

        function startStatusPolling() {
            if (statusPollTimer || !mobileQrStatusEndpoint) {
                return;
            }

            statusPollTimer = setInterval(function () {
                checkConsumedStatus();
            }, 1000);
        }

        async function generateQr(showLoading) {
            if (loading) {
                return;
            }
            loading = true;

            if (showLoading) {
                svgContainer.innerHTML = '<p class="text-sm text-slate-500 dark:text-slate-300">Generando QR...</p>';
            }

            try {
                const requestUrl = new URL(mobileQrEndpoint, window.location.origin);
                requestUrl.searchParams.set('rotate_seconds', String(rotateSeconds));

                const response = await fetch(requestUrl.toString(), {
                    method: 'GET',
                    credentials: 'same-origin',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                });

                const payload = await response.json();
                if (!response.ok || !payload.ok) {
                    const message = payload && payload.message ? String(payload.message) : 'No se pudo generar QR.';
                    throw new Error(message);
                }

                const qrSvgMarkup = resolveQrSvgMarkup(payload.qr_svg);
                svgContainer.innerHTML = qrSvgMarkup !== '' ? qrSvgMarkup : '<p class="text-sm text-slate-500 dark:text-slate-300">Sin QR disponible.</p>';
                payloadEl.textContent = payload.qr_payload ? String(payload.qr_payload) : '-';
                activeToken = payload.token ? String(payload.token).trim().toUpperCase() : '';
                expiresAtTs = Number(payload.expires_at_ts || 0);
                startCountdown();
                scheduleRefresh();
            } catch (error) {
                const message = error instanceof Error ? error.message : 'No se pudo generar QR.';
                svgContainer.innerHTML = '<p class="text-sm text-rose-500">Error generando QR.</p>';
                payloadEl.textContent = '-';
                activeToken = '';
                expiresAtTs = 0;
                clearTimers(false);
                setCountdown(message, 'error');
            } finally {
                loading = false;
            }
        }

        startStatusPolling();
        window.addEventListener('beforeunload', clearTimers);
        generateQr(true);
    })();
</script>
</body>
</html>
