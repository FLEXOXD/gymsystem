@extends('layouts.panel')

@section('title', 'Sugerencias')
@section('page-title', 'Sugerencias de gimnasios')

@section('content')
    @php
        $suggestionItems = method_exists($suggestions, 'getCollection') ? $suggestions->getCollection() : collect($suggestions);
        $suggestionTotal = method_exists($suggestions, 'total') ? $suggestions->total() : $suggestionItems->count();
        $pendingSuggestionCount = $suggestionItems->where('status', 'pending')->count();
        $reviewedSuggestionCount = $suggestionItems->where('status', 'reviewed')->count();
        $reactivationSuggestionCount = $suggestionItems
            ->filter(fn ($suggestion) => mb_strtolower(trim((string) ($suggestion->subject ?? ''))) === 'solicitud de reactivacion de suscripcion')
            ->count();
    @endphp
    <div class="sa-shell">
        <section class="sa-hero">
            <div class="sa-hero-grid">
                <div>
                    <span class="sa-kicker">Feedback de gimnasios</span>
                    <h2 class="sa-title">Sugerencias y reactivaciones en una bandeja mas util para decidir rapido.</h2>
                    <p class="sa-subtitle">
                        Filtras por estado, gimnasio o texto y ves de inmediato que requiere respuesta, comprobante o revision interna.
                    </p>
                </div>

                <aside class="sa-note-card">
                    <p class="sa-note-label">Lectura rapida</p>
                    <div class="sa-note-list">
                        <div class="sa-note-item">
                            <strong>Pendientes primero</strong>
                            <span>Las solicitudes por atender quedan visibles con su estado.</span>
                        </div>
                        <div class="sa-note-item">
                            <strong>Reactivaciones detectadas</strong>
                            <span>Las peticiones de reactivacion se distinguen rapido dentro del listado.</span>
                        </div>
                        <div class="sa-note-item">
                            <strong>Comprobante cuando exista</strong>
                            <span>Si llega evidencia, la tabla deja el acceso directo al archivo.</span>
                        </div>
                    </div>
                </aside>
            </div>
        </section>

        <section class="sa-stat-grid">
            <article class="sa-stat-card is-neutral">
                <p class="sa-stat-label">Total sugerencias</p>
                <p class="sa-stat-value">{{ $suggestionTotal }}</p>
                <p class="sa-stat-meta">Registros visibles bajo el filtro actual.</p>
            </article>
            <article class="sa-stat-card is-warning">
                <p class="sa-stat-label">Pendientes</p>
                <p class="sa-stat-value">{{ $pendingSuggestionCount }}</p>
                <p class="sa-stat-meta">Casos por revisar en esta pagina.</p>
            </article>
            <article class="sa-stat-card is-success">
                <p class="sa-stat-label">Revisadas</p>
                <p class="sa-stat-value">{{ $reviewedSuggestionCount }}</p>
                <p class="sa-stat-meta">Registros ya atendidos en la vista actual.</p>
            </article>
            <article class="sa-stat-card is-info">
                <p class="sa-stat-label">Reactivaciones</p>
                <p class="sa-stat-value">{{ $reactivationSuggestionCount }}</p>
                <p class="sa-stat-meta">Solicitudes de reactivacion detectadas en esta pagina.</p>
            </article>
        </section>
    <x-ui.card title="Sugerencias de gimnasios" subtitle="Bandeja de mejoras y solicitudes de reactivacion enviadas por gimnasios.">
        <form method="GET" action="{{ route('superadmin.suggestions.index') }}" class="mb-4 flex flex-wrap items-end gap-3">
            <label class="text-sm font-semibold ui-muted">
                Estado
                <select name="status" class="ui-input mt-1 block min-w-[170px]">
                    <option value="pending" @selected(($filters['status'] ?? 'pending') === 'pending')>Pendientes</option>
                    <option value="reviewed" @selected(($filters['status'] ?? '') === 'reviewed')>Revisadas</option>
                    <option value="all" @selected(($filters['status'] ?? '') === 'all')>Todas</option>
                </select>
            </label>

            <label class="text-sm font-semibold ui-muted">
                Gimnasio
                <select name="gym_id" class="ui-input mt-1 block min-w-[220px]">
                    <option value="">Todos</option>
                    @foreach ($gyms as $gym)
                        <option value="{{ $gym->id }}" @selected((int) ($filters['gym_id'] ?? 0) === (int) $gym->id)>{{ $gym->name }}</option>
                    @endforeach
                </select>
            </label>

            <label class="text-sm font-semibold ui-muted">
                Buscar
                <input type="text" name="q" value="{{ $filters['q'] ?? '' }}" class="ui-input mt-1 block min-w-[260px]" placeholder="Asunto, mensaje, gym o usuario">
            </label>

            <x-ui.button type="submit" variant="secondary">Aplicar</x-ui.button>
        </form>

        <div class="mb-3 text-sm text-slate-600 dark:text-slate-300">
            Total: <strong>{{ $suggestions->total() }}</strong>
        </div>

        <div class="sa-table-shell overflow-x-auto">
            <table class="ui-table min-w-[1180px]">
                <thead>
                    <tr class="border-b border-slate-200 bg-slate-50 text-left text-xs uppercase tracking-wider text-slate-500 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300">
                        <th class="px-3 py-3">Fecha</th>
                        <th class="px-3 py-3">Gimnasio</th>
                        <th class="px-3 py-3">Enviado por</th>
                        <th class="px-3 py-3">Asunto</th>
                        <th class="px-3 py-3">Sugerencia</th>
                        <th class="px-3 py-3">Estado</th>
                        <th class="px-3 py-3">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($suggestions as $suggestion)
                        @php
                            $isPending = $suggestion->status === 'pending';
                            $isReactivationRequest = mb_strtolower(trim((string) $suggestion->subject)) === 'solicitud de reactivacion de suscripcion';
                            $message = (string) $suggestion->message;
                            $displayMessage = $message;
                            $receiptUrl = null;
                            $receiptPath = null;
                            if (preg_match('/Ruta interna:\s*([^\r\n]+)/u', $message, $pathMatch)) {
                                $rawPath = trim((string) ($pathMatch[1] ?? ''));
                                if ($rawPath !== '') {
                                    $normalizedPath = ltrim($rawPath, '/');
                                    if (\Illuminate\Support\Str::startsWith($normalizedPath, 'storage/')) {
                                        $normalizedPath = ltrim(\Illuminate\Support\Str::after($normalizedPath, 'storage/'), '/');
                                    }
                                    if ($normalizedPath !== '') {
                                        $receiptPath = $normalizedPath;
                                    }
                                }
                            }
                            if ($receiptPath === null && preg_match('/Comprobante:\s*(https?:\/\/\S+|\/storage\/\S+)/u', $message, $receiptMatch)) {
                                $rawReceiptUrl = trim((string) ($receiptMatch[1] ?? ''));
                                if (\Illuminate\Support\Str::startsWith($rawReceiptUrl, '/storage/')) {
                                    $receiptPath = ltrim(\Illuminate\Support\Str::after($rawReceiptUrl, '/storage/'), '/');
                                } elseif (filter_var($rawReceiptUrl, FILTER_VALIDATE_URL)) {
                                    $parsedPath = parse_url($rawReceiptUrl, PHP_URL_PATH);
                                    $parsedPath = is_string($parsedPath) ? trim($parsedPath) : '';
                                    if ($parsedPath !== '' && \Illuminate\Support\Str::startsWith($parsedPath, '/storage/')) {
                                        $receiptPath = ltrim(\Illuminate\Support\Str::after($parsedPath, '/storage/'), '/');
                                    }
                                }
                            }
                            if ($receiptPath !== null && $receiptPath !== '') {
                                $receiptUrl = url('/storage/'.$receiptPath);
                            } elseif (preg_match('/Comprobante:\s*(https?:\/\/\S+|\/storage\/\S+)/u', $message, $receiptMatch)) {
                                $rawReceiptUrl = trim((string) ($receiptMatch[1] ?? ''));
                                if (\Illuminate\Support\Str::startsWith($rawReceiptUrl, '/storage/')) {
                                    $receiptUrl = url($rawReceiptUrl);
                                } else {
                                    $receiptUrl = $rawReceiptUrl !== '' ? $rawReceiptUrl : null;
                                }
                            }
                            if (is_string($receiptUrl) && $receiptUrl !== '') {
                                $normalizedLine = 'Comprobante: '.$receiptUrl;
                                $displayMessage = preg_replace('/Comprobante:\s*(https?:\/\/\S+|\/storage\/\S+)/u', $normalizedLine, $message, 1) ?? $message;
                            }
                            $statusClass = $isPending
                                ? 'sa-status-chip is-warning'
                                : 'sa-status-chip is-success';
                        @endphp
                        <tr>
                            <td class="whitespace-nowrap dark:text-slate-200">
                                {{ $suggestion->created_at?->format('Y-m-d H:i') ?? '-' }}
                            </td>
                            <td class="font-semibold dark:text-slate-100">
                                {{ $suggestion->gym?->name ?? 'N/D' }}
                            </td>
                            <td class="dark:text-slate-200">
                                <p class="font-semibold">{{ $suggestion->sender?->name ?? 'Usuario eliminado' }}</p>
                                <p class="text-xs ui-muted">{{ $suggestion->sender?->email ?? '-' }}</p>
                            </td>
                            <td class="dark:text-slate-100">
                                @if ($isReactivationRequest)
                                    <span class="sa-status-chip is-info mb-1">Reactivacion</span>
                                @endif
                                <p class="font-semibold">{{ $suggestion->subject }}</p>
                            </td>
                            <td class="dark:text-slate-200">
                                <p class="max-w-lg whitespace-pre-wrap break-words text-sm">{{ $displayMessage }}</p>
                                @if (is_string($receiptUrl) && $receiptUrl !== '')
                                    <a href="{{ $receiptUrl }}" target="_blank" rel="noopener" class="sa-status-chip is-info mt-2">
                                        Ver comprobante
                                    </a>
                                @endif
                            </td>
                            <td>
                                <span class="{{ $statusClass }}">
                                    {{ $isPending ? 'Pendiente' : 'Revisada' }}
                                </span>
                                @if (! $isPending && $suggestion->reviewedBy)
                                    <p class="sa-inline-note mt-1">Por {{ $suggestion->reviewedBy->name }}</p>
                                @endif
                            </td>
                            <td>
                                @if ($isPending)
                                    <div class="sa-action-row">
                                        <form method="POST" action="{{ route('superadmin.suggestions.reviewed', $suggestion->id) }}">
                                            @csrf
                                            <x-ui.button type="submit" size="sm" variant="success">Marcar revisada</x-ui.button>
                                        </form>
                                    </div>
                                @else
                                    <span class="sa-inline-note">Sin acciones</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="sa-empty-row">
                                No hay sugerencias para los filtros seleccionados.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $suggestions->links() }}
        </div>
    </x-ui.card>
    </div>
@endsection
