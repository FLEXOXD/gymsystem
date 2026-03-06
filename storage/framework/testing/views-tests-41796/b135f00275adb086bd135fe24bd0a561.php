<?php $__env->startSection('title', 'Escaner movil recepcion'); ?>
<?php $__env->startSection('page-title', 'Escaner movil'); ?>

<?php $__env->startSection('content'); ?>
    <?php if (isset($component)) { $__componentOriginaldae4cd48acb67888a4631e1ba48f2f93 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.card','data' => ['title' => 'Escanear QR desde mi celular','subtitle' => 'Funciona en celular y laptop. Si un modo falla, activa modo compatible automaticamente.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Escanear QR desde mi celular','subtitle' => 'Funciona en celular y laptop. Si un modo falla, activa modo compatible automaticamente.']); ?>
        <div class="grid gap-5 xl:grid-cols-[minmax(0,1fr)_370px]">
            <section class="space-y-3">
                <div class="rounded-2xl border border-slate-300 bg-slate-950/90 p-3 dark:border-slate-700">
                    <video id="mobile-scanner-video" class="h-[340px] w-full rounded-xl bg-black object-cover sm:h-[460px]" autoplay muted playsinline></video>
                </div>

                <p id="mobile-scanner-status" class="rounded-lg border border-cyan-300 bg-cyan-50 px-3 py-2 text-sm font-semibold text-cyan-800 dark:border-cyan-700/60 dark:bg-cyan-900/20 dark:text-cyan-100">
                    Listo para iniciar camara.
                </p>

                <p class="text-xs text-slate-500 dark:text-slate-300">
                    Sede activa: <strong><?php echo e($syncGymName); ?></strong>
                </p>
            </section>

            <aside class="space-y-3">
                <div class="rounded-xl border border-slate-200 bg-slate-50 p-3 dark:border-slate-700 dark:bg-slate-900/70">
                    <p class="text-xs font-black uppercase tracking-[0.14em] text-slate-600 dark:text-slate-300">1) Camara</p>
                    <div class="mt-2 grid grid-cols-2 gap-2">
                        <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['id' => 'mobile-scanner-start','type' => 'button','variant' => 'primary','class' => 'w-full']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => 'mobile-scanner-start','type' => 'button','variant' => 'primary','class' => 'w-full']); ?>Iniciar camara <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $attributes = $__attributesOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__attributesOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $component = $__componentOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__componentOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
                        <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['id' => 'mobile-scanner-stop','type' => 'button','variant' => 'ghost','class' => 'w-full','disabled' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => 'mobile-scanner-stop','type' => 'button','variant' => 'ghost','class' => 'w-full','disabled' => true]); ?>Detener <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $attributes = $__attributesOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__attributesOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $component = $__componentOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__componentOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
                    </div>
                    <p class="mt-2 text-xs text-slate-500 dark:text-slate-300">
                        Al iniciar, el navegador debe mostrar el popup para permitir camara.
                    </p>
                </div>

                <div class="rounded-xl border border-slate-200 bg-slate-50 p-3 dark:border-slate-700 dark:bg-slate-900/70">
                    <p class="text-xs font-black uppercase tracking-[0.14em] text-slate-600 dark:text-slate-300">2) Tipo de registro</p>
                    <div class="mt-2 grid grid-cols-2 gap-2">
                        <label id="mobile-scan-card-checkin" class="inline-flex cursor-pointer items-center justify-center rounded-lg border border-emerald-300 bg-emerald-50 px-2 py-2 text-xs font-black text-emerald-800 dark:border-emerald-700/60 dark:bg-emerald-900/20 dark:text-emerald-100">
                            <input id="mobile-scanner-action-checkin" type="radio" name="mobile-scanner-action" value="checkin" class="sr-only" checked>
                            Ingreso
                        </label>
                        <label id="mobile-scan-card-checkout" class="inline-flex cursor-pointer items-center justify-center rounded-lg border border-slate-300 bg-white px-2 py-2 text-xs font-black text-slate-700 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100">
                            <input id="mobile-scanner-action-checkout" type="radio" name="mobile-scanner-action" value="checkout" class="sr-only">
                            Salida
                        </label>
                    </div>
                </div>

                <div class="rounded-xl border border-slate-200 bg-slate-50 p-3 dark:border-slate-700 dark:bg-slate-900/70">
                    <p class="text-xs font-black uppercase tracking-[0.14em] text-slate-600 dark:text-slate-300">3) Fallback manual</p>
                    <label class="mt-2 block space-y-2 text-sm font-semibold ui-muted">
                        <span>Codigo manual</span>
                        <input id="mobile-scanner-input" type="text" inputmode="text" autocomplete="off" placeholder="RFID, QR o documento" class="ui-input">
                    </label>
                    <div class="mt-2 grid grid-cols-2 gap-2">
                        <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['id' => 'mobile-scanner-send-checkin','type' => 'button','variant' => 'secondary','class' => 'w-full']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => 'mobile-scanner-send-checkin','type' => 'button','variant' => 'secondary','class' => 'w-full']); ?>Registrar ingreso <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $attributes = $__attributesOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__attributesOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $component = $__componentOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__componentOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
                        <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['id' => 'mobile-scanner-send-checkout','type' => 'button','variant' => 'ghost','class' => 'w-full']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => 'mobile-scanner-send-checkout','type' => 'button','variant' => 'ghost','class' => 'w-full']); ?>Registrar salida <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $attributes = $__attributesOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__attributesOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $component = $__componentOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__componentOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
                    </div>
                </div>

                <div class="rounded-xl border border-slate-300 bg-white p-3 dark:border-slate-700 dark:bg-slate-900/70">
                    <p class="text-xs font-black uppercase tracking-[0.14em] text-slate-500 dark:text-slate-300">Ultimo resultado</p>
                    <p id="mobile-scanner-result" class="mt-2 text-sm font-semibold text-slate-800 dark:text-slate-100">Sin lecturas aun.</p>
                </div>
            </aside>
        </div>
     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93)): ?>
<?php $attributes = $__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93; ?>
<?php unset($__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93); ?>
<?php endif; ?>
<?php if (isset($__componentOriginaldae4cd48acb67888a4631e1ba48f2f93)): ?>
<?php $component = $__componentOriginaldae4cd48acb67888a4631e1ba48f2f93; ?>
<?php unset($__componentOriginaldae4cd48acb67888a4631e1ba48f2f93); ?>
<?php endif; ?>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
    <script>
        (function () {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
            const checkInEndpoint = <?php echo json_encode(route('reception.check-in'), 15, 512) ?>;
            const checkOutEndpoint = <?php echo json_encode(route('reception.check-out'), 15, 512) ?>;
            const scannerVideo = document.getElementById('mobile-scanner-video');
            const statusEl = document.getElementById('mobile-scanner-status');
            const resultEl = document.getElementById('mobile-scanner-result');
            const inputEl = document.getElementById('mobile-scanner-input');
            const startBtn = document.getElementById('mobile-scanner-start');
            const stopBtn = document.getElementById('mobile-scanner-stop');
            const sendCheckInBtn = document.getElementById('mobile-scanner-send-checkin');
            const sendCheckOutBtn = document.getElementById('mobile-scanner-send-checkout');
            const actionCheckIn = document.getElementById('mobile-scanner-action-checkin');
            const actionCheckOut = document.getElementById('mobile-scanner-action-checkout');
            const actionCheckInCard = document.getElementById('mobile-scan-card-checkin');
            const actionCheckOutCard = document.getElementById('mobile-scan-card-checkout');

            if (!scannerVideo || !statusEl || !resultEl || !inputEl || !startBtn || !stopBtn || !sendCheckInBtn || !sendCheckOutBtn) {
                return;
            }

            const SCAN_DEBOUNCE_MS = 1600;
            let stream = null;
            let detector = null;
            let scanTimer = null;
            let scannerFallbackLibraryPromise = null;
            let fallbackScannerReader = null;
            let fallbackScannerControls = null;
            let scanBusy = false;
            let submitting = false;
            let lastScannedValue = '';
            let lastScannedAt = 0;

            function currentAction() {
                return actionCheckOut && actionCheckOut.checked ? 'checkout' : 'checkin';
            }

            function refreshActionCards() {
                const checkinActive = !(actionCheckOut && actionCheckOut.checked);

                if (actionCheckInCard) {
                    actionCheckInCard.className = checkinActive
                        ? 'inline-flex cursor-pointer items-center justify-center rounded-lg border border-emerald-300 bg-emerald-50 px-2 py-2 text-xs font-black text-emerald-800 dark:border-emerald-700/60 dark:bg-emerald-900/20 dark:text-emerald-100'
                        : 'inline-flex cursor-pointer items-center justify-center rounded-lg border border-slate-300 bg-white px-2 py-2 text-xs font-black text-slate-700 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100';
                }

                if (actionCheckOutCard) {
                    actionCheckOutCard.className = !checkinActive
                        ? 'inline-flex cursor-pointer items-center justify-center rounded-lg border border-cyan-300 bg-cyan-50 px-2 py-2 text-xs font-black text-cyan-800 dark:border-cyan-700/60 dark:bg-cyan-900/20 dark:text-cyan-100'
                        : 'inline-flex cursor-pointer items-center justify-center rounded-lg border border-slate-300 bg-white px-2 py-2 text-xs font-black text-slate-700 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100';
                }
            }

            function setStatus(message, tone = 'info') {
                statusEl.textContent = message;
                if (tone === 'error') {
                    statusEl.className = 'rounded-lg border border-rose-300 bg-rose-50 px-3 py-2 text-sm font-semibold text-rose-800 dark:border-rose-700/60 dark:bg-rose-900/20 dark:text-rose-100';
                    return;
                }
                if (tone === 'ok') {
                    statusEl.className = 'rounded-lg border border-emerald-300 bg-emerald-50 px-3 py-2 text-sm font-semibold text-emerald-800 dark:border-emerald-700/60 dark:bg-emerald-900/20 dark:text-emerald-100';
                    return;
                }
                if (tone === 'warn') {
                    statusEl.className = 'rounded-lg border border-amber-300 bg-amber-50 px-3 py-2 text-sm font-semibold text-amber-800 dark:border-amber-700/60 dark:bg-amber-900/20 dark:text-amber-100';
                    return;
                }

                statusEl.className = 'rounded-lg border border-cyan-300 bg-cyan-50 px-3 py-2 text-sm font-semibold text-cyan-800 dark:border-cyan-700/60 dark:bg-cyan-900/20 dark:text-cyan-100';
            }

            function normalizeInput(rawValue) {
                let value = String(rawValue || '').replace(/[\u0000-\u001f\u007f]/g, '').trim();
                if (value === '') return '';

                const prefixed = value.match(/^(?:uid|rfid|qr|code|codigo)\s*[:\-]\s*(.+)$/i);
                if (prefixed && prefixed[1]) {
                    value = prefixed[1].trim();
                }

                if (value.startsWith('http://') || value.startsWith('https://')) {
                    try {
                        const url = new URL(value);
                        const keys = ['value', 'code', 'uid', 'rfid', 'qr', 'token'];
                        for (const key of keys) {
                            const candidate = (url.searchParams.get(key) || '').trim();
                            if (candidate !== '') {
                                value = candidate;
                                break;
                            }
                        }
                    } catch (_error) {
                        // keep original value
                    }
                }

                return value.trim();
            }

            function setScannerActiveState(active) {
                startBtn.disabled = active;
                stopBtn.disabled = !active;
            }

            function normalizeApiPayload(payload, fallbackMessage) {
                const safePayload = payload && typeof payload === 'object' ? payload : {};
                return {
                    ok: Boolean(safePayload.ok),
                    message: safePayload.message ? String(safePayload.message) : fallbackMessage,
                    method: safePayload.method ? String(safePayload.method) : '-',
                };
            }

            async function submitValue(rawValue, action) {
                if (submitting) {
                    return;
                }

                const value = normalizeInput(rawValue);
                if (value === '') {
                    setStatus('Ingresa o escanea un codigo valido.', 'warn');
                    return;
                }

                const endpoint = action === 'checkout' ? checkOutEndpoint : checkInEndpoint;
                submitting = true;
                sendCheckInBtn.disabled = true;
                sendCheckOutBtn.disabled = true;
                setStatus(action === 'checkout' ? 'Procesando salida...' : 'Procesando ingreso...');

                let parsed = null;
                try {
                    const response = await fetch(endpoint, {
                        method: 'POST',
                        credentials: 'same-origin',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': csrfToken,
                        },
                        body: JSON.stringify({ value: value }),
                    });

                    let payload = null;
                    try {
                        payload = await response.json();
                    } catch (_error) {
                        payload = null;
                    }

                    parsed = normalizeApiPayload(payload, 'No se pudo procesar la solicitud.');
                    if (!response.ok) {
                        setStatus(parsed.message, 'error');
                    } else if (parsed.ok) {
                        setStatus(parsed.message, 'ok');
                    } else {
                        setStatus(parsed.message, 'warn');
                    }
                } catch (_error) {
                    parsed = normalizeApiPayload(null, 'Error de red. Revisa conexion e intenta de nuevo.');
                    setStatus(parsed.message, 'error');
                } finally {
                    submitting = false;
                    sendCheckInBtn.disabled = false;
                    sendCheckOutBtn.disabled = false;
                }

                resultEl.textContent = '[' + (action === 'checkout' ? 'SALIDA' : 'INGRESO') + '] '
                    + parsed.message
                    + ' (metodo: ' + parsed.method + ')';

                if (parsed.ok) {
                    inputEl.value = '';
                } else {
                    inputEl.value = value;
                    inputEl.focus({ preventScroll: true });
                }
            }

            async function supportsNativeQrDetection() {
                if (!('BarcodeDetector' in window)) {
                    return false;
                }

                if (typeof BarcodeDetector.getSupportedFormats !== 'function') {
                    return true;
                }

                try {
                    const formats = await BarcodeDetector.getSupportedFormats();
                    return Array.isArray(formats) && formats.includes('qr_code');
                } catch (_error) {
                    return false;
                }
            }

            async function loadFallbackScannerLibrary() {
                if (window.ZXingBrowser && window.ZXingBrowser.BrowserQRCodeReader) {
                    return window.ZXingBrowser;
                }

                if (scannerFallbackLibraryPromise) {
                    return scannerFallbackLibraryPromise;
                }

                const scriptSources = [
                    'https://unpkg.com/@zxing/browser@0.1.5/umd/zxing-browser.min.js',
                    'https://cdn.jsdelivr.net/npm/@zxing/browser@0.1.5/umd/zxing-browser.min.js',
                ];

                scannerFallbackLibraryPromise = new Promise((resolve, reject) => {
                    const tryLoad = (index) => {
                        if (index >= scriptSources.length) {
                            reject(new Error('No se pudo cargar el lector QR alternativo.'));
                            return;
                        }

                        const script = document.createElement('script');
                        script.src = scriptSources[index];
                        script.async = true;
                        script.onload = () => {
                            if (window.ZXingBrowser && window.ZXingBrowser.BrowserQRCodeReader) {
                                resolve(window.ZXingBrowser);
                                return;
                            }
                            tryLoad(index + 1);
                        };
                        script.onerror = () => {
                            tryLoad(index + 1);
                        };
                        document.head.appendChild(script);
                    };

                    tryLoad(0);
                });

                return scannerFallbackLibraryPromise;
            }

            function stopFallbackScanner() {
                if (fallbackScannerControls && typeof fallbackScannerControls.stop === 'function') {
                    try {
                        fallbackScannerControls.stop();
                    } catch (_error) {
                        // ignore
                    }
                }
                fallbackScannerControls = null;

                if (fallbackScannerReader && typeof fallbackScannerReader.reset === 'function') {
                    try {
                        fallbackScannerReader.reset();
                    } catch (_error) {
                        // ignore
                    }
                }
                fallbackScannerReader = null;
            }

            function stopCamera(silent = false) {
                if (scanTimer) {
                    clearInterval(scanTimer);
                    scanTimer = null;
                }

                stopFallbackScanner();
                detector = null;
                scanBusy = false;

                if (stream) {
                    stream.getTracks().forEach(function (track) {
                        track.stop();
                    });
                    stream = null;
                }

                const boundStream = scannerVideo.srcObject;
                if (boundStream && typeof boundStream.getTracks === 'function') {
                    boundStream.getTracks().forEach(function (track) {
                        track.stop();
                    });
                }

                scannerVideo.srcObject = null;
                setScannerActiveState(false);
                if (!silent) {
                    setStatus('Camara detenida.');
                }
            }

            function resolveCameraErrorMessage(error) {
                const errorName = String(error && error.name ? error.name : '').trim();
                if (errorName === 'NotAllowedError' || errorName === 'PermissionDeniedError') {
                    return 'No se concedio permiso de camara. Pulsa Iniciar camara y acepta el popup del navegador.';
                }
                if (errorName === 'NotFoundError' || errorName === 'DevicesNotFoundError') {
                    return 'No se encontro una camara disponible en este dispositivo.';
                }
                if (errorName === 'NotReadableError' || errorName === 'TrackStartError') {
                    return 'La camara esta en uso por otra app o pestana.';
                }
                if (errorName === 'SecurityError') {
                    return 'El navegador bloqueo la camara por seguridad.';
                }

                return 'No se pudo abrir la camara.';
            }

            async function requestCameraStream() {
                const constraintsAttempts = [
                    { video: { facingMode: { ideal: 'environment' } }, audio: false },
                    { video: { facingMode: 'environment' }, audio: false },
                    { video: true, audio: false },
                ];

                let lastError = null;
                for (const constraints of constraintsAttempts) {
                    try {
                        return await navigator.mediaDevices.getUserMedia(constraints);
                    } catch (error) {
                        lastError = error;
                        const errorName = String(error && error.name ? error.name : '').trim();
                        if (
                            errorName === 'NotAllowedError'
                            || errorName === 'PermissionDeniedError'
                            || errorName === 'SecurityError'
                        ) {
                            break;
                        }
                    }
                }

                throw (lastError || new Error('camera_unavailable'));
            }

            async function startNativeScan() {
                detector = new BarcodeDetector({ formats: ['qr_code'] });
                stream = await requestCameraStream();
                scannerVideo.srcObject = stream;
                await scannerVideo.play();

                scanTimer = window.setInterval(async function () {
                    if (!detector || scannerVideo.readyState < 2 || scanBusy || submitting) {
                        return;
                    }

                    try {
                        const codes = await detector.detect(scannerVideo);
                        if (!codes || !codes.length) {
                            return;
                        }

                        const rawValue = normalizeInput(String(codes[0].rawValue || ''));
                        if (rawValue === '') {
                            return;
                        }

                        const now = Date.now();
                        if (rawValue === lastScannedValue && (now - lastScannedAt) < SCAN_DEBOUNCE_MS) {
                            return;
                        }
                        lastScannedValue = rawValue;
                        lastScannedAt = now;

                        scanBusy = true;
                        try {
                            inputEl.value = rawValue;
                            await submitValue(rawValue, currentAction());
                        } finally {
                            scanBusy = false;
                        }
                    } catch (_error) {
                        // keep loop alive
                    }
                }, 220);

                setScannerActiveState(true);
                setStatus('Escaneando QR (modo nativo)...', 'ok');
            }

            async function startFallbackScan() {
                const zxingBrowser = await loadFallbackScannerLibrary();
                const ReaderCtor = zxingBrowser && (
                    (typeof zxingBrowser.BrowserQRCodeReader === 'function' && zxingBrowser.BrowserQRCodeReader)
                    || (typeof zxingBrowser.BrowserMultiFormatReader === 'function' && zxingBrowser.BrowserMultiFormatReader)
                );
                if (!ReaderCtor) {
                    throw new Error('Fallback QR reader unavailable');
                }

                fallbackScannerReader = new ReaderCtor();
                fallbackScannerControls = await fallbackScannerReader.decodeFromVideoDevice(undefined, scannerVideo, async function (result) {
                    if (!result || scanBusy || submitting) return;

                    const raw = String(typeof result.getText === 'function' ? result.getText() : (result.text || '')).trim();
                    const normalized = normalizeInput(raw);
                    if (normalized === '') return;

                    const now = Date.now();
                    if (normalized === lastScannedValue && (now - lastScannedAt) < SCAN_DEBOUNCE_MS) return;
                    lastScannedValue = normalized;
                    lastScannedAt = now;

                    scanBusy = true;
                    try {
                        inputEl.value = normalized;
                        await submitValue(normalized, currentAction());
                    } finally {
                        scanBusy = false;
                    }
                });

                setScannerActiveState(true);
                setStatus('Escaneando QR (modo compatible)...', 'ok');
            }

            async function startCamera() {
                if (!window.isSecureContext && location.hostname !== 'localhost' && location.hostname !== '127.0.0.1') {
                    setStatus('La camara requiere HTTPS.', 'error');
                    return;
                }

                if (!navigator.mediaDevices || typeof navigator.mediaDevices.getUserMedia !== 'function') {
                    setStatus('Este navegador no soporta acceso a camara.', 'error');
                    return;
                }

                stopCamera(true);
                lastScannedValue = '';
                lastScannedAt = 0;
                setStatus('Abriendo camara...', 'info');
                setScannerActiveState(true);

                const canUseNativeQr = await supportsNativeQrDetection();
                if (canUseNativeQr) {
                    try {
                        await startNativeScan();
                        return;
                    } catch (error) {
                        stopCamera(true);
                        setStatus(resolveCameraErrorMessage(error) + ' Probando modo compatible...', 'warn');
                    }
                }

                try {
                    await startFallbackScan();
                    return;
                } catch (error) {
                    stopCamera(true);
                    const readable = resolveCameraErrorMessage(error);
                    if (readable !== 'No se pudo abrir la camara.') {
                        setStatus(readable, 'error');
                    } else {
                        setStatus('No se pudo abrir lector QR automatico. Usa codigo manual.', 'warn');
                    }
                }
            }

            startBtn.addEventListener('click', function () {
                startCamera();
            });

            stopBtn.addEventListener('click', function () {
                stopCamera();
            });

            sendCheckInBtn.addEventListener('click', function () {
                submitValue(inputEl.value, 'checkin');
            });

            sendCheckOutBtn.addEventListener('click', function () {
                submitValue(inputEl.value, 'checkout');
            });

            actionCheckIn?.addEventListener('change', refreshActionCards);
            actionCheckOut?.addEventListener('change', refreshActionCards);

            inputEl.addEventListener('keydown', function (event) {
                if (event.key !== 'Enter') {
                    return;
                }

                event.preventDefault();
                submitValue(inputEl.value, currentAction());
            });

            document.addEventListener('visibilitychange', function () {
                if (document.hidden) {
                    stopCamera(true);
                }
            });

            window.addEventListener('beforeunload', function () {
                stopCamera(true);
            });

            refreshActionCards();
        })();
    </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.panel', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\gymsystem\resources\views/reception/mobile-scanner.blade.php ENDPATH**/ ?>