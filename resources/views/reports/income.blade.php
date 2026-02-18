@extends('layouts.panel')

@section('title', 'Reporte de ingresos')
@section('page-title', 'Reporte de ingresos y egresos')

@section('content')
    @php
        $methodLabels = [
            'cash' => 'Efectivo',
            'card' => 'Tarjeta',
            'transfer' => 'Transferencia',
        ];
    @endphp
    <x-ui.card title="Filtro" subtitle="Consulta movimientos por rango de fecha.">
        <form method="GET" action="{{ route('reports.income') }}" class="grid gap-3 md:grid-cols-4 md:items-end">
            <label class="space-y-1 text-sm font-semibold ui-muted">
                <span>Desde</span>
                <input type="date" name="from" value="{{ $from->toDateString() }}" class="ui-input">
            </label>

            <label class="space-y-1 text-sm font-semibold ui-muted">
                <span>Hasta</span>
                <input type="date" name="to" value="{{ $to->toDateString() }}" class="ui-input">
            </label>

            <x-ui.button type="submit" variant="secondary">Aplicar</x-ui.button>

            <div class="flex gap-2">
                <x-ui.button :href="route('reports.export.csv', ['from' => $from->toDateString(), 'to' => $to->toDateString()])"
                             class="js-loading-link" data-loading-text="Generando CSV...">Exportar CSV</x-ui.button>
                <x-ui.button :href="route('reports.index', request()->query())" variant="ghost">Volver al panel</x-ui.button>
            </div>
        </form>
    </x-ui.card>

    <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
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
    </section>

    <x-ui.card title="Detalle de movimientos">
        <div class="overflow-x-auto">
            <table class="ui-table min-w-[1100px]">
                <thead>
                <tr class="sticky top-0 border-b border-slate-200 bg-slate-50 text-left text-xs uppercase tracking-wider text-slate-500">
                    <th class="px-3 py-3">ID</th>
                    <th class="px-3 py-3">Fecha</th>
                    <th class="px-3 py-3">Tipo</th>
                    <th class="px-3 py-3">Metodo</th>
                    <th class="px-3 py-3">Monto</th>
                    <th class="px-3 py-3">Cliente</th>
                    <th class="px-3 py-3">Usuario</th>
                    <th class="px-3 py-3">Descripcion</th>
                </tr>
                </thead>
                <tbody>
                @forelse ($movements as $movement)
                    <tr class="border-b border-slate-100 text-sm">
                        <td class="px-3 py-3">{{ $movement->id }}</td>
                        <td class="px-3 py-3">{{ $movement->occurred_at?->format('Y-m-d H:i') ?? '-' }}</td>
                        <td class="px-3 py-3">
                            <x-ui.badge :variant="$movement->type === 'income' ? 'success' : 'danger'">{{ $movement->type }}</x-ui.badge>
                        </td>
                        <td class="px-3 py-3">{{ $methodLabels[$movement->method] ?? $movement->method }}</td>
                        <td class="px-3 py-3 font-semibold {{ $movement->type === 'income' ? 'text-emerald-700' : 'text-rose-700' }}">
                            {{ $movement->type === 'income' ? '+' : '-' }}${{ number_format((float) $movement->amount, 2) }}
                        </td>
                        <td class="px-3 py-3">{{ $movement->membership?->client?->full_name ?? '-' }}</td>
                        <td class="px-3 py-3">{{ $movement->createdBy?->name ?? '-' }}</td>
                        <td class="px-3 py-3">{{ $movement->description ?: '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-3 py-6 text-center text-sm text-slate-500">No hay movimientos en este rango.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">{{ $movements->links() }}</div>
    </x-ui.card>
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
    })();
</script>
@endpush
