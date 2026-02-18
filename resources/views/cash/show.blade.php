@extends('layouts.panel')

@section('title', 'Caja #'.$session->id)
@section('page-title', 'Detalle de caja #'.$session->id)

@section('content')
    @php
        $methodLabels = [
            'cash' => 'Efectivo',
            'card' => 'Tarjeta',
            'transfer' => 'Transferencia',
        ];
    @endphp
    <x-ui.card title="Sesion #{{ $session->id }}" subtitle="Apertura {{ $session->opened_at?->format('Y-m-d H:i') }} por {{ $session->openedBy?->name ?? 'N/D' }}">
        <div class="flex flex-wrap items-center gap-2">
            <x-ui.badge :variant="$session->status === 'open' ? 'success' : 'info'">{{ $session->status }}</x-ui.badge>
            <x-ui.button :href="route('cash.index')" size="sm" variant="secondary">Caja actual</x-ui.button>
            <x-ui.button :href="route('cash.sessions.index')" size="sm" variant="ghost">Historial</x-ui.button>
        </div>

        <div class="mt-4 grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
            <article class="rounded-xl border border-slate-200 bg-slate-50 p-3">
                <p class="text-xs font-bold uppercase tracking-wider text-slate-500">Apertura</p>
                <p class="mt-1 text-2xl font-black text-slate-900">${{ number_format((float) $session->opening_balance, 2) }}</p>
            </article>
            <article class="rounded-xl border border-emerald-200 bg-emerald-50 p-3">
                <p class="text-xs font-bold uppercase tracking-wider text-emerald-700">Ingresos</p>
                <p class="mt-1 text-2xl font-black text-emerald-800">${{ number_format((float) $summary['income_total'], 2) }}</p>
            </article>
            <article class="rounded-xl border border-rose-200 bg-rose-50 p-3">
                <p class="text-xs font-bold uppercase tracking-wider text-rose-700">Egresos</p>
                <p class="mt-1 text-2xl font-black text-rose-800">${{ number_format((float) $summary['expense_total'], 2) }}</p>
            </article>
            <article class="rounded-xl border border-cyan-200 bg-cyan-50 p-3">
                <p class="text-xs font-bold uppercase tracking-wider text-cyan-700">Esperado</p>
                <p class="mt-1 text-2xl font-black text-cyan-800">${{ number_format((float) $summary['expected_balance'], 2) }}</p>
            </article>
        </div>

        <div class="mt-4 grid gap-2 text-sm text-slate-700 md:grid-cols-2">
            <p><span class="font-semibold text-slate-900">Cierre:</span> {{ $session->closed_at?->format('Y-m-d H:i') ?? 'Sin cerrar' }}</p>
            <p><span class="font-semibold text-slate-900">Cerrada por:</span> {{ $session->closedBy?->name ?? '-' }}</p>
            <p><span class="font-semibold text-slate-900">Notas:</span> {{ $session->notes ?: '-' }}</p>
            @if ($session->status === 'closed')
                <p>
                    <span class="font-semibold text-slate-900">Diferencia:</span>
                    <span class="{{ (float) $session->difference === 0.0 ? 'text-slate-800' : ((float) $session->difference > 0 ? 'text-emerald-700' : 'text-rose-700') }} font-bold">
                        ${{ number_format((float) $session->difference, 2) }}
                    </span>
                </p>
            @endif
        </div>
    </x-ui.card>

    <x-ui.card title="Totales por metodo">
        <div class="overflow-x-auto">
            <table class="ui-table min-w-[640px]">
                <thead>
                <tr class="border-b border-slate-200 text-left text-xs uppercase tracking-wider text-slate-500">
                    <th class="px-3 py-3">Metodo</th>
                    <th class="px-3 py-3">Movimientos</th>
                    <th class="px-3 py-3">Ingresos</th>
                    <th class="px-3 py-3">Egresos</th>
                </tr>
                </thead>
                <tbody>
                @forelse ($methodTotals as $methodTotal)
                    <tr class="border-b border-slate-100 text-sm">
                        <td class="px-3 py-3">{{ $methodLabels[$methodTotal->method] ?? $methodTotal->method }}</td>
                        <td class="px-3 py-3">{{ (int) $methodTotal->movements_count }}</td>
                        <td class="px-3 py-3 text-emerald-700">${{ number_format((float) $methodTotal->income_total, 2) }}</td>
                        <td class="px-3 py-3 text-rose-700">${{ number_format((float) $methodTotal->expense_total, 2) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-3 py-6 text-center text-sm text-slate-500">Sin movimientos.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </x-ui.card>

    <x-ui.card title="Movimientos del turno">
        <div class="overflow-x-auto">
            <table class="ui-table min-w-[1100px]">
                <thead>
                <tr class="border-b border-slate-200 text-left text-xs uppercase tracking-wider text-slate-500">
                    <th class="px-3 py-3">ID</th>
                    <th class="px-3 py-3">Fecha</th>
                    <th class="px-3 py-3">Tipo</th>
                    <th class="px-3 py-3">Metodo</th>
                    <th class="px-3 py-3">Monto</th>
                    <th class="px-3 py-3">Membresia</th>
                    <th class="px-3 py-3">Creado por</th>
                    <th class="px-3 py-3">Descripcion</th>
                </tr>
                </thead>
                <tbody>
                @forelse ($session->movements as $movement)
                    <tr class="border-b border-slate-100 text-sm">
                        <td class="px-3 py-3">{{ $movement->id }}</td>
                        <td class="px-3 py-3">{{ $movement->occurred_at?->format('Y-m-d H:i') ?? '-' }}</td>
                        <td class="px-3 py-3">
                            <x-ui.badge :variant="$movement->type === 'income' ? 'success' : 'danger'">{{ $movement->type }}</x-ui.badge>
                        </td>
                        <td class="px-3 py-3">{{ $methodLabels[$movement->method] ?? $movement->method }}</td>
                        <td class="px-3 py-3 {{ $movement->type === 'income' ? 'text-emerald-700' : 'text-rose-700' }} font-semibold">
                            {{ $movement->type === 'income' ? '+' : '-' }}${{ number_format((float) $movement->amount, 2) }}
                        </td>
                        <td class="px-3 py-3">{{ $movement->membership_id ?: '-' }}</td>
                        <td class="px-3 py-3">{{ $movement->createdBy?->name ?? '-' }}</td>
                        <td class="px-3 py-3">{{ $movement->description ?: '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-3 py-6 text-center text-sm text-slate-500">Sin movimientos en este turno.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </x-ui.card>
@endsection
