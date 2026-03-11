@extends('layouts.panel')

@section('title', 'Movimientos del mes')
@section('page-title', 'Movimientos del mes')

@section('content')
    <div class="space-y-4">
        <x-ui.card :title="$isCashierScoped ? 'Tus movimientos del mes' : 'Movimientos del mes'" subtitle="Consulta todo lo registrado en el mes actual. Este acumulado se reinicia automaticamente al comenzar un nuevo mes.">
            <div class="flex flex-wrap items-center gap-2">
                <x-ui.button :href="route('cash.index')" size="sm" variant="secondary">Volver a caja</x-ui.button>
                <x-ui.button :href="route('clients.index')" size="sm" variant="primary">Cobrar membresia</x-ui.button>
                @if (! $isCashierScoped)
                    <x-ui.button :href="route('reports.income')" size="sm" variant="ghost">Ver reporte de ingresos</x-ui.button>
                @endif
            </div>
            <div class="mt-4">
                @include('cash.partials.monthly-movements-content', [
                    'monthlyMovements' => $monthlyMovements,
                    'monthlySummary' => $monthlySummary,
                    'monthStart' => $monthStart,
                    'monthEnd' => $monthEnd,
                    'isCashierScoped' => $isCashierScoped,
                ])
            </div>
        </x-ui.card>
    </div>
@endsection
