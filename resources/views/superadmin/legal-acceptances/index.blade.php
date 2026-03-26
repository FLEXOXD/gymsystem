@extends('layouts.panel')

@section('title', 'Aceptaciones legales')
@section('page-title', 'Aceptaciones legales')

@section('content')
    @php
        $visibleAcceptanceCount = method_exists($acceptances, 'count') ? $acceptances->count() : collect($acceptances)->count();
        $acceptanceItems = method_exists($acceptances, 'getCollection') ? $acceptances->getCollection() : collect($acceptances);
        $withContractCount = $acceptanceItems->filter(fn ($acceptance) => filled($acceptance->contract_code ?? null))->count();
        $acceptanceTotal = method_exists($acceptances, 'total') ? $acceptances->total() : $visibleAcceptanceCount;
    @endphp
    <div class="sa-shell">
        <section class="sa-hero">
            <div class="sa-hero-grid">
                <div>
                    <span class="sa-kicker">Respaldo legal</span>
                    <h2 class="sa-title">Aceptaciones legales con filtro, version y acceso rapido al contrato.</h2>
                    <p class="sa-subtitle">
                        Esta vista deja claro que version esta activa, cuantos registros tienes y cuantas evidencias estan listas para abrir en PDF.
                    </p>
                </div>

                <aside class="sa-note-card">
                    <p class="sa-note-label">Lectura rapida</p>
                    <div class="sa-note-list">
                        <div class="sa-note-item">
                            <strong>Version vigente {{ $currentVersion }}</strong>
                            <span>La tabla muestra aceptaciones digitales del primer ingreso.</span>
                        </div>
                        <div class="sa-note-item">
                            <strong>{{ $withContractCount }} contratos listos</strong>
                            <span>Los registros con codigo pueden abrirse directamente en PDF.</span>
                        </div>
                        <div class="sa-note-item">
                            <strong>{{ ($dbNotReady ?? false) ? 'Migracion pendiente' : 'Base legal operativa' }}</strong>
                            <span>{{ ($dbNotReady ?? false) ? 'Ejecuta migraciones para habilitar la vista.' : 'Puedes filtrar y consultar evidencias desde aqui.' }}</span>
                        </div>
                    </div>
                </aside>
            </div>
        </section>

        <section class="sa-stat-grid">
            <article class="sa-stat-card is-neutral">
                <p class="sa-stat-label">Total registros</p>
                <p class="sa-stat-value">{{ $acceptanceTotal }}</p>
                <p class="sa-stat-meta">Aceptaciones legales guardadas en el sistema.</p>
            </article>
            <article class="sa-stat-card is-info">
                <p class="sa-stat-label">Visibles</p>
                <p class="sa-stat-value">{{ $visibleAcceptanceCount }}</p>
                <p class="sa-stat-meta">Resultados del filtro actual.</p>
            </article>
            <article class="sa-stat-card is-success">
                <p class="sa-stat-label">Con contrato</p>
                <p class="sa-stat-value">{{ $withContractCount }}</p>
                <p class="sa-stat-meta">Registros con evidencia lista para PDF.</p>
            </article>
            <article class="sa-stat-card {{ ($dbNotReady ?? false) ? 'is-warning' : 'is-success' }}">
                <p class="sa-stat-label">Estado modulo</p>
                <p class="sa-stat-value">{{ ($dbNotReady ?? false) ? 'Pendiente' : 'Listo' }}</p>
                <p class="sa-stat-meta">Migracion y consulta legal del modulo.</p>
            </article>
        </section>
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
                <input type="text" name="versión" value="{{ $filters['versión'] ?? '' }}" class="ui-input mt-1 block min-w-[140px]" placeholder="{{ $currentVersion }}">
            </label>

            <x-ui.button type="submit" variant="secondary">Aplicar</x-ui.button>
        </form>

        <div class="mb-3 text-sm text-slate-600 dark:text-slate-300">
            Total registros: <strong>{{ $acceptances->total() }}</strong> | Versión vigente: <strong>{{ $currentVersion }}</strong>
        </div>

        <div class="sa-table-shell overflow-x-auto">
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
                        <tr>
                            <td class="whitespace-nowrap dark:text-slate-200">
                                {{ $acceptance->accepted_at?->format('Y-m-d H:i:s') ?? '-' }}
                            </td>
                            <td class="dark:text-slate-100">
                                <p class="font-semibold">{{ $acceptance->full_name }}</p>
                                <p class="text-xs ui-muted">ID usuario: {{ $acceptance->user_id ?? 'N/D' }}</p>
                            </td>
                            <td class="dark:text-slate-200">
                                {{ $acceptance->email }}
                            </td>
                            <td class="dark:text-slate-200">
                                {{ $acceptance->document_label }}
                            </td>
                            <td class="whitespace-nowrap dark:text-slate-200">
                                {{ $acceptance->legal_version }}
                            </td>
                            <td class="dark:text-slate-200">
                                <p>{{ $acceptance->ip_address ?? '-' }}</p>
                                <p class="text-xs ui-muted">vía {{ $acceptance->accepted_via ?? 'n/a' }}</p>
                            </td>
                            <td class="dark:text-slate-200">
                                <p>{{ $coordsLabel }}</p>
                                <p class="text-xs ui-muted">permiso: {{ $acceptance->location_permission ?? 'skipped' }}</p>
                            </td>
                            <td class="dark:text-slate-200">
                                <span class="sa-table-code">
                    {{ $acceptance->contract_code ?: 'SIN-CÓDIGO' }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('superadmin.legal-acceptances.contract.pdf', $acceptance->id) }}" target="_blank" rel="noreferrer">
                                    <x-ui.button type="button" size="sm" variant="secondary">Ver PDF contrato</x-ui.button>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="sa-empty-row">
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
    </div>
@endsection
