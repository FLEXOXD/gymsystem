@extends('layouts.panel')

@section('title', 'Reporte de asistencias')
@section('page-title', 'Reporte de asistencias')

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

    <x-ui.card title="Filtro de asistencias">
        <form method="GET" action="{{ route('reports.attendance') }}" class="grid gap-3 md:grid-cols-3 md:items-end">
            <label class="space-y-1 text-sm font-semibold ui-muted">
                <span>Desde</span>
                <input type="date" name="from" value="{{ $from->toDateString() }}" class="ui-input">
            </label>

            <label class="space-y-1 text-sm font-semibold ui-muted">
                <span>Hasta</span>
                <input type="date" name="to" value="{{ $to->toDateString() }}" class="ui-input">
            </label>

            <div class="flex flex-wrap gap-2">
                <x-ui.button type="submit" variant="secondary">Aplicar</x-ui.button>
                <x-ui.button :href="route('reports.index', request()->query())" variant="ghost">Panel</x-ui.button>
            </div>
        </form>
    </x-ui.card>

    <section class="grid gap-4 md:grid-cols-3">
        <x-ui.card>
            <p class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-300">Periodo actual</p>
            <p class="mt-2 text-3xl font-black text-slate-900 dark:text-slate-100">{{ (int) $comparison['current_total'] }}</p>
            <p class="mt-1 text-xs text-slate-500 dark:text-slate-300">{{ $from->toDateString() }} al {{ $to->toDateString() }}</p>
        </x-ui.card>

        <x-ui.card>
            <p class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-300">Periodo anterior</p>
            <p class="mt-2 text-3xl font-black text-slate-900 dark:text-slate-100">{{ (int) $comparison['previous_total'] }}</p>
            <p class="mt-1 text-xs text-slate-500 dark:text-slate-300">{{ $comparison['previous_from']->toDateString() }} al {{ $comparison['previous_to']->toDateString() }}</p>
        </x-ui.card>

        <x-ui.card>
            <p class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-300">Variación</p>
            <p class="mt-2 text-3xl font-black {{ $diffToneClass }}">
                {{ $comparison['diff'] > 0 ? '+' : '' }}{{ (int) $comparison['diff'] }}
            </p>
            <p class="mt-1 text-xs text-slate-500 dark:text-slate-300">
                @if ($comparison['diff_pct'] !== null)
                    {{ $comparison['diff_pct'] > 0 ? '+' : '' }}{{ number_format((float) $comparison['diff_pct'], 1) }}%
                @elseif ((int) $comparison['previous_total'] === 0 && (int) $comparison['current_total'] > 0)
                    Sin base de comparación
                @else
                    Sin cambios
                @endif
            </p>
        </x-ui.card>
    </section>

    <x-ui.card title="Comparativa de asistencias">
        <canvas id="attendanceComparisonChart" height="120"></canvas>
    </x-ui.card>

    <x-ui.card title="Asistencias por día">
        <div class="overflow-x-auto">
            <table class="ui-table min-w-[560px]">
                <thead>
                <tr class="sticky top-0 border-b border-slate-200 bg-slate-50 text-left text-xs uppercase tracking-wider text-slate-500">
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
                plugins: {
                    legend: {
                        position: 'bottom',
                    },
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
