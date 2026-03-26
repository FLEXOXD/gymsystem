@extends('layouts.panel')

@section('title', 'Bandeja de contacto web')
@section('page-title', 'Inbox de contacto y soporte')

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
            \App\Models\SupportChatConversation::STATUS_BOT => 'sa-status-chip is-neutral',
            \App\Models\SupportChatConversation::STATUS_WAITING_AGENT => 'sa-status-chip is-warning',
            \App\Models\SupportChatConversation::STATUS_ACTIVE => 'sa-status-chip is-success',
            \App\Models\SupportChatConversation::STATUS_CLOSED => 'sa-status-chip is-danger',
        ];
        $reviewedCount = max(0, (int) $totalCount - (int) $unreadCount);
    @endphp
    <div class="sa-shell">
        <section class="sa-hero">
            <div class="sa-hero-grid">
                <div>
                    <span class="sa-kicker">Inbox comercial</span>
                    <h2 class="sa-title">Mensajes web y soporte en un solo lugar, sin perder prioridad.</h2>
                    <p class="sa-subtitle">
                        Primero lees lo pendiente, luego bajas al detalle. Asi el inbox deja de sentirse pesado y gana jerarquia.
                    </p>
                    <div class="sa-actions">
                        <a href="#support-chat-inbox" class="ui-button ui-button-secondary">Ver soporte</a>
                    </div>
                </div>

                <aside class="sa-note-card">
                    <p class="sa-note-label">Reglas rapidas</p>
                    <div class="sa-note-list">
                        <div class="sa-note-item">
                            <strong>Inbox web 24h</strong>
                            <span>Los mensajes web se eliminan automaticamente despues de 24 horas.</span>
                        </div>
                        <div class="sa-note-item">
                            <strong>Chat con cierre automatico</strong>
                            <span>El soporte recuerda y cierra conversaciones inactivas.</span>
                        </div>
                        <div class="sa-note-item">
                            <strong>{{ $supportRepresentativeOnline ? $supportRepresentativeName.' conectado' : 'Sin representante activo' }}</strong>
                            <span>Estado actual del soporte para responder sin demora.</span>
                        </div>
                    </div>
                </aside>
            </div>
        </section>

        <section class="sa-stat-grid">
            <article class="sa-stat-card is-neutral">
                <p class="sa-stat-label">Mensajes web</p>
                <p class="sa-stat-value">{{ $totalCount }}</p>
                <p class="sa-stat-meta">Total visible dentro de la ventana activa.</p>
            </article>
            <article class="sa-stat-card is-warning">
                <p class="sa-stat-label">Sin leer</p>
                <p class="sa-stat-value">{{ $unreadCount }}</p>
                <p class="sa-stat-meta">{{ $reviewedCount }} revisados en la bandeja web.</p>
            </article>
            <article class="sa-stat-card is-info">
                <p class="sa-stat-label">Chats soporte</p>
                <p class="sa-stat-value">{{ $supportTotalCount }}</p>
                <p class="sa-stat-meta">Conversaciones registradas en el modulo de soporte.</p>
            </article>
            <article class="sa-stat-card is-success">
                <p class="sa-stat-label">Pendientes soporte</p>
                <p class="sa-stat-value">{{ $supportUnreadCount }}</p>
                <p class="sa-stat-meta">Casos con mensajes por revisar en soporte.</p>
            </article>
        </section>

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

        <div class="sa-toolbar mb-4">
            <div class="flex flex-wrap items-center justify-between gap-3">
            <div class="flex flex-wrap items-center gap-2 text-xs font-semibold uppercase tracking-wide">
                <span class="sa-pill is-neutral">
                    Total: {{ $totalCount }}
                </span>
                <span class="sa-pill is-success">
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
        </div>

        <div class="sa-split-shell">
            <aside class="sa-split-pane">
                <div class="sa-split-scroll max-h-[68vh] overflow-y-auto">
                    @forelse ($messages as $message)
                        @php
                            $fullName = trim($message->first_name.' '.$message->last_name);
                            $isActive = $selectedMessage && (int) $selectedMessage->id === (int) $message->id;
                            $isUnread = $message->read_at === null;
                            $expiresAt = $message->created_at?->copy()->addHours(\App\Models\LandingContactMessage::INBOX_RETENTION_HOURS);
                            $minutesLeft = $expiresAt ? max(0, now()->diffInMinutes($expiresAt, false)) : null;
                        @endphp
                        <a href="{{ route('superadmin.inbox.show', array_merge(['message' => $message->id], request()->only(['status', 'q', 'page']))) }}"
                           class="sa-split-item {{ $isActive ? 'is-active' : '' }}">
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
                        <div class="sa-empty-row">
                            No hay mensajes con este filtro.
                        </div>
                    @endforelse
                </div>
            </aside>

            <section class="sa-split-detail">
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
                            <div class="sa-action-row">
                                <form method="POST" action="{{ route('superadmin.inbox.read', $selectedMessage->id) }}">
                                    @csrf
                                    <x-ui.button type="submit" size="sm" variant="secondary">Marcar como leído</x-ui.button>
                                </form>
                            </div>
                        @else
                            <span class="sa-status-chip is-neutral">
                                Leído
                            </span>
                        @endif
                    </div>

                    <article class="sa-message-surface mt-4 whitespace-pre-wrap break-words">
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

        <div class="sa-toolbar mb-4">
            <div class="flex flex-wrap items-center justify-between gap-3">
            <div class="flex flex-wrap items-center gap-2 text-xs font-semibold uppercase tracking-wide">
                <span class="sa-pill is-neutral">
                    Total: {{ $supportTotalCount }}
                </span>
                <span class="sa-pill is-success">
                    Sin leer: {{ $supportUnreadCount }}
                </span>
                <span class="{{ $supportRepresentativeOnline ? 'sa-pill is-success' : 'sa-pill is-neutral' }}">
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
        </div>

        @if (($supportUnreadCount ?? 0) > 0 && ($supportTotalCount ?? 0) === 0)
            <div class="mb-4 rounded-xl border border-amber-300 bg-amber-50 px-4 py-3 text-xs font-semibold text-amber-900 dark:border-amber-500/30 dark:bg-amber-900/20 dark:text-amber-100">
                Se detectaron pendientes sin conversaciones visibles. Refresca la página y valida que las tablas de soporte estén actualizadas.
            </div>
        @endif

        <div class="sa-split-shell support-chat-grid">
            <aside class="support-chat-sidebar sa-split-pane">
                <div class="support-chat-list-scroll max-h-[70vh] overflow-y-auto">
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
                            $statusClass = $supportStatusBadgeClasses[$conversation->status] ?? 'sa-status-chip is-neutral';
                            $supportLinkQuery = array_merge(
                                request()->only(['status', 'q', 'page', 'support_status', 'support_q', 'support_page']),
                                ['support' => $conversation->id]
                            );
                        @endphp
                        <a href="{{ route('superadmin.inbox.index', $supportLinkQuery) }}"
                           class="support-chat-list-item sa-split-item {{ $conversationActive ? 'is-active' : '' }}">
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
                                    <p class="mt-0.5 text-xs text-slate-500 dark:text-slate-300">{{ $conversation->sourceLabel() }} - {{ $conversation->requesterLabel() }}</p>
                                    <p class="mt-1 line-clamp-2 text-xs text-slate-600 dark:text-slate-300">{{ \Illuminate\Support\Str::limit((string) ($conversation->latestMessage?->message ?? 'Sin mensajes'), 110) }}</p>
                                    <div class="mt-1 flex items-center justify-between gap-2">
                                        <span class="{{ $statusClass }}">
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
                        <div class="sa-empty-row">
                            A&uacute;n no hay conversaciones de soporte.
                        </div>
                    @endforelse
                </div>
            </aside>

            <section class="support-chat-detail sa-split-detail"
                     @if ($selectedSupportConversation)
                         data-support-detail
                         data-support-state-url="{{ route('superadmin.support-chat.state', $selectedSupportConversation->id) }}"
                     @endif>
                @if ($selectedSupportConversation)
                    @php
                        $selectedSupportStatusClass = $supportStatusBadgeClasses[$selectedSupportConversation->status] ?? 'sa-status-chip is-neutral';
                    @endphp
                    <div class="support-chat-detail__header flex flex-wrap items-start justify-between gap-3 border-b border-slate-200 pb-3 dark:border-slate-700">
                        <div>
                            <h3 class="text-lg font-black text-slate-900 dark:text-slate-100" data-support-title>{{ $selectedSupportConversation->displayName() }}</h3>
                            <p class="text-sm text-slate-600 dark:text-slate-300" data-support-source-label>
                                {{ $selectedSupportConversation->sourceLabel() }} - {{ $selectedSupportConversation->requesterLabel() }}
                            </p>
                            @if (trim((string) $selectedSupportConversation->visitor_email) !== '')
                                <p class="text-xs text-slate-500 dark:text-slate-400" data-support-email>{{ $selectedSupportConversation->visitor_email }}</p>
                            @endif
                            @if (trim((string) $selectedSupportConversation->visitor_name) !== '')
                                <p class="text-xs text-slate-500 dark:text-slate-400" data-support-contact-name>Contacto: {{ $selectedSupportConversation->visitor_name }}</p>
                            @endif
                        </div>
                        <span data-support-status-badge
                              data-support-status="{{ $selectedSupportConversation->status }}"
                              class="{{ $selectedSupportStatusClass }}">
                            {{ $selectedSupportConversation->statusLabel() }}
                        </span>
                    </div>

                    <div data-support-thread class="support-chat-thread mt-4 max-h-[46vh] space-y-3 overflow-y-auto rounded-xl border border-slate-200 bg-slate-50 p-3 dark:border-slate-700 dark:bg-slate-800/40">
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
                            <div class="support-thread-row {{ $isMine ? 'is-mine' : '' }}">
                                <article class="support-thread-bubble {{ $isSystem ? 'is-system' : ($isMine ? 'is-mine' : 'is-other') }}">
                                    <p class="support-thread-bubble__label">{{ $senderLabel }}</p>
                                    <p class="support-thread-bubble__text">{{ $chatMessage->message }}</p>
                                    <p class="support-thread-bubble__time">
                                        {{ $chatMessage->created_at?->copy()->timezone($panelTimezone)?->format('d/m/Y H:i') }}
                                    </p>
                                </article>
                            </div>
                        @empty
                            <div class="support-chat-thread__empty py-10 text-center text-sm text-slate-500 dark:text-slate-300">
                                A&uacute;n no hay mensajes en esta conversaci&oacute;n.
                            </div>
                        @endforelse
                    </div>

                    <p data-support-feedback class="support-chat-feedback mt-3">
                        Las respuestas nuevas se actualizan sin recargar la pagina.
                    </p>

                    <div class="support-chat-controls mt-4 grid gap-3 lg:grid-cols-[minmax(0,1fr)_220px]">
                        <form method="POST"
                              action="{{ route('superadmin.support-chat.reply', $selectedSupportConversation->id) }}"
                              class="space-y-2"
                              data-support-reply-form>
                            @csrf
                            <label class="text-xs font-bold uppercase tracking-wide text-slate-500 dark:text-slate-400">
                                Responder como SuperAdmin
                                <textarea name="message"
                                          rows="3"
                                          class="ui-input mt-1"
                                          placeholder="Escribe tu respuesta..."
                                          data-support-reply-input
                                          required></textarea>
                            </label>
                            <x-ui.button type="submit" variant="secondary" data-support-reply-submit>Enviar respuesta</x-ui.button>
                        </form>

                        <div class="space-y-2">
                            <form method="POST"
                                  action="{{ route('superadmin.support-chat.status', $selectedSupportConversation->id) }}"
                                  class="space-y-2"
                                  data-support-status-form>
                                @csrf
                                <label class="text-xs font-bold uppercase tracking-wide text-slate-500 dark:text-slate-400">
                                    Estado de la conversaci&oacute;n
                                    <select name="status" class="ui-input mt-1" data-support-status-select>
                                        <option value="bot" @selected($selectedSupportConversation->status === 'bot')>Bot</option>
                                        <option value="waiting_agent" @selected($selectedSupportConversation->status === 'waiting_agent')>Esperando agente</option>
                                        <option value="active" @selected($selectedSupportConversation->status === 'active')>Activa</option>
                                        <option value="closed" @selected($selectedSupportConversation->status === 'closed')>Cerrada</option>
                                    </select>
                                </label>
                                <x-ui.button type="submit" variant="ghost" data-support-status-submit>Actualizar estado</x-ui.button>
                            </form>

                            <form method="POST"
                                  action="{{ route('superadmin.support-chat.finalize', $selectedSupportConversation->id) }}"
                                  data-support-finalize-form>
                                @csrf
                                <input type="hidden" name="status" value="{{ $filters['status'] }}">
                                <input type="hidden" name="q" value="{{ $filters['q'] }}">
                                <input type="hidden" name="support" value="{{ $selectedSupportConversation->id }}">
                                <input type="hidden" name="support_status" value="{{ $supportFilters['status'] ?? 'all' }}">
                                <input type="hidden" name="support_q" value="{{ $supportFilters['q'] ?? '' }}">
                                <input type="hidden" name="support_page" value="{{ request()->query('support_page', 1) }}">
                                <x-ui.button type="submit" variant="danger" data-support-finalize-submit>Finalizar conversacion</x-ui.button>
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
    </div>
@endsection

@push('styles')
    <style>
        #support-chat-inbox .support-chat-grid {
            display: grid;
            grid-template-columns: minmax(0, 1fr);
            gap: 1rem;
            align-items: stretch;
        }

        #support-chat-inbox .support-chat-sidebar,
        #support-chat-inbox .support-chat-detail {
            position: relative;
            overflow: hidden;
        }

        #support-chat-inbox .support-chat-detail {
            display: flex;
            flex-direction: column;
        }

        #support-chat-inbox .support-chat-list-scroll {
            max-height: min(72vh, 920px);
            overflow-y: auto;
        }

        #support-chat-inbox .support-chat-list-item {
            transition: background-color 0.18s ease, border-color 0.18s ease, transform 0.18s ease;
        }

        #support-chat-inbox .support-chat-list-item:hover {
            transform: translateY(-1px);
        }

        #support-chat-inbox .support-chat-thread {
            max-height: min(48vh, 620px);
            overflow-y: auto;
            scrollbar-width: thin;
            scrollbar-color: rgba(59, 130, 246, 0.4) transparent;
        }

        #support-chat-inbox .support-chat-controls {
            display: grid;
            grid-template-columns: minmax(0, 1fr);
            gap: 0.9rem;
        }

        #support-chat-inbox .support-chat-thread::-webkit-scrollbar,
        #support-chat-inbox .support-chat-list-scroll::-webkit-scrollbar {
            width: 9px;
        }

        #support-chat-inbox .support-chat-thread::-webkit-scrollbar-thumb,
        #support-chat-inbox .support-chat-list-scroll::-webkit-scrollbar-thumb {
            border-radius: 999px;
            background: rgba(59, 130, 246, 0.32);
        }

        #support-chat-inbox .support-thread-row {
            display: flex;
            justify-content: flex-start;
        }

        #support-chat-inbox .support-thread-row.is-mine {
            justify-content: flex-end;
        }

        #support-chat-inbox .support-thread-bubble {
            max-width: 84%;
            border-radius: 18px;
            padding: 0.8rem 0.92rem;
            border: 1px solid transparent;
            box-shadow: 0 16px 28px rgba(15, 23, 42, 0.08);
        }

        #support-chat-inbox .support-thread-bubble__label {
            margin: 0;
            font-size: 11px;
            font-weight: 900;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        #support-chat-inbox .support-thread-bubble__text {
            margin: 0.45rem 0 0;
            white-space: pre-wrap;
            word-break: break-word;
            line-height: 1.55;
            font-size: 0.95rem;
        }

        #support-chat-inbox .support-thread-bubble__time {
            margin: 0.55rem 0 0;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.04em;
            text-transform: uppercase;
            opacity: 0.84;
        }

        #support-chat-inbox .support-thread-bubble.is-other {
            background: #ffffff;
            border-color: #d7e6ff;
            color: #0f172a;
        }

        #support-chat-inbox .support-thread-bubble.is-system {
            background: #eef4ff;
            border-color: #c5d7ff;
            color: #1e3a8a;
        }

        #support-chat-inbox .support-thread-bubble.is-mine {
            background: linear-gradient(135deg, #0f6edc 0%, #1d4ed8 100%);
            color: #f8fbff;
        }

        #support-chat-inbox .support-chat-feedback {
            min-height: 1.3rem;
            font-size: 0.78rem;
            font-weight: 700;
            color: #1d4ed8;
        }

        #support-chat-inbox .support-chat-feedback.is-error {
            color: #dc2626;
        }

        #support-chat-inbox .support-status-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 999px;
            padding: 0.32rem 0.72rem;
            font-size: 0.72rem;
            font-weight: 800;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            border: 1px solid transparent;
        }

        #support-chat-inbox .support-status-badge.is-bot {
            background: #e2e8f0;
            color: #334155;
            border-color: #cbd5e1;
        }

        #support-chat-inbox .support-status-badge.is-waiting {
            background: #fef3c7;
            color: #92400e;
            border-color: #fcd34d;
        }

        #support-chat-inbox .support-status-badge.is-active {
            background: #dcfce7;
            color: #166534;
            border-color: #86efac;
        }

        #support-chat-inbox .support-status-badge.is-closed {
            background: #ffe4e6;
            color: #be123c;
            border-color: #fda4af;
        }

        .theme-light #support-chat-inbox .support-chat-detail,
        .theme-light #support-chat-inbox .support-chat-sidebar {
            background: linear-gradient(180deg, #ffffff 0%, #f8fbff 100%);
        }

        .theme-light #support-chat-inbox .support-chat-list-item.is-active {
            background: linear-gradient(135deg, rgba(219, 234, 254, 0.9), rgba(239, 246, 255, 0.96));
            border-left: 3px solid #0f6edc;
            padding-left: calc(1rem - 3px);
        }

        .theme-light #support-chat-inbox .support-chat-thread {
            background: linear-gradient(180deg, #f7fbff 0%, #eef5ff 100%);
            border-color: #cfe0ff;
        }

        .theme-dark #support-chat-inbox .support-chat-sidebar,
        .theme-dark #support-chat-inbox .support-chat-detail {
            border-color: rgba(96, 165, 250, 0.18);
            background: linear-gradient(180deg, rgba(15, 23, 42, 0.94), rgba(15, 23, 42, 0.86));
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.03);
        }

        .theme-dark #support-chat-inbox .support-chat-detail__header {
            border-bottom-color: rgba(96, 165, 250, 0.16);
        }

        .theme-dark #support-chat-inbox .support-chat-list-item {
            border-color: rgba(148, 163, 184, 0.1);
        }

        .theme-dark #support-chat-inbox .support-chat-list-item:hover {
            background: rgba(30, 41, 59, 0.82);
        }

        .theme-dark #support-chat-inbox .support-chat-list-item.is-active {
            background: linear-gradient(135deg, rgba(29, 78, 216, 0.28), rgba(15, 23, 42, 0.92));
            border-left: 3px solid rgba(34, 211, 238, 0.9);
            padding-left: calc(1rem - 3px);
        }

        .theme-dark #support-chat-inbox .support-chat-thread {
            background: linear-gradient(180deg, rgba(15, 23, 42, 0.72), rgba(15, 23, 42, 0.95));
            border-color: rgba(96, 165, 250, 0.18);
        }

        .theme-dark #support-chat-inbox .support-thread-bubble {
            box-shadow: 0 16px 32px rgba(2, 8, 23, 0.28);
        }

        .theme-dark #support-chat-inbox .support-thread-bubble.is-other {
            background: rgba(15, 23, 42, 0.96);
            border-color: rgba(100, 116, 139, 0.42);
            color: #e2e8f0;
        }

        .theme-dark #support-chat-inbox .support-thread-bubble.is-system {
            background: rgba(37, 99, 235, 0.12);
            border-color: rgba(96, 165, 250, 0.34);
            color: #dbeafe;
        }

        .theme-dark #support-chat-inbox .support-thread-bubble.is-mine {
            background: linear-gradient(135deg, #0284c7 0%, #2563eb 100%);
            color: #f8fbff;
        }

        .theme-dark #support-chat-inbox .support-chat-feedback {
            color: #7dd3fc;
        }

        .theme-dark #support-chat-inbox .support-chat-feedback.is-error {
            color: #fda4af;
        }

        .theme-dark #support-chat-inbox .support-status-badge.is-bot {
            background: rgba(51, 65, 85, 0.7);
            color: #e2e8f0;
            border-color: rgba(148, 163, 184, 0.24);
        }

        .theme-dark #support-chat-inbox .support-status-badge.is-waiting {
            background: rgba(245, 158, 11, 0.18);
            color: #fde68a;
            border-color: rgba(245, 158, 11, 0.34);
        }

        .theme-dark #support-chat-inbox .support-status-badge.is-active {
            background: rgba(16, 185, 129, 0.18);
            color: #86efac;
            border-color: rgba(52, 211, 153, 0.32);
        }

        .theme-dark #support-chat-inbox .support-status-badge.is-closed {
            background: rgba(244, 63, 94, 0.18);
            color: #fda4af;
            border-color: rgba(251, 113, 133, 0.32);
        }

        @media (min-width: 1100px) {
            #support-chat-inbox .support-chat-grid {
                grid-template-columns: minmax(320px, 0.78fr) minmax(0, 1.22fr);
            }

            #support-chat-inbox .support-chat-controls {
                grid-template-columns: minmax(0, 1fr) 220px;
                align-items: start;
            }
        }

        @media (max-width: 1023px) {
            #support-chat-inbox .support-chat-thread {
                max-height: 58vh;
            }
        }
    </style>
@endpush

@push('scripts')
    <script>
        (function () {
            const params = new URLSearchParams(window.location.search);
            const section = document.getElementById('support-chat-inbox');
            if (!(section instanceof HTMLElement)) {
                return;
            }

            const thread = section.querySelector('[data-support-thread]');
            if (params.has('support')) {
                section.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
            if (thread instanceof HTMLElement) {
                thread.scrollTop = thread.scrollHeight;
            }

            const detail = section.querySelector('[data-support-detail]');
            if (!(detail instanceof HTMLElement) || !(thread instanceof HTMLElement)) {
                return;
            }

            const stateUrl = String(detail.getAttribute('data-support-state-url') || '').trim();
            const replyForm = detail.querySelector('[data-support-reply-form]');
            const replyInput = detail.querySelector('[data-support-reply-input]');
            const replyButton = detail.querySelector('[data-support-reply-submit]');
            const statusForm = detail.querySelector('[data-support-status-form]');
            const statusSelect = detail.querySelector('[data-support-status-select]');
            const statusButton = detail.querySelector('[data-support-status-submit]');
            const finalizeForm = detail.querySelector('[data-support-finalize-form]');
            const finalizeButton = detail.querySelector('[data-support-finalize-submit]');
            const feedback = detail.querySelector('[data-support-feedback]');
            const statusBadge = detail.querySelector('[data-support-status-badge]');
            const title = detail.querySelector('[data-support-title]');
            const sourceLabel = detail.querySelector('[data-support-source-label]');
            const emailLabel = detail.querySelector('[data-support-email]');
            const contactNameLabel = detail.querySelector('[data-support-contact-name]');
            const csrfMeta = document.querySelector('meta[name="csrf-token"]');
            const csrfToken = csrfMeta ? String(csrfMeta.getAttribute('content') || '').trim() : '';
            const pollingMs = 5000;
            const finalizeMessage = 'Finalizar esta conversacion y borrar todo su historial ahora?';
            const statusClassMap = {
                bot: 'sa-status-chip is-neutral',
                waiting_agent: 'sa-status-chip is-warning',
                active: 'sa-status-chip is-success',
                closed: 'sa-status-chip is-danger',
            };

            let pollTimer = null;
            let actionInFlight = false;
            let latestSignature = '';
            let currentStatus = statusBadge instanceof HTMLElement
                ? String(statusBadge.getAttribute('data-support-status') || '').trim()
                : '';

            function setFeedback(message, isError) {
                if (!(feedback instanceof HTMLElement)) {
                    return;
                }

                feedback.textContent = String(message || '').trim();
                feedback.classList.toggle('is-error', Boolean(isError));
            }

            function isNearBottom() {
                return (thread.scrollHeight - thread.scrollTop - thread.clientHeight) < 96;
            }

            function scrollToBottom() {
                thread.scrollTop = thread.scrollHeight;
            }

            function syncControls() {
                const isClosed = currentStatus === 'closed';

                if (replyInput instanceof HTMLTextAreaElement) {
                    replyInput.disabled = actionInFlight;
                    replyInput.placeholder = isClosed
                        ? 'Conversacion cerrada. Escribe para reactivarla.'
                        : 'Escribe tu respuesta...';
                }
                if (replyButton instanceof HTMLButtonElement) {
                    replyButton.disabled = actionInFlight;
                }
                if (statusSelect instanceof HTMLSelectElement) {
                    statusSelect.disabled = actionInFlight;
                }
                if (statusButton instanceof HTMLButtonElement) {
                    statusButton.disabled = actionInFlight;
                }
                if (finalizeButton instanceof HTMLButtonElement) {
                    finalizeButton.disabled = actionInFlight;
                }

                detail.classList.toggle('is-closed', isClosed);
            }

            function requestHeaders(includeContentType) {
                const nextHeaders = {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                };

                if (csrfToken !== '') {
                    nextHeaders['X-CSRF-TOKEN'] = csrfToken;
                }
                if (includeContentType) {
                    nextHeaders['Content-Type'] = 'application/json';
                }

                return nextHeaders;
            }

            async function requestJson(url, method, payload) {
                const response = await fetch(url, {
                    method: method,
                    credentials: 'same-origin',
                    cache: 'no-store',
                    headers: requestHeaders(method !== 'GET'),
                    body: method === 'GET' ? null : JSON.stringify(payload || {}),
                });

                let data = null;
                const contentType = String(response.headers.get('content-type') || '').toLowerCase();
                if (contentType.includes('application/json')) {
                    data = await response.json();
                }

                if (!response.ok) {
                    let message = 'No se pudo procesar la solicitud.';
                    if (data && typeof data.message === 'string' && data.message.trim() !== '') {
                        message = data.message.trim();
                    } else if (data && data.errors && typeof data.errors === 'object') {
                        const firstErrorGroup = Object.values(data.errors)[0];
                        if (Array.isArray(firstErrorGroup) && typeof firstErrorGroup[0] === 'string') {
                            message = firstErrorGroup[0];
                        }
                    }

                    const error = new Error(message);
                    error.status = response.status;
                    throw error;
                }

                return data;
            }

            function updateBadge(status, label) {
                if (!(statusBadge instanceof HTMLElement)) {
                    return;
                }

                const normalizedStatus = String(status || '').trim();
                statusBadge.className = statusClassMap[normalizedStatus] || statusClassMap.bot;
                statusBadge.textContent = String(label || '').trim();
                statusBadge.setAttribute('data-support-status', normalizedStatus);
                currentStatus = normalizedStatus;
            }

            function renderMessages(messages, forceScroll) {
                const list = Array.isArray(messages) ? messages : [];
                const signature = list.map(function (message) {
                    return String(message && message.id ? message.id : '');
                }).join(':');

                if (signature === latestSignature) {
                    return;
                }

                const shouldStickBottom = Boolean(forceScroll) || isNearBottom();
                thread.innerHTML = '';

                if (list.length === 0) {
                    const emptyState = document.createElement('div');
                    emptyState.className = 'support-chat-thread__empty py-10 text-center text-sm text-slate-500 dark:text-slate-300';
                    emptyState.textContent = 'Aun no hay mensajes en esta conversacion.';
                    thread.appendChild(emptyState);
                    latestSignature = signature;
                    return;
                }

                list.forEach(function (message) {
                    const row = document.createElement('div');
                    row.className = 'support-thread-row';
                    if (message && message.mine) {
                        row.classList.add('is-mine');
                    }

                    const bubble = document.createElement('article');
                    if (message && message.is_system) {
                        bubble.className = 'support-thread-bubble is-system';
                    } else if (message && message.mine) {
                        bubble.className = 'support-thread-bubble is-mine';
                    } else {
                        bubble.className = 'support-thread-bubble is-other';
                    }

                    const label = document.createElement('p');
                    label.className = 'support-thread-bubble__label';
                    label.textContent = String(message && message.sender_label ? message.sender_label : '');
                    bubble.appendChild(label);

                    const text = document.createElement('p');
                    text.className = 'support-thread-bubble__text';
                    text.textContent = String(message && message.message ? message.message : '');
                    bubble.appendChild(text);

                    const time = document.createElement('p');
                    time.className = 'support-thread-bubble__time';
                    time.textContent = String(message && message.created_at ? message.created_at : '');
                    bubble.appendChild(time);

                    row.appendChild(bubble);
                    thread.appendChild(row);
                });

                latestSignature = signature;
                if (shouldStickBottom) {
                    window.requestAnimationFrame(scrollToBottom);
                }
            }

            function applyPayload(payload, forceScroll) {
                if (!payload || typeof payload !== 'object' || !payload.conversation) {
                    setFeedback('No se pudo actualizar la conversacion.', true);
                    return;
                }

                const conversation = payload.conversation;
                const status = String(conversation.status || '').trim();
                const statusLabel = String(conversation.status_label || '').trim();

                if (title instanceof HTMLElement) {
                    title.textContent = String(conversation.display_name || '').trim();
                }
                if (sourceLabel instanceof HTMLElement) {
                    sourceLabel.textContent = String(conversation.source_label || '').trim() + ' - ' + String(conversation.requester_label || '').trim();
                }
                if (emailLabel instanceof HTMLElement) {
                    emailLabel.textContent = String(conversation.visitor_email || '').trim();
                }
                if (contactNameLabel instanceof HTMLElement) {
                    const contactName = String(conversation.visitor_name || '').trim();
                    contactNameLabel.textContent = contactName === '' ? '' : 'Contacto: ' + contactName;
                }
                if (statusSelect instanceof HTMLSelectElement && status !== '') {
                    statusSelect.value = status;
                }

                updateBadge(status, statusLabel);
                renderMessages(payload.messages || [], forceScroll);

                if (status === 'closed') {
                    const closedNotice = String(payload.notice || '').trim();
                    setFeedback(closedNotice !== '' ? closedNotice : 'Conversacion cerrada. Puedes responder para reactivarla.', false);
                } else if (typeof payload.notice === 'string' && payload.notice.trim() !== '') {
                    setFeedback(payload.notice.trim(), false);
                } else if (status === 'active') {
                    setFeedback('Chat activo con el cliente. Las respuestas entran sin recargar.', false);
                } else if (status === 'waiting_agent') {
                    setFeedback('Conversacion en espera de agente. Se seguira actualizando sola.', false);
                } else {
                    setFeedback('Las respuestas nuevas se actualizan sin recargar la pagina.', false);
                }

                syncControls();
            }

            async function loadState(forceScroll) {
                if (actionInFlight || stateUrl === '') {
                    return;
                }

                try {
                    const payload = await requestJson(stateUrl, 'GET');
                    applyPayload(payload, forceScroll);
                } catch (error) {
                    if (error && error.status === 404) {
                        setFeedback('La conversacion ya no existe o fue eliminada.', true);
                        if (pollTimer) {
                            window.clearInterval(pollTimer);
                            pollTimer = null;
                        }
                        return;
                    }

                    setFeedback(error instanceof Error ? error.message : 'No se pudo actualizar la conversacion.', true);
                }
            }

            async function handleReplySubmit(event) {
                event.preventDefault();
                if (!(replyForm instanceof HTMLFormElement) || !(replyInput instanceof HTMLTextAreaElement)) {
                    return;
                }

                const message = String(replyInput.value || '').trim();
                if (message === '') {
                    replyInput.focus();
                    return;
                }

                actionInFlight = true;
                syncControls();
                setFeedback('Enviando respuesta...', false);

                try {
                    const payload = await requestJson(replyForm.action, 'POST', { message: message });
                    replyInput.value = '';
                    applyPayload(payload, true);
                } catch (error) {
                    setFeedback(error instanceof Error ? error.message : 'No se pudo enviar la respuesta.', true);
                } finally {
                    actionInFlight = false;
                    syncControls();
                }
            }

            async function handleStatusSubmit(event) {
                event.preventDefault();
                if (!(statusForm instanceof HTMLFormElement) || !(statusSelect instanceof HTMLSelectElement)) {
                    return;
                }

                actionInFlight = true;
                syncControls();
                setFeedback('Actualizando estado...', false);

                try {
                    const payload = await requestJson(statusForm.action, 'POST', { status: statusSelect.value });
                    applyPayload(payload, false);
                } catch (error) {
                    setFeedback(error instanceof Error ? error.message : 'No se pudo actualizar el estado.', true);
                } finally {
                    actionInFlight = false;
                    syncControls();
                }
            }

            async function handleFinalizeSubmit(event) {
                event.preventDefault();
                if (!(finalizeForm instanceof HTMLFormElement)) {
                    return;
                }

                if (!window.confirm(finalizeMessage)) {
                    return;
                }

                actionInFlight = true;
                syncControls();
                setFeedback('Finalizando conversacion...', false);

                try {
                    const payload = await requestJson(finalizeForm.action, 'POST', {});
                    if (payload && payload.deleted) {
                        setFeedback(String(payload.notice || 'Conversacion finalizada y eliminada con su historial.').trim(), false);

                        const redirectUrl = String(payload.redirect_url || '').trim();
                        if (redirectUrl !== '') {
                            window.location.assign(redirectUrl);
                            return;
                        }

                        const nextUrl = new URL(window.location.href);
                        nextUrl.searchParams.delete('support');
                        window.location.assign(nextUrl.toString());
                        return;
                    }
                    applyPayload(payload, true);
                } catch (error) {
                    setFeedback(error instanceof Error ? error.message : 'No se pudo finalizar la conversacion.', true);
                } finally {
                    actionInFlight = false;
                    syncControls();
                }
            }

            if (replyForm instanceof HTMLFormElement) {
                replyForm.addEventListener('submit', handleReplySubmit);
            }
            if (statusForm instanceof HTMLFormElement) {
                statusForm.addEventListener('submit', handleStatusSubmit);
            }
            if (finalizeForm instanceof HTMLFormElement) {
                finalizeForm.addEventListener('submit', handleFinalizeSubmit);
            }
            if (replyInput instanceof HTMLTextAreaElement) {
                replyInput.addEventListener('keydown', function (event) {
                    if ((event.ctrlKey || event.metaKey) && event.key === 'Enter') {
                        event.preventDefault();
                        if (replyForm instanceof HTMLFormElement) {
                            replyForm.requestSubmit();
                        }
                    }
                });
            }

            syncControls();
            loadState(true);

            pollTimer = window.setInterval(function () {
                if (document.hidden) {
                    return;
                }

                loadState(false);
            }, pollingMs);

            document.addEventListener('visibilitychange', function () {
                if (!document.hidden) {
                    loadState(false);
                }
            });

            window.addEventListener('beforeunload', function () {
                if (pollTimer) {
                    window.clearInterval(pollTimer);
                    pollTimer = null;
                }
            });
        })();
    </script>
@endpush
