@extends('layouts.panel')

@section('title', 'Sucursales')
@section('page-title', 'Módulo multisucursal')

@section('content')
    @php
        $money = static fn (float $amount): string => \App\Support\Currency::format($amount, $appCurrencyCode);
    @endphp

    <div class="space-y-4">
        <x-ui.card title="Resumen consolidado" subtitle="Totales operativos combinados entre sede principal y sucursales vinculadas.">
            <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
                <div class="rounded-xl border border-slate-200 bg-white px-4 py-3 dark:border-slate-700 dark:bg-slate-900/60">
                    <p class="text-[11px] font-bold uppercase tracking-wide text-slate-500 dark:text-slate-400">Sedes vinculadas</p>
                    <p class="mt-1 text-2xl font-black text-slate-900 dark:text-slate-100">{{ (int) ($kpis['total_gyms'] ?? 0) }}</p>
                </div>
                <div class="rounded-xl border border-sky-200 bg-sky-50 px-4 py-3 dark:border-sky-700/60 dark:bg-sky-900/20">
                    <p class="text-[11px] font-bold uppercase tracking-wide text-sky-700 dark:text-sky-300">Clientes</p>
                    <p class="mt-1 text-2xl font-black text-sky-700 dark:text-sky-200">{{ number_format((int) ($kpis['total_clients'] ?? 0)) }}</p>
                </div>
                <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 dark:border-emerald-700/60 dark:bg-emerald-900/20">
                    <p class="text-[11px] font-bold uppercase tracking-wide text-emerald-700 dark:text-emerald-300">Membresías activas</p>
                    <p class="mt-1 text-2xl font-black text-emerald-700 dark:text-emerald-200">{{ number_format((int) ($kpis['active_memberships'] ?? 0)) }}</p>
                </div>
                <div class="rounded-xl border border-indigo-200 bg-indigo-50 px-4 py-3 dark:border-indigo-700/60 dark:bg-indigo-900/20">
                    <p class="text-[11px] font-bold uppercase tracking-wide text-indigo-700 dark:text-indigo-300">Check-ins hoy</p>
                    <p class="mt-1 text-2xl font-black text-indigo-700 dark:text-indigo-200">{{ number_format((int) ($kpis['checkins_today'] ?? 0)) }}</p>
                </div>
            </div>

            <div class="mt-4 grid gap-3 sm:grid-cols-3">
                <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 dark:border-emerald-700/60 dark:bg-emerald-900/20">
                    <p class="text-[11px] font-bold uppercase tracking-wide text-emerald-700 dark:text-emerald-300">Ingresos 30 días</p>
                    <p class="mt-1 text-xl font-black text-emerald-700 dark:text-emerald-200">{{ $money((float) ($kpis['income_30d'] ?? 0)) }}</p>
                </div>
                <div class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 dark:border-rose-700/60 dark:bg-rose-900/20">
                    <p class="text-[11px] font-bold uppercase tracking-wide text-rose-700 dark:text-rose-300">Egresos 30 días</p>
                    <p class="mt-1 text-xl font-black text-rose-700 dark:text-rose-200">{{ $money((float) ($kpis['expense_30d'] ?? 0)) }}</p>
                </div>
                <div class="rounded-xl border border-cyan-200 bg-cyan-50 px-4 py-3 dark:border-cyan-700/60 dark:bg-cyan-900/20">
                    <p class="text-[11px] font-bold uppercase tracking-wide text-cyan-700 dark:text-cyan-300">Balance 30 días</p>
                    <p class="mt-1 text-xl font-black text-cyan-700 dark:text-cyan-200">{{ $money((float) ($kpis['balance_30d'] ?? 0)) }}</p>
                </div>
            </div>
        </x-ui.card>

        <x-ui.card title="Gestión de vinculos" subtitle="Las sucursales se crean y administran desde SuperAdmin.">
            <p class="rounded-xl border border-cyan-200 bg-cyan-50 px-3 py-2 text-sm font-semibold text-cyan-900 dark:border-cyan-500/40 dark:bg-cyan-900/20 dark:text-cyan-100">
                Tu gimnasio solo puede visualizar las sedes vinculadas. Para crear, editar o desvincular sucursales, usa el panel de SuperAdmin.
            </p>
        </x-ui.card>

        <x-ui.card title="Detalle por sede" subtitle="Rendimiento individual de cada sede vinculada.">
            <div class="overflow-x-auto">
                <table class="ui-table min-w-[1200px]">
                    <thead>
                        <tr class="border-b border-slate-200 bg-slate-50 text-left text-xs uppercase tracking-wider text-slate-500 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300">
                            <th class="px-3 py-3">Sede</th>
                            <th class="px-3 py-3">Plan</th>
                            <th class="px-3 py-3">Estado</th>
                            <th class="px-3 py-3">Clientes</th>
                            <th class="px-3 py-3">Membresías</th>
                            <th class="px-3 py-3">Check-ins hoy</th>
                            <th class="px-3 py-3">Ingresos 30d</th>
                            <th class="px-3 py-3">Egresos 30d</th>
                            <th class="px-3 py-3">Balance 30d</th>
                            <th class="px-3 py-3">Gestión</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse (($branchRows ?? collect()) as $row)
                            @php
                                $status = (string) ($row['subscription_status'] ?? '-');
                                $badgeClass = match ($status) {
                                    'active' => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-200',
                                    'grace' => 'bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-200',
                                    'suspended' => 'bg-rose-100 text-rose-800 dark:bg-rose-900/40 dark:text-rose-200',
                                    default => 'bg-slate-200 text-slate-700 dark:bg-slate-700 dark:text-slate-200',
                                };
                            @endphp
                            <tr class="border-b border-slate-100 text-sm odd:bg-white even:bg-slate-50 dark:border-slate-800 dark:odd:bg-slate-900 dark:even:bg-slate-950/50">
                                <td class="px-3 py-3">
                                    <p class="font-semibold text-slate-800 dark:text-slate-100">{{ $row['name'] }}</p>
                                    <p class="text-xs text-slate-500 dark:text-slate-400">{{ $row['slug'] }}</p>
                                    @if (! empty($row['is_hub']))
                                        <span class="mt-1 inline-flex rounded-full bg-cyan-100 px-2 py-0.5 text-[10px] font-bold uppercase tracking-wide text-cyan-800 dark:bg-cyan-900/40 dark:text-cyan-200">
                                            Sede principal
                                        </span>
                                    @endif
                                </td>
                                <td class="px-3 py-3 dark:text-slate-200">{{ $row['plan_name'] }}</td>
                                <td class="px-3 py-3">
                                    <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-bold uppercase tracking-wide {{ $badgeClass }}">
                                        {{ $status !== '' ? $status : '-' }}
                                    </span>
                                </td>
                                <td class="px-3 py-3 dark:text-slate-200">{{ number_format((int) $row['clients_total']) }}</td>
                                <td class="px-3 py-3 dark:text-slate-200">{{ number_format((int) $row['active_memberships']) }}</td>
                                <td class="px-3 py-3 dark:text-slate-200">{{ number_format((int) $row['checkins_today']) }}</td>
                                <td class="px-3 py-3 text-emerald-700 dark:text-emerald-300">{{ $money((float) $row['income_30d']) }}</td>
                                <td class="px-3 py-3 text-rose-700 dark:text-rose-300">{{ $money((float) $row['expense_30d']) }}</td>
                                <td class="px-3 py-3 text-cyan-700 dark:text-cyan-300">{{ $money((float) $row['balance_30d']) }}</td>
                                <td class="px-3 py-3">
                                    <span class="text-xs font-semibold text-slate-500 dark:text-slate-300">Solo SuperAdmin</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="px-3 py-6 text-center text-sm text-slate-500 dark:text-slate-300">
                                    No hay sedes vinculadas todavía.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-ui.card>
    </div>
@endsection
