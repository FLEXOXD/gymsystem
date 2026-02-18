@extends('layouts.panel')

@section('title', 'Recepcion')
@section('page-title', 'Modo recepcion PRO')

@section('content')
    <x-ui.card title="Check-in unificado" subtitle="Escanea RFID/QR o escribe documento. Soporta auto-envio por lector tipo teclado.">
        <div class="grid gap-4 md:grid-cols-[1fr_auto] md:items-end">
            <label class="space-y-2 text-sm font-semibold ui-muted">
                <span>Valor de entrada</span>
                <input id="value" name="value" type="text" inputmode="text" autocomplete="off" autofocus
                       placeholder="RFID, QR o documento"
                       class="ui-input h-16 rounded-xl border-2 px-4 text-2xl font-black tracking-wide md:h-20 md:text-3xl">
            </label>

            <div class="flex flex-wrap items-center gap-2 md:pb-1">
                <x-ui.button id="send-btn" type="button" variant="primary" size="lg" class="h-14 md:h-16">Enviar</x-ui.button>
                <label class="inline-flex items-center gap-2 rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-xs font-bold uppercase tracking-wide text-slate-700 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200">
                    <input id="auto-submit-enabled" type="checkbox" class="h-4 w-4" checked>
                    Auto scanner
                </label>
                <label class="inline-flex items-center gap-2 rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-xs font-bold uppercase tracking-wide text-slate-700 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200">
                    <input id="sound-enabled" type="checkbox" class="h-4 w-4" checked>
                    Sonido
                </label>
            </div>
        </div>

        <p id="status-chip" class="mt-4 inline-flex rounded-full bg-cyan-100 px-3 py-1 text-xs font-bold uppercase tracking-wide text-cyan-800 dark:bg-cyan-900/40 dark:text-cyan-200">
            Listo para escanear
        </p>
    </x-ui.card>

    <x-ui.card id="result-panel" class="border-slate-300 bg-white dark:border-slate-700 dark:bg-slate-900" title="Resultado">
        <div class="grid gap-5 md:grid-cols-[220px_1fr] md:items-center">
            <div class="w-full">
                <img id="result-photo" src="" alt="Foto del cliente" class="hidden h-48 w-full rounded-xl border border-slate-300 object-cover dark:border-slate-700 md:h-56">
                <div id="result-photo-placeholder" class="flex h-48 w-full items-center justify-center rounded-xl border border-dashed border-slate-300 bg-slate-50 text-sm font-medium text-slate-500 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300 md:h-56">
                    Sin foto
                </div>
            </div>

            <div>
                <p id="result-method" class="mb-2 inline-flex rounded-full bg-slate-200 px-3 py-1 text-xs font-bold uppercase tracking-widest text-slate-700 dark:bg-slate-700 dark:text-slate-100">
                    Metodo: -
                </p>
                <p id="result-message" class="text-2xl font-black text-slate-800 dark:text-slate-100 md:text-3xl">Esperando lectura...</p>
                <p id="result-name" class="mt-2 text-xl font-bold text-slate-900 dark:text-slate-100 md:text-2xl">-</p>
                <p id="result-membership" class="mt-2 text-base text-slate-700 dark:text-slate-300">Fin membresia: -</p>
            </div>
        </div>
    </x-ui.card>

    <x-ui.card title="Ultimos 10 check-ins">
        <div class="overflow-x-auto">
            <table class="ui-table min-w-[780px]">
                <thead>
                <tr class="border-b border-slate-200 bg-slate-50 text-left text-xs uppercase tracking-wider text-slate-500 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300">
                    <th class="px-3 py-3">Fecha</th>
                    <th class="px-3 py-3">Hora</th>
                    <th class="px-3 py-3">Cliente</th>
                    <th class="px-3 py-3">Metodo</th>
                </tr>
                </thead>
                <tbody>
                @forelse ($recentAttendances as $attendance)
                    <tr class="border-b border-slate-100 text-sm odd:bg-white even:bg-slate-50 dark:border-slate-800 dark:odd:bg-slate-900 dark:even:bg-slate-950/50">
                        <td class="px-3 py-3 dark:text-slate-200">{{ $attendance->date?->toDateString() ?? '-' }}</td>
                        <td class="px-3 py-3 dark:text-slate-200">{{ $attendance->time ?? '-' }}</td>
                        <td class="px-3 py-3 font-semibold dark:text-slate-100">{{ $attendance->client?->full_name ?? '-' }}</td>
                        <td class="px-3 py-3 dark:text-slate-200">{{ $attendance->credential?->type ?? 'document' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-3 py-6 text-center text-sm text-slate-500 dark:text-slate-300">Sin check-ins recientes.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </x-ui.card>
@endsection

@push('scripts')
<script>
    (function () {
        const AUTO_RESET_MS = 4000;
        const SCAN_SPEED_MS = 35;
        const SCAN_MIN_LENGTH = 6;
        const SCAN_IDLE_SUBMIT_MS = 90;
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        const input = document.getElementById('value');
        const sendBtn = document.getElementById('send-btn');
        const autoSubmitEnabled = document.getElementById('auto-submit-enabled');
        const soundEnabled = document.getElementById('sound-enabled');
        const panel = document.getElementById('result-panel');
        const statusChip = document.getElementById('status-chip');
        const photo = document.getElementById('result-photo');
        const photoPlaceholder = document.getElementById('result-photo-placeholder');
        const method = document.getElementById('result-method');
        const message = document.getElementById('result-message');
        const name = document.getElementById('result-name');
        const membership = document.getElementById('result-membership');

        let resetTimer = null;
        let scanTimer = null;
        let submitting = false;
        let lastKeyTimestamp = 0;
        let burstCount = 0;
        let scannerLikely = false;

        function basePanelClasses() {
            return 'rounded-2xl border p-5 shadow-sm';
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

        function safePayload(payload) {
            return {
                ok: Boolean(payload && payload.ok),
                message: payload && payload.message ? payload.message : 'Respuesta invalida del servidor.',
                method: payload && Object.prototype.hasOwnProperty.call(payload, 'method') ? payload.method : null,
                client: payload && Object.prototype.hasOwnProperty.call(payload, 'client') ? payload.client : null,
            };
        }

        function playTone(type) {
            if (!soundEnabled.checked) return;

            try {
                const context = new (window.AudioContext || window.webkitAudioContext)();
                const oscillator = context.createOscillator();
                const gain = context.createGain();
                oscillator.connect(gain);
                gain.connect(context.destination);

                oscillator.type = 'sine';
                oscillator.frequency.value = type === 'ok' ? 760 : 220;
                gain.gain.value = 0.03;

                oscillator.start();
                oscillator.stop(context.currentTime + (type === 'ok' ? 0.12 : 0.2));
            } catch (error) {
                // Ignore audio failures silently.
            }
        }

        function focusInput() {
            input.focus({ preventScroll: true });
        }

        function resetScanDetection() {
            burstCount = 0;
            scannerLikely = false;
            lastKeyTimestamp = 0;
            if (scanTimer) {
                clearTimeout(scanTimer);
                scanTimer = null;
            }
        }

        function scheduleScannerSubmit() {
            if (!autoSubmitEnabled.checked || !scannerLikely) return;
            if (scanTimer) clearTimeout(scanTimer);

            scanTimer = setTimeout(function () {
                submitCheckIn();
            }, SCAN_IDLE_SUBMIT_MS);
        }

        function registerBurstKey(nowTimestamp) {
            const delta = nowTimestamp - lastKeyTimestamp;
            lastKeyTimestamp = nowTimestamp;

            if (delta > 0 && delta <= SCAN_SPEED_MS) {
                burstCount += 1;
            } else {
                burstCount = 1;
            }

            scannerLikely = burstCount >= SCAN_MIN_LENGTH;
        }

        function render(payload) {
            if (payload.ok) {
                panel.className = basePanelClasses() + ' border-emerald-300 bg-emerald-50 dark:border-emerald-700 dark:bg-emerald-900/30';
                method.className = 'mb-2 inline-flex rounded-full bg-emerald-200 px-3 py-1 text-xs font-bold uppercase tracking-widest text-emerald-800 dark:bg-emerald-800/40 dark:text-emerald-200';
                message.className = 'text-2xl font-black text-emerald-700 dark:text-emerald-200 md:text-3xl';
                statusChip.className = 'mt-4 inline-flex rounded-full bg-emerald-100 px-3 py-1 text-xs font-bold uppercase tracking-wide text-emerald-800';
                statusChip.textContent = 'Acceso permitido';
                playTone('ok');
            } else {
                panel.className = basePanelClasses() + ' border-rose-300 bg-rose-50 dark:border-rose-700 dark:bg-rose-900/30';
                method.className = 'mb-2 inline-flex rounded-full bg-rose-200 px-3 py-1 text-xs font-bold uppercase tracking-widest text-rose-800 dark:bg-rose-800/40 dark:text-rose-200';
                message.className = 'text-2xl font-black text-rose-700 dark:text-rose-200 md:text-3xl';
                statusChip.className = 'mt-4 inline-flex rounded-full bg-rose-100 px-3 py-1 text-xs font-bold uppercase tracking-wide text-rose-800';
                statusChip.textContent = 'Acceso denegado';
                playTone('error');
            }

            message.textContent = payload.message;
            method.textContent = 'Metodo: ' + (payload.method || '-');

            if (payload.client) {
                name.textContent = payload.client.full_name || '-';
                membership.textContent = 'Fin membresia: ' + (payload.client.membership_ends_at || '-');

                if (payload.client.photo_url) {
                    photo.src = payload.client.photo_url;
                    photo.classList.remove('hidden');
                    photoPlaceholder.classList.add('hidden');
                } else {
                    photo.classList.add('hidden');
                    photoPlaceholder.classList.remove('hidden');
                }
            } else {
                name.textContent = '-';
                membership.textContent = 'Fin membresia: -';
                photo.classList.add('hidden');
                photoPlaceholder.classList.remove('hidden');
            }
        }

        function renderIdle() {
            panel.className = basePanelClasses() + ' border-slate-300 bg-white dark:border-slate-700 dark:bg-slate-900';
            method.className = 'mb-2 inline-flex rounded-full bg-slate-200 px-3 py-1 text-xs font-bold uppercase tracking-widest text-slate-700 dark:bg-slate-700 dark:text-slate-100';
            method.textContent = 'Metodo: -';
            message.className = 'text-2xl font-black text-slate-800 dark:text-slate-100 md:text-3xl';
            message.textContent = 'Esperando lectura...';
            statusChip.className = 'mt-4 inline-flex rounded-full bg-cyan-100 px-3 py-1 text-xs font-bold uppercase tracking-wide text-cyan-800';
            statusChip.textContent = 'Listo para escanear';
            name.textContent = '-';
            membership.textContent = 'Fin membresia: -';
            photo.classList.add('hidden');
            photoPlaceholder.classList.remove('hidden');
            resetScanDetection();
        }

        function scheduleReset() {
            if (resetTimer) clearTimeout(resetTimer);
            resetTimer = setTimeout(function () {
                renderIdle();
                focusInput();
            }, AUTO_RESET_MS);
        }

        async function submitCheckIn(forcedValue = null) {
            if (submitting) return;

            const value = normalizeInput(forcedValue === null ? input.value : forcedValue);
            if (!value) {
                render({ ok: false, message: 'Ingrese un valor para procesar.', method: null, client: null });
                scheduleReset();
                focusInput();
                return;
            }

            input.value = value;
            submitting = true;
            sendBtn.setAttribute('disabled', 'disabled');
            sendBtn.textContent = 'Procesando...';
            statusChip.className = 'mt-4 inline-flex rounded-full bg-amber-100 px-3 py-1 text-xs font-bold uppercase tracking-wide text-amber-800';
            statusChip.textContent = 'Procesando...';

            let payload;
            try {
                const response = await fetch('{{ route('reception.check-in') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                    body: JSON.stringify({ value: value }),
                });

                payload = safePayload(await response.json());
            } catch (error) {
                payload = {
                    ok: false,
                    message: 'Error de red. Verifique conexion y vuelva a intentar.',
                    method: null,
                    client: null,
                };
            }

            render(payload);
            input.value = '';
            scheduleReset();
            focusInput();

            sendBtn.removeAttribute('disabled');
            sendBtn.textContent = 'Enviar';
            submitting = false;
            resetScanDetection();
        }

        sendBtn.addEventListener('click', submitCheckIn);
        input.addEventListener('keydown', function (event) {
            if (event.key === 'Enter' || event.key === 'Tab') {
                event.preventDefault();
                submitCheckIn();
                return;
            }

            if (event.ctrlKey || event.metaKey || event.altKey) {
                return;
            }

            if (event.key.length === 1) {
                registerBurstKey(Date.now());
            }
        });

        input.addEventListener('input', function () {
            input.value = normalizeInput(input.value);
            scheduleScannerSubmit();
        });

        window.addEventListener('keydown', function (event) {
            if (event.ctrlKey || event.metaKey || event.altKey) return;

            const target = event.target;
            const isInputFocused = target === input;

            if (event.key === 'Enter' || event.key === 'Tab') {
                if (!isInputFocused && input.value.trim() !== '') {
                    event.preventDefault();
                    submitCheckIn();
                }
                return;
            }

            if (event.key.length !== 1) return;

            registerBurstKey(Date.now());

            if (!isInputFocused) {
                event.preventDefault();
                focusInput();
                input.value = normalizeInput(input.value + event.key);
            }

            scheduleScannerSubmit();
        }, true);

        input.addEventListener('blur', function () {
            setTimeout(focusInput, 120);
        });

        setInterval(function () {
            if (!document.hidden && document.activeElement !== input) {
                focusInput();
            }
        }, 1000);

        renderIdle();
        focusInput();
    })();
</script>
@endpush
