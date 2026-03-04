@extends('layouts.panel')

@section('title', 'Recepción')
@section('page-title', 'Modo recepción PRO')

@section('content')
    <x-ui.card title="Ingreso unificado" subtitle="Escanea RFID/QR o escribe documento. Soporta autoenvío por lector tipo teclado.">
        <div class="grid gap-4 md:grid-cols-[1fr_auto] md:items-end">
            <label class="space-y-2 text-sm font-semibold ui-muted">
                <span>Valor de entrada</span>
                <input id="value" name="value" type="text" inputmode="text" autocomplete="off" autofocus
                       placeholder="RFID, QR o documento"
                       class="ui-input h-16 rounded-xl border-2 px-4 text-2xl font-black tracking-wide md:h-20 md:text-3xl">
            </label>

            <div class="flex flex-wrap items-center gap-2 md:pb-1">
                <x-ui.button id="send-btn" type="button" variant="primary" size="lg" class="h-14 md:h-16">Enviar</x-ui.button>
                <x-ui.button id="checkout-btn" type="button" variant="ghost" size="lg" class="h-14 md:h-16">
                    Registrar salida
                </x-ui.button>
                <x-ui.button :href="route('reception.display')" target="_blank" rel="noopener" variant="secondary" size="lg" class="h-14 md:h-16">
                    Pantalla 2 + QR
                </x-ui.button>
                <label class="inline-flex items-center gap-2 rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-xs font-bold uppercase tracking-wide text-slate-700 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200">
                    <input id="auto-submit-enabled" type="checkbox" class="h-4 w-4" checked>
                    Autoescaneo
                </label>
                <label class="inline-flex items-center gap-2 rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-xs font-bold uppercase tracking-wide text-slate-700 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200">
                    <input id="sound-enabled" type="checkbox" class="h-4 w-4" checked>
                    Sonido
                </label>
            </div>
        </div>

        <p id="status-chip" class="mt-4 inline-flex rounded-full bg-cyan-100 px-3 py-1 text-xs font-bold uppercase tracking-wide text-cyan-800 dark:bg-cyan-900/40 dark:text-cyan-200">
            {{ __('messages.reception.ready_to_scan') }}
        </p>
    </x-ui.card>

    @if (!empty($canManageClientAccounts))
        <x-ui.card title="QR dinámico móvil" subtitle="Genera QR temporal para check-in desde la PWA del cliente.">
            <div class="space-y-3">
                <div class="flex justify-end">
                    <p id="mobile-qr-countdown" class="inline-flex rounded-full border border-cyan-300 bg-cyan-50 px-3 py-1 text-xs font-bold uppercase tracking-wide text-cyan-800 dark:border-cyan-700/60 dark:bg-cyan-900/20 dark:text-cyan-100">
                        QR activo
                    </p>
                </div>
                <div id="mobile-qr-svg" class="flex min-h-[180px] items-center justify-center rounded-xl border border-slate-300 bg-white p-3 dark:border-slate-700 dark:bg-slate-900/70">
                    <p class="text-xs text-slate-500 dark:text-slate-300">Generando QR...</p>
                </div>

                <div>
                    <div class="rounded-lg border border-slate-300 bg-slate-50 px-3 py-2 dark:border-slate-700 dark:bg-slate-900/60">
                        <p class="text-[11px] font-bold uppercase tracking-wide text-slate-500 dark:text-slate-400">Payload</p>
                        <p id="mobile-qr-payload" class="mt-1 break-all font-mono text-xs text-slate-700 dark:text-slate-200">-</p>
                    </div>
                </div>

                <div class="grid gap-3 md:grid-cols-[220px_auto_minmax(0,1fr)] md:items-end">
                    <label class="space-y-1 text-sm font-semibold ui-muted">
                        <span>Rotación del QR</span>
                        <input id="mobile-qr-rotate-seconds" type="number" min="10" max="2592000" step="1" value="20" class="ui-input" />
                    </label>

                    <label class="inline-flex items-center gap-2 rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-xs font-bold uppercase tracking-wide text-slate-700 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200">
                        <input id="mobile-qr-auto-refresh" type="checkbox" class="h-4 w-4" checked>
                        {{ __('messages.reception.auto_rotation') }}
                    </label>
                    <div class="flex justify-start md:justify-end">
                        <x-ui.button id="mobile-qr-refresh" type="button" variant="secondary" class="w-full md:w-auto">
                            Regenerar ahora
                        </x-ui.button>
                    </div>
                </div>
            </div>
        </x-ui.card>
    @else
        <div class="ui-alert ui-alert-warning">
            El QR dinámico para app cliente está disponible solo en planes Premium y Sucursales.
        </div>
    @endif

    <x-ui.card id="result-panel" class="relative overflow-hidden border-slate-300 bg-white dark:border-slate-700 dark:bg-slate-900" title="Resultado">
        <div class="pointer-events-none absolute inset-0 bg-gradient-to-br from-cyan-500/10 via-transparent to-amber-400/10"></div>
        <div class="relative grid gap-4 md:grid-cols-[minmax(220px,280px)_minmax(0,1fr)] md:items-start xl:grid-cols-[minmax(260px,320px)_minmax(0,1fr)]">
            <div class="w-full max-w-sm md:max-w-none">
                <div class="relative overflow-hidden rounded-[1.75rem] border border-slate-300/70 bg-slate-900/20 shadow-2xl dark:border-slate-700/80">
                    <img id="result-photo" src="" alt="Foto del cliente" class="hidden h-64 w-full object-cover object-top sm:h-72 md:h-[22rem] xl:h-[26rem]">
                    <div id="result-photo-placeholder" class="flex h-64 w-full flex-col items-center justify-center gap-2 bg-slate-50/80 text-sm font-medium text-slate-500 dark:bg-slate-800/70 dark:text-slate-300 sm:h-72 md:h-[22rem] xl:h-[26rem]">
                        <span id="result-avatar-initials" class="text-5xl font-black tracking-wider text-slate-400 dark:text-slate-300">--</span>
                        <span class="text-xs uppercase tracking-[0.24em]">Avatar gym</span>
                    </div>
                    <div class="pointer-events-none absolute inset-x-0 bottom-0 h-20 bg-gradient-to-t from-slate-950/45 to-transparent"></div>
                </div>
            </div>

            <div class="space-y-4">
                <div class="flex flex-wrap items-center gap-2">
                    <p id="result-method" class="inline-flex rounded-full bg-slate-200 px-3 py-1 text-xs font-bold uppercase tracking-widest text-slate-700 dark:bg-slate-700 dark:text-slate-100">
                        Método: -
                    </p>
                    <span id="result-days-pill" class="inline-flex rounded-full bg-slate-100 px-3 py-1 text-xs font-bold uppercase tracking-wide text-slate-700 dark:bg-slate-700 dark:text-slate-100">
                        Días restantes: -
                    </span>
                    <span id="result-month-pill" class="inline-flex rounded-full bg-slate-100 px-3 py-1 text-xs font-bold uppercase tracking-wide text-slate-700 dark:bg-slate-700 dark:text-slate-100">
                        Visitas mes: -
                    </span>
                </div>

                <p id="result-message" class="pr-1 text-[clamp(1.5rem,4.2vw,2.5rem)] font-black break-words [overflow-wrap:anywhere] text-slate-800 dark:text-slate-100">Esperando lectura...</p>
                <p id="result-name" class="text-[clamp(1.2rem,3.8vw,2rem)] font-bold text-slate-900 dark:text-slate-100">-</p>

                <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
                    <div class="rounded-xl border border-slate-300 bg-white px-3 py-2 dark:border-slate-700 dark:bg-slate-900/60">
                        <p class="text-[11px] font-bold uppercase tracking-wide text-slate-600 dark:text-slate-400">Fin membresía</p>
                        <p id="result-membership" class="mt-1 text-sm font-bold text-slate-900 dark:text-slate-100">-</p>
                    </div>
                    <div class="rounded-xl border border-slate-300 bg-white px-3 py-2 dark:border-slate-700 dark:bg-slate-900/60">
                        <p class="text-[11px] font-bold uppercase tracking-wide text-slate-600 dark:text-slate-400">Fecha check-in</p>
                        <p id="result-checkin-date" class="mt-1 text-sm font-bold text-slate-900 dark:text-slate-100">-</p>
                    </div>
                    <div class="rounded-xl border border-slate-300 bg-white px-3 py-2 dark:border-slate-700 dark:bg-slate-900/60">
                        <p class="text-[11px] font-bold uppercase tracking-wide text-slate-600 dark:text-slate-400">Hora check-in</p>
                        <p id="result-checkin-time" class="mt-1 text-sm font-bold text-slate-900 dark:text-slate-100">-</p>
                    </div>
                    <div class="rounded-xl border border-slate-300 bg-white px-3 py-2 dark:border-slate-700 dark:bg-slate-900/60">
                        <p class="text-[11px] font-bold uppercase tracking-wide text-slate-600 dark:text-slate-400">Visitas del mes</p>
                        <p id="result-month-visits" class="mt-1 text-sm font-bold text-slate-900 dark:text-slate-100">-</p>
                    </div>
                </div>

                <div id="result-motivation" class="rounded-xl border border-cyan-200 bg-cyan-50 px-3 py-2 text-sm font-semibold text-cyan-900 dark:border-cyan-700/60 dark:bg-cyan-900/20 dark:text-cyan-100">
                    Listo para recibir al cliente.
                </div>
            </div>
        </div>
    </x-ui.card>

    <x-ui.card title="Últimos 10 ingresos">
        <div class="mb-3 flex flex-wrap items-center justify-between gap-2">
            <p class="text-xs text-slate-500 dark:text-slate-300">
                Historial detallado disponible para los últimos 2 meses.
            </p>
            <button id="reception-open-history" type="button" class="ui-button ui-button-ghost px-3 py-1.5 text-xs" data-open-attendance-history>
                Ver asistencias (2 meses)
            </button>
        </div>

        <div class="overflow-x-auto">
            <table class="ui-table min-w-[780px]">
                <thead>
                <tr class="border-b border-slate-200 bg-slate-50 text-left text-xs uppercase tracking-wider text-slate-500 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300">
                    <th class="px-3 py-3">Fecha</th>
                    <th class="px-3 py-3">Hora</th>
                    <th class="px-3 py-3">Cliente</th>
                    <th class="px-3 py-3">Método</th>
                </tr>
                </thead>
                <tbody id="recent-attendances-body">
                @forelse ($recentAttendances as $attendance)
                    @php
                        $attendanceMethod = $attendance->credential?->type ?? 'document';
                        $attendanceMethodLabel = match ($attendanceMethod) {
                            'rfid' => 'RFID',
                            'qr' => 'QR',
                            'document' => 'Documento',
                            default => strtoupper((string) $attendanceMethod),
                        };
                    @endphp
                    <tr data-role="recent-attendance-row" data-attendance-id="{{ (int) $attendance->id }}" class="border-b border-slate-100 text-sm odd:bg-white even:bg-slate-50 dark:border-slate-800 dark:odd:bg-slate-900 dark:even:bg-slate-950/50">
                        <td class="px-3 py-3 dark:text-slate-200">{{ $attendance->date?->toDateString() ?? '-' }}</td>
                        <td class="px-3 py-3 dark:text-slate-200">{{ $attendance->time ?? '-' }}</td>
                        <td class="px-3 py-3 font-semibold dark:text-slate-100">{{ $attendance->client?->full_name ?? '-' }}</td>
                        <td class="px-3 py-3 dark:text-slate-200">{{ $attendanceMethodLabel }}</td>
                    </tr>
                @empty
                    <tr>
                        <td id="recent-attendances-empty" colspan="4" class="px-3 py-6 text-center text-sm text-slate-500 dark:text-slate-300">Sin ingresos recientes.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </x-ui.card>

    <div id="attendance-history-modal" class="ui-modal-backdrop hidden" role="dialog" aria-modal="true" aria-labelledby="attendanceHistoryTitle">
        <div class="ui-modal-panel max-w-6xl">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <h3 id="attendanceHistoryTitle" class="ui-heading text-lg">Asistencias de los últimos 2 meses</h3>
                    <p class="mt-1 text-sm ui-muted">
                        Desde {{ $attendanceHistoryStart }}.
                        <strong>{{ (int) ($attendanceHistoryTotal ?? 0) }}</strong> registros en ventana operativa.
                    </p>
                </div>
                <button type="button" class="ui-button ui-button-ghost px-2 py-1" data-close-attendance-history aria-label="Cerrar">X</button>
            </div>

            @if (!empty($attendanceHistoryTruncated))
                <p class="mt-3 ui-alert ui-alert-warning text-xs">
                    Se muestran los primeros 2000 registros para mantener velocidad en pantalla.
                </p>
            @endif

            <div class="mt-4 overflow-x-auto rounded-xl border border-slate-200 dark:border-slate-700">
                <table class="ui-table min-w-[980px]">
                    <thead>
                    <tr class="border-b border-slate-200 bg-slate-50 text-left text-xs uppercase tracking-wider text-slate-500 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300">
                        <th class="px-3 py-3">Fecha</th>
                        <th class="px-3 py-3">Hora</th>
                        <th class="px-3 py-3">Cliente</th>
                        <th class="px-3 py-3">Método</th>
                        <th class="px-3 py-3">Usuario</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse (($attendanceHistory ?? collect()) as $row)
                        @php
                            $historyMethod = $row->credential?->type ?? 'document';
                            $historyMethodLabel = match ($historyMethod) {
                                'rfid' => 'RFID',
                                'qr' => 'QR',
                                'document' => 'Documento',
                                default => strtoupper((string) $historyMethod),
                            };
                        @endphp
                        <tr class="border-b border-slate-100 text-sm odd:bg-white even:bg-slate-50 dark:border-slate-800 dark:odd:bg-slate-900 dark:even:bg-slate-950/50">
                            <td class="px-3 py-3 dark:text-slate-200">{{ $row->date?->toDateString() ?? '-' }}</td>
                            <td class="px-3 py-3 dark:text-slate-200">{{ $row->time ?? '-' }}</td>
                            <td class="px-3 py-3 font-semibold dark:text-slate-100">{{ $row->client?->full_name ?? '-' }}</td>
                            <td class="px-3 py-3 dark:text-slate-200">{{ $historyMethodLabel }}</td>
                            <td class="px-3 py-3 dark:text-slate-200">{{ $row->createdBy?->name ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-3 py-6 text-center text-sm text-slate-500 dark:text-slate-300">No hay asistencias en la ventana de 2 meses.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4 flex justify-end">
                <button type="button" class="ui-button ui-button-ghost" data-close-attendance-history>Cerrar</button>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    (function () {
        const AUTO_RESET_MS = 15000;
        const SCAN_SPEED_MS = 35;
        const SCAN_MIN_LENGTH = 6;
        const SCAN_IDLE_SUBMIT_MS = 90;
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        const syncGymName = String(@json((string) $syncGymName));
        const syncGymId = Number(@json((int) $syncGymId));
        const gymAvatarUrls = @json($gymAvatarUrls);
        const mobileQrEndpoint = @json(route('reception.mobile-qr'));
        const mobileQrStatusEndpoint = @json(route('reception.mobile-qr.status'));
        const syncPollUrl = @json(route('reception.sync.latest'));
        const checkInEndpoint = @json(route('reception.check-in'));
        const checkOutEndpoint = @json(route('reception.check-out'));
        const latestSyncEventId = String(@json((string) ($latestSyncEventId ?? '')));
        const latestSyncEventPublishedAt = Number(@json((int) ($latestSyncEventPublishedAt ?? 0))) || 0;
        const SYNC_POLL_INTERVAL_MS = 1200;
        const syncChannelName = 'reception.checkin.' + String(syncGymId || 0);
        const syncStorageKey = syncChannelName + '.event';
        const syncSourceId = 'main-' + Math.random().toString(36).slice(2);
        const syncChannel = typeof BroadcastChannel !== 'undefined'
            ? new BroadcastChannel(syncChannelName)
            : null;

        const input = document.getElementById('value');
        const sendBtn = document.getElementById('send-btn');
        const checkoutBtn = document.getElementById('checkout-btn');
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
        const daysPill = document.getElementById('result-days-pill');
        const monthPill = document.getElementById('result-month-pill');
        const checkinDate = document.getElementById('result-checkin-date');
        const checkinTime = document.getElementById('result-checkin-time');
        const monthVisitsValue = document.getElementById('result-month-visits');
        const motivation = document.getElementById('result-motivation');
        const avatarInitials = document.getElementById('result-avatar-initials');
        const recentAttendancesBody = document.getElementById('recent-attendances-body');
        const attendanceHistoryModal = document.getElementById('attendance-history-modal');
        const mobileQrSvg = document.getElementById('mobile-qr-svg');
        const mobileQrPayload = document.getElementById('mobile-qr-payload');
        const mobileQrCountdown = document.getElementById('mobile-qr-countdown');
        const mobileQrRotateSeconds = document.getElementById('mobile-qr-rotate-seconds');
        const mobileQrAutoRefresh = document.getElementById('mobile-qr-auto-refresh');
        const mobileQrRefresh = document.getElementById('mobile-qr-refresh');
        const receptionI18n = {
            ready_to_scan: @json(__('messages.reception.ready_to_scan')),
            invalid_server_response: @json(__('messages.reception.invalid_server_response')),
            qr_update_failed: @json(__('messages.reception.qr_update_failed')),
            network_retry: @json(__('messages.reception.network_retry')),
            processing: @json(__('messages.reception.processing')),
            processing_checkout: @json(__('messages.reception.processing_checkout')),
            register_checkout: @json(__('messages.reception.register_checkout')),
        };
        const methodLabels = {
            rfid: 'RFID',
            qr: 'QR',
            document: 'Documento',
        };

        let resetTimer = null;
        let scanTimer = null;
        let submitting = false;
        let lastKeyTimestamp = 0;
        let burstCount = 0;
        let scannerLikely = false;
        let audioContext = null;
        let lastRenderedAttendanceId = readTopAttendanceId();
        let lastHandledSyncEventId = latestSyncEventId !== '' ? latestSyncEventId : null;
        let lastHandledSyncPublishedAt = latestSyncEventPublishedAt > 0 ? latestSyncEventPublishedAt : 0;
        let syncPollTimer = null;
        let syncPollInFlight = false;
        let mobileQrExpiresAtTs = 0;
        let mobileQrCountdownTimer = null;
        let mobileQrRefreshTimer = null;
        let mobileQrLoading = false;
        let mobileQrActiveToken = '';
        let mobileQrEffectiveRotateSeconds = 20;
        let mobileQrPendingRotateApply = false;
        let mobileQrLastConsumedAtMs = 0;
        let mobileQrStatusPollTimer = null;
        let mobileQrStatusLoading = false;

        document.querySelectorAll('[data-open-attendance-history]').forEach(function (button) {
            button.addEventListener('click', function () {
                attendanceHistoryModal?.classList.remove('hidden');
            });
        });
        document.querySelectorAll('[data-close-attendance-history]').forEach(function (button) {
            button.addEventListener('click', function () {
                attendanceHistoryModal?.classList.add('hidden');
            });
        });
        attendanceHistoryModal?.addEventListener('click', function (event) {
            if (event.target === attendanceHistoryModal) {
                attendanceHistoryModal.classList.add('hidden');
            }
        });

        function clearMobileQrTimers(includeStatusPoll = true) {
            if (mobileQrCountdownTimer) {
                clearInterval(mobileQrCountdownTimer);
                mobileQrCountdownTimer = null;
            }

            if (mobileQrRefreshTimer) {
                clearTimeout(mobileQrRefreshTimer);
                mobileQrRefreshTimer = null;
            }

            if (includeStatusPoll && mobileQrStatusPollTimer) {
                clearInterval(mobileQrStatusPollTimer);
                mobileQrStatusPollTimer = null;
            }
        }

        function clampRotateSeconds(rawValue) {
            const parsed = Number(rawValue);
            if (!Number.isFinite(parsed)) return 20;

            return Math.max(10, Math.min(2592000, Math.floor(parsed)));
        }

        function parseRotateSeconds(rawValue) {
            const parsed = Number(rawValue);
            if (!Number.isFinite(parsed)) return null;

            const seconds = Math.floor(parsed);
            if (seconds < 10 || seconds > 2592000) {
                return null;
            }

            return seconds;
        }

        function setMobileQrCountdownText(text, tone) {
            if (!mobileQrCountdown) return;

            mobileQrCountdown.textContent = text;
            if (tone === 'error') {
                mobileQrCountdown.className = 'inline-flex rounded-full border border-rose-300 bg-rose-50 px-3 py-1 text-xs font-bold uppercase tracking-wide text-rose-800 dark:border-rose-700/60 dark:bg-rose-900/20 dark:text-rose-100';
                return;
            }

            if (tone === 'warn') {
                mobileQrCountdown.className = 'inline-flex rounded-full border border-amber-300 bg-amber-50 px-3 py-1 text-xs font-bold uppercase tracking-wide text-amber-800 dark:border-amber-700/60 dark:bg-amber-900/20 dark:text-amber-100';
                return;
            }

            mobileQrCountdown.className = 'inline-flex rounded-full border border-cyan-300 bg-cyan-50 px-3 py-1 text-xs font-bold uppercase tracking-wide text-cyan-800 dark:border-cyan-700/60 dark:bg-cyan-900/20 dark:text-cyan-100';
        }

        function startMobileQrCountdown() {
            if (!mobileQrExpiresAtTs) {
                setMobileQrCountdownText('Esperando QR...', 'warn');
                return;
            }

            if (mobileQrCountdownTimer) {
                clearInterval(mobileQrCountdownTimer);
                mobileQrCountdownTimer = null;
            }

            const renderTick = function () {
                const nowTs = Math.floor(Date.now() / 1000);
                const remaining = Math.max(0, mobileQrExpiresAtTs - nowTs);

                if (remaining <= 3) {
                    setMobileQrCountdownText('QR vence en ' + String(remaining) + 's', 'warn');
                } else {
                    setMobileQrCountdownText('QR vence en ' + String(remaining) + 's', 'info');
                }
            };

            renderTick();
            mobileQrCountdownTimer = setInterval(renderTick, 1000);
        }

        function scheduleMobileQrRefresh() {
            if (mobileQrRefreshTimer) {
                clearTimeout(mobileQrRefreshTimer);
                mobileQrRefreshTimer = null;
            }

            if (!mobileQrAutoRefresh?.checked || !mobileQrExpiresAtTs) {
                return;
            }

            const msUntilRefresh = Math.max(1000, (mobileQrExpiresAtTs * 1000) - Date.now() - 500);
            mobileQrRefreshTimer = setTimeout(function () {
                generateMobileQr(false);
            }, msUntilRefresh);
        }

        async function checkMobileQrConsumedStatus() {
            if (!mobileQrStatusEndpoint || !mobileQrActiveToken || mobileQrLoading || mobileQrStatusLoading) {
                return;
            }

            mobileQrStatusLoading = true;
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
                    && consumedToken === mobileQrActiveToken
                    && consumedAtMs > mobileQrLastConsumedAtMs;

                if (hasValidConsumedAt && consumedAtMs > mobileQrLastConsumedAtMs) {
                    mobileQrLastConsumedAtMs = consumedAtMs;
                }

                if (shouldRefresh) {
                    generateMobileQr(false);
                }
            } catch (error) {
                // Silent: reintenta en el siguiente poll.
            } finally {
                mobileQrStatusLoading = false;
            }
        }

        function startMobileQrStatusPolling() {
            if (mobileQrStatusPollTimer || !mobileQrStatusEndpoint) {
                return;
            }

            mobileQrStatusPollTimer = setInterval(function () {
                checkMobileQrConsumedStatus();
            }, 1000);
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

        async function parseJsonResponse(response) {
            const raw = await response.text();
            if (raw.trim() === '') {
                return { payload: null };
            }

            try {
                return { payload: JSON.parse(raw) };
            } catch (error) {
                return { payload: null };
            }
        }

        function normalizeReceptionQrError(payload, statusCode) {
            const reason = payload && payload.reason ? String(payload.reason) : '';
            const message = payload && payload.message ? String(payload.message) : '';

            if (reason === 'too_many_attempts' || statusCode === 429) {
                return 'Sincronizando QR...';
            }

            if (statusCode === 401 || statusCode === 419) {
                return 'Sesión vencida. Recarga la página.';
            }

            if (statusCode >= 500) {
                return 'Servicio QR temporalmente no disponible.';
            }

            if (message !== '') {
                return message.length > 90 ? receptionI18n.qr_update_failed : message;
            }

            return receptionI18n.qr_update_failed;
        }

        function applyMobileQrState(rawPayload) {
            if (!mobileQrSvg || !mobileQrPayload || !mobileQrRotateSeconds || !rawPayload || typeof rawPayload !== 'object') {
                return false;
            }

            const qrSvgMarkup = resolveQrSvgMarkup(rawPayload.qr_svg);
            mobileQrSvg.innerHTML = qrSvgMarkup !== '' ? qrSvgMarkup : '<p class="text-xs text-slate-500 dark:text-slate-300">Sin QR disponible.</p>';
            mobileQrPayload.textContent = rawPayload.qr_payload ? String(rawPayload.qr_payload) : '-';

            const serverRotateSeconds = clampRotateSeconds(rawPayload.rotate_seconds || mobileQrEffectiveRotateSeconds);
            mobileQrEffectiveRotateSeconds = serverRotateSeconds;
            if (document.activeElement !== mobileQrRotateSeconds) {
                mobileQrRotateSeconds.value = String(serverRotateSeconds);
            }

            mobileQrActiveToken = rawPayload.token ? String(rawPayload.token).trim().toUpperCase() : '';
            mobileQrExpiresAtTs = Number(rawPayload.expires_at_ts || 0);
            startMobileQrCountdown();
            scheduleMobileQrRefresh();
            return true;
        }

        async function generateMobileQr(showLoading, force = false) {
            if (!mobileQrSvg || !mobileQrPayload || !mobileQrRotateSeconds || !mobileQrEndpoint) {
                return;
            }

            if (mobileQrLoading) return;
            mobileQrLoading = true;

            const typedRotateSeconds = parseRotateSeconds(mobileQrRotateSeconds.value);
            const rotateSeconds = typedRotateSeconds !== null
                ? typedRotateSeconds
                : mobileQrEffectiveRotateSeconds;
            mobileQrEffectiveRotateSeconds = rotateSeconds;
            if (document.activeElement !== mobileQrRotateSeconds) {
                mobileQrRotateSeconds.value = String(rotateSeconds);
            }

            if (showLoading) {
                mobileQrSvg.innerHTML = '<p class="text-xs text-slate-500 dark:text-slate-300">Generando QR...</p>';
            }

            try {
                const url = new URL(mobileQrEndpoint, window.location.origin);
                url.searchParams.set('rotate_seconds', String(rotateSeconds));
                if (force) {
                    url.searchParams.set('force', '1');
                }

                const response = await fetch(url.toString(), {
                    method: 'GET',
                    credentials: 'same-origin',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                });

                const parsed = await parseJsonResponse(response);
                const payload = parsed.payload;
                if (!response.ok || !payload || !payload.ok) {
                    const reason = payload && payload.reason ? String(payload.reason) : '';
                    const message = normalizeReceptionQrError(payload, response.status);
                    if (reason === 'too_many_attempts' || response.status === 429) {
                        setMobileQrCountdownText(message, 'info');
                        return;
                    }
                    throw new Error(message);
                }

                applyMobileQrState(payload);
                emitMobileQrStateSync(payload);
            } catch (error) {
                const message = error instanceof Error ? error.message : receptionI18n.qr_update_failed;
                if (!mobileQrActiveToken) {
                    mobileQrSvg.innerHTML = '<p class="text-xs text-slate-500 dark:text-slate-300">Sin QR disponible.</p>';
                    mobileQrPayload.textContent = '-';
                    mobileQrExpiresAtTs = 0;
                    clearMobileQrTimers(false);
                }
                setMobileQrCountdownText(message, 'warn');
            } finally {
                mobileQrLoading = false;
                if (mobileQrPendingRotateApply) {
                    mobileQrPendingRotateApply = false;
                    setTimeout(function () {
                        submitRotateSeconds();
                    }, 120);
                }
            }
        }

        mobileQrRefresh?.addEventListener('click', function () {
            generateMobileQr(true, true);
        });

        function submitRotateSeconds() {
            const seconds = parseRotateSeconds(mobileQrRotateSeconds?.value);
            if (seconds === null) {
                setMobileQrCountdownText('Ingresa segundos válidos (10 a 2592000).', 'warn');
                return;
            }

            mobileQrRotateSeconds.value = String(seconds);
            mobileQrEffectiveRotateSeconds = seconds;
            if (mobileQrLoading) {
                mobileQrPendingRotateApply = true;
                return;
            }

            mobileQrPendingRotateApply = false;
            generateMobileQr(true, true);
        }

        mobileQrRotateSeconds?.addEventListener('keydown', function (event) {
            if (event.key !== 'Enter') return;
            event.preventDefault();
            submitRotateSeconds();
        });

        mobileQrRotateSeconds?.addEventListener('change', function () {
            submitRotateSeconds();
        });

        mobileQrAutoRefresh?.addEventListener('change', function () {
            if (mobileQrAutoRefresh.checked) {
                scheduleMobileQrRefresh();
                return;
            }

            if (mobileQrRefreshTimer) {
                clearTimeout(mobileQrRefreshTimer);
                mobileQrRefreshTimer = null;
            }
        });

        function basePanelClasses() {
            return 'rounded-2xl border p-5 shadow-sm';
        }

        function readTopAttendanceId() {
            if (!recentAttendancesBody) return null;

            const row = recentAttendancesBody.querySelector('tr[data-role="recent-attendance-row"]');
            if (!row) return null;

            const parsed = Number(row.getAttribute('data-attendance-id'));

            return Number.isFinite(parsed) ? parsed : null;
        }

        function payloadAttendanceId(payload) {
            const parsed = Number(payload && payload.attendance ? payload.attendance.id : null);

            return Number.isFinite(parsed) ? parsed : null;
        }

        function payloadAttempt(payload) {
            if (!payload) return null;
            if (payload.attendance && payload.attendance.date && payload.attendance.time) {
                return {
                    date: String(payload.attendance.date),
                    time: String(payload.attendance.time),
                };
            }
            if (payload.attempt && payload.attempt.date && payload.attempt.time) {
                return {
                    date: String(payload.attempt.date),
                    time: String(payload.attempt.time),
                };
            }

            return null;
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
                reason: payload && payload.reason ? String(payload.reason) : null,
                message: payload && payload.message ? payload.message : receptionI18n.invalid_server_response,
                method: payload && Object.prototype.hasOwnProperty.call(payload, 'method') ? payload.method : null,
                client: payload && Object.prototype.hasOwnProperty.call(payload, 'client') ? payload.client : null,
                attendance: payload && Object.prototype.hasOwnProperty.call(payload, 'attendance') ? payload.attendance : null,
                attempt: payload && Object.prototype.hasOwnProperty.call(payload, 'attempt') ? payload.attempt : null,
                event_type: payload && payload.event_type ? String(payload.event_type) : 'checkin',
            };
        }

        function payloadReason(payload) {
            return payload && payload.reason ? String(payload.reason) : '';
        }

        function clientInitials(fullName) {
            const normalized = String(fullName || '').trim();
            if (normalized === '') return '--';

            const initials = normalized
                .split(/\s+/)
                .filter(Boolean)
                .slice(0, 2)
                .map(function (part) {
                    return part.charAt(0).toUpperCase();
                })
                .join('');

            return initials || '--';
        }

        function normalizeGender(rawGender) {
            const value = String(rawGender || '').trim().toLowerCase();

            return value === 'male' || value === 'female' ? value : 'neutral';
        }

        function avatarLabelByGender(gender) {
            if (gender === 'male') return 'HOMBRE';
            if (gender === 'female') return 'MUJER';

            return 'NEUTRAL';
        }

        function customAvatarByGender(gender) {
            const normalizedGender = normalizeGender(gender);
            if (!gymAvatarUrls || typeof gymAvatarUrls !== 'object') return '';
            const value = gymAvatarUrls[normalizedGender];
            const normalizedValue = typeof value === 'string' ? value.trim() : '';
            if (normalizedValue !== '') {
                return normalizedValue;
            }

            const neutralValue = gymAvatarUrls.neutral;

            return typeof neutralValue === 'string' ? neutralValue.trim() : '';
        }

        function generatedAvatarDataUrl(gender, fullName) {
            const normalizedGender = normalizeGender(gender);
            const initials = clientInitials(fullName);
            const gymLabel = String(syncGymName || 'GYM').toUpperCase();
            const genderLabel = avatarLabelByGender(normalizedGender);

            let gradientFrom = '#0f172a';
            let gradientTo = '#1d4ed8';
            let accent = '#22d3ee';

            if (normalizedGender === 'female') {
                gradientFrom = '#3b0a45';
                gradientTo = '#9d174d';
                accent = '#f472b6';
            } else if (normalizedGender === 'neutral') {
                gradientFrom = '#1f2937';
                gradientTo = '#0f766e';
                accent = '#34d399';
            }

            const escapedGym = gymLabel.replace(/&/g, '&amp;').replace(/</g, '&lt;');
            const escapedGender = genderLabel.replace(/&/g, '&amp;').replace(/</g, '&lt;');
            const escapedInitials = initials.replace(/&/g, '&amp;').replace(/</g, '&lt;');

            const svg = `<svg xmlns="http://www.w3.org/2000/svg" width="640" height="760" viewBox="0 0 640 760">
                <defs>
                    <linearGradient id="bg" x1="0" y1="0" x2="1" y2="1">
                        <stop offset="0%" stop-color="${gradientFrom}"/>
                        <stop offset="100%" stop-color="${gradientTo}"/>
                    </linearGradient>
                </defs>
                <rect width="640" height="760" rx="36" fill="url(#bg)"/>
                <circle cx="320" cy="250" r="128" fill="rgba(255,255,255,0.08)"/>
                <rect x="180" y="360" width="280" height="260" rx="42" fill="rgba(255,255,255,0.08)"/>
                <text x="320" y="285" font-family="Arial, sans-serif" font-size="116" font-weight="900" text-anchor="middle" fill="${accent}">${escapedInitials}</text>
                <text x="320" y="668" font-family="Arial, sans-serif" font-size="36" font-weight="700" text-anchor="middle" fill="rgba(255,255,255,0.95)">${escapedGym}</text>
                <text x="320" y="712" font-family="Arial, sans-serif" font-size="28" font-weight="700" text-anchor="middle" fill="${accent}">${escapedGender}</text>
            </svg>`;

            return 'data:image/svg+xml;charset=UTF-8,' + encodeURIComponent(svg);
        }

        function showClientVisual(client) {
            const fullName = client && client.full_name ? client.full_name : '';
            avatarInitials.textContent = clientInitials(fullName);

            const directPhoto = client && client.photo_url ? String(client.photo_url) : '';
            const avatarPhoto = customAvatarByGender(client ? client.gender : 'neutral')
                || generatedAvatarDataUrl(client ? client.gender : 'neutral', fullName);
            const candidates = Array.from(new Set([directPhoto, avatarPhoto].filter(function (value) {
                return typeof value === 'string' && value.trim() !== '';
            })));
            let index = 0;

            const loadNextCandidate = function () {
                if (index >= candidates.length) {
                    photo.classList.add('hidden');
                    photoPlaceholder.classList.remove('hidden');
                    return;
                }

                photo.src = candidates[index];
                index += 1;
            };

            photo.onerror = function () {
                loadNextCandidate();
            };
            photo.onload = function () {
                photo.classList.remove('hidden');
                photoPlaceholder.classList.add('hidden');
            };

            loadNextCandidate();
        }

        function formatDateDisplay(dateValue) {
            if (!dateValue) return '-';
            const parsed = new Date(String(dateValue) + 'T00:00:00');
            if (Number.isNaN(parsed.getTime())) return String(dateValue);

            return parsed.toLocaleDateString('es-EC', {
                day: '2-digit',
                month: 'short',
                year: 'numeric',
            });
        }

        function daysRemainingInfo(membershipDate) {
            if (!membershipDate) {
                return { label: 'Días restantes: -', tone: 'neutral', days: null };
            }

            const endDate = new Date(String(membershipDate) + 'T00:00:00');
            if (Number.isNaN(endDate.getTime())) {
                return { label: 'Días restantes: -', tone: 'neutral', days: null };
            }

            const now = new Date();
            const today = new Date(now.getFullYear(), now.getMonth(), now.getDate());
            const diffMs = endDate.getTime() - today.getTime();
            const days = Math.floor(diffMs / 86400000);

            if (days < 0) {
                return {
                    label: 'Membresía vencida',
                    tone: 'danger',
                    days: days,
                };
            }

            if (days < 5) {
                return {
                    label: 'Días restantes: ' + String(days) + ' días',
                    tone: 'danger',
                    days: days,
                };
            }

            if (days <= 10) {
                return {
                    label: 'Días restantes: ' + String(days) + ' días',
                    tone: 'warn',
                    days: days,
                };
            }

            return {
                label: 'Días restantes: ' + String(days) + ' días',
                tone: 'ok',
                days: days,
            };
        }

        function monthVisitsValueOf(payload) {
            const reason = payloadReason(payload);
            const sourceValue = reason === 'membership_inactive'
                ? (payload && payload.client ? payload.client.total_visits : NaN)
                : (payload && payload.client ? payload.client.month_visits : NaN);
            const value = Number(sourceValue);

            return Number.isFinite(value) ? Math.max(0, Math.floor(value)) : null;
        }

        function motivationText(payload, daysInfo, monthVisits) {
            const reason = payloadReason(payload);
            const eventType = payload && payload.event_type ? String(payload.event_type) : 'checkin';

            if (reason === 'checkout_success' || (payload.ok && eventType === 'checkout')) {
                return 'Salida confirmada. Cupo actualizado en vivo.';
            }

            if (reason === 'not_inside') {
                return 'Este cliente no tenia ingreso activo para registrar salida.';
            }

            if (reason === 'duplicate_attendance') {
                return 'Bienvenido de vuelta, ya te registraste hoy. Sigue nomas.';
            }

            if (reason === 'membership_inactive') {
                return 'Membresía no vigente. Renueva para habilitar ingreso.';
            }

            if (!payload.ok) {
                return 'Reintenta con otro método o verifica estado del cliente.';
            }

            if (monthVisits !== null && monthVisits >= 20) {
                return 'Ritmo top: constancia excelente este mes.';
            }

            if (monthVisits !== null && monthVisits >= 12) {
                return 'Muy buen progreso mensual. Sigue asi.';
            }

            if (daysInfo.days !== null && daysInfo.days >= 0 && daysInfo.days <= 5) {
                return 'Renovación cercana: oportunidad perfecta para venta.';
            }

            return 'Buen trabajo. Check-in registrado y flujo estable.';
        }

        function checkinDisplay(payload) {
            const reason = payloadReason(payload);
            if (reason === 'membership_inactive') {
                const lastTime = payload && payload.client && payload.client.last_attendance_time
                    ? String(payload.client.last_attendance_time)
                    : '-';

                return {
                    date: '-',
                    time: lastTime,
                };
            }

            const attempt = payloadAttempt(payload);
            return {
                date: attempt ? formatDateDisplay(attempt.date) : '-',
                time: attempt ? String(attempt.time) : '-',
            };
        }

        function setPillTone(element, tone) {
            if (!element) return;

            if (tone === 'ok') {
                element.className = 'inline-flex rounded-full bg-emerald-100 px-3 py-1 text-xs font-bold uppercase tracking-wide text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-200';
                return;
            }

            if (tone === 'warn') {
                element.className = 'inline-flex rounded-full bg-amber-100 px-3 py-1 text-xs font-bold uppercase tracking-wide text-amber-800 dark:bg-amber-900/30 dark:text-amber-200';
                return;
            }

            if (tone === 'danger') {
                element.className = 'inline-flex rounded-full bg-rose-100 px-3 py-1 text-xs font-bold uppercase tracking-wide text-rose-800 dark:bg-rose-900/30 dark:text-rose-200';
                return;
            }

            element.className = 'inline-flex rounded-full bg-slate-100 px-3 py-1 text-xs font-bold uppercase tracking-wide text-slate-700 dark:bg-slate-700 dark:text-slate-100';
        }

        function buildAttendanceRowCell(text, className) {
            const td = document.createElement('td');
            td.className = className;
            td.textContent = text;

            return td;
        }

        function removeRecentAttendancesEmptyState() {
            const emptyCell = document.getElementById('recent-attendances-empty');
            if (!emptyCell) return;

            const emptyRow = emptyCell.closest('tr');
            if (emptyRow) {
                emptyRow.remove();
            }
        }

        function buildRecentAttendanceRow(id, dateValue, timeValue, clientName, methodText) {
            const row = document.createElement('tr');
            row.setAttribute('data-role', 'recent-attendance-row');
            if (id !== null) {
                row.setAttribute('data-attendance-id', String(id));
            }
            row.className = 'border-b border-slate-100 text-sm odd:bg-white even:bg-slate-50 dark:border-slate-800 dark:odd:bg-slate-900 dark:even:bg-slate-950/50';
            row.appendChild(buildAttendanceRowCell(dateValue, 'px-3 py-3 dark:text-slate-200'));
            row.appendChild(buildAttendanceRowCell(timeValue, 'px-3 py-3 dark:text-slate-200'));
            row.appendChild(buildAttendanceRowCell(clientName, 'px-3 py-3 font-semibold dark:text-slate-100'));
            row.appendChild(buildAttendanceRowCell(methodText, 'px-3 py-3 dark:text-slate-200'));

            return row;
        }

        function prependRecentAttendance(payload) {
            if (!payload.ok || !payload.attendance || !recentAttendancesBody) return;

            const attendanceId = payloadAttendanceId(payload);
            const methodText = payload.method ? (methodLabels[payload.method] || String(payload.method).toUpperCase()) : '-';
            const clientName = payload.client && payload.client.full_name ? payload.client.full_name : '-';
            const dateValue = payload.attendance.date ? String(payload.attendance.date) : '-';
            const timeValue = payload.attendance.time ? String(payload.attendance.time) : '-';

            if (attendanceId !== null) {
                const existing = recentAttendancesBody.querySelector(`tr[data-attendance-id="${attendanceId}"]`);
                if (existing) {
                    existing.remove();
                }
            }

            removeRecentAttendancesEmptyState();
            recentAttendancesBody.prepend(
                buildRecentAttendanceRow(attendanceId, dateValue, timeValue, clientName, methodText)
            );

            const rows = recentAttendancesBody.querySelectorAll('tr[data-role="recent-attendance-row"]');
            if (rows.length > 10) {
                for (let i = 10; i < rows.length; i += 1) {
                    rows[i].remove();
                }
            }
        }

        function emitSync(payload) {
            const syncType = payload && payload.event_type === 'checkout' ? 'checkout' : 'checkin';
            const eventPayload = {
                id: syncSourceId + '-' + Date.now().toString(36) + '-' + Math.random().toString(36).slice(2),
                type: syncType,
                source: syncSourceId,
                payload: payload,
                timestamp: Date.now(),
            };

            if (syncChannel) {
                syncChannel.postMessage(eventPayload);
            }

            try {
                localStorage.setItem(syncStorageKey, JSON.stringify(eventPayload));
            } catch (error) {
                // Ignore storage quota errors.
            }
        }

        function relaySyncEvent(eventPayload) {
            if (!eventPayload || typeof eventPayload !== 'object') {
                return;
            }

            if (syncChannel) {
                syncChannel.postMessage(eventPayload);
            }

            try {
                localStorage.setItem(syncStorageKey, JSON.stringify(eventPayload));
            } catch (error) {
                // Ignore storage quota errors.
            }
        }

        function emitMobileQrRefreshSync() {
            const eventPayload = {
                id: syncSourceId + '-qr-' + Date.now().toString(36) + '-' + Math.random().toString(36).slice(2),
                type: 'mobile_qr_refresh',
                source: syncSourceId,
                timestamp: Date.now(),
            };

            if (syncChannel) {
                syncChannel.postMessage(eventPayload);
            }

            try {
                localStorage.setItem(syncStorageKey, JSON.stringify(eventPayload));
            } catch (error) {
                // Ignore storage quota errors.
            }
        }

        function emitMobileQrStateSync(rawPayload) {
            if (!rawPayload || typeof rawPayload !== 'object') {
                return;
            }

            const rotateSeconds = clampRotateSeconds(rawPayload.rotate_seconds || mobileQrRotateSeconds?.value || 20);
            const eventPayload = {
                id: syncSourceId + '-qrstate-' + Date.now().toString(36) + '-' + Math.random().toString(36).slice(2),
                type: 'mobile_qr_state',
                source: syncSourceId,
                payload: {
                    qr_svg: rawPayload.qr_svg || '',
                    qr_payload: rawPayload.qr_payload || '',
                    token: rawPayload.token || '',
                    expires_at_ts: Number(rawPayload.expires_at_ts || 0),
                    rotate_seconds: rotateSeconds,
                },
                timestamp: Date.now(),
            };

            if (syncChannel) {
                syncChannel.postMessage(eventPayload);
            }

            try {
                localStorage.setItem(syncStorageKey, JSON.stringify(eventPayload));
            } catch (error) {
                // Ignore storage quota errors.
            }
        }

        function applySyncedPayload(rawPayload) {
            const payload = safePayload(rawPayload);
            if (!payload.message) return;

            const attendanceId = payloadAttendanceId(payload);
            if (payload.ok && attendanceId !== null && attendanceId === lastRenderedAttendanceId) return;

            render(payload);
            prependRecentAttendance(payload);
            scheduleReset();
        }

        function handleSyncEvent(eventData) {
            if (!eventData || !eventData.type) return;
            if (eventData.source === syncSourceId) return;
            if (eventData.id && eventData.id === lastHandledSyncEventId) return;
            const eventPublishedAt = Number(eventData.published_at_ms || eventData.timestamp || 0);
            if (eventPublishedAt > 0 && eventPublishedAt <= lastHandledSyncPublishedAt) return;
            if (eventData.id) {
                lastHandledSyncEventId = eventData.id;
            }
            if (eventPublishedAt > 0) {
                lastHandledSyncPublishedAt = eventPublishedAt;
            }

            if (eventData.type === 'mobile_qr_refresh') {
                generateMobileQr(true);
                return;
            }

            if (eventData.type === 'mobile_qr_state') {
                applyMobileQrState(eventData.payload);
                return;
            }

            if (eventData.type !== 'checkin' && eventData.type !== 'checkout') return;
            applySyncedPayload(eventData.payload);
        }

        async function pollLatestSyncEvent() {
            if (syncPollInFlight) return;

            syncPollInFlight = true;

            try {
                const query = lastHandledSyncPublishedAt > 0
                    ? '?after=' + encodeURIComponent(String(lastHandledSyncPublishedAt))
                    : '';
                const response = await fetch(syncPollUrl + query, {
                    method: 'GET',
                    headers: { 'Accept': 'application/json' },
                    credentials: 'same-origin',
                    cache: 'no-store',
                });

                if (!response.ok) {
                    return;
                }

                const data = await response.json();
                if (data && data.event) {
                    handleSyncEvent(data.event);
                    relaySyncEvent(data.event);
                }
            } catch (error) {
                // Ignore intermittent network errors while polling.
            } finally {
                syncPollInFlight = false;
            }
        }

        function startSyncPolling() {
            pollLatestSyncEvent();
            syncPollTimer = window.setInterval(pollLatestSyncEvent, SYNC_POLL_INTERVAL_MS);
        }

        function getAudioContext() {
            if (audioContext) return audioContext;
            const Ctx = window.AudioContext || window.webkitAudioContext;
            if (!Ctx) return null;
            audioContext = new Ctx();
            return audioContext;
        }

        function playPulse(context, settings) {
            const oscillator = context.createOscillator();
            const gain = context.createGain();
            oscillator.connect(gain);
            gain.connect(context.destination);

            oscillator.type = settings.wave;
            oscillator.frequency.value = settings.freq;

            // Envelope corto para que suene fuerte sin distorsionar demasiado.
            const attack = 0.01;
            const release = 0.06;
            const start = settings.start;
            const end = start + settings.duration;
            gain.gain.setValueAtTime(0.0001, start);
            gain.gain.exponentialRampToValueAtTime(settings.gain, start + attack);
            gain.gain.exponentialRampToValueAtTime(0.0001, Math.max(end, start + attack + release));

            oscillator.start(start);
            oscillator.stop(end + release);
        }

        function playTone(type) {
            if (!soundEnabled.checked) return;

            try {
                const context = getAudioContext();
                if (!context) return;
                if (context.state === 'suspended') {
                    context.resume();
                }

                const now = context.currentTime + 0.001;

                if (type === 'ok') {
                    playPulse(context, {
                        freq: 900,
                        gain: 0.18,
                        duration: 0.16,
                        start: now,
                        wave: 'triangle',
                    });
                    return;
                }

                // Error: doble tono más notorio.
                playPulse(context, {
                    freq: 260,
                    gain: 0.23,
                    duration: 0.17,
                    start: now,
                    wave: 'square',
                });
                playPulse(context, {
                    freq: 190,
                    gain: 0.23,
                    duration: 0.2,
                    start: now + 0.2,
                    wave: 'square',
                });
            } catch (error) {
                // Ignore audio failures silently.
            }
        }

        function primeAudio() {
            try {
                const context = getAudioContext();
                if (context && context.state === 'suspended') {
                    context.resume();
                }
            } catch (error) {
                // Ignore audio unlock errors.
            }
        }

        function isEditableElement(element) {
            if (!(element instanceof HTMLElement)) {
                return false;
            }

            if (element.isContentEditable) {
                return true;
            }

            const tag = element.tagName;
            if (tag === 'INPUT' || tag === 'TEXTAREA' || tag === 'SELECT') {
                return true;
            }

            return Boolean(element.closest('input, textarea, select, [contenteditable=""], [contenteditable="true"]'));
        }

        function canAutoFocusScanner() {
            const active = document.activeElement;
            if (!active || active === document.body) {
                return true;
            }

            return active === input;
        }

        function focusInput(force = false) {
            if (!force && !canAutoFocusScanner()) {
                return;
            }

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
            const attendanceId = payloadAttendanceId(payload);
            const reason = payloadReason(payload);
            const eventType = payload && payload.event_type ? String(payload.event_type) : 'checkin';
            const isCheckoutEvent = eventType === 'checkout';
            if (attendanceId !== null) {
                lastRenderedAttendanceId = attendanceId;
            }

            if (payload.ok && isCheckoutEvent) {
                panel.className = basePanelClasses() + ' border-cyan-300 bg-cyan-50 dark:border-cyan-700 dark:bg-cyan-900/25';
                method.className = 'mb-2 inline-flex rounded-full bg-cyan-200 px-3 py-1 text-xs font-bold uppercase tracking-widest text-cyan-800 dark:bg-cyan-800/40 dark:text-cyan-200';
                message.className = 'pr-1 text-[clamp(1.5rem,4.2vw,2.5rem)] font-black break-words [overflow-wrap:anywhere] text-cyan-700 dark:text-cyan-200';
                statusChip.className = 'mt-4 inline-flex rounded-full bg-cyan-100 px-3 py-1 text-xs font-bold uppercase tracking-wide text-cyan-800';
                statusChip.textContent = 'Salida registrada';
                playTone('ok');
            } else if (payload.ok) {
                panel.className = basePanelClasses() + ' border-emerald-300 bg-emerald-50 dark:border-emerald-700 dark:bg-emerald-900/30';
                method.className = 'mb-2 inline-flex rounded-full bg-emerald-200 px-3 py-1 text-xs font-bold uppercase tracking-widest text-emerald-800 dark:bg-emerald-800/40 dark:text-emerald-200';
                message.className = 'pr-1 text-[clamp(1.5rem,4.2vw,2.5rem)] font-black break-words [overflow-wrap:anywhere] text-emerald-700 dark:text-emerald-200';
                statusChip.className = 'mt-4 inline-flex rounded-full bg-emerald-100 px-3 py-1 text-xs font-bold uppercase tracking-wide text-emerald-800';
                statusChip.textContent = 'Acceso permitido';
                playTone('ok');
            } else if (reason === 'duplicate_attendance') {
                panel.className = basePanelClasses() + ' border-amber-300 bg-amber-50 dark:border-amber-700 dark:bg-amber-900/25';
                method.className = 'mb-2 inline-flex rounded-full bg-amber-200 px-3 py-1 text-xs font-bold uppercase tracking-widest text-amber-800 dark:bg-amber-800/40 dark:text-amber-200';
                message.className = 'pr-1 text-[clamp(1.5rem,4.2vw,2.5rem)] font-black break-words [overflow-wrap:anywhere] text-amber-700 dark:text-amber-200';
                statusChip.className = 'mt-4 inline-flex rounded-full bg-amber-100 px-3 py-1 text-xs font-bold uppercase tracking-wide text-amber-800';
                statusChip.textContent = 'Ya registrado hoy';
                playTone('ok');
            } else if (reason === 'not_inside') {
                panel.className = basePanelClasses() + ' border-amber-300 bg-amber-50 dark:border-amber-700 dark:bg-amber-900/25';
                method.className = 'mb-2 inline-flex rounded-full bg-amber-200 px-3 py-1 text-xs font-bold uppercase tracking-widest text-amber-800 dark:bg-amber-800/40 dark:text-amber-200';
                message.className = 'pr-1 text-[clamp(1.5rem,4.2vw,2.5rem)] font-black break-words [overflow-wrap:anywhere] text-amber-700 dark:text-amber-200';
                statusChip.className = 'mt-4 inline-flex rounded-full bg-amber-100 px-3 py-1 text-xs font-bold uppercase tracking-wide text-amber-800';
                statusChip.textContent = 'Sin ingreso activo';
                playTone('error');
            } else {
                panel.className = basePanelClasses() + ' border-rose-300 bg-rose-50 dark:border-rose-700 dark:bg-rose-900/30';
                method.className = 'mb-2 inline-flex rounded-full bg-rose-200 px-3 py-1 text-xs font-bold uppercase tracking-widest text-rose-800 dark:bg-rose-800/40 dark:text-rose-200';
                message.className = 'pr-1 text-[clamp(1.5rem,4.2vw,2.5rem)] font-black break-words [overflow-wrap:anywhere] text-rose-700 dark:text-rose-200';
                statusChip.className = 'mt-4 inline-flex rounded-full bg-rose-100 px-3 py-1 text-xs font-bold uppercase tracking-wide text-rose-800';
                statusChip.textContent = 'Acceso denegado';
                playTone('error');
            }

            message.textContent = payload.message;
            const methodText = payload.method ? (methodLabels[payload.method] || String(payload.method).toUpperCase()) : '-';
            method.textContent = 'Método: ' + methodText;

            const membershipDate = payload.client && payload.client.membership_ends_at
                ? String(payload.client.membership_ends_at)
                : null;
            const daysInfo = daysRemainingInfo(membershipDate);
            setPillTone(daysPill, daysInfo.tone);
            daysPill.textContent = daysInfo.label;

            const monthVisits = monthVisitsValueOf(payload);
            monthPill.textContent = 'Visitas mes: ' + (monthVisits !== null ? String(monthVisits) : '-');
            setPillTone(monthPill, payload.ok ? 'ok' : (reason === 'duplicate_attendance' ? 'warn' : 'neutral'));

            const checkin = checkinDisplay(payload);
            checkinDate.textContent = checkin.date;
            checkinTime.textContent = checkin.time;
            monthVisitsValue.textContent = monthVisits !== null ? String(monthVisits) : '-';
            motivation.textContent = motivationText(payload, daysInfo, monthVisits);

            if (payload.client) {
                name.textContent = payload.client.full_name || '-';
                membership.textContent = formatDateDisplay(payload.client.membership_ends_at || null);
                showClientVisual(payload.client);
            } else {
                name.textContent = '-';
                membership.textContent = '-';
                avatarInitials.textContent = '--';
                photo.classList.add('hidden');
                photoPlaceholder.classList.remove('hidden');
            }
        }

        function renderIdle() {
            panel.className = basePanelClasses() + ' border-slate-300 bg-white dark:border-slate-700 dark:bg-slate-900';
            method.className = 'mb-2 inline-flex rounded-full bg-slate-200 px-3 py-1 text-xs font-bold uppercase tracking-widest text-slate-700 dark:bg-slate-700 dark:text-slate-100';
            method.textContent = 'Método: -';
            message.className = 'pr-1 text-[clamp(1.5rem,4.2vw,2.5rem)] font-black break-words [overflow-wrap:anywhere] text-slate-800 dark:text-slate-100';
            message.textContent = 'Esperando lectura...';
            statusChip.className = 'mt-4 inline-flex rounded-full bg-cyan-100 px-3 py-1 text-xs font-bold uppercase tracking-wide text-cyan-800';
            statusChip.textContent = receptionI18n.ready_to_scan;
            name.textContent = '-';
            membership.textContent = '-';
            checkinDate.textContent = '-';
            checkinTime.textContent = '-';
            monthVisitsValue.textContent = '-';
            motivation.textContent = 'Listo para recibir al cliente.';
            daysPill.textContent = 'Días restantes: -';
            monthPill.textContent = 'Visitas mes: -';
            avatarInitials.textContent = '--';
            setPillTone(daysPill, 'neutral');
            setPillTone(monthPill, 'neutral');
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

        async function submitCheckIn(forcedValue = null, action = 'checkin') {
            if (submitting) return;

            const isCheckoutAction = action === 'checkout';
            const endpoint = isCheckoutAction ? checkOutEndpoint : checkInEndpoint;

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
            if (checkoutBtn) {
                checkoutBtn.setAttribute('disabled', 'disabled');
            }
            sendBtn.textContent = receptionI18n.processing;
            if (checkoutBtn) {
                checkoutBtn.textContent = isCheckoutAction ? receptionI18n.processing_checkout : receptionI18n.processing;
            }
            statusChip.className = 'mt-4 inline-flex rounded-full bg-amber-100 px-3 py-1 text-xs font-bold uppercase tracking-wide text-amber-800';
            statusChip.textContent = isCheckoutAction ? receptionI18n.processing_checkout : receptionI18n.processing;

            let payload;
            try {
                const response = await fetch(endpoint, {
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
                    message: receptionI18n.network_retry,
                    method: null,
                    client: null,
                    attendance: null,
                    event_type: isCheckoutAction ? 'checkout' : 'checkin',
                };
            }

            if (!payload.event_type) {
                payload.event_type = isCheckoutAction ? 'checkout' : 'checkin';
            }

            render(payload);
            prependRecentAttendance(payload);
            emitSync(payload);
            input.value = '';
            scheduleReset();
            focusInput();

            sendBtn.removeAttribute('disabled');
            if (checkoutBtn) {
                checkoutBtn.removeAttribute('disabled');
            }
            sendBtn.textContent = 'Enviar';
            if (checkoutBtn) {
                checkoutBtn.textContent = receptionI18n.register_checkout;
            }
            submitting = false;
            resetScanDetection();
        }

        sendBtn.addEventListener('click', function () {
            submitCheckIn(null, 'checkin');
        });
        checkoutBtn?.addEventListener('click', function () {
            submitCheckIn(null, 'checkout');
        });
        window.addEventListener('pointerdown', primeAudio, { once: true });
        window.addEventListener('keydown', primeAudio, { once: true });
        input.addEventListener('keydown', function (event) {
            if (event.key === 'Enter' || event.key === 'Tab') {
                event.preventDefault();
                submitCheckIn(null, 'checkin');
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
            const isTypingElsewhere = target instanceof HTMLElement
                && isEditableElement(target)
                && !isInputFocused;

            if (isTypingElsewhere) {
                return;
            }

            if (event.key === 'Enter' || event.key === 'Tab') {
                if (!isInputFocused && input.value.trim() !== '') {
                    event.preventDefault();
                    submitCheckIn(null, 'checkin');
                }
                return;
            }

            if (event.key.length !== 1) return;

            registerBurstKey(Date.now());

            if (!isInputFocused) {
                event.preventDefault();
                focusInput(true);
                input.value = normalizeInput(input.value + event.key);
            }

            scheduleScannerSubmit();
        }, true);

        input.addEventListener('blur', function () {
            setTimeout(function () {
                focusInput();
            }, 120);
        });

        setInterval(function () {
            if (!document.hidden && document.activeElement !== input && canAutoFocusScanner()) {
                focusInput();
            }
        }, 1000);

        if (syncChannel) {
            syncChannel.onmessage = function (event) {
                handleSyncEvent(event.data);
            };
        }

        window.addEventListener('storage', function (event) {
            if (event.key !== syncStorageKey || !event.newValue) return;
            try {
                handleSyncEvent(JSON.parse(event.newValue));
            } catch (error) {
                // Ignore malformed storage payloads.
            }
        });

        window.addEventListener('beforeunload', function () {
            clearMobileQrTimers();
            if (syncChannel) {
                syncChannel.close();
            }
            if (syncPollTimer) {
                clearInterval(syncPollTimer);
            }
        });

        document.addEventListener('visibilitychange', function () {
            if (!document.hidden) {
                pollLatestSyncEvent();
            }
        });

        startMobileQrStatusPolling();
        startSyncPolling();
        generateMobileQr(true);
        const initialResult = @json($latestResult ?? null);
        if (initialResult) {
            const payload = safePayload(initialResult);
            render(payload);
            prependRecentAttendance(payload);
            scheduleReset();
        } else {
            renderIdle();
        }
        focusInput();
    })();
</script>
@endpush

