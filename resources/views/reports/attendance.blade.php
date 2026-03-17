@extends('layouts.panel')

@section('title', 'Reporte de asistencias')
@section('page-title', 'Reporte de asistencias')

@push('styles')
<style>
    .report-attendance .filter-form {
        align-items: end;
    }

    .report-attendance .period-card {
        min-height: 100%;
    }

    .report-attendance .chart-shell {
        height: clamp(260px, 46vh, 440px);
    }

    .report-attendance .chart-shell canvas {
        width: 100% !important;
        height: 100% !important;
    }

    .report-attendance .attendance-table-wrap {
        border-radius: 0.85rem;
        border: 1px solid rgb(203 213 225);
        overflow: auto;
    }

    .theme-dark .report-attendance .attendance-table-wrap {
        border-color: rgb(51 65 85 / 0.85);
    }

    .report-attendance .attendance-table-wrap .ui-table thead th {
        position: sticky;
        top: 0;
        z-index: 4;
    }
</style>
@endpush

@section('content')
    @php
        $comparison = $attendanceComparison ?? [
            'current_total' => (int) ($attendanceSummary['total_attendances'] ?? 0),
            'previous_total' => 0,
            'diff' => 0,
            'diff_pct' => null,
            'previous_from' => $from,
            'previous_to' => $to,
            'range_days' => 1,
        ];
        $diffToneClass = $comparison['diff'] > 0
            ? 'text-emerald-700 dark:text-emerald-300'
            : ($comparison['diff'] < 0 ? 'text-rose-700 dark:text-rose-300' : 'text-slate-700 dark:text-slate-200');
    @endphp

    <div class="report-attendance space-y-4">
        <x-ui.card title="Filtro de asistencias" subtitle="Compara el periodo actual con el rango anterior equivalente.">
            <form method="GET" action="{{ route('reports.attendance') }}" class="filter-form grid gap-3 md:grid-cols-4">
                <label class="space-y-1 text-sm font-semibold ui-muted">
                    <span>Desde</span>
                    <input type="date" name="from" value="{{ $from->toDateString() }}" class="ui-input">
                </label>

                <label class="space-y-1 text-sm font-semibold ui-muted">
                    <span>Hasta</span>
                    <input type="date" name="to" value="{{ $to->toDateString() }}" class="ui-input">
                </label>

                <div class="md:col-span-2 flex flex-wrap gap-2">
                    <x-ui.button type="submit" variant="secondary">Aplicar</x-ui.button>
                    <x-ui.button :href="route('reports.index', request()->query())" variant="ghost">Volver al panel</x-ui.button>
                </div>
            </form>
        </x-ui.card>

        <section class="grid gap-4 md:grid-cols-3">
            <x-ui.card class="period-card">
                <p class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-300">Periodo actual</p>
                <p class="mt-2 text-3xl font-black text-slate-900 dark:text-slate-100">{{ (int) $comparison['current_total'] }}</p>
                <p class="mt-1 text-xs text-slate-500 dark:text-slate-300">{{ $from->toDateString() }} al {{ $to->toDateString() }}</p>
            </x-ui.card>

            <x-ui.card class="period-card">
                <p class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-300">Periodo anterior</p>
                <p class="mt-2 text-3xl font-black text-slate-900 dark:text-slate-100">{{ (int) $comparison['previous_total'] }}</p>
                <p class="mt-1 text-xs text-slate-500 dark:text-slate-300">{{ $comparison['previous_from']->toDateString() }} al {{ $comparison['previous_to']->toDateString() }}</p>
            </x-ui.card>

            <x-ui.card class="period-card">
                <p class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-300">Variacion</p>
                <p class="mt-2 text-3xl font-black {{ $diffToneClass }}">
                    {{ $comparison['diff'] > 0 ? '+' : '' }}{{ (int) $comparison['diff'] }}
                </p>
                <p class="mt-1 text-xs text-slate-500 dark:text-slate-300">
                    @if ($comparison['diff_pct'] !== null)
                        {{ $comparison['diff_pct'] > 0 ? '+' : '' }}{{ number_format((float) $comparison['diff_pct'], 1) }}%
                    @elseif ((int) $comparison['previous_total'] === 0 && (int) $comparison['current_total'] > 0)
                        Sin base de comparacion
                    @else
                        Sin cambios
                    @endif
                </p>
            </x-ui.card>
        </section>

        <x-ui.card title="Comparativa de asistencias">
            <div class="chart-shell">
                <canvas id="attendanceComparisonChart"></canvas>
            </div>
        </x-ui.card>

        <x-ui.card title="Asistencias por dia">
            <div class="attendance-table-wrap">
                <table class="ui-table min-w-[560px]">
                    <thead>
                    <tr class="border-b border-slate-200 bg-slate-50 text-left text-xs uppercase tracking-wider text-slate-500">
                        <th class="px-3 py-3">Fecha</th>
                        <th class="px-3 py-3">Cantidad</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse ($attendanceByDay as $row)
                        <tr class="border-b border-slate-100 text-sm">
                            <td class="px-3 py-3">{{ \Carbon\Carbon::parse($row->date)->format('Y-m-d') }}</td>
                            <td class="px-3 py-3">{{ (int) $row->attendances_count }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="2" class="px-3 py-6 text-center text-sm text-slate-500">No hay asistencias en este rango.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </x-ui.card>
    </div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    (function () {
        const labels = @json($attendanceComparisonLabels ?? []);
        const currentSeries = @json($attendanceCurrentSeries ?? []);
        const previousSeries = @json($attendancePreviousSeries ?? []);
        const chartEl = document.getElementById('attendanceComparisonChart');

        if (!chartEl) return;

        new Chart(chartEl, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Periodo actual',
                        data: currentSeries,
                        borderColor: '#06b6d4',
                        backgroundColor: 'rgba(6, 182, 212, 0.15)',
                        fill: true,
                        tension: 0.28,
                    },
                    {
                        label: 'Periodo anterior',
                        data: previousSeries,
                        borderColor: '#f59e0b',
                        backgroundColor: 'rgba(245, 158, 11, 0.10)',
                        fill: true,
                        tension: 0.28,
                    },
                ],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                    },
                },
                interaction: {
                    mode: 'index',
                    intersect: false,
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0,
                        },
                    },
                },
            },
        });
    })();
</script>
@endpush
