@extends('layouts.panel')

@section('title', 'Historial de caja')
@section('page-title', 'Historial de caja')

@section('content')
    @php
        $currencyFormatter = \App\Support\Currency::class;
        $currencyCode = $appCurrencyCode ?? null;
        $historyRows = $sessions ?? collect();
    @endphp

    <x-ui.card title="Historial de caja" subtitle="Revisión de cierres, diferencias y responsables.">
        <div class="overflow-x-auto">
            <table class="ui-table min-w-[1040px]">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Apertura</th>
                    <th>Cierre</th>
                    <th>Apertura por</th>
                    <th>Cierre por</th>
                    <th>Esperado</th>
                    <th>Cierre</th>
                    <th>Diferencia</th>
                    <th>Estado</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                @forelse ($historyRows as $session)
                    @php
                        $difference = (float) ($session->difference ?? 0);
                        $closedWithDifference = (string) $session->status === 'closed' && abs($difference) > 0.00001;
                    @endphp
                    <tr>
                        <td>{{ $session->id }}</td>
                        <td>{{ $session->opened_at?->format('Y-m-d H:i') ?? '-' }}</td>
                        <td>{{ $session->closed_at?->format('Y-m-d H:i') ?? '-' }}</td>
                        <td>{{ $session->openedBy?->name ?? '-' }}</td>
                        <td>{{ $session->closedBy?->name ?? '-' }}</td>
                        <td>{{ $currencyFormatter::format((float) ($session->expected_balance ?? 0), $currencyCode) }}</td>
                        <td>{{ $session->closing_balance !== null ? $currencyFormatter::format((float) $session->closing_balance, $currencyCode) : '-' }}</td>
                        <td class="font-bold {{ $difference > 0 ? 'text-emerald-700 dark:text-emerald-300' : ($difference < 0 ? 'text-rose-700 dark:text-rose-300' : 'text-slate-700 dark:text-slate-200') }}">{{ $currencyFormatter::format($difference, $currencyCode) }}</td>
                        <td>
                            <x-ui.badge :variant="(string) $session->status === 'open' ? 'info' : 'success'">{{ $session->status }}</x-ui.badge>
                            @if ($closedWithDifference)
                                <x-ui.badge variant="warning">Cerro con diferencia</x-ui.badge>
                            @endif
                        </td>
                        <td class="text-right"><x-ui.button :href="route('cash.sessions.show', $session->id)" variant="ghost" size="sm">Detalle</x-ui.button></td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" class="text-center text-sm text-slate-500 dark:text-slate-300">No hay sesiones registradas.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        @if (method_exists($historyRows, 'links'))
            <div class="mt-4">{{ $historyRows->links() }}</div>
        @endif
    </x-ui.card>

    {{--
    TODO backend mínimo:
    1) Si usas esta vista como ruta principal de historial, actualizar controlador para `return view('cash.history', ...)`.
    2) Exponer bandera consolidada `closed_with_difference` para no recalcular en Blade.
    --}}
@endsection
