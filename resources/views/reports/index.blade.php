@extends('layouts.panel')

@section('title', 'Reportes')
@section('page-title', 'Reportes')

@push('styles')
<style>
    .report-hub .report-filter-form {
        align-items: end;
    }

    .report-hub .report-module-grid,
    .report-hub .report-chart-grid {
        align-items: stretch;
    }

    .report-hub .report-module-card,
    .report-hub .report-kpi-card,
    .report-hub .report-chart-card {
        min-height: 100%;
    }

    .report-hub .report-chart-shell {
        height: clamp(220px, 32vh, 340px);
    }

    .report-hub .report-chart-shell canvas {
        width: 100% !important;
        height: 100% !important;
    }

    @media (max-width: 768px) {
        .report-hub {
            gap: 0.8rem;
        }

        .report-hub .ui-card {
            padding: 0.9rem;
        }

        .report-hub .report-filter-form {
            grid-template-columns: minmax(0, 1fr);
            gap: 0.65rem;
        }

        .report-hub .report-module-grid,
        .report-hub .report-chart-grid {
            gap: 0.8rem;
        }

        .report-hub .report-module-card .ui-heading {
            font-size: 1.03rem;
            line-height: 1.2;
        }

        .report-hub .report-module-card p {
            line-height: 1.28;
        }
    }

    @media (max-width: 640px) {
        .report-hub .report-chart-shell {
            height: 215px;
        }
    }
</style>
@endpush

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
        $clientEarningsRouteParams = [
            'contextGym' => $contextGym,
            'from' => $from->toDateString(),
            'to' => $to->toDateString(),
        ] + ($isGlobalScope ? ['scope' => 'global'] : []);
    @endphp

    <div class="report-hub space-y-4">
        @if ($isGlobalScope)
            <div class="ui-alert ui-alert-info">
                Reporte global activo: los datos mostrados suman todas las sedes vinculadas.
            </div>
        @endif

        <x-ui.card class="report-hub-filter" title="Panel de reportes" subtitle="Resumen financiero y operativo del rango seleccionado.">
            <form id="reports-filter-form" method="GET" action="{{ route('reports.index', ['contextGym' => $contextGym]) }}" class="report-filter-form grid gap-3 md:grid-cols-4">
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
                            {{ $isBranchContext ? 'Sucursal secundaria: exportación bloqueada (solo lectura).' : 'Exportación disponible en plan Premium o Sucursales.' }}
                        </p>
                    @endif
                </div>
            </form>
        </x-ui.card>

        <section class="report-module-grid grid gap-4 xl:grid-cols-4">
            <x-ui.card class="report-module-card" title="Ganancias del gimnasio" subtitle="Resumen financiero principal del negocio.">
                <div class="space-y-3">
                    <p class="text-sm text-slate-600 dark:text-slate-300">Usa esta sección para revisar ingresos, egresos, balance y exportaciones del rango seleccionado.</p>
                    <div class="flex flex-wrap gap-2">
                        <x-ui.button :href="route('reports.income', ['contextGym' => $contextGym] + request()->query())" variant="secondary">Detalle ingresos</x-ui.button>
                        <x-ui.button :href="route('panel.index', $baseRouteParams)" variant="ghost">Volver al panel</x-ui.button>
                    </div>
                </div>
            </x-ui.card>

            <x-ui.card class="report-module-card" title="Clientes" subtitle="Lectura de asistencia y estado de membresías.">
                <div class="space-y-3">
                    <p class="text-sm text-slate-600 dark:text-slate-300">Centraliza lo relacionado con comportamiento de clientes, renovaciones y vigencia de membresías.</p>
                    <div class="flex flex-wrap gap-2">
                        <x-ui.button :href="route('reports.attendance', ['contextGym' => $contextGym] + request()->query())" variant="secondary">Asistencias</x-ui.button>
                        <x-ui.button :href="route('reports.memberships', $membershipsRouteParams)" variant="ghost">Membresías</x-ui.button>
                    </div>
                </div>
            </x-ui.card>

            <x-ui.card class="report-module-card" title="Ganancias de clientes" subtitle="Facturación por cliente con desglose y filtros avanzados.">
                <div class="space-y-3">
                    <p class="text-sm text-slate-600 dark:text-slate-300">Revisa cuántos clientes han sido facturados, cuánto aportan y su último movimiento comercial.</p>
                    <div class="flex flex-wrap gap-2">
                        <x-ui.button :href="route('reports.client-earnings', $clientEarningsRouteParams)" variant="secondary">Abrir reporte</x-ui.button>
                        <x-ui.button :href="route('clients.index', $baseRouteParams)" variant="ghost">Ir a clientes</x-ui.button>
                    </div>
                </div>
            </x-ui.card>

            <x-ui.card class="report-module-card" title="Ventas e inventario" subtitle="Rendimiento comercial de productos y control de stock.">
                @if ($canUseSalesInventoryReports)
                    <div class="space-y-3">
                        <p class="text-sm text-slate-600 dark:text-slate-300">Sección separada para ver ingresos por productos, utilidad, rotación y alertas de inventario.</p>
                        <div class="flex flex-wrap gap-2">
                            <x-ui.button :href="route('reports.sales-inventory', ['contextGym' => $contextGym] + request()->query())" variant="secondary">Abrir reportes</x-ui.button>
                            <x-ui.button :href="route('sales.index', $baseRouteParams)" variant="ghost">Ir al módulo</x-ui.button>
                        </div>
                    </div>
                @else
                    <p class="text-sm font-semibold text-amber-700 dark:text-amber-300">Disponible en planes Profesional, Premium y Sucursales.</p>
                @endif
            </x-ui.card>
        </section>

        <section class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
            <x-ui.card class="report-kpi-card">
                <p class="ui-muted text-xs font-bold uppercase tracking-wider">Total ingresos</p>
                <p class="mt-2 text-3xl font-black text-emerald-700">{{ $currencyFormatter::format((float) $incomeSummary['total_income'], $appCurrencyCode) }}</p>
            </x-ui.card>
            <x-ui.card class="report-kpi-card">
                <p class="ui-muted text-xs font-bold uppercase tracking-wider">Total egresos</p>
                <p class="mt-2 text-3xl font-black text-rose-700">{{ $currencyFormatter::format((float) $incomeSummary['total_expense'], $appCurrencyCode) }}</p>
            </x-ui.card>
            <x-ui.card class="report-kpi-card">
                <p class="ui-muted text-xs font-bold uppercase tracking-wider">Balance</p>
                <p class="mt-2 text-3xl font-black text-cyan-700">{{ $currencyFormatter::format((float) $incomeSummary['balance'], $appCurrencyCode) }}</p>
            </x-ui.card>
            <x-ui.card class="report-kpi-card">
                <p class="ui-muted text-xs font-bold uppercase tracking-wider">Movimientos</p>
                <p class="ui-heading mt-2 text-4xl font-black tracking-tight">{{ (int) $incomeSummary['total_movements'] }}</p>
            </x-ui.card>
            <x-ui.card class="report-kpi-card">
                <p class="ui-muted text-xs font-bold uppercase tracking-wider">Asistencias</p>
                <p class="ui-heading mt-2 text-4xl font-black tracking-tight">{{ (int) $attendanceSummary['total_attendances'] }}</p>
            </x-ui.card>
            <x-ui.card class="report-kpi-card">
                <p class="ui-muted text-xs font-bold uppercase tracking-wider">Membresías activas</p>
                <p class="ui-heading mt-2 text-4xl font-black tracking-tight">{{ (int) $membershipSummary['active'] }}</p>
                <div class="mt-3 flex gap-2">
                    <x-ui.badge variant="success">Activos {{ (int) $membershipSummary['active'] }}</x-ui.badge>
                    <x-ui.badge variant="danger">Vencidos {{ (int) $membershipSummary['expired'] }}</x-ui.badge>
                </div>
            </x-ui.card>
        </section>

        <section class="report-chart-grid grid gap-4 xl:grid-cols-2">
            <x-ui.card class="report-chart-card" title="Ingresos / egresos por método">
                <div class="report-chart-shell">
                    <canvas id="methodChart"></canvas>
                </div>
            </x-ui.card>
            <x-ui.card class="report-chart-card" title="Asistencias por día">
                <div class="report-chart-shell">
                    <canvas id="attendanceChart"></canvas>
                </div>
            </x-ui.card>
        </section>

        <x-ui.card title="Navegación rápida de reportes">
            <div class="flex flex-wrap gap-2">
                <x-ui.button id="reports-go-income" :href="route('reports.income', ['contextGym' => $contextGym] + request()->query())" variant="secondary">Detalle ingresos</x-ui.button>
                <x-ui.button :href="route('reports.attendance', ['contextGym' => $contextGym] + request()->query())" variant="ghost">Detalle asistencias</x-ui.button>
                <x-ui.button :href="route('reports.memberships', $membershipsRouteParams)" variant="ghost">Estado membresías</x-ui.button>
                <x-ui.button :href="route('reports.client-earnings', $clientEarningsRouteParams)" variant="ghost">Ganancias de clientes</x-ui.button>
                @if ($canUseSalesInventoryReports)
                    <x-ui.button :href="route('reports.sales-inventory', ['contextGym' => $contextGym] + request()->query())" variant="ghost">Ventas e inventario</x-ui.button>
                @endif
            </div>
        </x-ui.card>
    </div>
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
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { position: 'bottom' } },
                    scales: {
                        y: { beginAtZero: true },
                    },
                },
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
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { position: 'bottom' } },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: { precision: 0 },
                        },
                    },
                },
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
