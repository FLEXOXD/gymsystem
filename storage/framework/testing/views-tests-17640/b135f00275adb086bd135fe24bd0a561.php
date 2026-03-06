<?php $__env->startSection('title', 'Escaner movil recepcion'); ?>
<?php $__env->startSection('page-title', 'Escaner movil'); ?>

<?php $__env->startSection('content'); ?>
    <?php if (isset($component)) { $__componentOriginaldae4cd48acb67888a4631e1ba48f2f93 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.card','data' => ['title' => 'Escanear QR desde mi celular','subtitle' => 'Usa la camara del telefono para registrar ingreso o salida sin lector externo.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Escanear QR desde mi celular','subtitle' => 'Usa la camara del telefono para registrar ingreso o salida sin lector externo.']); ?>
        <div class="grid gap-4 lg:grid-cols-[minmax(0,1fr)_360px]">
            <div class="space-y-3">
                <div class="rounded-2xl border border-slate-300 bg-slate-950/90 p-3 dark:border-slate-700">
                    <video id="mobile-scanner-video" class="h-[320px] w-full rounded-xl bg-black object-cover sm:h-[420px]" autoplay muted playsinline></video>
                </div>
                <p id="mobile-scanner-status" class="rounded-lg border border-cyan-300 bg-cyan-50 px-3 py-2 text-sm font-semibold text-cyan-800 dark:border-cyan-700/60 dark:bg-cyan-900/20 dark:text-cyan-100">
                    Listo para iniciar camara.
                </p>
            </div>

            <div class="space-y-3">
                <div class="grid grid-cols-2 gap-2">
                    <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['id' => 'mobile-scanner-start','type' => 'button','variant' => 'primary']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => 'mobile-scanner-start','type' => 'button','variant' => 'primary']); ?>Iniciar camara <?php echo $__env->renderComponent(); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['id' => 'mobile-scanner-stop','type' => 'button','variant' => 'ghost','disabled' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => 'mobile-scanner-stop','type' => 'button','variant' => 'ghost','disabled' => true]); ?>Detener <?php echo $__env->renderComponent(); ?>
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

                <div class="rounded-xl border border-slate-200 bg-slate-50 p-3 dark:border-slate-700 dark:bg-slate-900/70">
                    <p class="text-xs font-black uppercase tracking-[0.14em] text-slate-600 dark:text-slate-300">Modo de escaneo</p>
                    <div class="mt-2 grid grid-cols-2 gap-2">
                        <label class="inline-flex cursor-pointer items-center justify-center rounded-lg border border-emerald-300 bg-emerald-50 px-2 py-2 text-xs font-black text-emerald-800 dark:border-emerald-700/60 dark:bg-emerald-900/20 dark:text-emerald-100">
                            <input id="mobile-scanner-action-checkin" type="radio" name="mobile-scanner-action" value="checkin" class="sr-only" checked>
                            Ingreso
                        </label>
                        <label class="inline-flex cursor-pointer items-center justify-center rounded-lg border border-cyan-300 bg-cyan-50 px-2 py-2 text-xs font-black text-cyan-800 dark:border-cyan-700/60 dark:bg-cyan-900/20 dark:text-cyan-100">
                            <input id="mobile-scanner-action-checkout" type="radio" name="mobile-scanner-action" value="checkout" class="sr-only">
                            Salida
                        </label>
                    </div>
                </div>

                <label class="space-y-2 text-sm font-semibold ui-muted">
                    <span>Codigo manual (fallback)</span>
                    <input id="mobile-scanner-input" type="text" inputmode="text" autocomplete="off" placeholder="RFID, QR o documento"
                           class="ui-input">
                </label>

                <div class="grid grid-cols-2 gap-2">
                    <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['id' => 'mobile-scanner-send-checkin','type' => 'button','variant' => 'secondary']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => 'mobile-scanner-send-checkin','type' => 'button','variant' => 'secondary']); ?>Registrar ingreso <?php echo $__env->renderComponent(); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['id' => 'mobile-scanner-send-checkout','type' => 'button','variant' => 'ghost']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => 'mobile-scanner-send-checkout','type' => 'button','variant' => 'ghost']); ?>Registrar salida <?php echo $__env->renderComponent(); ?>
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

                <div class="rounded-xl border border-slate-300 bg-white p-3 dark:border-slate-700 dark:bg-slate-900/70">
                    <p class="text-xs font-black uppercase tracking-[0.14em] text-slate-500 dark:text-slate-300">Ultimo resultado</p>
                    <p id="mobile-scanner-result" class="mt-2 text-sm font-semibold text-slate-800 dark:text-slate-100">Sin lecturas aun.</p>
                </div>

                <p class="text-xs text-slate-500 dark:text-slate-300">
                    Sede activa: <strong><?php echo e($syncGymName); ?></strong>
                </p>
            </div>
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

            if (!scannerVideo || !statusEl || !resultEl || !inputEl || !startBtn || !stopBtn || !sendCheckInBtn || !sendCheckOutBtn) {
                return;
            }

            const SCAN_DEBOUNCE_MS = 2600;
            let stream = null;
            let detector = null;
            let frameRequestId = null;
            let detectBusy = false;
            let submitting = false;
            let lastScannedValue = '';
            let lastScannedAt = 0;

            function currentAction() {
                return actionCheckOut && actionCheckOut.checked ? 'checkout' : 'checkin';
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
                    } catch (error) {
                        // Ignore malformed URLs and keep original value.
                    }
                }

                return value.trim();
            }

            function extractBarcodeValue(code) {
                if (!code || typeof code !== 'object') {
                    return '';
                }

                if (typeof code.rawValue === 'string') {
                    return code.rawValue;
                }

                if (typeof code.displayValue === 'string') {
                    return code.displayValue;
                }

                return '';
            }

            function setScannerActiveState(active) {
                startBtn.disabled = active;
                stopBtn.disabled = !active;
            }

            function stopCamera() {
                if (frameRequestId !== null) {
                    cancelAnimationFrame(frameRequestId);
                    frameRequestId = null;
                }
                detectBusy = false;

                if (stream) {
                    stream.getTracks().forEach(function (track) {
                        track.stop();
                    });
                    stream = null;
                }

                scannerVideo.srcObject = null;
                setScannerActiveState(false);
                setStatus('Camara detenida.');
            }

            async function ensureQrDetector() {
                if (!('BarcodeDetector' in window)) {
                    return false;
                }

                if (typeof BarcodeDetector.getSupportedFormats === 'function') {
                    try {
                        const formats = await BarcodeDetector.getSupportedFormats();
                        if (Array.isArray(formats) && !formats.includes('qr_code')) {
                            return false;
                        }
                    } catch (error) {
                        return false;
                    }
                }

                try {
                    detector = new BarcodeDetector({ formats: ['qr_code'] });
                    return true;
                } catch (error) {
                    detector = null;
                    return false;
                }
            }

            function normalizeApiPayload(payload, fallbackMessage) {
                const safePayload = payload && typeof payload === 'object' ? payload : {};
                return {
                    ok: Boolean(safePayload.ok),
                    reason: safePayload.reason ? String(safePayload.reason) : '',
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
                    } catch (error) {
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
                } catch (error) {
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

            function scheduleScanLoop() {
                if (!stream || !detector) {
                    return;
                }

                frameRequestId = requestAnimationFrame(async function scanFrame() {
                    if (!stream || !detector) {
                        frameRequestId = null;
                        return;
                    }

                    if (
                        !detectBusy
                        && scannerVideo.readyState >= 2
                        && !submitting
                    ) {
                        detectBusy = true;
                        try {
                            const detected = await detector.detect(scannerVideo);
                            if (Array.isArray(detected) && detected.length > 0) {
                                const rawValue = extractBarcodeValue(detected[0]);
                                const normalized = normalizeInput(rawValue);
                                const now = Date.now();
                                if (
                                    normalized !== ''
                                    && (
                                        normalized !== lastScannedValue
                                        || (now - lastScannedAt) > SCAN_DEBOUNCE_MS
                                    )
                                ) {
                                    lastScannedValue = normalized;
                                    lastScannedAt = now;
                                    inputEl.value = normalized;
                                    await submitValue(normalized, currentAction());
                                }
                            }
                        } catch (error) {
                            // Keep scanning loop alive.
                        } finally {
                            detectBusy = false;
                        }
                    }

                    frameRequestId = requestAnimationFrame(scanFrame);
                });
            }

            async function startCamera() {
                if (stream) {
                    return;
                }

                if (!window.isSecureContext && location.hostname !== 'localhost' && location.hostname !== '127.0.0.1') {
                    setStatus('La camara requiere HTTPS.', 'error');
                    return;
                }

                if (!navigator.mediaDevices || typeof navigator.mediaDevices.getUserMedia !== 'function') {
                    setStatus('Este navegador no soporta acceso a camara.', 'error');
                    return;
                }

                const detectorReady = await ensureQrDetector();
                if (!detectorReady) {
                    setStatus('Este navegador no soporta lector QR interno. Usa codigo manual.', 'warn');
                    return;
                }

                try {
                    stream = await navigator.mediaDevices.getUserMedia({
                        video: {
                            facingMode: { ideal: 'environment' },
                            width: { ideal: 1280 },
                            height: { ideal: 720 },
                        },
                        audio: false,
                    });
                } catch (error) {
                    if (error && error.name === 'NotAllowedError') {
                        setStatus('Permiso de camara denegado. Habilitalo e intenta de nuevo.', 'error');
                        return;
                    }
                    if (error && error.name === 'NotFoundError') {
                        setStatus('No se encontro camara disponible en este dispositivo.', 'error');
                        return;
                    }
                    setStatus('No se pudo iniciar la camara.', 'error');
                    return;
                }

                scannerVideo.srcObject = stream;
                try {
                    await scannerVideo.play();
                } catch (error) {
                    stopCamera();
                    setStatus('No se pudo reproducir vista previa de camara.', 'error');
                    return;
                }

                setScannerActiveState(true);
                setStatus('Camara activa. Apunta al QR del cliente.', 'ok');
                scheduleScanLoop();
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

            inputEl.addEventListener('keydown', function (event) {
                if (event.key !== 'Enter') {
                    return;
                }

                event.preventDefault();
                submitValue(inputEl.value, currentAction());
            });

            document.addEventListener('visibilitychange', function () {
                if (document.hidden && stream) {
                    stopCamera();
                }
            });

            window.addEventListener('beforeunload', function () {
                stopCamera();
            });
        })();
    </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.panel', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\gymsystem\resources\views/reception/mobile-scanner.blade.php ENDPATH**/ ?>