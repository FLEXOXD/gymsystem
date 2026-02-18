@extends('layouts.panel')

@section('title', 'SuperAdmin Suscripciones')
@section('page-title', 'Suscripciones')

@section('content')
    <x-ui.card title="Suscripciones" subtitle="Vista anterior unificada al layout del panel.">
        <p class="mb-4 text-sm text-slate-600 dark:text-slate-300">
            Esta pantalla se mantiene por compatibilidad. La gestion principal esta en <strong>SuperAdmin -> Gimnasios</strong>.
        </p>

        @if (!empty($gyms))
            <div class="overflow-x-auto">
                <table class="ui-table min-w-[980px]">
                    <thead>
                    <tr class="border-b border-slate-200 bg-slate-50 text-left text-xs uppercase tracking-wider text-slate-500 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300">
                        <th class="px-3 py-3">Gimnasio</th>
                        <th class="px-3 py-3">Plan</th>
                        <th class="px-3 py-3">Precio</th>
                        <th class="px-3 py-3">Fin</th>
                        <th class="px-3 py-3">Estado</th>
                        <th class="px-3 py-3">Acciones</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($gyms as $gym)
                        @php
                            $subscription = $gym->subscriptions->first();
                        @endphp
                        <tr class="border-b border-slate-100 text-sm dark:border-slate-800">
                            <td class="px-3 py-3 dark:text-slate-100">{{ $gym->name }}</td>
                            <td class="px-3 py-3 dark:text-slate-200">{{ $subscription?->plan_name ?? '-' }}</td>
                            <td class="px-3 py-3 dark:text-slate-200">{{ $subscription ? '$'.number_format((float) $subscription->price, 2) : '-' }}</td>
                            <td class="px-3 py-3 dark:text-slate-200">{{ $subscription?->ends_at?->toDateString() ?? '-' }}</td>
                            <td class="px-3 py-3 dark:text-slate-200">{{ match ($subscription?->status) { 'active' => 'Activo', 'grace' => 'Gracia', 'suspended' => 'Suspendido', null => 'Sin suscripcion', default => $subscription?->status } }}</td>
                            <td class="px-3 py-3">
                                <a href="{{ route('superadmin.gyms.index') }}" class="ui-link">Ir a Gimnasios</a>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </x-ui.card>
@endsection
