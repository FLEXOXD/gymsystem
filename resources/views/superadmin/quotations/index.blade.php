@extends('layouts.panel')

@section('title', 'Solicitudes de cotización')
@section('page-title', 'Solicitudes de cotización')

@section('content')
    @php
        $panelTimezone = trim((string) (auth()->user()?->timezone ?? ''));
        if (
            $panelTimezone === ''
            || $panelTimezone === 'UTC'
            || ! in_array($panelTimezone, timezone_identifiers_list(), true)
        ) {
            $panelTimezone = 'America/Guayaquil';
        }
        $reviewedCount = max(0, (int) $totalCount - (int) $unreadCount);
    @endphp
    <div class="sa-shell">
        <section class="sa-hero">
            <div class="sa-hero-grid">
                <div>
                    <span class="sa-kicker">Leads comerciales</span>
                    <h2 class="sa-title">Cotizaciones claras para leer, priorizar y responder sin perder contexto.</h2>
                    <p class="sa-subtitle">
                        El listado queda a la izquierda y el detalle a la derecha para trabajar rapido sin abrir pantallas extra.
                    </p>
                </div>

                <aside class="sa-note-card">
                    <p class="sa-note-label">Uso recomendado</p>
                    <div class="sa-note-list">
                        <div class="sa-note-item">
                            <strong>Lee primero las pendientes</strong>
                            <span>Las solicitudes sin revisar quedan arriba.</span>
                        </div>
                        <div class="sa-note-item">
                            <strong>Filtra por plan o pais</strong>
                            <span>Sirve para separar leads de sucursales, premium o general.</span>
                        </div>
                        <div class="sa-note-item">
                            <strong>Marca al revisar</strong>
                            <span>El estado no cambia solo por abrir el detalle.</span>
                        </div>
                    </div>
                </aside>
            </div>
        </section>

        <section class="sa-stat-grid">
            <article class="sa-stat-card is-neutral">
                <p class="sa-stat-label">Total leads</p>
                <p class="sa-stat-value">{{ $totalCount }}</p>
                <p class="sa-stat-meta">Solicitudes registradas en la bandeja.</p>
            </article>
            <article class="sa-stat-card is-warning">
                <p class="sa-stat-label">Pendientes</p>
                <p class="sa-stat-value">{{ $unreadCount }}</p>
                <p class="sa-stat-meta">Solicitudes aun sin revisar.</p>
            </article>
            <article class="sa-stat-card is-success">
                <p class="sa-stat-label">Revisadas</p>
                <p class="sa-stat-value">{{ $reviewedCount }}</p>
                <p class="sa-stat-meta">Leads ya abiertos y procesados.</p>
            </article>
            <article class="sa-stat-card is-info">
                <p class="sa-stat-label">Visibles</p>
                <p class="sa-stat-value">{{ $quotes->count() }}</p>
                <p class="sa-stat-meta">Resultados del filtro actual.</p>
            </article>
        </section>

    <x-ui.card title="Solicitudes de cotización" subtitle="Leads enviados desde el modal comercial de la landing principal.">
        @if ($unreadCount > 0)
            <div class="mb-4 rounded-xl border border-cyan-300 bg-cyan-50 px-4 py-3 text-sm text-cyan-900 dark:border-cyan-500/30 dark:bg-cyan-900/20 dark:text-cyan-100">
                <p class="font-bold">Tienes {{ $unreadCount }} solicitud(es) pendientes de revisar.</p>
                <p class="mt-1">Abre cada solicitud para ver telefono, pais, cantidad de personal y observaciones.</p>
            </div>
        @endif

        <div class="sa-toolbar mb-4">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div class="flex flex-wrap items-center gap-2 text-xs font-semibold uppercase tracking-wide">
                    <span class="sa-pill is-neutral">
                        Total: {{ $totalCount }}
                    </span>
                    <span class="sa-pill is-info">
                        Sin revisar: {{ $unreadCount }}
                    </span>
                </div>

                <form method="GET" action="{{ route('superadmin.quotations.index') }}" class="grid w-full gap-2 sm:w-auto sm:grid-cols-[150px_260px_auto]">
                    <select name="status" class="ui-input">
                        <option value="all" @selected($filters['status'] === 'all')>Todos</option>
                        <option value="unread" @selected($filters['status'] === 'unread')>Sin revisar</option>
                        <option value="read" @selected($filters['status'] === 'read')>Revisados</option>
                    </select>
                    <input type="text" name="q" value="{{ $filters['q'] }}" class="ui-input" placeholder="Buscar por nombre, correo, pais o plan">
                    <x-ui.button type="submit" variant="secondary">Filtrar</x-ui.button>
                </form>
            </div>
        </div>

        <div class="sa-split-shell">
            <aside class="sa-split-pane">
                <div class="sa-split-scroll max-h-[68vh] overflow-y-auto">
                    @forelse ($quotes as $quote)
                        @php
                            $fullName = trim($quote->first_name.' '.$quote->last_name);
                            $isActive = $selectedQuote && (int) $selectedQuote->id === (int) $quote->id;
                            $isUnread = $quote->read_at === null;
                            $phoneDisplay = trim($quote->phone_country_code.' '.$quote->phone_number);
                            $planLabel = trim((string) ($quote->requested_plan ?? ''));
                            $planLabel = $planLabel !== '' ? \Illuminate\Support\Str::headline(str_replace(['-', '_'], ' ', $planLabel)) : '';
                            $receivedAt = $quote->created_at?->copy()->timezone($panelTimezone);
                        @endphp
                        <a href="{{ route('superadmin.quotations.show', array_merge(['quote' => $quote->id], request()->only(['status', 'q', 'page']))) }}"
                           class="sa-split-item {{ $isActive ? 'is-active' : '' }}">
                            <div class="flex items-start justify-between gap-2">
                                <p class="text-sm font-bold text-slate-900 dark:text-slate-100">{{ $fullName !== '' ? $fullName : 'Sin nombre' }}</p>
                                @if ($isUnread)
                                    <span class="mt-1 inline-block h-2.5 w-2.5 rounded-full bg-cyan-500"></span>
                                @endif
                            </div>
                            <p class="mt-0.5 text-xs text-slate-500 dark:text-slate-300">{{ $quote->email }}</p>
                            <p class="mt-1 text-xs text-slate-600 dark:text-slate-300">{{ $phoneDisplay !== '' ? $phoneDisplay : 'Sin telefono' }}</p>
                            <div class="mt-2 flex flex-wrap items-center gap-1.5">
                                <span class="sa-status-chip is-neutral">
                                    {{ $quote->country }}
                                </span>
                                <span class="sa-status-chip is-success">
                                    {{ $quote->professionals_count }} profesionales
                                </span>
                                @if ($planLabel !== '')
                                    <span class="sa-status-chip is-info">
                                        {{ $planLabel }}
                                    </span>
                                @endif
                            </div>
                            <p class="mt-2 text-[11px] font-semibold uppercase tracking-wide text-slate-400 dark:text-slate-500">
                                {{ $receivedAt?->format('d/m/Y H:i') }}
                            </p>
                        </a>
                    @empty
                        <div class="sa-empty-row">
                            No hay solicitudes con este filtro.
                        </div>
                    @endforelse
                </div>
            </aside>

            <section class="sa-split-detail">
                @if ($selectedQuote)
                    @php
                        $selectedFullName = trim($selectedQuote->first_name.' '.$selectedQuote->last_name);
                        $selectedPlanLabel = trim((string) ($selectedQuote->requested_plan ?? ''));
                        $selectedPlanLabel = $selectedPlanLabel !== '' ? \Illuminate\Support\Str::headline(str_replace(['-', '_'], ' ', $selectedPlanLabel)) : '';
                        $requestTypeLabel = $selectedPlanLabel !== '' ? 'Plan '.$selectedPlanLabel : 'Cotizacion general';
                        $selectedReceivedAt = $selectedQuote->created_at?->copy()->timezone($panelTimezone);
                    @endphp
                    <div class="flex flex-wrap items-start justify-between gap-3 border-b border-slate-200 pb-3 dark:border-slate-700">
                        <div>
                            <h3 class="text-lg font-black text-slate-900 dark:text-slate-100">{{ $selectedFullName !== '' ? $selectedFullName : 'Sin nombre' }}</h3>
                            <p class="text-sm text-slate-600 dark:text-slate-300">{{ $selectedQuote->email }}</p>
                            <p class="mt-1 text-xs font-semibold uppercase tracking-wide text-slate-400 dark:text-slate-500">
                                Recibido: {{ $selectedReceivedAt?->format('d/m/Y H:i') }}
                            </p>
                        </div>
                        @if ($selectedQuote->read_at === null)
                            <div class="sa-action-row">
                                <form method="POST" action="{{ route('superadmin.quotations.read', $selectedQuote->id) }}">
                                    @csrf
                                    <x-ui.button type="submit" size="sm" variant="secondary">Marcar como revisada</x-ui.button>
                                </form>
                            </div>
                        @else
                            <span class="sa-status-chip is-neutral">
                                Revisada
                            </span>
                        @endif
                    </div>

                    <div class="mt-4 grid gap-3 md:grid-cols-2">
                        <article class="sa-mini-card">
                            <p class="text-xs font-black uppercase tracking-wide text-slate-500 dark:text-slate-400">Telefono</p>
                            <p class="mt-1 text-sm font-semibold text-slate-900 dark:text-slate-100">{{ trim($selectedQuote->phone_country_code.' '.$selectedQuote->phone_number) }}</p>
                        </article>
                        <article class="sa-mini-card">
                            <p class="text-xs font-black uppercase tracking-wide text-slate-500 dark:text-slate-400">Pais</p>
                            <p class="mt-1 text-sm font-semibold text-slate-900 dark:text-slate-100">{{ $selectedQuote->country }}</p>
                        </article>
                        <article class="sa-mini-card">
                            <p class="text-xs font-black uppercase tracking-wide text-slate-500 dark:text-slate-400">Profesionales</p>
                            <p class="mt-1 text-sm font-semibold text-slate-900 dark:text-slate-100">{{ $selectedQuote->professionals_count }}</p>
                        </article>
                        <article class="sa-mini-card">
                            <p class="text-xs font-black uppercase tracking-wide text-slate-500 dark:text-slate-400">Tipo de solicitud</p>
                            <p class="mt-1 text-sm font-semibold text-slate-900 dark:text-slate-100">{{ $requestTypeLabel }}</p>
                        </article>
                        <article class="sa-mini-card">
                            <p class="text-xs font-black uppercase tracking-wide text-slate-500 dark:text-slate-400">Estado</p>
                            <p class="mt-2">
                                <span class="{{ $selectedQuote->read_at === null ? 'sa-status-chip is-warning' : 'sa-status-chip is-success' }}">
                                    {{ $selectedQuote->read_at === null ? 'Pendiente' : 'Revisada' }}
                                </span>
                            </p>
                        </article>
                    </div>

                    <article class="sa-message-surface mt-4">
                        <p class="text-xs font-black uppercase tracking-wide text-slate-500 dark:text-slate-400">Comentarios</p>
                        <div class="mt-2 whitespace-pre-wrap break-words">
                            {{ $selectedQuote->notes !== null && trim($selectedQuote->notes) !== '' ? $selectedQuote->notes : 'Sin comentarios adicionales.' }}
                        </div>
                    </article>
                @else
                    <div class="py-14 text-center text-sm text-slate-500 dark:text-slate-300">
                        Selecciona una solicitud para ver el detalle.
                    </div>
                @endif
            </section>
        </div>

        <div class="mt-4">
            {{ $quotes->links() }}
        </div>
    </x-ui.card>
    </div>
@endsection
