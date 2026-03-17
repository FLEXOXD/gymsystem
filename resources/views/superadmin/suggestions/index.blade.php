@extends('layouts.panel')

@section('title', 'Sugerencias')
@section('page-title', 'Sugerencias de gimnasios')

@section('content')
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

        <div class="overflow-x-auto">
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
                                ? 'bg-amber-100 text-amber-800 dark:bg-amber-900/45 dark:text-amber-200'
                                : 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-200';
                        @endphp
                        <tr class="border-b border-slate-100 align-top text-sm odd:bg-white even:bg-slate-50 dark:border-slate-800 dark:odd:bg-slate-900 dark:even:bg-slate-950/50">
                            <td class="px-3 py-3 whitespace-nowrap dark:text-slate-200">
                                {{ $suggestion->created_at?->format('Y-m-d H:i') ?? '-' }}
                            </td>
                            <td class="px-3 py-3 font-semibold dark:text-slate-100">
                                {{ $suggestion->gym?->name ?? 'N/D' }}
                            </td>
                            <td class="px-3 py-3 dark:text-slate-200">
                                <p class="font-semibold">{{ $suggestion->sender?->name ?? 'Usuario eliminado' }}</p>
                                <p class="text-xs ui-muted">{{ $suggestion->sender?->email ?? '-' }}</p>
                            </td>
                            <td class="px-3 py-3 dark:text-slate-100">
                                @if ($isReactivationRequest)
                                    <span class="mb-1 inline-flex rounded-full bg-cyan-100 px-2 py-0.5 text-[10px] font-bold uppercase tracking-wide text-cyan-800 dark:bg-cyan-900/45 dark:text-cyan-200">Reactivacion</span>
                                @endif
                                <p class="font-semibold">{{ $suggestion->subject }}</p>
                            </td>
                            <td class="px-3 py-3 dark:text-slate-200">
                                <p class="max-w-lg whitespace-pre-wrap break-words text-sm">{{ $displayMessage }}</p>
                                @if (is_string($receiptUrl) && $receiptUrl !== '')
                                    <a href="{{ $receiptUrl }}" target="_blank" rel="noopener" class="mt-2 inline-flex rounded-full bg-cyan-100 px-2.5 py-1 text-xs font-semibold text-cyan-800 hover:bg-cyan-200 dark:bg-cyan-900/45 dark:text-cyan-200 dark:hover:bg-cyan-900/65">
                                        Ver comprobante
                                    </a>
                                @endif
                            </td>
                            <td class="px-3 py-3">
                                <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-bold uppercase tracking-wide {{ $statusClass }}">
                                    {{ $isPending ? 'Pendiente' : 'Revisada' }}
                                </span>
                                @if (! $isPending && $suggestion->reviewedBy)
                                    <p class="mt-1 text-xs ui-muted">Por {{ $suggestion->reviewedBy->name }}</p>
                                @endif
                            </td>
                            <td class="px-3 py-3">
                                @if ($isPending)
                                    <form method="POST" action="{{ route('superadmin.suggestions.reviewed', $suggestion->id) }}">
                                        @csrf
                                        <x-ui.button type="submit" size="sm" variant="success">Marcar revisada</x-ui.button>
                                    </form>
                                @else
                                    <span class="text-xs ui-muted">Sin acciones</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-3 py-6 text-center text-sm text-slate-500 dark:text-slate-300">
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
@endsection
