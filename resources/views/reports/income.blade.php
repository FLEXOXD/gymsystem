@extends('layouts.panel')

@section('title', 'Reporte de ingresos')
@section('page-title', 'Reporte de ingresos y egresos')

@push('styles')
<style>
    .report-income .filter-form {
        align-items: end;
    }

    .report-income .movements-toolbar {
        display: flex;
        flex-wrap: wrap;
        gap: 0.75rem;
        align-items: end;
        justify-content: space-between;
        margin-bottom: 0.9rem;
    }

    .report-income .movements-toolbar-main {
        flex: 1 1 520px;
        display: grid;
        gap: 0.75rem;
        grid-template-columns: minmax(0, 1fr) minmax(180px, 220px) auto;
    }

    .report-income .movements-counter {
        flex: 1 1 260px;
        font-size: 0.85rem;
    }

    .report-income .movements-scroll {
        max-height: min(66vh, 720px);
        overflow: auto;
        border-radius: 0.85rem;
        border: 1px solid rgb(203 213 225);
    }

    .theme-dark .report-income .movements-scroll {
        border-color: rgb(51 65 85 / 0.85);
    }

    .report-income .movements-scroll .ui-table thead th {
        position: sticky;
        top: 0;
        z-index: 5;
        background: rgb(241 245 249 / 0.95);
        backdrop-filter: blur(4px);
    }

    .theme-dark .report-income .movements-scroll .ui-table thead th {
        background: rgb(30 41 59 / 0.95);
    }

    @media (max-width: 900px) {
        .report-income .movements-toolbar-main {
            grid-template-columns: minmax(0, 1fr);
        }
    }

    @media (max-width: 768px) {
        .report-income .ui-card {
            padding: 0.9rem;
        }

        .report-income .filter-form {
            grid-template-columns: minmax(0, 1fr);
            gap: 0.65rem;
        }

        .report-income .movements-toolbar {
            gap: 0.55rem;
        }

        .report-income .movements-counter {
            font-size: 0.78rem;
            line-height: 1.3;
        }

        .report-income .movements-scroll {
            max-height: none;
        }
    }
</style>
@endpush

@section('content')
    @php
        $currencyFormatter = \App\Support\Currency::class;
        $planAccessService = app(\App\Services\PlanAccessService::class);
        $isBranchContext = (bool) request()->attributes->get('gym_context_is_branch', false);
        $activeGymId = (int) (request()->attributes->get('active_gym_id') ?? auth()->user()?->gym_id ?? 0);
        $canExportReports = ! $isBranchContext
            && $activeGymId > 0
            && $planAccessService->canForGym($activeGymId, 'reports_export');
        $isGlobalScope = (bool) request()->attributes->get('active_gym_is_global', false);
        $reportRouteParams = [
            'from' => $from->toDateString(),
            'to' => $to->toDateString(),
        ] + ($isGlobalScope ? ['scope' => 'global'] : []);
        $methodLabels = [
            'cash' => 'Efectivo',
            'card' => 'Tarjeta',
            'transfer' => 'Transferencia',
        ];
    @endphp

    <div class="report-income space-y-4">
        <x-ui.card title="Filtro" subtitle="Consulta movimientos por rango de fecha.">
            <form method="GET" action="{{ route('reports.income') }}" class="filter-form grid gap-3 md:grid-cols-5">
                @if ($isGlobalScope)
                    <input type="hidden" name="scope" value="global">
                @endif

                <label class="space-y-1 text-sm font-semibold ui-muted">
                    <span>Desde</span>
                    <input type="date" name="from" value="{{ $from->toDateString() }}" class="ui-input">
                </label>

                <label class="space-y-1 text-sm font-semibold ui-muted">
                    <span>Hasta</span>
                    <input type="date" name="to" value="{{ $to->toDateString() }}" class="ui-input">
                </label>

                <x-ui.button type="submit" variant="secondary">Aplicar</x-ui.button>

                <div class="md:col-span-2 flex flex-wrap gap-2">
                    @if ($canExportReports)
                        <x-ui.button id="reports-income-export-pdf"
                                     :href="route('reports.export.pdf', $reportRouteParams)"
                                     target="_blank"
                                     rel="noopener"
                                     class="js-loading-link"
                                     data-loading-text="Generando PDF...">Exportar PDF</x-ui.button>
                        <x-ui.button id="reports-income-export-csv"
                                     :href="route('reports.export.csv', $reportRouteParams)"
                                     data-ui-loading-ignore="1"
                                     variant="secondary">Exportar CSV</x-ui.button>
                    @else
                        <p class="self-center text-xs font-semibold text-amber-700 dark:text-amber-300">
                            {{ $isBranchContext ? 'Sucursal secundaria: exportación bloqueada (solo lectura).' : 'Exportación disponible en planes Profesional, Premium o Sucursales.' }}
                        </p>
                    @endif
                    <x-ui.button :href="route('reports.index', request()->query())" variant="ghost">Volver al panel</x-ui.button>
                </div>
            </form>
        </x-ui.card>

        <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <x-ui.card>
                <p class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-300">Total ingresos</p>
                <p class="mt-2 text-3xl font-black text-emerald-700 dark:text-emerald-300">{{ $currencyFormatter::format((float) $incomeSummary['total_income'], $appCurrencyCode) }}</p>
            </x-ui.card>
            <x-ui.card>
                <p class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-300">Total egresos</p>
                <p class="mt-2 text-3xl font-black text-rose-700 dark:text-rose-300">{{ $currencyFormatter::format((float) $incomeSummary['total_expense'], $appCurrencyCode) }}</p>
            </x-ui.card>
            <x-ui.card>
                <p class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-300">Balance</p>
                <p class="mt-2 text-3xl font-black text-cyan-700 dark:text-cyan-300">{{ $currencyFormatter::format((float) $incomeSummary['balance'], $appCurrencyCode) }}</p>
            </x-ui.card>
            <x-ui.card>
                <p class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-300">Movimientos</p>
                <p class="mt-2 text-3xl font-black text-slate-900 dark:text-slate-100">{{ (int) $incomeSummary['total_movements'] }}</p>
            </x-ui.card>
        </section>

        <x-ui.card title="Detalle de movimientos">
            <div class="movements-toolbar">
                <div class="movements-toolbar-main">
                    <label class="space-y-1 text-sm font-semibold ui-muted">
                        <span>Buscar movimiento</span>
                        <input id="movement-search" type="text" class="ui-input" placeholder="ID, cliente, usuario, descripcion..." autocomplete="off">
                    </label>

                    <label class="space-y-1 text-sm font-semibold ui-muted">
                        <span>Tipo</span>
                        <select id="movement-type-filter" class="ui-input">
                            <option value="all">Todos</option>
                            <option value="income">Solo ingresos</option>
                            <option value="expense">Solo egresos</option>
                        </select>
                    </label>

                    <div class="flex">
                        <x-ui.button id="movement-clear-filters" type="button" variant="ghost">Limpiar filtros</x-ui.button>
                    </div>
                </div>

                <p class="movements-counter text-slate-600 dark:text-slate-300">
                    Pagina <strong>{{ $movements->currentPage() }}</strong> de <strong>{{ $movements->lastPage() }}</strong> |
                    Mostrando <strong id="movement-visible-count">{{ $movements->count() }}</strong> de <strong id="movement-page-count">{{ $movements->count() }}</strong> en esta pagina
                    (total <strong>{{ $movements->total() }}</strong>, {{ $movements->perPage() }} por pagina)
                </p>
            </div>

            <div class="movements-scroll table-mobile-stack">
                <table class="ui-table min-w-[1260px] text-slate-800 dark:text-slate-100" data-smart-list-manual>
                    <thead>
                    <tr class="border-b border-slate-200 text-left text-xs uppercase tracking-wider text-slate-600 dark:border-slate-700 dark:text-slate-300">
                        <th class="px-3 py-3">ID</th>
                        <th class="px-3 py-3">Fecha</th>
                        <th class="px-3 py-3">Tipo</th>
                        <th class="px-3 py-3">Metodo</th>
                        <th class="px-3 py-3">Monto</th>
                        @if (! empty($showGymColumn))
                            <th class="px-3 py-3">Sede</th>
                        @endif
                        <th class="px-3 py-3">Cliente</th>
                        <th class="px-3 py-3">Alta cliente</th>
                        <th class="px-3 py-3">Usuario</th>
                        <th class="px-3 py-3">Descripcion</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse ($movements as $movement)
                        @php
                            $isIncome = $movement->type === 'income';
                            $searchIndex = \Illuminate\Support\Str::lower(implode(' ', [
                                (string) $movement->id,
                                (string) ($movement->occurred_at?->format('Y-m-d H:i') ?? ''),
                                (string) ($methodLabels[$movement->method] ?? $movement->method),
                                (string) ($movement->gym?->name ?? ''),
                                (string) ($movement->membership?->client?->full_name ?? ''),
                                (string) (\App\Support\ClientAudit::actorDisplay((string) ($movement->membership?->client?->created_by_name_snapshot ?? ''), (string) ($movement->membership?->client?->created_by_role_snapshot ?? ''))),
                                (string) ($movement->createdBy?->name ?? ''),
                                (string) ($movement->description ?? ''),
                            ]));
                        @endphp
                        <tr data-movement-row data-type="{{ $movement->type }}" data-search="{{ $searchIndex }}" class="border-b border-slate-100 text-sm odd:bg-slate-50/45 hover:bg-cyan-50/70 dark:border-slate-800 dark:odd:bg-slate-900/30 dark:hover:bg-cyan-500/10">
                            <td data-label="ID" class="px-3 py-3">{{ $movement->id }}</td>
                            <td data-label="Fecha" class="px-3 py-3">{{ $movement->occurred_at?->format('Y-m-d H:i') ?? '-' }}</td>
                            <td data-label="Tipo" class="px-3 py-3">
                                <x-ui.badge :variant="$movement->type === 'income' ? 'success' : 'danger'">{{ $movement->type }}</x-ui.badge>
                            </td>
                            <td data-label="Metodo" class="px-3 py-3">{{ $methodLabels[$movement->method] ?? $movement->method }}</td>
                            <td data-label="Monto" class="px-3 py-3">
                                <span class="inline-flex items-center rounded-full border px-2.5 py-1 text-xs font-black tracking-wide {{ $isIncome
                                    ? 'border-emerald-300 bg-emerald-50 text-emerald-800 dark:border-emerald-400/40 dark:bg-emerald-500/20 dark:text-emerald-200'
                                    : 'border-rose-300 bg-rose-50 text-rose-800 dark:border-rose-400/40 dark:bg-rose-500/20 dark:text-rose-200' }}">
                                    {{ $movement->type === 'income' ? '+' : '-' }}{{ $currencyFormatter::format((float) $movement->amount, $appCurrencyCode, true) }}
                                </span>
                            </td>
                            @if (! empty($showGymColumn))
                                <td data-label="Sede" class="px-3 py-3">{{ $movement->gym?->name ?? '-' }}</td>
                            @endif
                            <td data-label="Cliente" class="px-3 py-3">{{ $movement->membership?->client?->full_name ?? '-' }}</td>
                            <td data-label="Alta cliente" class="px-3 py-3">{{ \App\Support\ClientAudit::actorDisplay((string) ($movement->membership?->client?->created_by_name_snapshot ?? ''), (string) ($movement->membership?->client?->created_by_role_snapshot ?? '')) }}</td>
                            <td data-label="Usuario" class="px-3 py-3">{{ $movement->createdBy?->name ?? '-' }}</td>
                            <td data-label="Descripcion" class="px-3 py-3">{{ $movement->description ?: '-' }}</td>
                        </tr>
                    @empty
                        <tr id="movement-empty-range">
                            <td colspan="{{ ! empty($showGymColumn) ? 10 : 9 }}" class="px-3 py-6 text-center text-sm text-slate-500">No hay movimientos en este rango.</td>
                        </tr>
                    @endforelse
                    <tr id="movement-empty-filter" class="hidden">
                        <td colspan="{{ ! empty($showGymColumn) ? 10 : 9 }}" class="px-3 py-6 text-center text-sm text-slate-500 dark:text-slate-300">No hay coincidencias para tu búsqueda.</td>
                    </tr>
                    </tbody>
                </table>
            </div>

            <div class="mt-4">{{ $movements->links() }}</div>
        </x-ui.card>
    </div>
@endsection

@push('scripts')
<script>
    (function () {
        document.querySelectorAll('.js-loading-link').forEach(function (link) {
            link.addEventListener('click', function () {
                const text = link.getAttribute('data-loading-text');
                if (!text) return;
                link.dataset.originalText = link.textContent;
                link.textContent = text;
                link.classList.add('pointer-events-none', 'opacity-70');
                setTimeout(function () {
                    link.textContent = link.dataset.originalText || link.textContent;
                    link.classList.remove('pointer-events-none', 'opacity-70');
                }, 1800);
            });
        });

        const searchInput = document.getElementById('movement-search');
        const typeFilter = document.getElementById('movement-type-filter');
        const clearFiltersButton = document.getElementById('movement-clear-filters');
        const visibleCount = document.getElementById('movement-visible-count');
        const pageCount = document.getElementById('movement-page-count');
        const movementRows = Array.from(document.querySelectorAll('[data-movement-row]'));
        const emptyFilter = document.getElementById('movement-empty-filter');

        function normalizeText(value) {
            return (value || '')
                .toString()
                .toLowerCase()
                .normalize('NFD')
                .replace(/[\u0300-\u036f]/g, '');
        }

        function resetFilters() {
            if (searchInput) searchInput.value = '';
            if (typeFilter) typeFilter.value = 'all';
            filterMovements();
        }

        function filterMovements() {
            if (!movementRows.length) return;

            const term = normalizeText(searchInput?.value || '');
            const type = (typeFilter?.value || 'all').toLowerCase();
            let totalVisible = 0;

            movementRows.forEach(function (row) {
                const rowType = (row.getAttribute('data-type') || '').toLowerCase();
                const haystack = normalizeText(row.getAttribute('data-search') || '');
                const matchesType = type === 'all' || rowType === type;
                const matchesText = term === '' || haystack.includes(term);
                const isVisible = matchesType && matchesText;

                row.classList.toggle('hidden', !isVisible);
                if (isVisible) totalVisible += 1;
            });

            if (visibleCount) visibleCount.textContent = String(totalVisible);
            if (emptyFilter) emptyFilter.classList.toggle('hidden', totalVisible > 0);
            if (clearFiltersButton) {
                const hasActiveFilter = term.length > 0 || type !== 'all';
                clearFiltersButton.classList.toggle('opacity-70', !hasActiveFilter);
            }
            if (pageCount && movementRows.length) {
                pageCount.textContent = String(movementRows.length);
            }
        }

        searchInput?.addEventListener('input', filterMovements);
        typeFilter?.addEventListener('change', filterMovements);
        clearFiltersButton?.addEventListener('click', resetFilters);
        filterMovements();
    })();
</script>
@endpush
