@extends('layouts.panel')

@section('title', 'Historial de notificaciones')
@section('page-title', 'Historial de notificaciones')

@section('content')
    @php
        $visibleNotificationCount = method_exists($notifications, 'count') ? $notifications->count() : collect($notifications)->count();
        $historyItems = method_exists($notifications, 'getCollection') ? $notifications->getCollection() : collect($notifications);
        $sentHistoryCount = $historyItems->where('status', 'sent')->count();
        $skippedHistoryCount = $historyItems->where('status', 'skipped')->count();
        $historyTotal = method_exists($notifications, 'total') ? $notifications->total() : $visibleNotificationCount;
    @endphp
    <div class="sa-shell">
        <section class="sa-hero">
            <div class="sa-hero-grid">
                <div>
                    <span class="sa-kicker">Historial</span>
                    <h2 class="sa-title">Historial de notificaciones mas claro para seguir lo enviado y lo omitido.</h2>
                    <p class="sa-subtitle">
                        Filtras por fecha o gimnasio y ves rapido que se envio, que se omitio y quien lo gestiono.
                    </p>
                    <div class="sa-actions">
                        <a href="{{ route('superadmin.notifications.index') }}" class="ui-button ui-button-secondary">Volver a pendientes</a>
                    </div>
                </div>

                <aside class="sa-note-card">
                    <p class="sa-note-label">Lectura rapida</p>
                    <div class="sa-note-list">
                        <div class="sa-note-item">
                            <strong>Envios y omisiones</strong>
                            <span>La tabla separa claramente lo enviado de lo omitido.</span>
                        </div>
                        <div class="sa-note-item">
                            <strong>Filtro por rango</strong>
                            <span>Sirve para revisar semanas o cortes comerciales puntuales.</span>
                        </div>
                        <div class="sa-note-item">
                            <strong>Gestionado por</strong>
                            <span>Queda visible quien movio cada notificacion.</span>
                        </div>
                    </div>
                </aside>
            </div>
        </section>

        <section class="sa-stat-grid">
            <article class="sa-stat-card is-neutral">
                <p class="sa-stat-label">Total historial</p>
                <p class="sa-stat-value">{{ $historyTotal }}</p>
                <p class="sa-stat-meta">Registros historicos segun filtros aplicados.</p>
            </article>
            <article class="sa-stat-card is-success">
                <p class="sa-stat-label">Enviadas</p>
                <p class="sa-stat-value">{{ $sentHistoryCount }}</p>
                <p class="sa-stat-meta">Notificaciones marcadas como enviadas en la pagina actual.</p>
            </article>
            <article class="sa-stat-card is-warning">
                <p class="sa-stat-label">Omitidas</p>
                <p class="sa-stat-value">{{ $skippedHistoryCount }}</p>
                <p class="sa-stat-meta">Casos omitidos dentro del filtro actual.</p>
            </article>
            <article class="sa-stat-card is-info">
                <p class="sa-stat-label">Visibles</p>
                <p class="sa-stat-value">{{ $visibleNotificationCount }}</p>
                <p class="sa-stat-meta">Resultados cargados en esta vista.</p>
            </article>
        </section>
    <x-ui.card title="Historial" subtitle="Notificaciones enviadas/omitidas con filtros por fecha y gimnasio.">
        <form method="GET" action="{{ route('superadmin.notifications.history') }}" class="mb-4 grid gap-3 md:grid-cols-4">
            <label class="text-sm font-semibold ui-muted">
                Desde
                <input type="date" name="date_from" value="{{ $filters['date_from'] }}" class="ui-input mt-1 block w-full">
            </label>
            <label class="text-sm font-semibold ui-muted">
                Hasta
                <input type="date" name="date_to" value="{{ $filters['date_to'] }}" class="ui-input mt-1 block w-full">
            </label>
            <label class="text-sm font-semibold ui-muted">
                Gimnasio
                <select name="gym_id" class="ui-input mt-1 block w-full">
                    <option value="">Todos</option>
                    @foreach ($gyms as $gym)
                        <option value="{{ $gym->id }}" @selected($filters['gym_id'] === (int) $gym->id)>{{ $gym->name }}</option>
                    @endforeach
                </select>
            </label>
            <div class="flex items-end">
                <x-ui.button type="submit" variant="secondary">Filtrar</x-ui.button>
            </div>
        </form>

        <div class="sa-table-shell overflow-x-auto">
            <table class="ui-table min-w-[1100px]">
                <thead>
                <tr class="border-b border-slate-200 bg-slate-50 text-left text-xs uppercase tracking-wider text-slate-500 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300">
                    <th class="px-3 py-3">Fecha</th>
                    <th class="px-3 py-3">Gimnasio</th>
                    <th class="px-3 py-3">Tipo</th>
                    <th class="px-3 py-3">Estado</th>
                    <th class="px-3 py-3">Plan</th>
                    <th class="px-3 py-3">Vence</th>
                    <th class="px-3 py-3">Gestionado por</th>
                    <th class="px-3 py-3">Enviado en</th>
                </tr>
                </thead>
                <tbody>
                @forelse ($notifications as $notification)
                    @php
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
                        <td class="dark:text-slate-200">{{ $notification->scheduled_for?->toDateString() }}</td>
                        <td class="font-semibold dark:text-slate-100">{{ $notification->gym?->name ?? 'N/D' }}</td>
                        <td class="dark:text-slate-200">{{ $typeLabel }}</td>
                        <td>
                            @php
                                $statusClass = $notification->status === 'sent'
                                    ? 'sa-status-chip is-success'
                                    : 'sa-status-chip is-neutral';
                            @endphp
                            <span class="{{ $statusClass }}">
                                {{ $notification->status === 'sent' ? 'Enviado' : 'Omitido' }}
                            </span>
                        </td>
                        <td class="dark:text-slate-200">{{ $notification->subscription?->plan_name ?? '-' }}</td>
                        <td class="dark:text-slate-200">{{ $notification->subscription?->ends_at?->toDateString() ?? '-' }}</td>
                        <td class="dark:text-slate-200">{{ $notification->createdBy?->name ?? '-' }}</td>
                        <td class="dark:text-slate-200">{{ $notification->sent_at?->format('Y-m-d H:i') ?? '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="sa-empty-row">
                            No hay resultados para el filtro actual.
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $notifications->links() }}
        </div>
    </x-ui.card>
    </div>
@endsection
