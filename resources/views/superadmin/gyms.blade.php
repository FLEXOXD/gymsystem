@extends('layouts.panel')

@section('title', 'SuperAdmin Gyms')
@section('page-title', 'Gyms y Suscripciones')

@section('content')
    <x-ui.card title="Gimnasios y estado de suscripcion" subtitle="Ordenado por prioridad operativa: grace, active y luego suspended.">
        <div class="overflow-x-auto">
            <table class="ui-table min-w-[1100px]">
                <thead>
                <tr class="border-b border-slate-200 bg-slate-50 text-left text-xs uppercase tracking-wider text-slate-500 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300">
                    <th class="px-3 py-3">Gym</th>
                    <th class="px-3 py-3">Plan</th>
                    <th class="px-3 py-3">Precio</th>
                    <th class="px-3 py-3">Fin</th>
                    <th class="px-3 py-3">Status</th>
                    <th class="px-3 py-3">Dias</th>
                    <th class="px-3 py-3">Ultimo pago</th>
                    <th class="px-3 py-3">Acciones</th>
                </tr>
                </thead>
                <tbody>
                @forelse ($gyms as $gym)
                    @php
                        $statusClasses = [
                            'active' => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-200',
                            'grace' => 'bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-200',
                            'suspended' => 'bg-rose-100 text-rose-800 dark:bg-rose-900/40 dark:text-rose-200',
                        ];
                        $badgeClass = $statusClasses[$gym->status] ?? 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-200';
                    @endphp
                    <tr class="border-b border-slate-100 align-top text-sm odd:bg-white even:bg-slate-50 dark:border-slate-800 dark:odd:bg-slate-900 dark:even:bg-slate-950/50">
                        <td class="px-3 py-3 font-semibold text-slate-800 dark:text-slate-100">{{ $gym->gym_name }}</td>
                        <td class="px-3 py-3 dark:text-slate-200">{{ $gym->plan_name }}</td>
                        <td class="px-3 py-3 dark:text-slate-200">${{ number_format((float) $gym->price, 2) }}</td>
                        <td class="px-3 py-3 dark:text-slate-200">{{ \Carbon\Carbon::parse($gym->ends_at)->toDateString() }}</td>
                        <td class="px-3 py-3">
                            <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-bold uppercase tracking-wide {{ $badgeClass }}">
                                {{ $gym->status }}
                            </span>
                        </td>
                        <td class="px-3 py-3 dark:text-slate-200">
                            @if ($gym->status === 'active')
                                {{ (int) ($gym->days_left ?? 0) }} dias
                            @elseif ($gym->status === 'grace')
                                {{ (int) ($gym->grace_left ?? 0) }} dias de gracia
                            @else
                                -
                            @endif
                        </td>
                        <td class="px-3 py-3 dark:text-slate-200">{{ $gym->last_payment_method ?? '-' }}</td>
                        <td class="px-3 py-3">
                            <div class="flex flex-wrap items-center gap-2">
                                <form method="POST" action="{{ route('superadmin.subscriptions.renew', $gym->gym_id) }}" class="flex items-center gap-2">
                                    @csrf
                                    <input type="hidden" name="months" value="1">
                                    <select name="payment_method" class="ui-input px-2 py-1.5 text-xs" required>
                                        @foreach ($paymentMethods as $method)
                                            <option value="{{ $method }}">{{ $method }}</option>
                                        @endforeach
                                    </select>
                                    <x-ui.button type="submit" size="sm">Renovar 1 mes</x-ui.button>
                                </form>

                                <form method="POST" action="{{ route('superadmin.subscriptions.suspend', $gym->gym_id) }}"
                                      onsubmit="return confirm('Suspender suscripcion de este gimnasio?');">
                                    @csrf
                                    <x-ui.button type="submit" variant="danger" size="sm">Suspender</x-ui.button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-3 py-6 text-center text-sm text-slate-500 dark:text-slate-300">
                            No hay gimnasios registrados.
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </x-ui.card>
@endsection
