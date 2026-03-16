@extends('layouts.panel')

@section('title', 'Recepción')
@section('page-title', 'Modo recepción PRO')

@push('styles')
<style>
    .reception-command-grid {
        display: grid;
        gap: 1rem;
        grid-template-columns: minmax(0, 1fr);
    }

    .reception-actions-grid {
        display: grid;
        gap: 0.55rem;
        grid-template-columns: repeat(1, minmax(0, 1fr));
    }

    .reception-action-card {
        border: 1px solid color-mix(in srgb, var(--border) 82%, transparent);
        border-radius: 0.85rem;
        background: color-mix(in srgb, var(--card) 88%, transparent);
        padding: 0.65rem;
    }

    .reception-action-title {
        margin: 0 0 0.5rem;
        font-size: 0.66rem;
        font-weight: 900;
        letter-spacing: 0.12em;
        text-transform: uppercase;
        color: color-mix(in srgb, var(--muted) 86%, #fff);
    }

    .reception-shortcuts {
        display: flex;
        flex-wrap: wrap;
        gap: 0.45rem;
        align-items: center;
    }

    .reception-shortcut-chip {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        border-radius: 9999px;
        border: 1px solid color-mix(in srgb, var(--border) 82%, transparent);
        background: color-mix(in srgb, var(--card) 88%, transparent);
        padding: 0.26rem 0.58rem;
        font-size: 0.66rem;
        font-weight: 800;
        color: color-mix(in srgb, var(--text) 90%, #fff);
        line-height: 1;
    }

    .reception-shortcut-chip kbd {
        border-radius: 0.38rem;
        border: 1px solid color-mix(in srgb, var(--border) 82%, transparent);
        background: color-mix(in srgb, var(--card-2) 88%, transparent);
        padding: 0.14rem 0.32rem;
        font-size: 0.63rem;
        font-weight: 900;
        letter-spacing: 0.03em;
        line-height: 1;
    }

    .reception-status-subtext {
        margin-top: 0.45rem;
        font-size: 0.76rem;
        font-weight: 600;
        color: color-mix(in srgb, var(--muted) 90%, #fff);
    }

    .reception-collapsible-group {
        display: grid;
        gap: 1rem;
    }

    .reception-toggle-card {
        transition: grid-column 170ms ease, border-color 170ms ease, box-shadow 170ms ease;
    }

    .reception-toggle-card.reception-panel-collapsed {
        min-height: 9.25rem;
        display: flex;
        flex-direction: column;
        justify-content: flex-start;
    }

    .reception-toggle-card.reception-panel-collapsed header {
        margin-bottom: 0.65rem;
    }

    .reception-toggle-card.reception-panel-collapsed .ui-heading {
        font-size: clamp(1.12rem, 1.2vw, 1.38rem);
    }

    .reception-panel-toggle-bar {
        margin-bottom: 0.65rem;
    }

    .reception-collapsible-content[aria-hidden="true"] {
        pointer-events: none;
    }

    .reception-history-tools {
        display: grid;
        gap: 0.55rem;
        grid-template-columns: repeat(1, minmax(0, 1fr));
    }

    .reception-history-tools .ui-input,
    .reception-history-tools .ui-select {
        min-height: 2.45rem;
    }

    .reception-history-summary {
        font-size: 0.74rem;
        font-weight: 700;
        color: color-mix(in srgb, var(--muted) 90%, #fff);
    }

    #recent-attendances-wrap table thead th {
        position: sticky;
        top: 0;
        z-index: 3;
    }

    #recent-attendances-wrap table tbody tr {
        transition: opacity 120ms ease;
    }

    #recent-attendances-empty-filter {
        margin-top: 0.55rem;
        border-radius: 0.7rem;
        border: 1px dashed color-mix(in srgb, var(--border) 78%, transparent);
        padding: 0.52rem 0.65rem;
        font-size: 0.76rem;
        font-weight: 700;
        color: color-mix(in srgb, var(--muted) 90%, #fff);
    }

    @media (min-width: 768px) {
        .reception-actions-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .reception-history-tools {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }
    }

    @media (min-width: 1280px) {
        .reception-command-grid {
            grid-template-columns: minmax(320px, 1.2fr) minmax(340px, 1fr);
        }
    }

    @media (min-width: 1024px) {
        .reception-collapsible-group {
            grid-template-columns: repeat(3, minmax(0, 1fr));
            align-items: start;
        }

        .reception-collapsible-group > * {
            grid-column: 1 / -1;
        }

        .reception-toggle-card.reception-panel-collapsed {
            grid-column: auto;
            min-height: 10.5rem;
        }

        .reception-toggle-card.reception-panel-expanded {
            grid-column: 1 / -1;
        }
    }
</style>
@endpush

@section('content')
    <x-ui.card title="Ingreso unificado" subtitle="Escanea RFID/QR o escribe documento. Soporta autoenvío por lector tipo teclado.">
        <div class="reception-command-grid">
            <div class="space-y-3">
                <label class="space-y-2 text-sm font-semibold ui-muted">
                    <span>Valor de entrada</span>
                    <input id="value" name="value" type="text" inputmode="text" autocomplete="off" autofocus
                           placeholder="RFID, QR o documento"
                           class="ui-input h-16 rounded-xl border-2 px-4 text-2xl font-black tracking-wide md:h-20 md:text-3xl">
                </label>

                <p id="status-chip" class="inline-flex rounded-full bg-cyan-100 px-3 py-1 text-xs font-bold uppercase tracking-wide text-cyan-800 dark:bg-cyan-900/40 dark:text-cyan-200">
                    {{ __('messages.reception.ready_to_scan') }}
                </p>
                <p id="status-detail" class="reception-status-subtext">
                    Atajos: Enter registra ingreso, F3 registra salida y F2 limpia el campo.
                </p>
            </div>

            <div class="space-y-3">
                <div class="reception-action-card">
                    <p class="reception-action-title">Registro principal</p>
                    <div class="reception-actions-grid">
                        <x-ui.button id="send-btn" type="button" variant="primary" size="lg" class="h-14 w-full">Enviar</x-ui.button>
                        <x-ui.button id="checkout-btn" type="button" variant="ghost" size="lg" class="h-14 w-full">
                            Registrar salida
                        </x-ui.button>
                    </div>
                </div>

                <div class="reception-action-card">
                    <p class="reception-action-title">Soporte y escaneo</p>
                    <div class="reception-actions-grid">
                        <x-ui.button :href="route('reception.display')" target="_blank" rel="noopener" variant="secondary" size="lg" class="h-14 w-full">
                            Pantalla 2 + QR
                        </x-ui.button>
                        <x-ui.button id="reception-open-mobile-scanner" type="button" variant="secondary" size="lg" class="h-14 w-full" aria-haspopup="dialog" aria-controls="reception-mobile-scanner-modal" aria-expanded="false">
                            Escanear QR desde mi celular
                        </x-ui.button>
                    </div>
                </div>

                <div class="reception-shortcuts">
                    <label class="inline-flex items-center gap-2 rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-xs font-bold uppercase tracking-wide text-slate-700 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200">
                        <input id="auto-submit-enabled" type="checkbox" class="h-4 w-4" checked>
                        Autoescaneo
                    </label>
                    <label class="inline-flex items-center gap-2 rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-xs font-bold uppercase tracking-wide text-slate-700 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200">
                        <input id="sound-enabled" type="checkbox" class="h-4 w-4" checked>
                        Sonido
                    </label>
                    <span class="reception-shortcut-chip"><kbd>F2</kbd>Limpiar</span>
                    <span class="reception-shortcut-chip"><kbd>F3</kbd>Salida</span>
                    <span class="reception-shortcut-chip"><kbd>Ctrl+K</kbd>Enfocar</span>
                </div>
            </div>
        </div>

    </x-ui.card>

    <div class="reception-collapsible-group">
    @if (!empty($canManageClientAccounts))
        <x-ui.card title="QR dinámico móvil" subtitle="Genera QR temporal para check-in desde la PWA del cliente.">
            <div class="reception-panel-toggle-bar flex justify-end">
                <button id="toggle-mobile-qr-panel"
                        type="button"
                        class="ui-button ui-button-ghost px-3 py-1.5 text-xs"
                        aria-controls="mobile-qr-panel-content"
                        aria-expanded="true">
                    Ocultar
                </button>
            </div>
            <div id="mobile-qr-panel-content" class="reception-collapsible-content space-y-3">
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
        <div class="ui-alert ui-alert-warning lg:col-span-3">
            <strong>QR dinamico no habilitado en este plan.</strong>
            Puedes seguir operando con RFID, documento y lector QR local desde esta pantalla.
            El QR movil temporal se habilita en planes Premium y Sucursales.
        </div>
    @endif

    <x-ui.card id="result-panel" class="reception-toggle-card reception-panel-expanded relative overflow-hidden border-slate-300 bg-white dark:border-slate-700 dark:bg-slate-900" title="Resultado">
        <div class="reception-panel-toggle-bar relative z-10 flex justify-end">
            <button id="toggle-result-panel"
                    type="button"
                    class="ui-button ui-button-ghost px-3 py-1.5 text-xs"
                    aria-controls="result-panel-content"
                    aria-expanded="true">
                Ocultar
            </button>
        </div>
        <div id="result-panel-content" class="reception-collapsible-content">
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
        </div>
    </x-ui.card>

    <x-ui.card title="Últimos 10 ingresos">
        <div class="reception-panel-toggle-bar flex justify-end">
            <button id="toggle-recent-panel"
                    type="button"
                    class="ui-button ui-button-ghost px-3 py-1.5 text-xs"
                    aria-controls="recent-panel-content"
                    aria-expanded="true">
                Ocultar
            </button>
        </div>
        <div id="recent-panel-content" class="reception-collapsible-content">
        <div class="mb-3 flex flex-wrap items-center justify-between gap-2">
            <p class="text-xs text-slate-500 dark:text-slate-300">Historial detallado disponible para los últimos 2 meses.</p>
            <button id="reception-open-history" type="button" class="ui-button ui-button-ghost px-3 py-1.5 text-xs" data-open-attendance-history>
                Ver asistencias (2 meses)
            </button>
        </div>

        <div class="reception-history-tools mb-3">
            <label class="space-y-1 text-xs font-bold uppercase tracking-wide ui-muted">
                <span>Rango</span>
                <select id="recent-attendance-range" class="ui-input">
                    <option value="all">Todo</option>
                    <option value="today">Solo hoy</option>
                    <option value="week">Últimos 7 días</option>
                </select>
            </label>
            <label class="space-y-1 text-xs font-bold uppercase tracking-wide ui-muted">
                <span>Método</span>
                <select id="recent-attendance-method" class="ui-input">
                    <option value="all">Todos</option>
                    <option value="document">Documento</option>
                    <option value="rfid">RFID</option>
                    <option value="qr">QR</option>
                </select>
            </label>
            <label class="space-y-1 text-xs font-bold uppercase tracking-wide ui-muted">
                <span>Buscar cliente</span>
                <input id="recent-attendance-search" type="text" inputmode="search" autocomplete="off" placeholder="Nombre del cliente" class="ui-input">
            </label>
        </div>

        <div class="mb-3 flex flex-wrap items-center justify-between gap-2">
            <p id="recent-attendance-summary" class="reception-history-summary">Mostrando 0 de 0 registros.</p>
            <button id="recent-attendance-reset" type="button" class="ui-button ui-button-ghost px-3 py-1.5 text-xs">
                Limpiar filtros
            </button>
        </div>

        <div id="recent-attendances-wrap" class="overflow-x-auto rounded-xl border border-slate-200 dark:border-slate-700">
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
                    <tr data-role="recent-attendance-row"
                        data-attendance-id="{{ (int) $attendance->id }}"
                        data-attendance-date="{{ $attendance->date?->toDateString() ?? '' }}"
                        data-attendance-method="{{ strtolower((string) $attendanceMethod) }}"
                        data-attendance-client="{{ mb_strtolower((string) ($attendance->client?->full_name ?? '-')) }}"
                        class="border-b border-slate-100 text-sm odd:bg-white even:bg-slate-50 dark:border-slate-800 dark:odd:bg-slate-900 dark:even:bg-slate-950/50">
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
        <p id="recent-attendances-empty-filter" class="hidden">No hay registros que coincidan con los filtros actuales.</p>
        </div>
    </x-ui.card>
    </div>

    <div id="reception-mobile-scanner-modal" class="ui-modal-backdrop hidden" role="dialog" aria-modal="true" aria-labelledby="receptionMobileScannerTitle">
        <div class="ui-modal-panel max-w-6xl">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <h3 id="receptionMobileScannerTitle" class="ui-heading text-lg">Escanear QR desde mi celular</h3>
                    <p class="mt-1 text-sm ui-muted">
                        Cámara en modal para ingreso y salida sin abrir otra pantalla.
                    </p>
                </div>
                <button type="button" class="ui-button ui-button-ghost px-3 py-1.5 text-sm" data-close-mobile-scanner aria-label="Cerrar escáner">
                    Cerrar
                </button>
            </div>

            <div class="mt-4 grid gap-4 xl:grid-cols-[minmax(0,1fr)_340px]">
                <section class="space-y-3">
                    <div class="rounded-2xl border border-slate-300 bg-slate-950/90 p-2 dark:border-slate-700">
                        <video id="reception-mobile-scanner-video" class="h-[300px] w-full rounded-xl bg-black object-cover sm:h-[420px]" autoplay muted playsinline></video>
                    </div>
                    <p id="reception-mobile-scanner-status" class="rounded-lg border border-cyan-300 bg-cyan-50 px-3 py-2 text-sm font-semibold text-cyan-800 dark:border-cyan-700/60 dark:bg-cyan-900/20 dark:text-cyan-100">
                        Presiona "Iniciar cámara" para escanear.
                    </p>
                    <p id="reception-mobile-scanner-feedback" class="rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm font-semibold text-slate-800 dark:border-slate-700 dark:bg-slate-900/70 dark:text-slate-100">
                        Sin lecturas aún.
                    </p>
                </section>

                <aside class="space-y-3">
                    <div class="grid gap-2 sm:grid-cols-2 xl:grid-cols-1">
                        <x-ui.button id="reception-mobile-scanner-start" type="button" variant="primary" class="w-full">Iniciar cámara</x-ui.button>
                        <x-ui.button id="reception-mobile-scanner-stop" type="button" variant="ghost" class="w-full" disabled>Detener</x-ui.button>
                    </div>

                    <div class="rounded-xl border border-slate-200 bg-slate-50 p-3 dark:border-slate-700 dark:bg-slate-900/70">
                        <p class="text-xs font-black uppercase tracking-[0.14em] text-slate-600 dark:text-slate-300">Modo de registro</p>
                        <div class="mt-2 grid grid-cols-2 gap-2">
                            <label id="reception-mobile-mode-checkin-card" class="inline-flex cursor-pointer items-center justify-center rounded-lg border border-emerald-300 bg-emerald-50 px-2 py-2 text-xs font-black text-emerald-800 dark:border-emerald-700/60 dark:bg-emerald-900/20 dark:text-emerald-100">
                                <input id="reception-mobile-mode-checkin" type="radio" name="reception-mobile-mode" value="checkin" class="sr-only" checked>
                                Ingreso
                            </label>
                            <label id="reception-mobile-mode-checkout-card" class="inline-flex cursor-pointer items-center justify-center rounded-lg border border-slate-300 bg-white px-2 py-2 text-xs font-black text-slate-700 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100">
                                <input id="reception-mobile-mode-checkout" type="radio" name="reception-mobile-mode" value="checkout" class="sr-only">
                                Salida
                            </label>
                        </div>
                    </div>

                    <div class="rounded-xl border border-slate-200 bg-slate-50 p-3 dark:border-slate-700 dark:bg-slate-900/70">
                        <label class="space-y-2 text-sm font-semibold ui-muted">
                            <span>Código manual (fallback)</span>
                            <input id="reception-mobile-scanner-input" type="text" inputmode="text" autocomplete="off" placeholder="RFID, QR o documento" class="ui-input">
                        </label>
                        <x-ui.button id="reception-mobile-scanner-submit" type="button" variant="secondary" class="mt-2 w-full">
                            Procesar código
                        </x-ui.button>
                    </div>

                    <p class="text-xs text-slate-500 dark:text-slate-300">
                        Si el cliente vuelve a escanear y ya tenía ingreso activo, se registrará salida automáticamente.
                    </p>
                </aside>
            </div>
        </div>
    </div>

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
        const SUBMIT_DEDUP_WINDOW_MS = 1200;
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        const syncGymName = String(@json((string) $syncGymName));
        const syncGymId = Number(@json((int) $syncGymId));
        const gymAvatarUrls = @json($gymAvatarUrls);
        const mobileQrEndpoint = @json(route('reception.mobile-qr'));
        const mobileQrStatusEndpoint = @json(route('reception.mobile-qr.status'));
        const syncPollUrl = @json(route('reception.sync.latest'));
        const checkInEndpoint = @json(route('reception.check-in'));
        const checkOutEndpoint = @json(route('reception.check-out'));
        const autoOpenMobileScannerModal = Boolean(@json(request()->boolean('open_mobile_scanner')));
        const latestSyncEventId = String(@json((string) ($latestSyncEventId ?? '')));
        const latestSyncEventPublishedAt = Number(@json((int) ($latestSyncEventPublishedAt ?? 0))) || 0;
        const SYNC_POLL_INTERVAL_MS = 1200;
        const MOBILE_SCANNER_SCAN_DEBOUNCE_MS = 1600;
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
        const statusDetail = document.getElementById('status-detail');
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
        const recentAttendanceRange = document.getElementById('recent-attendance-range');
        const recentAttendanceMethod = document.getElementById('recent-attendance-method');
        const recentAttendanceSearch = document.getElementById('recent-attendance-search');
        const recentAttendanceSummary = document.getElementById('recent-attendance-summary');
        const recentAttendanceReset = document.getElementById('recent-attendance-reset');
        const recentAttendanceFilterEmpty = document.getElementById('recent-attendances-empty-filter');
        const toggleMobileQrPanelBtn = document.getElementById('toggle-mobile-qr-panel');
        const mobileQrPanelContent = document.getElementById('mobile-qr-panel-content');
        const toggleResultPanelBtn = document.getElementById('toggle-result-panel');
        const resultPanelContent = document.getElementById('result-panel-content');
        const toggleRecentPanelBtn = document.getElementById('toggle-recent-panel');
        const recentPanelContent = document.getElementById('recent-panel-content');
        const openMobileScannerBtn = document.getElementById('reception-open-mobile-scanner');
        const mobileScannerModal = document.getElementById('reception-mobile-scanner-modal');
        const mobileScannerVideo = document.getElementById('reception-mobile-scanner-video');
        const mobileScannerStatus = document.getElementById('reception-mobile-scanner-status');
        const mobileScannerFeedback = document.getElementById('reception-mobile-scanner-feedback');
        const mobileScannerStartBtn = document.getElementById('reception-mobile-scanner-start');
        const mobileScannerStopBtn = document.getElementById('reception-mobile-scanner-stop');
        const mobileScannerSubmitBtn = document.getElementById('reception-mobile-scanner-submit');
        const mobileScannerInput = document.getElementById('reception-mobile-scanner-input');
        const mobileScannerModeCheckIn = document.getElementById('reception-mobile-mode-checkin');
        const mobileScannerModeCheckOut = document.getElementById('reception-mobile-mode-checkout');
        const mobileScannerModeCheckInCard = document.getElementById('reception-mobile-mode-checkin-card');
        const mobileScannerModeCheckOutCard = document.getElementById('reception-mobile-mode-checkout-card');
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
        let lastSubmitSignature = '';
        let lastSubmitAt = 0;
        let lastKeyTimestamp = 0;
        let burstCount = 0;
        let scannerLikely = false;
        let audioContext = null;
        let lastRenderedAttendanceId = readTopAttendanceId();
        let lastHandledSyncEventId = latestSyncEventId !== '' ? latestSyncEventId : null;
        // Ignora eventos previos a esta carga para no pintar la última asistencia al reabrir recepción.
        let lastHandledSyncPublishedAt = Math.max(
            latestSyncEventPublishedAt > 0 ? latestSyncEventPublishedAt : 0,
            Date.now()
        );
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
        let mobileScannerIsOpen = false;
        let mobileScannerStream = null;
        let mobileScannerDetector = null;
        let mobileScannerScanTimer = null;
        let mobileScannerFallbackLibraryPromise = null;
        let mobileScannerFallbackReader = null;
        let mobileScannerFallbackControls = null;
        let mobileScannerScanBusy = false;
        let mobileScannerSubmitting = false;
        let mobileScannerLastScannedValue = '';
        let mobileScannerLastScannedAt = 0;

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

        recentAttendanceRange?.addEventListener('change', applyRecentAttendanceFilters);
        recentAttendanceMethod?.addEventListener('change', applyRecentAttendanceFilters);
        recentAttendanceSearch?.addEventListener('input', applyRecentAttendanceFilters);
        recentAttendanceReset?.addEventListener('click', resetRecentAttendanceFilters);

        function setMobileScannerStatus(text, tone = 'info') {
            if (!mobileScannerStatus) return;

            mobileScannerStatus.textContent = String(text || '').trim() || 'Sin estado.';
            if (tone === 'ok') {
                mobileScannerStatus.className = 'rounded-lg border border-emerald-300 bg-emerald-50 px-3 py-2 text-sm font-semibold text-emerald-800 dark:border-emerald-700/60 dark:bg-emerald-900/20 dark:text-emerald-100';
                return;
            }

            if (tone === 'warn') {
                mobileScannerStatus.className = 'rounded-lg border border-amber-300 bg-amber-50 px-3 py-2 text-sm font-semibold text-amber-800 dark:border-amber-700/60 dark:bg-amber-900/20 dark:text-amber-100';
                return;
            }

            if (tone === 'error') {
                mobileScannerStatus.className = 'rounded-lg border border-rose-300 bg-rose-50 px-3 py-2 text-sm font-semibold text-rose-800 dark:border-rose-700/60 dark:bg-rose-900/20 dark:text-rose-100';
                return;
            }

            mobileScannerStatus.className = 'rounded-lg border border-cyan-300 bg-cyan-50 px-3 py-2 text-sm font-semibold text-cyan-800 dark:border-cyan-700/60 dark:bg-cyan-900/20 dark:text-cyan-100';
        }

        function setMobileScannerFeedback(text, tone = 'neutral') {
            if (!mobileScannerFeedback) return;

            mobileScannerFeedback.textContent = String(text || '').trim() || 'Sin lecturas aún.';
            if (tone === 'ok') {
                mobileScannerFeedback.className = 'rounded-lg border border-emerald-300 bg-emerald-50 px-3 py-2 text-sm font-semibold text-emerald-800 dark:border-emerald-700/60 dark:bg-emerald-900/20 dark:text-emerald-100';
                return;
            }

            if (tone === 'warn') {
                mobileScannerFeedback.className = 'rounded-lg border border-amber-300 bg-amber-50 px-3 py-2 text-sm font-semibold text-amber-800 dark:border-amber-700/60 dark:bg-amber-900/20 dark:text-amber-100';
                return;
            }

            if (tone === 'error') {
                mobileScannerFeedback.className = 'rounded-lg border border-rose-300 bg-rose-50 px-3 py-2 text-sm font-semibold text-rose-800 dark:border-rose-700/60 dark:bg-rose-900/20 dark:text-rose-100';
                return;
            }

            mobileScannerFeedback.className = 'rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm font-semibold text-slate-800 dark:border-slate-700 dark:bg-slate-900/70 dark:text-slate-100';
        }

        function setMobileScannerButtonsState(isActive) {
            if (mobileScannerStartBtn) {
                mobileScannerStartBtn.disabled = Boolean(isActive);
            }
            if (mobileScannerStopBtn) {
                mobileScannerStopBtn.disabled = !isActive;
            }
        }

        function mobileScannerMode() {
            return mobileScannerModeCheckOut && mobileScannerModeCheckOut.checked ? 'checkout' : 'checkin';
        }

        function refreshMobileScannerModeCards() {
            const checkInActive = mobileScannerMode() === 'checkin';

            if (mobileScannerModeCheckInCard) {
                mobileScannerModeCheckInCard.className = checkInActive
                    ? 'inline-flex cursor-pointer items-center justify-center rounded-lg border border-emerald-300 bg-emerald-50 px-2 py-2 text-xs font-black text-emerald-800 dark:border-emerald-700/60 dark:bg-emerald-900/20 dark:text-emerald-100'
                    : 'inline-flex cursor-pointer items-center justify-center rounded-lg border border-slate-300 bg-white px-2 py-2 text-xs font-black text-slate-700 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100';
            }

            if (mobileScannerModeCheckOutCard) {
                mobileScannerModeCheckOutCard.className = !checkInActive
                    ? 'inline-flex cursor-pointer items-center justify-center rounded-lg border border-cyan-300 bg-cyan-50 px-2 py-2 text-xs font-black text-cyan-800 dark:border-cyan-700/60 dark:bg-cyan-900/20 dark:text-cyan-100'
                    : 'inline-flex cursor-pointer items-center justify-center rounded-lg border border-slate-300 bg-white px-2 py-2 text-xs font-black text-slate-700 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100';
            }
        }

        function stopMobileScannerFallback() {
            if (mobileScannerFallbackControls && typeof mobileScannerFallbackControls.stop === 'function') {
                try {
                    mobileScannerFallbackControls.stop();
                } catch (_error) {
                    // Ignore stop fallback errors.
                }
            }
            mobileScannerFallbackControls = null;

            if (mobileScannerFallbackReader && typeof mobileScannerFallbackReader.reset === 'function') {
                try {
                    mobileScannerFallbackReader.reset();
                } catch (_error) {
                    // Ignore reset fallback errors.
                }
            }
            mobileScannerFallbackReader = null;
        }

        function stopMobileScannerCamera(silent = false) {
            if (mobileScannerScanTimer) {
                clearInterval(mobileScannerScanTimer);
                mobileScannerScanTimer = null;
            }

            stopMobileScannerFallback();
            mobileScannerDetector = null;
            mobileScannerScanBusy = false;
            mobileScannerLastScannedValue = '';
            mobileScannerLastScannedAt = 0;

            if (mobileScannerStream) {
                mobileScannerStream.getTracks().forEach(function (track) {
                    track.stop();
                });
                mobileScannerStream = null;
            }

            const activeStream = mobileScannerVideo?.srcObject;
            if (activeStream && typeof activeStream.getTracks === 'function') {
                activeStream.getTracks().forEach(function (track) {
                    track.stop();
                });
            }

            if (mobileScannerVideo) {
                mobileScannerVideo.srcObject = null;
            }
            setMobileScannerButtonsState(false);
            if (!silent) {
                setMobileScannerStatus('Cámara detenida.', 'info');
            }
        }

        function resolveMobileScannerCameraError(error) {
            const errorName = String(error && error.name ? error.name : '').trim();
            if (errorName === 'NotAllowedError' || errorName === 'PermissionDeniedError') {
                return 'No se concedió permiso de cámara. Acepta el popup del navegador.';
            }
            if (errorName === 'NotFoundError' || errorName === 'DevicesNotFoundError') {
                return 'No se encontró una cámara disponible en este dispositivo.';
            }
            if (errorName === 'NotReadableError' || errorName === 'TrackStartError') {
                return 'La cámara está en uso por otra app o pestaña.';
            }
            if (errorName === 'SecurityError') {
                return 'La política del sitio bloquea cámara. Recarga y vuelve a intentar.';
            }

            return 'No se pudo abrir la cámara.';
        }

        async function requestMobileScannerStream() {
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

        async function supportsNativeMobileQrDetection() {
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

        async function loadMobileScannerFallbackLibrary() {
            if (window.ZXingBrowser && window.ZXingBrowser.BrowserQRCodeReader) {
                return window.ZXingBrowser;
            }

            if (mobileScannerFallbackLibraryPromise) {
                return mobileScannerFallbackLibraryPromise;
            }

            const scriptSources = [
                'https://unpkg.com/@zxing/browser@0.1.5/umd/zxing-browser.min.js',
                'https://cdn.jsdelivr.net/npm/@zxing/browser@0.1.5/umd/zxing-browser.min.js',
            ];

            mobileScannerFallbackLibraryPromise = new Promise((resolve, reject) => {
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

            return mobileScannerFallbackLibraryPromise;
        }

        function normalizeMobileModalOutcome(payload) {
            const reason = payloadReason(payload);
            const eventType = payload && payload.event_type ? String(payload.event_type) : 'checkin';
            if (payload.ok && eventType === 'checkout') {
                return { headline: 'Salida registrada', tone: 'ok' };
            }

            if (payload.ok) {
                return { headline: 'Cliente registrado', tone: 'ok' };
            }

            if (reason === 'membership_inactive' || reason === 'not_found' || reason === 'client_inactive' || reason === 'credential_inactive') {
                return { headline: 'Cliente no disponible o membresía caducada', tone: 'error' };
            }

            if (reason === 'duplicate_attendance') {
                return { headline: 'Cliente ya estaba registrado', tone: 'warn' };
            }

            if (reason === 'not_inside') {
                return { headline: 'No hay ingreso activo para salida', tone: 'warn' };
            }

            return { headline: 'No se pudo registrar', tone: 'error' };
        }

        async function mobileScannerRequest(action, value) {
            const endpoint = action === 'checkout' ? checkOutEndpoint : checkInEndpoint;

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

                if (!payload || typeof payload !== 'object') {
                    return safePayload({
                        ok: false,
                        reason: 'invalid_payload',
                        message: receptionI18n.invalid_server_response,
                        method: null,
                        client: null,
                        attendance: null,
                        attempt: null,
                        event_type: action === 'checkout' ? 'checkout' : 'checkin',
                    });
                }

                if (!Object.prototype.hasOwnProperty.call(payload, 'event_type')) {
                    payload.event_type = action === 'checkout' ? 'checkout' : 'checkin';
                }

                return safePayload(payload);
            } catch (_error) {
                return safePayload({
                    ok: false,
                    reason: 'network_error',
                    message: 'Error de red. Revisa conexión e intenta de nuevo.',
                    method: null,
                    client: null,
                    attendance: null,
                    attempt: null,
                    event_type: action === 'checkout' ? 'checkout' : 'checkin',
                });
            }
        }

        async function processMobileScannerValue(rawValue, preferredAction = null, allowAutoCheckout = true) {
            if (mobileScannerSubmitting) {
                return;
            }

            const value = normalizeInput(rawValue);
            if (value === '') {
                setMobileScannerStatus('Ingresa o escanea un código válido.', 'warn');
                return;
            }

            let action = preferredAction === 'checkout' ? 'checkout' : (preferredAction === 'checkin' ? 'checkin' : mobileScannerMode());
            mobileScannerSubmitting = true;
            if (mobileScannerSubmitBtn) {
                mobileScannerSubmitBtn.disabled = true;
            }

            setMobileScannerStatus(action === 'checkout' ? 'Procesando salida...' : 'Procesando ingreso...', 'info');
            let payload = await mobileScannerRequest(action, value);
            const duplicateCanAutoCheckout = payload && payload.can_auto_checkout === true;

            if (
                allowAutoCheckout
                && action === 'checkin'
                && payloadReason(payload) === 'duplicate_attendance'
                && duplicateCanAutoCheckout
            ) {
                const checkoutPayload = await mobileScannerRequest('checkout', value);
                if (checkoutPayload.ok || payloadReason(checkoutPayload) !== 'not_inside') {
                    payload = checkoutPayload;
                    action = 'checkout';
                }
            }

            const normalized = normalizeMobileModalOutcome(payload);
            setMobileScannerStatus(normalized.headline, normalized.tone);
            setMobileScannerFeedback(payload.message, normalized.tone);

            render(payload);
            prependRecentAttendance(payload);
            emitSync(payload);
            scheduleReset();
            focusInput();

            if (payload.ok && mobileScannerInput) {
                mobileScannerInput.value = '';
            } else if (mobileScannerInput) {
                mobileScannerInput.value = value;
                mobileScannerInput.focus({ preventScroll: true });
                mobileScannerInput.select();
            }

            if (mobileScannerSubmitBtn) {
                mobileScannerSubmitBtn.disabled = false;
            }
            mobileScannerSubmitting = false;
        }

        async function startMobileScannerNative() {
            mobileScannerDetector = new BarcodeDetector({ formats: ['qr_code'] });
            mobileScannerStream = await requestMobileScannerStream();
            if (mobileScannerVideo) {
                mobileScannerVideo.srcObject = mobileScannerStream;
                await mobileScannerVideo.play();
            }

            mobileScannerScanTimer = window.setInterval(async function () {
                if (!mobileScannerDetector || !mobileScannerVideo || mobileScannerVideo.readyState < 2 || mobileScannerScanBusy || mobileScannerSubmitting) {
                    return;
                }

                try {
                    const codes = await mobileScannerDetector.detect(mobileScannerVideo);
                    if (!codes || !codes.length) {
                        return;
                    }

                    const rawValue = normalizeInput(String(codes[0].rawValue || ''));
                    if (rawValue === '') {
                        return;
                    }

                    const now = Date.now();
                    if (rawValue === mobileScannerLastScannedValue && (now - mobileScannerLastScannedAt) < MOBILE_SCANNER_SCAN_DEBOUNCE_MS) {
                        return;
                    }
                    mobileScannerLastScannedValue = rawValue;
                    mobileScannerLastScannedAt = now;

                    mobileScannerScanBusy = true;
                    try {
                        if (mobileScannerInput) {
                            mobileScannerInput.value = rawValue;
                        }
                        await processMobileScannerValue(rawValue, null, true);
                    } finally {
                        mobileScannerScanBusy = false;
                    }
                } catch (_error) {
                    // Keep scan loop alive.
                }
            }, 220);

            setMobileScannerButtonsState(true);
            setMobileScannerStatus('Escaneando QR (modo nativo)...', 'ok');
        }

        async function startMobileScannerFallback() {
            const zxingBrowser = await loadMobileScannerFallbackLibrary();
            const ReaderCtor = zxingBrowser && (
                (typeof zxingBrowser.BrowserQRCodeReader === 'function' && zxingBrowser.BrowserQRCodeReader)
                || (typeof zxingBrowser.BrowserMultiFormatReader === 'function' && zxingBrowser.BrowserMultiFormatReader)
            );
            if (!ReaderCtor) {
                throw new Error('Fallback QR reader unavailable');
            }

            mobileScannerFallbackReader = new ReaderCtor();
            mobileScannerFallbackControls = await mobileScannerFallbackReader.decodeFromVideoDevice(undefined, mobileScannerVideo, async function (result) {
                if (!result || mobileScannerScanBusy || mobileScannerSubmitting) return;

                const rawValue = String(typeof result.getText === 'function' ? result.getText() : (result.text || '')).trim();
                const normalized = normalizeInput(rawValue);
                if (normalized === '') return;

                const now = Date.now();
                if (normalized === mobileScannerLastScannedValue && (now - mobileScannerLastScannedAt) < MOBILE_SCANNER_SCAN_DEBOUNCE_MS) {
                    return;
                }
                mobileScannerLastScannedValue = normalized;
                mobileScannerLastScannedAt = now;

                mobileScannerScanBusy = true;
                try {
                    if (mobileScannerInput) {
                        mobileScannerInput.value = normalized;
                    }
                    await processMobileScannerValue(normalized, null, true);
                } finally {
                    mobileScannerScanBusy = false;
                }
            });

            setMobileScannerButtonsState(true);
            setMobileScannerStatus('Escaneando QR (modo compatible)...', 'ok');
        }

        async function startMobileScannerCamera() {
            if (!mobileScannerIsOpen) {
                return;
            }

            if (!window.isSecureContext && location.hostname !== 'localhost' && location.hostname !== '127.0.0.1') {
                setMobileScannerStatus('La cámara requiere HTTPS.', 'error');
                return;
            }

            if (!navigator.mediaDevices || typeof navigator.mediaDevices.getUserMedia !== 'function') {
                setMobileScannerStatus('Este navegador no soporta acceso a cámara.', 'error');
                return;
            }

            stopMobileScannerCamera(true);
            setMobileScannerButtonsState(true);
            setMobileScannerStatus('Abriendo cámara...', 'info');

            const canUseNative = await supportsNativeMobileQrDetection();
            if (canUseNative) {
                try {
                    await startMobileScannerNative();
                    return;
                } catch (error) {
                    stopMobileScannerCamera(true);
                    setMobileScannerStatus(resolveMobileScannerCameraError(error) + ' Probando modo compatible...', 'warn');
                }
            }

            try {
                await startMobileScannerFallback();
            } catch (error) {
                stopMobileScannerCamera(true);
                setMobileScannerStatus(resolveMobileScannerCameraError(error), 'error');
            }
        }

        function closeMobileScannerModal(restoreFocus = true) {
            if (!mobileScannerModal || mobileScannerModal.classList.contains('hidden')) {
                return;
            }

            const focused = document.activeElement;
            if (focused instanceof HTMLElement && mobileScannerModal.contains(focused)) {
                focused.blur();
            }

            stopMobileScannerCamera(true);
            mobileScannerModal.classList.add('hidden');
            mobileScannerIsOpen = false;
            if (openMobileScannerBtn) {
                openMobileScannerBtn.setAttribute('aria-expanded', 'false');
                if (restoreFocus) {
                    openMobileScannerBtn.focus({ preventScroll: true });
                }
            }
        }

        function openMobileScannerModal(shouldAutoStart = true) {
            if (!mobileScannerModal) {
                return;
            }

            mobileScannerModal.classList.remove('hidden');
            mobileScannerIsOpen = true;
            if (openMobileScannerBtn) {
                openMobileScannerBtn.setAttribute('aria-expanded', 'true');
            }
            refreshMobileScannerModeCards();
            setMobileScannerStatus('Presiona "Iniciar cámara" para escanear.', 'info');
            if (mobileScannerFeedback) {
                setMobileScannerFeedback('Sin lecturas aún.', 'neutral');
            }

            if (mobileScannerInput) {
                mobileScannerInput.value = '';
            }
            setMobileScannerButtonsState(false);

            if (shouldAutoStart) {
                startMobileScannerCamera();
            }
        }

        openMobileScannerBtn?.addEventListener('click', function () {
            openMobileScannerModal(true);
        });
        document.querySelectorAll('[data-close-mobile-scanner]').forEach(function (button) {
            button.addEventListener('click', function () {
                closeMobileScannerModal(true);
            });
        });
        mobileScannerModal?.addEventListener('click', function (event) {
            if (event.target === mobileScannerModal) {
                closeMobileScannerModal(true);
            }
        });
        mobileScannerStartBtn?.addEventListener('click', function () {
            startMobileScannerCamera();
        });
        mobileScannerStopBtn?.addEventListener('click', function () {
            stopMobileScannerCamera();
        });
        mobileScannerSubmitBtn?.addEventListener('click', function () {
            processMobileScannerValue(mobileScannerInput ? mobileScannerInput.value : '', null, true);
        });
        mobileScannerInput?.addEventListener('keydown', function (event) {
            if (event.key !== 'Enter') {
                return;
            }

            event.preventDefault();
            processMobileScannerValue(mobileScannerInput.value, null, true);
        });
        mobileScannerModeCheckIn?.addEventListener('change', refreshMobileScannerModeCards);
        mobileScannerModeCheckOut?.addEventListener('change', refreshMobileScannerModeCards);
        window.addEventListener('keydown', function (event) {
            if (event.key === 'Escape' && mobileScannerIsOpen) {
                closeMobileScannerModal(true);
            }
        });

        if (autoOpenMobileScannerModal) {
            openMobileScannerModal(false);
        }

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

        function currentResultPanelStateClass() {
            if (!panel) return 'reception-panel-expanded';
            return panel.classList.contains('reception-panel-collapsed')
                ? 'reception-panel-collapsed'
                : 'reception-panel-expanded';
        }

        function basePanelClasses() {
            return 'reception-toggle-card '
                + currentResultPanelStateClass()
                + ' relative overflow-hidden rounded-2xl border p-5 shadow-sm';
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

        function normalizeSearchText(rawValue) {
            const value = String(rawValue || '')
                .trim()
                .toLocaleLowerCase('es-EC');

            if (typeof value.normalize !== 'function') {
                return value;
            }

            return value.normalize('NFD').replace(/[\u0300-\u036f]/g, '');
        }

        function parseAttendanceDate(dateValue) {
            const value = String(dateValue || '').trim();
            const parts = value.match(/^(\d{4})-(\d{2})-(\d{2})$/);
            if (!parts) return null;

            const year = Number(parts[1]);
            const month = Number(parts[2]) - 1;
            const day = Number(parts[3]);
            const parsed = new Date(year, month, day);

            return Number.isNaN(parsed.getTime()) ? null : parsed;
        }

        function isAttendanceInRange(dateValue, range) {
            if (range === 'all') return true;

            const parsedDate = parseAttendanceDate(dateValue);
            if (!parsedDate) return false;

            const now = new Date();
            const today = new Date(now.getFullYear(), now.getMonth(), now.getDate());
            if (range === 'today') {
                return parsedDate.getTime() === today.getTime();
            }

            if (range === 'week') {
                const sevenDaysAgo = new Date(today);
                sevenDaysAgo.setDate(today.getDate() - 6);
                return parsedDate >= sevenDaysAgo && parsedDate <= today;
            }

            return true;
        }

        function applyRecentAttendanceFilters() {
            if (!recentAttendancesBody) return;

            const rows = Array.from(recentAttendancesBody.querySelectorAll('tr[data-role="recent-attendance-row"]'));
            const selectedRange = recentAttendanceRange ? String(recentAttendanceRange.value || 'all') : 'all';
            const selectedMethod = recentAttendanceMethod ? String(recentAttendanceMethod.value || 'all') : 'all';
            const query = normalizeSearchText(recentAttendanceSearch ? recentAttendanceSearch.value : '');
            let visibleRows = 0;

            rows.forEach(function (row) {
                const rowDate = String(row.getAttribute('data-attendance-date') || '').trim();
                const rowMethod = String(row.getAttribute('data-attendance-method') || '').trim().toLowerCase();
                const rowClient = normalizeSearchText(row.getAttribute('data-attendance-client') || '');
                const rangeOk = isAttendanceInRange(rowDate, selectedRange);
                const methodOk = selectedMethod === 'all' || selectedMethod === rowMethod;
                const queryOk = query === ''
                    || rowClient.includes(query)
                    || rowDate.includes(query)
                    || rowMethod.includes(query);
                const shouldShow = rangeOk && methodOk && queryOk;

                row.classList.toggle('hidden', !shouldShow);
                if (shouldShow) {
                    visibleRows += 1;
                }
            });

            if (recentAttendanceSummary) {
                recentAttendanceSummary.textContent = 'Mostrando ' + String(visibleRows) + ' de ' + String(rows.length) + ' registros.';
            }

            if (recentAttendanceFilterEmpty) {
                recentAttendanceFilterEmpty.classList.toggle('hidden', !(rows.length > 0 && visibleRows === 0));
            }
        }

        function resetRecentAttendanceFilters() {
            if (recentAttendanceRange) recentAttendanceRange.value = 'all';
            if (recentAttendanceMethod) recentAttendanceMethod.value = 'all';
            if (recentAttendanceSearch) recentAttendanceSearch.value = '';
            applyRecentAttendanceFilters();
        }

        function setCollapsibleContentState(content, hiddenState, animate = true) {
            if (!content) return;

            const prefersReducedMotion = typeof window.matchMedia === 'function'
                && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
            const shouldAnimate = animate && !prefersReducedMotion;
            const transitionMs = 240;

            const clearActiveTransition = function () {
                if (content._collapseTimer) {
                    clearTimeout(content._collapseTimer);
                    content._collapseTimer = null;
                }
                if (typeof content._collapseOnEnd === 'function') {
                    content.removeEventListener('transitionend', content._collapseOnEnd);
                    content._collapseOnEnd = null;
                }
            };

            const finish = function (finalHiddenState) {
                clearActiveTransition();
                content.hidden = finalHiddenState;
                content.setAttribute('aria-hidden', finalHiddenState ? 'true' : 'false');
                if ('inert' in content) {
                    content.inert = finalHiddenState;
                }
                content.style.removeProperty('height');
                content.style.removeProperty('opacity');
                content.style.removeProperty('overflow');
                content.style.removeProperty('transition');
                content.style.removeProperty('will-change');
            };

            clearActiveTransition();

            if (!shouldAnimate) {
                finish(hiddenState);
                return;
            }

            content.hidden = false;
            content.setAttribute('aria-hidden', hiddenState ? 'true' : 'false');
            if ('inert' in content) {
                content.inert = hiddenState;
            }
            content.style.overflow = 'hidden';
            content.style.transition = 'height 240ms cubic-bezier(0.2, 0.8, 0.2, 1), opacity 180ms ease';
            content.style.willChange = 'height, opacity';

            const onEnd = function (event) {
                if (event.target !== content || event.propertyName !== 'height') return;
                finish(hiddenState);
            };

            content._collapseOnEnd = onEnd;
            content.addEventListener('transitionend', onEnd);
            content._collapseTimer = window.setTimeout(function () {
                finish(hiddenState);
            }, transitionMs + 80);

            if (hiddenState) {
                const startHeight = content.scrollHeight;
                content.style.height = startHeight + 'px';
                content.style.opacity = '1';
                content.offsetHeight;
                content.style.height = '0px';
                content.style.opacity = '0';
                return;
            }

            content.style.height = '0px';
            content.style.opacity = '0';
            content.offsetHeight;
            const targetHeight = content.scrollHeight;
            content.style.height = targetHeight + 'px';
            content.style.opacity = '1';
        }

        function setupPanelToggle(button, content, storageKey, hideLabel = 'Ocultar', showLabel = 'Mostrar') {
            if (!button || !content) return;
            const card = button.closest('.reception-toggle-card')
                || button.closest('section.ui-card')
                || button.closest('.ui-card');
            if (card) {
                card.classList.add('reception-toggle-card');
            }

            let isHidden = false;
            try {
                isHidden = localStorage.getItem(storageKey) === '1';
            } catch (_error) {
                isHidden = false;
            }

            let currentHiddenState = isHidden;
            const applyState = function (hiddenState, animateState = false) {
                currentHiddenState = hiddenState;
                setCollapsibleContentState(content, hiddenState, animateState);
                button.setAttribute('aria-expanded', hiddenState ? 'false' : 'true');
                button.textContent = hiddenState ? showLabel : hideLabel;
                if (card) {
                    card.classList.toggle('reception-panel-collapsed', hiddenState);
                    card.classList.toggle('reception-panel-expanded', !hiddenState);
                }
            };

            applyState(isHidden, false);

            button.addEventListener('click', function () {
                const nextHiddenState = !currentHiddenState;
                applyState(nextHiddenState, true);
                try {
                    localStorage.setItem(storageKey, nextHiddenState ? '1' : '0');
                } catch (_error) {
                    // Ignore storage errors.
                }
            });
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

            const prefixed = value.match(/^(?:uid|rfid|qr|code|código)\s*[:\-]\s*(.+)$/i);
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
                can_auto_checkout: payload && Object.prototype.hasOwnProperty.call(payload, 'can_auto_checkout')
                    ? payload.can_auto_checkout === true
                    : null,
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
                return 'Muy buen progreso mensual. Sigue así.';
            }

            if (daysInfo.days !== null && daysInfo.days >= 0 && daysInfo.days <= 5) {
                return 'Renovación cercana: oportunidad perfecta para venta.';
            }

            return 'Buen trabajo. Check-in registrado y flujo estable.';
        }

        function statusDetailFromPayload(payload) {
            const reason = payloadReason(payload);
            const eventType = payload && payload.event_type ? String(payload.event_type) : 'checkin';

            if (payload && payload.ok && eventType === 'checkout') {
                return 'Salida registrada. El cupo y la presencia se actualizaron en vivo.';
            }

            if (payload && payload.ok) {
                return 'Ingreso confirmado. Puedes continuar con el siguiente cliente.';
            }

            if (reason === 'membership_inactive') {
                return 'Membresia no vigente. Solicita renovacion antes de permitir ingreso.';
            }

            if (reason === 'not_found' || reason === 'client_inactive' || reason === 'credential_inactive') {
                return 'Documento o credencial no valida. Verifica datos en clientes.';
            }

            if (reason === 'duplicate_attendance') {
                return 'El cliente ya tenia ingreso hoy. Si corresponde, usa registrar salida.';
            }

            if (reason === 'not_inside') {
                return 'No existe un ingreso activo para registrar salida.';
            }

            return 'Revisa el codigo escaneado y vuelve a intentar.';
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

        function buildRecentAttendanceRow(id, dateValue, timeValue, clientName, methodText, methodRaw) {
            const row = document.createElement('tr');
            row.setAttribute('data-role', 'recent-attendance-row');
            if (id !== null) {
                row.setAttribute('data-attendance-id', String(id));
            }
            row.setAttribute('data-attendance-date', String(dateValue || '').trim());
            row.setAttribute('data-attendance-method', String(methodRaw || '').trim().toLowerCase());
            row.setAttribute('data-attendance-client', normalizeSearchText(clientName));
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
                buildRecentAttendanceRow(attendanceId, dateValue, timeValue, clientName, methodText, payload.method || '')
            );

            const rows = recentAttendancesBody.querySelectorAll('tr[data-role="recent-attendance-row"]');
            if (rows.length > 10) {
                for (let i = 10; i < rows.length; i += 1) {
                    rows[i].remove();
                }
            }

            applyRecentAttendanceFilters();
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

        setupPanelToggle(toggleMobileQrPanelBtn, mobileQrPanelContent, 'reception.panel.mobile_qr.hidden');
        setupPanelToggle(toggleResultPanelBtn, resultPanelContent, 'reception.panel.result.hidden');
        setupPanelToggle(toggleRecentPanelBtn, recentPanelContent, 'reception.panel.recent.hidden');

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
            if (mobileScannerIsOpen) {
                return false;
            }

            if (attendanceHistoryModal && !attendanceHistoryModal.classList.contains('hidden')) {
                return false;
            }

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

            if (statusDetail) {
                statusDetail.textContent = statusDetailFromPayload(payload);
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
            if (statusDetail) {
                statusDetail.textContent = 'Atajos: Enter registra ingreso, F3 registra salida y F2 limpia el campo.';
            }
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
            if (scanTimer) {
                clearTimeout(scanTimer);
                scanTimer = null;
            }

            const isCheckoutAction = action === 'checkout';
            const endpoint = isCheckoutAction ? checkOutEndpoint : checkInEndpoint;

            const value = normalizeInput(forcedValue === null ? input.value : forcedValue);
            if (!value) {
                render({ ok: false, message: 'Ingrese un valor para procesar.', method: null, client: null });
                scheduleReset();
                focusInput();
                return;
            }

            const submitSignature = action + '|' + value;
            const nowMs = Date.now();
            if (submitSignature === lastSubmitSignature && (nowMs - lastSubmitAt) < SUBMIT_DEDUP_WINDOW_MS) {
                return;
            }

            lastSubmitSignature = submitSignature;
            lastSubmitAt = nowMs;
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
            if (statusDetail) {
                statusDetail.textContent = isCheckoutAction
                    ? 'Procesando salida. Mantén el lector apuntando hasta confirmar.'
                    : 'Procesando ingreso. Espera la respuesta del servidor.';
            }

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
            if (event.defaultPrevented) return;
            if (mobileScannerIsOpen) return;
            if (attendanceHistoryModal && !attendanceHistoryModal.classList.contains('hidden')) return;

            const target = event.target;
            const isInputFocused = target === input;
            const isTypingElsewhere = target instanceof HTMLElement
                && isEditableElement(target)
                && !isInputFocused;

            if ((event.ctrlKey || event.metaKey) && String(event.key).toLowerCase() === 'k') {
                event.preventDefault();
                focusInput(true);
                input.select();
                return;
            }

            if (isTypingElsewhere || event.ctrlKey || event.metaKey || event.altKey) {
                return;
            }

            if (event.key === 'F2') {
                event.preventDefault();
                input.value = '';
                renderIdle();
                focusInput(true);
                return;
            }

            if (event.key === 'F3') {
                event.preventDefault();
                submitCheckIn(null, 'checkout');
            }
        }, true);

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
            stopMobileScannerCamera(true);
            if (syncChannel) {
                syncChannel.close();
            }
            if (syncPollTimer) {
                clearInterval(syncPollTimer);
            }
        });

        document.addEventListener('visibilitychange', function () {
            if (document.hidden) {
                stopMobileScannerCamera(true);
                return;
            }

            if (!document.hidden) {
                pollLatestSyncEvent();
            }
        });

        startMobileQrStatusPolling();
        startSyncPolling();
        generateMobileQr(true);
        applyRecentAttendanceFilters();
        input.value = '';
        renderIdle();
        focusInput();
    })();
</script>
@endpush

