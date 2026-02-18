@extends('layouts.panel')

@section('title', 'Reporte de membresias')
@section('page-title', 'Reporte de membresias')

@section('content')
    <section class="grid gap-4 md:grid-cols-3">
        <x-ui.card>
            <p class="text-xs font-bold uppercase tracking-wider text-slate-500">Activos</p>
            <p class="mt-2 text-3xl font-black text-emerald-700">{{ (int) $membershipSummary['active'] }}</p>
        </x-ui.card>
        <x-ui.card>
            <p class="text-xs font-bold uppercase tracking-wider text-slate-500">Vencidos</p>
            <p class="mt-2 text-3xl font-black text-rose-700">{{ (int) $membershipSummary['expired'] }}</p>
        </x-ui.card>
        <x-ui.card>
            <p class="text-xs font-bold uppercase tracking-wider text-slate-500">Total clientes</p>
            <p class="mt-2 text-3xl font-black text-slate-900">{{ (int) $membershipSummary['total_clients'] }}</p>
        </x-ui.card>
    </section>

    <x-ui.card title="Clientes con membresia activa">
        <div class="overflow-x-auto">
            <table class="ui-table min-w-[820px]">
                <thead>
                <tr class="sticky top-0 border-b border-slate-200 bg-slate-50 text-left text-xs uppercase tracking-wider text-slate-500">
                    <th class="px-3 py-3">Cliente</th>
                    <th class="px-3 py-3">Documento</th>
                    <th class="px-3 py-3">Inicio</th>
                    <th class="px-3 py-3">Fin</th>
                    <th class="px-3 py-3">Estado</th>
                </tr>
                </thead>
                <tbody>
                @forelse ($activeClients as $client)
                    <tr class="border-b border-slate-100 text-sm">
                        <td class="px-3 py-3">{{ $client->full_name }}</td>
                        <td class="px-3 py-3">{{ $client->document_number }}</td>
                        <td class="px-3 py-3">{{ $client->starts_at ?? '-' }}</td>
                        <td class="px-3 py-3">{{ $client->ends_at ?? '-' }}</td>
                        <td class="px-3 py-3"><x-ui.badge variant="success">{{ $client->status }}</x-ui.badge></td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-3 py-6 text-center text-sm text-slate-500">No hay clientes activos.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </x-ui.card>

    <x-ui.card title="Clientes con membresia vencida o no activa">
        <div class="overflow-x-auto">
            <table class="ui-table min-w-[820px]">
                <thead>
                <tr class="sticky top-0 border-b border-slate-200 bg-slate-50 text-left text-xs uppercase tracking-wider text-slate-500">
                    <th class="px-3 py-3">Cliente</th>
                    <th class="px-3 py-3">Documento</th>
                    <th class="px-3 py-3">Inicio</th>
                    <th class="px-3 py-3">Fin</th>
                    <th class="px-3 py-3">Estado</th>
                </tr>
                </thead>
                <tbody>
                @forelse ($expiredClients as $client)
                    <tr class="border-b border-slate-100 text-sm">
                        <td class="px-3 py-3">{{ $client->full_name }}</td>
                        <td class="px-3 py-3">{{ $client->document_number }}</td>
                        <td class="px-3 py-3">{{ $client->starts_at ?? '-' }}</td>
                        <td class="px-3 py-3">{{ $client->ends_at ?? '-' }}</td>
                        <td class="px-3 py-3"><x-ui.badge variant="danger">{{ $client->status }}</x-ui.badge></td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-3 py-6 text-center text-sm text-slate-500">No hay clientes vencidos.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </x-ui.card>
@endsection
