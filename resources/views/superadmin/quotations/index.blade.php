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
    @endphp
    <x-ui.card title="Solicitudes de cotización" subtitle="Leads enviados desde el modal comercial de la landing principal.">
        @if ($unreadCount > 0)
            <div class="mb-4 rounded-xl border border-cyan-300 bg-cyan-50 px-4 py-3 text-sm text-cyan-900 dark:border-cyan-500/30 dark:bg-cyan-900/20 dark:text-cyan-100">
                <p class="font-bold">Tienes {{ $unreadCount }} solicitud(es) pendientes de revisar.</p>
                <p class="mt-1">Abre cada solicitud para ver teléfono, país, cantidad de personal y observaciones.</p>
            </div>
        @endif

        <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
            <div class="flex flex-wrap items-center gap-2 text-xs font-semibold uppercase tracking-wide">
                <span class="inline-flex items-center rounded-full bg-slate-200 px-2.5 py-1 text-slate-700 dark:bg-slate-800 dark:text-slate-200">
                    Total: {{ $totalCount }}
                </span>
                <span class="inline-flex items-center rounded-full bg-cyan-100 px-2.5 py-1 text-cyan-800 dark:bg-cyan-900/40 dark:text-cyan-200">
                    Sin revisar: {{ $unreadCount }}
                </span>
            </div>

            <form method="GET" action="{{ route('superadmin.quotations.index') }}" class="grid w-full gap-2 sm:w-auto sm:grid-cols-[150px_260px_auto]">
                <select name="status" class="ui-input">
                    <option value="all" @selected($filters['status'] === 'all')>Todos</option>
                    <option value="unread" @selected($filters['status'] === 'unread')>Sin revisar</option>
                    <option value="read" @selected($filters['status'] === 'read')>Revisados</option>
                </select>
                <input type="text" name="q" value="{{ $filters['q'] }}" class="ui-input" placeholder="Buscar por nombre, correo, país o plan">
                <x-ui.button type="submit" variant="secondary">Filtrar</x-ui.button>
            </form>
        </div>

        <div class="grid gap-4 xl:grid-cols-[340px_minmax(0,1fr)]">
            <aside class="overflow-hidden rounded-xl border border-slate-200 bg-white dark:border-slate-700 dark:bg-slate-900">
                <div class="max-h-[68vh] overflow-y-auto">
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
                           class="block border-b border-slate-100 px-4 py-3 transition last:border-b-0 hover:bg-slate-50 dark:border-slate-800 dark:hover:bg-slate-800/40 {{ $isActive ? 'bg-slate-100 dark:bg-slate-800/70' : '' }}">
                            <div class="flex items-start justify-between gap-2">
                                <p class="text-sm font-bold text-slate-900 dark:text-slate-100">{{ $fullName !== '' ? $fullName : 'Sin nombre' }}</p>
                                @if ($isUnread)
                                    <span class="mt-1 inline-block h-2.5 w-2.5 rounded-full bg-cyan-500"></span>
                                @endif
                            </div>
                            <p class="mt-0.5 text-xs text-slate-500 dark:text-slate-300">{{ $quote->email }}</p>
                            <p class="mt-1 text-xs text-slate-600 dark:text-slate-300">{{ $phoneDisplay !== '' ? $phoneDisplay : 'Sin teléfono' }}</p>
                            <div class="mt-2 flex flex-wrap items-center gap-1.5">
                                <span class="inline-flex items-center rounded-full bg-slate-200 px-2 py-0.5 text-[10px] font-bold uppercase tracking-wide text-slate-700 dark:bg-slate-800 dark:text-slate-200">
                                    {{ $quote->country }}
                                </span>
                                <span class="inline-flex items-center rounded-full bg-emerald-100 px-2 py-0.5 text-[10px] font-bold uppercase tracking-wide text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-200">
                                    {{ $quote->professionals_count }} profesionales
                                </span>
                                @if ($planLabel !== '')
                                    <span class="inline-flex items-center rounded-full bg-cyan-100 px-2 py-0.5 text-[10px] font-bold uppercase tracking-wide text-cyan-800 dark:bg-cyan-900/40 dark:text-cyan-200">
                                        {{ $planLabel }}
                                    </span>
                                @endif
                            </div>
                            <p class="mt-2 text-[11px] font-semibold uppercase tracking-wide text-slate-400 dark:text-slate-500">
                                {{ $receivedAt?->format('d/m/Y H:i') }}
                            </p>
                        </a>
                    @empty
                        <div class="px-4 py-8 text-center text-sm text-slate-500 dark:text-slate-300">
                            No hay solicitudes con este filtro.
                        </div>
                    @endforelse
                </div>
            </aside>

            <section class="rounded-xl border border-slate-200 bg-white p-4 dark:border-slate-700 dark:bg-slate-900">
                @if ($selectedQuote)
                    @php
                        $selectedFullName = trim($selectedQuote->first_name.' '.$selectedQuote->last_name);
                        $selectedPlanLabel = trim((string) ($selectedQuote->requested_plan ?? ''));
                        $selectedPlanLabel = $selectedPlanLabel !== '' ? \Illuminate\Support\Str::headline(str_replace(['-', '_'], ' ', $selectedPlanLabel)) : '';
                        $requestTypeLabel = $selectedPlanLabel !== '' ? 'Plan '.$selectedPlanLabel : 'Cotización general';
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
                            <form method="POST" action="{{ route('superadmin.quotations.read', $selectedQuote->id) }}">
                                @csrf
                                <x-ui.button type="submit" size="sm" variant="secondary">Marcar como revisada</x-ui.button>
                            </form>
                        @else
                            <span class="inline-flex items-center rounded-full bg-slate-200 px-2.5 py-1 text-xs font-bold uppercase tracking-wide text-slate-700 dark:bg-slate-800 dark:text-slate-200">
                                Revisada
                            </span>
                        @endif
                    </div>

                    <div class="mt-4 grid gap-3 md:grid-cols-2">
                        <article class="rounded-xl bg-slate-50 p-4 dark:bg-slate-800/50">
                            <p class="text-xs font-black uppercase tracking-wide text-slate-500 dark:text-slate-400">Teléfono</p>
                            <p class="mt-1 text-sm font-semibold text-slate-900 dark:text-slate-100">{{ trim($selectedQuote->phone_country_code.' '.$selectedQuote->phone_number) }}</p>
                        </article>
                        <article class="rounded-xl bg-slate-50 p-4 dark:bg-slate-800/50">
                            <p class="text-xs font-black uppercase tracking-wide text-slate-500 dark:text-slate-400">País</p>
                            <p class="mt-1 text-sm font-semibold text-slate-900 dark:text-slate-100">{{ $selectedQuote->country }}</p>
                        </article>
                        <article class="rounded-xl bg-slate-50 p-4 dark:bg-slate-800/50">
                            <p class="text-xs font-black uppercase tracking-wide text-slate-500 dark:text-slate-400">Profesionales</p>
                            <p class="mt-1 text-sm font-semibold text-slate-900 dark:text-slate-100">{{ $selectedQuote->professionals_count }}</p>
                        </article>
                        <article class="rounded-xl bg-slate-50 p-4 dark:bg-slate-800/50">
                            <p class="text-xs font-black uppercase tracking-wide text-slate-500 dark:text-slate-400">Tipo de solicitud</p>
                            <p class="mt-1 text-sm font-semibold text-slate-900 dark:text-slate-100">{{ $requestTypeLabel }}</p>
                        </article>
                        <article class="rounded-xl bg-slate-50 p-4 dark:bg-slate-800/50">
                            <p class="text-xs font-black uppercase tracking-wide text-slate-500 dark:text-slate-400">Estado</p>
                            <p class="mt-1 text-sm font-semibold text-slate-900 dark:text-slate-100">{{ $selectedQuote->read_at === null ? 'Pendiente' : 'Revisada' }}</p>
                        </article>
                    </div>

                    <article class="mt-4 rounded-xl bg-slate-50 p-4 text-sm leading-relaxed text-slate-800 dark:bg-slate-800/50 dark:text-slate-100">
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
@endsection
