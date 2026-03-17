@php
    $progressStats = collect($progressOverview['stats'] ?? []);
    $alerts = collect($progressOverview['alerts'] ?? []);
    $prediction = (array) ($progressOverview['prediction'] ?? []);
    $weeklyGoal = (array) ($progressOverview['weekly_goal'] ?? []);
    $bodyState = (array) ($progressOverview['body_state'] ?? []);
    $timeline = (array) ($progressOverview['timeline'] ?? []);
    $timelineEntries = collect($timeline['entries'] ?? []);
    $training = (array) ($progressOverview['training'] ?? []);
    $profile = (array) ($progressOverview['profile'] ?? []);
    $profileReady = (bool) ($profile['ready'] ?? false);
    $canManageClientAccounts = (bool) ($canManageClientAccounts ?? false);
    $membership = (array) ($progressOverview['membership'] ?? []);
    $performance = (array) ($progressOverview['performance'] ?? []);
    $snapshotSourceLabel = trim((string) ($progressOverview['snapshot_source_label'] ?? ''));

    $toneClasses = [
        'success' => 'border-emerald-300/70 bg-emerald-500/10 text-emerald-900 dark:border-emerald-400/30 dark:bg-emerald-400/15 dark:text-emerald-100',
        'warning' => 'border-amber-300/70 bg-amber-500/10 text-amber-900 dark:border-amber-400/30 dark:bg-amber-400/15 dark:text-amber-100',
        'danger' => 'border-rose-300/70 bg-rose-500/10 text-rose-900 dark:border-rose-400/30 dark:bg-rose-400/15 dark:text-rose-100',
        'info' => 'border-cyan-300/70 bg-cyan-500/10 text-cyan-900 dark:border-cyan-400/30 dark:bg-cyan-400/15 dark:text-cyan-100',
        'muted' => 'border-slate-300/70 bg-slate-100 text-slate-900 dark:border-white/10 dark:bg-slate-900/40 dark:text-slate-100',
    ];
    $barClasses = [
        'force' => 'bg-sky-500',
        'resistance' => 'bg-cyan-500',
        'discipline' => 'bg-emerald-500',
        'recovery' => 'bg-amber-500',
    ];
@endphp

@once
    @push('styles')
        <style>
            .client-progress-calendar {
                border: 1px solid color-mix(in srgb, var(--accent) 22%, var(--border));
                background:
                    radial-gradient(circle at top right, color-mix(in srgb, var(--accent) 20%, transparent), transparent 34%),
                    linear-gradient(180deg, color-mix(in srgb, var(--card) 96%, transparent), color-mix(in srgb, var(--card-2) 92%, transparent));
                border-radius: 22px;
                padding: 16px;
            }

            .client-progress-calendar-top {
                display: flex;
                justify-content: space-between;
                align-items: flex-start;
                gap: 14px;
                margin-bottom: 12px;
            }

            .client-progress-calendar-month {
                font-size: 13px;
                font-weight: 900;
                letter-spacing: .08em;
                text-transform: uppercase;
                color: color-mix(in srgb, var(--text) 92%, #ffffff);
            }

            .client-progress-calendar-help {
                margin-top: 4px;
                font-size: 12px;
                line-height: 1.4;
                color: color-mix(in srgb, var(--muted) 90%, #ffffff);
            }

            .client-progress-calendar-legend {
                display: flex;
                flex-wrap: wrap;
                gap: 8px 10px;
                justify-content: flex-end;
            }

            .client-progress-calendar-legend-item {
                display: inline-flex;
                align-items: center;
                gap: 6px;
                font-size: 11px;
                font-weight: 700;
                color: color-mix(in srgb, var(--muted) 90%, #ffffff);
            }

            .client-progress-calendar-dot {
                width: 9px;
                height: 9px;
                border-radius: 999px;
                display: inline-block;
            }

            .client-progress-calendar-dot-trained {
                background: #22c55e;
                box-shadow: 0 0 0 1px rgb(34 197 94 / 0.42);
            }

            .client-progress-calendar-dot-rest {
                background: #94a3b8;
                box-shadow: 0 0 0 1px rgb(148 163 184 / 0.45);
            }

            .client-progress-calendar-dot-missed {
                background: #f59e0b;
                box-shadow: 0 0 0 1px rgb(245 158 11 / 0.45);
            }

            .client-progress-calendar-dot-pending {
                background: rgb(148 163 184 / 0.45);
                box-shadow: 0 0 0 1px rgb(71 85 105 / 0.45);
            }

            .client-progress-calendar-weekdays,
            .client-progress-calendar-grid {
                display: grid;
                grid-template-columns: repeat(7, minmax(0, 1fr));
                gap: 8px;
            }

            .client-progress-calendar-weekdays {
                margin-bottom: 8px;
            }

            .client-progress-calendar-weekday {
                text-align: center;
                font-size: 11px;
                font-weight: 900;
                letter-spacing: .08em;
                text-transform: uppercase;
                color: color-mix(in srgb, var(--muted) 92%, #ffffff);
            }

            .client-progress-calendar-cell {
                position: relative;
                min-height: 54px;
                border-radius: 16px;
                border: 1px solid color-mix(in srgb, var(--border) 72%, transparent);
                background: color-mix(in srgb, var(--card) 92%, transparent);
                padding: 9px 8px;
                box-shadow: inset 0 1px 0 rgb(255 255 255 / 0.03);
                transition: transform .14s ease, border-color .14s ease, box-shadow .14s ease;
            }

            .client-progress-calendar-cell:hover {
                transform: translateY(-1px);
            }

            .client-progress-calendar-cell-placeholder {
                background: color-mix(in srgb, var(--card) 55%, transparent);
                border-style: dashed;
                opacity: .35;
                box-shadow: none;
                pointer-events: none;
            }

            .client-progress-calendar-cell-trained {
                border-color: color-mix(in srgb, #22c55e 68%, var(--border));
                background: linear-gradient(180deg, rgb(34 197 94 / 0.19), rgb(22 163 74 / 0.11));
            }

            .client-progress-calendar-cell-rest {
                border-color: color-mix(in srgb, var(--border) 86%, transparent);
                background: color-mix(in srgb, var(--card-2) 78%, transparent);
            }

            .client-progress-calendar-cell-missed {
                border-color: color-mix(in srgb, #f59e0b 62%, var(--border));
                background: linear-gradient(180deg, rgb(245 158 11 / 0.16), rgb(251 191 36 / 0.08));
            }

            .client-progress-calendar-cell-pending {
                border-style: dashed;
                border-color: color-mix(in srgb, var(--muted) 55%, transparent);
                background: color-mix(in srgb, var(--card) 72%, transparent);
                opacity: .82;
            }

            .client-progress-calendar-cell-today {
                box-shadow:
                    0 0 0 2px color-mix(in srgb, var(--accent) 34%, transparent),
                    0 10px 24px color-mix(in srgb, var(--accent) 16%, transparent);
            }

            .client-progress-calendar-day {
                font-size: 14px;
                font-weight: 900;
                line-height: 1;
                color: color-mix(in srgb, var(--text) 95%, #ffffff);
            }

            .client-progress-calendar-status {
                position: absolute;
                left: 8px;
                right: 8px;
                bottom: 8px;
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 6px;
            }

            .client-progress-calendar-chip {
                display: inline-flex;
                align-items: center;
                min-height: 18px;
                padding: 0 7px;
                border-radius: 999px;
                font-size: 10px;
                font-weight: 800;
                letter-spacing: .04em;
                text-transform: uppercase;
            }

            .client-progress-calendar-chip-trained {
                background: rgb(34 197 94 / 0.18);
                color: #166534;
            }

            .client-progress-calendar-chip-rest {
                background: rgb(148 163 184 / 0.16);
                color: color-mix(in srgb, var(--text) 75%, #ffffff);
            }

            .client-progress-calendar-chip-missed {
                background: rgb(245 158 11 / 0.18);
                color: #92400e;
            }

            .client-progress-calendar-chip-pending {
                background: rgb(148 163 184 / 0.12);
                color: color-mix(in srgb, var(--muted) 88%, #ffffff);
            }

            .client-progress-calendar-marker {
                width: 8px;
                height: 8px;
                border-radius: 999px;
                flex: 0 0 auto;
            }

            @media (prefers-color-scheme: dark) {
                .client-progress-calendar-chip-trained {
                    color: #dcfce7;
                }

                .client-progress-calendar-chip-missed {
                    color: #fef3c7;
                }
            }

            @media (max-width: 860px) {
                .client-progress-calendar-top {
                    flex-direction: column;
                }

                .client-progress-calendar-legend {
                    justify-content: flex-start;
                }
            }

            @media (max-width: 640px) {
                .client-progress-calendar {
                    padding: 12px;
                }

                .client-progress-calendar-weekdays,
                .client-progress-calendar-grid {
                    gap: 6px;
                }

                .client-progress-calendar-cell {
                    min-height: 46px;
                    padding: 7px 6px;
                    border-radius: 13px;
                }

                .client-progress-calendar-day {
                    font-size: 12px;
                }

                .client-progress-calendar-chip {
                    padding: 0 5px;
                    font-size: 9px;
                }
            }
        </style>
    @endpush
@endonce

<div class="space-y-6">
    <section class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
        @foreach ($progressStats as $stat)
            @php
                $tone = (string) ($stat['tone'] ?? 'muted');
            @endphp
            <article class="rounded-2xl border p-4 shadow-sm {{ $toneClasses[$tone] ?? $toneClasses['muted'] }}">
                <p class="text-xs font-semibold uppercase tracking-widest opacity-80">{{ $stat['label'] ?? 'Dato' }}</p>
                <p class="mt-2 text-2xl font-black">{{ $stat['value'] ?? '-' }}</p>
                <p class="mt-1 text-xs opacity-80">{{ $stat['meta'] ?? '' }}</p>
            </article>
        @endforeach
    </section>

    <div class="client-layout-wide">
        <div class="space-y-6">
            <x-ui.card title="Analisis de rendimiento" subtitle="Lectura operativa del progreso del cliente.">
                <div class="grid gap-4 lg:grid-cols-[1.35fr_.65fr]">
                    <div class="rounded-xl border border-slate-300 bg-slate-100 p-4 dark:border-white/10 dark:bg-slate-900/40">
                        <p class="text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Estado actual</p>
                        <div class="mt-2 flex flex-wrap items-center gap-2">
                            <x-ui.badge :variant="match ((string) ($performance['tone'] ?? 'muted')) { 'success' => 'success', 'warning' => 'warning', 'danger' => 'danger', 'info' => 'info', default => 'muted' }">
                                {{ $performance['label'] ?? 'Sin lectura' }}
                            </x-ui.badge>
                            <span class="text-sm text-slate-700 dark:text-slate-300">Membresia: {{ $membership['status_label'] ?? 'Sin membresia' }}</span>
                        </div>
                        <p class="mt-3 text-sm text-slate-700 dark:text-slate-300">{{ $performance['summary'] ?? 'Sin resumen disponible.' }}</p>
                        <p class="mt-2 text-sm text-slate-700 dark:text-slate-300">Ultima asistencia: <span class="font-semibold text-slate-900 dark:text-slate-100">{{ $progressOverview['last_attendance_label'] ?? 'Sin asistencia' }}</span></p>
                        <p class="mt-1 text-sm text-slate-700 dark:text-slate-300">Ventana activa: <span class="font-semibold text-slate-900 dark:text-slate-100">{{ $membership['period_window_label'] ?? 'Sin membresia activa' }}</span></p>
                        @if ($snapshotSourceLabel !== '')
                            <p class="mt-3 rounded-lg border border-cyan-300/60 bg-cyan-500/10 px-3 py-2 text-xs text-cyan-900 dark:border-cyan-400/30 dark:bg-cyan-400/10 dark:text-cyan-100">{{ $snapshotSourceLabel }}</p>
                        @endif
                    </div>

                    <div class="rounded-xl border border-slate-300 bg-slate-100 p-4 dark:border-white/10 dark:bg-slate-900/40">
                        <p class="text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Actividad en vivo</p>
                        <p class="mt-2 text-4xl font-black text-slate-900 dark:text-slate-100">{{ (int) ($progressOverview['live_clients_count'] ?? 0) }}</p>
                        <p class="mt-1 text-sm text-slate-700 dark:text-slate-300">clientes presentes ahora</p>
                        <div class="mt-4 space-y-2 text-sm text-slate-700 dark:text-slate-300">
                            <p>Vence: <span class="font-semibold text-slate-900 dark:text-slate-100">{{ $membership['ends_at_label'] ?? 'N/A' }}</span></p>
                            <p>Dias restantes: <span class="font-semibold text-slate-900 dark:text-slate-100">{{ $membership['days_remaining_label'] ?? 'N/A' }}</span></p>
                        </div>
                    </div>
                </div>
            </x-ui.card>

            <div class="grid gap-6 lg:grid-cols-2">
                <x-ui.card title="Prediccion" subtitle="Proyeccion simple basada en constancia y objetivo.">
                    <p class="text-xs font-semibold uppercase tracking-widest text-cyan-700 dark:text-cyan-300">{{ $prediction['rhythm_label'] ?? 'Sin datos' }} | Constancia: {{ (int) ($prediction['consistency_percent'] ?? 0) }}%</p>
                    <p class="mt-3 text-lg font-black text-slate-900 dark:text-slate-100">{{ $prediction['primary_line'] ?? 'Sin prediccion disponible.' }}</p>
                    <p class="mt-2 text-sm text-slate-700 dark:text-slate-300">{{ $prediction['secondary_line'] ?? '' }}</p>
                    <p class="mt-2 text-xs text-slate-500 dark:text-slate-400">{{ $prediction['context_line'] ?? '' }}</p>
                </x-ui.card>

                <x-ui.card title="Meta semanal" subtitle="Seguimiento de frecuencia y adherencia.">
                    <p class="text-lg font-black text-slate-900 dark:text-slate-100">{{ (int) ($weeklyGoal['visits'] ?? 0) }} de {{ (int) ($weeklyGoal['target'] ?? 0) }} sesiones</p>
                    <div class="mt-3 h-2 rounded-full bg-slate-200 dark:bg-slate-800">
                        <span class="block h-2 rounded-full bg-emerald-500" style="width: {{ (int) ($weeklyGoal['completion_percent'] ?? 0) }}%;"></span>
                    </div>
                    <div class="mt-3 grid gap-2 text-sm text-slate-700 dark:text-slate-300 sm:grid-cols-3">
                        <p>Completado: <span class="font-semibold text-slate-900 dark:text-slate-100">{{ (int) ($weeklyGoal['completion_percent'] ?? 0) }}%</span></p>
                        <p>Faltan: <span class="font-semibold text-slate-900 dark:text-slate-100">{{ (int) ($weeklyGoal['remaining'] ?? 0) }}</span></p>
                        <p>Dias: <span class="font-semibold text-slate-900 dark:text-slate-100">{{ (int) ($weeklyGoal['days_left_week'] ?? 0) }}</span></p>
                    </div>
                    <p class="mt-3 text-sm text-slate-700 dark:text-slate-300">{{ $weeklyGoal['commitment_line'] ?? '' }}</p>
                    <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">{{ $weeklyGoal['rest_line'] ?? '' }}</p>
                    @if (! empty($weeklyGoal['alerts']))
                        <div class="mt-3 space-y-2">
                            @foreach ($weeklyGoal['alerts'] as $alert)
                                @php
                                    $alertTone = match ((string) ($alert['type'] ?? 'info')) {
                                        'success' => 'success',
                                        'warning' => 'warning',
                                        'danger' => 'danger',
                                        default => 'info',
                                    };
                                @endphp
                                <p class="rounded-lg border px-3 py-2 text-xs {{ $toneClasses[$alertTone] ?? $toneClasses['info'] }}">{{ $alert['text'] ?? '' }}</p>
                            @endforeach
                        </div>
                    @endif
                </x-ui.card>
            </div>

            <x-ui.card title="Historial del mes" subtitle="{{ $timeline['month_label'] ?? 'Mes actual' }}">
                <div class="client-progress-calendar">
                    <div class="client-progress-calendar-top">
                        <div>
                            <p class="client-progress-calendar-month">{{ $timeline['month_label'] ?? 'Mes actual' }}</p>
                            <p class="client-progress-calendar-help">Calendario visual de asistencias, descansos y dias pendientes del cliente.</p>
                        </div>
                        <div class="client-progress-calendar-legend" aria-label="Leyenda de estados">
                            <span class="client-progress-calendar-legend-item">
                                <span class="client-progress-calendar-dot client-progress-calendar-dot-trained" aria-hidden="true"></span>
                                Entreno
                            </span>
                            <span class="client-progress-calendar-legend-item">
                                <span class="client-progress-calendar-dot client-progress-calendar-dot-rest" aria-hidden="true"></span>
                                Descanso
                            </span>
                            <span class="client-progress-calendar-legend-item">
                                <span class="client-progress-calendar-dot client-progress-calendar-dot-missed" aria-hidden="true"></span>
                                Falta
                            </span>
                            <span class="client-progress-calendar-legend-item">
                                <span class="client-progress-calendar-dot client-progress-calendar-dot-pending" aria-hidden="true"></span>
                                Pendiente
                            </span>
                        </div>
                    </div>

                    <div class="client-progress-calendar-weekdays" aria-hidden="true">
                        @foreach (['L', 'M', 'X', 'J', 'V', 'S', 'D'] as $dayLabel)
                            <span class="client-progress-calendar-weekday">{{ $dayLabel }}</span>
                        @endforeach
                    </div>

                    <div class="client-progress-calendar-grid">
                        @foreach ($timelineEntries as $entry)
                            @php
                                $status = (string) ($entry['status'] ?? 'pending');
                                $isPlaceholder = ! empty($entry['is_placeholder']);
                                $statusLabel = match ($status) {
                                    'trained' => 'Entreno',
                                    'missed' => 'Falta',
                                    'rest' => 'Descanso',
                                    default => 'Pendiente',
                                };
                                $cellClass = match ($status) {
                                    'trained' => 'client-progress-calendar-cell-trained',
                                    'missed' => 'client-progress-calendar-cell-missed',
                                    'rest' => 'client-progress-calendar-cell-rest',
                                    default => 'client-progress-calendar-cell-pending',
                                };
                                $chipClass = match ($status) {
                                    'trained' => 'client-progress-calendar-chip-trained',
                                    'missed' => 'client-progress-calendar-chip-missed',
                                    'rest' => 'client-progress-calendar-chip-rest',
                                    default => 'client-progress-calendar-chip-pending',
                                };
                                $markerClass = match ($status) {
                                    'trained' => 'client-progress-calendar-dot-trained',
                                    'missed' => 'client-progress-calendar-dot-missed',
                                    'rest' => 'client-progress-calendar-dot-rest',
                                    default => 'client-progress-calendar-dot-pending',
                                };
                                $title = trim((string) (($entry['weekday_short'] ?? '').' '.($entry['date'] ?? '').' - '.$statusLabel.(! empty($entry['is_today']) ? ' (hoy)' : '')));
                            @endphp
                            <div
                                class="client-progress-calendar-cell {{ $isPlaceholder ? 'client-progress-calendar-cell-placeholder' : $cellClass }} {{ ! $isPlaceholder && ! empty($entry['is_today']) ? 'client-progress-calendar-cell-today' : '' }}"
                                @if (! $isPlaceholder)
                                    title="{{ $title }}"
                                    aria-label="{{ $title }}"
                                @else
                                    aria-hidden="true"
                                @endif
                            >
                                @if (! $isPlaceholder)
                                    <span class="client-progress-calendar-day">{{ $entry['label'] ?? '' }}</span>
                                    <div class="client-progress-calendar-status">
                                        <span class="client-progress-calendar-chip {{ $chipClass }}">{{ $statusLabel }}</span>
                                        <span class="client-progress-calendar-dot {{ $markerClass }} client-progress-calendar-marker" aria-hidden="true"></span>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            </x-ui.card>
        </div>

        <div class="space-y-6">
            <x-ui.card title="Alertas operativas" subtitle="Senales para recepcion, renovacion y seguimiento.">
                @if ($alerts->isNotEmpty())
                    <div class="space-y-3">
                        @foreach ($alerts as $alert)
                            @php
                                $alertTone = (string) ($alert['tone'] ?? 'info');
                            @endphp
                            <div class="rounded-xl border px-3 py-3 {{ $toneClasses[$alertTone] ?? $toneClasses['info'] }}">
                                <p class="text-xs font-black uppercase tracking-widest">{{ $alert['title'] ?? 'Alerta' }}</p>
                                <p class="mt-2 text-sm">{{ $alert['text'] ?? '' }}</p>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-sm text-slate-700 dark:text-slate-300">Sin alertas relevantes por ahora.</p>
                @endif
            </x-ui.card>

            <x-ui.card title="Estado del cuerpo" subtitle="Lectura orientativa con base en constancia y carga.">
                <p class="text-sm text-slate-700 dark:text-slate-300">{{ $bodyState['summary_line'] ?? 'Sin resumen disponible.' }}</p>
                <div class="mt-4 space-y-3">
                    @foreach (['force' => 'Fuerza', 'resistance' => 'Resistencia', 'discipline' => 'Disciplina', 'recovery' => 'Recuperacion'] as $key => $label)
                        @php
                            $value = (int) ($bodyState[$key] ?? 0);
                        @endphp
                        <div>
                            <div class="mb-1 flex items-center justify-between text-sm text-slate-700 dark:text-slate-300">
                                <span>{{ $label }}</span>
                                <span class="font-bold text-slate-900 dark:text-slate-100">{{ $value }}</span>
                            </div>
                            <div class="h-2 rounded-full bg-slate-200 dark:bg-slate-800">
                                <span class="block h-2 rounded-full {{ $barClasses[$key] ?? 'bg-cyan-500' }}" style="width: {{ $value }}%;"></span>
                            </div>
                        </div>
                    @endforeach
                </div>
                <p class="mt-3 text-xs text-slate-500 dark:text-slate-400">{{ $bodyState['context_line'] ?? '' }}</p>
            </x-ui.card>

            <x-ui.card title="Perfil fisico" subtitle="Datos base usados para el analisis.">
                <dl class="space-y-2 text-sm">
                    <div class="flex items-center justify-between gap-2">
                        <dt class="text-slate-600 dark:text-slate-400">Objetivo</dt>
                        <dd class="font-semibold text-slate-900 dark:text-slate-100">{{ $profile['goal_label'] ?? 'Sin objetivo' }}</dd>
                    </div>
                    <div class="flex items-center justify-between gap-2">
                        <dt class="text-slate-600 dark:text-slate-400">Principal</dt>
                        <dd class="font-semibold text-slate-900 dark:text-slate-100">{{ $profile['primary_goal_label'] ?? 'Sin objetivo' }}</dd>
                    </div>
                    <div class="flex items-center justify-between gap-2">
                        <dt class="text-slate-600 dark:text-slate-400">Secundario</dt>
                        <dd class="font-semibold text-slate-900 dark:text-slate-100">{{ $profile['secondary_goal_label'] ?? 'Sin secundario' }}</dd>
                    </div>
                    <div class="flex items-center justify-between gap-2">
                        <dt class="text-slate-600 dark:text-slate-400">Nivel</dt>
                        <dd class="font-semibold text-slate-900 dark:text-slate-100">{{ $profile['experience_label'] ?? 'Sin nivel' }}</dd>
                    </div>
                    <div class="flex items-center justify-between gap-2">
                        <dt class="text-slate-600 dark:text-slate-400">Dias/semana</dt>
                        <dd class="font-semibold text-slate-900 dark:text-slate-100">{{ $profile['days_per_week'] ?? 'N/A' }}</dd>
                    </div>
                    <div class="flex items-center justify-between gap-2">
                        <dt class="text-slate-600 dark:text-slate-400">Sesion</dt>
                        <dd class="font-semibold text-slate-900 dark:text-slate-100">{{ $profile['session_minutes'] ?? 'N/A' }} min</dd>
                    </div>
                    <div class="flex items-center justify-between gap-2">
                        <dt class="text-slate-600 dark:text-slate-400">Peso</dt>
                        <dd class="font-semibold text-slate-900 dark:text-slate-100">{{ $profile['weight_label'] ?? 'N/A' }}</dd>
                    </div>
                    <div class="flex items-center justify-between gap-2">
                        <dt class="text-slate-600 dark:text-slate-400">Altura</dt>
                        <dd class="font-semibold text-slate-900 dark:text-slate-100">{{ $profile['height_label'] ?? 'N/A' }}</dd>
                    </div>
                </dl>
                <p class="mt-3 text-xs text-slate-500 dark:text-slate-400">Limitaciones: {{ $profile['limitations_label'] ?? 'Sin datos' }}</p>
                @if (! empty($profile['updated_label']))
                    <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Ultima actualizacion: {{ $profile['updated_label'] }}</p>
                @endif
                @if (! $profileReady)
                    <div class="client-empty-state mt-4 rounded-xl border border-dashed border-slate-400 bg-slate-50 p-3">
                        <p class="text-xs font-semibold text-slate-700 dark:text-slate-200">Completa datos fisicos para activar recomendaciones mas precisas.</p>
                        <div class="mt-2 flex flex-wrap gap-2">
                            @if ($canManageClientAccounts)
                                <x-ui.button type="button" size="sm" variant="ghost" x-on:click="setTab('app_access')">Configurar usuario app</x-ui.button>
                            @endif
                            <x-ui.button type="button" size="sm" variant="secondary" x-on:click="setTab('credentials')">Enviar acceso PWA</x-ui.button>
                        </div>
                    </div>
                @endif
            </x-ui.card>

            <x-ui.card :title="(string) ($training['title'] ?? 'Orientacion de entrenamiento')" subtitle="Resumen rapido del enfoque recomendado.">
                <div class="space-y-2 text-sm text-slate-700 dark:text-slate-300">
                    <p class="font-semibold text-slate-900 dark:text-slate-100">{{ $training['objective_line'] ?? 'Sin objetivo' }}</p>
                    <p>{{ $training['focus_line'] ?? '' }}</p>
                    <p>{{ $training['rhythm_line'] ?? '' }}</p>
                    <p>{{ $training['adaptation_line'] ?? '' }}</p>
                    <p class="text-xs text-slate-500 dark:text-slate-400">{{ $training['context_line'] ?? '' }}</p>
                </div>
            </x-ui.card>
        </div>
    </div>
</div>
