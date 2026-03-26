@extends('layouts.panel')

@section('title', 'Bandeja de notificaciones')
@section('page-title', 'Notificaciones pendientes')

@section('content')
    @php
        $pushCampaigns = $pushCampaigns ?? collect();
        $pushSentCount = $pushCampaigns->where('status', 'sent')->count();
        $pushProblemCount = $pushCampaigns->filter(fn ($campaign) => in_array((string) ($campaign->status ?? ''), ['partial', 'failed'], true))->count();
        $pendingNotificationCount = $notifications->count();
    @endphp
    <div class="sa-shell">
        <section class="sa-hero">
            <div class="sa-hero-grid">
                <div>
                    <span class="sa-kicker">Notificaciones</span>
                    <h2 class="sa-title">Campanas push y recordatorios en una lectura mas ordenada.</h2>
                    <p class="sa-subtitle">
                        Tienes campañas manuales arriba y pendientes automaticos abajo, con conteo rapido para actuar sin revisar toda la tabla.
                    </p>
                    <div class="sa-actions">
                        <a href="#pending-notifications" class="ui-button ui-button-secondary">Ver pendientes</a>
                        <a href="{{ route('superadmin.notifications.history') }}" class="ui-button ui-button-ghost">Ver historial</a>
                    </div>
                </div>

                <aside class="sa-note-card">
                    <p class="sa-note-label">Lectura rapida</p>
                    <div class="sa-note-list">
                        <div class="sa-note-item">
                            <strong>Push manual</strong>
                            <span>Lanza campañas segmentadas por gimnasio y audiencia.</span>
                        </div>
                        <div class="sa-note-item">
                            <strong>Pendientes del dia</strong>
                            <span>Los avisos automáticos quedan listos para marcar enviados u omitidos.</span>
                        </div>
                        <div class="sa-note-item">
                            <strong>Historial separado</strong>
                            <span>Los registros enviados u omitidos ya tienen su vista propia.</span>
                        </div>
                    </div>
                </aside>
            </div>
        </section>

        <section class="sa-stat-grid">
            <article class="sa-stat-card is-neutral">
                <p class="sa-stat-label">Campanas push</p>
                <p class="sa-stat-value">{{ $pushCampaigns->count() }}</p>
                <p class="sa-stat-meta">Ultimas campanas registradas en el panel.</p>
            </article>
            <article class="sa-stat-card is-success">
                <p class="sa-stat-label">Enviadas</p>
                <p class="sa-stat-value">{{ $pushSentCount }}</p>
                <p class="sa-stat-meta">Campanas completadas sin accion adicional.</p>
            </article>
            <article class="sa-stat-card is-warning">
                <p class="sa-stat-label">Con problema</p>
                <p class="sa-stat-value">{{ $pushProblemCount }}</p>
                <p class="sa-stat-meta">Campanas parciales o fallidas para revisar.</p>
            </article>
            <article class="sa-stat-card is-info">
                <p class="sa-stat-label">Pendientes</p>
                <p class="sa-stat-value">{{ $pendingNotificationCount }}</p>
                <p class="sa-stat-meta">Avisos automaticos pendientes para la fecha elegida.</p>
            </article>
        </section>
    <x-ui.card title="Campañas push" subtitle="Envio segmentado de notificaciones push a gimnasios y roles operativos.">
        <form method="POST"
              action="{{ route('superadmin.notifications.push-campaigns.send') }}"
              class="grid gap-3 md:grid-cols-2 xl:grid-cols-4"
              data-ui-loading-overlay="1"
              data-ui-loading-message="Enviando campaña push...">
            @csrf
            <label class="text-sm font-semibold ui-muted">
                Gimnasio
                <select name="gym_id" class="ui-input mt-1 block w-full">
                    <option value="">Todos los gimnasios</option>
                    @foreach ($gyms as $gym)
                        <option value="{{ $gym->id }}" @selected((int) old('gym_id') === (int) $gym->id)>{{ $gym->name }}</option>
                    @endforeach
                </select>
            </label>

            <label class="text-sm font-semibold ui-muted">
                Audiencia
                <select name="audience" class="ui-input mt-1 block w-full" required>
                    <option value="owners" @selected(old('audience', 'owners') === 'owners')>Solo duenos</option>
                    <option value="staff" @selected(old('audience') === 'staff')>Duenos y cajeros</option>
                    <option value="all_users" @selected(old('audience') === 'all_users')>Todos los usuarios del gym</option>
                </select>
            </label>

            <label class="text-sm font-semibold ui-muted xl:col-span-2">
                Titulo
                <input type="text" name="title" value="{{ old('title') }}" maxlength="120" class="ui-input mt-1 block w-full" placeholder="Ej: Recordatorio de renovación" required>
            </label>

            <label class="text-sm font-semibold ui-muted xl:col-span-2">
                Mensaje
                <input type="text" name="body" value="{{ old('body') }}" maxlength="255" class="ui-input mt-1 block w-full" placeholder="Mensaje corto para push en celular" required>
            </label>

            <label class="text-sm font-semibold ui-muted xl:col-span-3">
                Detalle (opcional)
                <textarea name="detail_text" rows="3" maxlength="1500" class="ui-input mt-1 block w-full" placeholder="Texto adicional para abrir en detalle">{{ old('detail_text') }}</textarea>
            </label>

            <label class="text-sm font-semibold ui-muted">
                Tag (opcional)
                <input type="text" name="tag" value="{{ old('tag') }}" maxlength="120" class="ui-input mt-1 block w-full" placeholder="promo-marzo-2026">
            </label>

            <div class="flex items-end xl:col-span-1">
                <x-ui.button type="submit" variant="primary">Enviar campaña push</x-ui.button>
            </div>
        </form>

        <div class="sa-table-shell mt-4 overflow-x-auto">
            <table class="ui-table min-w-[1100px]">
                <thead>
                <tr class="border-b border-slate-200 bg-slate-50 text-left text-xs uppercase tracking-wider text-slate-500 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300">
                    <th class="px-3 py-3">Fecha</th>
                    <th class="px-3 py-3">Gimnasio</th>
                    <th class="px-3 py-3">Audiencia</th>
                    <th class="px-3 py-3">Titulo</th>
                    <th class="px-3 py-3">Estado</th>
                    <th class="px-3 py-3">Enviadas</th>
                    <th class="px-3 py-3">Fallidas</th>
                    <th class="px-3 py-3">Saltadas</th>
                </tr>
                </thead>
                <tbody>
                @forelse (($pushCampaigns ?? collect()) as $campaign)
                    @php
                        $statusVariant = match ((string) ($campaign->status ?? 'queued')) {
                            'sent' => 'sa-status-chip is-success',
                            'partial' => 'sa-status-chip is-warning',
                            'failed' => 'sa-status-chip is-danger',
                            'skipped' => 'sa-status-chip is-neutral',
                            'sending' => 'sa-status-chip is-info',
                            default => 'sa-status-chip is-indigo',
                        };
                        $audienceLabel = match ((string) ($campaign->audience ?? 'owners')) {
                            'staff' => 'Duenos y cajeros',
                            'all_users' => 'Todos usuarios',
                            default => 'Solo duenos',
                        };
                    @endphp
                    <tr data-push-campaign-status="{{ (string) ($campaign->status ?? 'queued') }}">
                        <td class="dark:text-slate-200">{{ $campaign->created_at?->format('Y-m-d H:i') ?? '-' }}</td>
                        <td class="font-semibold dark:text-slate-100">{{ $campaign->gym?->name ?? 'Todos' }}</td>
                        <td class="dark:text-slate-200">{{ $audienceLabel }}</td>
                        <td class="dark:text-slate-200">{{ $campaign->title }}</td>
                        <td>
                            <span class="{{ $statusVariant }}">
                                {{ $campaign->status }}
                            </span>
                        </td>
                        <td class="dark:text-slate-200">{{ (int) ($campaign->sent_count ?? 0) }}</td>
                        <td class="dark:text-slate-200">{{ (int) ($campaign->failed_count ?? 0) }}</td>
                        <td class="dark:text-slate-200">{{ (int) ($campaign->skipped_count ?? 0) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="sa-empty-row">
                            Aún no se han enviado campañas push.
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </x-ui.card>

    <x-ui.card id="pending-notifications" title="Bandeja de notificaciones" subtitle="Avisos automaticos por vencimiento y días de gracia.">
        <form method="GET" action="{{ route('superadmin.notifications.index') }}" class="mb-4 flex flex-wrap items-end gap-3">
            <label class="text-sm font-semibold ui-muted">
                Fecha
                <input type="date" name="date" value="{{ $selectedDate }}" class="ui-input mt-1 block">
            </label>
            <x-ui.button type="submit" variant="secondary">Aplicar</x-ui.button>
        </form>

        <div class="mb-3 text-sm text-slate-600 dark:text-slate-300">Pendientes para {{ $selectedDate }}: <strong>{{ $notifications->count() }}</strong></div>
        <div class="sa-table-shell overflow-x-auto">
            <table class="ui-table min-w-[1100px]">
                <thead>
                <tr class="border-b border-slate-200 bg-slate-50 text-left text-xs uppercase tracking-wider text-slate-500 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300">
                    <th class="px-3 py-3">Tipo</th>
                    <th class="px-3 py-3">Gimnasio</th>
                    <th class="px-3 py-3">Plan</th>
                    <th class="px-3 py-3">Vence</th>
                    <th class="px-3 py-3">Canal</th>
                    <th class="px-3 py-3">Mensaje</th>
                    <th class="px-3 py-3">Acciones</th>
                </tr>
                </thead>
                <tbody>
                @forelse ($notifications as $notification)
                    @php
                        $typeClass = str_starts_with($notification->type, 'grace_')
                            ? 'sa-status-chip is-warning'
                            : 'sa-status-chip is-indigo';
                        $typeLabel = match ($notification->type) {
                            'expires_7' => 'Vence en 7 días',
                            'expires_3' => 'Vence en 3 días',
                            'expires_1' => 'Vence en 1 día',
                            'grace_1' => 'Gracia día 1',
                            'grace_2' => 'Gracia día 2',
                            'grace_3' => 'Gracia día 3',
                            default => str_replace('_', ' ', $notification->type),
                        };
                    @endphp
                    <tr>
                        <td>
                            <span class="{{ $typeClass }}">
                                {{ $typeLabel }}
                            </span>
                        </td>
                        <td class="font-semibold dark:text-slate-100">{{ $notification->gym?->name ?? 'N/D' }}</td>
                        <td class="dark:text-slate-200">{{ $notification->subscription?->plan_name ?? '-' }}</td>
                        <td class="dark:text-slate-200">{{ $notification->subscription?->ends_at?->toDateString() ?? '-' }}</td>
                        <td class="dark:text-slate-200">{{ $notification->channel }}</td>
                        <td>
                            <p id="msg-{{ $notification->id }}" class="max-w-md whitespace-pre-wrap break-words text-xs text-slate-700 dark:text-slate-200">{{ $notification->message_snapshot }}</p>
                        </td>
                        <td>
                            <div class="sa-action-row">
                                <button type="button"
                                        class="ui-button ui-button-muted"
                                        data-copy-target="msg-{{ $notification->id }}">
                                    Copiar mensaje
                                </button>
                                <form method="POST" action="{{ route('superadmin.notifications.sent', $notification->id) }}">
                                    @csrf
                                    <x-ui.button type="submit" size="sm" variant="success">Marcar enviado</x-ui.button>
                                </form>
                                <form method="POST" action="{{ route('superadmin.notifications.skipped', $notification->id) }}">
                                    @csrf
                                    <x-ui.button type="submit" size="sm" variant="danger">Omitir</x-ui.button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="sa-empty-row">
                            No hay notificaciones pendientes para esta fecha.
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </x-ui.card>
    </div>
@endsection

@push('scripts')
<script>
    document.querySelectorAll('[data-copy-target]').forEach(function (button) {
        button.addEventListener('click', function () {
            const id = button.getAttribute('data-copy-target');
            const element = document.getElementById(id);
            if (!element) {
                return;
            }

            navigator.clipboard.writeText(element.innerText).then(function () {
                const original = button.innerText;
                button.innerText = 'Copiado';
                setTimeout(function () {
                    button.innerText = original;
                }, 1000);
            });
        });
    });

</script>
@endpush
