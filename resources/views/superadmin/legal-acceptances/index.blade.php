@extends('layouts.panel')

@section('title', 'Aceptaciones legales')
@section('page-title', 'Aceptaciones legales')

@section('content')
    <x-ui.card title="Aceptaciones legales" subtitle="Respaldo legal de aceptaciones digitales registradas en el primer ingreso.">
        @if (($dbNotReady ?? false) === true)
            <div class="mb-4 rounded-lg border border-amber-300 bg-amber-50 px-4 py-3 text-sm text-amber-900 dark:border-amber-500/40 dark:bg-amber-900/20 dark:text-amber-200">
                La base legal aún no está lista. Ejecuta <code>php artisan migrate</code> para habilitar esta sección.
            </div>
        @endif

        <form method="GET" action="{{ route('superadmin.legal-acceptances.index') }}" class="mb-4 flex flex-wrap items-end gap-3">
            <label class="text-sm font-semibold ui-muted">
                Buscar
                <input type="text" name="q" value="{{ $filters['q'] ?? '' }}" class="ui-input mt-1 block min-w-[260px]" placeholder="Nombre, correo o código de contrato">
            </label>

            <label class="text-sm font-semibold ui-muted">
                Desde
                <input type="date" name="from" value="{{ $filters['from'] ?? '' }}" class="ui-input mt-1 block min-w-[170px]">
            </label>

            <label class="text-sm font-semibold ui-muted">
                Hasta
                <input type="date" name="to" value="{{ $filters['to'] ?? '' }}" class="ui-input mt-1 block min-w-[170px]">
            </label>

            <label class="text-sm font-semibold ui-muted">
                Versión
                <input type="text" name="version" value="{{ $filters['version'] ?? '' }}" class="ui-input mt-1 block min-w-[140px]" placeholder="{{ $currentVersion }}">
            </label>

            <x-ui.button type="submit" variant="secondary">Aplicar</x-ui.button>
        </form>

        <div class="mb-3 text-sm text-slate-600 dark:text-slate-300">
            Total registros: <strong>{{ $acceptances->total() }}</strong> | Versión vigente: <strong>{{ $currentVersion }}</strong>
        </div>

        <div class="overflow-x-auto">
            <table class="ui-table min-w-[1280px]">
                <thead>
                    <tr class="border-b border-slate-200 bg-slate-50 text-left text-xs uppercase tracking-wider text-slate-500 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300">
                        <th class="px-3 py-3">Fecha de aceptación</th>
                        <th class="px-3 py-3">Usuario</th>
                        <th class="px-3 py-3">Correo</th>
                        <th class="px-3 py-3">Documento</th>
                        <th class="px-3 py-3">Versión</th>
                        <th class="px-3 py-3">IP</th>
                        <th class="px-3 py-3">Ubicación</th>
                        <th class="px-3 py-3">Contrato</th>
                        <th class="px-3 py-3">Acción</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($acceptances as $acceptance)
                        @php
                            $hasCoords = $acceptance->latitude !== null && $acceptance->longitude !== null;
                            $coordsLabel = $hasCoords
                                ? number_format((float) $acceptance->latitude, 6).', '.number_format((float) $acceptance->longitude, 6)
                                : 'Sin coordenadas';
                        @endphp
                        <tr class="border-b border-slate-100 align-top text-sm odd:bg-white even:bg-slate-50 dark:border-slate-800 dark:odd:bg-slate-900 dark:even:bg-slate-950/50">
                            <td class="px-3 py-3 whitespace-nowrap dark:text-slate-200">
                                {{ $acceptance->accepted_at?->format('Y-m-d H:i:s') ?? '-' }}
                            </td>
                            <td class="px-3 py-3 dark:text-slate-100">
                                <p class="font-semibold">{{ $acceptance->full_name }}</p>
                                <p class="text-xs ui-muted">ID usuario: {{ $acceptance->user_id ?? 'N/D' }}</p>
                            </td>
                            <td class="px-3 py-3 dark:text-slate-200">
                                {{ $acceptance->email }}
                            </td>
                            <td class="px-3 py-3 dark:text-slate-200">
                                {{ $acceptance->document_label }}
                            </td>
                            <td class="px-3 py-3 whitespace-nowrap dark:text-slate-200">
                                {{ $acceptance->legal_version }}
                            </td>
                            <td class="px-3 py-3 dark:text-slate-200">
                                <p>{{ $acceptance->ip_address ?? '-' }}</p>
                                <p class="text-xs ui-muted">vía {{ $acceptance->accepted_via ?? 'n/a' }}</p>
                            </td>
                            <td class="px-3 py-3 dark:text-slate-200">
                                <p>{{ $coordsLabel }}</p>
                                <p class="text-xs ui-muted">permiso: {{ $acceptance->location_permission ?? 'skipped' }}</p>
                            </td>
                            <td class="px-3 py-3 dark:text-slate-200">
                                <span class="inline-flex rounded bg-slate-100 px-2 py-1 font-mono text-xs text-slate-700 dark:bg-slate-800 dark:text-slate-200">
                                    {{ $acceptance->contract_code ?: 'SIN-CÓDIGO' }}
                                </span>
                            </td>
                            <td class="px-3 py-3">
                                <a href="{{ route('superadmin.legal-acceptances.contract.pdf', $acceptance->id) }}" target="_blank" rel="noreferrer">
                                    <x-ui.button type="button" size="sm" variant="secondary">Ver PDF contrato</x-ui.button>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-3 py-6 text-center text-sm text-slate-500 dark:text-slate-300">
                                No existen aceptaciones legales registradas con esos filtros.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $acceptances->links() }}
        </div>
    </x-ui.card>
@endsection
