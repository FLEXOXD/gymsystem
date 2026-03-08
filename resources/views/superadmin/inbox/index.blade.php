@extends('layouts.panel')

@section('title', 'Bandeja de contacto web')
@section('page-title', 'Notificaciones de contacto')

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
    <x-ui.card title="Bandeja de contacto web" subtitle="Mensajes del formulario público. Campana: 4 horas. Inbox y borrado: 24 horas.">
        @if ($unreadCount > 0)
            <div class="mb-4 rounded-xl border border-amber-300 bg-amber-50 px-4 py-3 text-sm text-amber-900 dark:border-amber-500/30 dark:bg-amber-900/20 dark:text-amber-100">
                <p class="font-bold">Tienes {{ $unreadCount }} mensaje(s) sin revisar.</p>
                <p class="mt-1">
                    Si no los abres, se eliminan automáticamente a las 24 horas.
                    @if ($nextUnreadMinutesLeft !== null)
                        El próximo vence en <strong>{{ $nextUnreadMinutesLeft }} min</strong>.
                    @endif
                </p>
            </div>
        @endif

        <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
            <div class="flex flex-wrap items-center gap-2 text-xs font-semibold uppercase tracking-wide">
                <span class="inline-flex items-center rounded-full bg-slate-200 px-2.5 py-1 text-slate-700 dark:bg-slate-800 dark:text-slate-200">
                    Total: {{ $totalCount }}
                </span>
                <span class="inline-flex items-center rounded-full bg-emerald-100 px-2.5 py-1 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-200">
                    Sin leer: {{ $unreadCount }}
                </span>
            </div>

            <form method="GET" action="{{ route('superadmin.inbox.index') }}" class="grid w-full gap-2 sm:w-auto sm:grid-cols-[150px_260px_auto]">
                <select name="status" class="ui-input">
                    <option value="all" @selected($filters['status'] === 'all')>Todos</option>
                    <option value="unread" @selected($filters['status'] === 'unread')>Sin leer</option>
                    <option value="read" @selected($filters['status'] === 'read')>Leidos</option>
                </select>
                <input type="text" name="q" value="{{ $filters['q'] }}" class="ui-input" placeholder="Buscar por nombre, correo o texto">
                <x-ui.button type="submit" variant="secondary">Filtrar</x-ui.button>
            </form>
        </div>

        <div class="grid gap-4 xl:grid-cols-[340px_minmax(0,1fr)]">
            <aside class="overflow-hidden rounded-xl border border-slate-200 bg-white dark:border-slate-700 dark:bg-slate-900">
                <div class="max-h-[68vh] overflow-y-auto">
                    @forelse ($messages as $message)
                        @php
                            $fullName = trim($message->first_name.' '.$message->last_name);
                            $isActive = $selectedMessage && (int) $selectedMessage->id === (int) $message->id;
                            $isUnread = $message->read_at === null;
                            $expiresAt = $message->created_at?->copy()->addHours(\App\Models\LandingContactMessage::INBOX_RETENTION_HOURS);
                            $minutesLeft = $expiresAt ? max(0, now()->diffInMinutes($expiresAt, false)) : null;
                        @endphp
                        <a href="{{ route('superadmin.inbox.show', array_merge(['message' => $message->id], request()->only(['status', 'q', 'page']))) }}"
                           class="block border-b border-slate-100 px-4 py-3 transition last:border-b-0 hover:bg-slate-50 dark:border-slate-800 dark:hover:bg-slate-800/40 {{ $isActive ? 'bg-slate-100 dark:bg-slate-800/70' : '' }}">
                            <div class="flex items-start justify-between gap-2">
                                <p class="text-sm font-bold text-slate-900 dark:text-slate-100">{{ $fullName !== '' ? $fullName : 'Sin nombre' }}</p>
                                @if ($isUnread)
                                    <span class="mt-1 inline-block h-2.5 w-2.5 rounded-full bg-emerald-500"></span>
                                @endif
                            </div>
                            <p class="mt-0.5 text-xs text-slate-500 dark:text-slate-300">{{ $message->email }}</p>
                            <p class="mt-1 text-xs text-slate-600 dark:text-slate-300">{{ \Illuminate\Support\Str::limit($message->message, 90) }}</p>
                            <div class="mt-1 flex items-center justify-between gap-2">
                                <p class="text-[11px] font-semibold uppercase tracking-wide text-slate-400 dark:text-slate-500">
                                    {{ $message->created_at?->copy()->timezone($panelTimezone)?->format('d/m/Y H:i') }}
                                </p>
                                @if ($minutesLeft !== null)
                                    <span class="inline-flex items-center rounded-full px-2 py-0.5 text-[10px] font-bold uppercase tracking-wide {{ $minutesLeft <= 20 ? 'bg-rose-100 text-rose-700 dark:bg-rose-900/40 dark:text-rose-200' : 'bg-slate-200 text-slate-700 dark:bg-slate-800 dark:text-slate-200' }}">
                                        Vence en {{ $minutesLeft }} min
                                    </span>
                                @endif
                            </div>
                        </a>
                    @empty
                        <div class="px-4 py-8 text-center text-sm text-slate-500 dark:text-slate-300">
                            No hay mensajes con este filtro.
                        </div>
                    @endforelse
                </div>
            </aside>

            <section class="rounded-xl border border-slate-200 bg-white p-4 dark:border-slate-700 dark:bg-slate-900">
                @if ($selectedMessage)
                    @php
                        $selectedFullName = trim($selectedMessage->first_name.' '.$selectedMessage->last_name);
                        $selectedExpiresAt = $selectedMessage->created_at?->copy()->addHours(\App\Models\LandingContactMessage::INBOX_RETENTION_HOURS);
                        $selectedMinutesLeft = $selectedExpiresAt ? max(0, now()->diffInMinutes($selectedExpiresAt, false)) : null;
                    @endphp
                    <div class="flex flex-wrap items-start justify-between gap-3 border-b border-slate-200 pb-3 dark:border-slate-700">
                        <div>
                            <h3 class="text-lg font-black text-slate-900 dark:text-slate-100">{{ $selectedFullName !== '' ? $selectedFullName : 'Sin nombre' }}</h3>
                            <p class="text-sm text-slate-600 dark:text-slate-300">{{ $selectedMessage->email }}</p>
                            <p class="mt-1 text-xs font-semibold uppercase tracking-wide text-slate-400 dark:text-slate-500">
                                Recibido: {{ $selectedMessage->created_at?->copy()->timezone($panelTimezone)?->format('d/m/Y H:i') }}
                            </p>
                            @if ($selectedMinutesLeft !== null)
                                <p class="mt-1 inline-flex items-center rounded-full px-2 py-0.5 text-[10px] font-bold uppercase tracking-wide {{ $selectedMinutesLeft <= 20 ? 'bg-rose-100 text-rose-700 dark:bg-rose-900/40 dark:text-rose-200' : 'bg-slate-200 text-slate-700 dark:bg-slate-800 dark:text-slate-200' }}">
                                    Se elimina en {{ $selectedMinutesLeft }} min
                                </p>
                            @endif
                        </div>
                        @if ($selectedMessage->read_at === null)
                            <form method="POST" action="{{ route('superadmin.inbox.read', $selectedMessage->id) }}">
                                @csrf
                                <x-ui.button type="submit" size="sm" variant="secondary">Marcar como leido</x-ui.button>
                            </form>
                        @else
                            <span class="inline-flex items-center rounded-full bg-slate-200 px-2.5 py-1 text-xs font-bold uppercase tracking-wide text-slate-700 dark:bg-slate-800 dark:text-slate-200">
                                Leido
                            </span>
                        @endif
                    </div>

                    <article class="mt-4 whitespace-pre-wrap break-words rounded-xl bg-slate-50 p-4 text-sm leading-relaxed text-slate-800 dark:bg-slate-800/50 dark:text-slate-100">
                        {{ $selectedMessage->message }}
                    </article>
                @else
                    <div class="py-14 text-center text-sm text-slate-500 dark:text-slate-300">
                        Selecciona un mensaje para ver el detalle.
                    </div>
                @endif
            </section>
        </div>

        <div class="mt-4">
            {{ $messages->links() }}
        </div>
    </x-ui.card>
@endsection
