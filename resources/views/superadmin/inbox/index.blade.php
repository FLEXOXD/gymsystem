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
        $resolvePublicMediaUrl = static function (?string $path): ?string {
            $rawPath = trim((string) $path);
            if ($rawPath === '') {
                return null;
            }

            if (str_starts_with($rawPath, 'http://') || str_starts_with($rawPath, 'https://')) {
                return $rawPath;
            }

            $normalized = str_replace('\\', '/', ltrim($rawPath, '/'));
            $publicStorageMarker = '/storage/app/public/';
            $markerPos = strpos($normalized, $publicStorageMarker);
            if ($markerPos !== false) {
                $normalized = substr($normalized, $markerPos + strlen($publicStorageMarker));
            }
            if (str_starts_with($normalized, 'public/')) {
                $normalized = substr($normalized, strlen('public/'));
            }
            if (str_starts_with($normalized, 'storage/')) {
                $normalized = substr($normalized, strlen('storage/'));
            }
            $normalized = ltrim($normalized, '/');

            return $normalized !== '' ? asset('storage/'.$normalized) : null;
        };
        $supportStatusBadgeClasses = [
            \App\Models\SupportChatConversation::STATUS_BOT => 'bg-slate-200 text-slate-700 dark:bg-slate-800 dark:text-slate-200',
            \App\Models\SupportChatConversation::STATUS_WAITING_AGENT => 'bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-200',
            \App\Models\SupportChatConversation::STATUS_ACTIVE => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-200',
            \App\Models\SupportChatConversation::STATUS_CLOSED => 'bg-rose-100 text-rose-700 dark:bg-rose-900/40 dark:text-rose-200',
        ];
    @endphp
    <x-ui.card title="Bandeja de contacto web" subtitle="Mensajes del formulario público. Campaña: 4 horas. Inbox y borrado: 24 horas.">
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
                    <option value="read" @selected($filters['status'] === 'read')>Leídos</option>
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
                                <x-ui.button type="submit" size="sm" variant="secondary">Marcar como leído</x-ui.button>
                            </form>
                        @else
                            <span class="inline-flex items-center rounded-full bg-slate-200 px-2.5 py-1 text-xs font-bold uppercase tracking-wide text-slate-700 dark:bg-slate-800 dark:text-slate-200">
                                Leído
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

    <x-ui.card id="support-chat-inbox" title="Bandeja de chat de soporte" subtitle="Conversaciones del botón flotante (página principal y panel de gimnasios)." class="mt-6">
        @if (! ($supportSchemaReady ?? false))
            <div class="mb-4 rounded-xl border border-amber-300 bg-amber-50 px-4 py-3 text-sm text-amber-900 dark:border-amber-500/30 dark:bg-amber-900/20 dark:text-amber-100">
                <p class="font-bold">El módulo de chat de soporte aún no está instalado completamente.</p>
                <p class="mt-1">Ejecuta migraciones pendientes para habilitar la bandeja de conversaciones.</p>
            </div>
        @elseif (($supportLoadError ?? false) === true && $supportConversations->count() === 0)
            <div class="mb-4 rounded-xl border border-rose-300 bg-rose-50 px-4 py-3 text-sm text-rose-900 dark:border-rose-500/30 dark:bg-rose-900/20 dark:text-rose-100">
                <p class="font-bold">No se pudo cargar la bandeja de soporte.</p>
                <p class="mt-1">Revisa logs del servidor y vuelve a cargar. Los datos pueden estar desincronizados temporalmente.</p>
            </div>
        @endif

        <div class="mb-4 rounded-xl border border-sky-200 bg-sky-50 px-4 py-3 text-xs text-sky-900 dark:border-sky-500/40 dark:bg-sky-900/20 dark:text-sky-100">
            Si no hay respuesta del cliente, el sistema enviará un recordatorio automático y cerrará la conversación a los 15 minutos de inactividad.
        </div>

        <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
            <div class="flex flex-wrap items-center gap-2 text-xs font-semibold uppercase tracking-wide">
                <span class="inline-flex items-center rounded-full bg-slate-200 px-2.5 py-1 text-slate-700 dark:bg-slate-800 dark:text-slate-200">
                    Total: {{ $supportTotalCount }}
                </span>
                <span class="inline-flex items-center rounded-full bg-emerald-100 px-2.5 py-1 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-200">
                    Sin leer: {{ $supportUnreadCount }}
                </span>
                <span class="inline-flex items-center rounded-full px-2.5 py-1 {{ $supportRepresentativeOnline ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-200' : 'bg-slate-200 text-slate-700 dark:bg-slate-800 dark:text-slate-200' }}">
                    {{ $supportRepresentativeOnline ? ($supportRepresentativeName.' conectado') : 'Representante no conectado' }}
                </span>
            </div>

            <form method="GET" action="{{ route('superadmin.inbox.index') }}" class="grid w-full gap-2 sm:w-auto sm:grid-cols-[180px_260px_auto]">
                <input type="hidden" name="status" value="{{ $filters['status'] }}">
                <input type="hidden" name="q" value="{{ $filters['q'] }}">
                <select name="support_status" class="ui-input">
                    <option value="all" @selected(($supportFilters['status'] ?? 'all') === 'all')>Todos</option>
                    <option value="unread" @selected(($supportFilters['status'] ?? '') === 'unread')>Sin leer</option>
                    <option value="bot" @selected(($supportFilters['status'] ?? '') === 'bot')>Bot</option>
                    <option value="waiting_agent" @selected(($supportFilters['status'] ?? '') === 'waiting_agent')>Esperando agente</option>
                    <option value="active" @selected(($supportFilters['status'] ?? '') === 'active')>Activos</option>
                    <option value="closed" @selected(($supportFilters['status'] ?? '') === 'closed')>Cerrados</option>
                </select>
                <input type="text" name="support_q" value="{{ $supportFilters['q'] ?? '' }}" class="ui-input" placeholder="Buscar por gimnasio o contacto">
                <x-ui.button type="submit" variant="secondary">Filtrar</x-ui.button>
            </form>
        </div>

        @if (($supportUnreadCount ?? 0) > 0 && ($supportTotalCount ?? 0) === 0)
            <div class="mb-4 rounded-xl border border-amber-300 bg-amber-50 px-4 py-3 text-xs font-semibold text-amber-900 dark:border-amber-500/30 dark:bg-amber-900/20 dark:text-amber-100">
                Se detectaron pendientes sin conversaciones visibles. Refresca la página y valida que las tablas de soporte estén actualizadas.
            </div>
        @endif

        <div class="grid gap-4 xl:grid-cols-[360px_minmax(0,1fr)]">
            <aside class="overflow-hidden rounded-xl border border-slate-200 bg-white dark:border-slate-700 dark:bg-slate-900">
                <div class="max-h-[70vh] overflow-y-auto">
                    @forelse ($supportConversations as $conversation)
                        @php
                            $conversationActive = $selectedSupportConversation && (int) $selectedSupportConversation->id === (int) $conversation->id;
                            $logoUrl = $resolvePublicMediaUrl($conversation->gym?->logo_path);
                            $displayName = $conversation->displayName();
                            $displayInitials = \Illuminate\Support\Str::of($displayName)
                                ->explode(' ')
                                ->filter()
                                ->take(2)
                                ->map(static fn (string $word): string => mb_strtoupper(mb_substr($word, 0, 1)))
                                ->implode('');
                            $displayInitials = $displayInitials !== '' ? $displayInitials : 'GY';
                            $unreadSupport = (int) ($conversation->unread_for_superadmin_count ?? 0);
                            $statusClass = $supportStatusBadgeClasses[$conversation->status] ?? 'bg-slate-200 text-slate-700 dark:bg-slate-800 dark:text-slate-200';
                            $supportLinkQuery = array_merge(
                                request()->only(['status', 'q', 'page', 'support_status', 'support_q', 'support_page']),
                                ['support' => $conversation->id]
                            );
                        @endphp
                        <a href="{{ route('superadmin.inbox.index', $supportLinkQuery) }}"
                           class="block border-b border-slate-100 px-4 py-3 transition last:border-b-0 hover:bg-slate-50 dark:border-slate-800 dark:hover:bg-slate-800/40 {{ $conversationActive ? 'bg-slate-100 dark:bg-slate-800/70' : '' }}">
                            <div class="flex items-start gap-3">
                                <div class="h-10 w-10 shrink-0 overflow-hidden rounded-full border border-slate-200 bg-slate-100 dark:border-slate-700 dark:bg-slate-800">
                                    @if ($logoUrl)
                                        <img src="{{ $logoUrl }}" alt="{{ $displayName }}" class="h-full w-full object-cover">
                                    @else
                                        <span class="flex h-full w-full items-center justify-center text-xs font-black text-slate-700 dark:text-slate-200">{{ $displayInitials }}</span>
                                    @endif
                                </div>
                                <div class="min-w-0 flex-1">
                                    <div class="flex items-start justify-between gap-2">
                                        <p class="truncate text-sm font-bold text-slate-900 dark:text-slate-100">{{ $displayName }}</p>
                                        @if ($unreadSupport > 0)
                                            <span class="inline-flex min-w-[1.4rem] items-center justify-center rounded-full bg-emerald-500 px-1.5 py-0.5 text-[10px] font-black text-white">{{ $unreadSupport }}</span>
                                        @endif
                                    </div>
                                    <p class="mt-0.5 text-xs text-slate-500 dark:text-slate-300">{{ $conversation->sourceLabel() }} · {{ $conversation->requesterLabel() }}</p>
                                    <p class="mt-1 line-clamp-2 text-xs text-slate-600 dark:text-slate-300">{{ \Illuminate\Support\Str::limit((string) ($conversation->latestMessage?->message ?? 'Sin mensajes'), 110) }}</p>
                                    <div class="mt-1 flex items-center justify-between gap-2">
                                        <span class="inline-flex items-center rounded-full px-2 py-0.5 text-[10px] font-bold uppercase tracking-wide {{ $statusClass }}">
                                            {{ $conversation->statusLabel() }}
                                        </span>
                                        <span class="text-[11px] font-semibold uppercase tracking-wide text-slate-400 dark:text-slate-500">
                                            {{ $conversation->last_message_at?->copy()->timezone($panelTimezone)?->format('d/m/Y H:i') }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </a>
                    @empty
                        <div class="px-4 py-8 text-center text-sm text-slate-500 dark:text-slate-300">
                            A&uacute;n no hay conversaciones de soporte.
                        </div>
                    @endforelse
                </div>
            </aside>

            <section class="rounded-xl border border-slate-200 bg-white p-4 dark:border-slate-700 dark:bg-slate-900">
                @if ($selectedSupportConversation)
                    @php
                        $selectedSupportStatusClass = $supportStatusBadgeClasses[$selectedSupportConversation->status] ?? 'bg-slate-200 text-slate-700 dark:bg-slate-800 dark:text-slate-200';
                    @endphp
                    <div class="flex flex-wrap items-start justify-between gap-3 border-b border-slate-200 pb-3 dark:border-slate-700">
                        <div>
                            <h3 class="text-lg font-black text-slate-900 dark:text-slate-100">{{ $selectedSupportConversation->displayName() }}</h3>
                            <p class="text-sm text-slate-600 dark:text-slate-300">
                                {{ $selectedSupportConversation->sourceLabel() }} · {{ $selectedSupportConversation->requesterLabel() }}
                            </p>
                            @if (trim((string) $selectedSupportConversation->visitor_email) !== '')
                                <p class="text-xs text-slate-500 dark:text-slate-400">{{ $selectedSupportConversation->visitor_email }}</p>
                            @endif
                            @if (trim((string) $selectedSupportConversation->visitor_name) !== '')
                                <p class="text-xs text-slate-500 dark:text-slate-400">Contacto: {{ $selectedSupportConversation->visitor_name }}</p>
                            @endif
                        </div>
                        <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-bold uppercase tracking-wide {{ $selectedSupportStatusClass }}">
                            {{ $selectedSupportConversation->statusLabel() }}
                        </span>
                    </div>

                    <div data-support-thread class="mt-4 max-h-[46vh] space-y-3 overflow-y-auto rounded-xl border border-slate-200 bg-slate-50 p-3 dark:border-slate-700 dark:bg-slate-800/40">
                        @forelse ($selectedSupportMessages as $chatMessage)
                            @php
                                $senderType = (string) ($chatMessage->sender_type ?? '');
                                $isMine = $senderType === \App\Models\SupportChatMessage::SENDER_SUPERADMIN;
                                $isSystem = in_array($senderType, [\App\Models\SupportChatMessage::SENDER_SYSTEM, \App\Models\SupportChatMessage::SENDER_BOT], true);
                                $senderLabel = trim((string) ($chatMessage->sender_label ?? ''));
                                if ($senderLabel === '') {
                                    $senderLabel = match ($senderType) {
                                        \App\Models\SupportChatMessage::SENDER_VISITOR => 'Visitante',
                                        \App\Models\SupportChatMessage::SENDER_GYM => 'Gimnasio',
                                        \App\Models\SupportChatMessage::SENDER_SUPERADMIN => 'SuperAdmin',
                                        \App\Models\SupportChatMessage::SENDER_BOT => 'Bot',
                                        default => 'Sistema',
                                    };
                                }
                            @endphp
                            <div class="flex {{ $isMine ? 'justify-end' : 'justify-start' }}">
                                <article class="max-w-[84%] rounded-xl px-3 py-2 text-sm {{ $isSystem ? 'border border-indigo-200 bg-indigo-50 text-indigo-900 dark:border-indigo-800/60 dark:bg-indigo-900/25 dark:text-indigo-100' : ($isMine ? 'bg-sky-600 text-white' : 'border border-slate-200 bg-white text-slate-800 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100') }}">
                                    <p class="text-[11px] font-black uppercase tracking-wide {{ $isMine ? 'text-sky-100' : ($isSystem ? 'text-indigo-700 dark:text-indigo-200' : 'text-slate-500 dark:text-slate-400') }}">{{ $senderLabel }}</p>
                                    <p class="mt-1 whitespace-pre-wrap break-words leading-relaxed">{{ $chatMessage->message }}</p>
                                    <p class="mt-1 text-[10px] font-semibold uppercase tracking-wide {{ $isMine ? 'text-sky-100/90' : 'text-slate-400 dark:text-slate-500' }}">
                                        {{ $chatMessage->created_at?->copy()->timezone($panelTimezone)?->format('d/m/Y H:i') }}
                                    </p>
                                </article>
                            </div>
                        @empty
                            <div class="py-10 text-center text-sm text-slate-500 dark:text-slate-300">
                                A&uacute;n no hay mensajes en esta conversaci&oacute;n.
                            </div>
                        @endforelse
                    </div>

                    <div class="mt-4 grid gap-3 lg:grid-cols-[minmax(0,1fr)_220px]">
                        <form method="POST" action="{{ route('superadmin.support-chat.reply', $selectedSupportConversation->id) }}" class="space-y-2">
                            @csrf
                            <label class="text-xs font-bold uppercase tracking-wide text-slate-500 dark:text-slate-400">
                                Responder como SuperAdmin
                                <textarea name="message" rows="3" class="ui-input mt-1" placeholder="Escribe tu respuesta..." required></textarea>
                            </label>
                            <x-ui.button type="submit" variant="secondary">Enviar respuesta</x-ui.button>
                        </form>

                        <div class="space-y-2">
                            <form method="POST" action="{{ route('superadmin.support-chat.status', $selectedSupportConversation->id) }}" class="space-y-2">
                                @csrf
                                <label class="text-xs font-bold uppercase tracking-wide text-slate-500 dark:text-slate-400">
                                    Estado de la conversaci&oacute;n
                                    <select name="status" class="ui-input mt-1">
                                        <option value="bot" @selected($selectedSupportConversation->status === 'bot')>Bot</option>
                                        <option value="waiting_agent" @selected($selectedSupportConversation->status === 'waiting_agent')>Esperando agente</option>
                                        <option value="active" @selected($selectedSupportConversation->status === 'active')>Activa</option>
                                        <option value="closed" @selected($selectedSupportConversation->status === 'closed')>Cerrada</option>
                                    </select>
                                </label>
                                <x-ui.button type="submit" variant="ghost">Actualizar estado</x-ui.button>
                            </form>

                            <form method="POST"
                                  action="{{ route('superadmin.support-chat.finalize', $selectedSupportConversation->id) }}"
                                  onsubmit="return confirm('Finalizar esta conversacion? El historial se borrara automaticamente en 15 minutos.');">
                                @csrf
                                <input type="hidden" name="status" value="{{ $filters['status'] }}">
                                <input type="hidden" name="q" value="{{ $filters['q'] }}">
                                <input type="hidden" name="support" value="{{ $selectedSupportConversation->id }}">
                                <input type="hidden" name="support_status" value="{{ $supportFilters['status'] ?? 'all' }}">
                                <input type="hidden" name="support_q" value="{{ $supportFilters['q'] ?? '' }}">
                                <input type="hidden" name="support_page" value="{{ request()->query('support_page', 1) }}">
                                <x-ui.button type="submit" variant="danger">Finalizar conversacion</x-ui.button>
                            </form>
                        </div>
                    </div>
                @else
                    <div class="py-14 text-center text-sm text-slate-500 dark:text-slate-300">
                        Selecciona una conversaci&oacute;n de soporte para ver el detalle.
                    </div>
                @endif
            </section>
        </div>

        <div class="mt-4">
            {{ $supportConversations->links() }}
        </div>
    </x-ui.card>
@endsection

@push('scripts')
    <script>
        (function () {
            const params = new URLSearchParams(window.location.search);
            if (!params.has('support')) {
                return;
            }

            const section = document.getElementById('support-chat-inbox');
            if (!(section instanceof HTMLElement)) {
                return;
            }

            section.scrollIntoView({ behavior: 'smooth', block: 'start' });

            const thread = section.querySelector('[data-support-thread]');
            if (thread instanceof HTMLElement) {
                thread.scrollTop = thread.scrollHeight;
            }
        })();
    </script>
@endpush
