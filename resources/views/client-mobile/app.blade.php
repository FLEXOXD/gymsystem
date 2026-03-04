<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>App cliente - {{ (string) $gym->name }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .mobile-guard { display: none; }
        .mobile-shell { min-height: 100vh; background: radial-gradient(circle at 20% 10%, rgba(34,197,94,.22), transparent 40%), radial-gradient(circle at 80% 0%, rgba(14,165,233,.22), transparent 45%), #020617; color: #e2e8f0; }
        .mobile-card { border: 1px solid rgba(56,189,248,.32); background: rgba(2,6,23,.82); box-shadow: 0 20px 60px rgba(2,6,23,.65); }
        .live-mobile-card { border: 1px solid rgba(16,185,129,.45); background: radial-gradient(circle at 14% 20%, rgba(16,185,129,.22), transparent 46%), rgba(2,6,23,.9); box-shadow: 0 18px 44px rgba(16,185,129,.20); }
        .live-dot { position: relative; display: inline-flex; width: 10px; height: 10px; border-radius: 9999px; background: #10b981; }
        .live-dot::after { content: ''; position: absolute; inset: -5px; border-radius: 9999px; background: rgba(16,185,129,.52); animation: livePulse 1.8s ease-out infinite; }
        .live-count-pop { animation: livePop .28s ease-out; }
        @keyframes livePulse { 0% { transform: scale(.4); opacity: .9; } 100% { transform: scale(1.6); opacity: 0; } }
        @keyframes livePop { 0% { transform: scale(.86); } 100% { transform: scale(1); } }
        video { width: 100%; border-radius: 14px; border: 1px solid rgba(56,189,248,.35); background: #020617; }
        @media (min-width: 900px) and (pointer:fine) {
            .mobile-shell { display: none; }
            .mobile-guard { min-height: 100vh; display: grid; place-items: center; padding: 24px; background: #020617; color: #cbd5e1; }
        }
    </style>
</head>
<body>
<div class="mobile-guard">
    <div class="max-w-xl rounded-2xl border border-slate-700 bg-slate-900/80 p-6 text-center">
        <h1 class="text-2xl font-black text-white">Interfaz exclusiva para celulares</h1>
        <p class="mt-3 text-sm text-slate-300">Para esta app usa celular y PWA instalada.</p>
    </div>
</div>

<main class="mobile-shell px-4 py-6" data-checkin-url="{{ route('client-mobile.check-in', ['gymSlug' => $gym->slug]) }}" data-progress-url="{{ route('client-mobile.progress', ['gymSlug' => $gym->slug]) }}">
    <section class="mx-auto max-w-md space-y-4">
        <header class="rounded-2xl border border-emerald-400/40 bg-emerald-500/10 p-4">
            <p class="text-xs font-black uppercase tracking-[.16em] text-emerald-200">{{ (string) $gym->name }}</p>
            <h1 class="mt-1 text-xl font-black text-white">Hola, {{ (string) $client->full_name }}</h1>
            <p class="mt-1 text-xs text-emerald-100">{{ __('messages.client_mobile.scan_dynamic_qr_help') }}</p>
        </header>

        <article class="live-mobile-card rounded-2xl p-4">
            <div class="flex items-center justify-between gap-3">
                <div class="inline-flex items-center gap-2">
                    <span class="live-dot" aria-hidden="true"></span>
                    <p class="text-xs font-black uppercase tracking-[.18em] text-emerald-100">Presentes</p>
                </div>
                <p class="text-[11px] font-semibold text-emerald-100/90">ahora</p>
            </div>
            <div class="mt-2 flex items-end gap-2">
                <p class="text-4xl font-black leading-none text-white" id="live-clients-count">{{ (int) ($progress['live_clients_count'] ?? 0) }}</p>
                <p class="pb-1 text-xs font-semibold text-emerald-100/90">en tu gimnasio</p>
            </div>
            <p class="mt-2 text-[11px] text-emerald-100/80" id="live-clients-window">Conteo de {{ (string) ($progress['live_window_label'] ?? 'En vivo') }}. Actualiza automático.</p>
        </article>

        <article class="mobile-card rounded-2xl p-4 space-y-3">
            <h2 class="text-sm font-black uppercase tracking-wide text-cyan-200">Tu progreso</h2>
            <div class="grid grid-cols-2 gap-2 text-sm">
                <div class="rounded-xl border border-slate-700 bg-slate-900/70 p-2"><p class="text-xs text-slate-400">Estado</p><p id="progress-status" class="font-bold text-slate-100">{{ (string) ($progress['membership_status'] ?? '-') }}</p></div>
                <div class="rounded-xl border border-slate-700 bg-slate-900/70 p-2"><p class="text-xs text-slate-400">Vence</p><p id="progress-ends" class="font-bold text-slate-100">{{ (string) ($progress['membership_ends_at'] ?? '-') }}</p></div>
                <div class="rounded-xl border border-slate-700 bg-slate-900/70 p-2"><p class="text-xs text-slate-400">Visitas mes</p><p id="progress-month" class="font-bold text-slate-100">{{ (int) ($progress['month_visits'] ?? 0) }}</p></div>
                <div class="rounded-xl border border-slate-700 bg-slate-900/70 p-2"><p class="text-xs text-slate-400">Total visitas</p><p id="progress-total" class="font-bold text-slate-100">{{ (int) ($progress['total_visits'] ?? 0) }}</p></div>
            </div>
        </article>

        <article class="mobile-card rounded-2xl p-4 space-y-3">
            <h2 class="text-sm font-black uppercase tracking-wide text-cyan-200">Validar ingreso</h2>
            <p class="text-xs text-slate-300">{{ __('messages.client_mobile.scan_qr_hint') }}</p>

            <video id="scan-video" playsinline muted class="hidden"></video>

            <div class="flex gap-2">
                <button id="start-scan" type="button" class="ui-button ui-button-primary flex-1 justify-center">Escanear QR</button>
                <button id="stop-scan" type="button" class="ui-button ui-button-ghost hidden">Detener</button>
            </div>

            <label class="block space-y-1 text-sm">
                <span class="text-slate-300">Código manual (fallback)</span>
                <input id="manual-token" type="text" class="ui-input" placeholder="Pega token o contenido QR">
            </label>

            <button id="send-manual" type="button" class="ui-button ui-button-secondary w-full justify-center">Validar código</button>
            <p id="checkin-status" class="rounded-lg border border-slate-700 bg-slate-900/70 px-3 py-2 text-xs text-slate-200">{{ __('messages.client_mobile.ready_to_scan') }}</p>
        </article>

        <form method="POST" action="{{ route('client-mobile.logout', ['gymSlug' => $gym->slug]) }}">
            @csrf
            <button type="submit" class="ui-button ui-button-ghost w-full justify-center">Cerrar sesión</button>
        </form>
    </section>
</main>

<script>
(function () {
    const shell = document.querySelector('main.mobile-shell');
    if (!shell) return;

    const checkinUrl = shell.dataset.checkinUrl || '';
    const progressUrl = shell.dataset.progressUrl || '';
    const csrfMeta = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

    const statusEl = document.getElementById('checkin-status');
    const startBtn = document.getElementById('start-scan');
    const stopBtn = document.getElementById('stop-scan');
    const video = document.getElementById('scan-video');
    const manualInput = document.getElementById('manual-token');
    const sendManual = document.getElementById('send-manual');
    const liveCountEl = document.getElementById('live-clients-count');
    const liveWindowEl = document.getElementById('live-clients-window');
    const mobileI18n = {
        ready_to_scan: @json(__('messages.client_mobile.ready_to_scan')),
        manual_token_empty: @json(__('messages.client_mobile.manual_token_empty')),
        validating_entry: @json(__('messages.client_mobile.validating_entry')),
        session_expired_reload: @json(__('messages.client_mobile.session_expired_reload')),
        checkin_success: @json(__('messages.client_mobile.checkin_success')),
        validation_failed: @json(__('messages.client_mobile.validation_failed')),
        network_validation_failed: @json(__('messages.client_mobile.network_validation_failed')),
        scan_in_progress: @json(__('messages.client_mobile.scan_in_progress')),
        camera_open_failed: @json(__('messages.client_mobile.camera_open_failed')),
        scan_qr_unsupported: @json(__('messages.client_mobile.browser_qr_not_supported')),
    };

    let stream = null;
    let scanTimer = null;
    let detector = null;

    function readCookie(name) {
        const source = String(document.cookie || '');
        if (source === '') return '';

        const parts = source.split(';');
        for (let i = 0; i < parts.length; i += 1) {
            const part = parts[i].trim();
            if (!part.startsWith(name + '=')) continue;
            return part.slice(name.length + 1);
        }

        return '';
    }

    function resolveCsrfToken() {
        const cookieRaw = readCookie('XSRF-TOKEN');
        if (cookieRaw !== '') {
            try {
                return decodeURIComponent(cookieRaw);
            } catch (error) {
                return cookieRaw;
            }
        }

        return csrfMeta;
    }

    async function refreshProgress() {
        if (!progressUrl) return;
        try {
            const res = await fetch(progressUrl, { headers: { 'Accept': 'application/json' }, credentials: 'same-origin' });
            if (!res.ok) return;
            const payload = await res.json();
            if (!payload || !payload.ok || !payload.progress) return;
            document.getElementById('progress-status').textContent = payload.progress.membership_status || '-';
            document.getElementById('progress-ends').textContent = payload.progress.membership_ends_at || '-';
            document.getElementById('progress-month').textContent = String(payload.progress.month_visits ?? 0);
            document.getElementById('progress-total').textContent = String(payload.progress.total_visits ?? 0);
            if (liveCountEl) {
                const next = String(payload.progress.live_clients_count ?? 0);
                if (liveCountEl.textContent !== next) {
                    liveCountEl.classList.remove('live-count-pop');
                    void liveCountEl.offsetWidth;
                    liveCountEl.classList.add('live-count-pop');
                }
                liveCountEl.textContent = next;
            }
            if (liveWindowEl) {
                const label = String(payload.progress.live_window_label || 'En vivo');
                liveWindowEl.textContent = 'Conteo de ' + label + '. Actualiza automático.';
            }
        } catch (error) {
            // ignore
        }
    }

    async function submitToken(rawToken) {
        const token = String(rawToken || '').trim();
        if (token === '') {
            statusEl.textContent = mobileI18n.manual_token_empty;
            return;
        }

        statusEl.textContent = mobileI18n.validating_entry;

        try {
            const csrf = resolveCsrfToken();
            const res = await fetch(checkinUrl, {
                method: 'POST',
                credentials: 'same-origin',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrf,
                    'X-XSRF-TOKEN': csrf,
                },
                body: JSON.stringify({ token }),
            });

            const payload = await res.json();
            if (res.status === 419) {
                statusEl.textContent = mobileI18n.session_expired_reload;
                return;
            }
            if (payload.ok) {
                statusEl.textContent = payload.message || mobileI18n.checkin_success;
                manualInput.value = '';
                await refreshProgress();
                stopScan();
                return;
            }

            statusEl.textContent = payload.message || mobileI18n.validation_failed;
        } catch (error) {
            statusEl.textContent = mobileI18n.network_validation_failed;
        }
    }

    async function startScan() {
        if (!('BarcodeDetector' in window)) {
            statusEl.textContent = mobileI18n.scan_qr_unsupported;
            return;
        }

        detector = new BarcodeDetector({ formats: ['qr_code'] });

        try {
            stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: { ideal: 'environment' } }, audio: false });
            video.srcObject = stream;
            await video.play();
            video.classList.remove('hidden');
            stopBtn.classList.remove('hidden');
            startBtn.classList.add('hidden');
            statusEl.textContent = mobileI18n.scan_in_progress;

            scanTimer = window.setInterval(async () => {
                if (!detector || !video || video.readyState < 2) return;
                try {
                    const codes = await detector.detect(video);
                    if (!codes || !codes.length) return;
                    const raw = String(codes[0].rawValue || '').trim();
                    if (raw === '') return;
                    await submitToken(raw);
                } catch (error) {
                    // ignore decode errors
                }
            }, 350);
        } catch (error) {
            statusEl.textContent = mobileI18n.camera_open_failed;
            stopScan();
        }
    }

    function stopScan() {
        if (scanTimer) {
            clearInterval(scanTimer);
            scanTimer = null;
        }

        if (stream) {
            stream.getTracks().forEach((track) => track.stop());
            stream = null;
        }

        video.pause();
        video.srcObject = null;
        video.classList.add('hidden');
        stopBtn.classList.add('hidden');
        startBtn.classList.remove('hidden');
    }

    startBtn.addEventListener('click', startScan);
    stopBtn.addEventListener('click', stopScan);
    sendManual.addEventListener('click', () => submitToken(manualInput.value));

    refreshProgress();
    window.setInterval(refreshProgress, 20000);
})();
</script>
</body>
</html>
