@extends('layouts.panel')

@section('title', 'Reportes')
@section('page-title', 'Reportes')

@section('content')
    @php
        $currencyFormatter = \App\Support\Currency::class;
        $planAccessService = app(\App\Services\PlanAccessService::class);
        $contextGym = (string) request()->route('contextGym');
        $isBranchContext = (bool) request()->attributes->get('gym_context_is_branch', false);
        $activeGymId = (int) (request()->attributes->get('active_gym_id') ?? auth()->user()?->gym_id ?? 0);
        $canExportReports = ! $isBranchContext
            && $activeGymId > 0
            && $planAccessService->canForGym($activeGymId, 'reports_export');
        $isGlobalScope = (bool) request()->attributes->get('active_gym_is_global', false);
        $canUseSalesInventoryReports = $activeGymId > 0
            && $planAccessService->canForGym($activeGymId, 'sales_inventory_reports');
        $baseRouteParams = ['contextGym' => $contextGym] + ($isGlobalScope ? ['scope' => 'global'] : []);
        $reportRouteParams = [
            'contextGym' => $contextGym,
            'from' => $from->toDateString(),
            'to' => $to->toDateString(),
        ] + ($isGlobalScope ? ['scope' => 'global'] : []);
        $membershipsRouteParams = ['contextGym' => $contextGym] + ($isGlobalScope ? ['scope' => 'global'] : []);
    @endphp

    @if ($isGlobalScope)
        <div class="mb-4 ui-alert ui-alert-info">
            Reporte global activo: los datos mostrados suman todas las sedes vinculadas.
        </div>
    @endif

    <x-ui.card title="Panel de reportes" subtitle="Resumen financiero y operativo del rango seleccionado.">
        <form id="reports-filter-form" method="GET" action="{{ route('reports.index', ['contextGym' => $contextGym]) }}" class="grid gap-3 md:grid-cols-4 md:items-end">
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

            <x-ui.button type="submit" variant="secondary">Aplicar filtro</x-ui.button>

            <div class="flex flex-wrap gap-2">
                @if ($canExportReports)
                    <x-ui.button id="reports-export-pdf"
                                 :href="route('reports.export.pdf', $reportRouteParams)"
                                 target="_blank" rel="noopener" variant="ghost" class="js-loading-link" data-loading-text="Generando PDF...">Exportar PDF</x-ui.button>
                    <x-ui.button id="reports-export-csv"
                                 :href="route('reports.export.csv', $reportRouteParams)"
                                 data-ui-loading-ignore="1"
                                 variant="ghost">Exportar CSV</x-ui.button>
                @else
                    <p class="text-xs font-semibold text-amber-700 dark:text-amber-300">
                        {{ $isBranchContext ? 'Sucursal secundaria: exportacion bloqueada (solo lectura).' : 'Exportacion disponible en plan Premium o Sucursales.' }}
                    </p>
                @endif
            </div>
        </form>
    </x-ui.card>

    <section class="grid gap-4 xl:grid-cols-3">
        <x-ui.card title="Ganancias del gimnasio" subtitle="Resumen financiero principal del negocio.">
            <div class="space-y-3">
                <p class="text-sm text-slate-600 dark:text-slate-300">Usa esta seccion para revisar ingresos, egresos, balance y exportaciones del rango seleccionado.</p>
                <div class="flex flex-wrap gap-2">
                    <x-ui.button :href="route('reports.income', ['contextGym' => $contextGym] + request()->query())" variant="secondary">Detalle ingresos</x-ui.button>
                    <x-ui.button :href="route('panel.index', $baseRouteParams)" variant="ghost">Volver al panel</x-ui.button>
                </div>
            </div>
        </x-ui.card>

        <x-ui.card title="Clientes" subtitle="Lectura de asistencia y estado de membresias.">
            <div class="space-y-3">
                <p class="text-sm text-slate-600 dark:text-slate-300">Centraliza lo relacionado con comportamiento de clientes, renovaciones y vigencia de membresias.</p>
                <div class="flex flex-wrap gap-2">
                    <x-ui.button :href="route('reports.attendance', ['contextGym' => $contextGym] + request()->query())" variant="secondary">Asistencias</x-ui.button>
                    <x-ui.button :href="route('reports.memberships', $membershipsRouteParams)" variant="ghost">Membresias</x-ui.button>
                </div>
            </div>
        </x-ui.card>

        <x-ui.card title="Ventas e inventario" subtitle="Rendimiento comercial de productos y control de stock.">
            @if ($canUseSalesInventoryReports)
                <div class="space-y-3">
                    <p class="text-sm text-slate-600 dark:text-slate-300">Seccion separada para ver ingresos por productos, utilidad, rotacion y alertas de inventario.</p>
                    <div class="flex flex-wrap gap-2">
                        <x-ui.button :href="route('reports.sales-inventory', ['contextGym' => $contextGym] + request()->query())" variant="secondary">Abrir reportes</x-ui.button>
                        <x-ui.button :href="route('sales.index', $baseRouteParams)" variant="ghost">Ir al modulo</x-ui.button>
                    </div>
                </div>
            @else
                <p class="text-sm font-semibold text-amber-700 dark:text-amber-300">Disponible en planes Profesional, Premium y Sucursales.</p>
            @endif
        </x-ui.card>
    </section>

    <section class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
        <x-ui.card>
            <p class="ui-muted text-xs font-bold uppercase tracking-wider">Total ingresos</p>
            <p class="mt-2 text-3xl font-black text-emerald-700">{{ $currencyFormatter::format((float) $incomeSummary['total_income'], $appCurrencyCode) }}</p>
        </x-ui.card>
        <x-ui.card>
            <p class="ui-muted text-xs font-bold uppercase tracking-wider">Total egresos</p>
            <p class="mt-2 text-3xl font-black text-rose-700">{{ $currencyFormatter::format((float) $incomeSummary['total_expense'], $appCurrencyCode) }}</p>
        </x-ui.card>
        <x-ui.card>
            <p class="ui-muted text-xs font-bold uppercase tracking-wider">Balance</p>
            <p class="mt-2 text-3xl font-black text-cyan-700">{{ $currencyFormatter::format((float) $incomeSummary['balance'], $appCurrencyCode) }}</p>
        </x-ui.card>
        <x-ui.card>
            <p class="ui-muted text-xs font-bold uppercase tracking-wider">Movimientos</p>
            <p class="ui-heading mt-2 text-4xl font-black tracking-tight">{{ (int) $incomeSummary['total_movements'] }}</p>
        </x-ui.card>
        <x-ui.card>
            <p class="ui-muted text-xs font-bold uppercase tracking-wider">Asistencias</p>
            <p class="ui-heading mt-2 text-4xl font-black tracking-tight">{{ (int) $attendanceSummary['total_attendances'] }}</p>
        </x-ui.card>
        <x-ui.card>
            <p class="ui-muted text-xs font-bold uppercase tracking-wider">Membresias activas</p>
            <p class="ui-heading mt-2 text-4xl font-black tracking-tight">{{ (int) $membershipSummary['active'] }}</p>
            <div class="mt-3 flex gap-2">
                <x-ui.badge variant="success">Activos {{ (int) $membershipSummary['active'] }}</x-ui.badge>
                <x-ui.badge variant="danger">Vencidos {{ (int) $membershipSummary['expired'] }}</x-ui.badge>
            </div>
        </x-ui.card>
    </section>

    <section class="grid gap-4 xl:grid-cols-2">
        <x-ui.card title="Ingresos / egresos por metodo">
            <canvas id="methodChart" height="120"></canvas>
        </x-ui.card>
        <x-ui.card title="Asistencias por dia">
            <canvas id="attendanceChart" height="120"></canvas>
        </x-ui.card>
    </section>

    <x-ui.card title="Navegacion rapida de reportes">
        <div class="flex flex-wrap gap-2">
            <x-ui.button id="reports-go-income" :href="route('reports.income', ['contextGym' => $contextGym] + request()->query())" variant="secondary">Detalle ingresos</x-ui.button>
            <x-ui.button :href="route('reports.attendance', ['contextGym' => $contextGym] + request()->query())" variant="ghost">Detalle asistencias</x-ui.button>
            <x-ui.button :href="route('reports.memberships', $membershipsRouteParams)" variant="ghost">Estado membresias</x-ui.button>
            @if ($canUseSalesInventoryReports)
                <x-ui.button :href="route('reports.sales-inventory', ['contextGym' => $contextGym] + request()->query())" variant="ghost">Ventas e inventario</x-ui.button>
            @endif
        </div>
    </x-ui.card>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    (function () {
        const methodLabels = @json($methodLabels);
        const methodIncomeData = @json($methodIncomeData);
        const methodExpenseData = @json($methodExpenseData);
        const attendanceLabels = @json($attendanceLabels);
        const attendanceData = @json($attendanceData);

        const methodCtx = document.getElementById('methodChart');
        if (methodCtx) {
            new Chart(methodCtx, {
                type: 'bar',
                data: {
                    labels: methodLabels,
                    datasets: [
                        { label: 'Ingresos', data: methodIncomeData, backgroundColor: '#059669' },
                        { label: 'Egresos', data: methodExpenseData, backgroundColor: '#dc2626' },
                    ],
                },
                options: { responsive: true, plugins: { legend: { position: 'bottom' } } },
            });
        }

        const attendanceCtx = document.getElementById('attendanceChart');
        if (attendanceCtx) {
            new Chart(attendanceCtx, {
                type: 'line',
                data: {
                    labels: attendanceLabels,
                    datasets: [
                        {
                            label: 'Asistencias',
                            data: attendanceData,
                            borderColor: '#0284c7',
                            backgroundColor: 'rgba(2,132,199,0.15)',
                            fill: true,
                            tension: 0.25,
                        },
                    ],
                },
                options: { responsive: true, plugins: { legend: { position: 'bottom' } } },
            });
        }

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
    })();
</script>
@endpush
