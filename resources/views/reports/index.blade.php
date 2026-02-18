@extends('layouts.panel')

@section('title', 'Reportes')
@section('page-title', 'Reportes')

@section('content')
    <x-ui.card title="Dashboard de reportes" subtitle="Resumen financiero y operativo del rango seleccionado.">
        <form method="GET" action="{{ route('reports.index') }}" class="grid gap-3 md:grid-cols-4 md:items-end">
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
                <x-ui.button :href="route('reports.export.csv', ['from' => $from->toDateString(), 'to' => $to->toDateString()])"
                             class="js-loading-link" data-loading-text="Generando CSV...">Exportar CSV</x-ui.button>
                <x-ui.button :href="route('reports.export.pdf', ['from' => $from->toDateString(), 'to' => $to->toDateString()])"
                             target="_blank" rel="noopener" variant="ghost" class="js-loading-link" data-loading-text="Generando PDF...">Exportar PDF</x-ui.button>
            </div>
        </form>
    </x-ui.card>

    <section class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
        <x-ui.card>
            <p class="text-xs font-bold uppercase tracking-wider text-slate-500">Total ingresos</p>
            <p class="mt-2 text-3xl font-black text-emerald-700">${{ number_format((float) $incomeSummary['total_income'], 2) }}</p>
        </x-ui.card>
        <x-ui.card>
            <p class="text-xs font-bold uppercase tracking-wider text-slate-500">Total egresos</p>
            <p class="mt-2 text-3xl font-black text-rose-700">${{ number_format((float) $incomeSummary['total_expense'], 2) }}</p>
        </x-ui.card>
        <x-ui.card>
            <p class="text-xs font-bold uppercase tracking-wider text-slate-500">Balance</p>
            <p class="mt-2 text-3xl font-black text-cyan-700">${{ number_format((float) $incomeSummary['balance'], 2) }}</p>
        </x-ui.card>
        <x-ui.card>
            <p class="text-xs font-bold uppercase tracking-wider text-slate-500">Movimientos</p>
            <p class="mt-2 text-3xl font-black text-slate-900">{{ (int) $incomeSummary['total_movements'] }}</p>
        </x-ui.card>
        <x-ui.card>
            <p class="text-xs font-bold uppercase tracking-wider text-slate-500">Asistencias</p>
            <p class="mt-2 text-3xl font-black text-slate-900">{{ (int) $attendanceSummary['total_attendances'] }}</p>
        </x-ui.card>
        <x-ui.card>
            <p class="text-xs font-bold uppercase tracking-wider text-slate-500">Membresias activas</p>
            <p class="mt-2 text-3xl font-black text-slate-900">{{ (int) $membershipSummary['active'] }}</p>
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
            <x-ui.button :href="route('reports.income', request()->query())" variant="secondary">Detalle ingresos</x-ui.button>
            <x-ui.button :href="route('reports.attendance', request()->query())" variant="ghost">Detalle asistencias</x-ui.button>
            <x-ui.button :href="route('reports.memberships')" variant="ghost">Estado membresias</x-ui.button>
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
                        { label: 'Income', data: methodIncomeData, backgroundColor: '#059669' },
                        { label: 'Expense', data: methodExpenseData, backgroundColor: '#dc2626' },
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
