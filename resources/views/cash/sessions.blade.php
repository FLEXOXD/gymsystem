@extends('layouts.panel')

@section('title', 'Historial de caja')
@section('page-title', 'Historial de caja')

@section('content')
    <x-ui.card title="Turnos de caja" subtitle="Control historico de apertura y cierre por gimnasio.">
        <div class="mb-4">
            <x-ui.button :href="route('cash.index')" variant="secondary" size="sm">Volver a caja actual</x-ui.button>
        </div>

        <div class="overflow-x-auto">
            <table class="ui-table min-w-[1100px]">
                <thead>
                <tr class="border-b border-slate-200 text-left text-xs uppercase tracking-wider text-slate-500">
                    <th class="px-3 py-3">ID</th>
                    <th class="px-3 py-3">Estado</th>
                    <th class="px-3 py-3">Apertura</th>
                    <th class="px-3 py-3">Cierre</th>
                    <th class="px-3 py-3">Opening</th>
                    <th class="px-3 py-3">Expected</th>
                    <th class="px-3 py-3">Difference</th>
                    <th class="px-3 py-3">Abierta por</th>
                    <th class="px-3 py-3">Cerrada por</th>
                    <th class="px-3 py-3">Detalle</th>
                </tr>
                </thead>
                <tbody>
                @forelse ($sessions as $session)
                    <tr class="border-b border-slate-100 text-sm">
                        <td class="px-3 py-3">{{ $session->id }}</td>
                        <td class="px-3 py-3">
                            <x-ui.badge :variant="$session->status === 'open' ? 'success' : 'info'">{{ $session->status }}</x-ui.badge>
                        </td>
                        <td class="px-3 py-3">{{ $session->opened_at?->format('Y-m-d H:i') ?? '-' }}</td>
                        <td class="px-3 py-3">{{ $session->closed_at?->format('Y-m-d H:i') ?? '-' }}</td>
                        <td class="px-3 py-3">${{ number_format((float) $session->opening_balance, 2) }}</td>
                        <td class="px-3 py-3">{{ $session->expected_balance !== null ? '$'.number_format((float) $session->expected_balance, 2) : '-' }}</td>
                        <td class="px-3 py-3">{{ $session->difference !== null ? '$'.number_format((float) $session->difference, 2) : '-' }}</td>
                        <td class="px-3 py-3">{{ $session->openedBy?->name ?? '-' }}</td>
                        <td class="px-3 py-3">{{ $session->closedBy?->name ?? '-' }}</td>
                        <td class="px-3 py-3">
                            <x-ui.button :href="route('cash.sessions.show', $session->id)" size="sm" variant="secondary">Ver</x-ui.button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" class="px-3 py-6 text-center text-sm text-slate-500">No hay sesiones registradas.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $sessions->links() }}
        </div>
    </x-ui.card>
@endsection
